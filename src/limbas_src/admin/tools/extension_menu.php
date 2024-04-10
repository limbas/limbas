<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



$openToPath=false;
$openToPath_Path="";
$file=null;


if($newrootfolder){
    $newrootfolder = preg_replace("/[^[:alnum:]_-äöüÄÖÜ]|[ ]/",'',$newrootfolder);
    $dir = EXTENSIONSPATH . parse_db_string($newrootfolder,25);
    mkdir($dir);
}

if($filename AND $todo){
	if(lmb_substr($filename,0,9) == 'EXTENSION' AND !lmb_strpos($filename,'..')){
		# new file or new folder
		if($todo == 'addfile' || $todo == 'addfolder'){
			$parts = explode("/",$filename);
			$name = array_pop($parts);
			$newFileFolder = implode("/",$parts);

			if(is_file($umgvar['pfad'] . "/" . $newFileFolder)){
				$parts = explode("/",$newFileFolder);
				array_pop($parts);
				$newFileFolder = implode("/",$parts);
			}
			$openToPath=true;
			$dir = $umgvar['pfad'] . "/" . $newFileFolder . "/" . $name;
			if($todo == 'addfile'){
				file_put_contents($dir,' ');
				$openToPath_Path=$newFileFolder;
			}else{
				mkdir($dir);
				$openToPath_Path = $newFileFolder . "/" . $name;
			}
		}

		$folder = null;
		if(is_file($filename)){
			$folder = explode('/',$filename);
			$file = array_pop ($folder);
			$folder = trim(implode('/',$folder),'/');
		}elseif (is_dir($filename)){
			$folder = trim($filename,'/');
		}

		if($folder){

			# delete
			if($todo == 'delete'){
				if($file){
					unlink($filename);
					$openToPath=true;
					$openToPath_Path=$folder;

				}else{
					rmdirr($umgvar["pfad"].'/'.$folder,1,0,1);
					$openToPath=true;
					$folders = explode("/",$folder);
					array_pop($folders);
					$openToPath_Path=implode("/",$folders);;
				}
			}

			# rename
			if($todo == 'rename'){
				if(!$newfilename){
                                    if($newfoldername) {
                                        $newfilename = $newfoldername;
                                    }else{
                                        exit;
                                    }
                                }
				if($file){
					chdir($folder);
					rename($file,$newfilename);

					$openToPath=true;
					$openToPath_Path=$folder."/".$newfilename;
				}else{
					$folderpath = explode('/',$filename);
					$foldername = array_pop($folderpath);
					$folderpath = trim(implode('/',$folderpath),'/');
					chdir($folderpath);
					rename($foldername,$newfilename);

					$openToPath=true;
					$openToPath_Path=$folderpath."/".$newfilename;
				}

			}

			# upload
			if($todo == 'upload'){
				$openToPath=true;
				$openToPath_Path=$folder;
			}
		}
	}
}
?>

<Script language="JavaScript">
//Initiate Drag and Drop for future divs
function initDraggable(id){
    $("#"+id).draggable({
        opacity:0.5,
        helper: "clone",     
        refreshPositions: true  //decreases performance
    });    
}

var timeoutId = setTimeout("",0);
function initDroppable(id){
    $("#"+id).droppable({
	addClasses: false,
	hoverClass: "lmbUploadDragenter",
	tolerance: "pointer",
	drop: function(event,ui){},
	over: function(event,ui){
	    timeoutId = window.setTimeout(function() {
		lmb_getTree($("#"+id).attr("actfile"), $("#"+id).attr("actid"), true);
	    }, 1200);
	    
	},
	out: function(event,ui){
	    clearTimeout(timeoutId);
	}
	
    });
    
    $("#"+id).on("drop", function(event,ui){
	event.preventDefault;
	moveFileDragAndDrop(ui.draggable.attr("path"),$(this).attr("fullpath"),$(this).attr("actid"));
	clearTimeout(timeoutId);
	ui.helper.hide();
    });
    
}

//Drag and Drop file upload from Desktop
var timeout = setTimeout("",0);

$(document).on('dragenter','tr[id^="tr_"]',function(e){
    e.stopPropagation();
    e.preventDefault();
    clearTimeout(timeout);
    $('tr[id^="tr_"]').removeClass("lmbUploadDragenter");
    $(this).addClass("lmbUploadDragenter");
    var elem = $(this);
    timeout = window.setTimeout(function() {
	lmb_getTree(elem.attr("actfile"),elem.attr("actid"),true);
    }, 1200);
    return false;
});

$(document).on('dragenter','.nodrag',function(e){
    e.stopPropagation();
    e.preventDefault();
    clearTimeout(timeout);
    $('tr[id^="tr_"]').removeClass("lmbUploadDragenter");
});

