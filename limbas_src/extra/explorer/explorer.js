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

// ------------- AJAX REQUESTS ----------------------------------------------------------------

//----------------- get result count of rows -------------------
function LmEx_showResultlimit() {
	var getstring = "&ID="+document.form2.ID.value+"&LID="+document.form2.LID.value+"&MID="+document.form2.MID.value+"&typ="+document.form2.typ.value;
	ajaxGet(null,'main_dyns.php','getFileRowCount' + getstring,null,'LmEx_resultlimitShow');
}

//----------------- show result count of rows -------------------
function LmEx_resultlimitShow(result) {
	alert(jsvar["lng_93"]+' '+result+' | '+jsvar["lng_2114"]+' '+jsvar["resultspace"]+'\n'+jsvar["lng_1868"]+jsvar["lng_1913"]);
}

//----------------- mark copy/paste symbols -------------------
function LmEx_ajaxCopyPasteEvent(typ) {
	var getstring = "&typ="+typ;
	ajaxGet(null,'main_dyns.php','saveCopyPasteEvent' + getstring,null,'LmEx_ajaxResultCopyPasteEvent');
}

//----------------- mark copy/paste symbols -------------------
function LmEx_ajaxResultCopyPasteEvent(result) {
	if(browser_ns5){
		document.getElementById("conextIcon_191").style.opacity = '1.0';
	}else{
		document.getElementById("conextIcon_191").style.filter = 'Alpha(opacity=100)';
	}
}






// show extendet parameter in parameter formular
function updateDublicateUploads(num,type,c){

	var chk = document.getElementsByName("fileVersionType_"+c)[type].checked;
	for (var i = c; i <= num; i++){
		if(document.getElementById("fileVersionId_"+i)){
			if(chk == true){
				var el = document.getElementsByName("fileVersionType_"+i)[type].checked = true;
			}else{
				var el = document.getElementsByName("fileVersionType_"+i)[type].checked = false;
			}

			// display versioning text
			if(type == 3){
				document.getElementById("versioning_subj_"+i).style.display='';
			}else{
				document.getElementById("versioning_subj_"+i).style.display='none';
			}
		}
	}
}


// handle & submit from parameter formular of dublicate uploads
function selectDublicateUploads(evt,num,fp){

	var fvt = new Array();
	var subj = new Array();
	var formel = document.form1;
	
	for (var i = 1; i <= num; i++){
		if(document.getElementById("fileVersionId_"+i)){
			var el = document.getElementsByName("fileVersionType_"+i);
			var fvtValue = radioValue(el);
			var el = document.getElementsByName("fileVersionSubj_"+i);
			var sbjValue = el[0].value;

			fvt.push(fvtValue);
			subj.push(sbjValue);
		}else{
			fvt.push('');
			subj.push('');
		}
		
		// upload
		if(formel.elements["dublicate[type]["+i+"]"]){
			formel.elements["dublicate[type]["+i+"]"].value = fvtValue;
			formel.elements["dublicate[subj]["+i+"]"].value = sbjValue;
			var upl = 1;
		}
		
	}
	
	// copy move
	if(!upl){
		if(!formel.elements["dublicate[type][0]"]){
			$(formel).append("<input type='hidden' name='dublicate[type][0]'>");
			$(formel).append("<input type='hidden' name='dublicate[subj][0]'>");
		}
		formel.elements["dublicate[type][0]"].value = fvt.join(";");
		formel.elements["dublicate[subj][0]"].value = subj.join(";");
	}

	if(num > 0){
		document.getElementById('limbasDivMenuVersioning').style.visibility='hidden';

		$('.lmbUploadProgress').show(); //show all progressbars

		if(LmEx_pasteTyp == 'copy'){
			LmEx_copy_file(LmEx_pasteFilelist);
		}else if(LmEx_pasteTyp == 'move'){
			LmEx_move_file(LmEx_pasteFilelist);
		}else if(isDragDrop){
			LmEx_dragFileUploadPost(null,fp,isDragDrop['files'],isDragDrop['LID'],isDragDrop['ID'],isDragDrop['gtabid'],isDragDrop['fieldid']);
		}else{
			LmEx_ajaxUploadCheckPost(null,fp);
		}
	}
}

