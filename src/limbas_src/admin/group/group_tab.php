<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>

<div ID="element6" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;"
     onclick="activ_menu=1">
    <form NAME="edittab_form">
        <?php pop_left(); ?>
        <textarea
                id="edit_rule"
                name="edit_rule"
                data-rule="27"
                class="form-control form-control-sm w-100"
                style="height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></textarea>
        <?php pop_right(); ?>
        <?php pop_bottom(); ?>
        <input type="hidden" name="gtabid">
    </form>
</div>

<div ID="element4" class="lmbContextMenu" style="visibility:hidden;z-index:10001;" onclick="activ_menu = 1;">
    <FORM NAME="fcolor_form">
        <?php #----------------- Farb-Menü -------------------
        unset($opt);
        pop_top('limbasDivMenuFarb');
        pop_color(null, null, 'element4');
        pop_bottom();
        ?>
    </FORM>
</div>

<div ID="element1" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;"
     onclick="activ_menu=1">
    <form NAME="indicator_form">
        <?php pop_left(); ?>
        <textarea
                id="indicator_rule"
                name="indicator_rule"
                data-rule="31"
                class="form-control form-control-sm w-100"
                style="height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></textarea>
        <?php pop_right(); ?>
        <?php pop_bottom(); ?>
        <input type="hidden" name="gtabid">
    </form>
</div>

<div ID="element7" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:10001;"
     onclick="activ_menu=1">
    <form NAME="orderby_form">
        <?php pop_left(); ?>
        <textarea
                id="orderby_value"
                name="orderby_value"
                data-rule="7"
                class="form-control form-control-sm w-100"
                style="height:100px;background-color:<?= $farbschema['WEB8'] ?>;"></textarea>
        <?php pop_right(); ?>
        <?php pop_bottom(); ?>
        <input type="hidden" name="gtabid">
    </form>
</div>


