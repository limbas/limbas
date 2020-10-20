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
 * ID:
 */

var jsvar = Array();

/* --- Element-Bewegen Maus ----------------------------------- */
var dx = new Array, dy = new Array, ex = 0, ey = 0, px = 0, py = 0, xy = 0;
var current = null;
var currenttyp = null;
var currentpic = null;
var currentdiv = null;
var currentel = null;
var currentid = null;
var resizeobj = null;
var tabelement = null;
var activ_menu = 0;
var move_id = new Array();
var view_tab = new Array();


function stopDefault(e) {
    if (e && e.preventDefault) {
        e.preventDefault();
    }
    else {
        window.event.returnValue = false;
    }
    return false;
}

if(browser_ns5){document.captureEvents(Event.MOUSEDOWN | Event.MOUSEUP);}
document.onmouseup = endDrag;

function startDrag(evt) {
	dx = new Array();
	dy = new Array();
	
	if(currentdiv != 'menu'){
		$("#menu").removeClass('ui-selected');
		current = document.getElementById(currentdiv);
	}
	
	multiselect = lmbGetUISelected();

	if(browser_ns5){
		// Schleife über alle selectable elemente
		$(".ui-selected").each(function() {
			obj = $( this ).get(0);
			objid = $( this ).attr('id');
			dx[objid] = evt.pageX - parseInt(obj.style.left);
			dy[objid] = evt.pageY - parseInt(obj.style.top);
			if(!multiselect){return false;}
		});
	}else{	
		$(".ui-selected").each(function() {
			obj = $( this ).get(0);
			objid = $( this ).attr('id');
			dx[objid] = window.event.clientX - parseInt(obj.style.left);
			dy[objid] = window.event.clientY - parseInt(obj.style.top);
			if(!multiselect){return false;}
		});
	}

	if(!multiselect && parent.form_menu.document.form1.set_zindex.checked == 1 && current){
		current.style.zIndex = zIndexTop;
	}

	if(browser_ns5){document.captureEvents(Event.MOUSEMOVE)}
	document.onmousemove = drag;
	
	return false;
}

function drag(evt){
	
	if(currentdiv == 'menu'){
		dragEl(evt,document.getElementById('menu'));
	}else{
		$("#menu").hide();
		$(".ui-selected").each(function() {
			dragEl(evt,$( this ).get(0));
		});
	}
}

function dragEl(evt,el) {

	if (objid = el.id) {

		if(parent.form_menu.document.form1.raster.value > 0){var raster = parent.form_menu.document.form1.raster.value;}
		else{var raster = 10;}
		
		if(browser_ns5){
			ctop = evt.pageY - dy[objid];
			cleft = evt.pageX - dx[objid];
			if(evt.ctrlKey){var ctrlk = 1;}
		}else{
			ctop = window.event.clientY - dy[objid];
			cleft = window.event.clientX - dx[objid];
		}
		
		if(!ctrlk){
			cleft = (cleft/raster);
			cleft = Math.round(cleft);
			cleft = (cleft*raster);
		
			ctop = (ctop/raster);
			ctop = Math.round(ctop);
			ctop = (ctop*raster);
		}
		
		el.style.top = ctop;
		el.style.left = cleft;
		
		document.getElementById('border_move').style.display = 'none';

		if(currentdiv != 'menu'){
			parent.form_menu.document.form1.XPOSI.value = parseInt(cleft);
			parent.form_menu.document.form1.YPOSI.value = parseInt(ctop);
		}
	}
	return false;
}



function endDrag(e) {
    // prevent shadow of menu
    $('#menu').removeClass('ui-selected');

	// Schleife über alle selectable elemente
	$(".ui-selected").each(function() {
		fill_posxy($( this ).attr('id'));
	});
	
	// enable selectable function
	$('#innerframe').selectable("enable");
	
	if(mainisactive){
		$("#menu").not(":visible").show('fast');
	}
	
	if(browser_ns5){document.releaseEvents(Event.MOUSEMOVE);}
	document.onmousemove = null;
	document.onmousedown = null;
	current = null;
	eltd = null;
	eltb = null;
	mainisactive = null;
	
	if(document.getElementById(currentdiv)){
		document.getElementById('border_move').style.left = parentsetx(currentdiv) + document.getElementById(currentdiv).offsetWidth - 8;
		document.getElementById('border_move').style.top = parentsety(currentdiv) + document.getElementById(currentdiv).offsetHeight - 8;
		document.getElementById('border_move').style.display = '';
	}
	
	return false;
}


function startResize(evt) {
	if(browser_ns5){
		var obj = evt.target;
		current = obj.parentNode.style;
		dx = evt.pageX - parseInt(current.left);
		dy = evt.pageY - parseInt(current.top);
		px = evt.pageX;
		py = evt.pageY;
	}else{
		var obj = window.event.srcElement;
		current = obj.parentElement.style;
		dx = window.event.clientX - parseInt(current.left);
		dy = window.event.clientY - parseInt(current.top);
		px = window.event.clientX;
		py = window.event.clientY;
	}

	if(currentdiv){
		current_div = document.getElementById(currentdiv).style;
	}
	if(currentpic){current_pic = document.getElementById(currentpic);}
	ex = parseInt(document.getElementById(currentdiv).offsetWidth);
	ey = parseInt(document.getElementById(currentdiv).offsetHeight);
	xy = (ex / ey);
	if(parent.form_menu.document.form1.set_zindex.checked == 1){
		current.zIndex = zIndexTop;
	}
	if(obj.parentNode.id != 'ramen'){
		if(browser_ns5){document.captureEvents(Event.MOUSEMOVE)}
		if(currentpic){document.onmousemove = resize_pic;}else{document.onmousemove = resize;}
	}

	return false;
}

