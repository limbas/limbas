<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


require_once(COREPATH . 'gtab/html/contextmenus/gtab_search.lib');

// explorer extension
if($module == 'explorer_search') {
    $gsr = $GLOBALS['ffilter']['gsr'];
}

// save filter
if($params['save_search_selection'] >= 2 && $params['gs']) {
    global $gsnap;

    if($params['save_search_selection'] == 2) {
        $sname = $params['save_SnapFilterName'];
    }elseif($params['save_search_selection'] == 3) {
        $sname = $params['save_SearchFilterName'];
    }

    // change snapshot
    if((!$sname && $filter["snapid"][$gtabid]) OR ($sname && $sname == $gsnap[$gtabid]["name"][$filter["snapid"][$gtabid]])){
        $saveSnapID = $filter["snapid"][$gtabid];
        $gs = $params['gs'];
        $gssearch = 1;
        include(COREPATH . 'gtab/gtab_register.lib');
    }

    if($sname OR $saveSnapID) {
        if($params['save_search_selection'] == 3) {
            // search filter
            $snap_id = SNAP_save($gtabid, $sname, $saveSnapID, array('gsr' => $params['gs'][$gtabid]), 2);
        }elseif($params['save_search_selection'] == 2) {
            // snapshot filter
            $snap_id = SNAP_save($gtabid, $sname, $saveSnapID);
        }
        $gsnap = SNAP_loadInSession(1);
        lmb_register_snapshot($gtabid, $snap_id);
    }
}

// hold search selection and reload searchform
elseif($params['save_search_selection'] && $params['gs']) {
    $gs = $params['gs'];
    $gssearch = 1;
    include(COREPATH . 'gtab/gtab_register.lib');
}



?>

<link href="assets/vendor/select2/select2.min.css" rel="stylesheet">
<script type="text/javascript">
    /**
     * Custom DropdownAdapter to show current table name on top of field list
     */
    $.fn.select2.amd.define(
        "TableHeaderDropdownAdapter",
        [
            "select2/utils",
            "select2/dropdown",
            "select2/dropdown/attachBody",
            "select2/dropdown/attachContainer"
        ],
        function(Utils, Dropdown, AttachBody, AttachContainer) {
            const customDropdown = Utils.Decorate(Dropdown, AttachContainer);
            customDropdown.prototype.render = function() {
                const $searchField = $('#gdsearchfield');

                // show button if previous table exists
                let button = '';
                if ($searchField.data('originArr').length > 0) {
                    button = '<button type="button" onclick="stepBack()" class="btn btn-secondary rounded-0 full-height"><i class="lmb-icon lmb-caret-left"></i></button>';
                }

                const $header = $(
                    `<div class="border-bottom border-secondary d-flex justify-content-between">
                        <span class="flex-basis-50 text-start">${button}</span>
                        <span class="select2-search select2-search--dropdown text-center align-self-center font-small">${this.options.get("tableName")}</span>
                        <span class="flex-basis-50"></span>
                    </div>`);
                return Dropdown.prototype.render.call(this).prepend($header);
            };

            return Utils.Decorate(customDropdown, AttachBody);
        }
    );

    /**
     * Custom result adapter to show an arrow next to relation fields leading to next table
     * @param tableID int current table id
     * @returns {Function}
     */
    function formatState (tableID) {
        return function(state) {
            // normal (non-relation) entry
            if (!state.relatedGtabid) {
                return $(`<span title="${state.title}">${state.text}</span>`);
            }

            // relation barams
            let paramsButton = '';
            if (state.relationGtabid) {
                paramsButton = `
                    <button type="button" onclick="stepForward(${tableID}, ${state.id}, ${state.relationGtabid}, '${state.text}_params');" class="btn btn-secondary rounded-0" style="margin-left: auto; margin-right: 3px;">
                        <i class="lmb-icon lmb-chain"></i>
                    </button>
                `;
            }

            return $(
                `<div class="select2-results__option--link d-flex justify-content-between">
                    <span title="${state.title}" class="select2-results__option--link__text fw-bold">${state.text}</span>
                    ${paramsButton}
                    <button type="button" onclick="stepForward(${tableID}, ${state.id}, ${state.relatedGtabid}, '${state.text}');" class="btn btn-secondary rounded-0">
                        <i class="lmb-icon lmb-caret-right"></i>
                    </button>
                </div>`);
        }
    }

    /**
     * Navigates to related table in select2
     * @param tableID current table
     * @param relationID clicked relation field id
     * @param relationTableID table id which is target of relation
     * @param relationFieldName name of relation field
     */
    function stepForward(tableID, relationID, relationTableID, relationFieldName) {
        const $searchField = $('#gdsearchfield');

        const originArr = $searchField.data('originArr');
        originArr.push(tableID);
        originArr.push(relationID);
        originArr.push(0);

        $searchField.data('relationFieldNames').push(relationFieldName);

        showFields(relationTableID);
    }

    /**
     * Navigates to previous table in select2
     */
    function stepBack() {
        const $searchField = $('#gdsearchfield');

        const originArr = $searchField.data('originArr');
        originArr.pop(); // filterIndex
        originArr.pop(); // relationField
        const lastTableID = originArr.pop();

        $searchField.data('relationFieldNames').pop();

        showFields(lastTableID);
    }

    /**
     * Loads the fields of the current table and opens the select2 dropdown with the fields as data
     * @param tableID int
     */
    function showFields(tableID) {
        $.ajax({
            url: 'main_dyns.php?actid=gtabFieldsSelect2&gtabid=' + tableID,
            dataType: 'json',
            success: function (data) {
                const $searchField = $('#gdsearchfield');

                // dropdown has been opened before -> empty
                const firstTimeOpened = $searchField.data('originArr') === undefined;
                if (firstTimeOpened) {
                    $searchField.data('originArr', []);
                } else {
                    $searchField.empty().select2('destroy');
                }

                // navigate from different table -> show table header
                const dropdownAdapter = firstTimeOpened ? undefined : $.fn.select2.amd.require('TableHeaderDropdownAdapter');

                // get name to show on top
                const relationFieldNames = $searchField.data('relationFieldNames');
                let tableName = '';
                if (relationFieldNames.length > 0) {
                    // last known relation field name
                    tableName = relationFieldNames[relationFieldNames.length - 1];
                } else {
                    // current table name
                    tableName = data['tableName'];
                }

                // init select2 with current table
                $searchField.select2({
                    width: '100%',
                    placeholder: '<?= $lang[3169] ?>',
                    allowClear: true,
                    templateResult: formatState(tableID),
                    data: data['fields'],
                    dropdownAdapter: dropdownAdapter,
                    dropdownParent: $searchField.parent(),
                    tableID: tableID,
                    tableName: tableName,
                    language: '<?= getLangShort() ?>'
                });

                $('#gdsearchfield').prepend('<option selected></option>');

                // on select listener
                $searchField
                    .off('select2:selecting')
                    .on('select2:selecting', function (e) {
                        e.preventDefault();
                        if ($(e.params.args.originalEvent.target).is('button') || $(e.params.args.originalEvent.target).is('i'))
                            return;

                        $(this).select2('close');
                        limbasAddSearchPara($(this).data('originArr').join("_"), tableID, e.params.args.data.id);
                    });

                // keep select open
                if (!firstTimeOpened) {
                    $searchField.select2('open');
                }
            }
        });
    }

    $(function() {
        showFields(<?= $gtabid ?>);
    });
