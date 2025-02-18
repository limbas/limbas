/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



limbasUserSelectPostValid = "";

var sendFormTimeout = null;

/* ---------------- User ------------------ */
function f_2(PARAMETER,FUNCID) {
document.form1.action.value = PARAMETER;
document.form1.submit();
}


// --- Formular senden ----------------------------------
function send_form(id,ajax,b,f,h,s) {

	window.setTimeout("clear_send_form()", 300);
	if(sendFormTimeout){return;}
	sendFormTimeout = 1;
	selected_rows = new Array(); // reset selected_rows;

	if(typeof(b) == "undefined"){b = 1;}
	if(typeof(f) == "undefined"){f = 1;}
	if(typeof(h) == "undefined"){h = 0;}
	if(typeof(s) == "undefined"){s = 0;}

	// if grouping view
	if(document.getElementById('GtabTableGroup') || (document.form1.view_version_status && document.form1.view_version_status.value)){
		ajax = 0;
	}

	if(rowsize){tdsize();}
	divclose();

    listmode = [b,f,h,s];

    // no changes // no validate
    if(!jsvar['gtab_validate_'+document.form1.gtabid.value] || !document.form1.history_fields.value){
        confirm_form(id,ajax,null,listmode);
        return;
    }

    confirm_submit = function(){
        confirm_form(id,ajax,null,listmode);
    }

    var query = ajaxFormToURL('form' + id, 'gtabValidate');
    if(validate_request('update',query)){
        confirm_submit();
    }

}

function confirm_form(id,ajax,need_,listmode,typ) {

    // delete, versioning, hide
    if(typ && typ != 'update'){
        var actrows = checkActiveRows();
        document.form1.use_typ.value = typ;
        document.form1.use_record.value = actrows.join(";");
		send_form('1');
        return;
    }

	if(ajax == 1 && !jsvar["form_id"]){
		clearTimeout(dyns_time);
		lmbAjax_postHistoryResult("form"+id);
		document.form1.history_fields.value='';
		document.form1.history_search.value = '';
	}else if(ajax == 2 && !jsvar["form_id"]){
		clearTimeout(dyns_time);
        if(typeof listmode === 'string'){
            listmode = listmode.split("_");
        }
		lmbAjax_resultGtab("form1",listmode[0],listmode[1],listmode[2],listmode[3]);
		document.form1.history_fields.value='';
		document.form1.history_search.value = '';
	}else{
		if(parent.detail && document.form1.action.value != "gtab_erg"){
            document.form1.target='detail';
        }else{
            document.form1.target='_self';
        };
		//document.form1.target='_self';
		//document.form1.action.value='gtab_erg'; // todo ?? ist für Detailformular in Kalender & Kanban blöd
		document.getElementsByName("form"+id)[0].submit();
	}
}

// ############# Ajax functions #################

//----------------- get result count of rows -------------------
function lmbGetResultlimit() {
	ajaxGet(null,'main_dyns.php','getRowCount',null,'lmbGetResultlimitPost','form2');
}

//----------------- show result count of rows -------------------
function lmbGetResultlimitPost(result) {
	alert(jsvar["lng_93"]+' '+result+' | '+jsvar["lng_2114"]+' '+jsvar["resultspace"]+'\n'+jsvar["lng_1868"]+jsvar["lng_1913"]);
}

/* --- ajax_11_a ----------------------------------- */
function ajax_22_a(evt,old_value,id,form_name,fieldid,gtabid,ID,dash,act) {
	let new_value = [];
	const new_zvalue = [];
	let add = 1;
	var value = document.form1[form_name].value;
	if(value){new_value = value.split(';');}

	if(act == 1){
		for (let z in new_value){
			if(new_value[z] == id){
				add = 0;
			}
		}
		if(add){
			if(!evt.ctrlKey){dynsClose(form_name+'_dsl');}
			if(id){new_value.push(id);}
			document.getElementById(form_name+'_dse').innerHTML += '<SPAN STYLE=\'color:green;\'>' + dash + old_value+'</SPAN>';
			checktyp('32','',form_name,fieldid,gtabid,' ',ID);
		}
	}else{
		for (let z in new_value){
			if(new_value[z] != id){
				new_zvalue.push(new_value[z]);
			}
		}
		new_value = new_zvalue;
		checktyp('32','',form_name,fieldid,gtabid,' ',ID);
	}
	document.form1[form_name].value = new_value.join(';');
}


// --- Link-Popup List ----------
function ajax_linksPopup(evt,el,gtabid,datid,previd,prevgtabid,prevfieldid,ebene,subtab,pophist){
	if(evt.shiftKey){
		document.form1.popup_links.value = gtabid+","+previd;
		send_form(1);
	}else{
		ajax_linkPopup(el,gtabid,datid,previd,prevgtabid,prevfieldid,ebene,subtab,pophist);
	}

	return false;
}



