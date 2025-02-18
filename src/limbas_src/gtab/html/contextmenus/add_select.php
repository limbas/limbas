<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $db;

if (!$num_result) {
    $num_result = 15;
}
if (!$start) {
    $start = 1;
}

if ($gfield[$gtabid]["data_type"][$field_id] == 12 or $gfield[$gtabid]["data_type"][$field_id] == 14) {
    $single = 1;
} elseif ($gfield[$gtabid]["unique"][$field_id]) {
    $msingle = 1;
}

/* --- Werte hinzufügen --------------------------------------------- */
if ($select_add && $select_value && $LINK[8]) {
    pool_select_add($select_add, $select_value, $select_keywords, $gtabid, $field_id, $ID, $level_id);
}

/* --- Werte ändern ---------------------------------------- */
if ($change_id && $LINK[8]) {
    pool_select_change($change_id, $gtabid, $field_id, $fs_val, $fs_kw);
}

/* --- Werte sortieren --------------------------------------------- */
if ($select_sort && $select_sort_d && $LINK[8]) {
    pool_select_sort($select_sort, $select_sort_d, $gtabid, $field_id, $level_id);
}

if ($fs_sel) {
    pool_select_prepare($gtabid, $field_id, $ID, $fs_sel);
}

/* --- Werte löschen ---------------------------------------- */
if ($del_id && $LINK[8]) {
    pool_select_delete($del_id, $gtabid, $field_id, $level_id);
}

if ($single) {
    $sqlquery = "SELECT " . $gfield[$gtabid]["field_name"][$field_id] . " FROM " . $gtab["table"][$gtabid] . " WHERE ID = $ID";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    $single_value = lmbdb_result($rs, $gfield[$gtabid]["field_name"][$field_id]);
}

$result_fieldselect = pool_select_list($gtabid, $field_id, $ID, $find_value, $find_keyw, $num_result, $start);
?>

