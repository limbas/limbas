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
 * ID: 195
 */


// ------------------- Popupkalender ----------------

function selected(cal, date) {
	eval("document.form1." + calendar.inputf + ".value = date;");
}

function closeHandler(cal) {
	hide_selects(2);
	cal.hide();
}

function hide_selects(act){
	if(browser_ie == 5 || browser_ie == 6){
		var el = this.element;
		var ar = document.getElementsByTagName("select");
		var cc = null;

		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			if(act == 1 && cc.name.substring(0,1) == "g"){
				cc.style.visibility = "hidden";
			}
			if(act == 2 && cc.name.substring(0,1) == "g"){
				cc.style.visibility = "visible";
			}
		}
	}
}

function showCalendar(event,inputf,container,value) {
	
	var sel = document.getElementById(container);
	var calendar = new Calendar(true, null, selected, closeHandler);
	calendar.showsTime = true;
	calendar.setRange(2000, 2010);
	calendar.create();
	calendar.setDateFormat("%d.%m.%Y %H:%M");
	calendar.inputf = inputf;
	if(value){calendar.parseDate(value);}
	calendar.showAtElement(sel);
	return false;
}


// --- Plaziere DIV-Element auf Cursorposition -----------------------------------
function setxypos(evt,el) {
	if(browser_ns5){
		document.getElementById(el).style.left=evt.pageX;
		document.getElementById(el).style.top=evt.pageY;
	}else{
		document.getElementById(el).style.left=window.event.clientX + document.body.scrollLeft;
		document.getElementById(el).style.top=window.event.clientY + document.body.scrollTop;
	}
}

// --- Farbmenüsteuerung -----------------------------------
function showdiv(evt,el) {
	setxypos(evt,el);
	document.getElementById(el).style.visibility='visible';
}

function setcolor(color){
	document.form1.inp_color.value = color;
}


// --- temporäres Array für Tag erstellen -----------------------------------
function fill_hour(stamp,typ) {
	var d_id = new Array();
	var bzm = 0;
	var bzm1 = 0;

	if(d_day > 6 || typ == 'woche' || typ == 'tag'){
		a_key = new Array();
		d_day = 1;
	}

	for (var key in t_id){
		if(t_end[key] >= stamp && t_start[key] <= (stamp + 86400)){
			for (var e in a_key){
				if(t_id[key] == a_key[e]){
					bzm = e;
				}
			}
			if(bzm1 == 0){
				a_key = new Array();
			}
			d_id[bzm] = t_key[key];  		// Array für Stunden
			a_key[bzm] = t_id[key];			// Array für setzen gleicher werte auf gleiche höhe
			l_id[t_id[key]] = t_key[key];	// Array für Listengenerierung (überschreibt doppelte)
			bzm++;
		}
	bzm1++;
	}
	d_day++;
	return d_id;
}


// --- temporäres Array für Jahr erstellen -----------------------------------

function fill_year(y,stamp) {
	var bzm;
	var sty = new Date(y,0,1);
	sty = sty.getTime() / 1000;
	dd_id = new Array();
	da_id = new Array();
	
	for (var key in t_id){
		var start = Math.floor((t_start[key] - sty) / 86400) + 1;
		var end = Math.ceil((t_end[key] - sty) / 86400);
		bzm = start;
		for (var d=start; d <= end; d++){
			dd_id[bzm] = t_key[key];
			bzm++;
		}
	}
}


function numsort(a,b){
	return a-b;
}


function getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
}

function open_details(ev){
	var elid = getElement(ev).id;
	var y = document.form1.y.value;
	var m = document.form1.m.value;
	var d = document.form1.d.value;
	var typ = document.form1.typ.value;
	var tab_id = document.form1.gtabid.value;
	var field_id = document.form1.field_id.value;
	var dat_id = document.form1.dat_id.value;
	open("main_admin.php?&action=setup_user_tracking&typ=1&userid=" + userstat +"&periodid="+elid,"Tracking","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=600");

}

function open_week(ev){
	var el = getElement(ev);
	var par = el.id.split("_");
	call_day(par[0],par[1],par[2],'tag',91);
}

function open_month(ev){
	var el = getElement(ev);
	var par = el.id.split("_");
	call_month(par[0],par[1],par[2],'monat',91);
}

function addEvent(el, evname, func) {
	if (el.attachEvent) { // IE
		el.attachEvent("on" + evname, func);
	} else if (el.addEventListener) { // Gecko / W3C
		el.addEventListener(evname, func, true);
	} else {
		el["on" + evname] = func;
	}
}