var savepops = new Array();
// --- Link-Popup ----------
function ajax_linkPopup(el,gtabid,datid,previd,prevgtabid,prevfieldid,ebene,subtab,pophist){

	if(subtab && previd > 0){
		tabname = 'lmbGlistBodyTab_'+prevgtabid+'_'+prevfieldid+'_'+previd;
	}else{
		tabname = "lmbGlistBodyTab";
	}

	var table = document.getElementById(tabname);
	var na = gtabid+"_"+datid+"_"+previd;
	var tr = document.getElementById("subtab_"+gtabid+"_"+datid+"_"+previd);
	var td = document.getElementById("subtd_"+gtabid+"_"+datid+"_"+previd);
	var pi = document.getElementById("popicon_"+gtabid+"_"+datid+"_"+previd);

	// close relation
	if(tr){
		var rowindex = el.parentNode.parentNode.rowIndex;
		var row = table.deleteRow(rowindex+1);
                pi.classList.remove("lmb-small-arrow-down");
		pi.classList.add("lmb-icon", "lmb-small-arrow-right", "u-color-4");
		savepops[na] = 0;
		savepopups();
		gtabSetTablePosition();

	// open relation
	}else{
		var rowindex = el.parentNode.parentNode.rowIndex;
		var row = table.insertRow(rowindex+1);
		row.id = "subtab_"+gtabid+"_"+datid+"_"+previd;
		var cell = row.insertCell(0);
		cell.id = "subtd_"+gtabid+"_"+datid+"_"+previd;
		cell.colspan = table.rows[0].cells.length;

		var ischecked = 0;
		var ar = document.getElementsByTagName("input");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			if(cc.name.substr(0,5) == 'popg_'){
				if(cc.checked){
					ischecked = 1;
				}
			}
		}
		if(ischecked){
			// check already displayed popmenus
			var cc = null;
			var bzm = 0;
			var ph = new Array();
			var ar = document.getElementsByTagName("select");
			for (var i = ar.length; i > 0;) {
				cc = ar[--i];
				var ln = cc.name.split("_");
				if(ln[0] == "popg"){
					ph[bzm] = ln[1];
					bzm = bzm + 1;
				}
			}
			var pophistg  = ph.join('-');
                        pi.classList.remove("lmb-small-arrow-right");
                        pi.classList.add("lmb-icon", "lmb-small-arrow-down", "u-color-4");
			mainfunc = function(result){ajax_linkPopupPost(result,cell,na);};
			var actid = "linkPopup&gtabid=" + gtabid + "&datid=" + datid + "&previd=" + previd + "&ebene=" + ebene + "&pop_choice=" + document.form1.pop_choice.value + "&pophist=" + pophist + "&pophistg=" + pophistg;
			ajaxGet(null,"main_dyns.php",actid,null,"mainfunc");
		}else{
			alert(jsvar["lng_2138"]);
		}


	}

	return true;

}

function ajax_linkPopupPost(result,cell,na){

	//if(result.length > 150){
		cell.innerHTML = result;
		savepops[na] = 1;
		savepopups();
		//document.getElementById('messages').innerHTML = "";
	//}else{
	//	document.getElementById('messages').innerHTML = "<span style='color:red'>: "+jsvar["lng_2139"]+"</span>";
	//}
	gtabSetTablePosition();
}

function savepopups(){
	var saval = '';
	for (var e in savepops){
		var saval = saval + e + "_" + savepops[e] + "|";
	}
	document.form1.filter_popups.value = saval;
}

// Ajax table request
function lmbAjax_resultGtab(form,body,footer,header,search){
	var actid = "gresultGtab&view_body="+body+"&view_search="+search+"&view_header="+header+"&view_footer="+footer;
	ajaxGet(null,"main_dyns.php",actid,null,"lmbAjax_resultGtabPost",form,null,1);
}

// Ajax Preview output
function lmbAjax_resultGtabPost(result){
	ajaxEvalScript(result);
	if(result){
		result = result.split("#LMBSPLIT#");
		if(result[0] && result[0].trim() && document.getElementById("GtabTableSearch")){document.getElementById("GtabTableSearch").innerHTML = result[0];}
		if(result[1] && result[1].trim() && document.getElementById("GtabTableHeader")){document.getElementById("GtabTableHeader").innerHTML = result[1];}
		// if for adding new tab rows while scrolling
		if(result[2] && result[2].trim()){
		    if(pagination_s) {
		        n1 = result[2].search('LMB_STARTTAB') + 13;
		        n2 = result[2].search('LMB_ENDTAB') - 9;
                result[2] = result[2].substring(n1, n2);

                $('#lmbGlistBodyTab').append(result[2]);
				$('#GtabTableBody').find('[data-decrypt]').on('click', lmbAjax_decryptField);
            }else{
		        document.getElementById("GtabTableBody").innerHTML = result[2];
				$('#GtabTableBody').find('[data-decrypt]').on('click', lmbAjax_decryptField);
		    }
		}

		if(result[3] && result[3].trim() && document.getElementById("GtabTableFooter")){document.getElementById("GtabTableFooter").innerHTML = result[3];}
	}

	pagination_h = null;
	gtabSetTablePosition();
}





// --- Ajax reminder Filter ----
function limbasDivShowReminderFilter(evt,el) {
	activ_menu = 1;
	if(el){
		limbasDivShow(el,'limbasDivMenuAnsicht','limbasDivMenuReminder');
	}
}

//reminder Filter post
function lmb_reminderPost(result){
	document.getElementById("lmbAjaxContainer").innerHTML = result;
}


// gtab_replace function
function limbasCollectiveReplace(evt,el,confirming)
{
	if(typeof(confirming) == "undefined"){confirming = '';}

	if(confirming){
	count =	countofActiveRows();
	if(count > 0){
		var actrows = checkActiveRows();
		if(actrows.length > 0){
			document.form11.use_records.value = actrows.join(";");
		}
	}}

	var query = ajaxFormToURL('form2');
	actid = "gtabReplace&confirm=" + confirming + "&fieldid="+lmbCollReplaceField + "&" +query;
	mainfunc = function(result){limbasCollectiveReplacePost(result,evt,el,confirming);};
	ajaxGet(null,"main_dyns.php",actid,null,"mainfunc","form11");
}

function limbasCollectiveReplacePost(result,evt,el,confirm)
{
	ajaxEvalScript(result);
	if(!confirm){
		document.getElementById("limbasAjaxGtabContainer").innerHTML = result;
		if(evt){limbasDivShow(el,'limbasDivMenuBearbeiten','limbasAjaxGtabContainer');}
	}
}

