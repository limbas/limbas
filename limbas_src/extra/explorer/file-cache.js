/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */

var debug = null;

// ---- File-copy-Cache-----
function cache_open(ev){
	var el = cache_getElement(ev);
	var file_list = el.id;
	var elid = el.id.split(',');
	var cookieresult = holeCookie(elid[1]);

	if((elid[2] == "&" && jsvar["action"] != "message_main") || (elid[2] == "#" && jsvar["action"] != "explorer_main")){
		alert(jsvar["message1"]);
		divclose();
		return false;
	}

	if(elid[0] == "c"){
		LmEx_ajaxPasteCheck(ev,elid[2]+cookieresult,'copy');
		//LmEx_copy_file(elid[2]+cookieresult);
	}else{
		cache_list_reduce();
		LmEx_ajaxPasteCheck(ev,elid[2]+cookieresult,'move');
		//LmEx_move_file(elid[2]+cookieresult);
	}
}

function cache_getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
}

function cache_make_bold(ev){
	var el = cache_getElement(ev);
	el.style.fontWeight = 'bold';
}

function cache_make_unbold(ev){
	var el = cache_getElement(ev);
	el.style.fontWeight = 'normal';
}

// --- lösche alten Inhalt ---------
function cache_del_body(el){
	while(el.firstChild){
		el.removeChild(el.firstChild);
	}
}

function cache_addEvent(el, evname, func) {
	if (el.attachEvent) { // IE
	el.attachEvent('on' + evname, func);
	} else if (el.addEventListener) { // Gecko / W3C
	el.addEventListener(evname, func, true);
	} else {
		el['on' + evname] = func;
	}
}

// reduziere Cachliste bei verschieben
function cache_list_reduce(){
	var cookieresult = holeCookie('limbas_cachelist');
	var newcookielist = new Array();
	if(cookieresult != null){
		var cookielist = cookieresult.split(";");
		var cookielist_ = cookielist;
		//einfüge elemente löschen
		var i = 0;
		for (var e in cookielist){
			var elid = cookielist_[e].split(',');
			if(elid[1] == 'c'){
				newcookielist[i] = cookielist[e];
				i++;
			}else{
				loescheCookieWert(elid[0]);
			}
		}
	}
	setzeCookie('limbas_cachelist', newcookielist.join(";"));
	return true;
}

// erstelle Cachliste aller Filecookies
function cache_list(todo,count,typ,isfile){
	var cookieresult = holeCookie('limbas_cachelist');
	var now = new Date();
	var cookiename = now.getTime();
	if(cookieresult != null){
		cookielist = cookieresult.split(";");
		//neues element am Anfang hinzufügen
		cookielist.unshift(cookiename+","+todo+","+typ+","+count);
		var anz = cookielist.length;
		if(anz > jsvar["copycache"]){
			//letztes element löschen
			var lastelement = cookielist.pop();
			lastelement = lastelement.split(",");
			loescheCookieWert(lastelement[0]);
		}
		var cachelist = cookielist.join(";");
	}else{
		var cachelist = cookiename+","+todo+","+typ+","+count;
	}
	setzeCookie('limbas_cachelist', cachelist);
	return cookiename;
}