// add file upload forms to formular
function LmEx_multiupload(co,dest,LID,fp,gtabid,fieldid,ID){
	
	if(!fp){fp = 1;}
	if(!dest){dest = 'lmbUploadLayer';}
	if(!LID){LID = jsvar["LID"];}
	if(!gtabid){gtabid = '';}
	if(!fieldid){fieldid = '';}
	if(!ID){ID = '';}
	var upllayer = document.getElementById(dest);

	var cont = '<div class="gtabHeaderInputTR lmbUploadDiv" id="lmbUploadDiv_'+fp+'">\
	<input type="hidden" name="f_LID" value="'+LID+'">\
	<input type="hidden" name="f_datid" value="'+ID+'">\
	<input type="hidden" name="f_gtabid" value="'+gtabid+'">\
	<input type="hidden" name="f_fieldid" value="'+fieldid+'">\
	<table>';

	for (var i = 1; i <= co; i++) {
                // ?????
		if(!document.getElementById("lmbUploadLayer_'+i+'")){
			cont += '<tr><td nowrap id="lmbUploadLayer_'+fp+'_'+i+'">\
			<input type="file" multiple id="file['+fp+'_'+i+']" name="file['+i+']" size="20" onchange="LmEx_UploadArchivOption(this.value,\''+fp+'_'+i+'\')">\
			<input type="hidden" name="dublicate[type]['+i+']">\
			<input type="hidden" name="dublicate[subj]['+i+']">\
			</td><td nowrap><div id="lmbUploadLayerSubmit_'+fp+'_'+i+'"><span id="lmbUploadStateUnzip_'+fp+'_'+i+'" style="display:none;padding-left:5px;padding-right:5px;vertical-align:text-top">'+jsvar['lng_1560']+':<input style="vertical-align:bottom" type="checkbox" id="file_archiv['+fp+'_'+i+']" name="file_archiv['+i+']"></span>';
			if(i == 1){cont += '<input type="button" value="'+jsvar['lng_815']+'" style="vertical-align:middle" onclick="activ_menu=1;LmEx_CheckIfMultiUpload(\''+fp+'\',\''+LID+'\',\''+ID+'\',\''+dest+'\',\''+gtabid+'\',\''+fieldid+'\');">';}
			cont += '</div></td><td width="100%"><div id="lmbUploadState_'+fp+'_'+i+'" class="lmbUploadProgress">\
			<div id="lmbUploadState_'+fp+'_'+i+'Bar" class="lmbUploadProgressBar"></div></div>\
			</td></tr>\
			\n';
                }
	}

	cont += '</table></div>\n';
	
	// first drop all upload forms
	$(".lmbUploadDiv").remove();
	
	upllayer.innerHTML = cont;
	upllayer.parentNode.style.display='';	
	
	
	
}

// html5 multi-file upload simulated with existing multi-file upload function for drag&drop
function LmEx_CheckIfMultiUpload( fp, LID, ID, upllayer, gtabid,fieldid){
        var fileInput = $('#lmbUploadDiv_'+fp+' input[type=file]').first().get(0);

        if(fileInput.files.length > 1) {
            // 5th argument: 'LmEx_Ex_uploadfile_' + fp
            LmEx_dragFileUpload(fileInput.files, fp, LID, ID, upllayer, gtabid, fieldid);
            
        } else {
            LmEx_ajaxUploadCheck(fileInput, LID, fp);
        }
}

// display unzip option for upload
function LmEx_UploadArchivOption(name,dest){
	var npart  = name.split('.');
	var len = npart.length;
	var ext = npart[len-1];
	if(ext == "gz" || ext == "zip" || ext == "tar" || ext == "rar" || ext == "gzip" || ext == "tgz"){
		document.getElementById("lmbUploadStateUnzip_"+dest).style.display = '';
	}
}


// precheck upload if file already exists
var upl_file = new Array();
var upl_size = new Array();
function LmEx_ajaxUploadCheck(filename,LID,fp) {

	if(!filename){filename = "file";}
	fnlen = (filename.length)+3;

	if(!LID && document.form1.LID){var LID = document.form1.LID.value;}
	if(document.form1.ID){var ID = document.form1.ID.value;}
	
	upl_file = new Array();
	upl_size = new Array();
        
	var t = $('#lmbUploadDiv_'+fp+' input[type=file]').each(
	function( index, obj ) {
                
		if(navigator.hasxmlProgress){
			var filename = obj.files[0].name;
			var filesize = obj.files[0].size
		}else{
			var filename = $(obj).val();
			var filesize = 0;
		}

		// get name from path
		var filename = $(obj).val();
		var path = filename.replace(/\\/g,"/");
		var pathpart = path.split("/");
		pathpart.reverse();

                // fill array
		upl_file.push(escape(pathpart[0]));
		upl_size.push(escape(filesize));
        
		ind = obj.id.substring((obj.name.lastIndexOf('[')+1),(obj.id.length-1));
		spart = obj.id.substring(0,obj.id.lastIndexOf('[')).split('_');
                if(!isNaN(spart[spart.length-1]) && !isNaN(spart[spart.length-2])){
                        ind = spart[spart.length-2]+'_'+spart[spart.length-1]+'_'+ind;
                }

                document.getElementById('lmbUploadState_'+ind).style.display = 'inline'; //show progressbar
                document.getElementById('lmbUploadLayerSubmit_'+ind).style.display = 'none'; // hide submit-button
	}
	);
	
	if(upl_file){
		file = upl_file.join(";");
		size = upl_size.join(";");
		var getstring = "fileUploadCheck&level="+LID+"&name="+file+"&size="+size+"&ID="+ID+"&fp="+fp;
		mainfunc = function(result){LmEx_ajaxUploadCheckPost(result,fp);};
		ajaxGet(null,'main_dyns.php',getstring,null,'mainfunc',null,null,1);
	}
}