function limbasCollectiveReplaceRefresh(){
	send_form(1,2);
}

// ------------------ sanapshot -----------------------------

//function limbasSnapshotWorkflow()
//{
//	var parameters = new Array("snap_id","limbasSnapshotWorkflowId");
//	ajaxGet(0,"main_dyns.php","startWorkflowWithSnapshot",parameters,"limbasSnapshotWorkflowPost");
//}
//function limbasSnapshotWorkflowPost(string)
//{
//	divclose();
//	if(string.length!=0)
//		alert(jsvar["lng_56"]+":" + string);
//	else
//	{
//		alert(jsvar["lng_2082"]);
//	}
//}

//function limbasWorkflowStartWithTable(wfl_id)
//{
//	var parameters = new Array("gtabid","snap_id","limbaswfl_id");
//	document.getElementsByName("limbaswfl_id")[0].value = wfl_id;
//	ajaxGet(0,"main_dyns.php","startWorkflowWithTable",parameters,"limbasWorkflowStartWithTablePost");
//}


function limbasSnapshotManage(gtabid)
{
    $('#snapfilter-menu').modal('hide');
    document.getElementById('snapfilter-manage-content').src = 'main.php?action=user_snapshot&gtabfilter='+gtabid;
    $('#snapfilter-manage').modal('show');
}

function limbasSnapshotSaveas(gtabid,name)
{
    var parameters = 'snapshotSaveas&gtabid='+gtabid+'&limbasSnapshotName='+name;
	ajaxGet(0,"main_dyns.php",parameters,"","limbasSnapshotSaveasPost");
}
function limbasSnapshotSaveasPost(string)
{
	divclose();
	if(isNaN(string)){
		showAlert(jsvar["lng_56"]+": " + string);
	}else{
		showAlert(jsvar["lng_2006"]);
		refreshMenuOnSnapshot();
		document.form1.snap_id.value=string;
		send_form(1);
	}
}

function limbasSnapshotDisplaySaveas()
{
	//divclose();
	activ_menu = 1;
	document.getElementById("limbasDivSnapshotSaveas").style.left=200;
	document.getElementById("limbasDivSnapshotSaveas").style.top=150;
	document.getElementById("limbasDivSnapshotSaveas").style.visibility='visible';
}

function limbasSnapshotSave()
{
	//conf = confirm(jsvar["lng_2009"]);
	//if(conf)
	//{
		var parameters = new Array("snap_id","gtabid","tab_group");
		ajaxGet(0,"main_dyns.php","snapshotSave",parameters,"limbasSnapshotSavePost");
	//}
	//else{
		divclose();
	//}
}
function limbasSnapshotSavePost(string)
{
	divclose();
	if(string.length!=0)
		alert(jsvar["lng_56"]+": " + string);
	else
	{
		alert(jsvar["lng_2006"]);
	}
}

function limbasSnapshotDelete()
{
	conf = confirm(jsvar["lng_2010"]);
	if(conf)
	{
		var parameters = new Array("snap_id","gtabid");
		ajaxGet(0,"main_dyns.php","snapshotDelete",parameters,"limbasSnapshotDeletePost");
	}
	else{
		divclose();
	}
}
function limbasSnapshotDeletePost(string)
{
	divclose();
	if(string.length!=0)
		alert(jsvar["lng_56"]+":" + string);
	else
	{
		alert(jsvar["lng_2007"]);
		refreshMenuOnSnapshot();
		document.form1.snap_id.value='';
		send_form(1);
	}
}

function limbasSnapshotShare(el,snap_id,destUser,del,edit,drop)
{
	if(typeof(del) == "undefined"){del = 0;}
	if(typeof(edit) == "undefined"){edit = 0;}
	if(typeof(drop) == "undefined"){drop = 0;}

	ajaxGet('','main_dyns.php','showUserGroups&gtabid='+snap_id+'&usefunction=lmbSnapShareSelect&destUser='+destUser+'&del='+del+'&edit='+edit+'&drop='+drop,'', function(result) {
        $('#lmbAjaxContainer').html(result).show();
		if(el){
            limbasDivShow(el,'limbasDivMenuSnapshot','lmbAjaxContainer');
        }
	});
}

function lmbSnapShareSelect(ugval,snapname,gtabid){
	limbasSnapshotShare(null,gtabid,ugval);
}

function refreshMenuOnSnapshot()
{
	for(i=0;i<top.frames.length;i++)
	{
		if(top.frames[i])

			if(top.frames[i].name=="nav")
			{
				top.frames[i].location.href = top.frames[i].location.href;

				//while(top.frames[i].document.formLoaded != true)
				//{
				//	i = 0;
				//}
				//setTimeout('eval("alert(i + \' \' + top.frames[5].document.frameLoaded)")',1500);
				//top.frames[i].document.listdata('36','id35');
				//alert("win");
				break;
			}
	}
}


/*----------------- Diagramm -------------------*/
function lmbDiagramm(diag_id,gtabid) {
	var actid = "getDiag&gtabid=" + gtabid + "&diag_id="+ diag_id;
	ajaxGet(null,"main_dyns.php",actid,null,"lmbAjax_lmbDiagrammPost");
}

function lmbAjax_lmbDiagrammPost(result){
    if (ajaxEvalScript(result)) {
        result = result.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, '');
    }
	if(result){
		diagramm = open(result,"Diagram");
	}
}



// ######################################################


