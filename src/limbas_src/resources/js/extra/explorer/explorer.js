/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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

    document.getElementById("conextIcon_191").style.opacity = '1.0';

}

//region Upload

/**
 * In duplicate dialog with multiple duplicate files,
 *   if the user sets the checkbox 'apply for all files':
 *   Update the duplicate setting of all files
 * @param fileCount total number of files
 * @param newType 0=skip, 1=rename, 2=overwrite, 3=version
 * @param fileIndex index of the file whose type is changed
 */
function updateDublicateUploads(fileCount, newType, fileIndex){
	// only update other files if user wants to
	var changeAllCheckbox = $('#forallDublicateUploads').get(0);
	if (changeAllCheckbox && !changeAllCheckbox.checked) {
		return;
	}

	// TODO maybe improve
	var chk = document.getElementsByName("fileVersionType_"+fileIndex)[newType].checked;
	for (var i = fileIndex; i <= fileCount; i++){
		if(document.getElementById("fileVersionId_"+i)){
			document.getElementsByName("fileVersionType_"+i)[newType].checked = chk;

			// display versioning text
			if(newType == 3){
				document.getElementById("versioning_subj_"+i).style.display='';
			}else{
				document.getElementById("versioning_subj_"+i).style.display='none';
			}
		}
	}
}

/**
 * Collects information from duplicate dialog and initiates upload
 * @param fileCount total number of files
 */
function selectDublicateUploads(fileCount){
	if(fileCount > 0){

        var duplicateTypes = [];
        var versionNames = [];
        for (var i = 1; i <= fileCount; i++){
            if(document.getElementById("fileVersionId_" + i)){
                var duplicateType = radioValue(document.getElementsByName("fileVersionType_" + i)) || '';
                var versionName = document.getElementsByName("fileVersionSubj_" + i)[0].value || '';
                upload = true;
                duplicateTypes.push(duplicateType);
                versionNames.push(versionName);
            }else{
                duplicateTypes.push('');
                versionNames.push('');
            }
        }

        // destroy duplicate dialog
        $('#ldmsUploadDublicateModal').modal('hide');
        $('#ldmsUploadModal').modal('hide');

		if (LmEx_pasteTyp === 'copy') {
			LmEx_copy_file(LmEx_pasteFilelist,duplicateTypes,versionNames);
		} else if (LmEx_pasteTyp === 'move') {
			LmEx_move_file(LmEx_pasteFilelist,duplicateTypes,versionNames);
		} else {

            lmbUploadData.duplicateTypes = duplicateTypes;
            lmbUploadData.versionNames = versionNames;

            LmEx_uploadFiles(lmbUploadData.files, lmbUploadData.uploadUrl);
		}
	}
}

/**
 * If only selected file is an archive, show option to unzip
 * @param files FileList from input field
 * @param fp identifier of html elements for this upload
 */
function LmEx_UploadArchivOption(files, fp) {
    var checkbox = $('#lmbUploadStateUnzip_' + fp).hide();
	if (files.length === 1) {
		var name = files[0].name;
		var nameParts = name.split('.');
		var ext = nameParts[nameParts.length - 1];
		var extensions = ["gz", "zip", "tar", "rar", "gzip", "tgz"];
		if (extensions.indexOf(ext) !== -1) {
            checkbox.show();
		}
	}
}

// number of files to upload. used to determine if all files were uploaded
var lmb_uploadCount = 0;

/**
 * Creates a Status / Progress bar for the specified fp. Displays information of the given file
 * @param fp
 * @param file object of File class
 * @constructor
 */
function LmbStatusBar(fp, file) {
	this.container = $('#lmbUploadDiv_' + fp).first();
	this.file = file;

	this.row = $(`
		<div class="row mt-2">
			<div class="col">
				<div id="lmbUploadState_${fp}" class="progress rounded-2" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				  	<div id="lmbUploadState_${fp}Bar" class="progress-bar" style="width: 0%">${this.file.name}</div>
				</div>
			</div>
		</div>
	`);

	this.progressBar = this.row.find('.progress-bar');

	// add progress bar
	this.container.append(this.row);

    // sets width of progress bar in container to simulate progress
	this.setProgress = function(progress) {
		this.progressBar.css('width', progress + '%');
	};
}

/**
 * Adds functionality to drop files from OS
 * @param dropArea jQuery object that will be the drop area
 * @param onDrop function (files) that is called on drop
 */
function LmEx_createDropArea(dropArea, onDrop) {
    dropArea.addClass('lmbUploadDroparea');

    $('body')
        .on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
            // prevent default action when dropping file on body
            e.preventDefault();
            e.stopPropagation();
        })
        .on('dragover dragenter', function() {
            // add classes to indicate dropping area
            $(this).addClass('lmbBodyDropareaActive');
        })
        .on('dragleave dragend drop', function() {
            // remove classes after dropping ended
            $(this).removeClass('lmbBodyDropareaActive');
        });

    // init drop area
    dropArea
		.on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
            e.preventDefault();
			e.stopPropagation();
		})
		.on('dragover dragenter', function() {
            $(this).addClass('lmbUploadDropareaHover');
		})
		.on('dragleave dragend drop', function() {
            $(this).removeClass('lmbUploadDropareaHover');
		})
		.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
			if (files && files.length > 0) {
				onDrop(files);
			}
		});
}