$(document).on('drop','tr[id^="tr_"]',function(e){
    $(this).removeClass("lmbUploadDragenter");
    e.preventDefault();
    if(e.originalEvent.dataTransfer != null){
	var files = e.originalEvent.dataTransfer.files;
	if(files.length > 0){
	    dragAndDropFileUpload(files,$(this).attr("actfile"),document.getElementById("progressDragAndDrop"));
	}
    }
    
});

$(document).on('dragover', function (e){
    e.stopPropagation();
    e.preventDefault();
});

$(document).on('drop', function (e){
    e.stopPropagation();
    e.preventDefault();
});

var numberFilesUploaded = 0;
function dragAndDropFileUpload(files,act_file,progressbar){
   numberFilesUploaded = 0;
   for (var i = 0; i < files.length; i++){
        SingleFileUpload(files[i],act_file,progressbar,files.length);   
   }   
}

function SingleFileUpload(temp_file,act_file,progressbar,numberFiles){
    actfile = act_file;
          
    if(progressbar == null || actfile == null || temp_file == null){
	return;
    }	  
    
    if(temp_file.size == 0){
	alert("Upload of folders not allowed!");
	return;
    }
    
    progressbar.hidden=false;
    progressbar.value = 0;
    progressbar.max = 100;
    
    
    var formData = new FormData();
    client = new XMLHttpRequest();
    
    formData.append("src", temp_file);
    formData.append("actid","extensionEditorUpload");
    formData.append("dest",actfile);
    formData.append("todo","uploadFileFromDesktop");
    
    client.onerror = function(e) {
        lmbShowErrorMsg("onError");
    };
 
    client.onload = function(e) {
        document.getElementById("percent").innerHTML = "100%";
        progressbar.value = progressbar.max;
    };
 
    client.upload.onprogress = function(e) {
        const p = Math.round((100 / e.total * e.loaded) / numberFiles * numberFilesUploaded);
        progressbar.value = p;
       	document.getElementById("percent").innerHTML = p + "%";
    };
 
    client.onabort = function(e) {
        lmbShowErrorMsg("Upload aborted!");
    };
    
    client.onreadystatechange = function(e) {
	if (this.readyState == 4 && this.status == 200) {
// 	    //Anzahl der hochgeladenen Dateien erhöhen
	    numberFilesUploaded++;
// 	    //Wenn alle Dateien hochgeladen, dann Seite neu laden
	    if(numberFilesUploaded == numberFiles){
		lmb_ExtSubmit("upload");
	    }
	    
	}
    };
    
    client.open("POST", "main_dyns_admin.php");
    client.send(formData);
}

//upload from desktop with input
var client = null;
function fileChange(){   
	//FileList Objekt aus dem Input Element mit der ID "fileA"
	var fileList = document.getElementById("fileA").files;
	
	//File Objekt nicht vorhanden = keine Datei ausgewählt oder vom Browser nicht unterstützt
	if(fileList.length > 1){
		document.getElementById("fileName").innerHTML = fileList.length + ' files';
		var size = 0;
		for(var i=0;i<fileList.length;i++){
			size = size + fileList[i].size;
		}		
		document.getElementById("fileSize").innerHTML = 'Size: ' + size + ' B';
		document.getElementById("fileType").innerHTML = '';		
	}else{
		document.getElementById("fileName").innerHTML = 'Name: ' + fileList[0].name;
		document.getElementById("fileSize").innerHTML = 'Size: ' + fileList[0].size + ' B';
		document.getElementById("fileType").innerHTML = 'Type: ' + fileList[0].type;		
	}
	
	document.getElementById("progress").value = 0;
	document.getElementById("progress").hidden=false;
	document.getElementById("percent").innerHTML = "0%";   
}

function uploadFileFromDesktop(){
    var input = document.getElementById("fileA").files;
    for(var i=0;i<input.length;i++){
	 SingleFileUpload(input[i],actfile,document.getElementById("progress"),input.length);
    }
}

function uploadAbort(){
    if(client instanceof XMLHttpRequest){
	    client.abort();
    }
}

//Drag and Drop move file
function moveFileDragAndDrop(src,dest,id){
    if(src == null || dest == null || id == null){
	    return;
    }
    
    var formData = new FormData();
    client = new XMLHttpRequest();
    
    formData.append("src", src);
    formData.append("actid","extensionEditorUpload");
    formData.append("dest",dest);
    formData.append("todo","moveFileDragAndDrop");
    
    client.onerror = function(e) {
        lmbShowErrorMsg("onError");
    };
        
    client.onabort = function(e) {
        lmbShowErrorMsg("Upload aborted!");
    };

    client.onreadystatechange = function(e) {
	if (this.readyState == 4 && this.status == 200) {
	    refreshTree();
	}
    };
    
    client.open("POST", "main_dyns_admin.php");
    client.send(formData);     
}