// ----- Listeneintrag -------
function make_list(kontainer,stp,y,m,d) {
	var anZeilen = l_id.length;
	var td_h = 15;

	var ntp = new Date();
	ntp = ntp.getTime() / 1000;
	
	if(anZeilen > 0){
		// ---- Tabelle -------
		var el_tab = parent.cal_list.document.createElement("table");
		el_tab.style.margin = "0px";
		el_tab.style.padding = "0px";
		el_tab.style.width = "100%";
		el_tab.style.borderCollapse = "collapse";
		var el_body = parent.cal_list.document.createElement("tbody");
		el_tab.appendChild(el_body);
		l_id.sort(numsort);
	}
	for(var i = 0; i<=anZeilen; i++){
		if(l_id[i]){
			// ---- Zeilen -------
			var aktuTR = parent.cal_list.document.createElement("tr");
			el_body.appendChild(aktuTR);
			var aktuTD = parent.cal_list.document.createElement("td");
			var values = [color[8],4,td_h,0,0,1,"#CCCCCC","solid","middle","left","middle"];
			create_td(aktuTD,values);
			if(t_color[l_id[i]]){aktuTD.style.backgroundColor = t_color[l_id[i]];};
			aktuTD.rowSpan = "2";	
			aktuTR.appendChild(aktuTD);

			var aktuTD = parent.cal_list.document.createElement("td");
			var values = [color[8],0,td_h,0,0,1,"#CCCCCC","solid","middle","left","middle"];
			create_td(aktuTD,values);
			aktuTD.style.borderBottom = "none";
			aktuTD.style.padding = "2px";
			aktuTD.style.fontSize = "9px";
			aktuTD.style.fontWeight = "bold";
			if(ntp < t_start[l_id[i]]){aktuTD.style.color = "black";};
			if(ntp >= t_start[l_id[i]] && ntp <= t_end[l_id[i]]){aktuTD.style.color = "#990000";};
			if(ntp > t_end[l_id[i]]){aktuTD.style.color = color[3];};
			var txt = t_start_date[l_id[i]]+"  >  "+t_end_date[l_id[i]];
			aktuTD.appendChild(parent.cal_list.document.createTextNode(txt));
			aktuTR.appendChild(aktuTD);
			
			var aktuTR = parent.cal_list.document.createElement("tr");
			el_body.appendChild(aktuTR);			
			var aktuTD = parent.cal_list.document.createElement("td");
			var values = [color[8],0,td_h,0,0,1,"#CCCCCC","solid","middle","left","middle"];
			create_td(aktuTD,values);
			aktuTD.style.borderTop = "none";
			aktuTD.style.padding = "2px";
			aktuTD.style.fontSize = "9px";
			aktuTD.colSpan = "2";
			aktuTD.style.cursor = "pointer";
			aktuTD.id = t_id[l_id[i]];
			addEvent(aktuTD,"click", parent.cal_list.open_details);
			addEvent(aktuTD,"mouseover", parent.cal_list.make_bold);
			addEvent(aktuTD,"mouseout", parent.cal_list.make_unbold);
			var txt = t_title[l_id[i]];
			aktuTD.appendChild(parent.cal_list.document.createTextNode(txt));
			aktuTR.appendChild(aktuTD);
			
			kontainer.appendChild(el_tab);
		}
	}
	if(anZeilen > 0){
		kontainer.appendChild(el_tab);
	}
}

// --- Framegrößen berechnen -----------------------------------
function framesize() {
	return document.body.offsetWidth;
}

// --- lösche alten Inhalt -----------------------------------
function del_body(el){
	while(el.firstChild){
		el.removeChild(el.firstChild);
	}
}

// --- TD Style -----------------------------------
function create_td(aktuTD,values){
	aktuTD.style.backgroundColor = values[0];
	aktuTD.style.width = values[1]+"px";
	aktuTD.style.height = values[2]+"px";
	aktuTD.style.margin = values[3]+"px";
	aktuTD.style.padding = values[4]+"px";
	aktuTD.style.borderWidth = values[5]+"px";
	aktuTD.style.borderColor = values[6];
	aktuTD.style.borderStyle = values[7];
	aktuTD.style.verticalAlign = values[8];
	aktuTD.style.textAlign = values[9];
}



