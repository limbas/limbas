<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 215
 */

if(isset($session["miniexplorer_path"]) AND $home_level){
	$LID = $session["miniexplorer_path"];
}elseif(!$LID AND $home_level){
	$LID = $home_level;
}elseif(!$LID){
	$LID = 0;
}

get_filestructure();
$level = $filestruct["level"][$LID];
if(!$typ){$typ = $filestruct["typ"][$LID];}
$file_path = array_reverse(set_path($level,$LID));


if($show_details){
	$filter = get_userShow($LID,1);
}else{
	$gfile['show'][$LID] = null;
	$filter = get_userShow($LID,0);
}

if($ffilter_order){
	if($ffilter["order"][$LID][0] == $ffilter_order AND !$ffilter["order"][$LID][1]){
		$ffilter["order"][$LID][1] = 'DESC';
	}else{
		$ffilter["order"][$LID][1] = null;
	}
	$ffilter["order"][$LID][0] = $ffilter_order;
}
$filter["order"][$LID] = $ffilter["order"][$LID];

$filter["nolimit"][$LID] = 1;
$filter["page"][$LID] = 1;
if(!$home_level){$home_level = 0;}

$_SESSION["session"]["miniexplorer_path"] = $LID;

# ----- Usereinstellungen auslesen und zwischenspeichern ------
$tmpgfile = $gfile['show'][$LID];

$filter["rowsize"][$LID] = null;

# ---------------- Ordner hinzufügen -----------------------
if(($add_folder AND $LINK[119]) AND ($LID OR ($LID == '0' AND $session["user_id"] == 1))){
	add_file($LID,$add_folder);
	get_filestructure();
}

# ---------- upload deprecatet ---------------------
#if(is_numeric($LID) AND $LINK[128] AND $_FILES){
#	require_once('extra/explorer/explorer_upload.php');
#}

/* --- Actionliste --------------------------------------------- */
$files = $activelist["d"][$LID];
if($files){
	if(in_array("1",$files)){
		foreach($files as $key => $value){
			if($value){
				lmb_StartTransaction();
				if($value == 1){
					if($key == $edit_id){unset($edit_id);}
					# --- Datei löschen ----
					if($del_file AND $LINK[171] AND $filestruct["del"][$LID]){
						if(del_file($key)){lmb_EndTransaction(1);}else{lmb_EndTransaction(0);}
					}
				}
			}
		}
	}
}

$files = $activelist["f"][$LID];
if($files){
	if(in_array("1",$files)){
		foreach($files as $key => $value){
			if($value){
				lmb_StartTransaction();
				if($value == 1){
					# --- Ordner löschen ----
					if($del_file AND $LINK[171] AND $filestruct["del"][$key]){
						if(delete_dir($key)){lmb_EndTransaction(1);}else{lmb_EndTransaction(0);}
					}
				}
			}
		}
	}
}


# --- Abfrage starten ---
if($query = get_fwhere($LID,$filter,$typ)){
	$ffile = get_ffile($query,$filter,$LID,$typ);
}

# Funktionparameter auslesen
if($funcname){
	if($funcpara){
		$postaction = $funcname."($funcpara,";
	}else{
		$postaction = $funcname."(";
	}
}

if($postaction AND $home_level){
	$home = $home_level;
}else{
	$home = 0;
}
?>

<script language="JavaScript">
// ----- Js-Script-Variablen --------
jsvar["ID"] = "<?=$ID?>";
jsvar["LID"] = "<?=$LID?>";
jsvar["typ"] = "<?=$typ?>";
jsvar["action"] = "<?=$action?>";
jsvar["level"] = "<?=$level?>";
jsvar["copycache"] = "<?=$umgvar["copycache"]?>";
jsvar["res_viewcount"] = "<?=$ffile["res_viewcount"]?>";
jsvar["resultspace"] = "<?=$umgvar["resultspace"]?>";
jsvar["message1"] = "<?=$lang[1696]?>";




function postSelectedFiles(){

	<?php if($postaction){?>

	var selectedfiles = new Array();
	var filelist = LmEx_selectedFileList(<?=$LID?>);

	if(filelist){
		for (var i in filelist){
			if(filelist[i]){
				selectedfiles.push(i);
			}
		}

		var sfiles = selectedfiles.join("-");
		opener.<?=$postaction?>sfiles,null,null,null,null,null,null,null,null,null,null,null);
		self.focus();
	}else{
		alert(jsvar["lng_1717"]);
	}

	<?php }?>

}

$(function() {
    LmEx_createDropArea($('body'), function(files) {
        console.log(files);
        LmEx_showUploadField();
        LmEx_uploadFilesPrecheck(files, <?= $LID ?>, 1);
    });
});


<?php
if($uploadlink AND $upload_file AND $ufileId){
?>
opener.<?=$postaction.$ufileId?>);
<?php
}
?>
</script>


<form enctype="multipart/form-data" action="main.php" method="post" name="form1">

<DIV ID="dublicateCheckLayer" style="position:absolute;top:25%;left:25%;display:none;z-index:5"></DIV>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>


<div style="border: 1px solid black;margin:5px;background-color:<?=$farbschema["WEB8"]?>">