</script>

<form action="main.php" method="post" name="form11" id="form11" class="form-inline">
    <input type="hidden" name="action" value="gtab_erg">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="form_id" value="<?= $form_id ?>">
    <input type="hidden" name="snap_id" id="snap_id" value="<?=$snap_id?>">
    <input type="hidden" name="gfrist">
    <input type="hidden" name="LID">
    <input type="hidden" name="next" value="1">
    <input type="hidden" name="supersearch" value="1">
    <input type="hidden" name="filter_reset">
    <input type="hidden" name="fieldid">
    <input type="hidden" name="verknpf">
	<input type="hidden" name="verkn_ID">
	<input type="hidden" name="verkn_tabid">
	<input type="hidden" name="verkn_fieldid">
	<input type="hidden" name="verkn_showonly">


    <?php if($LINK[188] && $module != 'explorer_search'):?>

    <div class="row p-0">

        <?php
        $filterlist = SNAP_get_filtergroup($gtabid);
        ?>
        <div class="col-sm-6">
                <div class="input-group mb-2">
                    <label for="snapfilter" class="input-group-text" ><i class="lmb-icon lmb-filter" title="<?=$lang[3159]?>"></i></label>
                    <select id="snapfilter" class="form-select" onchange="limbasDetailSearch(event,this,<?=$gtabid?>,null,null,this.value)">
                        <option value="0" style="color:#EEEEEE" disabled selected><?=$lang[3159]?>
                        <option value="0">
                        <?php
                            if($filterlist) {
                                SNAP_print_select($gtabid, $filter["snapid"][$gtabid], $filterlist);
                            }
                        ?>
                    </select>
                    <?php if($LINK[225]):?>
                    <span class="input-group-text">
                        <i class="lmb-icon lmb-cog-alt cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseSnapFilter" title="<?=$lang[3152]?>"></i>
                    </span>

                    <div class="input-group mb-2 collapse" id="collapseSnapFilter">
                    <input type="text" class="form-control form-control-sm" name="save_SnapFilterName" placeholder="<?=$lang[3166]?>">
                    <button class="btn btn-outline-secondary" type="button" OnClick="limbasDetailSearch(event,this,<?=$gtabid?>,null,null,null,null,2);"><i class="lmb-icon lmb-page-save" title="<?=$lang[3152]?>"></i></button>
                    </div>
                    <?php endif;?>
                </div>
        </div>

        <?php
        $filterlist = SNAP_get_filtergroup($gtabid,null,2);
        ?>


        <div class="col-sm-6">
                <div class="input-group mb-2">
                    <label for="searchfilter" class="input-group-text"><i class="lmb-icon lmb-page-find" title="<?=$lang[3152]?>"></i></label>
                    <select id="searchfilter" class="form-select" onchange="limbasDetailSearch(event,this,<?=$gtabid?>,null,null,this.value)">
                        <option value="0" style="color:#EEEEEE" disabled selected><?=$lang[3152]?>
                        <option value="0">
                        <?php
                            if($filterlist) {
                                SNAP_print_select($gtabid, $filter["snapid"][$gtabid], $filterlist);
                            }
                        ?>
                    </select>
                    <?php if($LINK[225]):?>
                    <span class="input-group-text">
                        <i class="lmb-icon lmb-cog-alt cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseSearchFilter" title="<?=$lang[3152]?>"></i>
                    </span>

                    <div class="input-group mb-2 collapse" id="collapseSearchFilter">
                    <input type="text" class="form-control form-control-sm" name="save_SearchFilterName" placeholder="<?=$lang[3165]?>">
                    <button class="btn btn-outline-secondary" type="button" OnClick="limbasDetailSearch(event,this,<?=$gtabid?>,null,null,null,null,3);"><i class="lmb-icon lmb-page-save" title="<?=$lang[3152]?>"></i></button>
                    </div>
                    <?php endif;?>
                </div>
        </div>


    </div>

    <?php endif;?>


    <div class="row p-1">
        <div class="col-sm-16">
            <div class="d-flex align-items-center">
                <select type="text" id="gdsearchfield" data-relation-field-names="[]"></select>
            </div>
        </div>
    </div>

    <table id="searchFilterRowTable" class="table table-sm table-borderless table-hover p-0 m-0 w-100 align-middle">
        <tbody>
        <?php
        // list of search fields
        foreach ($gsr[$gtabid] as $key => $gval) {
            if (is_array($gsr[$gtabid][$key])) {
                ksort($gsr[$gtabid][$key]);
                foreach ($gsr[$gtabid][$key] as $nkey => $gsrres) {
                    if (!is_numeric($nkey))
                        continue;
                    printFilterRow('', $gtabid, $key, $nkey, $gsrres, $gsr[$gtabid][$key]);
                }
            }
        }
        ?>
        </tbody>
    </table>
    <script>
        // $(function() {
            // triggers limbasSetSearchOptionsActive
            // $('#searchFilterRowTable')
            //    .find('select[id^="gds"]')
            //    .change();
        // });
    </script>

    <?php
    $or = '';
    $and = '';
    if ($gsr[$gtabid]['andor'] == 2) {
        $or = "selected";
    } else {
        $and = "selected";
    }
    ?>


    <hr>

    <div class="row">
        <div class="col col-sm-3">
            <div class="input-group">
                <label for="globalsearch" class="input-group-text"><i class="lmb-icon lmb-globe"></i></label>
                <select id="globalsearch" class="form-select form-select-sm align-middle" name="gs[<?= $gtabid ?>][andor]">
                    <option value="1" <?= $and ?>><?= $lang[854] ?></option>
                    <option value="2" <?= $or ?>><?= $lang[855] ?></option>
                </select>
            </div>
        </div>

        <div class="col col-sm-6 text-center">
            <button type="button" class="btn btn-primary btn-sm align-middle bg-secondary" name="reset"
                    OnClick="LmGs_sendForm(1);"><i class="lmb-icon lmb-undo"></i><?= $lang[1891] ?>
            </button>
        </div>

        <div class="col col-sm-3 text-end">
            <button type="button" class="btn btn-primary btn-sm align-middle" name="search"
                        OnClick="LmGs_sendForm(0, '<?= $gtabid ?>');">
                <i class="lmb-icon lmb-page-find"></i> <?= $lang[30] ?>
            </button>
        </div>
    </div>

</form>