<script>

    $(function () {
        $('[data-opentabrows]').on('click', ajaxGetTabRows);
        $('[data-rule]').on('change', changeRule);
    });

    /**
     * Call this function on change of one of the group table rules to save the rule change and change the layout dynamically
     */
    function changeRule() {
        const gtabid = this.form.gtabid.value;
        const val = this.value;
        const ruleId = $(this).data('rule');
        switchRuleStatus(val, this, gtabid)
        $('input[name="' + this.id + '_' + gtabid + '"]').val(val);
        $('#t_' + this.id + '_' + gtabid).text(val);
        save_rules(this.form.gtabid.value, '', ruleId);
    }

    /**
     * Adjusts the rule layout based on val to save space
     * @param val
     * @param {Element} ruleElement
     * @param {Number} gtabid
     */
    function switchRuleStatus(val, ruleElement, gtabid) {
        let $ruleDiv = $('#div_' + ruleElement.id + '_' + gtabid);
        let $ruleDivEmpty = $('#div_' + ruleElement.id + '_' + gtabid + '_empty');
        if (val) {
            switchDisplay($ruleDiv, $ruleDivEmpty);
            return;
        }
        switchDisplay($ruleDivEmpty, $ruleDiv);
    }

    /**
     * Show div1 using flex, Hide div2
     * @param {jQuery} $div1
     * @param {jQuery} $div2
     */
    function switchDisplay($div1, $div2) {
        $div1.show();
        $div1.addClass('d-flex');
        $div2.hide();
        $div2.removeClass('d-flex');
    }

    var activ_menu = null;

    function ajaxGetTabRows() {
        let tabid = $(this).data("opentabrows");
        let ID = $('input[name="ID"]').val();
        ajaxGet(null, "main_dyns_admin.php", "manageGroupRights&tabid=" + tabid + "&ID=" + ID + "&action=tabRowsModal", null, "ajaxGetTabRowsPost", null);
    }

    function ajaxGetTabRowsPost(result) {
        ajaxEvalScript(result);
        $('#tableRightsContent').html(result);
        $('#tableRightsModal').modal('show');
        $('[data-filterruleid]').on('click', setSearchRule)
        $('[data-ruletype').on('click', click_all)
    }

    function divclose() {
        if (!activ_menu) {
            $('.lmbContextMenu').hide();
        }
        activ_menu = 0;
    }


    function set_color(color) {
        save_rules(ftab, ffield, '3', color);
        var el = "color_" + ftab + "_" + ffield;
        document.getElementById(el).style.backgroundColor = color;
    }

    // --- Farbmenüsteuerung -----------------------------------
    function div4(el, tab, field) {
        limbasDivShow(el, null, "element4");
        ftab = tab;
        ffield = field;
    }

    // --- table edit -----------------------------------
    function div6(el, gtabid) {
        limbasDivShow(el, null, "element6");
        document.edittab_form.gtabid.value = gtabid;
        el = "edit_rule_" + gtabid;
        document.getElementById("edit_rule").value = document.form1.elements[el].value;
    }

    // --- orderby-----------------------------------
    function div7(el, gtabid) {
        limbasDivShow(el, null, "element7");
        document.orderby_form.gtabid.value = gtabid;
        el = "orderby_value_" + gtabid;
        document.getElementById("orderby_value").value = document.form1.elements[el].value;
    }

    // --- indicator -----------------------------------
    function div1(el, gtabid) {
        limbasDivShow(el, null, "element1");
        document.indicator_form.gtabid.value = gtabid;
        el = "indicator_rule_" + gtabid;
        document.getElementById("indicator_rule").value = document.form1.elements[el].value;
    }

    // --- Popup-funktion ----------
    var popups = [];


    // --- Wertespeicher ----------
    var ftab;
    var ffield;
    var saverules = [];

    function save_rules(tab, field, typ, value) {
        saverules[tab + "_" + field + "_" + typ] = value;
    }

    function send() {
        let saval = '';
        for (const e in saverules) {
            saval = saval + e + "_" + saverules[e] + "|";
        }
        document.form1.rules.value = saval;

        const popup = [];
        $.each($(".popicon"), function () {
            if ($(this).attr('src') === 'assets/images/legacy/outliner/minusonly.gif') {
                popup.push($(this).attr('tabid'));
            }
        });

        document.form1.popup.value = popup.join(';');
    }

    /**
     * Uses previously stored filter rules to set default rules in table rights
     */
    function setSearchRule() {
        const $this = $(this);
        const tabId = $this.data('filterruleid');
        let flag = true;
        $("textarea[name^=filterrule_" + tabId + "]").each(function () {
            const $each = $(this);
            const [, , fieldId] = $each.attr('name').split('_');
            const filterPrevVal = $("[name=" + ["filterprev", tabId, fieldId].join("_") + "]").val();
            if (!$each.val() && filterPrevVal) {
                flag = false;
                $each.val(filterPrevVal);
                save_rules(tabId, fieldId, 8);
            }
        });
        if (flag) {
            <?php #todo add title to $lang ?>
            lmbShowWarningMsg("You need to use the filter in the table view right before you use the filter rule function!");
        }
    }

    /**
     * Checks all checkboxes in a column in the field view modal
     */
    function click_all() {
        const $this = $(this);
        const tabId = $this.data('gtabid');
        const ruleType = $this.data('ruletype');

        if ($this.data('isview')) {
            save_rules(tabId, 0, 1);
        }

        let subRules = {
            16: 'versionrule',
            32: 'optionrule',
            13: 'copyrule',
            9: 'needrule',
            2: 'edit',
            1: 'viewrule'
        }

        $('input[name^=' + subRules[ruleType] + '_' + tabId + ']').each(function () {
            let $each = $(this);
            let [, , eachFieldId] = $each.attr('name').split("_");
            save_rules(tabId, eachFieldId, ruleType, $this.val());
            $each.prop('checked', $this.prop('checked'));
        });
    }

    function f_3(PARAMETER) {
        document.form1.action.value = PARAMETER;
        document.form1.submit();
    }
</script>