// show upload pre-check result
var lmb_uploadCount=0;
function LmEx_ajaxUploadCheckPost(result,fp) {
	
	if(result){ajaxEvalScript(result)};
	
	var formel = document.form1;
	
	// file has dublicates - show parameters
	if(result){
		$('.lmbUploadProgress').hide(); //hide all progressbars
		document.getElementById("dublicateCheckLayer").style.visibility='visible'; // show parameter formular
		document.getElementById("dublicateCheckLayer").innerHTML = result;
	
	// direct files upload
	}else{
		// upload has progressbar
		if(navigator.hasxmlProgress){
			// paste each file element into single form
			var i = 1;
                        
			$('#lmbUploadDiv_'+fp+' input[type=file]').each(
				function( index, obj ) {
                                        var formData = new FormData();
					formData.append('file['+i+']', obj.files[0]);
                                        formData.append('LID', formel.f_LID.value);
                                        formData.append('gtabid', formel.f_gtabid.value);
                                        formData.append('fieldid', formel.f_fieldid.value);
                                        formData.append('ID', formel.f_datid.value);
                                        formData.append('dublicate[subj][1]', formel.elements['dublicate[subj]['+i+']'].value);
                                        formData.append('dublicate[type][1]', formel.elements['dublicate[type]['+i+']'].value);
                                        formData.append('file_archiv['+i+']', formel.elements['file_archiv['+i+']'].checked);
                                        var status = new lmb_createStatusbar('lmbUploadState_'+fp+'_'+(index+1));
                                        status.filesize=lmb_fileSize(obj.files[0].size,1);
                                        lmb_uploadCount++;
					i++;
                                        lmb_fileUpload(formData,status,formel);
                                }                        
			);
			return;
			
		// simple form-upload
		}else{
			//limbasWaitsymbol(evt);
			formel.submit();
		}
	}
}


// ajax based fileupload
function lmb_fileUpload(formData,status,formel)
{
	var uploadURL ="main_dyns.php?actid=fileUpload"; //Upload URL
	var extraData ={}; //Extra Data.
    var jqXHR=$.ajax({
            xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                        }
                        //Set progress
                        status.setProgress(percent);
                    }, false);
                }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType:false,
        processData: false,
        cache: false,
        data: formData,
        success: function(data){
        	lmb_uploadCount--;
            status.setProgress(100);
            ajaxEvalScript(data);
            if(!lmb_uploadCount){
            	lmb_refreshForm(formel);
            }
        }
    });

}


// show statusbar
function lmb_createStatusbar(fid)
{
	
	//console.log('#'+fid+'Bar');
	//console.log('#'+fid);
	
	this.progressBar = $('#'+fid+'Bar');
	$('#'+fid).show();
	$('#'+fid).css('backgroundImage','none');


	this.setProgress = function(progress)
	{
		this.progressBar.width(progress+'%').html('&nbsp;'+this.filesize+'&nbsp;/&nbsp;'+progress + "% ");
	};
	this.setAbort = function(jqxhr)
	{
		var sb = this.statusbar;
		this.abort.click(function()
		{
			jqxhr.abort();
			sb.hide();
		});
	}
}




// DRAG&DROP upload pre-check with
var isDragDrop = null;
function LmEx_dragFileUpload(files,fp,LID,ID,upllayer,gtabid,fieldid)
{
	upl_file = new Array();
	upl_size = new Array();
	
	LmEx_multiupload(files.length,upllayer,LID,fp,gtabid,fieldid,ID);
	
	for (var i = 0; i < files.length; i++)
	{
		e = i+1;
		
		document.getElementById('lmbUploadLayer_'+fp+'_'+e).innerHTML = '\
		<input id="files['+fp+'_'+e+']" value="'+files[i]["name"]+'">\
		<input type="hidden" name="dublicate[type]['+e+']">\
		<input type="hidden" name="dublicate[subj]['+e+']">';
		document.getElementById('lmbUploadLayerSubmit_'+fp+'_'+e).style.display = 'none';

                // fill array
		upl_file.push(escape(files[i]['name']));
		upl_size.push(escape(files[i]['size']));
	}
	
	// uploadcheck
	if(upl_file){
		file = upl_file.join(";");
		size = upl_size.join(";");
		var getstring = "fileUploadCheck&level="+LID+"&name="+file+"&size="+size+"&ID="+ID+"&fp="+fp;
		
		mainfunc = function(result){LmEx_dragFileUploadPost(result,fp,files,LID,ID,gtabid,fieldid);};
		ajaxGet(null,'main_dyns.php',getstring,null,'mainfunc',null,null,1);
	}
}


// DRAG&DROP show upload pre-check result
function LmEx_dragFileUploadPost(result,fp,files,LID,ID,gtabid,fieldid) {
	
	if(result){ajaxEvalScript(result)};

	formel = document.form1;
	
	// file has dublicates - show parameters
	if(result){
		
		isDragDrop = new Array();
		isDragDrop['files'] = files;
		isDragDrop['LID'] = LID;
		isDragDrop['ID'] = ID;
		isDragDrop['gtabid'] = gtabid;
		isDragDrop['fieldid'] = fieldid;
		
		$('.lmbUploadProgress').hide(); //hide all progressbars
		document.getElementById("dublicateCheckLayer").style.visibility='visible'; // show parameter formular
		document.getElementById("dublicateCheckLayer").innerHTML = result;
	
	// direct files upload
	}else{
		
		isDragDrop = null;
		
		for (var i = 0; i < files.length; i++)
		{
			var e = i+1;
			
			formData = new FormData();
			formData.append('file['+e+']', files[i]);
			formData.append('LID', LID);
			formData.append('ID', ID);
			formData.append('gtabid', gtabid);
			formData.append('fieldid', fieldid);
			formData.append('dublicate[subj][1]', formel.elements['dublicate[subj]['+e+']'].value);
			formData.append('dublicate[type][1]', formel.elements['dublicate[type]['+e+']'].value);

			var status = new lmb_createStatusbar('lmbUploadState_'+fp+'_'+e);
			status.filesize=lmb_fileSize(files[i].size,1);
			lmb_uploadCount++;
			lmb_fileUpload(formData,status,formel);
		}
	}
}