// --- Tagesansicht generieren -----------------------------------
function make_year(kontainer,y,m,d,stp,typ,size) {
	

	var frs = framesize();
	var tab_w = parseInt((frs * size / 100));
	var td_w = parseInt((tab_w / 7));
	var td_h = parseInt((td_w / 2));
	
	fill_year(y,stp);
	dayofyear = 0;
	
	// ---- Tabelle -------
	var el_tab = document.createElement("table");
	el_tab.style.margin = "0px";
	el_tab.style.padding = "0px";
	el_tab.style.width = tab_w+"px";
	el_tab.style.borderCollapse = "collapse";
	var el_body = document.createElement("tbody");
	el_tab.appendChild(el_body);

	// ---- Monate 1-4 -------
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	for(var mth=0; mth<=3; mth++){
		var aktuTD = document.createElement("td");
		var values = [color[8],tab_w/3,td_h/3,0,0,1,"#CCCCCC","solid","top","center"];
		create_td(aktuTD,values);
		var monatstabelle = call_month(y,mth,d,typ,23);
		aktuTD.appendChild(monatstabelle);
		aktuTR.appendChild(aktuTD);
	}
	
	// ---- Monate 5-8 -------
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	for(var mth=4; mth<=7; mth++){
		var aktuTD = document.createElement("td");
		var values = [color[8],tab_w/3,td_h/3,0,0,1,"#CCCCCC","solid","top","center"];
		create_td(aktuTD,values);
		var monatstabelle = call_month(y,mth,d,typ,23);
		aktuTD.appendChild(monatstabelle);
		aktuTR.appendChild(aktuTD);
	}
	
	// ---- Monate 9-12 -------
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	for(var mth=8; mth<=11; mth++){
		var aktuTD = document.createElement("td");
		var values = [color[8],tab_w/3,td_h/3,0,0,1,"#CCCCCC","solid","top","center"];
		create_td(aktuTD,values);
		var monatstabelle = call_month(y,mth,d,typ,23);
		aktuTD.appendChild(monatstabelle);
		aktuTR.appendChild(aktuTD);
	}
	
	kontainer.appendChild(el_tab);
	kontainer.appendChild(document.createElement("BR"));
}




// --- Monats-Darstellung -----------------------------------
function getmonth(themonth0, themonth1, themonth2, themonth3, themonth4, themonth5, themonth6, themonth7, themonth8, themonth9, themonth10, themonth11){
	this[0] = themonth0; this[1] = themonth1; this[2] = themonth2;
	this[3] = themonth3; this[4] = themonth4; this[5] = themonth5;
	this[6] = themonth6; this[7] = themonth7; this[8] = themonth8;
	this[9] = themonth9; this[10] = themonth10; this[11] = themonth11;
}