//----------------- neuer Datensatz -------------
function create_new() {
    if(jsvar["confirm_level"] > 0) {
        neu = 1;
    }else {
        neu = confirm(jsvar["lng_164"] + ':' + jsvar["tablename"] + '\n' + jsvar["lng_24"]);
    }
	if(neu){
		if(parent.detail){
			document.form2.target='detail';
		}else{
			document.form2.target='';
		}
		document.form2.action.value='gtab_neu';
		send_form(2);
	}
}


function lmbGetTableRowData(el) {

    return data = {
        ID:document.form2.ID.value,
        gtabid:document.form2.gtabid.value,
        formid:$(el).attr("data-formid"),
        formopenas:$(el).attr("data-formopenas"),
        formdimension: $(el).attr("data-formdimension"),
        v_tabid:$(el).attr("data-reltab"),
        v_fieldid:$(el).attr("data-relfield"),
        v_id:$(el).attr("data-relid")
    };
}


// handle dblclick on row
function lmbTableDblClickEvent(evt,el,gtabid,ID) {

    var params = lmbGetTableRowData(el);

    // open in new window
    formopener = params['formopener'];
    if(evt && evt.shiftKey){formopener = 'window';}
    if(evt && evt.altKey){formopener = 'tab';}

    params['formopener'] = formopener;
    lmbOpenForm(evt, gtabid, ID, params);

    aktivateRows(0);
    return false;
}

// open details from cached ID LmGl_edit_id (contextmenu)
function view_detail(action) {
	divclose();

    if(!action){action = 'gtab_deterg';}

    if(LmGl_edit_id){
        el = document.getElementById(LmGl_edit_id);
        var params = lmbGetTableRowData(el);

        params['action'] = action;
        lmbOpenForm(null, params['gtabid'], params['ID'],params);
    }

}

function view_change() {
    view_detail('gtab_change');
}

// menu function for "new win"
function lmbNewwin(ID) {
	divclose();
    if(!ID){
        ID = LmGl_edit_id;
    }
    if(ID) {
        el = document.getElementById(ID);
        var params = lmbGetTableRowData(el);

        params['formopenas'] = 'tab';
        lmbOpenForm(null, params['gtabid'], params['ID'],params);
    }
}


function view_copychange(id,gtabid) {
	if(parent.detail){
		document.form2.target='detail';
		document.form2.action.value='gtab_change';
		document.form2.ID.value=id;
		document.form2.gtabid.value=gtabid;
		send_form(2);
		document.form2.action.value='gtab_erg';
		document.form2.ID.value='';
		document.form2.gtabid.value=jsvar["gtabid"];
	}
}

// ---------------- Sendkeypress----------------------
function sendkeydown(evt) {
    if(evt.altKey) {
		if (evt.key === 's') {
			evt.preventDefault();
			alert('save');
		}

		if (evt.key === 'f') {
			evt.preventDefault();
			alert('find');
		}

		if (evt.key === 'a') {
			evt.preventDefault();
			alert('new');
		}

		if (evt.key === 'c') {
			evt.preventDefault();
			alert('copy');
		}

		if (evt.key === 'd') {
			evt.preventDefault();
			alert('delete');
		}

        return false;

    }
	if(evt.key === 'Enter' && !evt.shiftKey){
		evt.preventDefault();
		//set_focus();
		document.form1.select.value = 1;
		if(limbasDivCloseTab.length === 0 || validEnter){
			const etarget = (evt.target) ? evt.target : evt.srcElement;
			if(etarget.onchange){
				etarget.onchange();
			}
			window.setTimeout("send_form(1,2)",100);
		}
	}
}

// set table width / deprecated ??
function lmb_setTableWitdh(gtheader){

	var tabwidth = 0;
	for (var i = 0; i < gtheader.rows[0].cells.length; i++){
		tabwidth = tabwidth + $(gtheader.rows[0].cells[i]).width();
	}

	document.getElementById('lmbGlistSearchTab').style.width = tabwidth+'px';
	document.getElementById('lmbGlistHeaderTab').style.width = tabwidth+'px';
	document.getElementById('lmbGlistBodyTab').style.width = tabwidth+'px';

}

// set table position
function gtabSetTablePosition(posx,posy){

    // set window title
	document.title = jsvar["tablename"];

	if(top.main){
		top.main.focus();
	}

	// set height
    lmb_setAutoHeight(document.getElementById('GtabTableBody'), 20) ;

    // set width
    if($('#GtabTableFull')) {
            var overflowX = $(document).width() - $(window).width();
            var currentWidth = $('#GtabTableFull').width();
            $('#GtabTableFull').css('min-width', (currentWidth - overflowX) + 'px');
    }

    // set padding because of scrollbar
    if($('#lmbGlistBodyTab') && $('#GtabTableBody')) {
            var scrollBarVisible = $('#GtabTableBody').height() < $('#lmbGlistBodyTab').height();
            if(scrollBarVisible) {
                    $('#lmbGlistBodyTab > tbody > tr').find('td:last').css('padding-right', "10px");
                    $('#lmbGlistBodyTab > tbody > tr').find('td:last').css('-moz-box-sizing', "border-box");
                    $('#lmbGlistBodyTab > tbody > tr').find('td:last').css('-webkit-box-sizing', "border-box");
                    $('#lmbGlistBodyTab > tbody > tr').find('td:last').css('box-sizing', "border-box");
            }
    }

    if(!pagination_h) {
        gtabSetTablePagination();
    }

    // show if search filter is set
    let hasFilter;
    if ($('.lmbfringeGtabBody').data("hasFilter") == 1) {
        $('#conextIcon_14').addClass('text-warning');
        $('#conextIcon_28').addClass('text-success');
        hasFilter = 1;
    } else {
        $('#conextIcon_14').removeClass('text-warning');
        $('#conextIcon_28').removeClass('text-success');
    }

    if (typeof myExt_hasFilter === 'function') {
        myExt_hasFilter(hasFilter);
    }

}