// specific refresh of form
function lmb_refreshForm(formel)
{
	// explorer main view
	if(document.form1.action.value == 'explorer_main' || document.form1.action.value == 'mini_explorer'){
		// drop all upload forms
		$(".lmbUploadDiv").remove();
		LmEx_send_form(1,1);
	// gtab_change file relations
	}else if(document.form1.action.value == 'gtab_change' || document.form1.action.value == 'gtab_neu'){
		LmExt_Ex_RelationFields(formel['f_gtabid'].value,formel['f_fieldid'].value,'','',formel['f_datid'].value,'','','','','','','','');
		// drop all upload forms
		$(".lmbUploadDiv").remove();
	}
}


//----------------- move/copy pre-check -------------------
var LmEx_pasteFilelist;
var LmEx_pasteTyp;
function LmEx_ajaxPasteCheck(evt,filelist,typ) {
	if(!filelist){return;}
	LmEx_pasteFilelist = filelist;
	LmEx_pasteTyp = typ;
	LmEx_setxypos(evt,"dublicateCheckLayer");
	var getstring = "&level="+document.form1.LID.value+"&filelist="+escape(filelist);
	ajaxGet(null,'main_dyns.php','filePasteCheck' + getstring,null,'LmEx_ajaxResultPasteCheck');
}

//----------------- show upload preview check -------------------
function LmEx_ajaxResultPasteCheck(result) {
	if(result.substr(0,7) == 'direct-'){
		if(LmEx_pasteTyp == 'copy'){
			LmEx_copy_file(LmEx_pasteFilelist);
		}else if(LmEx_pasteTyp == 'move'){
			LmEx_move_file(LmEx_pasteFilelist);
		}
	}else if(result){
		document.getElementById("dublicateCheckLayer").style.visibility='visible';
		document.getElementById("dublicateCheckLayer").innerHTML = result;
	}else{
		alert('failure!');
	}
}


// --- Info Popus -----------------------------------
var dyns_el;
var dyns_actid;

function LmEx_dynsearchGet(evt,el,actid,par1,par2) {
	dyns_el  = el;

	//if(document.getElementById(dyns_el).innerHTML && dyns_actid == actid && !par2){
	if(document.getElementById(dyns_el).innerHTML && dyns_actid == actid){
		LmEx_dynsClose(dyns_el);
	}else{

		if (window.ActiveXObject) {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}else if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}

		xmlhttp.open("GET", "main_dyns.php?&actid="+actid+"&par1="+par1+"&par2="+par2,true);
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){
				LmEx_dynsShow(xmlhttp.responseText);
			}
		};
		xmlhttp.send(null);
	}
	dyns_actid  = actid;
}

// --- Info Popus Show -----------------------------------
function LmEx_dynsShow(value) {
	var el = document.getElementById(dyns_el);
	el.style.display='';
	el.parentNode.style.display='';
	el.innerHTML = value;
}

// --- Info Popus Close -----------------------------------
function LmEx_dynsClose(id) {
	var el = document.getElementById(id);
	el.style.display='none';
	el.parentNode.style.display='none';
	el.innerHTML = '';
}


// --- search -----------------------------------
function LmEx_detailSearch(evt, fieldid, element){
        if(element) {
            evt = element.id || evt;
        }           
        
	actid = "gtabSearch&gtabid=" + jsvar["gtabid"] + "&fieldid=" + fieldid;
	mainfunc = function(result){LmEx_detailSearchPost(result,fieldid);};
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc");
	limbasDivShow(element, evt,'limbasDetailSearch');
}

function LmEx_detailSearchPost(result,fieldid){
	document.getElementById("limbasDetailSearch").innerHTML = result;
	if(fieldid){Lm_divchange(fieldid);}
}

// -----------------------------------------------------------------------------------------------


// --- Formular senden ----------------------------------
var LmEx_edit_id = null;
var LmEx_edit_norm = null;
function LmEx_send_form(id,ajax) {
	if(tbwidth && rowsize){LmEx_tdsize();}
	if(document.form1.edit_id && LmEx_edit_id){document.form1.edit_id.value = LmEx_edit_id+";"+LmEx_edit_norm;}

	if(ajax && document.getElementById('gtabExplBody')){
		$.ajax({
			type: "POST",
			url: "main_dyns.php?actid=fileMainContent",
			data: $('#form'+id).serialize(),
			success: function(data){
				document.getElementById('gtabExplBody').innerHTML=data;
				ajaxEvalScript(data);
			}
		});
		rowsize = 0;
		document.form1.rowsize.value='';
	}else{
		eval("document.form"+id+".submit();");
	}

}

