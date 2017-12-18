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

var jsvar = new Array();

// --- dom Kalender ----------------------------------
function call_year() {
	
}

var term_modus = null;
var mouse_down = null;
var y_pos = null;
var posy = null;
var tel = null;
var zi = 1;
var resize = null;
var el_height = null;
drag_el = new Array();
change_el = new Array();
resize_el = new Array();

function startDrag(evt,el,move) {
	if(move){
		mouse_down = 1;
		term_modus=1;
		el.style.zIndex = zi++;
		tel = el;
		y_pos = el.offsetTop;
		el_height = el.offsetHeight;
		if(browser_ns5){posy = evt.pageY;}else{posy = window.event.clientY;}
		if(evt.shiftKey){resize = 1;}else{resize = 0;}
		document.onmousemove = drag;
	}else{
		if(!evt.shiftKey){
			clear_all();
		}
		mouse_down = 1;
	}
	return false;
}

function drag(e) {
	if(browser_ns5){
		var ty = e.pageY - posy;
	}else{
		var ty = window.event.clientY - posy;
	}
	if(cell_height < 15){cell_height = 15;}
	if((ty/cell_height) == Math.round(ty/cell_height)){
		if(resize){
			if((ty+el_height) > 0){
			tel.style.height = el_height+ty;
			resize_el[tel.id.substring(3,99)] = (ty/cell_height);
			already_changed = 1;
			}
		}else{
			tel.style.top = ty;
			drag_el[tel.id.substring(3,99)] = (ty/cell_height);
			already_changed = 1;
		}
	}
	return false;
}

function endDrag(e) {
	
	document.onmousemove = null;
	ty = null;
	posy = null;
	mouse_down = 0;
	term_modus = 0;
	if(!e.shiftKey){
		activate_termin();
	}
	return false;
}

// ---- Selectionen zurücksetzen ----
function clear_all(){
	for (var t in akpool){
		if(akpool[t]){
			var del = document.getElementById('td_'+t);
			del.style.backgroundColor = del.abbr;
			del.axis = '0';
		}
	}
	akpool = new Array();
}

// ---- aenderungen speichern ----
function check_change(){
	if(!already_saved && already_changed){
		var save = confirm(jsvar["lng_1424"]);
		if(save){
			send_form();
		}
	}
}


function InitCalender(){
	term_modus=0;

	var spacey = findPosY(document.getElementById("body_element"));
	var winsize = window.innerHeight;
	var size = (winsize - spacey - 60);
	var csize = document.getElementById("body_element").offsetHeight;
	if(csize > size){
		document.getElementById("body_element").style.height = size;
	}
	
}





var already_changed = null;
var already_saved = null;
// --- Formular senden ----------------------------------
function send_form() {
	already_saved = 1;
	
	var drel = new Array();
	var i = 0;
	for (var dragid in drag_el){
		drel[i] = dragid+","+drag_el[dragid];
		i++;
	}
	var rrel = new Array();
	var i = 0;
	for (var resizeid in resize_el){
		rrel[i] = resizeid+","+resize_el[resizeid];
		i++;
	}
	var crel = new Array();
	var i = 0;
	for (var changeid in change_el){
		crel[i] = changeid;
		i++;
	}
	document.form1.drag_el.value = drel.join(";");
	document.form1.resize_el.value = rrel.join(";");
	document.form1.change_el.value = crel.join(";");
	document.form1.posy.value = document.body.scrollTop;
	document.form1.submit();
}

// ---------------- Sendkeypress----------------------
function sendkeydown(evt) {
	if(evt.keyCode == 13 && !evt.shiftKey){
		set_focus();
		if(!activate_termin()){
			send_form();
		}
	}
}

// ---------------- delete ----------------------
function delete_termin(evt,id,el) {
	if(evt.keyCode == 46){
		el.innerHTML = '';
		el.style.display = 'none';
		if(id){
			document.form1.delete_el.value = document.form1.delete_el.value + ";" + id;
			already_changed = 1;
		}else{
			el.name = 'null';
		}
	}
}