// set auto pagination
var pagination_h = false;
var pagination_s = false;
function gtabSetTablePagination(){

    hasScrollBar = true;
    pagination_s = false;
	pagination_h = $('#lmbGlistBodyTab').outerHeight();
	if(!pagination_h){return;}
    pagination_h = (pagination_h - $('#GtabTableBody').outerHeight());

    var gtabTableBody = document.getElementById("GtabTableBody");
    var gtabTableGroup = document.getElementById("GtabTableGroup");

    // Math.round($('#GtabTableBody').outerHeight()
	if(gtabTableBody && gtabTableBody.scrollHeight == gtabTableBody.offsetHeight){
	    hasScrollBar = false;
    } else if(gtabTableGroup && gtabTableGroup.scrollHeight == gtabTableGroup.offsetHeight){
        hasScrollBar = false;
    }

    // no scrollbar
    if(hasScrollBar == false) {
        $("#GtabTableBody").bind('DOMMouseScroll mousewheel', function(e) {
            $("#GtabTableBody").unbind('DOMMouseScroll mousewheel');

            var np = document.form1.elements['filter_page[' + jsvar["gtabid"] + ']'];
            var page = np.value.split('/');
            var max = page[1];
            page = parseInt(page[0]);
            if(page >= max){return;}
            np.value = (page + 1 + '/' + max);

            pagination_s = 1;
            lmbAjax_resultGtab('form1', 1, 0, 0, 0);
            gtabSetTablePagination();
        });

    // scrollbar exists
	}else {

        $("#GtabTableBody")
			.unbind('DOMMouseScroll mousewheel')
			.scroll(function(e) {
				if ($(this).scrollTop() >= pagination_h) {
                    $(this).unbind('scroll');
					var np = document.form1.elements['filter_page[' + jsvar["gtabid"] + ']'];
					var page = np.value.split('/');
					var max = page[1];
					page = parseInt(page[0]);
					if(page >= max){return;}
                    np.value = (page + 1 + '/' + max);

					pagination_s = 1;
					e.preventDefault();
					lmbAjax_resultGtab('form1', 1, 0, 0, 0);
					gtabSetTablePagination();
				}
			});
    }

}



//----------------- Feldbreite automatisch verkleinern -------------------
function td_resize() {
	divclose();

    var gts = document.getElementById('lmbGlistSearchTab');
	var gth = document.getElementById('lmbGlistHeaderTab');
	var gtb = document.getElementById('lmbGlistBodyTab');
	//var tabbw = gtb.offsetWidth;
	var tdw_0 = $(gth.rows[0].cells[0]).width();

	var tabw = 0;

	for (var e = 0; e < gth.rows[0].cells.length; e++){
		var tdid = gth.rows[0].cells[e].id;
		var stdid = "tdhead"+tdid.substr(4,10);
		if(document.getElementById(stdid)){
			var tdw = document.getElementById(stdid).offsetWidth + 50;
		}else{
			var tdw =  tdw_0;
		}

		for (var i = 0; i < gtb.rows.length; i++){
			if(gtb.rows[i].cells[e]){
				gtb.rows[i].cells[e].style.width = tdw;
			}
		}

		tabw += tdw;
		gts.rows[0].cells[e].style.width = tdw+'px';
		gtb.rows[0].cells[e].style.width = tdw+'px';
		gth.rows[0].cells[e].style.width = tdw+'px';
	}
	gts.style.width = tabw;
	gth.style.width = tabw;
	gtb.style.width = tabw;

	tdsize(1);
	send_form(1);
}


// TD Sortierung
var dx = 0;
var current = null;
var zIndexTop = 1;
var pos_fid = null;
var pos_tid = null;

function lmbIniRowSort(evt,tabid,fieldid) {
	pos_fid = fieldid;
	pos_tid = tabid;
	document.onmousedown = lmbStartRowSort;
}

function lmbStartRowSort(evt) {

    var obj = evt.target;
    current = obj;
    dx = evt.pageX;

	//current.top = 10;
	current.className = 'gtabHeaderTitleTDItemOver';
	zIndexTop++;
	current.style.zIndex = zIndexTop;
	current.style.position = 'absolute';
	current.style.left = dx + 'px';

	if(obj.id.substr(0,6) == 'tdhead'){
		document.onmousemove = lmbRowSort;
		document.onmouseup = lmbEndRowSort;
	}

	return false;
}

function lmbRowSort(evt) {
	if (current != null) {
        current.style.left = evt.pageX + 'px';
	}
	return false;
}

function lmbEndRowSort(evt) {

    absposl = evt.pageX;

	if (Math.abs(absposl - dx) > 20) {
		var gth = document.getElementById('lmbGlistHeaderTab');
		for (var e = 1; e < gth.rows[0].cells.length; e++){
			var tdel = gth.rows[0].cells[e];
			var tdelposl = findPosX(tdel);
			var tdelposr = tdelposl + tdel.offsetWidth;
			if(absposl > tdelposl && absposl < tdelposr){
				var tid = tdel.id.split("_");
				tid = tid[2];
			}
		}

		if(tid && pos_fid && tid != pos_fid){
			document.form1.td_sort.value = pos_fid+";"+tid;
			send_form(1);
		}
	}

	current.className = 'gtabHeaderTitleTDItem';
	current.style.position = 'relative';
	current.style.left = 0 + 'px';
	document.onmousedown = null;
	document.onmouseup = null;
	document.onmousemove = null;

	current = null;
	return false;
}

function lmbShowRowSort(el,set) {
	if(current){
		if(el.id != 'gtdh_'+pos_tid+'_'+pos_fid){
			if(set){
				el.className = 'gtabHeaderTitleTDOver';
			}else{
				el.className = 'gtabHeaderTitleTD';
			}
		}
	}
}