<div class="container-fluid p-3">
    <form ACTION="main_admin.php" METHOD="post" NAME="form1">
        <?php
        require(COREPATH . "admin/group/group_tabrowsmodal.php");
        ?>
        <input type="hidden" name="action" value="setup_group_tab">
        <INPUT TYPE="hidden" NAME="ID" VALUE="<?= $ID ?>">
        <INPUT TYPE="hidden" NAME="rules">
        <INPUT TYPE="hidden" NAME="popup" VALUE="<?= $popup ?>">

        <input type="hidden" name="">
        <input type="hidden" name="">
        <input type="hidden" name="">

        <div class="row">
            <?php
            $activeTabLinkId = 100;
            require(__DIR__ . '/group_tabs.php');

            $gfilter = getFunctionsFromFile('ext_globalFilter');
            ?>



            <div class="tab-content col-9 ps-0">
                <div class="tab-pane active p-3 d-inline-block border border-start-0 bg-contrast">

                    <h5 class=""><i class="lmb-icon lmb-group"></i>&nbsp;<?= $groupdat["name"][$ID] ?></h5>

                    <div class="">

                        <table ID="tab1" class="table table-borderless table-striped p-0">

                            <?php
                            # ------ Suchkriterien übersetzten -------
                            $searchlang["txt"][1] = $lang[106];
                            $searchlang["txt"][2] = $lang[107];
                            $searchlang["txt"][3] = $lang[108];
                            $searchlang["num"][1] = $lang[713];
                            $searchlang["num"][2] = $lang[711];
                            $searchlang["num"][3] = $lang[712];
                            $searchlang["andor"][1] = $lang[854];
                            $searchlang["andor"][2] = $lang[855];

                            /* --- Spaltenrechte --------------------------------------- */


                            foreach ($_tabgroup['id'] as $bzm => $val):
                            $icon = '';
                            if ($iconClass = $_tabgroup['icon'][$bzm]) {
                                $icon = "<i class=\"lmb-icon $iconClass\"></i>&nbsp;";
                            }
                            ?>
                            <TR class="table-section">
                                <TD colspan="10">
                                    <?= $icon ?>
                                    <?= $_tabgroup['name'][$bzm] ?> (<?= $_tabgroup['beschreibung'][$bzm] ?>)
                                </TD>
                            </TR>
                            <?php
                            foreach ($_gtab["tab_id"] as $key => $value):

                            if ($_gtab["typ"][$key] == 5) {
                                $isview = 1;
                            } else {
                                $isview = 0;
                            }
                            if ($_gtab["tab_group"][$key] != $_tabgroup["id"][$bzm]) {
                                continue;
                            }
                            $icon = 'plusonly';
                            if ($is_popup and in_array($key, $is_popup)) {
                                $icon = 'minusonly';
                            } else {
                                $icon = 'plusonly';
                            }

                            ?>

                            <div>
                                <td>
                                    <i class="lmb-icon lmb-cog-alt cursor-pointer"
                                       data-opentabrows="<?= $key ?>"></i>
                                </td>
                                <td>
                                    <?= $_gtab['table'][$key] ?> (<?= $key ?>)&nbsp;
                                </td>


                                <td colspan="2">
                                    <div class="row">
                                        <?php
                                        $showER = $f_result[$key]["tabeditrule"];
                                        $showIR = $f_result[$key]["indicator"];
                                        $showOV = $f_result[$key]["orderby"];
                                        ?>
                                        <div class="col-3 d-flex flex-column">
                                            <?php if (!$isview) { ?>
                                                <div id="div_rules_empty_<?= $key ?>"
                                                     class="d-flex flex-row">
                                                    <div id="div_edit_rule_<?= $key ?>_empty"
                                                         class="p-0 <?= $showER ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= $showER ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-pencil cursor-pointer py-1 pe-2 me-2"
                                                           onclick="div6(this,'<?= $key ?>')"
                                                           style=""
                                                           title="<?= $lang[2573] ?>"></i>
                                                    </div>
                                                    <div id="div_indicator_rule_<?= $key ?>_empty"
                                                         class="p-0 <?= $showIR ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= $showIR ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-indicator-rule cursor-pointer py-1 pe-2 me-2"
                                                           onclick="div1(this,'<?= $key ?>')"
                                                           title="<?= $lang[1255] ?>"></i>
                                                    </div>
                                                    <div id="div_orderby_value_<?= $key ?>_empty"
                                                         class="p-0 <?= $showOV ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= $showOV ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-textsort-up cursor-pointer py-1 pe-2 me-2"
                                                           onclick="div7(this,'<?= $key ?>')"
                                                           style=""
                                                           title="<?= $lang[1837] ?>"></i>
                                                    </div>
                                                </div>


                                                <div id="div_rules_<?= $key ?>" class="d-flex flex-column">
                                                    <div id="div_edit_rule_<?= $key ?>"
                                                         class="p-0 <?= !$showER ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= !$showER ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-pencil cursor-pointer text-success py-1 pe-2 me-2"
                                                           onclick="div6(this,'<?= $key ?>')"
                                                           title="<?= $lang[2573] ?>"></i>
                                                        <div id="t_edit_rule_<?= $key ?>"
                                                             class=""
                                                             style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                            <?= e($f_result[$key]["tabeditrule"]) ?>
                                                        </div>
                                                        <input
                                                                type="hidden"
                                                                name="edit_rule_<?= $key ?>"
                                                                value="<?= e($f_result[$key]["tabeditrule"]) ?>">
                                                    </div>
                                                    <div id="div_indicator_rule_<?= $key ?>"
                                                         class="p-0 <?= !$showIR ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= !$showIR ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-indicator-rule cursor-pointer text-success py-1 pe-2 me-2"
                                                           onclick="div1(this,'<?= $key ?>')"
                                                           title="<?= $lang[1255] ?>"></i>
                                                        <div id="t_indicator_rule_<?= $key ?>"
                                                             class=""
                                                             style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                            <?= e($f_result[$key]["indicator"]) ?>
                                                        </div>
                                                        <input
                                                                type="hidden"
                                                                class="form-control-sm w-100"
                                                                name="indicator_rule_<?= $key ?>"
                                                                value="<?= e($f_result[$key]["indicator"]) ?>">
                                                    </div>
                                                    <div id="div_orderby_value_<?= $key ?>"
                                                         class="p-0 <?= !$showOV ? '' : 'd-flex' ?> align-items-center"
                                                         style="min-width: 0; <?= !$showOV ? 'display:none;' : '' ?>">
                                                        <i class="lmb-icon lmb-textsort-up cursor-pointer text-success py-1 pe-2 me-2"
                                                           onclick="div7(this,'<?= $key ?>')"
                                                           title="<?= $lang[1837] ?>"></i>
                                                        <div id="t_orderby_value_<?= $key ?>"
                                                             class=""
                                                             style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                                            <?= e($f_result[$key]["orderby"]) ?>
                                                        </div>
                                                        <input
                                                                type="hidden"
                                                                class="form-control-sm w-100"
                                                                name="orderby_value_<?= $key ?>"
                                                                value="<?= e($f_result[$key]["orderby"]) ?>"
                                                        >
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="col-3">
                                            <div class="row w-100 m-0">
                                                <?php
                                                # form selection
                                                $sqlquery = "SELECT ID,NAME,FORM_TYP FROM LMB_FORM_LIST WHERE REFERENZ_TAB = '" . $_gtab["tab_id"][$key] . "'";
                                                $rs1 = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                                                $form = null;
                                                while (lmbdb_fetch_row($rs1)) {
                                                    $id = lmbdb_result($rs1, 'ID');
                                                    $form['name'][$id] = lmbdb_result($rs1, 'NAME');
                                                    $form['typ'][$id] = lmbdb_result($rs1, 'FORM_TYP');
                                                    if($form['typ'][$id] == 1){$form['hastyp1'] = 1;} // detailform
                                                    if($form['typ'][$id] == 2){$form['hastyp2'] = 1;} // listform
                                                }

                                                if ($form['hastyp1']):
                                                    ?>
                                                    <div class="px-0 w-100">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="ps-0 pe-1 d-flex align-items-center">
                                                                <i class="lmb-icon-cus lmb-form-alt"
                                                                   title="<?= $lang[1169] ?>"></i>
                                                            </div>
                                                            <div class="px-0 w-100">
                                                                <select class="form-select form-select-sm"
                                                                        name="view_form_<?= $key ?>"
                                                                        onchange="save_rules('<?= $key ?>','',22)">
                                                                    <option VALUE=\"0\">
                                                                        default
                                                                    </option>

                                                                    <?php
                                                                    foreach ($form['name'] as $fid => $_value):
                                                                        if ($form['typ'][$fid] == 1):
                                                                            ?>
                                                                            <option value="<?= $fid ?>" <?= $f_result[$key]["view_form"] == $fid ? "selected" : "" ?>>
                                                                                <?= $form['name'][$fid] ?>
                                                                            </option>
                                                                        <?php
                                                                        endif;
                                                                    endforeach;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                endif;

                                                //tablelist form selection
                                                if ($form['hastyp2']):
                                                    ?>
                                                    <div class="px-0 w-100">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="ps-0 pe-1 d-flex align-items-center">
                                                                <i class="lmb-icon lmb-icon-cus lmb-list-edit"
                                                                   title="<?= $lang[2756] ?>"></i>
                                                            </div>
                                                            <div class="px-0 w-100">
                                                                <select class="form-select form-select-sm"
                                                                        name="view_lform_<?= $key ?>"
                                                                        onchange="save_rules('<?= $key ?>','',24)">
                                                                    <option VALUE="0">
                                                                        none
                                                                    </option>
                                                                    <?php foreach ($form['name'] as $fid => $_value):
                                                                        if ($form['typ'][$fid] == 2):
                                                                            ?>
                                                                            <option value="<?= $fid ?>" <?= $f_result[$key]["view_lform"] == $fid ? "selected" : "" ?>>
                                                                                <?= $form['name'][$fid] ?>
                                                                            </option>
                                                                        <?php
                                                                        endif;
                                                                    endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                endif;


                                                //calendar form selection
                                                if ($_gtab["typ"][$key] == 2 && $form['hastyp1']) {
                                                        ?>
                                                        <div class="px-0 w-100">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ps-0 pe-1 d-flex align-items-center">
                                                                    <i class="lmb-icon lmb-calendar"
                                                                       title="<?= $lang[1929] ?> <?= $lang[2574] ?>"></i>
                                                                </div>
                                                                <div class="px-0 w-100">
                                                                    <select class="form-select form-select-sm"
                                                                            name="view_tform_<?= $key ?>"
                                                                            onchange="save_rules('<?= $key ?>','',23)">
                                                                        <option value="0">
                                                                            default
                                                                        </option>
                                                                        <?php foreach ($form['name'] as $fid => $_value): ?>
                                                                            <option value="<?= $fid ?>" <?= $f_result[$key]["view_tform"] == $fid ? "selected" : "" ?>>
                                                                                <?= $form['name'][$fid] ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }

                                                    //kanban form selection
                                                    if ($_gtab["typ"][$key] == 7 && $form['hastyp1']):
                                                        ?>
                                                        <div class="px-0 w-100">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ps-0 pe-1 d-flex align-items-center">
                                                                    <i class="lmb-icon lmb-columns"
                                                                       title="kanban <?php $lang[2574] ?>"></i>
                                                                </div>
                                                                <div class="px-0 w-100">
                                                                    <select class="form-select form-select-sm"
                                                                            NAME="view_tform_<?= $key ?>"
                                                                            onchange="save_rules('<?= $key ?>','',23)">
                                                                        <option VALUE="0">
                                                                            default
                                                                        </option>
                                                                        <?php
                                                                        $bzm1 = 1;
                                                                        foreach ($form['name'] as $fid => $_value): ?>
                                                                            <option VALUE="<?= $fid ?>" <?= $f_result[$key]["view_tform"] == $fid ? "selected" : "" ?>>
                                                                                <?= $form['name'][$fid] ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php
                                                    endif;


                                                # Versioning Type
                                                if ($gtab["versioning"][$key] and !$isview) {
                                                    ?>
                                                    <div class="px-0 w-100">
                                                        <div class="d-flex flex-row align-items-center">
                                                            <div class="ps-0 pe-1 d-flex align-items-center">
                                                                <i class="lmb-icon lmb-versioning-type"
                                                                   title="<?= $lang[2565] ?>"></i>
                                                            </div>
                                                            <div class="px-0 w-100">
                                                                <select class="form-select form-select-sm"
                                                                        NAME="versioning_type_<?= $key ?>"
                                                                        onchange="save_rules('<?= $key ?>','',25)">
                                                                    <option VALUE="0">

                                                                    </option>
                                                                    <option VALUE="1" <?= $f_result[$key]["versioning_type"] == 1 ? "selected" : "" ?>>
                                                                        <?= $lang[2144] ?>
                                                                    </option>
                                                                    <option VALUE="2" <?= $f_result[$key]["versioning_type"] == 2 ? "selected" : "" ?>>
                                                                        <?= $lang[2145] ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

                                                if (!$isview) {
                                                    if ($LINK[226] and $gtrigger[$value]) {
                                                        ?>

                                                        <div class="px-0 w-100">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ps-0 pe-1 d-flex align-items-center">
                                                                    <i class="lmb-icon lmb-database ps-0 pe-1"
                                                                       TITLE="trigger"
                                                                       onclick="activ_menu=1;document.getElementById('tab_trigger_<?=$key?>').style.display=''"></i>
                                                                </div>
                                                                <div class="px-0 w-100">
                                                                    <span STYLE="display:none;position:absolute" class="lmbContextMenu"
                                                                          ID="tab_trigger_<?=$key?>"
                                                                          OnClick="activ_menu=1">
                                                                    <select NAME="tab_trigger_<?= $key ?>[]"
                                                                            onchange="save_rules('<?= $key ?>','',26)"
                                                                            multiple size="5">
                                                                        <?php
                                                                        $trlist = array();
                                                                        foreach ($gtrigger[$value]["id"] as $trid => $trval):
                                                                            if (is_array($f_result[$key]["tab_trigger"]) && in_array($trid, $f_result[$key]["tab_trigger"])) {
                                                                                $SELECTED = "selected";
                                                                                $trlist[] = $gtrigger[$value]["trigger_name"][$trid];
                                                                            } else {
                                                                                $SELECTED = "";
                                                                            }
                                                                            ?>
                                                                            <option VALUE="<?= $trid ?>" <?= $SELECTED ?>>
                                                                                    <?= $gtrigger[$value]["trigger_name"][$trid] ?> (<?= $gtrigger[$value]["type"][$trid] ?>)
                                                                            </option>
                                                                        <?php
                                                                        endforeach;
                                                                        ?>
                                                                    </select>
                                                                </span>
                                                                    <input type="TEXT"
                                                                           class="form-control form-control-sm"
                                                                           value="<?= implode(";", $trlist) ?>"
                                                                           onclick="activ_menu=1;document.getElementById('tab_trigger_<?=$key?>').style.display=''">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }


                                                    if ($gfilter) {
                                                        ?>

                                                        <div class="px-0 w-100">
                                                            <div class="d-flex flex-row align-items-center">
                                                                <div class="ps-0 pe-1 d-flex align-items-center">
                                                                    <i class="lmb-icon lmb-filter ps-0 pe-1"
                                                                       TITLE="<?=$lang[3163]?>"
                                                                       onclick="activ_menu=1;document.getElementById('tab_globalfilter_<?=$key?>').style.display=''"></i>
                                                                </div>
                                                                <div class="px-0 w-100">
                                                                    <span STYLE="display:none;position:absolute" class="lmbContextMenu"
                                                                          ID="tab_globalfilter_<?=$key?>"
                                                                          OnClick="activ_menu=1">
                                                                    <select NAME="tab_globalfilter_<?= $key ?>[]"
                                                                            onchange="save_rules('<?= $key ?>','',35)"
                                                                            multiple size="5">
                                                                        <?php
                                                                        $trlist = array();
                                                                        foreach($gfilter as $gfkey => $gfname):
                                                                            if (is_array($f_result[$key]["tab_globalfilter"]) && in_array($gfname, $f_result[$key]["tab_globalfilter"])) {
                                                                                $SELECTED = "selected";
                                                                                $trlist[] = $gfname;
                                                                            } else {
                                                                                $SELECTED = "";
                                                                            }
                                                                            ?>
                                                                            <option VALUE="<?= $gfname ?>" <?= $SELECTED ?>><?= $gfname ?></option>
                                                                        <?php
                                                                        endforeach;
                                                                        ?>
                                                                    </select>
                                                                </span>
                                                                    <input type="TEXT"
                                                                           class="form-control form-control-sm"
                                                                           value="<?= implode(";", $trlist) ?>"
                                                                           onclick="activ_menu=1;document.getElementById('tab_globalfilter_<?=$key?>').style.display=''">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php
                                                    }

                                                }
                                                ?>

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                </tr>
                                <?php
                                endforeach; ?>
                                <?php endforeach; ?>
                        </table>


                    </div>
                    <?php
                    $useSubmitJavascript = true;
                    $isTabRights = true;
                    require __DIR__ . '/submit-footer.php'; ?>
                </div>
            </div>
        </div>
    </form>
</div>
    