// ---- Termin-Vorschau -----
function preview_termin(pool){
	pool.reverse();
	var el_count = pool.length;
	var first_d = document.getElementById("d_"+pool[(el_count-1)]);
	var first_el = document.getElementById("td_"+pool[(el_count-1)]);
	var last_el = document.getElementById("td_"+pool[0]);
	
	var fel_time = first_el.id.substring(3,20);
	var lel_time = parseInt(last_el.id.substring(3,20)) + parseInt(jsvar["subh"]);
	var fel_h = first_el.offsetHeight;
	var fel_t = first_el.offsetTop;
	var lel_t = last_el.offsetTop;
	var el_w = jsvar["viewwidth"]+"%";
	var el_h = (lel_t-fel_t+fel_h);
	
	first_d.style.width = el_w;
	first_d.style.height = el_h;
	first_d.innerHTML = "<textarea id=\"nt_"+fel_time+"_"+lel_time+"\" name=\"new_term["+fel_time+"_"+lel_time+"]\" OnMousedown=\"startDrag(event,this,1);\" OnChange=\"term_modus=1;\" OnMousedown=\"term_modus=1;\" OnKeydown=\"delete_termin(event,0,this);\" class=\"calendarItemInput\" style=\"width:100%;height:"+el_h+";\"></textarea>";
	clear_all();
	document.getElementById("nt_"+fel_time+"_"+lel_time).value = '';
	document.getElementById("nt_"+fel_time+"_"+lel_time).focus();
	already_changed = 1;
}

// ---- Termin-Anzeige -----
function show_termin(fel_time,lel_time,count,nr,id,symbols){

	var first_d = document.getElementById("d_"+fel_time);
	var first_el = document.getElementById("td_"+fel_time);
	var last_el = document.getElementById("td_"+lel_time);
	
	var fel_h = first_el.offsetHeight;
	var fel_t = first_el.offsetTop;
	var lel_t = last_el.offsetTop;
	var el_w = Math.round(100/count);
	if(el_w*count > 100){
		var el_w = Math.round(95/count);
	}

	var el_lp = "left:"+Math.round((el_w*nr)-el_w)+"%";
	var el_h = (lel_t-fel_t);
	var el_dw = jsvar["viewwidth"]+"%";
	
	if(symbols){
		symbol = symbols.join("<br>");
	}
	first_d.style.width = el_dw;
	first_d.style.height = el_h;
	//first_d.innerHTML += "<textarea id=\"lt_"+id+"\" name=\"lt_"+id+"\" OnMouseOver=\"this.focus();\" OnChange=\"change_value(this);term_modus=1;\" OnMousedown=\"startDrag(event,this,1);\" OnDblClick=\"open_detail('"+id+"');\" OnClick=\"lmbCalShowInfo('"+id+"')\" OnKeydown=\"delete_termin(event,'"+id+"',this)\"; style=\"width:"+el_w+"%;height:"+el_h+";"+el_lp+";background-color:#FFFFFF;overflow:hidden;padding:1px;margin-left:1px;z-index:1000;position:absolute;\"></textarea>";
	first_d.innerHTML += "<span id=\"ll_"+id+"\" name=\"ll_"+id+"\" class=\"calendarItemSpan\" style=\"width:"+el_w+"%;height:"+(el_h-5)+";"+el_lp+";\" OnMousedown=\"startDrag(event,this,1);\"><div style=\"position:absolute;z-index:1001;padding:2px;\">"+symbol+"</div><textarea id=\"lt_"+id+"\" name=\"lt_"+id+"\" OnMouseOver=\"this.focus();\" OnChange=\"change_value(this);term_modus=1;\" OnDblClick=\"open_detail('"+id+"');\" OnClick=\"lmbCalShowInfo('"+id+"')\" OnKeydown=\"delete_termin(event,'"+id+"',this);\" class=\"calendarItemInput\" style=\"width:100%;height:100%;\"></textarea></span>";
}