/**
 * Adds an input[type=file] to the element specified with parentID
 * @param parentID
 */
function LmEx_showUploadField(parentID, folderID, fp, gtabid, fieldid, datid) {
	// optional params
	parentID = parentID || 'lmbUploadLayer';
	folderID = folderID || jsvar['LID'];
	fp = fp || 1;
	gtabid = gtabid || '';
	fieldid = fieldid || '';
	datid = datid || '';

	let html = `
        <div class="modal fade" id="ldmsUploadModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body overflow-visible" id="ldmsUploadModal-body">
                        <div class="gtabHeaderInputTR lmbUploadDiv" id="lmbUploadDiv_${fp}">
                            <input type="hidden" name="f_LID" value="${folderID}">
                            <input type="hidden" name="f_gtabid" value="${gtabid}">
                            <input type="hidden" name="f_fieldid" value="${fieldid}">
                            <input type="hidden" name="f_datid" value="${datid}">
                            <div class="row">
								<div class="col" id="lmbUploadLayer_${fp}">
									<input type="file" class="form-control" multiple id="file[${fp}]" name="file[0]" size="20" onchange="LmEx_UploadArchivOption(this.files, '${fp}');">
								</div>
								<div class="col-auto">
									<div id="lmbUploadLayerSubmit_${fp}" class="d-flex flex-row align-items-center">
										<div id="lmbUploadStateUnzip_${fp}" class="form-check me-2" style="display:none;">
											<label for="file_archiv[${fp}]" class="form-check-label">${jsvar['lng_1560']}</label>
											<input class="form-check-input" type="checkbox" id="file_archiv[${fp}]" name="file_archiv[0]">
										</div>
										<button type="button" class="btn btn-primary" style="vertical-align:middle" onclick="activ_menu=1; LmEx_uploadFilesPrecheck($(this).closest('.lmbUploadDiv').find('input[type=file]').get(0).files, '${folderID}', '${fp}', '${gtabid}', '${fieldid}', '${datid}');">${jsvar['lng_815']}</button>
									</div>
								</td>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">${jsvar['lng_844']}</button>
                </div>
            </div>
        </div>`;

	$('#' + parentID)
		.show()
		.html(html);

	$('#ldmsUploadModal').modal('show');
}

// contains data of the current upload
var lmbUploadData;

/**
 * Performs a limbas-precheck for the given files
 * @param files
 */
function LmEx_uploadFilesPrecheck(files, folderID, fp, gtabid, fieldid, datid) {
    lmbUploadData = {
    	files: files,
        folderID: folderID,
        fp: fp,
        gtabid: gtabid || '',
        fieldid: fieldid || '',
        datid: datid || ''
    };
    LmEx_uploadFilesPrecheckInner(files);
}
function LmEx_uploadFilesPrecheckInner(files) {
	// extract filenames and filesizes for url
	var fileNames = [];
	var fileSizes = [];
	for (var i = 0; i < files.length; i++) {
        //fileNames.push(encodeURIComponent(files[i]['name']));
        //fileSizes.push(encodeURIComponent(files[i]['size']));
        fileNames.push(files[i]['name']);
        fileSizes.push(files[i]['size']);
	}

	var url = 'fileUploadCheck&level='+lmbUploadData.folderID + '&name='+fileNames.join(';') + '&size='+fileSizes.join(';') + '&ID='+lmbUploadData.datid + '&fp='+lmbUploadData.fp + '&gtabid='+lmbUploadData.gtabid + '&fieldid='+lmbUploadData.fieldid;
    ajaxGet(null, 'main_dyns.php', url, null, function(result) {
        try {
            var resultObj = JSON.parse(result);
		} catch (err) {
            ajaxEvalScript(result);
            return;
		}

        lmbUploadData.uploadUrl = resultObj.uploadUrl;
        lmbUploadData.authToken = resultObj.authToken;
        var status = resultObj.status;
        if (status === 'confirmDuplicates') {
			lmbUploadData.hasDuplicates = true;
            ajaxEvalScript(resultObj.html);
			LmEx_handleDuplicates(resultObj.html);
            //$('#ldmsUploadModal').modal('toggle');

		} else if (status === 'upload') {
            LmEx_uploadFiles(files, resultObj.uploadUrl);
		}
	});
}

/**
 * Shows a dialog for the user to select what action to perform for duplicate files
 * @param result html returned by limbas
 */
