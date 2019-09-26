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
var resizeobj = null;
var activ_menu = 0;
var currobj = new Array();
var view_tab = new Array();

if(browser_ns5){document.captureEvents(Event.MOUSEDOWN | Event.MOUSEUP);}
document.onmouseup = endDrag;

function startDrag(evt) {
	dx = new Array();
	dy = new Array();
	
	if(currentdiv != 'menu'){
		$("#menu").removeClass('ui-selected');
		current = document.getElementById(currentdiv);
	}
	
	if($(".ui-selected").filter('[id^="div"]').length > 1){
		var multiselect = 1;
	}else{
		var multiselect = 0;
	}
	
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

	if(!multiselect && parent.report_menu.document.form1.set_zindex.checked == 1){
		current.style.zIndex = zIndexTop;
	}

	if(browser_ns5){document.captureEvents(Event.MOUSEMOVE)}
	document.onmousemove = drag;
	
	return false;
}

function drag(evt){
	
	if(currentdiv != 'menu'){
		$("#menu").hide();
	}
	
	if(currentdiv == 'menu'){
		dragEl(evt,document.getElementById('menu'));
	}else{
		$(".ui-selected").each(function() {
			dragEl(evt,$( this ).get(0));
		});
	
	}
}

function dragEl(evt,el) {

	if (objid = el.id) {
		
		if(parent.report_menu.document.form1.raster.value > 0){var raster = parent.report_menu.document.form1.raster.value;}
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
			parent.report_menu.document.form1.XPOSI.value = parseInt(cleft);
			parent.report_menu.document.form1.YPOSI.value = parseInt(ctop);
		}
	}
	return false;
}

