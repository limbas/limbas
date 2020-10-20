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
 * ID: 210
 */

if(!$snap_view){$snap_view = 1;}

if($del AND $gtabid){
	$sqlquery = "DELETE FROM LMB_SNAP_SHARED where ID = $del";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	
	$sqlquery = "DELETE FROM LMB_SNAP WHERE ID = $del AND TABID = $gtabid";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}else{
		unset($gsnap[$gtabid]['id'][$del]);
		unset($gsnap[$gtabid]['name'][$del]);
		unset($gsnap[$gtabid]['filter'][$del]);
		if(count($gsnap[$gtabid]['id']) == 0){unset($gsnap[$gtabid]);}
	}
}

if($snap_edit AND $gtabid AND $snapid){
	if($snap_name = trim($snap_name)){
		$update[] = "NAME = '".parse_db_string(str_replace(";",",",$snap_name),30)."'";
		$gsnap[$gtabid]['name'][$snapid] = lmb_substr(str_replace(";",",",$snap_name),0,30);
	}
	if($snap_global){
		if($snap_global == 1){$v = LMB_DBDEF_TRUE;$vs = 1;}elseif($snap_global == 2){$v = LMB_DBDEF_FALSE;$vs = 0;}
		$update[] = "GLOBAL = $v";
		$gsnap[$gtabid]['glob'][$snapid] = $vs;
	}
	
	if($update){
		$update = implode(",",$update);
		$sqlquery = "UPDATE LMB_SNAP SET $update WHERE ID = $snapid AND TABID = $gtabid";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	}
}

if($snap_extension){
	$prepare_string = "UPDATE LMB_SNAP SET EXT = ? WHERE ID = ".$snap_extension;
	if(!lmb_PrepareSQL($prepare_string,array($snap_extensionValue),__FILE__,__LINE__)){$commit = 1;}
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
		parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
	}
	if(parent.parent.nav){
		parent.parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&refresh=no';
	}
}

function limbasSnapshotShare(el,snap_id,destUser,del,edit,drop){
	if(typeof(del) == "undefined"){del = 0;}
	if(typeof(edit) == "undefined"){edit = 0;}
	if(typeof(drop) == "undefined"){drop = 0;}

	ajaxGet('','main_dyns.php','showUserGroups&gtabid='+snap_id+'&usefunction=lmbSnapShareSelect&destUser='+destUser+'&del='+del+'&edit='+edit+'&drop='+drop,'', function(result) {
        $('#lmbAjaxContainer').html(result).show();
		if(el){
            limbasDivShow(el,null,'lmbAjaxContainer');
        }
	});

}
function lmbSnapShareSelect(ugval,snapname,gtabid){
	limbasSnapshotShare(null,gtabid,ugval);
}

function lmbSnapExtension(el,snapid){
	document.form1.snap_extension.value = snapid;
	limbasDivShow(el,'','snapExtension');
	document.form1.snap_extensionValue.value = document.form1.elements['snap_extvalue['+snapid+']'].value;
}
	
var activ_menu = null;
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

</SCRIPT>


<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;z-index:999;" onclick="activ_menu=1;"></div>



<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_snap">
<input type="hidden" name="gtabid">
<input type="hidden" name="snapid">
<input type="hidden" name="snap_id">
<input type="hidden" name="snap_name">
<input type="hidden" name="snap_global">
<input type="hidden" name="snap_edit">
<input type="hidden" name="snap_extension">
<input type="hidden" name="del">

<div id="snapExtension" class="lmbContextMenu" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1">
<?php pop_left();?>
<textarea id="snap_extensionValue" name="snap_extensionValue" onchange="document.form1.submit()" style="width:400px;height:250px;background-color:<?= $farbschema['WEB8'] ?>;"></textarea>
<?php pop_right();?>
<?php pop_bottom();?>
</div>


<div class="lmbPositionContainerMain">

<select name="snap_view" onchange="document.form1.submit()">
<option value="1" <?php if($snap_view == 1){echo "SELECTED";}?>><?=$lang[2784]?>
<option value="2" <?php if($snap_view == 2){echo "SELECTED";}?>><?=$lang[2785]?>
</select>

<br><br>

<table border="0" cellspacing="0" cellpadding="0" class="tabfringe"><tr><td valign="top">

<?php

$gsnap_ = SNAP_loadInSession(null,$snap_view);

echo "
<UL>
";

if($tabgroup["name"] AND $gtab["tab_id"]){
	foreach($tabgroup["name"] as $key => $value){
		$viewgroup = 0;
		foreach($gtab["tab_id"] as $key1 => $value1){
			if($gsnap_[$key1] AND $gtab["tab_group"][$key1] == $tabgroup["id"][$key]){
				if(array_sum($gsnap_[$key1]["user_id"])>0){
					if(!$viewgroup){echo "<LI>".$value."<UL>";$viewgroup=1;}
					echo "<LI>".$gtab["desc"][$key1]."<UL><TABLE>";
					foreach($gsnap_[$key1]["id"] as $key2 => $snid){
							if($gsnap_[$key1]["glob"][$key2]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
							echo "<TR>
							<TD><INPUT TYPE=\"TEXT\" VALUE=\"".$gsnap_[$key1]["name"][$key2]."\" OnChange=\"edit_snap('$key1','$key2',this.value,1)\" STYLE=\"border:none;width:150px;\"></TD>
							<TD style=\"padding-left:5px;\"><a href=\"main.php?action=gtab_erg&gtabid=$key1&snapid=$key2&snap_id=$key2\" target=\"_new\"><i class=\"lmb-icon lmb-list-ul-alt\" border=\"0\"></i></a></TD>
							<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-pencil\"  style=\"cursor:pointer\" border=\"0\" onclick=\"lmbSnapExtension(this,$key2)\"></i></TD>
							<input type=\"hidden\" name=\"snap_extvalue[$key2]\" value=\"".htmlentities($gsnap_[$key1]["ext"][$key2],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\">
							";
							
							if($LINK[225]){
								echo "<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-groups\" OnCLick=\"limbasSnapshotShare(this,$key2,'');\" STYLE=\"cursor:pointer;$st\"></i></TD>";
							}
							echo "<TD style=\"padding-left:5px;\"><i class=\"lmb-icon lmb-trash\" OnCLick=\"document.form1.del.value=$key2;document.form1.gtabid.value=$key1;document.form1.submit();\" STYLE=\"cursor:pointer;\"></i></TD>
							<TD><i>".$userdat["bezeichnung"][$gsnap_[$key1]["user_id"][$key2]]."</i></TD>
							</TR>";
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

</td></tr></table></div>
</FORM>