function resize(evt) {
	if (current != null) {
		
		if(parent.form_menu.document.form1.raster.value > 0){var raster = parent.form_menu.document.form1.raster.value;}
		else{var raster = 10;}
		
		if(browser_ns5){
			cleft = evt.pageX;
			cletop = evt.pageY;
			if(evt.ctrlKey){var ctrlk = 1;}
			if(evt.shiftKey){var shftk = 1;}
		}else{
			cleft = window.event.clientX;
			cletop = window.event.clientY;
			if(window.event.ctrlKey){var ctrlk = 1;}
			if(window.event.shiftKey){var shftk = 1;}
		}

		if(cletop - py + ey > 0 && cleft - px + ex > 0){
			ptop = cletop - dy;
			pleft = cleft - dx;		
			pheight = cletop - py + ey;
			if(parent.form_menu.document.form1.prop.checked == 1 || shftk){
				pwidth = (cletop - py + ey) * xy;
			}else{
				pwidth = cleft - px + ex;
			}
			
			if(!ctrlk){
				pwidth = (pwidth/raster);
				pwidth = Math.round(pwidth);
				pwidth = (pwidth*raster);
			
				pheight = (pheight/raster);
				pheight = Math.round(pheight);
				pheight = (pheight*raster);
				
			}

			current.top = ptop;
			current.left = pleft;
			
			current_div.width = pwidth;
			current_div.height = pheight;
		}

		parent.form_menu.document.form1.HPOSI.value = parseInt(current_div.height);
		parent.form_menu.document.form1.WPOSI.value = parseInt(current_div.width);
	}
	return false;
}

function resize_pic(evt) {
	if (current != null) {
		if(browser_ns5){
			if(evt.pageY - py + ey > 0 && evt.pageX - px + ex > 0){
				if(parent.form_menu.document.form1.prop.checked == 1 || evt.shiftKey){
					current.top = evt.pageY - dy;
					current.left = px + ((evt.pageY - py) * xy) - 8;
					current_div.height = evt.pageY - py + ey;
					current_pic.height = evt.pageY - py + ey;
					current_div.width = (evt.pageY - py + ey) * xy;
					current_pic.width = (evt.pageY - py + ey) * xy;
				}else{
					current.top = evt.pageY - dy;
					current.left = evt.pageX - dx;
					current_div.height = evt.pageY - py + ey;
					current_div.width = evt.pageX - px + ex;
					current_pic.height = evt.pageY - py + ey;
					current_pic.width = evt.pageX - px + ex;
				}
			}
		}else{
			if((window.event.clientY - py + ey) > 0 && (window.event.clientX - px + ex) > 0){
				if(parent.form_menu.document.form1.prop.checked == 1 || window.event.shiftKey){
					current.top = window.event.clientY - dy;
					current.left = px + ((window.event.clientY - py) * xy) - 8;
					current_div.height = window.event.clientY - py + ey;
					current_div.width = (window.event.clientY - py + ey) * xy;
					current_pic.height = window.event.clientY - py + ey;
					current_pic.width = (window.event.clientY - py + ey) * xy;
				}else{
					current.top = window.event.clientY - dy;
					current.left = window.event.clientX - dx;
					current_div.height = window.event.clientY - py + ey;
					current_div.width = window.event.clientX - px + ex;
					current_pic.height = window.event.clientY - py + ey;
					current_pic.width = window.event.clientX - px + ex;
				}
			}
		}
		parent.form_menu.document.form1.HPOSI.value = current_pic.height;
		parent.form_menu.document.form1.WPOSI.value = current_pic.width;
	}
	return false;
}


/*---------------- Tabulator-Element anzeigen ---------------------*/
function switchTabulator(el){
	if(!el.parentNode.parentNode) return;
	for(var i=0; i<el.parentNode.parentNode.childNodes.length; i++){
		var mt = "formCatItem";
		if(!el.parentNode.parentNode.childNodes[i].id || el.parentNode.parentNode.childNodes[i].id.substr(0,mt.length)!=mt) continue;
		var dsp = "none";
		var st = "normal";
		var nodel = el.parentNode.parentNode.childNodes[i].id.replace(mt,"div");
		if(nodel==el.id){dsp = "";st='bold'}
		document.getElementById(nodel).style.fontWeight = st;
		if(document.getElementById(nodel).id == 'div10000') continue;
		el.parentNode.parentNode.childNodes[i].style.display = dsp;
	}
}

var aktivTabulator = new Array();
/*---------------- Tabulator-Element anzeigen ---------------------*/
function setTabulator(mainel,tabuid){
	var val = new Array();
	var bzm = 0;

	aktivTabulator[mainel] = tabuid;
	
	for (var key in aktivTabulator){
		val[bzm] = key+"_"+aktivTabulator[key];
		bzm++;
	}
	if(val){
		document.form1.aktiv_tabulator.value = val.join(";");
	}
}

/*---------------- X - Elternteile addieren ---------------------*/
function parentsetx(id){
	var x = 0;
	if(document.getElementById(id)){
		var eltern = document.getElementById(id).offsetParent;
		while(eltern){
			var x = eltern.offsetLeft + x;
			eltern = eltern.offsetParent;
		}
		var x = (document.getElementById(id).offsetLeft + x);
		return x;
	}
}
/*---------------- Y - Elternteile addieren ---------------------*/
function parentsety(id){
	var y = 0;
	if(document.getElementById(id)){
		var eltern = document.getElementById(id).offsetParent;
		while(eltern){
			var y = eltern.offsetTop + y;
			eltern = eltern.offsetParent;
		}
		var y = (document.getElementById(id).offsetTop + y);
		return y;
	}
}

/*---------------- get UI selected ---------------------*/
function lmbGetUISelected(){
	//if($(".ui-selected").filter('[id^="div"]').length > 1){
	var len = $(".ui-selected").filter('[lmbselectable="1"], [lmbtype="bild"], [lmbtype="line"], [lmbtype="ellipse"], [lmbtype="scroll"], [lmbtype="reminder"], [lmbtype="wflhist"]').length;
	if(len > 1){
		return len;
	}else{
		return false;
	}
}

/*---------------- Ausschneiden ---------------------*/
function lmbCutElements(el){
	
	// Schleife über alle selectable elemente
	$(".ui-selected").each(function() {
		obj = $( this ).get(0);
		objid = $( this ).attr('id');
		
		move_id.push(objid.substr(3));
		obj.className='markAsDisabled';

		//if(!lmbGetUISelected()){return false;}
	});

}