// ---------------- Sendkeypress----------------------
function LmEx_sendkeydown(evt) {
	if(evt.keyCode == 13){
		if(browser_ie){
			window.focus();
		}else{
			document.form1.LID.focus();
		}
		LmEx_send_form(1,1);
	}
}

// Kontextmenü schließen
var closingmenu = null;
function LmEx_close_menu(par){
	if(par == 3){LmEx_divclose();}
	else if(par == 1){
		closingmenu = window.setTimeout("LmEx_close_menu(3)", 100);
	}
}

// ---- Ordner wechseln -----
function LmEx_select_folder(evt,level){
	document.form1.LID.value = level;
	LmEx_send_form(1);
}


// Selektierte Dateien
function LmEx_selectedFileList(lid){
	var cc = null;
	var filelist = new Array();
	var pre = 0;

	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");
		if(cc.type == "hidden" && cid[0] == 'filestatus' && cid[2] == lid){
			if(cc.value == 1){
				filelist[cid[1]+'_'+cid[3]] = 1;
				pre = 1;
			}else{
				filelist[cid[1]+'_'+cid[3]] = 0;
			}
		}
	}

	if(!pre){
		return false;
	}

	return filelist;
}


// Anzahl Selektierter ermitteln
function LmEx_check_selected(lid,norm){
	var count = 0;
	var filelist = LmEx_selectedFileList(lid);

	if(norm){
		for (var i in filelist){
			if(filelist[i] == norm){count++;}
		}
	}else{
		for (var i in filelist){
			if(filelist[i]){count++;}
		}
	}

	return count;
}

// Ansicht-Felder auswählen

function fieldlist(field){
	
	var flshow = new Array();
	var flhide = new Array();

	var textel = document.getElementById("dc_"+field);

	if(textel.style.color == 'green'){
		textel.previousSibling.style.visibility = 'hidden';
		textel.style.color = '';
		flhide.push(field);
	}else{
        textel.previousSibling.style.visibility = '';
		textel.style.color = 'green';
		flshow.push(field);
	}
	document.form1.ffilter_fl_show.value = flshow.join(";");
	document.form1.ffilter_fl_hide.value = flhide.join(";");
	tbwidth = 1;
	
	LmEx_send_form(1,1);
}

// ---- File-Aktivierung -----
function LmEx_activate_file(evt,id,filename,lid,norm){

	if(evt.ctrlKey && evt.shiftKey && norm == 'd'){
		LmEx_file_detail(id);
	}

	var prev_id = LmEx_edit_id;
	var filestatus = "filestatus_"+norm+"_"+lid+"_"+id;
	var elline = "elline_"+norm+"_"+lid+"_"+id;
	var start = null;
	var down = null;
	var up = null;

        var elid = document.getElementById(filestatus).value;

	LmEx_divclose();
	if(!evt.ctrlKey && !evt.shiftKey){
		LmEx_check_all(0,lid);
	}

	if(evt.shiftKey && prev_id){
		var filelist = LmEx_selectedFileList(lid);
		for (var i in filelist){

				var key = i.split("_");
				var ftyp = key[0];
				var fid = key[1];

				if(start){
					document.getElementById("filestatus_"+ftyp+"_"+lid+"_"+fid).value=1;
					document.getElementById("elline_"+ftyp+"_"+lid+"_"+fid).style.backgroundColor = jsvar["WEB7"];
				}

				if(fid == prev_id && !up){
					start = 1;
					down = 1
				}
				if(fid == id && !down){
					start = 1;
					up = 1;
				}
				if(fid == id && !up){
					start = 0;
					down = 1
				}
				if(fid == prev_id && !down){
					start = 0;
					up = 1;
				}
		}
	}

	if(elid > 0){
		if(!filelist){
			document.getElementById(filestatus).value=0;
			document.getElementById(elline).style.backgroundColor = '';
		}
	}else{
		document.getElementById(filestatus).value=1;
		document.getElementById(elline).style.backgroundColor = jsvar["WEB7"];
		if(document.form_rename){document.form_rename.rename.value = filename;}
		LmEx_edit_id = id;
		LmEx_edit_norm = norm;
	}

	var filelist = LmEx_selectedFileList(lid);
	if(filelist){
		if(browser_ns5){
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.opacity = '1.0';}
		}else{
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.filter = 'Alpha(opacity=100)';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.filter = 'Alpha(opacity=100)';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.filter = 'Alpha(opacity=100)';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.filter = 'Alpha(opacity=100)';}
		}
	}else{
		if(browser_ns5){
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.opacity = '0.3';}
		}else{
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.filter = 'Alpha(opacity=30)';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.filter = 'Alpha(opacity=30)';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.filter = 'Alpha(opacity=30)';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.filter = 'Alpha(opacity=30)';}
		}
	}

	if(document.form1.filename){
		document.form1.filename.value = filename;
	}
}

// ---- line over / out -----
function LmEx_explorerLineOver(el,id,typ,lid,norm){
	if(document.getElementById("filestatus_"+norm+"_"+lid+"_"+id).value != 1){
		if(typ == 1){
			el.style.backgroundColor = jsvar["WEB7"];
		}else{
			el.style.backgroundColor = '';
		}
	}
}

