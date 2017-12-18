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


// Ajax replace function
var lmbCollReplaceSource = null;
function limbasCollectiveReplace(evt,el,confirming,gtabid)
{
	if(typeof(confirming) == "undefined"){confirming = '';}

	var relationid = '';
	var actrows = checkActiveRows();
	if(actrows.length > 0){
		if(confirming){
			document.form11.use_records.value = actrows.join(";");
		}
	}else {alert(jsvar["lng_2083"]);return;}
	

	
	actid = "gtabReplace&confirm=" + confirming + "&gtabid=" + gtabid;
	mainfunc = function(result){limbasCollectiveReplacePost(result,evt,el,confirming);}
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc","form11");
}

//Ajax replace function
function limbasCollectiveReplacePost(result,evt,el,confirm)
{
	ajaxEvalScript(result);
	if(!confirm){
		document.getElementById("lmbAjaxContainer").innerHTML = result;
		limbasDivShow(el,'','lmbAjaxContainer');
	}
}

function limbasCollectiveReplaceRefresh(gtabid,gfieldid,ID,gformid,formid){
	if(lmbCollReplaceSource[0]){
		LmExt_RelationFields(null,lmbCollReplaceSource[0],lmbCollReplaceSource[1],'',1,lmbCollReplaceSource[2],'','','','',lmbCollReplaceSource[3],lmbCollReplaceSource[4]);
		lmbCollReplaceSource = null;
	}
}






// Ajax Versionierung Diff
function limbasVersionDiff(gtabid,formid,ID,VID,pdf){
	if(pdf == 1){fct = "versionDiffPdf";}else{fct = "versionDiff";}
	actid = "actid="+fct + "&gtabid=" + gtabid + "&formid=" + formid + "&ID=" + ID + "&VID=" + VID + "&pdf=" + pdf;

	$.ajax({
		type: "GET",
		url: "main_dyns.php",
		async: false,
		data: actid,
		success: function(data){
			document.getElementById("lmbAjaxContainer").innerHTML = data;
			$("#lmbAjaxContainer").css({'position':'relative','left':'0','top':'0'}).dialog({
				resizable: true,
				modal: true,
				title: jsvar['lng_2354'],
				width:'auto'
			});
		}
	});

	return;
}

// change value of header-fields
var lmbSubheaderSelection = new Array();
function limbasSubheaderSelection(el,gtabid,fieldid,typ,formid,ID,form_subel,ajaxpost){

	grel = gtabid+'_'+fieldid;
	
	if(typ == 'TABLE'){
		gtabSetTablePosition(1);
	}
	var ar = document.getElementsByTagName(typ);
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var grpart = cc.id.split("_");
		if(grpart[0] == 'LmbHeaderPart'){
			cc.style.display = 'none';
		}
	}
	if(document.getElementById("LmbHeaderPart_"+grel)){
		var partel = document.getElementById("LmbHeaderPart_"+grel);
		partel.style.display = '';
	}
	
	if(el){
		limbasSetLayoutClassTabs(el,'lmbGtabSubheaderInactive','lmbGtabSubheaderActive');
	}
	
	document.form1.filter_groupheaderKey.value=fieldid;
	
	if(ajaxpost){
		if(!lmbSubheaderSelection[gtabid+'_'+fieldid]){
			actid = "subheaderValue&gtabid=" + gtabid + "&formid=" + formid + "&ID=" + ID + "&fieldid=" + fieldid + "&ID=" + ID + "&form_subel=" + form_subel + "&formaction=" + document.form1.action.value;
			ajaxGet(null,'main_dyns.php',actid,null,'','','LmbHeaderPart_'+grel,1);
		}
		lmbSubheaderSelection[gtabid+'_'+fieldid] = 1;
	}else{
		$(partel).find('table.resizable').each(function(index){
			lmb_syncTable($(this).attr('id'));
		}).removeClass('resizable');
	}
	

	gtabSetTablePosition();
}