function make_month(kontainer,y,m,d,stp,typ,size){
	
	var frs = framesize();
	var tab_w = parseInt((frs * size / 100));
	var td_w = parseInt((tab_w / 7));
	var td_h = parseInt((td_w / 2));

	var today = new Date(y,m,d); var thisDay;
	var monthDays = new getmonth(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	year=today.getFullYear(); thisDay = today.getDate();
	if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0))
	monthDays[1] = 29; nDays = monthDays[today.getMonth()];
	IsitNow = today; IsitNow.setDate(1); FindOut = IsitNow.getDate();
	if (FindOut == 1) IsitNow.setDate(0); startDay = IsitNow.getDay();
	
	// ---- Tabelle -------
	var el_tab = document.createElement("table");
	el_tab.style.margin = "0px";
	el_tab.style.padding = "0px";
	el_tab.style.width = tab_w+"px";
	el_tab.style.borderWidth = "1px";
	el_tab.style.borderColor = "black";
	el_tab.style.borderStyle = "solid";
	el_tab.style.borderCollapse = "collapse";
	var el_body = document.createElement("tbody");
	el_tab.appendChild(el_body);
	
	// ---- Datum-Info -------
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	var aktuTD = document.createElement("td");
	var values = [color[6],0,td_h/3,0,0,1,"#CCCCCC","solid","middle","center"];
	create_td(aktuTD,values);
	aktuTD.colSpan = "7";
	aktuTD.style.fontWeight= "bold";
	var txt = monthofyear[today.getMonth()] + " " + year;
	aktuTD.appendChild(document.createTextNode(txt));
	if(typ == "yahr"){
		aktuTD.id = y+"_"+m+"_"+d;
		aktuTD.style.cursor = "pointer";
		addEvent(aktuTD,"click", open_month);
	}
	
	aktuTR.appendChild(aktuTD);

	// ---- Wochentag -------
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	for (i=0; i<7; i++) {
		var aktuTD = document.createElement("td");
		var values = [color[7],td_w,td_h/3,0,0,1,"#CCCCCC","solid","middle","center"];
		create_td(aktuTD,values);
		aktuTD.style.fontWeight= "bold";
		aktuTD.appendChild(document.createTextNode(dayofweek[i]));
		aktuTR.appendChild(aktuTD);
	}
	// ---- Kalendertage -------
	var column = 0;
	var aktuTR = document.createElement("tr");
	el_body.appendChild(aktuTR);
	for (i=0; i<startDay; i++) {
		var aktuTD = document.createElement("td");
		var values = [color[8],td_w,td_h,0,0,1,"#CCCCCC","solid","top","center"];
		create_td(aktuTD,values);
		aktuTR.appendChild(aktuTD);
		column++;
	}
	for (i=1; i<=nDays; i++) {
		var aktuTD = document.createElement("td");
		var values = [color[8],td_w,td_h,0,0,1,"#CCCCCC","solid","top","center"];
		create_td(aktuTD,values);
		// --- Monat ----------------
		if(typ == 'monat'){
			var tagestabelle = call_day(y,m,i,typ,size);
			aktuTD.appendChild(tagestabelle);
		// --- Jahr -----------------
		}else{
			dayofyear++;
			if(dd_id[dayofyear]){
				aktuTD.title = dd_id[dayofyear];
				aktuTD.style.backgroundColor = color[10];
				aktuTD.appendChild(document.createTextNode(i));
				aktuTD.id = y+"_"+m+"_"+i;
				aktuTD.style.cursor = "pointer";
				addEvent(aktuTD,"click", open_week);
			}else{
				aktuTD.style.color= color[4];
				aktuTD.appendChild(document.createTextNode(i));
			}
		}
		aktuTR.appendChild(aktuTD);
		column++;
		if (column == 7) {
			var aktuTR = document.createElement("tr");
			el_body.appendChild(aktuTR);
			column = 0;
		}
	}	
	
	
	kontainer.appendChild(el_tab);
	if(typ == "monat"){kontainer.appendChild(document.createElement("BR"));}
	
	return el_tab;
}




