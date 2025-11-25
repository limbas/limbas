<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if(isset($session["miniexplorer_path"]) AND $home_level){
	$LID = $session["miniexplorer_path"];
}elseif(!$LID AND $home_level){
	$LID = $home_level;
}elseif(!$LID){
	$LID = 0;
}

$gtabid = $gtab["argresult_id"]["LDMS_FILES"];

get_filestructure();
$level = $filestruct["level"][$LID];
if(!$typ){$typ = $filestruct["typ"][$LID];}
$file_path = array_reverse(lmb_getPathFromLevel($level,$LID));

/*
$filter["order"][$LID] = $ffilter["order"][$LID];
$filter["anzahl"][$LID] = $ffilter["anzahl"][$LID];
$filter["viewmode"][$LID] = 1;
$filter["page"][$LID] = $ffilter["page"][$LID];
$filter["gsr"] = $ffilter["gsr"];
$ffilter[$key1][$fid][0];
$filter["rowsize"][$LID] = null;
#$filter = $ffilter;
*/


$tmpgfile = $gfile['show'][$LID];
$gfile['show'][$LID] = array("{$gtabid}_11"=>1,"{$gtabid}_3"=>1,"{$gtabid}_14"=>1);
if(!$home_level){$home_level = 0;}

$_SESSION["session"]["miniexplorer_path"] = $LID;

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
						if(lmb_deleteFile($key)){lmb_EndTransaction(1);}else{lmb_EndTransaction(0);}
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
if($query = get_fwhere($LID,$ffilter,$typ)){
	$ffile = get_ffile($query,$ffilter,$LID,$typ);
}

?>

<script>
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

    let selectedFiles = [];
    const LID = <?=$LID?>;
    const fileList = LmEx_selectedFileList(LID);

    if(fileList){
		for (let i in fileList){
			if(fileList[i]){
                let cid = i.split('_');
                let row = $('#elline_'+cid[0]+'_'+LID+'_'+cid[1]);

                if(row) {
                    selectedFiles.push(
                        {
                            'id': cid[1],
                            'name': row.attr('data-name'),
                            'path': row.attr('title'),
                            'type': cid[0],
                            'desc': i
                        }
                    )
                }

			}
		}

        window.parent.LmEx_handle_miniexplorer(selectedFiles);

		self.focus();
	}else{
		alert(jsvar["lng_1717"]);
	}

}

$(function() {
    LmEx_createDropArea($('body'), function(files) {
        //console.log(files);
        LmEx_showUploadField('lmbUploadLayer', <?= $LID ?>, 1);
        LmEx_uploadFilesPrecheck(files, <?= $LID ?>, 1);
    });
});

</script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<nav class="navbar navbar-expand center-navigation bg-nav p-0 lmbGtabmenu lmbGtabmenu-list">
<ul class="navbar-nav ms-auto">
    <?php if($level OR $level == '0'){ ?>
    <li class="nav-item nav-link lmb-folder-up">
    <?php pop_picmenu(326,'', '','');?>
    </li>
    <?php } ?>

    <li class="nav-item nav-link lmbGtabmenuIcon-327">
    <?php pop_picmenu(327,'', '','');?>
    </li>

    <li class="nav-item nav-link lmbGtabmenuIcon-217">
    <?php pop_picmenu(217,'', '','');?>
    </li>

</ul>
</nav>


<div class="mt-3" id="createfolder" style="display:none;"><input type="text" name="addfolder" style="width:200px;">&nbsp;<input type="button" value="<?=$lang[571]?>" style="cursor:pointer;" Onclick="document.form1.add_folder.value=document.form1.addfolder.value;document.form1.submit();"></div>
<div class="mt-3" id="uploadfile"><div id="lmbUploadLayer" style="width:100%" colspan="12"></div></div>

<form action="main.php" method="post" name="form2" id="form2">
<input type="hidden" name="action" value="mini_explorer">
<input type="hidden" name="MID" value="<?=$MID;?>">
<input type="hidden" name="LID" value="<?=$LID;?>">
<input type="hidden" name="filter_reset" value="1">
</form>

<form enctype="multipart/form-data" action="main.php" method="post" name="form1">
<input type="hidden" name="action" value="mini_explorer">
<input type="hidden" name="LID" value="<?=$LID?>">
<input type="hidden" name="ID">
<input type="hidden" name="typ" value="<?=$typ?>">
<input type="hidden" name="ffilter_order">
<input type="hidden" name="edit_id">
<input type="hidden" name="rename">
<input type="hidden" name="add_folder">
<input type="hidden" name="del_file">
<input type="hidden" name="dublicate[type]">
<input type="hidden" name="dublicate[subj]">
<input type="hidden" name="rowsize">
<input type="hidden" name="level" value="<?=$level;?>">

<div id="fileresultlist">
<?php
if($file_path){
echo '<div class="container-fluid border p-0 mb-1 mt-3 bg-secondary-subtle">
            <i class="lmb-icon lmb-icon-8 lmb-folder"></i>
            <span class="w-100">/' . implode("/",$file_path) . '</span></div>';
}
?>
<div class='table-responsive'>
<table class="table w-100 table-striped-columns table-hover border" id="filetab">
<?php

explSearch($LID,$fid,$ffilter);
explHeader($LID,0,$ffilter);
explFolders($LID,$fid,0,$level,$ffilter);
explFiles($LID,$fid,$level,$ffile,$ffilter);
explFooter($LID,$fid,$ffile,$ffilter);

if($tmpgfile){$gfile['show'][$LID] = $tmpgfile;}
?>
</table></div></div></form>

<div class="d-flex justify-content-end">
    <button class="btn btn-sm btn-outline-secondary me-3" type="button" OnClick="postSelectedFiles();"><?=$lang[2226]?></button>
</div>
