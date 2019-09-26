<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 222
 */
?>

<Script language="JavaScript">
function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}
</SCRIPT>



<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="action" value="setup_group_diags">

<div class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD valign="top" height="100%">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" height="100%" style="border-collapse:collapse;position:sticky;top:20px;">
<?php if($LINK[135]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][135]?>" TITLE="<?=$lang[$LINK["desc"][135]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][135]."\"></i>&nbsp;".$lang[$LINK["name"][135]] ?></TD></TR><?php }?>
<?php if($LINK[76]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][76]?>" TITLE="<?=$lang[$LINK["desc"][76]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][76]."\"></i>&nbsp;".$lang[$LINK["name"][76]] ?></TD></TR><?php }?>
<?php if($LINK[100]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][100]?>" TITLE="<?=$lang[$LINK["desc"][100]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][100]."\"></i>&nbsp;".$lang[$LINK["name"][100]] ?></TD></TR><?php }?>
<?php if($LINK[192]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][192]?>" TITLE="<?=$lang[$LINK["desc"][192]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][192]."\"></i>&nbsp;".$lang[$LINK["name"][192]] ?></TD></TR><?php }?>
<?php if($LINK[260]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][260]?>" TITLE="<?=$lang[$LINK["desc"][260]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][260]."\"></i>&nbsp;".$lang[$LINK["name"][260]] ?></TD></TR><?php }?>
<?php if($LINK[291]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][291]?>" TITLE="<?=$lang[$LINK["desc"][291]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][291]."\"></i>&nbsp;".$lang[$LINK["name"][291]] ?></TD></TR><?php }?>
<?php if($LINK[292]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemActive" OnClick="<?=$LINK["link_url"][292]?>" TITLE="<?=$lang[$LINK["desc"][292]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][292]."\"></i>&nbsp;".$lang[$LINK["name"][292]] ?></TD></TR><?php }?>
<?php if($LINK[290]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][290]?>" TITLE="<?=$lang[$LINK["desc"][290]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][290]."\"></i>&nbsp;".$lang[$LINK["name"][290]] ?></TD></TR><?php }?>
<?php if($LINK[293]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][293]?>" TITLE="<?=$lang[$LINK["desc"][293]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][293]."\"></i>&nbsp;".$lang[$LINK["name"][293]] ?></TD></TR><?php }?>
<TR><TD class="tabHpoolItemSpaceGtab"><div style="height:100%">&nbsp;</div></TD></TR>

</TABLE>

</TD><TD width="500px" class="tabHpoolfringe" style="border-left:none">

<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody">
    <TR class="tabHeader"><TD class="tabHeaderItem" colspan="23"><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></TD></TR>
<TR><TD colspan="5"><HR></TD></TR>

<TR class="tabHeader"><TD class="tabHeaderItem"><i class="lmb-icon-8 lmb-line-chart"></i>&nbsp;<b><?=$lang[2119]?></b></TD><TD class="tabHeaderItem"><?=$lang[575]?></TD><TD class="tabHeaderItem"><?=$lang[2088]?></TD></TR>
<?php
if($rulelist_){
foreach ($rulelist_ as $key => $value){
	if($gtab["table"][$key]){
	echo "<TR bgcolor=\"".$farbschema["WEB7"]."\"><TD style=\"color:green\"colspan=\"3\" ><i class=\"lmb-icon-8 lmb-table\"></i>&nbsp;".$gtab["desc"][$key]."</TD></TR>";
	if($value["id"]){
	foreach ($value["id"] as $key2 => $value2){
		if($value2){
			if($grouprule["hasview"][$value2]){$CHECKED1 = "CHECKED";}else{$CHECKED1 = "";}
			if($grouprule["hashidden"][$value2]){$CHECKED2 = "CHECKED";}else{$CHECKED2 = "";}
			echo "<TR><TD width=\"200\">&nbsp;&nbsp;&nbsp;&nbsp;".$value["name"][$key2]."</TD><TD>";
			if($levelrule["hasview"][$value2] OR !$group_level){
				echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"setrule[".$value2."]\" $CHECKED1>";
			}else{
				echo "<INPUT TYPE=\"checkbox\" $CHECKED1 readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
			}
			echo "</TD><TD>";
			if($CHECKED1){echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"sethidden[".$value2."]\" $CHECKED2>";}
			echo "</TD></TR>";
		}
	}}}
}}

if($session["user_id"] != 1 AND $session["group_id"] == $ID){
lmb_alert("no permission to change own group!");
}else{
?>
<TR><TD COLSPAN="3" ALIGN="CENTER"><HR></TD></TR>
<TR><TD colspan="3"><INPUT TYPE="submit" VALUE="<?=$lang[33]?>" NAME="change_rules">&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang[2107]?>:<input type="checkbox" name="addsubgroup" value="1"></TD></TR>
<?php }?>
<TR><TD COLSPAN="3"class="tabFooter">&nbsp;</TD></TR>
</TABLE>



</TD></TR></TABLE>
</DIV>
</FORM>