// --- Tagesansicht generieren -----------------------------------
function make_day(kontainer,stp,y,m,d,size,week,typ) {

	var anZeilen = 0;
	var frs = framesize();
	
	if(typ == 'monat'){
		var tab_w = parseInt((frs * size / 100) / 7);
		var td_w = parseInt((tab_w / 7));
		var td_h = parseInt((td_w / 2));
	}else{
		var tab_w = parseInt((frs * size / 100));
		var td_w = parseInt((tab_w / 7));
		var td_h = parseInt((td_w / 6));	
	}
	
	//--- Stunden-Array --------------------
	var d_id = fill_hour(stp,typ);
	var anZeilen = d_id.length;
	
	var el_tab = document.createElement("table");
	el_tab.style.margin = "0px";
	el_tab.style.padding = "0px";
	el_tab.style.width = tab_w+"px";
	el_tab.style.borderCollapse = "collapse";
	if(typ != 'monat'){
		el_tab.style.borderColor = "black";
		el_tab.style.borderStyle = "solid";
		el_tab.style.borderWidth = "1px";	
	}
	var el_body = document.createElement("tbody");
	el_tab.appendChild(el_body);
	
	if(typ == 'monat'){
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		var aktuTD = document.createElement("td");
		var stpp = new Date(document.form1.y.value,document.form1.m.value,document.form1.d.value);
		stpp = stpp.getTime() / 1000;
		if(stp == stpp){
			var tdcol = color[4];
			aktuTD.style.color = color[1];
		}else{
			var tdcol = color[7];
		}
		var values = [tdcol,td_w,td_h,0,0,1,"#CCCCCC","solid","middle","center"];
		create_td(aktuTD,values);
		aktuTD.colSpan = "24";
		aktuTD.id = y+"_"+m+"_"+d;
		aktuTD.style.cursor = "pointer";
		addEvent(aktuTD,"click", open_week);
		aktuTD.appendChild(document.createTextNode(d));
		aktuTR.appendChild(aktuTD);
	}else{
		// ---- Wochentag-Zeile -------
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		var aktuTD = document.createElement("td");
		var values = [color[6],0,td_h/3,0,0,1,"#CCCCCC","solid","middle","center"];
		create_td(aktuTD,values);
		aktuTD.colSpan = "24";
		aktuTD.style.fontWeight= "bold";
		var stpp = new Date(y,m,d);
		if(typ != 'woche'){var txt = dayofweek[week]+"   "+stpp.getDate()+"."+(stpp.getMonth()+1)+"."+stpp.getYear();}else{var txt = dayofweek[week];}
		aktuTD.appendChild(document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);

		// ---- Stunden-Zeile -------
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		for (var spalte=1; spalte <= 24; spalte++) {
			var aktuTD = document.createElement("td");
			var values = [color[7],td_w,td_h/3,0,0,1,"#CCCCCC","solid","middle","center"];		
			create_td(aktuTD,values);
			if(spalte < 10){var txt = String("0" + (spalte-1));}else{var txt = spalte-1;}
			aktuTD.appendChild(document.createTextNode(txt));
			aktuTR.appendChild(aktuTD);
		}
	}
	
	var clsp;
	var sstp;
	var estp;
	
	
	for (var zeile=0; zeile < anZeilen; zeile++) {
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		var spalte = 1;
		while(spalte <= 24){
			stamp = stp + (3600 * spalte);
			
				//alert(t_end[d_id[zeile]]+" >= "+(stamp-3600)+" "+t_start[d_id[zeile]]+" <= "+(stamp-3600))
			if(t_end[d_id[zeile]] >= stamp - 3600 && t_start[d_id[zeile]] <= stamp - 3600){
				estp = stp + (3600 * 24);
				if(t_start[d_id[zeile]] < stp){sstp = stp;}else{sstp = t_start[d_id[zeile]];};
				if(t_end[d_id[zeile]] > estp){estp = estp-1;}else{estp = t_end[d_id[zeile]];};
				clsp = parseInt((estp - sstp) / 3600) + 1;
				var aktuTD = document.createElement("td");
				var tdcolor = t_color[d_id[zeile]];
				aktuTD.id = t_id[d_id[zeile]];			
				aktuTD.title = "(" + t_start_date[d_id[zeile]] + " > " + t_end_date[d_id[zeile]] + ") " + t_title[d_id[zeile]];
				aktuTD.colSpan = clsp ;
				var values = [tdcolor,0,td_h,0,0,1,"#CCCCCC","solid","top","center"];
				create_td(aktuTD,values);
				aktuTD.style.cursor = "pointer";
				addEvent(aktuTD,"click", open_details);
				aktuTR.appendChild(aktuTD);
				create_td(aktuTD,values);
				aktuTR.appendChild(aktuTD);
				spalte = spalte + clsp;
			}else{
				if(stamp <= t_start[d_id[zeile]]){estp = t_start[d_id[zeile]];}else{estp = stp + (3600 * 24);};
				clsp = parseInt((estp - stamp) / 3600) + 1;
				var aktuTD = document.createElement("td");
				var tdcolor = color[8];
				aktuTD.colSpan = clsp;
				var values = [tdcolor,0,td_h,0,0,0,"#CCCCCC","none","top","center"];
				create_td(aktuTD,values);
				aktuTR.appendChild(aktuTD);					
				create_td(aktuTD,values);
				aktuTR.appendChild(aktuTD);
				spalte = spalte + clsp;
			}
		}
	}
	
		

	
	if(typ != 'monat'){
		kontainer.appendChild(el_tab);
		kontainer.appendChild(document.createElement("BR"));
	}
	
	return el_tab;
}






// --- Wochenansicht generieren -----------------------------------
function call_week(y,m,d,typ,size) {
	var kontainer = document.getElementById("kontainer");
	var stp = new Date(y,m,d);
	var day = stp.getDay();
	var stpp;
	var e;
	
	stp = stp.getTime() / 1000;
	for (var i = 1; i <= 7; i++) {
		e = i - day;
		stpp = stp + (60 * 60 * 24 * e);
		make_day(kontainer,stpp,y,m,d,size,i-1,typ);	
	}
}
// --- Tagesansicht generieren -----------------------------------
function call_day(y,m,d,typ,size) {
	var kontainer = document.getElementById("kontainer");
	var stp = new Date(y,m,d);
	var day = stp.getDay();
	stp = stp.getTime() / 1000;
	return make_day(kontainer,stp,y,m,d,size,day-1,typ);
}

function call_month(y,m,d,typ,size) {
	var stp = new Date(y,m,d);
	stp = stp.getTime() / 1000;
	var kontainer = document.getElementById("kontainer");
	return make_month(kontainer,y,m,d,stp,typ,size);
}

function call_year(y,m,d,typ,size) {
	var stp = new Date(y,m,d);
	stp = stp.getTime() / 1000;
	var kontainer = document.getElementById("kontainer");
	make_year(kontainer,y,m,d,stp,typ,size);
}