var tdwidth = null;
var posx = null;
var posy = null;
var eltd = null;
var elinp = null;
var tbw = null;
var tdw = null;
var rowsize = null;
var elmnt = null;

var tabsw = null;
var tabhw = null;
var tabbw = null;

// --- TD Breite  -----------------------------------
function startDrag(evt,tabid,fieldid) {
	//if(evt.ctrlKey){
		document.onmouseup = endDrag;
		eltd = document.getElementById("gtdh_"+tabid+"_"+fieldid);
		elinp = document.getElementById("tdinp_"+tabid+"_"+fieldid);

		tabsw = document.getElementById('lmbGlistSearchTab').offsetWidth;
		tabhw = document.getElementById('lmbGlistHeaderTab').offsetWidth;
		tabbw = document.getElementById('lmbGlistBodyTab').offsetWidth;

		tdwidth = eltd.offsetWidth;

        posx = evt.pageX;

		document.onmousemove = drag;
	//}
	return false;
}

function drag(e) {

    evw = e.pageX - posx;
    tdw = evw + tdwidth;

	if(tdw > 10){
		for (var i = 0; i < document.getElementById('lmbGlistBodyTab').rows.length; i++){
			if(document.getElementById('lmbGlistBodyTab').rows[i].cells[eltd.cellIndex]){
				document.getElementById('lmbGlistBodyTab').rows[i].cells[eltd.cellIndex].style.width = tdw + 'px';
			}
		}
		document.getElementById('lmbGlistSearchTab').rows[0].cells[eltd.cellIndex].style.width = tdw + 'px';
		eltd.style.width = tdw + 'px';
		if(elinp){elinp.style.width = tdw + 'px';}

		document.getElementById('lmbGlistSearchTab').style.width = tabsw + evw + 'px';
		document.getElementById('lmbGlistHeaderTab').style.width = tabhw + evw + 'px';
		document.getElementById('lmbGlistBodyTab').style.width = tabbw + evw + 'px';
	}
	rowsize = 1;
	return false;
}

function endDrag() {
	eltd = null;
	eltb = null;
	document.onmouseup = null;
	document.onmousemove = null;
	//if(evw){gtabSetTablePosition();}
	//setGlobalBorderWidth();
    if(rowsize) {
        send_form(1, 2);
    }
	return false;
}

// --- Spaltenbreite speichern -----
function tdsize(start){
	if(!start){
		var start = 0;
	}
	var row_size = new Array();
	var gtheader = document.getElementById('lmbGlistHeaderTab');
    var offset = 0; // should be 1 for chrome, 0 for firefox, probably 0 for ie
	for (var i = 0; i < gtheader.rows[0].cells.length; i++){
		var rsel = gtheader.rows[0].cells[i].id.split("_");
		//row_size[i] = rsel[2] + "," + ($(gtheader.rows[0].cells[i]).width() + offset);  // shit
		row_size[i] = rsel[2] + "," + parseInt(gtheader.rows[0].cells[i].style.width);
	}
	document.form1.rowsize.value = row_size.join(";");
}


// Tabellenspalte verstecken - anzeigen
function lmbShowColumn(tabid,fieldid) {

    if (fieldid == 'all') {
        $("span[id^=lmbViewFieldItem_]").each(function () {
            this.previousSibling.style.visibility = '';
            this.style.color = 'green';

            document.form1.filter_hidecols.value += $(this).attr("data-gtabid")+'_'+$(this).attr("data-fieldid")+'_1;';

        });
        document.form1.filter_hidecols.value += 'none';
    } else if (fieldid == 'none') {
        $("span[id^=lmbViewFieldItem_]").each(function () {
            this.previousSibling.style.visibility = 'hidden';
            this.style.color = '';

            document.form1.filter_hidecols.value += $(this).attr("data-gtabid")+'_'+$(this).attr("data-fieldid")+'_2;';
        });

    } else {
        var menuitem = document.getElementById("lmbViewFieldItem_" + tabid + "_" + fieldid);
        document.form1.filter_hidecols.value = tabid + "_" + fieldid;

        if (menuitem.style.color == 'green') {
            menuitem.previousSibling.style.visibility = 'hidden';
            menuitem.style.color = '';
        } else {
            menuitem.previousSibling.style.visibility = '';
            menuitem.style.color = 'green';
        }
    }

	lmbAjax_resultGtab("form1",1,1,1,1);
	document.form1.filter_hidecols.value = '';
}
function lmbShowSubColumns(headerEl) {
	var header = $(headerEl);

	// get/switch mode (show all / hide all)
	var mode;
	if (header.attr('lmb-mode') === 'show') {
		mode = 'show';
        header.attr('lmb-mode', 'hide');
	} else {
		mode = 'hide';
        header.attr('lmb-mode', 'show')
	}

	// iterate over all context rows of that group
    var toChange = [];
    header
		.nextUntil('.lmbContextHeader', '.lmbContextLink')
		.each(function() {
			var image = $(this).children('i');
			var isHidden = (image.css('visibility') === 'hidden');

			// already hidden / already shown -> nothing to do
			if (mode === 'show' && !isHidden || mode === 'hide' && isHidden) {
				return;
			}

			// change context row appearance
			var text = $(this).children('span').get(0);
			if (isHidden) {
				image.css('visibility', '');
				text.style.color = 'green';
			} else {
				image.css('visibility', 'hidden');
				text.style.color = ''
			}

			// save tabid_fieldid for ajax request
			var id = text.id;
			toChange.push(id.replace('lmbViewFieldItem_', ''));
		});

    // perform ajax request
    document.form1.filter_hidecols.value = toChange.join(';');
    lmbAjax_resultGtab("form1", 1, 1, 1, 1);
    document.form1.filter_hidecols.value = '';
}