function LmEx_handleDuplicates(result) {

    // use upload modal
    if(document.getElementById('ldmsUploadModal')){
        $('#ldmsUploadModal').modal('show');
        $('#ldmsUploadModal-body').html(result);
    // use dublicate modal
    }else if(document.getElementById('ldmsUploadDublicateModal')){
        $('#ldmsUploadDublicateModal').modal('show');
        $('#ldmsUploadDublicateModal-body').html(result);
    // create new dublicate modal
    }else{
        let html = `
        <div class="modal fade" id="ldmsUploadDublicateModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body overflow-visible" id="ldmsUploadDublicateModal-body">

                    </div>
                </div>
            </div>
        </div>`;

        $('#form1').append(html);
        $('#ldmsUploadDublicateModal').modal('show');
        $('#ldmsUploadDublicateModal-body').html(result);

    }

}

/**
 * Uploades given files separately to the given uploadUrl
 * @param files
 * @param uploadUrl
 */
function LmEx_uploadFiles(files, uploadUrl) {
    var formel = document.form1;
    lmb_uploadCount = 0;

    for (var i = 0; i < files.length; i++) {
        var formData = new FormData();
        formData.append('file[0]', files[i]);
        if (lmbUploadData.hasDuplicates) {
            var dupType = lmbUploadData.duplicateTypes[i];
            if (dupType === 'skip') {
				continue;
            }
            formData.append('dublicate[type][0]', dupType);
            formData.append('dublicate[subj][0]', lmbUploadData.versionNames[i]);
        }

        formData.append('LID', lmbUploadData.folderID);
        formData.append('gtabid', lmbUploadData.gtabid);
        formData.append('fieldid', lmbUploadData.fieldid);
        formData.append('ID', lmbUploadData.datid);
        if (files.length === 1) {
			formData.append('file_archiv[0]', formel.elements['file_archiv['+lmbUploadData.fp+']']?.checked ?? false); // TODO was only in ajax upload, not in drag
        }
        if (lmbUploadData.authToken) {
            formData.append('authToken', lmbUploadData.authToken);
		}

        let status = new LmbStatusBar(lmbUploadData.fp, files[i]);
        lmb_uploadCount++;

		LmEx_uploadFile(formData, uploadUrl, function(workDone, workTotal) {
			status.setProgress(Math.ceil(workDone / workTotal * 100));
        });
	}

	if (!lmb_uploadCount) {
    	lmb_refreshForm();
	}
}

/**
 * Uploads a file to the specified url
 * @param fileFormData	FormData object of data to send
 * @param uploadUrl		Url to upload FormData to
 * @param onProgress	Function (workDone, workTotal) that is called on progress/success
 */
function LmEx_uploadFile(fileFormData, uploadUrl, onProgress) {
	$.ajax({
		xhr: function() {
			// returns XMLHttpRequest with progress listener
			var xhr = $.ajaxSettings.xhr();
			if (xhr.upload) {
				xhr.upload.addEventListener('progress', function(event) {
					if (event.lengthComputable) {
                        var workDone = event.loaded || event.position;
                        var workTotal = event.total;
                        onProgress(workDone, workTotal);
					}
				}, false);
			}
			return xhr;
		},
        url: uploadUrl,
        type: 'POST',
        contentType: false,
        processData: false,
        cache: false,
        data: fileFormData,
        success: function(data) {
            onProgress(100);
            data = data.trim();
            if (data) {
				if (!ajaxEvalScript(data)) {
					lmbShowErrorMsg(data);
				}
            }
            lmb_uploadCount--;
            if(!lmb_uploadCount) {
                lmb_refreshForm();
                lmbUploadData = null;
            }
        },
		error: function() {
            lmbShowErrorMsg(jsvar["lng_56"]);
		}
	});
}

/**
 * Refreshes content by reloading type-field in gtab, or submit of form in explorer
 */
function lmb_refreshForm() {
	// explorer main view
	if(document.form1.action.value === 'explorer_main' || document.form1.action.value === 'mini_explorer'){
		LmEx_send_form(1,1);
	// gtab_change file relations
	}else if(document.form1.action.value === 'gtab_change' || document.form1.action.value === 'gtab_erg' || document.form1.action.value === 'gtab_neu'){
		LmExt_Ex_RelationFields(lmbUploadData.gtabid ,lmbUploadData.fieldid ,'','',lmbUploadData.datid ,'','','','','','','','');
	}

    // drop all upload forms
    //$(".lmbUploadDiv").remove();
}

//endregion

//----------------- move/copy pre-check -------------------
var LmEx_pasteFilelist;
var LmEx_pasteTyp;
function LmEx_ajaxPasteCheck(evt,filelist,typ) {
	if(!filelist){return;}
	LmEx_pasteFilelist = filelist;
	LmEx_pasteTyp = typ;
	var getstring = "&level="+document.form1.LID.value+"&filelist="+filelist;
	ajaxGet(null,'main_dyns.php','filePasteCheck' + getstring,null,'LmEx_ajaxResultPasteCheck');
}