<form ACTION="main.php" METHOD="post" NAME="form_fs">
    <input type="hidden" name="action" value="add_select">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="field_id" value="<?= $field_id ?>">
    <input type="hidden" name="ID" value="<?= $ID ?>">
    <input type="hidden" name="start" value="<?= $start ?>">
    <input type="hidden" name="change_id">
    <input type="hidden" name="del_id">
    <input type="hidden" name="fs_sel">
    <input type="hidden" name="select_sort">
    <input type="hidden" name="select_sort_d">
    <input type="hidden" name="select_cut" value="<?= $gfield[$gtabid]["select_cut"][$field_id] ?>">
    <input type="hidden" name="level_id" value="<?= $level_id ?? 0 ?>">

    <?php
    $parent = array();
    if (isset($result_fieldselect["parent"]) && (!empty($result_fieldselect["parent"])) && is_array($result_fieldselect["parent"])) {
        $result_fieldselect["parent"] = array_reverse($result_fieldselect["parent"], true);
        foreach ($result_fieldselect["parent"] as $k => $v) {
            $parent[] = "<a href=\"#\" onclick=\"document.form_fs.level_id.value=$k;lmbAjax_multiSelect();return false;\" style=\"font-weight:bold;color:blue;\" title=\"$v\">$v</a>";
        }
    }
    $parent = implode("=>", $parent);
    ?>

    <div style="width: 500px" class="tabfringe w-100">
        <?php if ($gfield[$gtabid]["data_type"][$field_id] != 12 || $gfield[$gtabid]["data_type"][$field_id] != 14) {
            $add = "<div class=\"col-1 pe-0\"></div>";
        } ?>

        <div class="tabHeader row">
            <?= $add ?>
            <div class="tabHeaderItem col-4 pe-1"><?= $lang[29] ?></div>
            <div class="tabHeaderItem col-4 px-1"><?= $lang[27] ?></div>
            <div class="col-3 ps-1"></div>
        </div>

        <?php if ($LINK[8]) { ?>
            <div class="tabHeader row">
                <?= $add ?>
                <div class="col-4 pe-1"><input TYPE="text" class="form-control form-control-sm" NAME="select_value"></div>
                <div class="col-4 px-1"><input class="form-control form-control-sm" TYPE="text" NAME="select_keywords"></div>
                <div class="col-3 ps-1">
                    <button type="button" class="btn btn-primary py-1" onclick="lmbAjax_multiSelect()" VALUE="<?= $lang[34] ?>" NAME="select_add"><?= $lang[34] ?></button>
                </div>
            </div>
            <tr class="tabHeader">
                <TD COLSPAN="6">&nbsp;</TD>
            </tr>
        <?php } ?>

        <div class="tabSubHeader row">
            <?= $add ?>
            <div class="col-8 d-flex justify-content-center"><b><?= $result_fieldselect['num_ges']; ?></b>&nbsp;<?= $lang[1843] ?>,&nbsp;<?= $lang[1844] ?>&nbsp;<b><?= $result_fieldselect['num_rows']; ?></b>&nbsp;<?= $lang[1846] ?>
                &nbsp;<b><?= $result_fieldselect['num_sel']; ?></b>&nbsp;<?= $lang[1845] ?>
            </div>
        </div>

        <div class="tabSubHeader row mb-3">
            <?php if ($gfield[$gtabid]["data_type"][$field_id] == 32): ?>
                <div class="col-1 pe-0 d-flex justify-content-center">
                    <div class="form-check"><input class="form-check-input" type="checkbox" style="border:none;" onclick="return fs_check_all(this.checked);"></div>
                </div>
            <?php else: ?>
                <?= $add ?>
            <?php endif; ?>
            <div class="col-4 pe-1"><INPUT class="form-control form-control-sm" TYPE="text" NAME="find_value" VALUE="<?= htmlentities($find_value, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>"></div>
            <div class="col-4 px-1"><INPUT class="form-control form-control-sm" TYPE="text" NAME="find_keyw" VALUE="<?= htmlentities($find_keyw, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>"></div>
            <div class="col-3 ps-1 d-flex">
                <input class="form-control form-control-sm w-100 p-1" TYPE="TEXT" NAME="num_result" onchange="lmbAjax_multiSelect()" VALUE="<?= $num_result ?>">
                <button type="button" class="btn btn-primary py-1" onclick="lmbAjax_multiSelect()" VALUE="<?= $lang[30] ?>"><?= $lang[30] ?></button>
            </div>
        </div>

        <?php
        if (!empty($parent)) { ?>
            <div class="tabSubHeader row">
                <div class="col-2">&nbsp;</div>
                <div class="col-8">$parent</div>
            </div>
        <?php }

        /* --- Ergebnisliste --------------------------------------- */
        if ($result_fieldselect["id"]) {
            if (!$LINK[8]) {
                $readonly = "READONLY";
            }
            foreach ($result_fieldselect["id"] as $key => $value) {
                if ($msingle) {
                    if ($result_fieldselect["select_id"]) {
                        if (in_array($result_fieldselect["id"][$key], $result_fieldselect["select_id"])) {
                            $CHECKED = "CHECKED";
                        } else {
                            $CHECKED = "";
                        }
                    }
                    $selbox = "type=\"radio\" name=\"msrd\" value=\"" . $result_fieldselect["wert"][$key] . "\" class=\"fs_checkbox form-check-input\" active=\"$CHECKED\" elid=\"$value\"";
                } elseif ($single) {
                    if ($single_value == $result_fieldselect["wert"][$key]) {
                        $CHECKED = "CHECKED";
                    } else {
                        $CHECKED = "";
                    }
                    $selbox = "type=\"radio\" name=\"msrd\" value=\"" . $result_fieldselect["wert"][$key] . "\" class=\"fs_checkbox form-check-input\" active=\"$CHECKED\" elid=\"$value\"";
                } else {
                    if ($result_fieldselect["select_id"]) {
                        if (in_array($result_fieldselect["id"][$key], $result_fieldselect["select_id"])) {
                            $CHECKED = "CHECKED";
                        } else {
                            $CHECKED = "";
                        }
                    }
                    $selbox = "type=\"checkbox\" class=\"fs_checkbox form-check-input\" active=\"$CHECKED\" elid=\"$value\" ";
                    $multiple = 1;
                } ?>

                <div class="tabBody row">
                    <div class="tabSubHeaderItem col-1">
                        <div class="form-check w-100 d-flex justify-content-center">
                        <input <?= $selbox ?> style="border:none; background-color: <?= $result_fieldselect["color"][$key] ?>;" onchange="2" <?= $CHECKED ?>>
                        </div>
                    </div>
                    <div class="tabSubHeaderItem col-4 pe-1">
                        <input class="form-control form-control-sm w-100" <?= $readonly ?> type="text" style="width:120px;" name="fs_val[<?= $result_fieldselect["id"][$key] ?>]" value="<?= $result_fieldselect["wert"][$key] ?>" id="fs_val_<?= $result_fieldselect["id"][$key] ?>"
                               onchange="document.form_fs.change_id.value=document.form_fs.change_id.value+'<?= $result_fieldselect["id"][$key] ?>';">
                    </div>
                    <div class="tabSubHeaderItem col-4 px-1">
                        <input class="form-control form-control-sm w-100" <?= $readonly ?> type="text" style="width:120px;" name="fs_kw[<?= $result_fieldselect["id"][$key] ?>]" value="<?= $result_fieldselect["keywords"][$key] ?>"
                               onchange="document.form_fs.change_id.value=document.form_fs.change_id.value+'<?= $result_fieldselect["id"][$key] ?>';">
                    </div>
                    <div class="tabSubHeaderItem col-1 px-1 d-flex align-items-center">
                        <?php if ($gfield[$gtabid]["select_sort"][$field_id] == "SORT" || !$gfield[$gtabid]["select_sort"][$field_id]) { ?>
                            <i class="lmb-icon lmb-long-arrow-up me-1" style="cursor:pointer" border="0" onclick="document.form_fs.select_sort_d.value=1;document.form_fs.select_sort.value='<?= $result_fieldselect['id'][$key] ?>';lmbAjax_multiSelect();"></i>
                            <i class="lmb-icon lmb-long-arrow-down" style="cursor:pointer" border="0" onclick="document.form_fs.select_sort_d.value=2;document.form_fs.select_sort.value='<?= $result_fieldselect['id'][$key] ?>';lmbAjax_multiSelect();"></i>
                        <?php } ?>
                    </div>
                    <div class="tabSubHeaderItem col-1 px-0 d-flex align-items-center">
                        <?php if ($LINK[8]) {
                            if (in_array($gfield[$gtabid]["data_type"][$field_id], array(32 /* multiselect ajax */, 46 /* attribute */))) {
                                $imgst = $result_fieldselect["haslevel"][$key] ? "" : "style=\"opacity:0.3;filter:Alpha(opacity=30)\"";
                                ?>
                                <a class="d-flex align-items-center" href="javascript:document.form_fs.level_id.value='<?= $result_fieldselect["id"][$key] ?>';lmbAjax_multiSelect();">
                                    <i class="lmb-icon lmb-connection" <?= $imgst ?> border="0"></i>
                                </a>
                                <img src="assets/images/legacy/outliner/blank.gif" border="0">
                                <?php
                            }
                            ?>
                            <a class="d-flex align-items-center" href="javascript:document.form_fs.del_id.value='<?= $result_fieldselect["id"][$key] ?>';lmbAjax_multiSelect();">
                                <i class="lmb-icon lmb-trash" border="0"></i>
                            </a>
                        <?php } ?>
                        &nbsp;
                    </div>
                </div>
            <?php }
        } ?>

        <div class="tabFooter row mt-3">
            <?= $add ?>
            <div class="col d-flex align-items-center">
                <i class="lmb-icon lmb-first" STYLE="cursor:pointer" OnClick="document.form_fs.start.value = '1';lmbAjax_multiSelect();"></i>
                <i class="lmb-icon lmb-previous" STYLE="cursor:pointer;font-size:1.5em;" OnClick="document.form_fs.start.value = '<?= ($start - $num_result) ?>'; lmbAjax_multiSelect();"></i>&nbsp;
                <i class="lmb-icon lmb-next" STYLE="cursor:pointer;font-size:1.5em;" OnClick="document.form_fs.start.value = '<?= ($start + $num_result) ?>';lmbAjax_multiSelect();"></i>
                <i class="lmb-icon lmb-last" STYLE="cursor:pointer" OnClick="document.form_fs.start.value = '<?= ($result_fieldselect["num_ges"] - $num_result + 1) ?>'; lmbAjax_multiSelect();"></i>
            </div>
            <div class="col-auto"><button type="button" class="btn btn-primary" VALUE="<?= $lang[33] ?>" NAME="select_change" onclick="lmbAjax_multiSelect(1);"><?= $lang[33] ?></button></div>

        </div>
    </div>
</form>