// Limit aufheben
function noLimit(){
	conf=confirm(jsvar["lng_2158"]);
	if(conf) {
		document.form1.filter_nolimit.value=1;
		send_form(1);
	}
}


// --- Export -----
function gtab_export(typ,medium) {
	document.form1.exp_typ.value=typ;
	document.form1.exp_medium.value=medium;
	document.form1.action.value='gtab_exp';
	document.form1.target='export';
	document.form1.submit();
	document.form1.action.value='gtab_erg';
	document.form1.target='_self';
}

// --- Setze Farbhintergründe -----------------------------------
var td_color = new Array();
var cell_id = null;
function set_color(COLOR) {

	if(!COLOR){COLOR = 'transparent';}
	//Zeile -------------
	if(document.fcolor_form.ctyp && document.fcolor_form.ctyp[0].checked == true){
		var line = cell_id.split("_");
		// Zellen einfärben ----------------
		var cc = null;
		var ar = document.getElementsByTagName("td");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			var ln = cc.id.split("_");
			if(ln[0] == "td"){
				if(ln[1] == line[1] || selected_rows['elrow_'+ln[1]+'_'+ln[3]]){
                    cc.style.setProperty('background-color', COLOR, 'important');
                    document.getElementById('elrow_'+ln[1]+'_'+ln[3]).style.backgroundColor = '';
					document.getElementById('elrow_'+ln[1]+'_'+ln[3]).setAttribute("lmbbgcolor", COLOR);
				}
			} else if(ln[0] == "tdap") {
                if(ln[1] == line[1] || selected_rows['elrow_'+ln[1]+'_'+ln[2]]){
                    cc.style.setProperty('background-color', COLOR, 'important');
                    document.getElementById('elrow_'+ln[1]+'_'+ln[2]).style.backgroundColor = '';
                    document.getElementById('elrow_'+ln[1]+'_'+ln[2]).setAttribute("lmbbgcolor", COLOR);
                }
			}
		}

		// Übergabevariable td_color füllen -------------
		var cc = null;
		var ar = document.getElementsByTagName("tr");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			var ln = cc.id.split("_");
			if(ln[0] == "elrow"){
				if(ln[1] == line[1] || selected_rows['elrow_'+ln[1]+'_'+ln[2]]){
					ajaxGet(null,'main_dyns.php','lmbSetGtabColor&td_color='+ln[0]+'_'+ln[1]+'_'+ln[2]+'_'+COLOR);
					selected_rows['elrow_'+ln[1]+'_'+ln[3]] = 0;
				}
			}
		}
	}else {
	//Spalte ------------
		var line = cell_id.split("_");
		var cc = null;
		var ar = document.getElementsByTagName("td");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			var ln = cc.id.split("_");
			if(ln[0] == "td" && ln[2] == line[2] && ln[3] == document.form2.gtabid.value){
				cc.style.backgroundColor = COLOR;
			}
		}
		ajaxGet(null,'main_dyns.php','lmbSetGtabColor&td_color=td_'+line[2]+'_'+line[3]+'_'+COLOR);
	}


}

// --- Sortierfunktion ----------
function lmbFieldSort(evt,VAL,TYP){
	if(evt.shiftKey){
		document.form1.order.value = VAL+TYP+'#+';
		send_form(1,2,1,1,1,0);
	}else if(evt.ctrlKey){
		if(document.form1.order.value){
			document.form1.order.value = document.form1.order.value+'#'+VAL+TYP;
		}else{
			document.form1.order.value = VAL+TYP;
		}
	}else{
		document.form1.order.value = VAL+TYP;
		send_form(1,2,1,1,1,0);
	}
}



// --- Popupchoice-funktion ----------
function lmbPopRelation(evt,el,type){
	if(el.checked){
		document.form1.elements[type].value = document.form1.elements[type].value + "|" + el.name + ";" + el.value + ";1";
	}else{
		document.form1.elements[type].value = document.form1.elements[type].value + "|" + el.name + ";" + el.value + ";0";
	}

	var myExtForms = document.getElementById('myExtForms');
	if(document.getElementById('GtabTableGroup') && myExtForms){myExtForms.innerHTML = "<div id='GtabTableGroup'></div>";}

	if(!evt.shiftKey){
		divclose();
		if(type == 'grp_choice'){send_form(1);}
	}
}


function popchoicexx(evt,choice,type){
	document.form1.elements[type].value = "";
	for(var i = 0; i < choice.length; i++){

		if(choice[i].selected == true){
			if(document.form1.elements[type].value){
				document.form1.elements[type].value = document.form1.elements[type].value + "|" + choice.name + ";" + choice[i].value + ";1";
			}else{
				document.form1.elements[type].value = choice.name + ";" + choice[i].value + ";1";
			}
		}else{
			if(document.form1.elements[type].value){
				document.form1.elements[type].value = document.form1.elements[type].value + "|" + choice.name + ";" + choice[i].value + ";0";
			}else{
				document.form1.elements[type].value = choice.name + ";" + choice[i].value + ";0";
			}
		}
	}
}