function endDrag(e) {
	
	// Schleife über alle selectable elemente
	$(".ui-selected").each(function() {
		fill_posxy($( this ).attr('id'));
	});
	
	// enable selectable function
	$('#innenramen').selectable("enable");
	
	$("#menu").not(":visible").show('fast');
	
	if(browser_ns5){document.releaseEvents(Event.MOUSEMOVE);}
	document.onmousemove = null;
	document.onmousedown = null;
	current = null;
	eltd = null;
	eltb = null;
	mainisactive = null;
	
	if(document.getElementById(currentdiv)){
		document.getElementById('border_move').style.left = parentsetx(currentdiv) + document.getElementById(currentdiv).offsetWidth - 4;
		document.getElementById('border_move').style.top = parentsety(currentdiv) + document.getElementById(currentdiv).offsetHeight - 4;
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
	
	if(currentdiv){current_div = document.getElementById(currentdiv).style;}
	if(currentpic){current_pic = document.getElementById(currentpic);}
	ex = parseInt(document.getElementById(currentdiv).offsetWidth);
	ey = parseInt(document.getElementById(currentdiv).offsetHeight);
	xy = (ex / ey);
	if(parent.report_menu.document.form1.set_zindex.checked == 1){
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
		
		if(parent.report_menu.document.form1.raster.value > 0){var raster = parent.report_menu.document.form1.raster.value;}
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
			if(parent.report_menu.document.form1.prop.checked == 1 || shftk){
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

		parent.report_menu.document.form1.HPOSI.value = parseInt(current_div.height);
		parent.report_menu.document.form1.WPOSI.value = parseInt(current_div.width);
	}
	return false;
}

function resize_pic(evt) {
	if (current != null) {
		if(browser_ns5){
			if(evt.pageY - py + ey > 0 && evt.pageX - px + ex > 0){
				if(parent.report_menu.document.form1.prop.checked == 1 || evt.shiftKey){
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
				if(parent.report_menu.document.form1.prop.checked == 1){
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
		parent.report_menu.document.form1.HPOSI.value = current_pic.height;
		parent.report_menu.document.form1.WPOSI.value = current_pic.width;
	}
	return false;
}


var tdwidth = null;
var tbwidth = null;
var posi = null;
var eltd = null;
var eltb = null;
var elts = null;
var eltc = null;


var tdw = null;
var tbw = null;


function startDragTd(evt,tabid,tdid,divid,id) {
	eltd = document.getElementById(tdid);
	eltb = document.getElementById(tabid);
	elts = document.getElementById(divid);
	eltc = tdid.split("_");

	// disable selectable function
	$('#innenramen').selectable("disable");
	
	tdwidth = eltd.offsetWidth;
	tbwidth = eltb.offsetWidth;
	if(browser_ns5){
		posi = evt.pageX;
	}else{
		posi = window.event.clientX;
	}
	document.onmousemove = dragTd;

	return false;
}

function dragTd(e) {
	if(browser_ns5){
		var tdw = e.pageX - posi + tdwidth;
		var tbw = e.pageX - posi + tbwidth;
	}else{
		var w = window.event.clientX - posi;
		var tdw = w + tdwidth;
		var tbw = w + tbwidth;
	}
	if(tdw > 0 && tbw > 0){
		for (var i = 0; i < eltb.rows.length; i++){
			var tdel = document.getElementById('tab_el_'+eltc[2]+'_'+i+'_'+eltc[4]);
			if(tdel){
				if (tdel.hasChildNodes()){
					var cn = tdel.childNodes;
					var cnl = cn.length;

					for (var e=0; e<cnl; e++) {
						if(cn.item(e).style){
							cn.item(e).style.width = tdw;
						}
					}
				}
				tdel.style.width = tdw;
			}
		}
	}
	return false;
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
	var len = $(".ui-selected").filter('[lmbselectable="1"]').length;
	//var len = $(".ui-selected").filter('[id^="div"]').length;
	if(len > 1){
		return len;
	}else{
		return false;
	}
}

/* --- Menu-Aktivierung ----------------------------------- */
function aktivate_menu(ID) {
	currentdiv = ID;
	
	// selectable reset all elements
	//$('[id^="div"]').removeClass('ui-selected');
	
	// selectable only for active element
	$("#"+currentdiv).addClass('ui-selected');
	
	document.onmousedown = startDrag;
}


/* --- Element-Aktivierung ----------------------------------- */
function aktivate_resize(evt) {
	document.onmousedown = startResize;
}

/* --- Tabellen-Element hinzufügen (Feld aktivieren) ----------------------------------- */
function act_tabelement(dv,TAB,ROW,COL){

	document.form1.report_tab.value = TAB;
	document.form1.report_tab_el.value = ROW+";"+COL;
	parent.report_menu.document.form1.report_tab.value = TAB;
	parent.report_menu.document.form1.report_tab_el.value = ROW+";"+COL

}

//Tabellen-Spaltenbreite berechnen
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
	document.form1.report_tab_size.value = val;
}



// -------------- Position/Gößen-Bestimmung ----------------------
// Array mit position und größe bei 'endDrag()' und von 'posxy_change()'
var posxy = new Array();
var setstyle = new Array();
function fill_posxy(ID) {
	if(ID){
		var div = ID.substr(3,999);
		posxy[div] = div + "," + parseInt(document.getElementById(ID).style.left) + "," + parseInt(document.getElementById(ID).style.top) + "," + parseInt(document.getElementById(ID).style.width) + "," +  parseInt(document.getElementById(ID).offsetHeight) + "," +  document.getElementById(ID).style.zIndex;
	}
}
// Array in var report_possize eintragen bei post()
function set_posxy() {
	for (var i in posxy){
		document.form1.report_possize.value = document.form1.report_possize.value + ";" + posxy[i];
	}
	document.form1.menuposx.value = parseInt(document.getElementById('menu').style.left);
	document.form1.menuposy.value = parseInt(document.getElementById('menu').style.top);
	set_style();
	set_tabcols();
	set_viewtab();
}
//Position-änderung bestimmen und fill_posxy() aufrufen
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
// --- Größen-änderung bestimmen und fill_posxy() aufrufen
function sizexy_change(X,Y) {
	$(".ui-selected").each(function() {
		el = $( this ).get(0);
		if(el.id == 'menu'){return;}
		if(X){el.style.height = parseInt(X);}
		if(Y){el.style.width = parseInt(Y);}
		
		fill_posxy(el.id);
		if(!lmbGetUISelected()){return false;}
	});
}
// Position-Änderung über Tastatureingabe
function movex(evt) {
	if(document.form1.aktiv_id.value && evt.shiftKey){
		
		$(".ui-selected").each(function() {
			el = $( this ).get(0);
			if(el.id == 'menu'){return;}

			POSY = parseInt(el.style.top);
			POSX = parseInt(el.style.left);
	
			if(evt.keyCode == 38){
				POSY = (POSY - 1);
				el.style.top = POSY;
				document.getElementById('border_move').style.top = (parseInt(document.getElementById('border_move').style.top) - 1);
				parent.report_menu.document.form1.YPOSI.value = POSY;
			}
			if(evt.keyCode == 40){
				POSY = (POSY + 1);
				el.style.top = POSY;
				document.getElementById('border_move').style.top = (parseInt(document.getElementById('border_move').style.top) + 1);
				parent.report_menu.document.form1.YPOSI.value = POSY;
			}
			if(evt.keyCode == 37){
				POSX = (POSX - 1);
				el.style.left = POSX;
				document.getElementById('border_move').style.left = (parseInt(document.getElementById('border_move').style.left) - 1);
				parent.report_menu.document.form1.XPOSI.value = POSX;
			}
			if(evt.keyCode == 39){
				POSX = (POSX + 1);
				el.style.left = POSX;
				document.getElementById('border_move').style.left = (parseInt(document.getElementById('border_move').style.left) + 1);
				parent.report_menu.document.form1.XPOSI.value = POSX;
			}

			fill_posxy(el.id);
		});
	}

	if(evt.keyCode == 13 && !evt.shiftKey){
		setTimeout("enterfocus()",200);
	}
}



// ---- Farbauswahl ---------
function set_color(val){
	fill_style(colorid,colorstyle,val)
}



// -------------- Style-Bestimmung ----------------------
// Stylebehandlung Eintrag in Array
function fill_style(STYLE_ID,STYLE,VAL) {
	var ID = document.form1.report_edit_id.value;
	var div = "div"+ID;
	var elval = VAL;
	el = document.getElementById(div).style;
	
	if($(".ui-selected").filter('[id^="div"]').length > 1){
		var multiselect = 1;
	}else{
		var multiselect = 0;
	}
	
	// Schleife über alle selectable elemente
	$(".ui-selected").filter('[id^="div"]').each(function() {
		div = $( this ).attr('id');
		ID = div.substr(3,10);
		el = document.getElementById(div).style;
		if(!setstyle[ID]){setstyle[ID] = new Array();}

        switch (STYLE) {
            case "":
                break;
            case "lineHeight":
                if (!VAL) {
                    VAL = 0;
                }
                VAL = parseFloat(el.fontSize) + parseFloat(VAL);
                VAL = VAL + "px";
                el.lineHeight = VAL;
                break;
            case "border":
                if (VAL && VAL != 'none') {
                    if (document.getElementById(VAL).checked) {
                        el[VAL] = '';
                        VAL = '';
                    } else {
                        el[VAL] = 'none';
                        VAL = 'none';
                    }
                }
                break;
            default:
                eval("el." + STYLE + "='" + VAL + "';");
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

		// Zusatz header/footer
		if(STYLE_ID == 28 && VAL){document.getElementById('input_head').checked=0;fill_style(27,'head',0);}
		if(STYLE_ID == 27 && VAL){document.getElementById('input_foot').checked=0;fill_style(28,'foot',0);}
		
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

// Array in var report_setstyle eintragen bei post()
function set_style() {
	var val = "";
	for (var i in setstyle){
		var val = val + "#;" + i + "#,";
		for (var e in setstyle[i]){
			var val = val + e + "#|" + setstyle[i][e] + "#,";
		}
	}
	document.form1.report_setstyle.value = val;
}

// Array view_tab eintragen bei post()
function set_viewtab() {
	var val = "";
	for (var i in view_tab){
		if(view_tab[i] == 'true'){
			var val = val + ";" + i;
		}
	}
	document.form1.report_view_tab.value = val;
}

var bigInputEdit;
// bei ENTER fokus()
function enterfocus(evt){
	if((currenttyp == 'text' || currenttyp == 'templ' || ((currenttyp == 'formel' || currenttyp == 'dbdat') && !bigInputEdit)) && currentdiv){document.getElementById(currentdiv).focus();}
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

function reportBodyClick(){
	window.setTimeout("divclose()", 250);
	document.getElementById("menu").style.zIndex = zIndexTop + 10000;
}

// show multimenu if more selected
function lmb_multiMenu(evt){
	divclose();
	divclose();
	if(lmbGetUISelected()){
		if($(".ui-selected").filter('[id^="div"]').get(0).onmousedown){
			$(".ui-selected").filter('[id^="div"]').get(0).onmousedown();
		}
	}
}

/* --- Element-Aktivierung ----------------------------------- */
//function aktivate(ID,PICID,TYP) {
function aktivate(evt,el,ID,TYP,tab_element) {
	divid = "div"+ID;
	picid = "pic"+ID;

	// disable selectable function
	if(evt) {
        $('#innenramen').selectable("disable");
    }

	document.onmousedown = startDrag;
	document.form1.aktiv_id.value = divid;
	if(divid){currentdiv = divid;}else{currentdiv = null;}
	if(TYP){currenttyp = TYP;}else{currenttyp = null;}
	if(TYP == "bild"){currentpic = picid;}else{currentpic = null;}

	// Focus setzen
	enterfocus();
	
	//erst alle Ramen entfernen
	if(jsvar["report_id"]){
		if(parent.report_menu.document.form1.set_zindex.checked == 1){
			zIndexTop++;
			document.getElementById(divid).style.zIndex = zIndexTop;
		}
	}
	
	//resize-graphik anzeigen
	if(TYP != 'tab' && tab_element <= 0){
		document.getElementById('border_move').style.left = parentsetx(divid) + document.getElementById(divid).offsetWidth - 4;
		document.getElementById('border_move').style.top = parentsety(divid) + document.getElementById(divid).offsetHeight - 4;
		document.getElementById('border_move').style.display = '';
		document.getElementById('border_move').style.zIndex = zIndexTop+1;
	}

	
	// controlKey for multiselect
	if(!evt || (evt && !evt.ctrlKey)){
		// active shadow
		var cc = null;
		var ar = document.getElementsByTagName("*");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			cc.style.boxShadow='';
		}
		// selectable reset all elements
		if($(".ui-selected").filter('[id^="div"]').length <= 1){
			$('[id^="div"]').removeClass('ui-selected');
			$('#menu').removeClass('ui-selected');
		}
	}
		
	// selectable only for active element
	$("#"+divid).addClass('ui-selected');
	document.getElementById(divid).style.boxShadow = '3px 3px 3px #619A00';
	
	limbasDivShow('','','menu');

	
	// Tabellenzelle
	if(tab_element > 0){
		//Zellenbreite
		$("#input_tr_width").val($("#"+currentdiv).closest('td').width()+1);
		//Position und Höhe im Menu anzeigen
		parent.report_menu.document.form1.HPOSI.value = '';
		parent.report_menu.document.form1.WPOSI.value = '';
		parent.report_menu.document.form1.XPOSI.value = '';
		parent.report_menu.document.form1.YPOSI.value = '';
	}else{
		$("#input_tr_width").val('');
		parent.report_menu.document.form1.HPOSI.value = parseInt(document.getElementById(divid).offsetHeight);
		parent.report_menu.document.form1.WPOSI.value = parseInt(document.getElementById(divid).offsetWidth);
		parent.report_menu.document.form1.XPOSI.value = parseInt(document.getElementById(divid).style.left);
		parent.report_menu.document.form1.YPOSI.value = parseInt(document.getElementById(divid).style.top);
	}
}

// ------------- öffne Hauptmenü ----------------
var mainisactive = 0;
function limbasMenuOpen(evt,el,ID,STYLE,TYP,VALUE,PICSTYLE,tab_element,dbdat_table,dbdat_field,TAB,ROW,COL) {

    // only get the first touched element, not its parents (which happens due to event propagation)
    if(TAB && mainisactive){return;}
	mainisactive = 1;

	div = 'div'+ID;
	window.focus();

	if(TYP){currenttyp = TYP;}else{currenttyp = null;}
	if(div){currentdiv = div;}
	document.form1.aktiv_id.value = div;
	document.form1.report_replace_element.value = '';
	document.getElementById("replace_element").checked = ''; 

	var fullborder = 0;
	var style = STYLE.split(";");

	//if(TYP == 'formel'){document.value_form.val.value=VALUE;}
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

	// Style sonstiges anzeigen
	document.form1.report_edit_id.value=ID;
	document.form_menu.id.value=ID;
	document.form_menu.input_info.value = jsvar["lng_1099"]+': '+ID;
	document.form_menu.input_infotable.value = dbdat_table;
	document.form_menu.input_infofield.value = dbdat_field;
	
	document.form_menu.ZIndex.value = 'zIndex: '+document.getElementById(div).style.zIndex;
	document.form_menu.input_fontface.value = document.getElementById(div).style.fontFamily;
	document.form_menu.input_fontsize.value = document.getElementById(div).style.fontSize;
	document.form_menu.input_tabpadding.value = document.getElementById(div).style.padding;
	document.fstyle_form.input_fontvalign.value = document.getElementById(div).style.fontValign;

	if(style[25] == 'true'){document.form_menu.input_line_reverse.checked = 1;}else{document.form_menu.input_line_reverse.checked = 0;}
	if(style[33] == 'true'){document.form_menu.input_list.checked = 1;}else{document.form_menu.input_list.checked = 0;}
	if(style[27] == 'true'){document.form_menu.input_head.checked = 1;}else{document.form_menu.input_head.checked = 0;}
	if(style[28] == 'true'){document.form_menu.input_foot.checked = 1;}else{document.form_menu.input_foot.checked = 0;}
	if(style[26] == 'true'){document.form_menu.input_prop.checked = 1;}else{document.form_menu.input_prop.checked = 0;}
	if(style[35] == 'true'){document.form_menu.input_breaklock.checked = 1;}else{document.form_menu.input_breaklock.checked = 0;}
	if(style[41] == 'true'){document.form_menu.input_tagmode.checked = 1;}else{document.form_menu.input_tagmode.checked = 0;}
	if(style[42] == 'true'){document.form_menu.input_html.checked = 1;}else{document.form_menu.input_html.checked = 0;}

	//if(style[22]){document.form_menu.input_tabpadding.value = style[22];}else{document.form_menu.input_tabpadding.value = '';}
	if(style[29]){document.form_menu.input_hide.value = style[29];}else{document.form_menu.input_hide.value = 0;}
	if(style[30]){document.form_menu.input_tabrows.value = style[30];}
	if(style[31]){document.form_menu.input_tabcols.value = style[31];}
	if(style[32]){document.form_menu.input_bg.value = style[32];}
	if(style[24]){document.form_menu.input_opacity.value = style[24];}else{document.form_menu.input_opacity.value = '';}
	if(style[34]){document.form_menu.input_pagebreak.value = style[34];}else{document.form_menu.input_pagebreak.value = '0';}
	if(style[36]){document.form_menu.input_relativepos.value = style[36];}else{document.form_menu.input_relativepos.value = '0';}
	if(style[37]){document.form_menu.input_colspan.value = style[37];}else{document.form_menu.input_colspan.value = '0';}
	//if(style[38]){document.form_menu.input_rowspan.value = style[38];}else{document.form_menu.input_rowspan.value = '0';}
	if(style[39]){document.form_menu.input_relativedisplay.value = style[39];}else{document.form_menu.input_relativedisplay.value = '0';}
	if(style[40]){document.form_menu.input_seperator.value = style[40];}else{document.form_menu.input_seperator.value = '';}
	if(style[43]){document.form_menu.input_rotate.value = style[43];}else{document.form_menu.input_rotate.value = '';}
	if(style[44]){document.form_menu.input_cellstyle.value = style[44];}else{document.form_menu.input_cellstyle.value = '';}


	// first remove all active cell borders
	$( ".activecellborder" ).removeClass( "activecellborder" );

	// Bordestyle
	var borderstyle = document.getElementById(div).style.borderStyle;
	document.form_menu.input_borderstyle.value = '';
	if(borderstyle && borderstyle.indexOf('solid') !== -1) {
        document.form_menu.input_borderstyle.value = 'solid';
    }

    // Boerderwidth
    var tmp = document.getElementById(div).style.borderWidth.split(' ');
	document.form_menu.input_borderwidth.value = '0';
	tmp.reverse();
	if(parseFloat(tmp[0])) {
        document.form_menu.input_borderwidth.value = tmp[0];
    }

	// border show
	var el_ = document.getElementById(div).style;
	if(!el_.borderStyle || el_.borderStyle == 'none' || !el_.borderLeft.indexOf('none') || !el_.borderLeft.indexOf('1px dotted grey')){document.form_menu.borderLeft.checked=0;}else{document.form_menu.borderLeft.checked=1;}
	if(!el_.borderStyle || el_.borderStyle == 'none' || !el_.borderRight.indexOf('none') || !el_.borderRight.indexOf('1px dotted grey')){document.form_menu.borderRight.checked=0;}else{document.form_menu.borderRight.checked=1;}
	if(!el_.borderStyle || el_.borderStyle == 'none' || !el_.borderTop.indexOf('none') || !el_.borderTop.indexOf('1px dotted grey')){document.form_menu.borderTop.checked=0;}else{document.form_menu.borderTop.checked=1;}
	if(!el_.borderStyle || el_.borderStyle == 'none' || !el_.borderBottom.indexOf('none') || !el_.borderBottom.indexOf('1px dotted grey')){document.form_menu.borderBottom.checked=0;}else{document.form_menu.borderBottom.checked=1;}

	// Textstyle anzeigen
	document.fstyle_form.input_fontstyle.value = document.getElementById(div).style.fontStyle;
	document.fstyle_form.input_fontweight.value = document.getElementById(div).style.fontWeight;
	document.fstyle_form.input_fontdeco.value = document.getElementById(div).style.textDecoration;
	document.fstyle_form.input_fonttransf.value = document.getElementById(div).style.textTransform;
	document.fstyle_form.input_fontalign.value = document.getElementById(div).style.textAlign;
	document.fstyle_form.input_fontvalign.value = document.getElementById(div).style.verticalAlign;
	
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
	
	document.getElementById('big_input').value=document.getElementById(currentdiv).value;


	//Typanzeige im Hauptmenü
    var TYP_ = TYP;
    if(TYP == 'tabcell'){TYP_ = 'tab';}
	parent.report_menu.resetmenu();
	parent.report_menu.document.getElementById(TYP_).style.backgroundColor = jsvar["WEB7"];

	if($(".ui-selected").filter('[id^="div"]').length > 1){
		var TYP = 'multi';
	}

	//Anzeigeoptionen im Menü bei unterschiedlichen Typen
	var cc = null;
	var ar = document.getElementById('menu').getElementsByTagName("tr");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,4) == "menu"){
			
			if(tab_element == 1){
				if(cc.id.indexOf('_'+TYP+'_') > 0 && cc.id.indexOf('onlynotab') == -1 || cc.id.indexOf("tabel") > 0){
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
	// show activateParentCell if is inner cellelement
	$('.menu_activateParentCell').hide();
	if($('#div'+ID).parent().hasClass('defaultcellborder')){
	    $('.menu_activateParentCell').show();
    }


	if(TYP=="tab") {
		sel = limbasGlobalGetIndexofValueInSelect("input_tabcols",PICSTYLE);
		document.getElementById("input_tabcols").options[sel].selected=true;
		sel = limbasGlobalGetIndexofValueInSelect("input_tabrows",VALUE);
		document.getElementById("input_tabrows").options[sel].selected=true;
	}


	// add active cell border
	if(TYP=='tabcell'){
	    $('#div'+ID).addClass( "activecellborder" );

		act_tabelement(ID,TAB,ROW,COL);
		parent.report_menu.document.form1.HPOSI.value = parseInt(document.getElementById(div).offsetHeight);
		parent.report_menu.document.form1.WPOSI.value = parseInt(document.getElementById(div).offsetWidth);
	}

	//Listenmarkierung setzten
	var cc = null;
	var ar = parent.report_menu.document.getElementById('itemlist_area').getElementsByTagName("td");
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,1) == "f"){
			cc.style.backgroundColor = '';
		}
	}
	var list_id = "fi"+ID;
	if(parent.report_menu.document.getElementById(list_id)){
		parent.report_menu.document.getElementById(list_id).style.backgroundColor = jsvar["WEB10"];
		var list_id = "fk"+ID;
		parent.report_menu.document.getElementById(list_id).style.backgroundColor = jsvar["WEB10"];
		var list_id = "fv"+ID;
		parent.report_menu.document.getElementById(list_id).style.backgroundColor = jsvar["WEB10"];
	}

	aktivate(evt,el,ID,TYP,tab_element);

	//Focus auf Element setzen (wichtig für Texteingabe bei Mozilla)
	setTimeout("enterfocus()",200);
}


function lmb_dropEl(lang){

	divclose();
	var sc = $(".ui-selected").filter('[id^="div"]').length;
	var bzm = 0;
	var objid = new Array();
	$(".ui-selected").each(function() {
		objid[bzm] = $( this ).attr('id').substring(3);
		bzm++;
	});
			
	if(sc > 1){
		if(confirm(lang+'?\n\n'+sc+' items!\n('+objid.join(',')+')')){
			document.form1.report_edit_id.value=objid.join(',');
			document.form1.report_del.value=1;
			set_posxy();
			document.form1.submit();
		}
	}else{
		if(confirm(lang+'?\n\nElement: '+currentdiv.substr(3,10)+'\nTyp: '+currenttyp+'\n ')){
			document.form1.report_del.value=1;
			set_posxy();
			document.form1.submit();
		}
	}

}



/* --- Submenüsteuerung ----------------------------------- */
var colorid;
var colorstyle;
function submenu_style(STYLE) {

	if(STYLE){
		var st = STYLE.split(";");
		colorid = st[0];
		colorstyle = st[1];
	}


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
	var el_tab = parent.report_menu.document.createElement("table");
	el_tab.style.margin = "0px";
	el_tab.style.width = "210px";
	el_tab.style.borderCollapse = "collapse";
	var el_body = parent.report_menu.document.createElement("tbody");
	el_tab.appendChild(el_body);

	for (var key in f_id){
		// ---- Zeilen -------
		var aktuTR = parent.report_menu.document.createElement("tr");
		aktuTR.id = "ft"+f_id[key];
		aktuTR.style.cursor = "pointer";
		//addEvent(aktuTR,"click", parent.report_menu.open_details);
		aktuTR.addEventListener("click", function(e){parent.report_menu.open_details(e)});
		addEvent(aktuTR,"mouseover", parent.report_menu.make_bold);
		addEvent(aktuTR,"mouseout", parent.report_menu.make_unbold);
		el_body.appendChild(aktuTR);

		var aktuTD = parent.report_menu.document.createElement("td");
		aktuTD.style.width = "60px";
		aktuTD.id = "fi"+f_id[key];
		var txt = f_id[key];
		aktuTD.appendChild(parent.report_menu.document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);

		var aktuTD = parent.report_menu.document.createElement("td");
		aktuTD.style.width = "120px";
		aktuTD.id = "fk"+f_id[key];
		var txt = f_typ[key];
		aktuTD.appendChild(parent.report_menu.document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);

		var aktuTD = parent.report_menu.document.createElement("td");
		aktuTD.style.width = "15px";
		aktuTD.id = "fv"+f_id[key];
		var txt = f_value[key];
		aktuTD.appendChild(parent.report_menu.document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);
	}
	kontainer.appendChild(el_tab);
}


function create_list(){
	del_body(parent.report_menu.document.getElementById("itemlist_area"));
	make_list(parent.report_menu.document.getElementById("itemlist_area"));
}

function el_to_front(dir){
	$(".ui-selected").filter('[id^="div"]').each(function() {
		obj = $( this ).get(0);
		obj.style.zIndex = dir;
		document.form_menu.ZIndex.value = dir;
	});

	if(currentdiv){fill_posxy(currentdiv);}
}


function el_to_cell(){

	if(currentdiv){
	    //console.log(currentdiv);
        //console.log($('#'+currentdiv ).parent().attr('id'));

        document.getElementById(currentdiv).parentNode.onmousedown();

    }
}