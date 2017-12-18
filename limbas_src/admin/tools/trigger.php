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
 * ID: 218
 */
?>

<Script language="JavaScript">

function open_definition(id){
	if(document.getElementById('definition_'+id).style.display == 'none'){
		document.getElementById('definition_'+id).style.display = '';
	}else{
		document.getElementById('definition_'+id).style.display = 'none';
	}
}

function delete_definition(id){
	document.getElementById('trigger_'+id).style.display = 'none';
	document.getElementById('definition_'+id).style.display = 'none';
	document.getElementById("definition_change_"+id).value='2';
}

</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_trigger">
<input type="hidden" name="sortup_definition">
<input type="hidden" name="sortdown_definition">
<input type="hidden" name="trigger_typ" value="<?=$trigger_typ?>">

<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

<?

$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE'"));
$odbc_table =  $odbc_table["table_name"];

if($trigger_typ == 1){
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemActive"><?=$lang[2450]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_trigger&trigger_typ=2'"><?=$lang[2451]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[2267]?></TD><TD class="tabHeaderItem"><?=$lang[2270]?></TD><TD class="tabHeaderItem"><?=$lang[2271]?></TD><TD class="tabHeaderItem"><?=$lang[2272]?></TD><TD class="tabHeaderItem"><?=$lang[2269]?></TD><TD class="tabHeaderItem">aktiv</TD><TD class="tabHeaderItem"></TD></TR>
	<?
	foreach ($result_trigger as $key1 => $value1){
		
		foreach ($value1["id"] as $key2 => $value2){
			if(!$showsystr AND (substr($value1["name"][$key2],0,12) == "INSERT_VERK_" OR substr($value1["name"][$key2],0,12) == "UPDATE_VERK_" OR substr($value1["name"][$key2],0,16) == "LMB_LASTMODIFIED")) {continue 2;}
		}
		
		echo "<TR class=\"tabSubHeader\"><TD colspan=\"7\" class=\"tabSubHeaderItem\">$lang[2268]: <b>".$key1."</b></TD></TR>";
		foreach ($value1["id"] as $key2 => $value2){
			if(!$value1["value"][$key2]){$displ = "";}else{$displ = "none";}
			echo "<TR class=\"tabBody\" ID=\"trigger_".$value1["id"][$key2]."\">";
			echo "<TD>".$value1["name"][$key2]."</TD>";
			echo "<TD>".get_date($value1["editdatum"][$key2],1)."</TD>";
			echo "<TD>".$userdat["bezeichnung"][$value1["edituser"][$key2]]."</TD>";
			echo "<TD>".$value1["position"][$key2]." ".$value1["type"][$key2]."</TD>";
			echo "<TD align=\"center\"><i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer\" OnClick=\"open_definition('".$value1["id"][$key2]."')\"></i></TD>";
			echo "<TD><INPUT TYPE=\"checkbox\" ID=\"active_" . $value1["id"][$key2] . "\" NAME=\"active[" . $value1["id"][$key2] . "]\" " . ($value1["active"][$key2]?"checked":"") . " onchange=\"document.form1.definition_change_".$value1["id"][$key2].".value='1';\"></TD>";
			echo "<TD align=\"center\" colspam=\"2\"><i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" OnClick=\"delete_definition('".$value1["id"][$key2]."')\"></i></TD></TR>";
			echo "<TR class=\"tabBody\" ID=\"definition_".$value1["id"][$key2]."\" style=\"display:$displ\"><TD COLSPAN=\"7\"><TEXTAREA NAME=\"trigger_definition[".$value1["id"][$key2]."]\" OnChange=\"document.form1.definition_change_".$value1["id"][$key2].".value='1';\" style=\"width:100%;height:100px;overflow:visible;\">".htmlentities($value1["value"][$key2],ENT_QUOTES,$umgvar["charset"])."</TEXTAREA></TD></TR>";
			echo "<INPUT TYPE=\"hidden\" ID=\"definition_change_".$value1["id"][$key2]."\" NAME=\"definition_change_".$value1["id"][$key2]."\">";
		}
	}
	
	
	if($showsystr){$showsystr = "checked";}
	echo "<TR class=\"tabBody\"><TD colspan=\"8\"><HR></TD></TR>";
	echo "<TR class=\"tabBody\"><TD colspan=\"7\" align=\"right\">
	show limbas trigger<input type=\"checkbox\" NAME=\"showsystr\" $showsystr onchange=\"this.form.submit();\">&nbsp;&nbsp;
	<input type=\"submit\" NAME=\"syncronize\" value=\"$lang[2275]\">&nbsp;&nbsp;
	<input type=\"submit\" NAME=\"change\" value=\"$lang[2273]\">
	</TD></TR>";
	echo "<TR class=\"tabBody\"><TD colspan=\"7\"><HR></TD></TR>";
	echo "<TR class=\"tabBody\">
	<TD><INPUT TYPE=\"TEXT\" NAME=\"add_trigger_name\" style=\"width:120px;\"></TD>
	<TD COLSPAN=\"2\"><SELECT NAME=\"add_trigger_table\"><OPTION><OPTION>".implode("<OPTION>",$odbc_table)."</TD>
	<TD><SELECT NAME=\"add_trigger_pos\"><OPTION value=\"AFTER\">AFTER</OPTION><OPTION value=\"BEFORE\">BEFORE</OPTION></SELECT></TD>
	<TD><SELECT NAME=\"add_trigger_type\"><OPTION value=\"INSERT\">INSERT</OPTION><OPTION value=\"UPDATE\">UPDATE</OPTION><OPTION value=\"DELETE\">DELETE</OPTION></SELECT>
	</TD><TD align=\"right\" colspan=\"2\"><input type=\"submit\" NAME=\"add\" value=\"$lang[2274]\"></TD></TR>
	<TR class=\"tabFooter\"><TD colspan=\"7\"></TD></TR>
	</TABLE>";
	
}elseif($trigger_typ == 2){
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
	<TD nowrap class="tabpoolItemInactive" OnClick="document.location.href='main_admin.php?action=setup_trigger&trigger_typ=1'"><?=$lang[2450]?></TD>
	<TD nowrap class="tabpoolItemActive" ><?=$lang[2451]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>
	
	<TR><TD class="tabpoolfringe">
	
	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[2267]?></TD><TD class="tabHeaderItem"><?=$lang[2270]?></TD><TD class="tabHeaderItem"><?=$lang[2271]?></TD><TD class="tabHeaderItem"><?=$lang[2272]?></TD><TD class="tabHeaderItem"><?=$lang[2269]?></TD><TD class="tabHeaderItem">aktiv</TD><TD class="tabHeaderItem">order</TD><TD class="tabHeaderItem"></TD></TR>
	<?
	foreach ($result_trigger as $key1 => $value1){
		echo "<TR class=\"tabSubHeader\"><TD colspan=\"8\" class=\"tabSubHeaderItem\">$lang[2268]: <b>".$key1."</b></TD></TR>";
		foreach ($value1["id"] as $key2 => $value2){
			if(!$value1["value"][$key2]){$displ = "";}else{$displ = "none";}
			echo "<TR class=\"tabBody\" ID=\"trigger_".$value1["id"][$key2]."\">";
			echo "<TD>".$value1["name"][$key2]."</TD>";
			echo "<TD>".get_date($value1["editdatum"][$key2],1)."</TD>";
			echo "<TD>".$userdat["bezeichnung"][$value1["edituser"][$key2]]."</TD>";
			echo "<TD>".$value1["position"][$key2]." ".$value1["type"][$key2]."</TD>";
			echo "<TD align=\"center\"><i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer\" OnClick=\"open_definition('".$value1["id"][$key2]."')\"></i></TD>";
			echo "<TD><INPUT TYPE=\"checkbox\" ID=\"active_" . $value1["id"][$key2] . "\" NAME=\"active[" . $value1["id"][$key2] . "]\" " . ($value1["active"][$key2]?"checked":"") . " onchange=\"document.form1.definition_change_".$value1["id"][$key2].".value='1';\"></TD>";
			
			echo "<TD align=\"center\" colspam=\"2\">
			<i class=\"lmb-icon lmb-long-arrow-up\" style=\"cursor:pointer\" OnClick=\"document.form1.sortup_definition.value='".$value1["id"][$key2]."';document.form1.submit();\"></i>
			<i class=\"lmb-icon lmb-long-arrow-down\" style=\"cursor:pointer\" OnClick=\"document.form1.sortdown_definition.value='".$value1["id"][$key2]."';document.form1.submit();\"></i>
			</TD>";
			
			echo "<TD align=\"center\" colspam=\"2\"><i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" OnClick=\"delete_definition('".$value1["id"][$key2]."')\"></i></TD></TR>";
			echo "<TR ID=\"definition_".$value1["id"][$key2]."\" style=\"display:$displ\"><TD COLSPAN=\"8\"><TEXTAREA NAME=\"trigger_definition[".$value1["id"][$key2]."]\" OnChange=\"document.form1.definition_change_".$value1["id"][$key2].".value='1';\" style=\"width:100%;height:100px;overflow:visible;\">".htmlentities($value1["value"][$key2],ENT_QUOTES,$umgvar["charset"])."</TEXTAREA></TD></TR>";
			echo "<INPUT TYPE=\"hidden\" ID=\"definition_change_".$value1["id"][$key2]."\" NAME=\"definition_change_".$value1["id"][$key2]."\">";
		}
	}
	
	echo "<TR class=\"tabBody\"><TD colspan=\"8\"><HR></TD></TR>";
	echo "<TR class=\"tabBody\"><TD colspan=\"8\" align=\"right\"><input type=\"submit\" NAME=\"change\" value=\"$lang[2273]\"></TD></TR>";
	echo "<TR class=\"tabBody\"><TD colspan=\"8\"><HR></TD></TR>";
	echo "<TR class=\"tabBody\">
	<TD><INPUT TYPE=\"TEXT\" NAME=\"add_trigger_name\" style=\"width:120px;\"></TD>
	<TD COLSPAN=\"2\"><SELECT NAME=\"add_trigger_table\"><OPTION><OPTION>".implode("<OPTION>",$odbc_table)."</TD>
	<TD><SELECT NAME=\"add_trigger_pos\"><OPTION value=\"AFTER\">AFTER</OPTION><OPTION value=\"BEFORE\">BEFORE</OPTION></SELECT></TD>
	<TD><SELECT NAME=\"add_trigger_type\"><OPTION value=\"INSERT\">INSERT</OPTION><OPTION value=\"UPDATE\">UPDATE</OPTION><OPTION value=\"DELETE\">DELETE</OPTION></SELECT></TD>
	<TD align=\"right\" colspan=\"3\"><input type=\"submit\" NAME=\"add\" value=\"$lang[2274]\"></TD></TR>
	<TR class=\"tabFooter\"><TD colspan=\"7\"></TD></TR>
	</TABLE>";
}
?>

</td></tr></table>

</div>
</FORM>