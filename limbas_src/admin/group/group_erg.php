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
 * ID: 115
 */
?>

<SCRIPT LANGUAGE="JavaScript">
function gurefresh() {
        gu = confirm("<?=$lang[896]?>");
        if(gu) {
        	refresh = open("main_admin.php?action=setup_grusrref&group_id=<?=$ID?>&group_name=<?=urlencode($groupdat["name"][$ID])?>" ,"refresh","toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=650");
        }
}
function lrefresh() {
        link = confirm("<?=$lang[896]?>");
        if(link) {
            refresh = open("main_admin.php?action=setup_linkref&group=<?=$ID?>" ,"refresh","toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=650");
        }
}

function del() {
        del = confirm("<?=$lang[1254]?>");
        if(del) {
                document.location.href="main_admin.php?action=setup_group_erg&group_del=1&ID=<?=$ID?>&duf=document.form1.duf.value";
        }
}

function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}

</SCRIPT>



<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_group_erg">
<input type="hidden" name="ID" VALUE="<?=$ID?>">
<input type="hidden" name="maingroup">
<input type="hidden" name="change">
<input type="hidden" name="duf" value="1">



<div>

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD valign="top" height="100%" style="min-width:150px">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" height="100%" width="100%" style="border-collapse:collapse;position:sticky;top:20px;">
<?php if($LINK[135]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemActive" OnClick="<?=$LINK["link_url"][135]?>" TITLE="<?=$lang[$LINK["desc"][135]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][135]."\"></i>&nbsp;".$lang[$LINK["name"][135]] ?></TD></TR><?php }?>
<?php if($LINK[76]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][76]?>" TITLE="<?=$lang[$LINK["desc"][76]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][76]."\"></i>&nbsp;".$lang[$LINK["name"][76]] ?></TD></TR><?php }?>
<?php if($LINK[100]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][100]?>" TITLE="<?=$lang[$LINK["desc"][100]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][100]."\"></i>&nbsp;".$lang[$LINK["name"][100]] ?></TD></TR><?php }?>
<?php if($LINK[192]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][192]?>" TITLE="<?=$lang[$LINK["desc"][192]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][192]."\"></i>&nbsp;".$lang[$LINK["name"][192]] ?></TD></TR><?php }?>
<?php if($LINK[260]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][260]?>" TITLE="<?=$lang[$LINK["desc"][260]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][260]."\"></i>&nbsp;".$lang[$LINK["name"][260]] ?></TD></TR><?php }?>
<?php if($LINK[291]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][291]?>" TITLE="<?=$lang[$LINK["desc"][291]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][291]."\"></i>&nbsp;".$lang[$LINK["name"][291]] ?></TD></TR><?php }?>
<?php if($LINK[292]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][292]?>" TITLE="<?=$lang[$LINK["desc"][292]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][292]."\"></i>&nbsp;".$lang[$LINK["name"][292]] ?></TD></TR><?php }?>
<?php if($LINK[290]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][290]?>" TITLE="<?=$lang[$LINK["desc"][290]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][290]."\"></i>&nbsp;".$lang[$LINK["name"][290]] ?></TD></TR><?php }?>
<?php if($LINK[293]){?><TR class="tabHpoolItemTR"><TD class="tabHpoolItemInactive" OnClick="<?=$LINK["link_url"][293]?>" TITLE="<?=$lang[$LINK["desc"][293]]?>"><?= "<i style=\"float:left;\" class=\"lmb-icon ".$LINK["icon_url"][293]."\"></i>&nbsp;".$lang[$LINK["name"][293]] ?></TD></TR><?php }?>
<TR><TD class="tabHpoolItemSpaceGtab"><div style="height:100%">&nbsp;</div></TD></TR>

</TABLE>

</TD><TD width="500px" class="tabHpoolfringe" style="border-left:none">

<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody">
    <TR class="tabHeader"><TD class="tabHeaderItem" colspan="23"><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></TD></TR>
<TR><TD colspan="23"><HR></TD></TR>


<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR><TD>ID</TD><TD><?=$ID?></TD></TR>
<?php if($ID == 1){?>
<TR><TD><?=$lang[561]?></TD><TD><?=$result_group["name"]?></TD></TR>
<TR><TD><?=$lang[126]?></TD><TD><?=$result_group["beschreibung"]?></TD></TR>
<?php }else{?>
<TR><TD><?=$lang[561]?></TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="groupname" VALUE="<?=$result_group["name"]?>"></TD></TR>
<TR><TD valign="top"><?=$lang[126]?></TD><TD><TEXTAREA STYLE="width:250px;height:100px;" NAME="groupdesc"><?=htmlentities($result_group["beschreibung"],ENT_QUOTES,$umgvar["charset"])?></TEXTAREA></TD></TR>
<?php }?>
<TR><TD><?=$lang[563]?></TD><TD><?=$result_group["erstdatum"]?></TD></TR>
<?php if($ID != 1 AND $session["superadmin"] == 1){?>
<TR><TD><?=$lang[900]?></TD><TD><SELECT ID="userdata_group_id" NAME="userdata[group_id]" STYLE="width:250px;" OnChange="document.form1.maingroup.value=this.value;">";
<option value="0">
<?php viewgrouptree($startgroup,"");?>
</SELECT>
</TR>
<?php }?>