/* --- Menu-Aktivierung ----------------------------------- */
function aktivate_menu(ID) {
	currentdiv = ID;

	// selectable only for active element
	$("#"+currentdiv).addClass('ui-selected');

	document.onmousedown = startDrag;
}


/* --- Element-Aktivierung ----------------------------------- */
function aktivate_resize() {
	document.onmousedown = startResize;
}


/* --- Tabellen-Element hinzufügen (Feld aktivieren) ----------------------------------- */
var active_tabcell;
var active_tabcell_style;
function act_tabelement(dv,TAB,ROW,COL){
	
	var el = document.getElementById('div'+dv);
	
	if(active_tabcell){
		active_tabcell.style.border = active_tabcell_style;
	}
	
	active_tabcell= el;
	active_tabcell_style = el.style.border;
	
	el.style.border = '2px dashed red';
	document.form1.form_tab.value = TAB;
	document.form1.form_tab_el.value = ROW+";"+COL;
	parent.form_menu.document.form1.form_tab.value = TAB;
	parent.form_menu.document.form1.form_tab_el.value = ROW+";"+COL

}

//Tabellen-Spaltenbreite/Höhe berechnen
var tdel = new Array();
function set_tabcols() {
	var cc = null;
	var ar = document.getElementsByTagName("th");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,6) == "tab_el"){
			var el = cc.id.split("_");
			var elname = el[2] + "_" + el[4];
			tdel[elname] = elname + "_" + parseInt(cc.offsetWidth);
		}
	}

	var val = "";
	for (var e in tdel){
		var val = val + tdel[e] + ";";
	}
	document.form1.form_tab_size.value = val;
}

// -------------- Position/Größen-Bestimmung ----------------------
// Array mit position und größe bei 'endDrag()' und von 'posxy_change()'
var posxy = new Array();
var setstyle = new Array();
function fill_posxy(ID) {
	if(ID){
		var div = ID.substr(3);
		posxy[div] = div + "," + parseInt(document.getElementById(ID).style.left) + "," + parseInt(document.getElementById(ID).style.top) + "," + parseInt(document.getElementById(ID).style.width) + "," +  parseInt(document.getElementById(ID).style.height) + "," + document.getElementById(ID).style.zIndex;
	}
}
// Array in var form_possize eintragen bei post()
function set_posxy() {
	for (var i in posxy){
		document.form1.form_possize.value = document.form1.form_possize.value + ";" + posxy[i];
	}
	document.form1.menuposx.value = parseInt(document.getElementById('menu').style.left);
	document.form1.menuposy.value = parseInt(document.getElementById('menu').style.top);
	set_style();
	set_tabcols();
}
// Position bestimmen und fill_posxy() aufrufen
function posxy_change(X,Y) {
	$(".ui-selected").each(function() {
		el = $( this ).get(0);
		if(el.id == 'menu'){return;}
		if(X){el.style.left = parseInt(X);}
		if(Y){el.style.top = parseInt(Y);}

		fill_posxy(el.id);
		if(!lmbGetUISelected()){return false;}
	});
}
// Größenäderung bestimmen und fill_posxy() aufrufen
function sizexy_change(X,Y) {
	
	$(".ui-selected").each(function() {
		el = $( this ).get(0);
		if(el.id == 'menu'){return;}
		if(X){el.style.height = parseInt(X);}
		if(Y){el.style.width = parseInt(Y);}
		if(el.tagName.toLowerCase() == 'td'){
			eltab = el.parentNode.parentNode;
			for (var i = 0; i < eltab.rows.length; i++){
				eltab.rows[i].cells[el.cellIndex].style.height = parseInt(X);
				eltab.rows[i].cells[el.cellIndex].style.width = parseInt(Y);
			}
		}
		
		fill_posxy(el.id);
		if(!lmbGetUISelected()){return false;}
	});

}
// Positionsänderung über Tastatureingabe
function movex(evt) {
	
	if(browser_ns5){
		if(evt.shiftKey){var shftk = 1;}
	}else{
		if(window.event.shiftKey){var shftk = 1;}
	}
	
	if(document.form1.aktiv_id.value && shftk){
		
		$(".ui-selected").each(function() {
			el = $( this ).get(0);
			
			if(el.id == 'menu'){return;}
			
			POSY = parseInt(el.style.top);
			POSX = parseInt(el.style.left);
	
			if(evt.keyCode == 38){
				POSY = (POSY - 1);
				el.style.top = POSY;
				document.getElementById('border_move').style.top = (parseInt(document.getElementById('border_move').style.top) - 1);
				parent.form_menu.document.form1.YPOSI.value = POSY;
			}
			if(evt.keyCode == 40){
				POSY = (POSY + 1);
				el.style.top = POSY;
				document.getElementById('border_move').style.top = (parseInt(document.getElementById('border_move').style.top) + 1);
				parent.form_menu.document.form1.YPOSI.value = POSY;
			}
			if(evt.keyCode == 37){
				POSX = (POSX - 1);
				el.style.left = POSX;
				document.getElementById('border_move').style.left = (parseInt(document.getElementById('border_move').style.left) - 1);
				parent.form_menu.document.form1.XPOSI.value = POSX;
			}
			if(evt.keyCode == 39){
				POSX = (POSX + 1);
				el.style.left = POSX;
				document.getElementById('border_move').style.left = (parseInt(document.getElementById('border_move').style.left) + 1);
				parent.form_menu.document.form1.XPOSI.value = POSX;
			}
		
			fill_posxy(el.id);
		});

	}

	// edit: uncommented to support codemirror
	// if(evt.keyCode == 13){
	// 	setTimeout("enterfocus()",200);
	// }
}


// ---- Farbauswahl ---------
function set_color(val){
	fill_style(colorid,colorstyle,val)
}

function lmb_setFormEvent(el,typ){
	document.form1.form_event1.value = document.form1.form_event1.value+'#;#'+document.form1.form_edit_id.value+'#,#'+typ+'#,#'+el.value;
}


