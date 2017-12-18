<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 12
 */

global $db;

if (!$num_result) {
    $num_result = 30;
}
if (!$start) {
    $start = 1;
}

if ($gfield[$gtabid]["data_type"][$field_id] == 12 OR $gfield[$gtabid]["data_type"][$field_id] == 14) {
    $single = 1;
} elseif ($gfield[$gtabid]["unique"][$field_id]) {
    $msingle = 1;
}

/* --- Werte hinzufügen --------------------------------------------- */
if ($select_add AND $select_value AND $LINK[8]) {
    select_add($select_add, $select_value, $select_keywords, $gtabid, $field_id, $ID);
}

/* --- Werte ändern ---------------------------------------- */
if ($change_id AND $LINK[8]) {
    select_change($change_id, $gtabid, $field_id, $fs_val, $fs_kw);
}

/* --- Werte sortieren --------------------------------------------- */
if ($select_sort AND $select_sort_d AND $LINK[8]) {
    select_sort($select_sort, $select_sort_d, $gtabid, $field_id);
}

/* --- Werte auswählen --------------------------------------------- */
#if($fs_sel AND $single){
#	if(is_numeric(lmb_substr($fs_sel,1,16))){
#		$sqlquery = "SELECT WERT FROM LMB_SELECT_W WHERE POOL = ".$gfield[$gtabid]["select_pool"][$field_id]." AND ID = ".lmb_substr($fs_sel,1,16);
#		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
#		if(!$rs) {$commit = 1;}
#		$val = odbc_result($rs, "WERT");
#		$sqlquery = "UPDATE ".$gtab["table"][$gtabid]." SET ".$gfield[$gtabid]["field_name"][$field_id]." = '".parse_db_string($val,160)."' WHERE ID = $ID";
#		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
#	}
#}else

if ($fs_sel) {
    select_select($gtabid, $field_id, $ID, $fs_sel);
}

/* --- Werte löschen ---------------------------------------- */
if ($del_id AND $LINK[8]) {
    select_delete($del_id, $gtabid, $field_id, $ID);
}