// Termin Inhalt
function fill_termin(elid,val,period,marker,noedit,nochange){
	var el = document.getElementsByName(elid);
	for (var i=0;i<el.length;i++){
		el[i].value = val;
		el[i].title = period;
		if(marker){el[i].style.borderLeft = '5px solid '+marker;}
		if(noedit){
			el[i].disabled = true;
		}
		if(nochange){
			el[i].OnMousedown = '';
		}
	}
}

// Mummernsortierung
function Numsort (a, b) {
 	return a - b;
}

// ---- Termin-Aktivierung -----
function activate_termin(){
	var pool = new Array();
	var nkpool = new Array();
	var n = 0;
	var prev = 0;
	var pool_nr = 0;
	var el_nr = 0;
	
	// num array mit selectierten Werten erstellen
	for (var t in akpool){
		if(akpool[t]){
			nkpool[n] = t;
			n++;
		}
	}
	
	// array Gruppieren
	nkpool.sort(Numsort);
	for (var i in nkpool){
		if(nkpool[i] - jsvar["subh"] == prev){
			el_nr++;
			pool[pool_nr][el_nr] = nkpool[i];
		}else{
			el_nr = 0;
			pool_nr++;
			pool[pool_nr] = new Array();
			pool[pool_nr][el_nr] = nkpool[i];
		}
		prev = nkpool[i];
	}
	
	akpool = new Array();
	
	// Vorschau erstellen
	if(pool_nr > 0){
		term_modus = 1;
		for (var pn in pool){
			preview_termin(pool[pn]);
		}
		return true;
	}
	return false;
}

var akpool = new Array();
// ---- Zellenaktivierung -----
function select_period() {
	if(mouse_down || gdirect){
		if(!term_modus){
			var elid = gel.id.split('_');
			var cel = document.getElementById('td_'+elid[1]);
	
			if(akpool[elid[1]]){
				gel.style.backgroundColor = cel.abbr;
				akpool[elid[1]] = 0;
			}else{
				gel.style.backgroundColor=jsvar["WEB9"] ;
				akpool[elid[1]] = 1;
			}
		}
		term_modus = 0;
	}
}

var gel = null;
var gdirect = null;
// ---- Zellenaktivierung verzögert -----
function select_tmp(evt,el,direct){
	if(!term_modus){
		gel = el;
		gdirect = direct;
		window.setTimeout("select_period()", 5);
	}
}

// ---- Termin-Aktivierung -----
function change_value(el){
	change_el[el.name.substring(3,99)] = 1;
	already_changed = 1;
}

// ---- Termin View Info -----
function lmbCalShowInfo(el){
	if(parent.cal_js){
		parent.cal_js.lmbCalSetShowInfo(el);
	}
}




// Layer positionieren und öffnen
function setxypos(evt,el) {
	if(browser_ns5){
		document.getElementById(el).style.left = evt.pageX;
		document.getElementById(el).style.top = evt.pageY;
	}else{
		document.getElementById(el).style.left = window.event.clientX + document.body.scrollLeft;
		document.getElementById(el).style.top = window.event.clientY + document.body.scrollTop;
	}
}

var activ_menu = null;
function divclose() {
	if(!activ_menu){
	//parent.document.getElementById("tzonemenu").style.visibility='hidden';
            $('#tzonemenu').hide();
            $('#viewmenu').hide();
	//parent.document.getElementById("viewmenu").style.visibility='hidden';
	}
	activ_menu = 0;
}


function body_click(){
	window.setTimeout("divclose()", 50);
}

function open_menu(evt,el,menu){
	activ_menu = 1;
	setxypos(evt,menu);
	//document.getElementById(menu).style.visibility='visible';
        $('#'+menu).show();
}

function open_detail(ID){
	detail = open("main.php?&action=gtab_change&gtabid="+jsvar['gtabid']+"&ID=" + ID + "&form_id="+jsvar["fformid"] ,"Calendar","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=500");
}