// ----- Listeneintrag -------
function cache_make_list(evt,kontainer) {
	var cookieresult = holeCookie('limbas_cachelist');

	// ---- Tabelle -------
	if(cookieresult){
		var el_tab = document.createElement('table');
		var c_el = cookieresult.split(';');
		var td_h = 15;
		el_tab.style.margin = '0px';
		el_tab.style.width = '140px';
		el_tab.style.borderCollapse = 'collapse';
		var el_body = document.createElement('tbody');
		el_tab.appendChild(el_body);

		// sofort ausführen
		if(jsvar["copycache"] == 1){
			for (var key in c_el){
				var el = c_el[key].split(',');
				var cookieresult = holeCookie(el[0]);
				if(el[1] == "c"){
					//cache_list_reduce();
					LmEx_ajaxPasteCheck(evt,cookieresult,'copy');
					//LmEx_copy_file(el[4]+cookieresult);
				}else{
					cache_list_reduce();
					LmEx_ajaxPasteCheck(evt,cookieresult,'move');
					//LmEx_move_file(el[4]+cookieresult);
				}
				return 1;
			}
		}

		// Listauswahl
		var bzm = 1;
		for (var key in c_el){
			var el = c_el[key].split(',');
			// ---- Zeilen -------
			var aktuTR = document.createElement('tr');
			aktuTR.style.cursor = 'pointer';
			cache_addEvent(aktuTR,'click', cache_open);
			cache_addEvent(aktuTR,'mouseover', cache_make_bold);
			cache_addEvent(aktuTR,'mouseout', cache_make_unbold);
			el_body.appendChild(aktuTR);
			var dobj = new Date();
			dobj.setTime(el[0]);

			if(el[1] == 'c'){
				var img1 = document.createElement("img");
				img1.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",0";
				img1.src = "pic/plus.gif";
			}else{
				var img1 = document.createElement("img");
				img1.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",0";
				img1.src = "pic/right.gif";
			}

			if(el[4] == '#'){
				var img4 = document.createElement("img");
				img4.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",1";
				img4.src = "pic/small_file.gif";
			}else if(el[4] == '&'){
				var img4 = document.createElement("img");
				img4.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",2";
				img4.src = "pic/small_mail.gif";
			}else if(el[4] == '*'){
				var img4 = document.createElement("img");
				img4.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",3";
				img4.src = "pic/small_ordner.gif";
			}

			var aktuTD = document.createElement('td');
			aktuTD.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",4";
			aktuTD.style.color = jsvar["WEB7"];
			aktuTD.appendChild(document.createTextNode('-'));
			aktuTR.appendChild(aktuTD);

			var aktuTD = document.createElement('td');
			aktuTD.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",5";
			aktuTD.appendChild(img1);
			aktuTR.appendChild(aktuTD);

			var aktuTD = document.createElement('td');
			aktuTD.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",6";
			aktuTD.appendChild(img4);
			aktuTR.appendChild(aktuTD);

			var aktuTD = document.createElement('td');
			aktuTD.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",7";
			aktuTD.appendChild(document.createTextNode('  '+el[3]));
			aktuTR.appendChild(aktuTD);

			var aktuTD = document.createElement('td');
			aktuTD.id = el[1]+","+el[0]+","+el[4]+","+el[2]+",8";
			if(bzm > 1){aktuTD.style.color = jsvar["WEB4"];}
			aktuTD.style.textAlign = 'right';
			var txt = dobj.getHours()+':'+dobj.getMinutes()+':'+dobj.getSeconds();
			aktuTD.appendChild(document.createTextNode(txt));
			aktuTR.appendChild(aktuTD);


			bzm++;
		}
		kontainer.appendChild(el_tab);
	}else{
		// --- alte Inhalte löschen ---
		document.getElementById("cachelist").style.visibility='hidden';
		//alert('empty cache');
	}
}

function LmEx_create_cachelist(evt){
	if(jsvar){
		cache_del_body(document.getElementById('cachelist_area'));
		cache_make_list(evt,document.getElementById('cachelist_area'));
	}
}

// --------------- Cookies -----------------

function setzeCookie(name, wert) {
	var arg_wert = setzeCookie.arguments;
	var arg_laenge = setzeCookie.arguments.length;
	var expires = (arg_laenge > 2) ? arg_wert[2] : null;
	var path = (arg_laenge > 3) ? arg_wert[3] : null;
	var domain = (arg_laenge > 4) ? arg_wert[4] : null;
	var secure = (arg_laenge > 5) ? arg_wert[5] : false;
	document.cookie = name + "=" + encodeURIComponent(wert) +
	((expires == null) ? "" : ("; expires=" +
	expires.toGMTString())) +
	((path == null) ? "" : ("; path=" + path)) +
	((domain == null) ? "" : ("; domain=" + domain)) +
	((secure == true) ? "; secure" : "");
}

function holeCookie(name) {
	name += "=";
	var laenge = name.length;
	var cookie_laenge = document.cookie.length;
	var i = 0;
	while (i < cookie_laenge) {
		var j = i + laenge;
		if (document.cookie.substring(i, j) == name)
		return holeCookieWert (j);
		i = document.cookie.indexOf(" ", i) + 1;
		if (i == 0)
		break;
	}
	return null;
}

function holeCookieWert(position) {
	var ende = document.cookie.indexOf (";", position);
	if (ende == -1)
	ende = document.cookie.length;

	return unescape(document.cookie.substring(position, ende));
}

function loescheCookieWert(name) {
  var exp = new Date();
	exp.setTime(exp.getTime() - 1);
	var cookie_wert = holeCookie(name);
	if(cookie_wert != null){
		document.cookie = name + "=" + cookie_wert + ";expires=" + exp.toGMTString();
	}
}