// change value of grouping-fields
function limbasGroupingFieldSelection(el,fid,grel){

	var ar = document.getElementsByTagName("SPAN");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var grpart = cc.id.split("_");
		if(grpart[0] == 'LmbGroupingPart' && grpart[1] == fid){
			cc.style.display = 'none';
		}
	}
	document.getElementById("LmbGroupingPart_"+fid+"_"+grel).style.display = '';
	
	limbasSetLayoutClassTabs(el,'lmbGtabTabmenuInactive','lmbGtabTabmenuActive');
}

// Ajax update locking of datasets
function limbasAjxLockData(gtabid,ID,time){
	url = "main_dyns.php";
	actid = "gtabLockData&gtabid=" + gtabid +"&ID="+ID+"&time="+time;
	ajaxGet(null,url,actid,null,"limbasAjxLockDataPost");
}

// Ajax update locking of datasets output
function limbasAjxLockDataPost(result){
        if(result == 'free'){            
                var icon = $('#LmbIconDataLock2');                
                if(icon){
                    icon.removeClass('lmb-forbidden');
                    icon.addClass('lmb-unlocked');			
		}  
                // was before in else if
                if(LockTimer){
			window.clearInterval(LockTimer);
		}
		
	} /*else if(result == 'lock'){
		if(LockTimer){
			window.clearInterval(LockTimer);
		}
	}*/
}

// Ajax user/grouplist fieldtype
var containerElement = null;
function lmb_UGtype(usgr,name,gtabid,fieldid,ID,parameter) {
	actid = "UGtype&gtabid="+gtabid+"&fieldid="+fieldid+"&ID="+ID+"&usgr="+usgr;
	dynfunc = function(result){lmb_UGtypePost(result,gtabid,fieldid);}
	ajaxGet(null,"main_dyns.php",actid,null,"dynfunc");
}

function lmb_UGtypePost(result,gtabid,fieldid){
	document.getElementById("g_"+gtabid+"_"+fieldid+"_dsl").innerHTML = result;
}

function lmb_initTable(elkey){
	var id = 'extRelationTab_'+elkey;
	$('#'+id+'head').remove();
	var head = $('#'+id+' thead');
    $("<table class='lmbfringeGtabBody' border='0' cellspacing='0' cellpadding='0' style='border:none;width:100%;table-layout:fixed' id='"+id+"head'>").append(head.html()).insertBefore("#extRelationFieldsTab_"+elkey);
    head.remove();

    document.getElementById("extRelationFieldsTab_"+elkey).style.height = document.getElementById("extRelationFieldsTab_"+elkey).getAttribute("lmbHeight") - document.getElementById(id+'head').offsetHeight - (document.getElementById(id+'menu') == null ? 0 : document.getElementById(id+'menu').offsetHeight);
    lmb_syncTable(id);
}

function lmb_syncTable(id){
	$('#'+id+" tbody tr:first td").each(function(idx) {
		$('#'+id+"head tr:first th:nth-child(" + (idx+1) + ")").width($(this).width());
	});
	$('#'+id+'head').width($('#'+id).width());
}







