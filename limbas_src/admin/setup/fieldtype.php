<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 160
 */
?>
<Script language="JavaScript">
function change_field(id) {
	document.form1.change.value = document.form1.change.value + ";" + id;
}
function del_field(id) {
	document.form1.del.value = id;
	document.form1.submit();
}
</Script>


<FORM ACTION="main_admin.php" METHOD=post name="form1">
	<input type="hidden" name="action"
		value="setup_ftype"> <input type="hidden" name="del"> <input
		type="hidden" name="change"> <input type="hidden" name="add">

	<div class="lmbPositionContainerMain">

		<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="1" WIDTH="100%">
			<TR class="tabHeader">
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[949]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;parse_type &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[1516]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[1517]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[1518]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[1519]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[210]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[924]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[126]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[1078]?> &nbsp;</B></TD>
				<TD class="tabHeaderItem" nowrap><B>&nbsp;<?=$lang[160]?></B></TD>
			</TR>

<?php
/* --- Ergebnisliste --------------------------------------- */
foreach ($result_ftype["id"] as $key => $val) {
    echo "<TR class=\"tabBody\">";
    
    if (!$result_ftype["field_type"][$val]) {
        echo "<TR class=\"tabSubHeader\">";
        echo "<TD VALIGN=\"TOP\" COLSPAN=\"11\" CLASS=\"tabSubHeaderItem\">" . $result_ftype["data_type_exp"][$val] . "</TD>";
    } else {
        if ($result_ftype["local"][$val] == 1) {
            // changed in _depend
            $bg = "red";
            $color = "";
        } elseif ($result_ftype["local"][$val] == 2) {
            // new in _depend
            $bg = "green";
            $color = "white";
        } else {
            $bg = "";
            $color = "";
        }
        
        echo "<TR class=\"tabBody\">";
        echo "<TD class=\"vAlignMiddle\" style=\"color:$color; background-color:$bg;\">&nbsp;";
        
        if ($result_ftype["local"][$val] == 1) {
            ?>
            <A onclick="document.getElementById('quickview_<?=$val?>').style.visibility='visible'"><?= $result_ftype["id"][$val] ?></A>&nbsp;
			<div id="quickview_<?=$val?>" style="position:absolute;overflow:visible;visibility:hidden;border:1px solid black;padding:3px;cursor:pointer;background-color:<?=$farbschema["WEB3"]?>" OnClick="this.style.visibility='hidden'">
                                <?php
            $result1 = "parse_type: " . $result_ftype["parse_type"][$val] . "\n" . $lang[1516] . ": " . $result_ftype["field_type"][$val] . "\n" . $lang[1517] . ": " . $result_ftype["data_type"][$val] . "\n" . $lang[1518] . ": " . $result_ftype["funcid"][$val] . "\n" . $lang[1519] . ": " . $result_ftype["datentyp"][$val] . "\n" . $lang[210] . ": " . $result_ftype["size"][$val] . "\n" . $lang[924] . ": " . $result_ftype["data_type_exp"][$val] . "\n" . $lang[126] . ": " . $result_ftype["format"][$val] . "\n" . $lang[1078] . ": " . $result_ftype["rule"][$val];
            $result2 = "parse_type: " . $result_ftype["system_parse_type"][$val] . "\n" . $lang[1516] . ": " . $result_ftype["system_field_type"][$val] . "\n" . $lang[1517] . ": " . $result_ftype["system_data_type"][$val] . "\n" . $lang[1518] . ": " . $result_ftype["system_funcid"][$val] . "\n" . $lang[1519] . ": " . $result_ftype["system_datentyp"][$val] . "\n" . $lang[210] . ": " . $result_ftype["system_size"][$val] . "\n" . $lang[924] . ": " . $result_ftype["system_data_type_exp"][$val] . "\n" . $lang[126] . ": " . $result_ftype["system_format"][$val] . "\n" . $lang[1078] . ": " . $result_ftype["system_rule"][$val];
            
            echo tableDiff($result1, $result2, "ID:" . $result_ftype["id"][$val], "local copy", "system", 2, 1);
            echo '</div>';

        
        } else {
            echo $result_ftype["id"][$val];
        }
        
        echo "</TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"parse_type_" . $result_ftype["id"][$val] . "\" STYLE=\"width:30px;\" VALUE=\"" . $result_ftype['parse_type'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"field_type_" . $result_ftype["id"][$val] . "\" STYLE=\"width:30px;\" VALUE=\"" . $result_ftype['field_type'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"data_type_" . $result_ftype["id"][$val] . "\" STYLE=\"width:30px;\" VALUE=\"" . $result_ftype['data_type'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"funcid_" . $result_ftype["id"][$val] . "\" STYLE=\"width:30px;\" VALUE=\"" . $result_ftype['funcid'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"datentyp_" . $result_ftype["id"][$val] . "\" STYLE=\"width:150px;\" VALUE=\"" . $result_ftype['datentyp'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"size_" . $result_ftype["id"][$val] . "\" STYLE=\"width:40px;\" VALUE=\"" . $result_ftype['size'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"data_type_exp_" . $result_ftype["id"][$val] . "\" STYLE=\"width:150px;\" VALUE=\"" . $result_ftype['data_type_exp'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"format_" . $result_ftype["id"][$val] . "\" STYLE=\"width:250px;\" VALUE=\"" . $result_ftype['format'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\"><INPUT TYPE=\"TEXT\" NAME=\"rule_" . $result_ftype["id"][$val] . "\" STYLE=\"width:250px;\" VALUE=\"" . $result_ftype['rule'][$val] . "\" OnChange=\"change_field('" . $result_ftype['id'][$val] . "')\"></TD>";
        echo "<TD VALIGN=\"TOP\" ALIGN=\"CENTER\">";
        
        $showTrash = true;
        if ($result_ftype["local"][$val] == 1) {
            // changed in _depend
            $tooltip = "delete only in local table";
        } elseif ($result_ftype["local"][$val] == 2) {
            // new in _depend
            $tooltip = "fully delete!";
        } elseif ($_SESSION['umgvar']['admin_mode']) {
            $tooltip = "fully delete!";
        } else {
            $showTrash = false;
        }
        if ($showTrash) {
            echo "<i class=\"lmb-icon lmb-trash\" title=\"$tooltip\" STYLE=\"cursor:pointer;color:$bg;\" OnClick=\"del_field('" . $result_ftype["id"][$val] . "')\"></i>";
        }
        
        echo "</TD>";
    }
    echo "</TR>";
}

?>

<TR class="tabBody">
				<TD COLSPAN="11"><HR></TD>
			</TR>
			<TR class="tabBody">
				<TD></TD>
				<TD COLSPAN="11"><INPUT TYPE="submit" VALUE="<?=$lang[522]?>"></TD>
			</TR>
			<TR class="tabBody">
				<TD COLSPAN="11"><HR></TD>
			</TR>
			<TR class="tabBody">
				<TD></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="parse_type"
					STYLE="width: 30px;" VALUE="2"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="field_type"
					STYLE="width: 30px;" VALUE="<?=$result_ftype["maxftype"]?>"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="data_type"
					STYLE="width: 30px;" VALUE="<?=$result_ftype["maxdtype"]?>"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="funcid"
					STYLE="width: 30px;" VALUE="<?=$result_ftype["maxfid"]?>"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="datentyp"
					STYLE="width: 150px;"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="size" STYLE="width: 40px;"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="data_type_exp"
					STYLE="width: 150px;"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="format"
					STYLE="width: 250px;"></TD>
				<TD ALIGN="LEFT"><INPUT TYPE="TEXT" NAME="rule"
					STYLE="width: 250px;"></TD>

				<TD><INPUT TYPE="submit" VALUE="<?=$lang[540]?>"
					onclick="document.form1.add.value='1';"></TD>
			</TR>

			<TR class="tabFooter">
				<TD COLSPAN="11"></TD>
			</TR>

		</TABLE>

</FORM>