<TR><TD><?=$lang[3]?></TD><TD><SELECT STYLE="width:250px" OnChange="document.form1.action.value='setup_user_change_admin';document.form1.ID.value=this.value;document.form1.submit();"><OPTION>
<?php
$sqlquery = "SELECT DISTINCT USER_ID,USERNAME FROM LMB_USERDB WHERE GROUP_ID = $ID AND DEL = ".LMB_DBDEF_FALSE;
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
        echo "<OPTION VALUE=\"".lmbdb_result($rs, "USER_ID")."\">".lmbdb_result($rs, "USERNAME");
}
?>
</SELECT></TD></TR>


<?php if($ID != 1){?>

<TR><TD><?=$lang[1532]?></TD><TD><INPUT TYPE="TEXT" VALUE="<?=htmlentities($result_group["redirect"],ENT_QUOTES,$umgvar["charset"])?>" NAME="redirect" STYLE="width:250px;"><BR><?= htmlentities("(eg. \"action=gtab_change&gtabid=1&formid=1\"",ENT_QUOTES,$umgvar["charset"]) ?>)</TD></TR>

<TD VALIGN="TOP"><?=$lang[1954]?></TD><TD><SELECT name="multiframe[]" size="3" multiple STYLE="width:250px">
<?php

if($result_group["mframelist"]){
	if($path = read_dir($umgvar["pfad"]."/extra/multiframe")){
		foreach($path["name"] as $key => $value){
			$value_ = explode(".",$value);
			if($path["typ"][$key] == "file" AND $value_[1] == "dao"){
				if(in_array($value,$result_group["mframelist"])){$SELECTED =  "SELECTED";}else {unset($SELECTED);}
				echo "<OPTION VALUE=\"".$value."\" $SELECTED>".$value;
			}
		}
	}
}

?>
</SELECT></TD></TR>

<?php /*?>
<TR><TD colspan=2><hr></TD></TR>
<TR><TD><?=$lang[905]?></TD><TD ALIGN="LEFT">&nbsp;<input type="text" value=" OK " OnClick="Javascript:gurefresh()" size="2" style="cursor:pointer; color:red;"></TD></TR>
<TR><TD><?=$lang[907]?></TD><TD ALIGN="LEFT">&nbsp;<input type="text" value=" OK " OnClick="Javascript:lrefresh()" size="2" style="cursor:pointer; color:red;"></TD></TR>
<?php */?>


<?php if($group_level){?>
<TR><TD colspan=2><hr></TD></TR>
<TR><TD><?=$lang[2585]?></TD>
<TD><select name="update_parent_group"><option value="0">
<?php
#viewgrouptree($startgroup,"",array($group_level));
viewgrouptree($startgroup,"");
?>
</select>
</TD></TR>


<TR><TD><?=$lang[2129]?></TD><TD ALIGN="LEFT">&nbsp;<INPUT TYPE="checkbox" NAME="update_parent_tabsettings" VALUE="1"></TD></TR>
<TR><TD><?=$lang[2130]?></TD><TD ALIGN="LEFT">&nbsp;<INPUT TYPE="checkbox" NAME="update_parent_filesettings" VALUE="1"></TD></TR>
<TR><TD><?=$lang[2131]?></TD><TD ALIGN="LEFT">&nbsp;<INPUT TYPE="checkbox" NAME="update_parent_menusettings" VALUE="1"></TD></TR>
<TR><TD><?=$lang[2306]?></TD><TD ALIGN="LEFT">&nbsp;<INPUT TYPE="checkbox" NAME="update_parent_formsettings" VALUE="1"></TD></TR>
<?php }?>

<TR><TD colspan=2><hr></TD></TR>

<TR><TD COLSPAN="2"><INPUT TYPE="button" OnClick="document.form1.change.value='1';document.form1.submit();" value="<?=$lang[522]?>" STYLE="cursor:pointer;width:100px;color:green;"></TR>
<TR><TD><INPUT TYPE="button" value="<?=$lang[160]?>" OnClick="del();" STYLE="cursor:pointer;width:100px;color:red">&nbsp;</TD></TR>
<?php }?>


<TR><TD COLSPAN="2"class="tabFooter">&nbsp;</TD></TR>

</TABLE>

</TD></TR></TABLE>
</FORM>
</DIV>