//Refresh tree
var openSubtrees = new Array();

function refreshTree(){
	refreshSubTree(document.getElementsByClassName("topelement")[0]);
}

function refreshSubTree(elem){
	openSubtrees = new Array();
	getSubTrees(elem);
	openSubtrees.sort();

	for(var i = 0;i<openSubtrees.length;i++){
		lmb_getTree(openSubtrees[i].getAttribute("actfile"),openSubtrees[i].getAttribute("actid"),true);
	}
}

function getSubTrees(elem){
	if(elem.getAttribute("type") == "folder" && elem.getAttribute("open") == "true"){
		openSubtrees.push(elem);		
	}
	for(var i = 0; i < elem.children.length;i++){
		getSubTrees(elem.children[i]);
	}
}


//Open foldertree
function lmb_getTree(path,id,open){ //Öffnet den Angegebenen Pfad und lädt die Ordner und Dateien in das <tr> mit der gegebenen id
	$.ajax({
		type: "GET",
		url: "main_dyns_admin.php",
		async: false,
		data:{'actid':"extensionEditor",'path':path,'open':open},
		success: function(data){
			document.getElementById(id).innerHTML = data;
									
			var list1 = document.getElementById("ul1_"+id);
			for(var i = 0; i<list1.children.length;i++){
				initDroppable("tr_" + list1.children[i].innerHTML);
			}
			list1.parentElement.removeChild(list1);
			
			var list2 = document.getElementById("ul2_"+id);
			for(var i = 0; i<list2.children.length;i++){
				initDraggable(list2.children[i].innerHTML);
			}
			list2.parentElement.removeChild(list2);
									
			if(open==true){			
				document.getElementById("tr_"+id).setAttribute("open","true");
				document.getElementById(id+"_imgopen").onclick = function(){lmb_getTree(path,id,false)};
				document.getElementById(id+"_boxopen").onclick = function(){lmb_getTree(path,id,false)};
				document.getElementById(id+"_imgopen").src = document.getElementById(id+"_imgopen").src.replace("plus","minus");
                                $('#'+id+'_boxopen').removeClass('lmb-folder-closed');
                                $('#'+id+'_boxopen').addClass('lmb-folder-open');
			}else if(open==false){
				document.getElementById("tr_"+id).setAttribute("open","false");
				document.getElementById(id+"_imgopen").onclick = function(){lmb_getTree(path,id,true)};
				document.getElementById(id+"_boxopen").onclick = function(){lmb_getTree(path,id,true)};
				document.getElementById(id+"_imgopen").src = document.getElementById(id+"_imgopen").src.replace("minus","plus");
                                $('#'+id+'_boxopen').removeClass('lmb-folder-open');
                                $('#'+id+'_boxopen').addClass('lmb-folder-closed');
				//Inhalt leeren
				document.getElementById(id).innerHTML = "";
			}	
		}
	});
}