/*----------------- Lösch/Versteck/Kopier/Versionier-Funktion -------------------*/
function userecord(typ,nomess,actrows) {
	divclose();
	if(actrows){
	    count =	actrows.length;
    }else {
	count =	countofActiveRows();
    }
	var delt = "";

	if(count > 0){
		if(typ == 'delete'){var mess = jsvar["lng_2153"];if(document.getElementById("filter_force_delete") && document.getElementById("filter_force_delete").checked == true){mess += '\n\n'+jsvar["lng_2794"];}}
		if(typ == 'copy'){var mess = jsvar["lng_2156"];}
		if(typ == 'versioning'){var mess = jsvar["lng_2155"];}
		if(typ == 'link'){var mess = jsvar["lng_2186"];}
		if(typ == 'unlink'){var mess = jsvar["lng_2187"];}
		if(typ == 'unlock'){var mess = jsvar["lng_2433"];}
		if(typ == 'lock'){var dele = 1;}
        if(typ == 'archive'){var mess = jsvar["lng_2154"];}
        if(typ == 'trash'){var mess = jsvar["lng_3101"];}
        if(typ == 'restore'){var mess = jsvar["lng_2157"];}

		if(mess){
			var delt=confirm(mess+' ('+count+')');
		}
		if(delt || !mess) {
		    if(!actrows) {
			    var actrows = checkActiveRows();
            }
			if(actrows.length > 0){

                var use_records = actrows.join(";");
                document.form1.use_record.value = use_records;

                if (typ == 'report' || typ == 'diagramm') {
                    return use_records;
                }

                confirm_submit = function() {
                    document.form1.use_typ.value = typ;
                    send_form(1);
                }

                if(!jsvar['gtab_validate_'+document.form1.gtabid.value] || !use_records){
                    confirm_submit();
                    return;
                }

                var query = ajaxFormToURL('form1', 'gtabValidate');
                if(validate_request(typ,query)){
                    confirm_submit();
                }

			}else if(!nomess){alert(jsvar["lng_2083"]);}
		}else{return false;}
	}else if(!nomess){alert(jsvar["lng_1733"]);}
}

function lmb_version_status(date) {
	send_form(1);
}

// --- Quickmenü -----------------------------------
function smenu(ID,TABID) {
	document.form1.ID.value=ID;
	document.form2.ID.value=ID;
	document.form2.gtabid.value=TABID;
}

// --- Multible-Select-quickview -----------------------------------
function div_select(evt,el,show) {
	divclose();
	if(show == '1'){
		setxypos(evt,el);
		document.getElementById(el).style.visibility='visible';
	}else{
		document.getElementById(el).style.visibility='hidden';
	}
}


var activ_menu = null;
// --- Plaziere DIV-Element auf Cursorposition -----------------------------------
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}


/*----------------- User Rechte -------------------*/
function limbasGetGtabRules(gtabid,el) {
	count =	countofActiveRows();
	if(count > 0){
		var actrows = checkActiveRows();
		if(actrows.length > 0){
			var use_records = actrows.join(";");
			limbasDivShow(el,'limbasDivMenuExtras','limbasAjaxGtabContainer');
			limbasAjaxGtabRules(gtabid,use_records,'','limbasAjaxGtabContainer');
		}else{alert(jsvar["lng_2083"]);}
	}else{alert(jsvar["lng_1733"]);}
}

/* --- snpap submenu ----------------------------------- */
function limbasSnapshotDisplayDelete(evt,el) {
	//divclose();
	activ_menu = 1;
	document.getElementById("limbasDivConfirm").style.left=110;
	document.getElementById("limbasDivConfirm").style.top=100;
	document.getElementById("limbasDivConfirm").style.visibility='visible';
}

/* --- snpap submenu ----------------------------------- */
function limbasSnapshotDisplaySave(evt,el,confirmText) {
	//divclose();
	activ_menu = 1;
	document.getElementById("LimbasDivConfirmValue").innerHTML = confirmText;
	document.getElementById("limbasDivConfirm").style.left=110;
	document.getElementById("limbasDivConfirm").style.top=100;
	document.getElementById("limbasDivConfirm").style.visibility='visible';
}

// --- Menü ---------------------------
function div_menu(evt,el,divid){
	divclose();
	activ_menu = 1;
	if(jsvar["form_id"]){
		setxypos(evt,divid);
	}else{
		document.getElementById(divid).style.left = el.offsetLeft + document.getElementById("parentmeu_td").offsetWidth;
		document.getElementById(divid).style.top = 20;
	}
	document.getElementById(divid).style.visibility='visible';
}



// --- Infomenüsteuerung -----------------------------------
function div2(el) {
	activ_menu = 1;
	limbasDivShow(el,'limbasDivMenuContext','limbasDivMenuInfo');
}

// --- Farbmenüsteuerung -----------------------------------
function div4(el) {
	activ_menu = 1;
	limbasDivShow(el,'LimbasDivMenuContext',"limbasDivMenuFarb");
}

// --- Grouping-Popupsteuerung -----------------------------------
function div6(evt,el,ID,GVAL,EBENE) {
	divclose();

	var cc = null;
	var ar = document.getElementsByTagName("div");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		var ln = cc.id.split("_");
		if(ln[0] == "element6"){
			cc.style.display = 'none';
		}
	}

        limbasDivShow('',evt,"element6_"+ID+"_"+EBENE+"_"+GVAL);

	activ_menu = 1;
	validEnter = true;
}

/* --- Fenster fuer Kalender ------------------------------------------ */
function newwin3(TAB,FIELD,ID) {
	calendarwin = open("main.php?&action=kalender&dat_id=" + ID + "&gtabid=" + TAB + "&field_id=" + FIELD + "&wfl_id=" + jsvar["wfl_id"] + "&wfl_inst=" + jsvar["wfl_inst"] ,"calendar","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=850,height=600");
}


//--- move table cursor -----------------------------------
$(window).keydown(function(event){
	if(event.keyCode == 40){
		$('#'+LmGl_edit_id).next().click();
	}else if(event.keyCode == 38){
		$('#'+LmGl_edit_id).prev().click();
	}
});