// ---- File-Aktivierung -----
function lmb_activate_file(evt,id,filename,lid,norm){

	if(evt.ctrlKey && evt.shiftKey && norm == 'd'){
		LmEx_file_detail(id);
	}

	var prev_id = LmEx_edit_id;
	var filestatus = "filestatus_"+norm+"_"+lid+"_"+id;
	var elline = "elline_"+norm+"_"+lid+"_"+id
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











//----------------- Formular senden -------------------
// form ID ; ajax Post ; container ID for checking needed fields 
var sendFormTimeout = null;
var dyns_time = null;
function send_form(id,ajax,need,wclose) {
	var needok = needfield(need);
	set_focus();
	
	window.setTimeout("clear_send_form()", 300);
	if(sendFormTimeout){return;}
	sendFormTimeout = 1;
	if(id == 2 && !document.form_2){id =1;}
	
	if(ajax){
		clearTimeout(dyns_time);
		lmbAjax_postHistoryResult("form"+id);
	}else if(needok){
		alert(jsvar["lng_1509"]+"\n\n"+needok);
	}else{
		document.body.style.cursor = 'wait';
		getScrollPos();
		divclose();
		if(id == 1){document.form1.change_ok.value='1';}

		// ---- Elternelement von neuen Verkn. Datensatz ----------
		if(document.form1.verkn_addfrom.value && (document.form1.action.value == 'gtab_change' || document.form1.action.value == 'gtab_deterg')){
			var addfrom = new Array();
			var arrel = new Array();
			addfrom = document.form1.verkn_addfrom.value.split(";");
			arrel = addfrom[addfrom.length-1].split(",");
			document.form1.gtabid.value = arrel[0];
			document.form1.ID.value = arrel[1];
			document.form1.form_id.value = arrel[2];
			addfrom.pop();
			document.form1.verknpf.value = "";
			document.form1.verkn_addfrom.value = addfrom.join(";");
		}
		
		if(wclose){document.form1.wind_force_close.value = 1;}
		document.getElementsByName("form"+id)[0].submit();
	}
}



/*---------------- Tabulator-Element für Formular anzeigen ---------------------*/
var lmbTabulatorKey = new Array();
function lmbSwitchTabulator(el,mainel,elkey,vert){
	
	if(vert){
		var hel = el.parentNode.parentNode.parentNode;
		var stpos = vert;
	}else{
		var hel = el.parentNode;
		var stpos = "";
	}
	
	if(!hel){return;}
	for(var i=0; i<hel.childNodes.length; i++){
		if(vert){
			divel =  hel.childNodes[i].firstChild.firstChild;
		}else{
			divel =  hel.childNodes[i];
		}
		
		var mt = "LmbTabulatorHeader";
		if(!divel.id || divel.id.substr(0,mt.length)!=mt) continue;
		var dsp = "none";
		var cl = "lmbGtabTabulator"+stpos+"Inactive";
		var nodel = divel.id.replace(mt,"LmbTabulatorItem");
		if(divel.id==el.id){
			dsp = "";
			cl="lmbGtabTabulator"+stpos+"Active";
		}
		divel.className = cl;
		document.getElementById(nodel).style.display = dsp;
	}

	var tabKey = new Array();
	lmbTabulatorKey[mainel] = elkey;
	var e = 0;
	for (var i in lmbTabulatorKey){
		tabKey[e] = i+"_"+lmbTabulatorKey[i];
		e++;
	}
	document.form1.filter_tabulatorKey.value = tabKey.join(";");
}

//----------------- springe zu Elternelement von neuen Verkn. Datensatz -------------------
function jump_to_addfrom(KEY) {
	if(document.form1.verkn_addfrom){
		var addfrom = new Array();
		var new_addfrom = new Array();
		addfrom = document.form1.verkn_addfrom.value.split(";");
		for(var i=0; i<=KEY; i++){
			new_addfrom[i] = addfrom[i];
		}

		document.form1.verkn_addfrom.value = new_addfrom.join(";");
		send_form(1);
	}
}

//----------------- Neuen Datensatz auf Inhalt prüfen -------------------
function check_new() {
	if(jsvar){
	if(jsvar["action"] == "gtab_neu"){
		if(document.form1.history_fields.value == ''){
			alert(jsvar["lng_1318"]);
		}
	}}
}

//----------------- Änderungen speichern falls vergessen -------------------
function check_change() {
	if(jsvar){
	if(!document.form1){return;}
	if(document.form1.history_fields.value != '' && document.form1.change_ok.value != 1){
		var history_fields = document.form1.history_fields.value.split(";");
		var cf = "";
	 	var ft = new Array();
		for (var i in history_fields){
			var history_part = history_fields[i].split(",");
			if(history_part[0] && history_part[1]){
				var key = history_part[0]+"_"+history_part[1];
				if(document.getElementsByName('g_'+history_part[0]+'_'+history_part[1])[0]){
					ft[key] = document.getElementsByName('g_'+history_part[0]+'_'+history_part[1])[0].title;
				}else if(document.getElementsByName('g_'+history_part[0]+'_'+history_part[1]+'[0]')[0]){
					ft[key] = document.getElementsByName('g_'+history_part[0]+'_'+history_part[1]+'[0]')[0].title;
				}
			}
		}
		for (var i in ft){
			if(ft[i] != 'undefined'){
				var cf = cf+"\n-> "+ft[i];
			}
		}
		var confirm_text = jsvar["lng_1424"]+"\n"+cf;
		var save = confirm(confirm_text);
		if(save){
			document.form1.change_ok.value='1'; document.form1.action.value='gtab_change'; send_form('1');
		}
	}}
}

//----------------- Infobox Feldtyp -------------------
function fieldtype(data_type,form_name,index) {
	if(document.getElementsByName(form_name)[0]){
	alert(document.getElementsByName(form_name)[0].title+': ('+FORMAT[data_type]+')\n'+index);
	}
}

//----------------- List-Frame neu laden -------------------
function reload_frame(frame) {
	if(eval("parent."+frame)){
		eval("parent."+frame+".window.send_form(1);");
	}
}

//----------------- Bearbeitungsansicht -------------------
function view_change() {
	if(parent.detail){document.form1.target='detail';}else{document.form1.target='_self';};
	document.form1.action.value='gtab_change';
	send_form(1);
	document.form1.action.value='gtab_erg';
}

//----------------- Detailansicht -------------------
function view_detail() {
	if(parent.detail){document.form1.target='detail';}else{document.form1.target='_self';};
	document.form1.action.value='gtab_deterg';
	send_form(1);
	document.form1.action.value='gtab_erg';
}

//----------------- neuen Datensatz anlegen -------------------
function create_new() {
	neu = confirm(jsvar["lng_164"]+':'+jsvar["tablename"]+'\n'+jsvar["lng_24"]);
	if(neu){
		document.form1.action.value='gtab_neu';
		send_form('1');
	}
}

//----------------- Fensterposition setzen -------------------
function getScrollPos() {
	if(document.getElementById("GtabTableBody")){
		document.form1.posy.value=document.getElementById("GtabTableBody").scrollTop;
	}
}

// set table position
function gtabSetTablePosition(reset,scrolltoY){

        if(!document.getElementById("GtabTableBody")){return false;}
	
	// set window title
	var mfn = "g_"+document.form1.gtabid.value+"_"+jsvar["mainfield"];
	if(document.getElementsByName(mfn)[0]){
		document.title = document.getElementsByName(mfn)[0].value;
	}        

        lmb_setAutoHeight(document.getElementById("GtabTableBody"), 20 /* px margin-bottom */, reset);

        document.getElementById("GtabTableBody").scrollTop=scrolltoY;
	
	if(!parent.main && document.form1.lmbSbmClose){document.form1.lmbSbmClose.style.display='';}
}


/*----------------- Lösch/Versteck/Version-Funktion -------------------*/
function userecord(typ) {
	ID = document.form1.ID.value;
	gtabid = document.form1.gtabid.value;
	if(typ == 'delete'){var mess = jsvar["lng_84"];if(document.getElementById("filter_force_delete") && document.getElementById("filter_force_delete").checked == true){mess += '\n\n'+jsvar["lng_2794"];}}
	if(typ == 'hide'){var mess = jsvar["lng_49"];}
	if(typ == 'unhide'){var mess = jsvar["lng_1311"];}
	if(typ == 'copy'){var mess = jsvar["lng_2156"];}
	if(typ == 'unlock'){var mess = jsvar["lng_2433"];}
	if(typ == 'lock'){var dele = 1;}
	
	if(typ == 'versioning'){
		var vs = document.getElementById("versionSelection");
		if(!vs){
			var vac = 1;
		}else if(vs.name == "versionSelection_green"){
			var vac = 1;
		}else if(vs.name == "versionSelection_red"){
			var vac = 0;
		}
		
		if(vac){
			var mess = jsvar["lng_2146"];
			dele=prompt(mess,' ');
			if(dele){document.form1.versdesc.value=dele;}
			mess = null;
		}else{
			alert(jsvar["lng_2147"]);
			return;
		}
	}
	
	if(mess){access=confirm(mess);}else{access = 1;}
	if(access) {
		document.form1.use_typ.value = typ;
		document.form1.use_record.value = ID+"_"+gtabid;
		send_form('1');
	}
}

/* --- MemoKeypress ----------------------------------- */
function memokeypress(evt,el,searchval) {
	if(evt.ctrlKey && evt.shiftKey){
		if(evt.keyCode == 13){
			var val = jsvar['userfullname'] + " (" + jsvar['datum'] + ") : ";
			len = el.value.length;
			if(len > 0){
				el.value = el.value + '\n' + val;
			}else{
				el.value = el.value + val;
			}
		}
	}else if(evt.keyCode == 18){
		setSelectedText(evt,el,searchval);
	}
}

/* --- SendKeypress ----------------------------------- */
function sendkeydown(evt) {
	if(evt.ctrlKey && evt.shiftKey){
		// ändern
		if(evt.keyCode == 83){
			send_form(1);
		}
		// vorheriger
		else if(evt.keyCode == 88){
			document.form1.scrollto.value='next';
			send_form(1);
		}
		// nächster
		else if(evt.keyCode == 89){
			document.form1.scrollto.value='prev';
			send_form(1);
		}
		// anlegen
		else if(evt.keyCode == 78){
			create_new();
		}
		// liste
		else if(evt.keyCode == 76){
			document.form1.action.value='gtab_erg';send_form('1');
		}
		return true;
	}
}

/* --- view_all_verkn ----------------------------------- */
function sendverknpf(KEY) {
	document.form1.view_all_verkn.value = document.form1.view_all_verkn.value + ";" + KEY;
	window.focus();
	getScrollPos();
	document.form1.lmbSbm.click();
}

/* --- show_zoom ----------------------------------- */
function show_zoom(evt,el) {
	//if(evt.ctrlKey){
		limbasDivShow('',evt,"lmbZoomContainer");
		document.getElementById("lmbZoomContainer").innerHTML = el.firstChild.nodeValue;
	//}
}


/* --- show_Tabs_info ----------------------------------- */
//function showTabsCount(elname,count) {
	//elname = "tabs_" + elname;
	//if(document.getElementById(elname)){
		//document.getElementById(elname).title = count;
		//value = document.getElementById(elname).firstChild.nodeValue;
		//document.getElementById(elname).firstChild.nodeValue = value + " (" + count + ")";
	//}
//}

/* --- Lock / INUSE-Function ----------------------------------- */
var LockTimer;
function inusetime(gtabid,ID) {
	if(jsvar["lockable"] && (jsvar["action"] == 'gtab_change' || jsvar["action"] == 'gtab_deterg') && ID){
		locktime = ((jsvar["inusetime"] * 1000 * 60) - 3000);
		LockTimer = window.setInterval("limbasAjxLockData("+gtabid+","+ID+",0)", locktime);
	}
}

// ---- Suchkriterien markieren ----------
var select_text = new Array();
function setSelectedText(evt,el,val) {
	if(val && el){
		if(!select_text[el.id]){select_text[el] = 0;};
		var posStart = el.value.indexOf(val,select_text[el.id]);
		var posEnd = val.length + posStart;
		select_text[el.id] = posEnd;

		if(posStart > 0){
		el.focus();
		// Mozilla ---
		if (el.setSelectionRange) {
			el.setSelectionRange(posStart, posEnd);
		}
		// IE ---
		else if (el.createTextRange) {
			var range = el.createTextRange();
			range.collapse(true);
			range.moveEnd('character', posEnd);
			range.moveStart('character', posStart);
			range.select();
		}
		}
	}
}


/*----------------- User Rechte -------------------*/
function limbasGetGtabRules(gtabid,el) {
	if(jsvar["ID"]){
		limbasDivShow(el,'limbasDivMenuExtras','lmbAjaxContainer');
		limbasAjaxGtabRules(gtabid,jsvar["ID"],'','lmbAjaxContainer');
	}
}



// Währungsfunktionen
function limbasSetNewCurrency(formname,value,crurency,fieldid){
	var formcurrency = formname+"_CURRENCY";
	document.form1.elements[formname].value = value+' '+crurency;
	//document.form1.elements[formcurrency].value = crurency;
	checktyp(30,formname,'',fieldid,jsvar["gtabid"],value,jsvar["ID"]);
}

function limbasChangeCurrency(el,value,curr,formname,fieldid){
	var infoval = "";
	
	if(!value){return;}
	limbasDivShow(el,null,'lmbAjaxContainer','','',1);
	value = value.replace(",",".");

	for (var i = 0; i < jsvar["currency_code"].length; ++i) {
		if(curr == jsvar["currency_code"][i]){
			var currency_id = i;
		}
	}
	for (var i = 0; i < jsvar["currency_code"].length; ++i) {
		numbval = value.split(' ');
		var calc = limbasRoundMath((1/jsvar["currency_unit"][i]) * jsvar["currency_unit"][currency_id] * numbval[0]);
		infoval = infoval+"<tr OnClick=\"limbasSetNewCurrency('"+formname+"','"+calc+"','"+jsvar["currency_code"][i]+"','"+fieldid+"')\"><td>"+jsvar["currency_code"][i]+"</td><td>"+calc+"</td></tr>";
	}
	document.getElementById("lmbAjaxContainer").innerHTML = "<table OnClick=\"this.parentNode.innerHTML=''\" bgcolor=\""+jsvar['WEB1']+"\" style=\"border:1px solid black;cursor:pointer\"><tr bgcolor=\""+jsvar["WEB6"]+"\"><td colspan=\"2\" style=\"height:10px;\"></td></tr>"+infoval+"</table>";
}




// --- jsGraphics -----------------------------------
function js_line(jg,x2,y2,xy,col,he){
	if(xy == 'true'){var y1 = y2; y2 = 1;}else{var y1 = 1;}
	eval(jg+".setStroke("+parseInt(he)+");");
	eval(jg+".setColor('"+col+"');");
	eval(jg+".drawLine(1, "+y1+", "+x2+", "+y2+");");
	eval(jg+".paint();");
}
function js_ellipse(jg,x,y,col,he){
	eval(jg+".setStroke("+parseInt(he)+");");
	eval(jg+".setColor('"+col+"');");
	eval(jg+".drawEllipse(1, 1, "+x+", "+y+");");
	eval(jg+".paint();");
}
function js_clear(jg){
	eval(jg+".clear();");
}

var nr = 0;
function lmb_formpicdmove(id,way,max){
	max = max-1;
	if(way == 1){
		nr = (nr + 1);
	}else if(way == 2){
		nr = (nr - 1);
	}else if(way == 3){
		nr = 0;
	}else if(way == 4){
		nr = max;
	}
	if(nr > max){
		nr = 0;
	}else if(nr < 0){
		 nr = max;
	}
	
	
	$("#formpicname_"+id).hide();

	eval("var pic = formpicsn_"+id+"["+nr+"].split('#');");
	eval("document.formpicname_"+id+".src = pic[0];");
	document.getElementById('formpicid_'+id).value = pic[1];
	
	$("#formpicname_"+id).show('slow');
	
}

function lmb_formpicdelete(fel,fp){
	
	if(confirm('delete file?')){
		document.getElementsByName(fel+'_delete')[0].value += document.getElementById('formpicid_'+fp).value;
		send_form(1);
	}
}


// --------------- Datei Funktionen ---------------------
function multiupload(NR,LID){
	for (var i = 1; i <= 10; i++) {
		el = "f_"+LID+"_"+i;
		if(i <= NR){
			document.getElementById(el).style.display = '';
		}else{
			document.getElementById(el).style.display = 'none';
		}
	}
}


// --------------- Layer Funktionen ---------------------


// --- Plaziere Context-Menüs auf Navigation ----------
function setxypos_n(evt,el,menu) {
	if(jsvar["is_formlist"]){var offs = el.offsetParent.offsetHeight;}else{var offs = 0;}
	document.getElementById(menu).style.left = el.offsetLeft + document.getElementById('global_border').offsetLeft;
	document.getElementById(menu).style.top = offs + el.offsetTop + document.getElementById('global_border').offsetTop + el.offsetHeight + 8;
}

// --- Plaziere Context-Menüs auf Cursorposition ----------
function setCenterPos(el) {

	var elt = document.getElementById(el);

	if (self.innerWidth)
	{
		frameWidth = self.innerWidth;
		frameHeight = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientWidth)
	{
		frameWidth = document.documentElement.clientWidth;
		frameHeight = document.documentElement.clientHeight;
	}
	else if (document.body)
	{
		frameWidth = document.body.clientWidth;
		frameHeight = document.body.clientHeight;
	}


	eltWidth = elt.style.width
	if(eltWidth.indexOf("px")>0)
		eltWidth=eltWidth.substring(0,eltWidth.length-2);



	elt.style.left=(frameWidth-eltWidth)/2;
	elt.style.top=(frameHeight-elt.style.height)/2;

}

//----------------- Schließe alle Context-Menüs -------------------
var activ_menu = null;

function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

// --- Haupt-Werkzeug -----------------------------------
function limbasDivShowContext(evt,el,ID,TABID,ERSTDATUM,EDITDATUM,ERSTUSER,EDITUSER) {
	divclose();
	//activ_menu = 1;
	document.getElementById("lmbInfoCreate").innerHTML = ERSTUSER+"\n"+ERSTDATUM;
	document.getElementById("lmbInfoEdit").innerHTML = EDITUSER+"\n"+EDITDATUM;
	limbasDivShow(el,'','limbasDivMenuContext');
}


/*----------------- Zeige R-Verknüpfung -------------------*/
function show_verknpf(value,ID) {
	var verknpf = value.split(";");
	divclose();
	document.location.href="main.php?&action="+jsvar['action']+"&verkn_tabid="+verknpf[0]+"&verkn_fieldid="+verknpf[1]+"&verkn_ID="+ID+"&verknpf=2";
}




// ---------- Popup Fenster ---------------


// --- Fenster für alten Kalender ------------------------------------------
function newwin3(TAB,FIELD,ID) {
	kalender = open("main.php?action=kalender&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&dat_id=" + ID + "&gtabid=" + TAB + "&field_id=" + FIELD + "" ,"Kalender","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=600");
}
// --- Explorer-Detail-Fenster -----------------------------------
function newwin4(ID,LEVEL) {
	divclose();
	detail = open("main.php?action=explorer_detail&level=" + LEVEL + "&ID=" + ID + "" ,"Info","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=650");
}
// --- Fenster Bericht -----------------------------------------------
function newwin8(ID,TAB_ID,REP_ID,MEDIUM,PRINT) {
	var UNIQUE = Date.parse(Date());
	bericht = open("main.php?&action=report&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ID=" + ID + "&gtabid=" + TAB_ID + "&report_id=" + REP_ID + "&report_output=" + PRINT + "&report_medium=" + MEDIUM + "&unique=" + UNIQUE + ".pdf" ,"Bericht","toolbar=1,location=0,status=1,menubar=1,scrollbars=1,resizable=1,width=650,height=880");
}

// --- Fenster Long-Feld editieren -----------------------------------
function newwin9(FIELDID,TABID,ID) {
	editlong = open("main.php?action=edit_long&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ID=" + ID + "&field_id=" + FIELDID + "&gtabid=" + TABID + "" ,"Texteditor","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=650,height=800");
}

// explorer
function newwin12(LID,ID,gtabid,f_fieldid){
	divclose();
	newexplorer=open("main.php?action=explorer_main&LID=" + LID + "&ID=" + ID + "&gtabid=" + gtabid + "&f_fieldid=" + f_fieldid ,"LIMBAS_Explorer","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

// download
function newwin13(ID){
	divclose();
	newexplorer=open("main.php?action=download&ID=" + ID ,"File_Download","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=500");
}

// --- Fenster Calender  -----------------------------------
function newwin14(TABID,V_TABID,V_FIELDID,V_ID) {
	calendarrelation = open("main.php?action=kalender&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] + "&ctyp=pro&verkn_ID=" + V_ID + "&gtabid=" + TABID + "&verkn_tabid=" + V_TABID + "&verkn_fieldid=" + V_FIELDID + "" ,"Calendar"+TABID,"toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=600");
}

/*----------------- Bericht -------------------*/
function report_archiv() {
	newwin12(jsvar["reportlevel"],document.form1.ID.value,document.form1.gtabid.value,0);
}

/*----------------- Diagramm -------------------*/
function lmbDiagramm(diag_id,gtabid,ID) {
	diagramm = open("main.php?action=diag_erg&gtabid=" + gtabid + "&diag_id="+ diag_id + "&data_id=" + ID ,"Diagramm","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=600,height=400");
}