var actfile = null;
function lmb_ExtSubmit(todo) {
    	//Statt lmb_ExtSubmit("upload") kann auch einfach refreshTree() aufgerufen werden. Problem ist nur, dass das Contextmenu nicht geschlossen wird!
	if(todo == 'edit'){
		newexplorer=open("main_admin.php?action=setup_exteditor&fpath=" + encodeURIComponent('/' + actfile) ,"File_Edit","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
	}else if(todo == 'download'){
		newexplorer=open(actfile ,"File_Download","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=600");
	}else{
		if(todo == 'delete'){
                    if(confirm("Do you really want to delete '" + actfile + "'?") !== true) {
                        return;
                    }
                }else if(todo == 'addfile'){
			var name = prompt("Name","newfile.php");
			if(!name){return;}
		}else if(todo == 'addfolder'){
			var name = prompt("Name","newfolder");
			if(!name){return;}			
		}

                document.form1.filename.value = actfile + ((name!=null)?("/" + name):"");
		document.form1.todo.value = todo;
                document.form1.newfoldername.value = (document.form1.newfoldername.value=="") ? $('#newfilename').val() : document.form1.newfoldername.value;
		document.form1.submit();
	}
}

var activ_menu = null;
function divclose() {
	if(!activ_menu){
            document.getElementById("limbasDivMenuContext").style.visibility='hidden';
            document.getElementById("limbasDivMenuContextFolder").style.visibility='hidden';
	}
	activ_menu = 0;
}

function body_click(){
	window.setTimeout("divclose()", 50);
}

function lmb_ExtOpenContext(el,path,filename){
	actfile = path;
        
        $('#newfilename').val(filename);
	
	limbasDivShow(el,'','limbasDivMenuContext');
}

function lmb_ExtOpenContextFolder(el,path,filename){
	actfile = path;

        $('#newfoldername').val(filename);

        //Upload-Steuerelemente zurücksetzen
        $('#fileA').val('');
        $('#fileName').html('');
        $('#fileSize').html('');
        $('#fileType').html('');
        $('#progress').hide();
        $('#percent').html('');        
	limbasDivShow(el,'','limbasDivMenuContextFolder');
}

</Script>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_exteditor">
<input type="hidden" name="todo">
<input type="hidden" name="filename">

    <?php //TODO: context menu nicht richtig positioniert ?>
<div id="limbasDivMenuContextFolder" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:991;" onclick="activ_menu = 1;">
    <?php
    #----------- context menu folder -----------
    pop_top('limbasDivMenuContextFolder');
    pop_menu(0,"lmb_ExtSubmit('addfile');",$lang[2758],null,null,"lmb-icon-cus lmb-page-new");
    pop_menu(0,"lmb_ExtSubmit('addfolder');",$lang[2297],null,null,"lmb-icon-cus lmb-folder-add");
    pop_line();
    ?>
    
    <div class="row">
        <label for="inputEmail3" class="col-sm-2 col-form-label">Name</label>
        <div class="col-sm-10 pt-1">
            <input type="text" class="form-control form-control-sm" name="newfoldername" id="newfoldername" value="" onchange="lmb_ExtSubmit('rename')">
        </div>
    </div>
    <?php
    
    pop_menu(0,"lmb_ExtSubmit('delete');",$lang[160],null,null,"lmb-trash");
    pop_line();
    pop_left();
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input name="file" type="file" id="fileA" onchange="fileChange();" multiple />
        <div class="d-flex justify-content-between mt-2 mb-0">
            <button class="btn btn-secondary btn-sm" name="abort" type="button" onclick="uploadAbort();">Abbrechen</button>
            <button class="btn btn-primary btn-sm" name="upload" type="button" onclick="uploadFileFromDesktop();">Upload</button>
        </div>
    </form>
    <div>
        <div id="fileName"></div>
        <div id="fileSize"></div>
        <div id="fileType"></div>
        <progress id="progress" hidden=true style="margin-top:10px"></progress>
        <span id="percent"></span>
    </div>
    <?php
    pop_right();
    pop_bottom();
    ?>
</div>

<div id="limbasDivMenuContext" class="lmbContextMenu" style="position:absolute;visibility:hidden;z-index:991;" onclick="activ_menu = 1;">
    <?php
    #------------ context menu file -----------
    pop_top('limbasDivMenuContext');
    pop_menu(0,"lmb_ExtSubmit('download')",$lang[1612],null,null,"lmb-download");
    pop_menu(0,"lmb_ExtSubmit('edit');",$lang[843],null,null,"lmb-page-edit");
    pop_line();
    pop_input2("lmb_ExtSubmit('rename')",'newfilename','',0,'Name', 20);
    pop_menu(0,"lmb_ExtSubmit('delete');",$lang[160],null,null,"lmb-trash");
    pop_bottom();
    ?>
</div>

    <div class="container-fluid p-3">
        <div class="card">
            <div class="card-body">
                
            
<table border="0" cellspacing="0" cellpadding="1" class="tabfringe"><tr class="nodrag"><td>

<table border="0" cellspacing="0" cellpadding="0">
<style>td{padding:0px;}</style>


<?php
//show folder structure using extension_ajax.php
echo "<div class=\"topelement\" id=\"" . md5(EXTENSIONSPATH) . "\"></div>";
echo "<script type=\"text/javascript\">lmb_getTree(\"" . EXTENSIONSPATH . "\",\"" . md5(EXTENSIONSPATH) . "\",null);</script>";


//open given path in folderstructure
if($openToPath==true){
      //create part-paths and execute javascript to open part-paths
      $pathParts=explode("/",str_replace("EXTENSIONS/","",$openToPath_Path));
      $zaehler=0;
      foreach($pathParts as $Part){
	    $tempPath="/EXTENSIONS";
	    for($i=0;$i<=$zaehler;$i++) {
		  $tempPath=$tempPath."/".$pathParts[$i];
	    }
	    $zaehler++;
	    echo "<script type='text/javascript'>lmb_getTree(\"".$umgvar["pfad"].$tempPath."/\",'" . md5($umgvar["pfad"].$tempPath."/") . "',true);</script>";
      }
}

?>

<progress id="progressDragAndDrop" hidden=true style="margin-top:10px"></progress>

            </div>
        </div>
        <hr>

        <div class="d-flex">
            <input type="text" id="newrootfolder" class="form-control form-control-sm">&nbsp;
            <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick='document.location.href="main_admin.php?action=setup_exteditor&newrootfolder="+document.getElementById("newrootfolder").value'>root Ordner anlegen</button>
        </div>
        
</div>
</FORM>