//----------------- show upload preview check -------------------
function LmEx_ajaxResultPasteCheck(result) {
	if(result && result.substr(0,7) == 'direct-'){
		if(LmEx_pasteTyp == 'copy'){
			LmEx_copy_file(LmEx_pasteFilelist,[]);
		}else if(LmEx_pasteTyp == 'move'){
			LmEx_move_file(LmEx_pasteFilelist,[]);
		}
	}else if(result){
        LmEx_handleDuplicates(result);
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

// -----------------------------------------------------------------------------------------------


// --- Formular senden ----------------------------------
var LmEx_edit_id = null;
var LmEx_edit_norm = null;
function LmEx_send_form(id,ajax) {
	if(tbwidth && rowsize){LmEx_tdsize();}
	if(document.form1.edit_id && LmEx_edit_id){document.form1.edit_id.value = LmEx_edit_id+";"+LmEx_edit_norm;}
    limbasWaitsymbol(null, true);
	if(ajax && document.getElementById('gtabExplBody')){
		$.ajax({
			type: "POST",
			url: "main_dyns.php?actid=fileMainContent",
			data: $('#form'+id).serialize(),
			success: function(data){
                limbasWaitsymbol(null, true, true);
				document.getElementById('gtabExplBody').innerHTML=data;
				ajaxEvalScript(data);
			}
		});
		rowsize = 0;
		document.form1.rowsize.value='';
	}else{
	    limbasWaitsymbol(null, true, true);
		eval("document.form"+id+".submit();");
	}
}

// ---------------- Sendkeypress----------------------
function LmEx_sendkeydown(evt) {
	if(evt.keyCode == 13){
        document.form1.LID.focus();
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
	var filelist = [];
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
	
	var flshow = [];
	var flhide = [];

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
    document.form1.save_setting.value=1;
	tbwidth = 1;
	
	LmEx_send_form(1,1);
}

// ---- File-Aktivierung -----
var lastActivatedFilename = null; // set in LmEx_activate_file, used in LmEx_open_detail
function LmEx_activate_file(evt,id,filename,lid,norm){

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
					document.getElementById("elline_"+ftyp+"_"+lid+"_"+fid).classList.add("table-active");
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
			document.getElementById(elline).classList.remove("table-active");
		}
	} else{
		document.getElementById(filestatus).value=1;
		document.getElementById(elline).style.backgroundColor = jsvar["WEB7"];
		document.getElementById(elline).classList.add("table-active");
		if(document.getElementById('file_dir_rename')){document.getElementById('file_dir_rename').value = filename;}
        lastActivatedFilename = filename;
		LmEx_edit_id = id;
		LmEx_edit_norm = norm;
	}

	var filelist = LmEx_selectedFileList(lid);
	if(filelist){
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.opacity = '1.0';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.opacity = '1.0';}
	}else{
			if(document.getElementById("conextIcon_129")){document.getElementById("conextIcon_129").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_130")){document.getElementById("conextIcon_130").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_171")){document.getElementById("conextIcon_171").style.opacity = '0.3';}
			if(document.getElementById("conextIcon_190")){document.getElementById("conextIcon_190").style.opacity = '0.3';}
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
			el.classList.add("table-active");
		}else{
			el.style.backgroundColor = '';
			el.classList.remove("table-active");
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
				document.getElementById("elline_"+cid[1]+"_"+cid[2]+"_"+cid[3]).classList.add("table-active");
				cc.value = 1;
			}else{
				document.getElementById("elline_"+cid[1]+"_"+cid[2]+"_"+cid[3]).style.backgroundColor = '';
				document.getElementById("elline_"+cid[1]+"_"+cid[2]+"_"+cid[3]).classList.remove("table-active");
				cc.value = 0;
			}
		}
	}
}

// refresh thumbs
function LmEx_refresh_thumbs(){
	LmEx_divclose();
	document.form1.refresh_file.value = 1;
	LmEx_send_form(1);
}


// copy file
function LmEx_copy_file(val,duplicateTypes,versionNames){
	LmEx_divclose();
    document.form1.copy_file.value = val;
    document.form1.duplicateTypes.value = duplicateTypes.join(';')

    // todo versionNames
	LmEx_send_form(1);
}

// move file
function LmEx_move_file(val,duplicateTypes,versionNames){
	LmEx_divclose();
	document.form1.move_file.value = val;
	document.form1.duplicateTypes.value = duplicateTypes.join(';')
    // todo versionNames
	LmEx_send_form(1);
}

// create new folder
 function LmEx_newfile(){
 	LmEx_divclose();
 	var filename = prompt(jsvar["lng_813"]);
 	if(name){
 		LmEx_divclose();
		document.form1.add_file.value = filename;
		LmEx_send_form(1);
 	}
}

// delete file
function LmEx_delfile(){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var del = confirm(jsvar["lng_822"] + ' (' + count + ')');
		if(del){
			document.form1.del_file.value = 1;
			LmEx_send_form(1);
		}
	}else{alert(jsvar["lng_1717"]);}
}