// -------------- Style-Bestimmung ----------------------
// Stylebehandlung Eintrag in Array
function fill_style(STYLE_ID,STYLE,VAL) {
	var ID = document.form1.form_edit_id.value;
	var div = "div"+ID;
	var elval = VAL;
	el = document.getElementById(div).style;
	if(!setstyle[ID]){setstyle[ID] = new Array();}
	
	var multiselect = lmbGetUISelected();
	
	// Schleife über alle selectable elemente
	$(".ui-selected").filter('[id^="div"]').each(function() {
		div = $( this ).attr('id');
		ID = div.substr(3,10);
		el = document.getElementById(div).style;
		if(!setstyle[ID]){setstyle[ID] = new Array();}

		switch(STYLE) {
			case "":
			break;
			case "lineHeight":
			if(!VAL){VAL = 0;}
			VAL = parseFloat(el.fontSize) + parseFloat(VAL);
			VAL = VAL + "px";
			el.lineHeight = VAL;
			break;
			case "border":
			if(document.getElementById(VAL).checked){
				eval("el."+VAL+" = '';");
				VAL = '';
			}else{
				eval("el."+VAL+" = 'none';");
				VAL = 'none';
			}
			break;
			default:
			eval("el."+STYLE+"='"+VAL+"';");
			break;
		}
	
		setstyle[ID][STYLE_ID] = VAL;
	
		// Zusatz Ramen
		if(STYLE_ID == 14 && VAL == 'none'){
			document.getElementById('borderLeft').checked=0;fill_style('17','border','borderLeft');
			document.getElementById('borderRight').checked=0;fill_style('18','border','borderRight');
			document.getElementById('borderTop').checked=0;fill_style('19','border','borderTop');
			document.getElementById('borderBottom').checked=0;fill_style('20','border','borderBottom');
		}else if(STYLE_ID == 14 && VAL != 'none'){
			document.getElementById('borderLeft').checked=1;fill_style('17','border','borderLeft');
			document.getElementById('borderRight').checked=1;fill_style('18','border','borderRight');
			document.getElementById('borderTop').checked=1;fill_style('19','border','borderTop');
			document.getElementById('borderBottom').checked=1;fill_style('20','border','borderBottom');
		}
	
		// Linienzusatz jsGraphics
		if(currenttyp == 'line'){
			var js_color = document.getElementById(div).style.color;
			var js_lheight = parseInt(document.getElementById(div).style.fontSize);
			var js_x2 = parseInt(document.getElementById(div).style.width);
			var js_y2 = parseInt(document.getElementById(div).style.height);
			if(VAL){var js_xy = 'true';}else{var js_xy = 'false';}
			eval("js_clear('jg"+ID+"');");
			eval("js_line('jg"+ID+"','"+js_x2+"','"+js_y2+"','"+js_xy+"','"+js_color+"','"+js_lheight+"');");
		}
	
		if(currenttyp == 'ellipse'){
			var js_color = document.getElementById(div).style.color;
			var js_lheight = parseInt(document.getElementById(div).style.fontSize);
			var js_height = parseInt(document.getElementById(div).style.height);
			var js_width = parseInt(document.getElementById(div).style.width);
			eval("js_clear('jg"+ID+"');");
			eval("js_ellipse('jg"+ID+"','"+js_width+"','"+js_height+"','"+js_color+"','"+js_lheight+"');");
		}

	});
}

// Array in var form_setstyle eintragen bei post()
function set_style() {
	var val = "";
	for (var i in setstyle){
		var val = val + ";" + i + ",";
		for (var e in setstyle[i]){
			var val = val + e + "|" + setstyle[i][e] + ",";
		}
	}
	document.form1.form_setstyle.value = val;
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


// ------------- Menu-Layer-Funktionen ----------------
// schließe alle Menu-Layer
function divclose() {
	if(activ_menu!=1){
		limbasDivClose('menu');
	}
	activ_menu=0;
}

var snap_tabid = 0;

// --- Submenüsteuerung -----------------------------------
var colorid;
var colorstyle;
function submenu_style(STYLE) {
	if(STYLE){
		var st = STYLE.split(";");
		colorid = st[0];
		colorstyle = st[1];
	}
}

// --- Framegrößen berechnen -----------------------------------
function set_posframe() {
	document.form1.form_framesize.value = parent.form_main1.document.body.offsetHeight + ";" + parent.form_main2.document.body.offsetHeight + ";" + parent.form_main3.document.body.offsetHeight;
}




var f_id = new Array();
var f_zindex = new Array();
var f_value = new Array();
var f_typ = new Array();

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
	aktuTD.style.width = values[11]+"px";
	aktuTD.style.overflow = "hidden";
	aktuTD.style.verticalAlign = "top";
}