// aktivate / deaktivate all
function LmEx_check_all(val,lid){
	var cc = null;
	var ar = document.getElementsByTagName("input");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");

		if(cc.type == "hidden" && cid[0] == 'filestatus' && cid[2] == lid){
			if(val){
				document.getElementById("elline_"+cid[1]+"_"+cid[2]+"_"+cid[3]).style.backgroundColor = jsvar["WEB7"];
				cc.value = 1;
			}else{
				document.getElementById("elline_"+cid[1]+"_"+cid[2]+"_"+cid[3]).style.backgroundColor = '';
				cc.value = 0;
			}
		}
	}
}

function LmEx_newfile(){
	LmEx_divclose();
	var filename = prompt(jsvar["lng_813"]);
	if(name){
		LmEx_divclose();
		document.form1.add_file.value = filename;
		document.form1.submit();
	}
}

function LmEx_delfile(){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var del = confirm(jsvar["lng_822"] + ' (' + count + ')');
		if(del){
			document.form1.del_file.value = 1;
			document.form1.submit();
		}
	}else{alert(jsvar["lng_1717"]);}
}

function LmEx_favorite_file(){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var del = confirm(jsvar["lng_2219"] + ' (' + count + ')');
		if(del){
			document.form1.favorite_file.value = 1;
			document.form1.submit();
		}
	}else{alert(jsvar["lng_1717"]);}
}

//function LmEx_empty_file(){
//	LmEx_divclose();
//	var del = confirm(jsvar["lng_1709"]);
//	if(del){
//		document.form1.del_file.value = 2;
//		document.form1.submit();
//	}
//}

function LmEx_refresh_thumbs(){
	LmEx_divclose();

	document.form1.refresh_file.value = 1;
	document.form1.submit();
}

function LmEx_ocrfile(format,destination,quality){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		if(destination == 'preview'){
			if(LmEx_edit_id && LmEx_edit_norm == "d"){
				LmEx_open_preview(jsvar["LID"],LmEx_edit_id,'ocr',format+'%26'+quality);
			}
		}else{
			document.form1.ocr_file.value = 1;
			document.form1.ocr_format.value = format+'&'+quality;
			document.form1.ocr_destination.value = destination;
			document.form1.submit();
		}
	}else{alert(jsvar["lng_1717"]);}
}

function LmEx_download_archive(method){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		document.form1.action.value = "explorer_download";
		document.form1.target = "new";
		document.form1.download_archive.value = method;
		LmEx_send_form(1);
		document.form1.action.value = "explorer_main";
		document.form1.target = "_self";
	}else{alert(jsvar["lng_1717"]);}
}


function LmEx_preview_archive(method,size){
	LmEx_divclose();

	if(size && !isNaN(size)){
		method = method+'-'+size
	}

	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var convert = confirm(jsvar["lng_1760"] + ' (' + count + ')');
		if(convert){
			document.form1.convert_file.value = method;
			document.form1.submit();
		}
	}else if(LmEx_edit_id){
		LmEx_open_preview(jsvar["LID"],LmEx_edit_id,method,'')
	}else{
		alert(jsvar["lng_1717"]);
	
	}
}


function LmEx_uploadFromPath(el,LID){
	
	var status = new lmb_createStatusbar('lmbUploadFromPath');
	status.filesize='import';
	el.style.visibility = 'hidden';
	
	$.ajax({
		xhr: function()
		{
			var xhr = new window.XMLHttpRequest();
			//Download progress
			xhr.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
					var percentComplete = evt.loaded / evt.total;
					//Do something with download progress
					//console.log(percentComplete*100);
					
					//show progressbar
					status.setProgress(percentComplete*100);
				}
			}, false);
			return xhr;
		},
		type: 'POST',
		url: 'main_dyns.php?actid=fileUploadFromPath&LID='+LID+'&path='+document.getElementById('LmEx_ImportPath').value+'&type='+document.getElementById('LmEx_ImportPathType').value,
		data: {},
		success: function(data){
			//Do something success-ish
			status.setProgress(100);
			LmEx_send_form(1,1);
			LmEx_divclose();
		}
	});
	
}



// copy file
function LmEx_copy_file(val){
	LmEx_divclose();
	document.form1.copy_file.value = val;
	document.form1.submit();
}

// move file
function LmEx_move_file(val){
	LmEx_divclose();
	document.form1.move_file.value = val;
	document.form1.submit();
}

// Dateien in Cookie speichern
function LmEx_cache_file(todo){
	var lid = jsvar["LID"];

	document.getElementById('filemenu').style.visibility = 'hidden';
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var copyfile = new Array();
		var filelist = LmEx_selectedFileList(lid);

		for (var i in filelist){
			if(filelist[i]){
				var key = i.split("_");
				var ftyp = key[0];
				var fid = key[1];
				copyfile.push(ftyp+fid);
			}
		}

		// set file
		if(copyfile.length > 0){
			var cookiename = cache_list(todo,copyfile.length,document.form1.typ.value);
			var wert = copyfile.join(";");
			setzeCookie(cookiename, wert);
		}

		LmEx_ajaxCopyPasteEvent(todo);

	}else{alert(jsvar["lng_1717"]);}
	LmEx_divclose();
}


