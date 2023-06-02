<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
if ($showsystabs) {
    if ($showsystabs == 2) {
        $showsystabs = "FALSE";
    } else {
        $showsystabs = "TRUE";
    }
    $sqlquery = "UPDATE LMB_CONF_VIEWS SET USESYSTABS = $showsystabs WHERE ID = $viewid";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
}

?>

<i id="relationSign" class="lmb-icon lmb-chain-alt"
   style="position:absolute;overflow:hidden;z-index:9999;visibility:hidden;"></i>
<div id="fieldinfo" class="ajax_container"
     style="width:300px;position:absolute;z-index:99999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?= $farbschema["WEB11"] ?>"
     OnClick="activ_menu=1;"></div>
<div id="tablist" class="ajax_container"
     style="height:300px;position:absolute;overflow:auto;z-index:999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?= $farbschema["WEB11"] ?>">
    <table>
        <?php
        $odbc_table = dbf_20(array($DBA["DBSCHEMA"], null, "'TABLE','VIEW','MATVIEW'"));
        foreach ($odbc_table["table_name"] as $tkey => $tvalue) {
            if (!$gview["showsystabs"]) {
                if (lmb_substr($odbc_table["table_name"][$tkey], 0, 4) == "lmb_" or lmb_substr($odbc_table["table_name"][$tkey], 0, 5) == "ldms_") {
                    continue;
                }
            }
            if (lmb_strtoupper($odbc_table["table_type"][$tkey]) == "VIEW") {
                $val = "VIEW :: " . $odbc_table["table_name"][$tkey];
            } else {
                $val = $odbc_table["table_name"][$tkey];
            }
            echo "<tr><td nowrap><a href=\"#\" OnClick=\"lmbAjax_ViewEditorPattern('" . str_replace("=", "", base64_encode($tvalue)) . ";;'+document.getElementById('tablist').style.left+','+document.getElementById('tablist').style.top);divclose();\">" . $val . "</a></td></tr>";
        }
        if ($gview["showsystabs"]) {
            $CKECKED = "CHECKED";
            $val = 2;
        } else {
            $val = 1;
        }
        echo "<tr><td><hr><input type=\"checkbox\" OnClick=\"document.form1.showsystabs.value=$val;document.form1.submit();\" $CKECKED>&nbsp;" . $lang[2635] . "</td></tr>";
        ?>
    </table>
</div>

<table class="table table-borderless table-sm tabBody">

    <TR>
        <Th><?= $gview["viewname"] ?></Th>
    </TR>

    <TR>
        <TD class="border bg-light" style="height:70vh;">
            <div id="vieweditorPattern" class="overflow-auto w-100 h-100 position-relative"
                 oncontextmenu="limbasDivShow('',event,'tablist');return false;">
                <?php
                require_once(COREPATH . 'admin/tables/view/viewschema.php');
                ?>
            </div>
        </TD>
    </TR>
    <TR>
        <TD class="w-100 p-1 border-bottom">
            <div id="lmbViewfieldContainer" class="overflow-auto">
                <?php
                show_viewFields($viewid);
                ?>
            </div>
        </TD>
    </TR>
</table>

<?php
$confirm = '';
if ($gview['setManually']) {
    $text = 'Storing configuration overwrites manually edited sql! Proceed?';
    $confirm = "if (!confirm('$text')) { return; }";
}
?>

<div class="row mt-3">

    <div class="col-md-4">
        <?= $lang[1996] ?><?php if ($gview["ispublic"]): ?>
            <i class="lmb-icon lmb-check"></i>
        <?php else: ?>
            <i class="lmb-icon lmb-minus-circle"></i>
        <?php endif; ?>&nbsp;
        <?= $lang[2023] ?><?php if ($gview["viewexists"]): ?>
            <i class="lmb-icon lmb-check"></i>
        <?php else: ?>
            <i class="lmb-icon lmb-minus-circle"></i>
        <?php endif; ?>&nbsp
        Syntax<?php if ($gview["isvalid"]): ?>
            <i class="lmb-icon lmb-check"></i>
        <?php else: ?>
            <i class="lmb-icon lmb-minus-circle"></i>
        <?php endif; ?>
    </div>
    <div class="col-md-4 text-center">
        <button type="button" class="btn btn-sm btn-outline-dark"
                onclick="<?= $confirm ?> document.form1.act.value='view_save';setDrag();document.form1.submit();"><?= $lang[2940] ?></button>
        <button type="button" class="btn btn-sm btn-outline-dark"
                onclick="document.form1.act.value='view_isvalid';setDrag();document.form1.submit();"><?= $lang[2941] ?></button>
    </div>
    <div class="col-md-4 text-end">
        <?php
        if ($gview["viewexists"]):

            if ($gview["ispublic"]) {
                $ispublic_lang = $lang[2943];
            } else {
                $ispublic_lang = $lang[1996];
            }

            ?>

            <button type="button" class="btn btn-sm btn-outline-dark"
                    onclick="document.form1.act.value='view_public';document.form1.submit();" <?= (!$gview["isvalid"]) ? 'disabled' : '' ?>><?= $ispublic_lang ?></button>


            <button type="button" class="btn btn-sm btn-outline-dark"
                    onclick="document.form1.act.value='view_replace';document.form1.submit();"><?= $lang[2942] ?></button>
            <button type="button" class="btn btn-sm btn-outline-dark"
                    onclick="document.form1.act.value='view_drop';document.form1.submit();"><?= $lang[2023] . " " . $lang[160] ?></button>

        <?php else: ?>

            <button type="button" class="btn btn-sm btn-outline-dark"
                    onclick="document.form1.act.value='view_create';document.form1.submit();"><?= $lang[2942] ?></button>

        <?php endif; ?>
    </div>

</div>

<script>
    document.getElementById("lmbViewfieldContainer").style.width = (window.innerWidth - 60);
</script>
	