// --- lösche alten Inhalt -----------------------------------
function del_body(el){
	while(el.firstChild){
		el.removeChild(el.firstChild);
	}
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
function make_list(kontainer) {
	var td_h = 15;
	
	// ---- Tabelle -------
	var el_tab = parent.form_menu.document.createElement("table");
    el_tab.className = "formeditorPanel";
	el_tab.style.margin = "0px";
	el_tab.style.width = "230px";
	el_tab.style.borderCollapse = "collapse";
	el_tab.style.tableLayout = "fixed";
	
	var el_body = parent.form_menu.document.createElement("tbody");
	el_tab.appendChild(el_body);

	for (var key in f_id){
		// ---- Zeilen -------
		var aktuTR = parent.form_menu.document.createElement("tr");
		aktuTR.id = "ft"+f_id[key];
		aktuTR.style.cursor = "pointer";
		//addEvent(aktuTR,"click", parent.form_menu.open_details);
		aktuTR.addEventListener("click", function(e){parent.form_menu.open_details(e)});
		addEvent(aktuTR,"mouseover", parent.form_menu.make_bold);
		addEvent(aktuTR,"mouseout", parent.form_menu.make_unbold);
		el_body.appendChild(aktuTR);

		//var aktuTD = parent.form_menu.document.createElement("td");
		//var values = [color[8],0,td_h,0,0,1,"#CCCCCC","solid","middle","left","middle",20];
		//create_td(aktuTD,values);
		//aktuTD.id = "fi"+f_id[key];
		//var txt = f_zindex[key];
		//aktuTD.appendChild(parent.form_menu.document.createTextNode(txt));
		//aktuTR.appendChild(aktuTD);

		var aktuTD = parent.form_menu.document.createElement("td");
		aktuTD.style.width = "60px";
		aktuTD.id = "fk"+f_id[key];
		var txt = f_typ[key];
		aktuTD.appendChild(parent.form_menu.document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);

		var aktuTD = parent.form_menu.document.createElement("td");
		aktuTD.style.width = "120px";
		aktuTD.id = "fv"+f_id[key];
		var txt = f_value[key];
		aktuTD.title = txt;
		aktuTD.appendChild(parent.form_menu.document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);
		
		var aktuTD = parent.form_menu.document.createElement("td");
		aktuTD.style.width = "15px";
		var aktuIMG = parent.form_menu.document.createElement('i');
		addEvent(aktuIMG,"click", parent.form_menu.delete_element);
		aktuIMG.id = "fi"+f_id[key];
		aktuIMG.className = 'lmb-icon lmb-trash';
		aktuTD.appendChild(aktuIMG);

		aktuTR.appendChild(aktuTD);

	}
	kontainer.appendChild(el_tab);
}


function create_list(){
	if(parent.form_menu){
		del_body(parent.form_menu.document.getElementById("itemlist_area"));
		make_list(parent.form_menu.document.getElementById("itemlist_area"));
	}
}


function el_to_front(dir){
	$(".ui-selected").filter('[id^="div"]').each(function() {
		obj = $( this ).get(0);
		obj.style.zIndex = dir;
	});

	document.form_menu.ZIndex.value = dir;
	if(currentdiv){
		document.getElementById(currentdiv).style.zIndex = dir;
		document.getElementById(currentdiv).style.opacity= 0.7;
		fill_posxy(currentdiv);
	}
}

function set_multiselect_val(styleid,el){
	var val = "";
	var sep = "";
	for (var i = 0; i < el.length; i++){
		if(el[i].selected){
			val += sep + el[i].value;
			sep = "_";
		}
	}
	fill_style(styleid,'',val);
}

function el_change_id(id){
	document.form1.change_id.value = id;
	document.form1.submit();
}

function formBodyClick(evt){
	//window.setTimeout("divclose()", 500);
	//document.getElementById("menu").style.zIndex = zIndexTop + 10000;
}

// show multimenu if more selected
function lmb_multiMenu(evt,ui){	
	divclose();
	divclose();
	if(lmbGetUISelected()){
		// remove hauptmenu/tabulator/gruppierungsrahmen/uform from selection
		$(".ui-selected").filter('[lmbtype="frame"], [lmbtype="menue"], [lmbtype="tabulator"], [lmbtype="uform"]').removeClass("ui-selected");

		// remove non-visible elements (e.g. in other tabs)
		$(".ui-selected").not(":visible").removeClass("ui-selected");

                // touchedElement is the element on which the selection was started (mousedown)
                // remove all elements that are not children of touchedElement
		if(touchedElement && touchedElement.id){
                        $(".ui-selected").not($(touchedElement).find('*')).removeClass("ui-selected");
                }
                
                var selectedElement = $(".ui-selected").filter('[id^="div"]').get(0);
		if(selectedElement && selectedElement.onmousedown){
			selectedElement.onmousedown();
		}
	}
        
        // reset touched element, so that it can be assigned in the next event
        touchedElement = null;
}


/* --- Element-Aktivierung ----------------------------------- */
function aktivate(evt,el,id,TYP) {
	divid = "div"+id;
	picid = "pic"+id;
	var ctrlk = 0;

	if(TYP == 'tab' || TYP == 'bild' || TYP == 'frame'|| TYP == 'tabulator'|| TYP == 'menue'|| TYP == 'scroll'|| TYP == 'reminder'|| TYP == 'wflhist' || TYP == 'chart' || TYP == 'menue'){
		$('#innerframe').selectable("disable");
	}
	document.onmousedown = startDrag;
	document.form1.aktiv_id.value = divid;
	if(divid){currentdiv = divid;}else{currentdiv = null;}
	if(TYP){currenttyp = TYP;}else{currenttyp = null;}
	if(TYP == "bild"){currentpic = picid;}else{currentpic = null;}
	currentel = document.getElementById(currentdiv);

	// Focus setzen
	enterfocus();

	if(jsvar["form_id"]){
		if(parent.form_menu.document.form1.set_zindex.checked == 1){
			zIndexTop++;
			document.getElementById(divid).style.zIndex = zIndexTop;
			document.getElementById('ZIndex').value = zIndexTop;
		}
	}

	//resize-graphik anzeigen
	if(TYP != 'tab' && TYP != 'tabcell'){
		document.getElementById('border_move').style.left = parentsetx(divid) + document.getElementById(divid).offsetWidth - 8;
		document.getElementById('border_move').style.top = parentsety(divid) + document.getElementById(divid).offsetHeight - 8;
		document.getElementById('border_move').style.display = 'inline';
		document.getElementById('border_move').style.zIndex = 999999;
	}else{
		document.getElementById('border_move').style.display = 'none';
	}
	
	// controlKey for multiselect
	if(evt && !evt.ctrlKey){
		// selectable reset all elements
		if(lmbGetUISelected() <= 1){
			$('.ui-selected').removeClass('ui-selected');
		}
	}

	// selectable only for active element
	$("#"+divid).addClass('ui-selected');
	//document.getElementById(divid).style.boxShadow = '3px 3px 3px #619A00';

    limbasDivShow('','','menu');
	
	//Position und Höhe im Menu anzeigen
	parent.form_menu.document.form1.HPOSI.value = parseInt(document.getElementById(divid).style.height);
	parent.form_menu.document.form1.WPOSI.value = parseInt(document.getElementById(divid).style.width);
	parent.form_menu.document.form1.XPOSI.value = parseInt(document.getElementById(divid).style.left);
	parent.form_menu.document.form1.YPOSI.value = parseInt(document.getElementById(divid).style.top);

}

function limbasSetTabulator(id){
        document.form1.aktiv_tabcontainer.value = id;
	if(prevElement != id){
		actel = document.getElementById("div"+id);
		if(prevElement && prevelBorder){
			prevel = document.getElementById("div"+prevElement);
			prevel.style.borderWidth = prevelBorder[0];
			prevel.style.borderStyle = prevelBorder[1];
			prevel.style.borderColor = prevelBorder[2];
		}
		prevElement = id;
		prevelBorder = Array(actel.style.borderWidth,actel.style.borderStyle,actel.style.borderColor);
		// actel.style.boxShadow = '3px 3px 3px #619A00';
	}
}

function lmb_dropEl(lang,id){
	
	if(id){document.form1.form_edit_id.value=id;currentid=id;}
	divclose();
	//var sc = $(".ui-selected").filter('[id^="div"]').length;
	var sc = lmbGetUISelected();
	var bzm = 0;
	var objid = new Array();
	$(".ui-selected").filter('[id^="div"]').each(function() {
		objid[bzm] = $( this ).attr('id').substring(3);
		bzm++;
	});
			
	if(sc > 1){
		if(confirm(lang+'?\n\n'+sc+' items!\n('+objid.join(',')+')')){
			document.form1.form_edit_id.value=objid.join(',');
			document.form1.form_del.value=1;
			set_posxy();
			document.form1.submit();
		}
	}else{
		if(confirm(lang+'?\n\nElement: '+currentid+'\nTyp: '+currenttyp+'\n ')){
			document.form1.form_del.value=1;
			set_posxy();
			document.form1.submit();
		}
	}

}

// ------------- öffne Hauptmenü ----------------
var mainisactive = 0;
var prevElement = 0;
var touchedElement = 0;
var prevelBorder = new Array();
var active_field_type = 0;

function limbasMenuOpen(evt,el,id,dicoParams) {

    // only get the first touched element, not its parents (which happens due to event propagation)
    if(dicoParams.get("MAINELEMENT") && mainisactive){return;}
	mainisactive = 1;
	if(!touchedElement) { touchedElement = el; }
	active_field_type = dicoParams.get("FIELD_TYPE");
	
	TYP = dicoParams.get("TYP");
	STYLE = dicoParams.get("STYLE");
	EVENT1 = decodeURIComponent((dicoParams.get("EVENT1") || "").replace(/\+/g, ' '));
	EVENT2 = decodeURIComponent((dicoParams.get("EVENT2") || "").replace(/\+/g, ' '));
	EVENT3 = decodeURIComponent((dicoParams.get("EVENT3") || "").replace(/\+/g, ' '));
	EVENT4 = decodeURIComponent((dicoParams.get("EVENT4") || "").replace(/\+/g, ' '));
	EVENT5 = decodeURIComponent((dicoParams.get("EVENT5") || "").replace(/\+/g, ' '));
	VALUE = dicoParams.get("VALUE");
	PICSTYLE = dicoParams.get("PICSTYLE");
	PICSIZE = dicoParams.get("PICSIZE");
	ADDPAR = dicoParams.get("ADDPAR");
	snap_tabid = dicoParams.get("SNAP_ID");
	params = dicoParams.get("PARAMETERS");
	title = dicoParams.get("TITLE");
	tabCols = dicoParams.get("COLS");
	tabRows = dicoParams.get("ROWS");
	elclass = dicoParams.get("CLASS");
	subelement = dicoParams.get("SUBELEMENT");
	categorie = dicoParams.get("CATEGORIE");
	istabelement = dicoParams.get("IS_TAB_EL");
	
	div = 'div'+id;
	
	window.focus();

	parent.form_menu.document.form1.objekt.value = '';
	document.form1.form_add.value = '';
	document.form1.form_replace_element.value = '';
	document.getElementById("replace_element").checked = ''; 
	
	if(TYP){currenttyp = TYP;}else{currenttyp = null;}
	
	if(div){
		currentdiv = div;
		currentid = div.substr(3,10);
	}
	
	document.form1.aktiv_id.value = div;
	
	// exit container before selectable
	if(evt && (TYP == 'menue' || TYP == 'frame' || TYP == 'tabulator' || TYP == 'uform' || TYP == 'tile') && evt.ctrlKey){
		$(el).removeClass('ui-selected');
		return;
	}
	
    // set new tab
	if(TYP == 'menue' || TYP == 'frame' || TYP == 'tabulator'){
		limbasSetTabulator(id);
	}

	var style = STYLE.split(";");
	arrayReplace(style, setstyle[id]); // merge state from db with (unsaved) state from ui
	selectedInput = null;
        
	// allgemeiner Inhalt
	if(TYP == 'php' || TYP == 'js' || TYP == 'frame') {
        // switch syntax highlighting
        if (TYP === 'php') {
            cmeditor.setOption("mode", "text/x-php");
        } else if(TYP === 'js') {
            cmeditor.setOption("mode", "text/javascript");
        }

        // initially, copy textarea content to codemirror
	    if( $('#fvalue'+id) && $('#fvalue'+id).val() ) {
            selectedInput = $('#fvalue'+id);
	    	cmeditor.setValue(selectedInput.val());

	    } else if( $('#div'+id) && $('#div'+id).val() ) {
	    	selectedInput = $('#div'+id);
            cmeditor.setValue(selectedInput.val());
		}

	}else{
		$('#lmb_subform_value').val('');
		$('#menu_value').hide();
	}
	
	// bilder Infos
	if(TYP == 'bild'){
		document.picinfo_form.picinfo_val.value=VALUE;
		if(PICSTYLE){
			var picstyle = PICSTYLE.split(";");
			var psval = new Array();
			for (var i in picstyle){
				var pstel = picstyle[i].split(":");
				psval[i] = picstyle[i];
			}
			document.pichistory_form.pichistory.value = psval.join('\n');
		}
	}

	// CSS CLASS
	if(document.form_menu.input_class){
		if(elclass){
			document.form_menu.input_class.value = '';
			document.form_menu.input_class.value = elclass;
		}else{
			document.form_menu.input_class.value = '';
		}
	}

	// Style sonstiges anzeigen
	document.form1.form_edit_id.value=id;
	document.form_menu.id.value=id;
	document.form_menu.input_id.value = id;
	document.form_menu.input_infotable.value = dicoParams.get("TABLE_NAME");
	document.form_menu.input_infofield.value = dicoParams.get("FIELD_NAME");

	document.form_menu.ZIndex.value = document.getElementById(div).style.zIndex;
	document.form_menu.input_fontface.value = document.getElementById(div).style.fontFamily;
	document.form_menu.input_fontsize.value = document.getElementById(div).style.fontSize;
	document.fstyle_form.input_fontvalign.value = document.getElementById(div).style.fontValign;
	var borderstyle = document.getElementById(div).style.borderStyle.split(' ');
	document.form_menu.input_borderstyle.value = borderstyle[0];

	if(EVENT1){document.event_form1.event1.value = EVENT1;}else{document.event_form1.event1.value = "";}
	if(EVENT2){document.event_form2.event2.value = EVENT2;}else{document.event_form2.event2.value = "";}
	if(EVENT3){document.event_form3.event3.value = EVENT3;}else{document.event_form3.event3.value = "";}
	if(EVENT4){document.event_form4.event4.value = EVENT4;}else{document.event_form4.event4.value = "";}
	if(EVENT5){document.event_form5.event5.value = EVENT5;}else{document.event_form5.event5.value = "";}
	if(document.form_menu.input_subelement){if(subelement){document.form_menu.input_subelement.value = subelement;}else{document.form_menu.input_subelement.value = '0';}}
	if(document.form_menu.input_categorie){if(categorie){document.form_menu.input_categorie.value = categorie;}else{document.form_menu.input_categorie.value = '0';}}
	if(PICSIZE && TYP == 'dbdat'){document.form_menu.input_formid.value = PICSIZE;}else{document.form_menu.input_formid.value = '';}
	
	if(style[25] == 'true'){document.form_menu.input_line_reverse.checked = 1;}else{document.form_menu.input_line_reverse.checked = 0;}
	if(style[35]){document.form_menu.input_readonly.value = style[35];}else{document.form_menu.input_readonly.value = '0';}
	if(style[22]){document.form_menu.input_tabpadding.value = style[22];}else{document.form_menu.input_tabpadding.value = '';}
    if(style[28]){document.form_menu.input_tabmargin.value = style[28];}else{document.form_menu.input_tabmargin.value = '';}
	if(style[24]){document.form_menu.input_overflow.value = style[24];}else{document.form_menu.input_overflow.value = '';}
	if(style[26]){document.form_menu.input_display.value = style[26];document.form_menu.input_tile.value = style[26];}else{document.form_menu.input_display.value = '';document.form_menu.input_tile.value = '';}
	if(style[25]){document.form_menu.input_opacity.value = style[25];}else{document.form_menu.input_opacity.value = '';}
	if(style[30]){document.form_menu.input_tabrows.value = style[30];}
	if(style[31]){document.form_menu.input_tabcols.value = style[31];}
	if(style[32]){document.form_menu.input_collapse.value = style[32];}
	if(style[34]){document.form_menu.input_cellstyle.value = style[34];}
	if(style[36]){document.form_menu.input_maxlenght.value = style[36];}else{document.form_menu.input_maxlenght.value = '';}
	if(style[37]){document.form_menu.input_rowspan.value = style[37];}else{document.form_menu.input_rowspan.value = '';}
	if(style[38]){document.form_menu.input_tabuItemPos.value = style[38];}else{document.form_menu.input_tabuItemPos.value = 1;}
	if(style[39]){document.form_menu.input_tabuItemMemPos.value = style[39];}else{document.form_menu.input_tabuItemMemPos.value = 1;}
	if(style[27]){document.form_menu.input_borderradius.value = style[27];}else{document.form_menu.input_borderradius.value = '';}


	// Zusatzparameter
	if(ADDPAR){
		var addpars = ADDPAR.split('#');
		for (var i in addpars){
			var par = addpars[i].split('*');
			// select,input
			if(par[0] == 1 && par[3]){
				eval("document."+par[1]+"."+par[2]+".value = '"+par[3]+"'");
			// checkbox
			}else if(par[0] == 2 && par[3]){

			// multiselect
			}else if(par[0] == 3 && par[3]){
				var selval = par[3].split('_');
				for (var e in selval){
					//alert(eval("document."+par[1]+"."+par[2]+".options"));
					eval("if(document."+par[1]+"."+par[2]+".options["+selval[e]+"]){ok = 1;}else{ok = 0;}");
					if(ok){
						eval("document."+par[1]+"."+par[2]+".options["+selval[e]+"].selected = true");
					}
				}
			}
		}
	}

	// params
	if(params) {
		document.getElementsByName("lmb_subform_params")[0].value = params;
	} else {
		document.getElementsByName("lmb_subform_params")[0].value = "";
	}
	// title
	if(title) {
		document.getElementsByName("lmb_subform_title")[0].value = title;
	} else {
		document.getElementsByName("lmb_subform_title")[0].value = "";
	}

	var tmp = null;
	// Borderstyle anzeigen
	if(browser_ns5){
		tmp = document.getElementById(div).style.borderWidth.split(' ');
		tmpc = parseInt(tmp[0]);
		if(tmpc > 0){
			document.form_menu.input_borderwidth.value = tmp[0];
		}else{
			document.form_menu.input_borderwidth.value = '0px';
		}
	}else{
		if(parseInt(document.getElementById(div).style.borderWidth)){
			document.form_menu.input_borderwidth.value = document.getElementById(div).style.borderWidth;
		}else{
			document.form_menu.input_borderwidth.value = '';
		}
	}

	var el1 = document.getElementById(div).style;
	document.form_menu.borderLeft.checked = el1.borderLeft.indexOf('none') < 0;
	document.form_menu.borderRight.checked = el1.borderRight.indexOf('none') < 0;
	document.form_menu.borderTop.checked = el1.borderTop.indexOf('none') < 0;
	document.form_menu.borderBottom.checked = el1.borderBottom.indexOf('none') < 0;

	// Textstyle anzeigen
	if(document.getElementById(div).style.fontStyle){document.fstyle_form.input_fontstyle.value = document.getElementById(div).style.fontStyle;}else{document.fstyle_form.input_fontstyle.selectedIndex = 0;}
	if(document.getElementById(div).style.fontWeight){document.fstyle_form.input_fontweight.value = document.getElementById(div).style.fontWeight;}else{document.fstyle_form.input_fontweight.selectedIndex = 0;}
	if(document.getElementById(div).style.textDecoration){document.fstyle_form.input_fontdeco.value = document.getElementById(div).style.textDecoration;}else{document.fstyle_form.input_fontdeco.selectedIndex = 0;}
	if(document.getElementById(div).style.textTransform){document.fstyle_form.input_fonttransf.value = document.getElementById(div).style.textTransform;}else{document.fstyle_form.input_fonttransf.selectedIndex = 0;}
	if(document.getElementById(div).style.textAlign){document.fstyle_form.input_fontalign.value = document.getElementById(div).style.textAlign;}else{document.fstyle_form.input_fontalign.selectedIndex = 0;}
	if(document.getElementById(div).style.verticalAlign){document.fstyle_form.input_fontvalign.value = document.getElementById(div).style.verticalAlign;}else{document.fstyle_form.input_fontvalign.selectedIndex = 0;}
	if(parseInt(document.getElementById(div).style.lineHeight) - parseInt(document.getElementById(div).style.fontSize)){
		document.fstyle_form.input_lineheight.value = parseInt(document.getElementById(div).style.lineHeight) - parseInt(document.getElementById(div).style.fontSize) + 'px';
	}else{
		document.fstyle_form.input_lineheight.value = "";
	}
	if(parseInt(document.getElementById(div).style.letterSpacing)){
		document.fstyle_form.input_letterspacing.value = document.getElementById(div).style.letterSpacing;
	}else{
		document.fstyle_form.input_letterspacing.value = "";
	}
	if(parseInt(document.getElementById(div).style.wordSpacing)){
		document.fstyle_form.input_wordspacing.value = document.getElementById(div).style.wordSpacing;
	}else{
		document.fstyle_form.input_wordspacing.value = "";
	}
	//document.fstyle_form.input_textshadow.value = document.getElementById(div).style.textShadow;

	//Typanzeige im Hauptmenü
        parent.form_menu.resetmenu();
	if(parent.form_menu.document.getElementById(TYP)){
		parent.form_menu.document.getElementById(TYP).style.backgroundColor = color[7];
	}
	
    // select multiple if ctrl key is pressed
    if((evt && evt.ctrlKey) || lmbGetUISelected()){
        var OLDTYP = TYP;
		var TYP = 'multi';
	}

	//Anzeigeoptionen im Menü bei unterschiedlichen Typen
	var cc = null;
	var ar = document.getElementById('menu').getElementsByTagName("tr");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,4) == "menu"){
			//var idval = cc.id.split("_");
			if(istabelement){
				if(cc.id.indexOf('_'+TYP+'_') > 0 && cc.id.indexOf('onlynotab') == -1){
					cc.style.display='';
				}else{
					cc.style.display='none';
				}
			}else{
				if(cc.id.indexOf('_'+TYP+'_') > 0 && cc.id.indexOf('onlytab') == -1){
					cc.style.display='';
				}else{
					cc.style.display='none';
				}
			}
		}
	}
	
	
	if(move_id.length <= 0){
		document.getElementById("form_movemenu").parentNode.style.display='none';
	}

	if(TYP=="tab")
	{
		sel = limbasGlobalGetIndexofValueInSelect("input_tabcols",tabCols);
		document.getElementById("input_tabcols").options[sel].selected=true;
		sel = limbasGlobalGetIndexofValueInSelect("input_tabrows",tabRows);
		document.getElementById("input_tabrows").options[sel].selected=true;
	}
	//Listenmarkierung setzten
	var cc = null;
	var ar = parent.form_menu.document.getElementById('itemlist_area').getElementsByTagName("td");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id == "fi" + id || cc.id == "fk" + id  || cc.id == "fv" + id ){
                        cc.style.backgroundColor = color[10].substring(1,7);//color[10]
		}else if(cc.id.substring(0,1) == "f"){
			cc.style.backgroundColor = color[8];
		}
	}
	
	if(TYP != 'tabuItem' && TYP != 'categorieItem'){
		// prevent activation of tabuItems etc.
		if(TYP != 'multi' || (OLDTYP != 'tabuItem' && OLDTYP != 'categorieItem')) {
				aktivate(evt,el,id,TYP);
		}

	}else{
		limbasDivShow('','','menu');
		parent.form_menu.document.form1.HPOSI.value = parseInt(document.getElementById(div).style.height);
		parent.form_menu.document.form1.WPOSI.value = parseInt(document.getElementById(div).style.width);
		parent.form_menu.document.form1.XPOSI.value = '';
		parent.form_menu.document.form1.YPOSI.value = '';
		fill_posxy(div);
	}
	
	if(TYP == 'tabcell'){
		act_tabelement(id,dicoParams.get("CELL_TAB"),dicoParams.get("CELL_ROW"),dicoParams.get("CELL_COL"));
		parent.form_menu.document.form1.HPOSI.value = parseInt(document.getElementById(div).offsetHeight);
		parent.form_menu.document.form1.WPOSI.value = parseInt(document.getElementById(div).offsetWidth);
	}
	
	// edit: uncommented to support codemirror
	//Focus auf Element setzen (wichtig für Texteingabe bei Mozilla)
	// setTimeout("enterfocus()",200);
    document.getElementById(currentdiv).focus();
}

// bei ENTER fokus()
function enterfocus(){
	if((currenttyp == 'text' || currenttyp == 'php' || currenttyp == 'inptext' || currenttyp == 'submt' || currenttyp == 'button' || currenttyp == 'inphidden' || currenttyp == 'inparea' || currenttyp == 'inpselect' || currenttyp == 'inpcheck' || currenttyp == 'inpradio' || currenttyp == 'js') && currentdiv){
		document.getElementById(currentdiv).focus();
	}
}