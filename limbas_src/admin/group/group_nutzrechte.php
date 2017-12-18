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
 * ID: 120
 */
?>

<Script language="JavaScript">
// --- Wertespeicher ----------
var ftab;
var ffield;
var saverules = new Array();

function save_rules(id){
	saverules[id] = 1;
}

function send(){
	var saval = '';
    for (var e in saverules){
    	var saval = saval + e + ";";    
    }
    document.form1.rules.value = saval;
    document.form1.submit();
}

function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}

function check_all(kat,val,sub,main){
	var chk = val.checked;
	var cc = null;
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");
		if(sub == 1){var group = cid[1];var rule = cid[3];}
		else if(sub == 2){var group = cid[2];var rule = cid[3]}
		if(cc.type == "checkbox" && group == kat && main == cid[1]){
			if(chk){
				cc.checked = 1;
			}else{
				cc.checked = 0;
			}
			save_rules(rule);
		}
	}
}
</Script>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="action" value="setup_group_nutzrechte">
<input type="hidden" name="rules">

<div>

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD valign="top" height="100%">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" height="100%" style="border-collapse:collapse">
<?if($LINK[135]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][135]?>" TITLE="<?=$lang[$LINK["desc"][135]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][135]."\"></i>&nbsp;".$lang[$LINK["name"][135]];?></TD></TR><?}?>
<?if($LINK[76]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemActive" OnClick="<?=$LINK["link_url"][76]?>" TITLE="<?=$lang[$LINK["desc"][76]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][76]."\"></i>&nbsp;".$lang[$LINK["name"][76]];?></TD></TR><?}?>
<?if($LINK[100]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][100]?>" TITLE="<?=$lang[$LINK["desc"][100]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][100]."\"></i>&nbsp;".$lang[$LINK["name"][100]];?></TD></TR><?}?>
<?if($LINK[192]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][192]?>" TITLE="<?=$lang[$LINK["desc"][192]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][192]."\"></i>&nbsp;".$lang[$LINK["name"][192]];?></TD></TR><?}?>
<?if($LINK[260]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][260]?>" TITLE="<?=$lang[$LINK["desc"][260]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][260]."\"></i>&nbsp;".$lang[$LINK["name"][260]];?></TD></TR><?}?>
<?if($LINK[291]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][291]?>" TITLE="<?=$lang[$LINK["desc"][291]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][291]."\"></i>&nbsp;".$lang[$LINK["name"][291]];?></TD></TR><?}?>
<?if($LINK[292]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][292]?>" TITLE="<?=$lang[$LINK["desc"][292]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][292]."\"></i>&nbsp;".$lang[$LINK["name"][292]];?></TD></TR><?}?>
<?if($LINK[290]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][290]?>" TITLE="<?=$lang[$LINK["desc"][290]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][290]."\"></i>&nbsp;".$lang[$LINK["name"][290]];?></TD></TR><?}?>
<?if($LINK[293]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][293]?>" TITLE="<?=$lang[$LINK["desc"][293]]?>"><?echo "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][293]."\"></i>&nbsp;".$lang[$LINK["name"][293]];?></TD></TR><?}?>
<TR><TD class="tabHpoolItemSpaceGtab"><div style="height:100%">&nbsp;</div></TD></TR>

</TABLE>

</TD><TD width="500px" class="tabHpoolfringe" style="border-left:none">

<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody">
    <TR class="tabHeader"><TD class="tabHeaderItem" colspan="23"><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></TD></TR>
<TR><TD colspan="23"><HR></TD></TR>


</TD></TR>
<TR class="tabHeader">
<TD nowrap class="tabHeaderItem"><?=$lang[573]?></TD>
<TD nowrap class="tabHeaderItem"><?=$lang[574]?></TD>
<TD nowrap class="tabHeaderItem"></TD>
<TD nowrap class="tabHeaderItem"><?=$lang[575]?></TD>
</TR>

<?
foreach($link_groupdesc as $key => $value){
	echo "<TR BGCOLOR=\"$farbschema[WEB7]\"><TD COLSPAN=\"3\"><B>".$link_groupdesc[$key][0]."</B></TD><TD ALIGN=\"CENTER\"><INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent;\" OnClick=\"check_all('$key',this,1,'$key');\"></TD></TR>";

	foreach($link_groupdesc[$key] as $key2 => $value2){
		foreach($result_links["sort"] as $bzm => $value0){
			if($result_links["maingroup"][$bzm] == $key AND $result_links["subgroup"][$bzm] == $key2){
				if($result_links["subgroup"][$bzm] != $tmpsubg){
					echo "<TR BGCOLOR=\"".$farbschema["WEB8"]."\"><TD COLSPAN=\"3\">&nbsp;&nbsp;&nbsp;&nbsp;<B STYLE=\"color:grey;\">".$link_groupdesc[$key][$result_links["subgroup"][$bzm]]."</B></TD><TD ALIGN=\"CENTER\"><INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent;\" OnClick=\"check_all('".$result_links[subgroup][$bzm]."',this,2,'$key');\"></TD></TR>";
				}
				$tmpsubg = $result_links["subgroup"][$bzm];
				if($LINK[$bzm] OR $session["superadmin"] == 1){
					echo "<TR>";
					echo "<TD>".$lang[$result_links["name"][$bzm]]."&nbsp;</TD>";
					echo "<TD>".$lang[$result_links["desc"][$bzm]]."&nbsp;</TD>";
					echo "<TD ALIGN=\"CENTER\">";
					if($result_links["icon_url"][$bzm]){echo "<i class=\"lmb-icon ".$result_links["icon_url"][$bzm]."\" BORDER=\"0\"></i>";}
					echo "&nbsp;</A></TD><TD ALIGN=\"CENTER\">";
					if($result_lgroup_link["PERM"][$bzm] == 2 OR !$result_lgroup_link["PERM"]){
						echo "<INPUT STYLE=\"border:none;background-color:transparent;\" TYPE=\"checkbox\" ID=\"menu_".$key."_".$tmpsubg."_".$result_links["link_id"][$bzm]."\" NAME=\"menu[".$result_links[link_id][$bzm]."]\" onclick=\"save_rules('".$result_links[link_id][$bzm]."');\"";
						if($result_links["perm"][$bzm] == 2){echo " CHECKED";}
						echo ">";
					}else{
						echo "<INPUT TYPE=\"checkbox\" readonly disabled style=\"opacity:0.3;filter:Alpha(opacity=30);\">";
					}
					echo "</TD>";
					echo "</TR>\n";
				}

			}
		}
	}
	echo "\n";
}
?>

<TR><TD colspan="5"><HR></TD></TR>
<?
if($session["user_id"] != 1 AND $session["group_id"] == $ID){
lmb_alert("no permission to change own group!");
}else{
?>
<TR><TD COLSPAN="5"><INPUT TYPE="BUTTON" OnCLick="send();" VALUE="<?=$lang[913]?>">&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang[2107]?>:<input type="checkbox" name="addsubgroup" value="1"></TD></TR>
<?}?>
<TR><TD colspan="5" class="tabFooter">&nbsp;</TD></TR>
</TABLE>

</TD></TR></TABLE>
</FORM>
</DIV>