// versioning diff text
function limbasFileVersionDiff(el,file1,file2,typ){
	
	$(".lmbFileVersionDiff").remove();
	
	$.ajax({
	url: "main_dyns.php?actid=fileVersionDiff&file1="+file1+"&file2="+ file2 +"&typ="+ typ,
	cache: false
	})
	.done(function( result ) {
		var val = "<div id='limbasFileVersionDiff' class='lmbFileVersionDiff' onclick='$(this).remove()'>"+result+"</div>";
		$("body").append(val);
		limbasDivShow(el,'','limbasFileVersionDiff');
	});
}

// Picture preview
function LmEx_preview_thumbs(evt,url,close) {
    // create img to show as dialog if not existent
    var size = jsvar['thumbsize2'].split('x');
    if(!$('#thumbPreviewLayer').length && !close) {
        $("body").append(
            '<img id="thumbPreviewLayer" onclick="LmEx_preview_thumbs(null,null,1)" autofocus="1" style="width:'+size[0]+'px; height:'+size[1]+'px; padding: 0px;">'
        );
    } else if (close) {
        $('#thumbPreviewLayer').dialog("close");
        return;
	}

    // set img url and show as dialog
    $('#thumbPreviewLayer')
		.attr('src', url)
        .css({'position': 'relative', 'left': '0', 'top': '0', 'width': size[0], 'height': size[1]})
        .dialog({
            resizable: false,
            modal: true,
            autoOpen: true,
            zIndex: 99999,
            width: size[0],
            close: function () {
                $('#thumbPreviewLayer').hide().remove();
            },
            open: function () {
                // click outside of dialog -> close preview
                $('.ui-widget-overlay').bind('click', function () {
                    $('#thumbPreviewLayer').hide().remove();
                });
            }
        }); 
}

// --- TD-Breite Bewegen Maus -----------------------------------
var tdwidth = null;
var tbwidth = null;
var posi = null;
var eltd = null;
var elts = null;
var tbw = null;
var tdw = null;
var evt = null;
var eid = null;
var to_count = null;
var rowsize = null;

function LmEx_startDrag(evt,ID) {

	eid = ID;
	elh = "tabheader_"+ID;
	els = "tabsearch_"+ID;
	eltd = document.getElementById(elh);
	elts = document.getElementById(els);

	tdwidth = document.getElementById(elh).offsetWidth;
	tbwidth = document.getElementById('filetab').offsetWidth;
	if(browser_ns5){
		posi = evt.pageX;
	}else{
		posi = window.event.clientX;
	}
	document.onmouseup = LmEx_endDrag;
	document.onmousemove = LmEx_drag;
	return false;
}

function LmEx_drag(e) {
	if(browser_ns5){
		tdw = e.pageX - posi + tdwidth;
		tbw = e.pageX - posi + tbwidth;
	}else{
		tdw = window.event.clientX - posi + tdwidth;
		tbw = window.event.clientX - posi + tbwidth;
	}
	if(tdw > 10){
		if(jsvar["res_viewcount"]){
			for(var i=1;i<=jsvar["res_viewcount"];i++){
				if(document.getElementById('td_'+eid+'_'+i)){
					document.getElementById('td_'+eid+'_'+i).style.width = tdw;
				}
			}
		}
		if(to_count && eid == 1){
			for(var i=1;i<to_count;i++){
				document.getElementById('to_'+i).style.width = tdw;
			}
		}

		eltd.style.width = tdw;
		if(elts){elts.style.width = tdw;}
		document.getElementById('filetab').style.width = document.getElementById('GtabBodyTable').style.width;
	}
	rowsize = 1;
	return false;
}

function LmEx_endDrag() {
	document.onmousemove = null;
	eltd = null;
	eltb = null;
	return true;
}

// --- Feldschleife für Spaltenbreite festlegen -----
var row_size = new Array();
function LmEx_tdsize(){
	var cc = null;
	var e = 0;
	var ar = document.getElementsByTagName("td");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var cid = cc.id.split("_");
		if(cid[0] == 'tabheader'){
			row_size[e] = cid[1] + "_" + cid[2] + "," + $(cc).width();
			e++;
		}
	}

	row_size[e] = "t," + document.getElementById("filetab").offsetWidth;
	document.form1.rowsize.value = row_size.join(";");
}



// Layer positionieren und öffnen
function LmEx_setxypos(evt,el) {
	if(browser_ns5){
		document.getElementById(el).style.left = evt.pageX;
		document.getElementById(el).style.top = evt.pageY;
	}else{
		document.getElementById(el).style.left = window.event.clientX + document.body.scrollLeft;
		document.getElementById(el).style.top = window.event.clientY + document.body.scrollTop;
	}
}

var activ_menu = null;
function LmEx_divclose() {
	if(!activ_menu){
		$( ".lmbContextMenu" ).css( "display", "none" );
	}
	activ_menu = 0;
}


function LmEx_body_click(){
	window.setTimeout("LmEx_divclose()", 50);
	//window.setTimeout("limbasDivClose()", 50);
}