// favorite file
function LmEx_favorite_file(){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var del = confirm(jsvar["lng_2219"] + ' (' + count + ')');
		if(del){
			document.form1.favorite_file.value = 1;
			LmEx_send_form(1);
		}
	}else{alert(jsvar["lng_1717"]);}
}

// OCR file
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
			LmEx_send_form(1);
		}
	}else{alert(jsvar["lng_1717"]);}
}

// download archiv
function LmEx_download_archive(method){
	LmEx_divclose();
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		document.form1.action.value = "download";
		document.form1.target = "new";
		document.form1.download_archive.value = method;
		LmEx_send_form(1);
		document.form2.action.value = "download";
		document.form1.download_archive.value = ''
		document.form1.target = '';
	}else{alert(jsvar["lng_1717"]);}
}

// convert file
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
			LmEx_send_form(1);
			document.form1.convert_file.value = '';
		}
	}else if(LmEx_edit_id){
		LmEx_open_preview(jsvar["LID"],LmEx_edit_id,method,'')
	}else{
		alert(jsvar["lng_1717"]);
	}
}

/**
 * DEPRECATED - DO NOT USE
 * @param evt
 * @param lid
 * @param printerID
 * @constructor
 */
function LmEx_print(evt, lid, printerID) {
    var printFilesArray = [];
    var fileList = LmEx_selectedFileList(lid);
    for (var i in fileList){
        if(fileList[i]){
            var key = i.split("_");
            var ftyp = key[0];
            if (ftyp == 'd' /* file */) {
				var fid = key[1];
                printFilesArray.push(fid);
            }
        }
    }

	if(evt){
		setxypos(evt,'lmbAjaxContainer');
	}
	limbasWaitsymbol(evt,1,0,false);
	document.body.style.cursor = 'wait';

	printFiles(printFilesArray).then((data) => {
		ajaxEvalScript(data.script);
		LmEx_divclose();
		limbasWaitsymbol(evt,1,1);
		document.body.style.cursor = 'default';

	}).catch((data) => {
		LmEx_divclose();
		limbasWaitsymbol(evt,1,1);
		document.body.style.cursor = 'default';
		if (data.msg) {
			lmbShowErrorMsg(data);
		}
		else {
			lmbShowErrorMsg('Server error');
		}
	});
}

function LmEx_uploadFromPath(el,LID){
	var status = $('#lmbUploadFromPath').show();
	var statusBar = status.children().first();
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
                    statusBar.width((percentComplete * 100) + '%');
				}
			}, false);
			return xhr;
		},
		type: 'POST',
		url: 'main_dyns.php?actid=fileUploadFromPath&LID='+LID+'&path='+encodeURIComponent(document.getElementById('LmEx_ImportPath').value)+'&type='+document.getElementById('LmEx_ImportPathType').value,
		data: {},
		success: function(data){
			//Do something success-ish
			status.hide();
			LmEx_send_form(1,1);
			LmEx_divclose();
		}
	});
	
}