if ($single) {
    $sqlquery = "SELECT " . $gfield[$gtabid]["field_name"][$field_id] . " FROM " . $gtab["table"][$gtabid] . " WHERE ID = $ID";
    $rs = odbc_exec($db, $sqlquery) or errorhandle(odbc_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    $single_value = odbc_result($rs, $gfield[$gtabid]["field_name"][$field_id]);
}


$result_fieldselect = select_list($gtabid, $field_id, $ID, $find_value, $find_keyw, $num_result, $start);
?>

<FORM ACTION="main.php" METHOD="post" NAME="form_fs">
    <input type="hidden" name="action" value="add_select">
    <input type="hidden" name="gtabid" value="<? echo $gtabid ?>">
    <input type="hidden" name="field_id" value="<? echo $field_id; ?>">
    <input type="hidden" name="ID" value="<? echo $ID; ?>">
    <input type="hidden" name="start" value="<? echo $start; ?>">
    <input type="hidden" name="change_id">
    <input type="hidden" name="del_id">
    <input type="hidden" name="fs_sel">
    <input type="hidden" name="select_sort">
    <input type="hidden" name="select_sort_d">
    <input type="hidden" name="select_cut" value="<?= $gfield[$gtabid]["select_cut"][$field_id] ?>">
    <input type="hidden" name="level_id" value="<?php echo isset($level_id) ? $level_id : 0; ?>">

    <?php
    $parent = array();
    if (isset($result_fieldselect["parent"]) && (!empty($result_fieldselect["parent"])) && is_array($result_fieldselect["parent"])) {

        $result_fieldselect["parent"] = array_reverse($result_fieldselect["parent"], true);
        foreach ($result_fieldselect["parent"] as $k => $v) {
            $parent[] = "<a href=\"#\" onclick=\"document.form_fs.level_id.value=$k;lmbAjax_multiSelect();return false;\" style=\"font-weight:bold;color:blue;\" title=\"$v\">$v</a>";
        }
    }
    $parent = implode("=>", $parent);
#echo "<pre>[".__FILE__."][".__LINE__."][".__FUNCTION__."]\n".print_r($result_fieldselect,1)."</pre>";
    ?>

    <TABLE BORDER="0" cellspacing="1" cellpadding="1" WIDTH="450" class="tabfringe">

        <? if ($gfield[$gtabid]["data_type"][$field_id] != 12 OR $gfield[$gtabid]["data_type"][$field_id] != 14) {
            $add = "<TD></TD>";
        } ?>

        <TR class="tabHeader">
<?= $add ?>
            <TD class="tabHeaderItem" nowrap><div style="margin-left:30px;"><?= $lang[29] ?></div></TD>
            <TD class="tabHeaderItem" nowrap><?= $lang[27] ?></TD>
            <TD class="tabHeaderItem" COLSPAN="2">&nbsp;</TD>
        </TR>

        <? if ($LINK[8]) { ?><TR class="tabHeader"><?= $add ?><TD align="right" style="width:150px;"><INPUT TYPE="text" NAME="select_value" STYLE="width:120px;"></TD><TD><INPUT TYPE="text" STYLE="width:120px;" NAME="select_keywords"></TD><TD COLSPAN="2"><INPUT TYPE="button" onclick="lmbAjax_multiSelect()" VALUE="<?= $lang[34] ?>" NAME="select_add"></TD></TR>
            <TR class="tabHeader"><TD COLSPAN="6">&nbsp;</TD></TR>
<? } ?>

        <TR class="tabSubHeader"><?= $add ?><TD COLSPAN="6"><B style="margin-left:30px;"><?= $result_fieldselect[num_ges]; ?></B>&nbsp;<?= $lang[1843] ?>,&nbsp;<?= $lang[1844] ?>&nbsp;<B><?= $result_fieldselect[num_rows]; ?></B>&nbsp;<?= $lang[1846] ?>&nbsp;<B><?= $result_fieldselect[num_sel]; ?></B>&nbsp;<?= $lang[1845] ?></TD></TR>
        <TR class="tabSubHeader"><?php
            echo ($gfield[$gtabid]["data_type"][$field_id] == 32 ? "<td align=\"center\"><input type=\"checkbox\" style=\"border:none;\" onclick=\"return fs_check_all(this.checked);\"></td>" : $add);
            ?><TD nowrap align="right" style="width:150px;"><INPUT TYPE="text" STYLE="width:120px;" NAME="find_value" VALUE="<?= htmlentities($find_value, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>"></TD><TD><INPUT TYPE="text" STYLE="width:120px;" NAME="find_keyw" VALUE="<?= htmlentities($find_keyw, ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>"></TD><TD><INPUT TYPE="TEXT" STYLE="width:40px;" NAME="num_result" OnChange="lmbAjax_multiSelect()" VALUE="<?= $num_result ?>"></TD><TD><INPUT TYPE="button" onclick="lmbAjax_multiSelect()" VALUE="<?= $lang[30] ?>"></TD></TR>

        <?
        if (!empty($parent))
            echo "<TR class=\"tabSubHeader\"><TD>&nbsp;</TD><TD colspan=\"3\">$parent</TD></TR>";

        /* --- Ergebnisliste --------------------------------------- */
        if ($result_fieldselect["id"]) {

            echo "<tr><td colspan=\"5\"><div style=\"overflow:auto;height:100%\"><table>";

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
                    $selbox = "type=\"radio\" name=\"msrd\" value=\"" . $result_fieldselect["wert"][$key] . "\" class=\"fs_checkbox\" active=\"$CHECKED\" elid=\"$value\"";
                } elseif ($single) {
                    if ($single_value == $result_fieldselect["wert"][$key]) {
                        $CHECKED = "CHECKED";
                    } else {
                        $CHECKED = "";
                    }
                    $selbox = "type=\"radio\" name=\"msrd\" value=\"" . $result_fieldselect["wert"][$key] . "\" class=\"fs_checkbox\" active=\"$CHECKED\" elid=\"$value\"";
                } else {
                    if ($result_fieldselect["select_id"]) {
                        if (in_array($result_fieldselect["id"][$key], $result_fieldselect["select_id"])) {
                            $CHECKED = "CHECKED";
                        } else {
                            $CHECKED = "";
                        }
                    }
                    $selbox = "type=\"checkbox\" class=\"fs_checkbox\" active=\"$CHECKED\" elid=\"$value\" ";
                    $multiple = 1;
                }
                echo "<TR class=\"tabBody\">";
                echo "<TD class=\"tabSubHeaderItem\" ALIGN=\"CENTER\"><INPUT $selbox STYLE=\"border:none; background-color:transparent;\" onchange=\"2\" $CHECKED></TD>";
                echo "<TD class=\"tabSubHeaderItem\" nowrap><INPUT $readonly TYPE =\"TEXT\" STYLE=\"width:120px;\" NAME=\"fs_val[" . $result_fieldselect["id"][$key] . "]\" VALUE=\"" . $result_fieldselect["wert"][$key] . "\" ID=\"fs_val_" . $result_fieldselect["id"][$key] . "\" OnChange=\"document.form_fs.change_id.value=document.form_fs.change_id.value+'" . $result_fieldselect["id"][$key] . ";';\"></TD>";
                echo "<TD class=\"tabSubHeaderItem\" nowrap><INPUT $readonly TYPE =\"TEXT\" STYLE=\"width:120px;\" NAME=\"fs_kw[" . $result_fieldselect["id"][$key] . "]\" VALUE=\"" . $result_fieldselect["keywords"][$key] . "\" OnChange=\"document.form_fs.change_id.value=document.form_fs.change_id.value=document.form_fs.change_id.value+'" . $result_fieldselect["id"][$key] . ";';\"></TD>";
                echo "<TD class=\"tabSubHeaderItem\" nowrap ALIGN=\"LEFT\">";
                if ($gfield[$gtabid]["select_sort"][$field_id] == "SORT" OR ! $gfield[$gtabid]["select_sort"][$field_id]) {
                    echo "&nbsp;&nbsp;<i class=\"lmb-icon lmb-long-arrow-up\" style=\"cursor:pointer\" BORDER=\"0\" OnClick=\"document.form_fs.select_sort_d.value=1;document.form_fs.select_sort.value='" . $result_fieldselect[id][$key] . "';lmbAjax_multiSelect();\"></i>
	        	<i class=\"lmb-icon lmb-long-arrow-down\" style=\"cursor:pointer\" BORDER=\"0\" OnClick=\"document.form_fs.select_sort_d.value=2;document.form_fs.select_sort.value='" . $result_fieldselect[id][$key] . "';lmbAjax_multiSelect();\"></i>";
                }
                echo "</TD><TD class=\"tabSubHeaderItem\" nowrap ALIGN=\"CENTER\">";
                if ($LINK[8]) {
                    if ($gfield[$gtabid]["data_type"][$field_id] == 32) {
                        if ($result_fieldselect["haslevel"][$key]) {
                            $imgst = "";
                        } else {
                            $imgst = "style=\"opacity:0.3;filter:Alpha(opacity=30)\"";
                        }
                        echo "<a href=\"javascript:document.form_fs.level_id.value='" . $result_fieldselect["id"][$key] . "';lmbAjax_multiSelect();\">"
                        . "<i class=\"lmb-icon lmb-connection\" $imgst border=\"0\"></i>"
                        . "</a><img src=\"pic/outliner/blank.gif\" border=\"0\">";
                    }
                    echo "<a href=\"javascript:document.form_fs.del_id.value='" . $result_fieldselect["id"][$key] . "';lmbAjax_multiSelect();\">"
                    . "<i class=\"lmb-icon lmb-trash\" border=\"0\"></i>"
                    . "</a>";
                }
                echo "&nbsp;</TD>";

                echo "</TR>";
            }

            echo "</table></div></td></tr>";
        }
        ?>

        <TR class="tabFooter"><?= $add ?><TD HEIGHT="30" COLSPAN="5"><INPUT TYPE="button" VALUE="<?= $lang[33] ?>" NAME="select_change" onclick="lmbAjax_multiSelect(1);">&nbsp;&nbsp;&nbsp;
                <i class="lmb-icon lmb-first" STYLE="cursor:pointer" OnClick="document.form_fs.start.value = '1';lmbAjax_multiSelect();"></i>
                <i class="lmb-icon lmb-previous" STYLE="cursor:pointer;font-size:1.5em;"  OnClick="document.form_fs.start.value = '<?= ($start - $num_result) ?>'; lmbAjax_multiSelect();"></i>&nbsp;
                <i class="lmb-icon lmb-next" STYLE="cursor:pointer;font-size:1.5em;"  OnClick="document.form_fs.start.value = '<?= ($start + $num_result) ?>';lmbAjax_multiSelect();"></i>
                <i class="lmb-icon lmb-last" STYLE="cursor:pointer"  OnClick="document.form_fs.start.value = '<?= ($result_fieldselect["num_ges"] - $num_result + 1) ?>'; lmbAjax_multiSelect();"></i>
            </TD></TR>





    </TABLE>
</FORM>
<BR><BR>