// deprecated, not used anymore
/*
function LmEx_open_menu(evt,el,menu){
        activ_menu = 1;
	LmEx_setxypos(evt,menu);
	if(menu == 'cachelist'){
		LmEx_create_cachelist(evt);
		if(jsvar["copycache"] == 1){
			return;
		}
	}
	document.getElementById(menu).style.visibility='visible';
}*/


function LmEx_open_menu(el,menu){
    limbasDivShow(el,el.parentNode.id,menu,'',1);
}

/* --- Detail-Fenster ----------------------------------- */
function LmEx_open_detail(evt,ID,lid,detail) {
        // close all contextmenues
        limbasDivClose();
        
	if(!lid){lid = jsvar["LID"];}
	LmEx_check_all(0,lid);

	if(evt.ctrlKey || evt.shiftKey || detail){
		detail = open("main.php?action=download&ID=" + ID ,"download","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
	}else{
		if(document.getElementById("limbasDivMenuExplContext")){ 
                    limbasDivShow("",evt,"limbasDivMenuExplContext");
                    activ_menu = 0; // function only gets called on right click which doesnt trigger click on body
                }
	}
	return false;
}

/* --- Vorschau-Fenster ----------------------------------- */
function LmEx_open_preview(LID,ID,method,format) {
	detail = open("main.php?"+jsvar["SID"]+"&action=explorer_convert&LID=" + LID + "&ID=" + ID + "&method="+ method + "&format="+ format ,"preview","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
}

//function LmEx_open_message(ID,MID) {
//	open("main.php?"+jsvar["SID"]+"&action=message_detail&det=1&get=1&ID=" + ID + "&MID=" + MID + "&LID="+jsvar["LID"]+"" ,"Kalender","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=550");
//}

/* --- Tabellen-Fenster ----------------------------------- */
//function LmEx_open_tab(gtabid,ID) {
//	record = open("main.php?"+jsvar["SID"]+"&action=gtab_deterg&gtabid=" + gtabid + "&ID=" + ID + "" ,"Record","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=500");
//}

/* --- neues explorer-Fenster ----------------------------------- */
function LmEx_open_newexplorer() {
	LmEx_divclose();
	newexplorer=open("main.php?"+jsvar["SID"]+"&action=explorer" ,"LIMBAS_Explorer","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_miniexplorer() {
	LmEx_divclose();
	miniexplorer=open("main.php?"+jsvar["SID"]+"&action=mini_explorer&home_level="+jsvar["LID"] ,"Datei_Browser","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=460,height=320");
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_dublicates() {
	LmEx_divclose();
	dublicates=open("main.php?&action=explorer_dublicates&LID="+jsvar["LID"] ,"Dublicates","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_details(evt,ID,LID,gtab_id,form_id,dimension) {
	LmEx_divclose();

	if(!evt.ctrlKey && !evt.shiftKey){
		if(!document.getElementById("LmEx_DetailFrame")){
			$("body").append('<div id="LmEx_DetailFrame" style="position:absolute;display:none;z-index:9999;overflow:hidden;width:300px;height:300px;"><iframe id="LmEx_DetailOpen" style="width:100%;height:100%;border:none;overflow:auto;"></iframe></div>');
		}
		
		if(form_id == '0'){form_id = null;}
		
		if(dimension){
			var size = dimension.split('x');
			var w_ = (parseFloat(size[0])+80);
			var h_ = (parseFloat(size[1])+100);
		}else{
			var w_ = 800;
			var h_ = 600;
		}

		// display in iframe
		$("#LmEx_DetailFrame").css({'position':'relative','left':'0','top':'0'}).dialog({
			width: w_,
			height: h_,
			resizable: true,
			modal: true,
			zIndex: 10,
			open: function(ev, ui){
				if(form_id){
					$('#LmEx_DetailOpen').attr("src","main.php?&action=gtab_change&ID="+ID+"&gtabid="+gtab_id+"&form_id="+form_id);
				}else{
					$('#LmEx_DetailOpen').attr("src","main.php?action=explorer_detail&LID="+LID+"&ID="+ID);
				}
			}
		});

	}else{
		filechange=open('main.php?action=explorer_detail&LID='+LID+'&ID='+ID ,'detail','toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=800,height=650');
	}
}

function LmEx_file_detail(id){
	if(!id && LmEx_edit_norm == 'd'){id = LmEx_edit_id;}
	if(id){
		detail = open("main.php?"+jsvar["SID"]+"&action=explorer_detail&level="+jsvar["level"]+"&LID="+jsvar["LID"]+"&ID=" + id + "" ,"Info","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
	}
}


/* --- Tabellen-Fenster ----------------------------------- */
function lmEx_openDataset(gtabid,ID) {
	record = open("main.php?&action=gtab_change&gtabid=" + gtabid + "&ID=" + ID + "" ,"Record","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=500");
}

/* --- Detail-Fenster ----------------------------------- */
function lmEx_dropRelation(ID,LID,RELATION) {
	document.location.href = "main.php?&action=explorer_detail&level="+LID+"&LID="+LID+"&ID=" + ID + "&show_part=origin&drop_relation=" + RELATION;
}