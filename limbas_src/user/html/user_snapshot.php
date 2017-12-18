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
 * ID:
 */


if($del AND $gtabid){
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED where ID = $del";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	
	$sqlquery = "DELETE FROM LMB_SNAP WHERE ID = $del AND USER_ID = ".$session["user_id"]." AND TABID = $gtabid";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}else{
		unset($gsnap[$gtabid]["id"][$del]);
		unset($gsnap[$gtabid][name][$del]);
		unset($gsnap[$gtabid][filter][$del]);
		if(count($gsnap[$gtabid]["id"]) == 0){unset($gsnap[$gtabid]);}
	}
}

if($snap_edit AND $gtabid AND $snapid){
	if($snap_name = trim($snap_name)){
		$update[] = "NAME = '".parse_db_string(str_replace(";",",",$snap_name),30)."'";
		$gsnap[$gtabid][name][$snapid] = substr(str_replace(";",",",$snap_name),0,30);
	}
	if($snap_global){
		if($snap_global == 1){$v = LMB_DBDEF_TRUE;$vs = 1;}elseif($snap_global == 2){$v = LMB_DBDEF_FALSE;$vs = 0;}
		$update[] = "GLOBAL = $v";
		$gsnap[$gtabid][glob][$snapid] = $vs;
	}
	
	if($update){
		$update = implode(",",$update);
		$sqlquery = "UPDATE LMB_SNAP SET $update WHERE ID = $snapid AND USER_ID = $session[user_id] AND TABID = $gtabid";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);	
	}
}
?>


<SCRIPT LANGUAGE="JavaScript">

function edit_snap(gtabid,snapid,val,typ) {
	document.form1.gtabid.value = gtabid;
	document.form1.snapid.value = snapid;
	if(typ == 1){
		document.form1.snap_name.value = val;
	}else if(typ == 2){
		if(val){val = 1;}else{val = 2;}
		document.form1.snap_global.value = val;
	}
	
	document.form1.snap_edit.value = 1;
	document.form1.submit();
}

function nav_refresh(gtabid,snapid,val) {
	if(parent.nav){
		parent.nav.document.location.href = 'main.php?<?=SID?>&action=nav&sparte=gtab&refresh=no';
	}
	if(parent.parent.nav){
		parent.parent.nav.document.location.href = 'main.php?<?=SID?>&action=nav&sparte=gtab&refresh=no';
	}
}

function viewSnap(tabid,snapid){
	document.form1.action.value = "gtab_erg";
	document.form1.snapid.value = snapid;
	document.form1.snap_id.value = snapid;
	document.form1.gtabid.value = tabid;
	document.form1.submit();
}

function limbasSnapshotShare(evt,snap_id,destUser,del,edit,drop){
	if(typeof(del) == "undefined"){del = 0;}
	if(typeof(edit) == "undefined"){edit = 0;}
	if(typeof(drop) == "undefined"){drop = 0;}
	browserType();

	ajaxGet(evt,'main_dyns.php','showUserGroups&gtabid='+snap_id+'&usefunction=lmbSnapShareSelect&destUser='+destUser+'&del='+del+'&edit='+edit+'&drop='+drop,'','ajaxContainerPost');
}
function lmbSnapShareSelect(ugval,snapname,gtabid){
	limbasSnapshotShare(null,gtabid,ugval);
}

var activ_menu = null;
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

</SCRIPT>

<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="user_snapshot">
<input type="hidden" name="gtabid">
<input type="hidden" name="snapid">
<input type="hidden" name="snap_id">
<input type="hidden" name="snap_name">
<input type="hidden" name="snap_global">
<input type="hidden" name="snap_edit">


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999;" onclick="activ_menu=1;"></div>

<div class="lmbPositionContainerMain">
<table class="tabfringe" border="0" cellspacing="0" cellpadding="0"><tr><td valign="top">


<?php

$sqlquery = "SELECT SNAPSHOT_ID FROM LMB_SNAP_SHARED,LMB_SNAP WHERE LMB_SNAP.ID = LMB_SNAP_SHARED.SNAPSHOT_ID AND LMB_SNAP.USER_ID = ".$session["user_id"];
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

while(odbc_fetch_row($rs)){
	$snid = odbc_result($rs,"SNAPSHOT_ID");
	$sharedsnap[$snid]++;
}
		
#echo "Name <input type=\"text\"> User <input type=\"text\"> public <input type=\"checkbox\"><hr>";
echo "
<UL>
";

if($tabgroup["name"] AND $gtab["tab_id"]){
        if(count($tabgroup["name"] == 0)) {
            echo "<div style=\"margin-top: 10px;margin-right: 35px;\"><b>".$lang[98]."</b></div>";
        }
	foreach($tabgroup["name"] as $key => $value){
		$viewgroup = 0;
		foreach($gtab["tab_id"] as $key1 => $value1){
			if($gsnap[$key1] AND $gtab["tab_group"][$key1] == $tabgroup["id"][$key]){
				if(array_sum($gsnap[$key1]["user_id"])>0){
					if(!$viewgroup){echo "<LI>".$value."<UL>";$viewgroup=1;}
					echo "<LI>".$gtab["desc"][$key1]."<UL><TABLE>";
					foreach($gsnap[$key1]["id"] as $key2 => $snid){
						if($gsnap[$key1]["user_id"][$key2]!=0){
							if($gsnap[$key1]["glob"][$key2]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
							
							echo "<TR>
							<TD><INPUT TYPE=\"TEXT\" VALUE=\"".$gsnap[$key1]["name"][$key2]."\" OnChange=\"edit_snap('$key1','$key2',this.value,1)\" STYLE=\"border:none;width:150px;\"></TD>
							<TD style=\"padding-left:5px;\"><A HREF=\"javascript:viewSnap(" . $key1 . "," . $key2 . ")\"><i class=\"lmb-icon lmb-list-ul-alt\"></i></TD>";
							
							if($LINK[225]){
								#echo "<TD title=\"public global\"><INPUT TYPE=\"CHECKBOX\" OnChange=\"edit_snap('$key1','$key2',this.checked,2)\" STYLE=\"border:1px solid black;\" $CHECKED></TD>";
								if(!$sharedsnap[$key2]){$st = "opacity:0.4;filter:Alpha(opacity=40);";}else{$st = "";}
								echo "<TD style=\"padding-left:5px;\" title=\"spezific public ($sharedsnap[$key2])\"><i class=\"lmb-icon lmb-group\" OnCLick=\"limbasSnapshotShare(event,$key2,'');\" STYLE=\"cursor:pointer;$st\"></i></TD>";
							}
							
							echo "<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-trash\" OnCLick=\"document.location.href='main.php?".SID."&action=user_snapshot&gtabid=$key1&del=$key2'\" STYLE=\"cursor:pointer;\"></i></TD>";
							echo "</TR>";
						}
					}
					echo "</TABLE></UL></LI>";
				}
			}
		}
		if($viewgroup){echo "</UL></LI>";}
	}
}
?>
</UL>

</div></tr></td></table>
</FORM>