// Dateien in Cookie speichern
function LmEx_cache_file(todo){
	var lid = jsvar["LID"];

	document.getElementById('filemenu').style.visibility = 'hidden';
	count =	LmEx_check_selected(jsvar["LID"]);
	if(count > 0){
		var copyfile = [];
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
function limbasFileVersionDiff(el,file1,file2,typ,parent='body'){
	
	$(".lmbFileVersionDiff").remove();

	let parentEl = $(parent);
	
	$.ajax({
	url: "main_dyns.php?actid=fileVersionDiff&file1="+file1+"&file2="+ file2 +"&typ="+ typ,
	cache: false
	})
	.done(function( result ) {
		var val = "<div id='limbasFileVersionDiff' class='lmbFileVersionDiff'>"+result+"</div>";
		parentEl.append(val);
	});
}

// Picture preview
function LmEx_preview_thumbs(evt,url,close) {
    // create img to show as dialog if not existent
    var size = jsvar['thumbsize2'].split('x');
    if(!$('#thumbPreviewLayer').length && !close) {
        $("body").append(
            '<img id="thumbPreviewLayer" onclick="LmEx_preview_thumbs(null,null,1)" style="width:'+size[0]+'px; height:'+size[1]+'px; padding: 0;">'
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

    return;

	eid = ID;
	elh = "tabheader_"+ID;
	els = "tabsearch_"+ID;
	eltd = document.getElementById(elh);
	elts = document.getElementById(els);

	tdwidth = document.getElementById(elh).offsetWidth;
	tbwidth = document.getElementById('filetab').offsetWidth;
    posi = evt.pageX;

	document.onmouseup = LmEx_endDrag;
	document.onmousemove = LmEx_drag;
	return false;
}

function LmEx_drag(e) {

    tdw = e.pageX - posi + tdwidth;
    tbw = e.pageX - posi + tbwidth;

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
    document.form1.save_setting.value=1;
	eltd = null;
	eltb = null;
	return true;
}

// --- Feldschleife für Spaltenbreite festlegen -----
var row_size = [];
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

    document.getElementById(el).style.left = evt.pageX;
    document.getElementById(el).style.top = evt.pageY;

}


// fieldtype upload
var LmEx_uploadNr = 1;
function lmb_formpicdmove(id,way,max){

	if(way == 1){
		var LmEx_uploadNr_ = (LmEx_uploadNr + 1);
	}else if(way == 2){
		var LmEx_uploadNr_ = (LmEx_uploadNr - 1);
	}else if(way == 3){
		var LmEx_uploadNr_ = 1;
	}else if(way == 4){
		var LmEx_uploadNr_ = max;
	}

	if(LmEx_uploadNr_ > max || LmEx_uploadNr_ <= 0){
        return;
	}

    LmEx_uploadNr = LmEx_uploadNr_;

	if(way == 5){
		LmEx_uploadNr = 0;
	}

	var el1 = $("#formpicname_"+id);
	var el2 = $("#formpicname0_"+id);

	el1.hide();
	el2.hide();

	eval("var pic = formpicsn_"+id+"["+LmEx_uploadNr+"].split('#');");

	el1.attr("src",pic[0]);
	el2.attr("src",pic[0]);

	el2.attr("data-id",pic[1]);

	el1.show('slow');
	el2.show('slow');

}

function lmb_formpicdelete(fel,fp){

    var bzm = document.getElementById('formpicid_'+fp).value;
    var el = eval('formpicsn_'+fp+'['+bzm+']');
    el = el.split('#');
    var elID = el[1];

	if(confirm('delete file?')){
		document.getElementsByName(fel+'_delete')[0].value = elID;
		send_form(1);
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
function LmEx_open_detail(evt,el,ID,lid,custmenu) {
    // close all contextmenues
    limbasDivClose();

    if(!lid){lid = jsvar["LID"];}
	LmEx_check_all(0,lid);

	if(evt.ctrlKey || evt.shiftKey){
		open("main.php?action=download&ID=" + ID ,"download","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
	}else if(custmenu) {

        var fieldid = $(evt.target).closest('.element-cell').attr( "data-fieldid" );
        if(!fieldid){fieldid = '';}
        child = 'lmb_custmenu_' + custmenu;

        limbasDivCloseTab.push(child);

        if (!document.getElementById(child)) {
            $('#limbasDivMenuExplContext').after("<div id='" + child + "' class='lmbContextMenu' style='position:absolute;z-index:994;' onclick='activ_menu = 1;'>");
        }

        var actid = "gtabCustmenu&custmenu=" + custmenu + "&ID=" + ID + "&gtabid=" + jsvar["gtabid"]  + "&fieldid=" + fieldid;
        ajaxGet(null, "main_dyns.php", actid, null, '', null, child);
        limbasDivShow(el, evt, child);

    }else {

        if (document.getElementById("limbasDivMenuExplContext")) {
            if (lastActivatedFilename) {
                $('#file_rename').val(lastActivatedFilename);
            }

            limbasDivShow("", evt, "limbasDivMenuExplContext");
            activ_menu = 0; // function only gets called on right click which doesnt trigger click on body
        }

    }

	return false;
}

/* --- Vorschau-Fenster ----------------------------------- */
function LmEx_open_preview(LID,ID,method,format) {
	detail = open("main.php?action=explorer_convert&LID=" + LID + "&ID=" + ID + "&method="+ method + "&format="+ format ,"preview","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
}

//function LmEx_open_message(ID,MID) {
//	open("main.php?action=message_detail&det=1&get=1&ID=" + ID + "&MID=" + MID + "&LID="+jsvar["LID"]+"" ,"Kalender","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=550");
//}

/* --- Tabellen-Fenster ----------------------------------- */
//function LmEx_open_tab(gtabid,ID) {
//	record = open("main.php?action=gtab_deterg&gtabid=" + gtabid + "&ID=" + ID + "" ,"Record","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=500");
//}

/* --- neues explorer-Fenster ----------------------------------- */
function LmEx_open_newexplorer() {
	LmEx_divclose();
	newexplorer=open("main.php?action=explorer" ,"LIMBAS_Explorer","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_miniexplorer(LID, params, callback) {

    LmEx_divclose();

    if(LID === undefined){
        LID = '';
    }

	//miniexplorer=open("main.php?action=mini_explorer&home_level="+jsvar["LID"] ,"Datei_Browser","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=460,height=320");
	document.getElementById('miniexplorer-manage-content').src = "main.php?action=mini_explorer&home_level="+LID;
	$('#miniexplorer-modal').data('callback',callback).data('params',params).modal('show');

}

function LmEx_handle_miniexplorer(selectedFiles) {

	const $miniExplorer = $('#miniexplorer-modal');
	const callback = $miniExplorer.data('callback');
	const params = $miniExplorer.data('params');

	if (callback !== null && typeof callback === 'function') {
		callback(selectedFiles,params);
	}
    //document.getElementById('miniexplorer-manage-content').src = '';
    //$('#miniexplorer-modal').modal('hide');
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_dublicates() {
	LmEx_divclose();
	dublicates=open("main.php?&action=explorer_dublicates&LID="+jsvar["LID"] ,"Dublicates","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

function LmEx_closeIframeDialog() {
    if($('#LmEx_DetailFrame').hasClass('ui-dialog-content')){
        $('#LmEx_DetailFrame').dialog('destroy');
    }
}

/* --- miniexplorer-Fenster ----------------------------------- */
function LmEx_open_details(evt,ID,LID,gtab_id,form_id,dimension) {
	LmEx_divclose();

		if(dimension){
			var size = dimension.split('x');
			var w_ = (parseFloat(size[0])+80);
			var h_ = (parseFloat(size[1])+100);
		}else{
			var w_ = 800;
			var h_ = 600;
		}

		if(!document.getElementById("LmEx_DetailFrame")){
			$("body").append('<div id="LmEx_DetailFrame" style="position:absolute;display:none;overflow:hidden;"><iframe id="LmEx_DetailOpen" style="width:100%;height:100%;overflow:auto;"></iframe></div>');
		}
		
		if(form_id == '0'){form_id = null;}

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

}

function openExplorerDetailModal(id, path = 'File details') {
	if(!id && LmEx_edit_norm == 'd'){id = LmEx_edit_id;}
	
	const $modal = $('#explorerDetailContainerModal');
	$modal.find('.modal-title').text(path);

	const iframeElement = document.createElement('iframe');
	iframeElement.id = 'LmEx_DetailOpen';
	iframeElement.style.width = '100%';

	//force ovveride modal height due to iframe
	iframeElement.style.height = '75vh';
	iframeElement.style.overflow = 'auto';
	iframeElement.src = 'main.php?action=explorer_detail&LID=null&ID='+id;

	$modal.find('.modal-body').html(iframeElement);
	$modal.modal('show');
}

function explorerDetailPrintFile(fileId) {
	printFiles([fileId]).then((data)=>{
			if (data.success) {
				lmbShowSuccessMsg(data.msg);
			}
			else {
				lmbShowErrorMsg(data.msg);
			}
		},(data)=>lmbShowErrorMsg(data.msg));
}

/**
 * requires printer options present in DOM
 * @param fileIDs - list of files which should get printed
 * @returns {Promise} - .success: bool, .msg:String -- simple message for user feedback, .script:HTML -- eval for automatic user feedback
 */
function printFiles(fileIDs) {
	let data = {
		actid: 'printFile',
		fileIDs: fileIDs,
	};
	data = {...data, ...getPrinterOptions()};

	return new Promise((resolve, reject) => {
		$.ajax({
			method: "POST",
			type: "POST",
			url: "main_dyns.php",
			data: data,
			dataType: 'json',
			success: function (data) {
				resolve(data)
			},
			error: function (error) {
				reject(error)
			}
		});
	})
}

/* --- Tabellen-Fenster ----------------------------------- */
function lmEx_openDataset(gtabid,ID) {
	record = open("main.php?&action=gtab_change&gtabid=" + gtabid + "&ID=" + ID + "" ,"Record","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=500");
}

/* --- Detail-Fenster ----------------------------------- */
function lmEx_dropRelation(ID,LID,RELATION) {
	document.location.href = "main.php?&action=explorer_detail&level="+LID+"&LID="+LID+"&ID=" + ID + "&show_part=origin&drop_relation=" + RELATION;
}


/* ------------------ extended functions -------------------- */

// open upload options
var tmp_el=null;
var tmp_gformid=null;
var tmp_elid=null;
var tmp_line = null;
function lmb_formpic_open(id,name,elid,gformid,line){

    tmp_el = id;
    tmp_gformid = gformid;
    tmp_elid = elid;
    tmp_line = line;

    $('#'+id+'_detail').dialog({
        appendTo: "#form1",
        title: name,
        resizable: true,
        modal: true,
        width:400,
        height:400,
        zIndex: 1000,
        close: function () {
            $('#'+id+'_detail').dialog("destroy");
        }
    });
}

// open upload preview
function lmb_formpic_preview(url,name,size){
    if(size){
        size = size.split('x');
        var width = size[0];
        var height = size[1];
    }else{
        height = undefined;
        width = undefined;
    }

    $("<div><img src='" + url + "'></div>").dialog({
        title: name,
        width: width,
        height: height,
        resizable: false,
        modal: true,
        zIndex: 1000,
        close: function () {
            $('#lmbformpicpreview').dialog("destroy").remove();
        }
    });
}

// explorer select folder
function LmExt_Ex_SelectFolder(LID,gtabid,gfieldid,ID){
	var url = "main_dyns.php";
	var actid = "extRelationFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&ID=" + jsvar["ID"] + "&LID=" + LID;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid);};
	ajaxGet(null,url,actid,null,"mainfunc");
}


// explorer upload Extension
function LmExt_Ex_RelationUploadDelete(gtabid,gfieldid,ID,LID,droprelation,max){
    droprelation = $('#'+droprelation).attr("data-id");

    if(droprelation == 0){return;}
    droprelation = 'd_'+droprelation;

	var url = "main_dyns.php";
    actid = "extRelationFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&LID="+ LID +"&ID=" + ID + "&droprelation=" + droprelation + "&delrelation=1";
	ajaxGet(null,url,actid,null,"ajaxEvalScript");

	lmb_formpicdmove(gtabid+'_'+gfieldid+'_'+ID,'1',max);
}


// explorer manager Extension
function LmExt_Ex_RelationFields(gtabid,gfieldid,viewmode,edittype,ID,orderfield,addrelation,droprelation,delrelation,picshow,formid,addfolder,sub,search){
	var url = "main_dyns.php";

	if(tmp_elid && tmp_el) {
	    $('#'+tmp_el+'_detail').dialog("destroy");
        lmb_formRenderElement(tmp_el, ID, gtabid, tmp_gformid, tmp_elid, tmp_line);
        tmp_el = null;
        tmp_gformid = null;
        tmp_elid = null;
        tmp_line = null;
        return;
    }

	if(!document.getElementById("LID_"+gtabid+"_"+gfieldid)){
	    send_form(1);
	    return;
	}

	var LID = document.getElementById("LID_"+gtabid+"_"+gfieldid).value;
	var textel = document.getElementById("extRelationFields_"+gtabid+"_"+gfieldid+"_"+viewmode);
	if(document.form1.form_id){var gformid = document.form1.form_id.value;}
	if(typeof search == 'object'){var searchval = search.value;var searchfield = search.id;}else{var searchval = '';var searchfield = search;}
	actid = "extRelationFileManager&gtabid=" + gtabid + "&gfieldid=" + gfieldid + "&viewmode=" + viewmode + "&edittype=" + edittype + "&LID="+ LID +"&ID=" + ID + "&orderfield=" + orderfield + "&addrelation=" + addrelation + "&droprelation=" + droprelation + "&delrelation=" + delrelation + "&picshow=" + picshow + "&gformid=" + gformid + "&formid=" + formid + "&addfolder=" + addfolder+ "&viewsub=" + sub+ "&searchval=" + searchval+ "&searchfield=" + searchfield;
	mainfunc = function(result){LmExt_RelationFieldsPost(result,gtabid,gfieldid,viewmode,textel);};
	ajaxGet(null,url,actid,null,"mainfunc");
}

// action of miniexplorer
function LmExt_Ex_LinkMiniEx(result,params){
    resarray = new Array();
    if(result) {
        for (var i in result) {
            resarray.push(result[i]['desc']);
        }
        resstr = resarray.join('-');
        LmExt_Ex_RelationFields(params["gtabid"], params["fieldid"], '', '', params["ID"], '', resstr, '', '', '', '', '', '', '');
    }
}

// unlink files
function LmExt_Ex_UnLinkFiles(gtabid,gfieldid,ID,LID,formid,del){
	filelist = LmEx_selectedFileList(LID);
	var e = 0;
	var f = 0;
	var result = new Array();
	for (var i in filelist){
		if(filelist[i]){
			result[e] = i;
			f++;
		}
		e++;
	}
	if(f > 0){
		if(del){conftxt = jsvar["lng_2153"];}else{conftxt = jsvar["lng_2187"];}
		var de = confirm(conftxt + ' (' + f + ')');
		if(de){
			result = result.join("-");
			LmExt_Ex_RelationFields(gtabid,gfieldid,'','',ID,'','',result,del,'',formid,'','');
		}
	}else{
		alert(jsvar["lng_1717"]);
	}
}

// symbol transparency
function LmExt_Ex_DisplaySymbols(smid,group,ident){
	var smid = document.getElementById('LmEx_Ex_symbol_'+ident+smid);
	if(group){
		var symlist = group.split(",");
		for (var i in symlist){
			document.getElementById('LmEx_Ex_symbol_'+ident+symlist[i]).style.opacity='0.3';
			document.getElementById('LmEx_Ex_symbol_'+ident+symlist[i]).style.filter='Alpha(opacity=30)';
		}
		smid.style.opacity='1';smid.style.filter='Alpha(opacity=100)';
	}else{
		if(smid.style.opacity == '0.3' || smid.style.filter == 'Alpha(opacity=30)'){
			smid.style.opacity='1';smid.style.filter='Alpha(opacity=100)';
		}else{
			smid.style.opacity='0.3';smid.style.filter='Alpha(opacity=30)';
		}
	}
}