<table><tr>
<td nowrap style="width:60px;"><?=$lang[2229]?>:&nbsp;</td><td>
<select style="width:150px;height:15px;" OnChange="document.form1.LID.value=this.value;document.form1.submit();">
<?php
$bzm = 1;
foreach($file_path as $key => $value){
	if(count($file_path) == $bzm){$sel = "SELECTED";}else{$sel = "";}
	echo "<option value=\"".$key."\" $sel>".$sp.$value;
	$sp .= "&nbsp;";
	$bzm++;
}

?>
</td><td>&nbsp;</td>
<td><i class="lmb-icon-cus lmb-folder-up" style="cursor:pointer;" onclick="document.form1.LID.value='<?=$filestruct['level'][$LID];?>';document.form1.submit();" title="<?=$lang[2225]?>"></i></td>
<td><i class="lmb-icon lmb-home" style="cursor:pointer;" onclick="document.form1.LID.value='<?=$home?>';document.form1.submit();" title="<?=$lang[2230]?>"></i></td>
    <?php if($LINK[119] AND $LID){?><td>&nbsp;</td><td><i class="lmb-icon-cus lmb-folder-add" style="cursor:pointer;" onclick="document.getElementById('uploadfile').style.display='none';document.getElementById('createfolder').style.display='';" title="<?=$lang[2231]?>"></i></td><?php }?>
    <?php if($LINK[128] AND $LID){?><td><i class="lmb-icon lmb-file-upload" style="cursor:pointer;" onclick="document.getElementById('createfolder').style.display='none';document.getElementById('uploadfile').style.display='';document.getElementById('lmbUploadLayer').innerHTML='';LmEx_showUploadField();" title="<?=$lang[2232]?>"></i></td><?php }?>
        <?php if($LINK[171] AND $LID){?><td><i class="lmb-icon lmb-page-delete-alt" style="cursor:pointer;" onclick="LmEx_delfile();" title="<?=$lang[2318]?>"></i></td><?php }?>

<td>&nbsp;</td>
<?php if($show_details){$style1="opacity:0.3;filter:Alpha(opacity=30)";}else{$style="opacity:0.3;filter:Alpha(opacity=30)";}?>
<td><i class="lmb-icon lmb-align-justify" style="cursor:pointer;<?=$style?>" onclick="document.form1.show_details.value='0';document.form1.submit();" title="<?=$lang[2233]?>"></i></td>
<td><i class="lmb-icon-cus lmb-txt-col" style="cursor:pointer;<?=$style1?>" onclick="document.form1.show_details.value='1';document.form1.submit();" title="<?=$lang[2234]?>"></i></td>
</tr>
<tr id="createfolder" style="display:none;"><td colspan="12"><input type="text" name="addfolder" style="width:200px;">&nbsp;<input type="button" value="<?=$lang[571]?>" style="cursor:pointer;" Onclick="document.form1.add_folder.value=document.form1.addfolder.value;document.form1.submit();"></td></tr>
<tr id="uploadfile"><td style="padding-left:3px;" id="lmbUploadLayer" style="width:100%" colspan="12"></td></tr>
</table>

<input type="hidden" name="action" value="mini_explorer">
<input type="hidden" name="LID" value="<?=$LID?>">
<input type="hidden" name="ID">
<input type="hidden" name="show_details" value="<?=$show_details?>">
<input type="hidden" name="funcname" value="<?=$funcname?>">
<input type="hidden" name="funcpara" value="<?=$funcpara?>">
<input type="hidden" name="typ" value="<?=$typ?>">
<input type="hidden" name="ffilter_order">
<input type="hidden" name="edit_id">
<input type="hidden" name="rename">
<input type="hidden" name="add_folder">
<input type="hidden" name="del_file">
<input type="hidden" name="dublicate[type]">
<input type="hidden" name="dublicate[subj]">
<input type="hidden" name="rowsize">

<div id="fileresultlist" style="border: 1px solid black;margin:6px;height:200px;background-color:<?=$farbschema["WEB8"]?>;overflow:scroll;">
<table id="filetab" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse">
<?php

if($file_path){echo "<TR><TD colspan=\"10\"><DIV style=\"background-color:".$farbschema["WEB11"]."\">&nbsp;<i class=\"lmb-icon lmb-icon-8 lmb-folder\"></i>&nbsp;<INPUT TYPE=\"TEXT\" STYLE=\"border:none;width:94%;height:17px;background-color:".$farbschema["WEB11"].";color:color:".$farbschema["WEB8"].".;z-index:1;\" VALUE=\"/".implode("/",$file_path)."\" READONLY></DIV></TD></TR>";}

explHeader($LID,0,$filter);
explFolders($LID,$fid,0,$level,$filter);
explFiles($LID,$fid,$level,$ffile,$filter);

if($tmpgfile){$gfile['show'][$LID] = $tmpgfile;}
?>
</table></div></form>

<table><tr>
<td><input type="button" value="<?=$lang[2226]?>" OnClick="postSelectedFiles();"></td>
<td><input type="button" value="<?=$lang[2227]?>" OnClick="window.close();"></td>
</tr></table>


</div>

