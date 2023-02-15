/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



var s_link = new Array();

var zIndexTop = 100;
var dx = 0, dy = 0, px = 0, py = 0, ex = 0, ey = 0, ax = 0, ay = 0;
var ityp = 1;
var setdrag = "";
var fieldel = null;

function iniDrag(evt,el,fel) {
	document.onmouseup = endDrag;
    startDrag(evt,el);
    fieldel=fel;
    ityp = 1;
}

function iniResize(evt,el) {
	document.onmouseup = endDrag;
    startDrag(evt,el);
    ityp = 2;
}

function startDrag(evt,el) {
	
	document.body.style.cursor = 'pointer';
	
	if(typeof(el)=="object"){
		el1 = el;
		el2 = 0;
	}else{
		el1 = document.getElementById('tabsh_'+el);
		el2 = document.getElementById('selsh_'+el);
	}


    px = evt.pageX;
    py = evt.pageY;

	
	if(el1.id == 'relationSign'){
		el1.style.visibility = 'visible';
		$(el1).css("left",px+10);
        $(el1).css("top",py+3);
	}
	
	dx = parseInt(el1.offsetLeft);
	dy = parseInt(el1.offsetTop);

	ex = parseInt(el1.offsetWidth);
	ey = parseInt(el1.offsetHeight);
	
	zIndexTop++;
	el1.zIndex = zIndexTop;
	document.onmousemove = drag;

	return false;
}

function drag(evt) {
	if (el1 != null) {
		

        ax = evt.pageX;
        ay = evt.pageY;

		
		// move
		if(ityp == 1){
			if((ax - px + dx) < 0){ax = 1 + px + dx;}
			if((ay - py + dy) < 0){ay = 1 + py + dy;}
			cl = ax - px + dx;
			ct = ay - py + dy;
			if(cl > 0){el1.style.left = cl+'px';}
			if(ct > 0){el1.style.top = ct+'px';}
		// resize
		}else if(ityp == 2){
			if((ax - px + ex) < 100){ax = 100 + px - ex;}
			if((ay - py + ey) < 100){ay = 100 + py - ey;}
			
			
			el1.style.width = (ax - px + ex) + 'px';
			el1.style.height = (ay - py + ey) + 'px';
			
			
			el2.style.width = (el1.style.offsetWidth) + 'px';
			el2.style.height = (ay - py + ey - 50) + 'px';
		}
	}
	//paint_lines();
	return false;
}

function endDrag(evt) {
	document.onmouseup = null;
	document.onmousemove = null;
	document.body.style.cursor = 'default';
	if(document.getElementById('relationSign')){
		document.getElementById('relationSign').style.visibility='hidden';
	}
	
	paint_lines();
	el1 = null;
	fieldel = null;
	
	//lmbAjax_ViewEditorPattern(setdrag)
	
	return false;
}

function setDrag(){
	var ar = document.getElementsByTagName("table");
	setdrag = document.form1.setdrag.value;
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,5) == "tabsh"){
			var size = parseInt(cc.style.width) + "," + parseInt(cc.style.height);
			var pos = parseInt(cc.style.left) + "," + parseInt(cc.style.top);
			var tab = cc.id.substring(6,100);
			setdrag = setdrag+":"+tab+";"+ size + ";" + pos;
		}
	}
	document.form1.setdrag.value=setdrag;
}


// ---------------- Sendkeypress----------------------
var stopEvent = 0;
function sendkeydown(evt) {
	if(evt.keyCode == 13 && !evt.shiftKey && !stopEvent && evt.target.localName !== "textarea"){
		setDrag();
		document.form1.submit();
	}
}

var activ_menu = null;
function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

// --- jsGraphics -----------------------------------
function js_line(tabid,vtabid,fieldid,fel,tel,bzm,dtp){
	
	var tab_el = document.getElementById("tabsh_"+tabid);
	var tab_toel = document.getElementById("tabsh_"+vtabid);
	var from_el = document.getElementById("opt_"+fel);
	var to_el = document.getElementById("opt_"+tel);
	var kont_el = document.getElementById("di_"+bzm);

	if(!from_el || !to_el){return;}
	
	jg = "jg_"+bzm;

    var pos_from_x = from_el.offsetParent.offsetParent.offsetLeft + from_el.offsetLeft + tab_el.offsetWidth + 10;
    var pos_from_y = from_el.offsetParent.offsetParent.offsetTop + from_el.offsetTop + from_el.offsetParent.offsetTop - from_el.parentNode.scrollTop + 5;
    var pos_to_x = to_el.offsetParent.offsetParent.offsetLeft + to_el.offsetLeft - pos_from_x - 25;
    var pos_to_y = to_el.offsetParent.offsetParent.offsetTop + to_el.offsetTop + to_el.offsetParent.offsetTop - pos_from_y - to_el.parentNode.scrollTop + 0;

	// unteres Limit
	if(pos_from_y > tab_el.offsetHeight+tab_el.offsetTop){
		pos_from_y_ = pos_from_y;
		pos_from_y = tab_el.offsetHeight+tab_el.offsetTop-10;
		pos_to_y = pos_to_y-(pos_from_y-pos_from_y_);
	}
	if(pos_to_y+pos_from_y > tab_toel.offsetHeight+tab_toel.offsetTop){pos_to_y = tab_toel.offsetHeight+tab_toel.offsetTop-pos_from_y-14;}

	$(kont_el).css("left",pos_from_x);
    $(kont_el).css("top",pos_from_y);

	eval(jg+".clear();");
	eval(jg+".setStroke(1);");
	eval(jg+".setColor('#000000');");
	eval(jg+".drawLine(1, 8, "+pos_to_x+", "+(pos_to_y+8)+");");
	eval(jg+".setFont('arial','12px');");
	if(fieldid){
		var strg = "<img src=\"assets/images/legacy/center.gif\" STYLE=\"cursor:help;\" OnClick=\"lmbAjax_fieldinfo(event,2,'"+tabid+"','"+fieldid+"','"+vtabid+"');\">";
		eval(jg+".drawString(strg,-15,-2);");
		var strg = "<img src=\"assets/images/legacy/right.gif\" STYLE=\"cursor:help;\" OnClick=\"lmbAjax_fieldinfo(event,2,'"+tabid+"','"+fieldid+"','"+vtabid+"');\">";
		eval(jg+".drawString(strg,"+(pos_to_x+2)+", "+(pos_to_y-1)+");");
	}else{
		var strg = "<img src=\"assets/images/legacy/center.gif\" STYLE=\"cursor:help;\" OnClick=\"lmbAjax_fieldinfo(event,3,document.form1.viewid.value,'"+fel+"','"+tel+"','');\">";
		eval(jg+".drawString(strg,-15,-2);");
		var strg = "<img src=\"assets/images/legacy/right.gif\" STYLE=\"cursor:help;\" OnClick=\"lmbAjax_fieldinfo(event,3,document.form1.viewid.value,'"+fel+"','"+tel+"','');\">";
		eval(jg+".drawString(strg,"+(pos_to_x+2)+", "+(pos_to_y-1)+");");
	}
	eval(jg+".paint();");
	
	to_el.style.color='#999999';
}

// --- Linien zeichnen ----
function paint_lines(clear){
	for (var e in s_link){
		var part = s_link[e].split(",");
		js_line(part[0],part[1],part[2],part[3],part[4],part[5],part[6]);
	}
}

// --- Setze Feldfarbe bei onmouseover ----------
function set_color(el,color){
	if(el.style.color == 'black' || el.style.color == 'red'){
		el.style.color = color;
	}
}

//show_fieldinfo
function lmbAjax_fieldinfo(evt,act,par1,par2,par3,par4){
	url = "main_dyns_admin.php";
	actid = "tabschemaInfos&act="+act+"&par1="+par1+"&par2="+par2+"&par3="+par3+"&par4="+par4;
	dynfunc = function(result){lmbAjax_fieldinfoPost(result,evt,par4);};
	ajaxGet(null,url,actid,null,"dynfunc");
}

//show_fieldinfo
function lmbAjax_fieldinfoPost(value,evt,par4){
	document.getElementById("fieldinfo").innerHTML = value;
	if(!par4){limbasDivShow('',evt,'fieldinfo');}
}

//drop relation
function lmbDropRelation(relation){
	document.form1.setrelation.value=relation;
	document.form1.settype.value=4;
	lmbAjax_ViewEditorPattern();
	document.getElementById('fieldinfo').style.visibility='hidden';
	document.form1.setrelation.value='';
	document.form1.settype.value='';
}












// ---------------------- View Functions --------------------------

var viewsortid=null;

// Ajax table request
function lmbAjax_ViewEditorPattern(setdrag){
	if(setdrag){document.form1.setdrag.value = setdrag+";add";}
	setDrag();
	ajaxGet(null,"main_dyns_admin.php","VieweditorPattern",null,"lmbAjax_VieweditorPatternPost","form1");
	document.form1.setdrag.value = '';
}

// Ajax Preview output
function lmbAjax_VieweditorPatternPost(result){
	document.getElementById("vieweditorPattern").innerHTML = result;
	ajaxEvalScript(result);
	window.setTimeout(paint_lines, 500)
}
// add new relation
function iniRelation(evt,el) {
	var lfield = fieldel.id.split('_');
	var rfield = el.id.split('_');
	
	if(lfield[1] != rfield[1]){
		document.form1.setrelation.value = fieldel.id+';'+el.id;
		lmbAjax_ViewEditorPattern();
	}
	document.form1.setrelation.value = '';
}


// Ajax fieldlist
function lmbAjax_EditViewfield(event,el,act,fieldid,formid){
	if(fieldid){document.form1.setviewfield.value = fieldid;}
	else if(fieldel && fieldel.id && act == 'add'){document.form1.setviewfield.value = fieldel.id.substring(4, 900);}
	else if(act == 'addalias'){document.form1.setviewfield.value = '1';}
	if((isNaN(fieldid) || isNaN(formid) || fieldid == null || formid == null || fieldid == formid) && act == 'move'){document.form1.setviewfield.value = '';return;}
	if(document.form1.setviewfield.value){
		ajaxGet(null,"main_dyns_admin.php","VieweditorFields&act="+act+"&formid="+formid,null,"lmbAjax_EditViewfieldPost","form1");
	}
	document.form1.setviewfield.value = '';
}

// Ajax fieldlist post
function lmbAjax_EditViewfieldPost(value){
	document.getElementById("lmbViewfieldContainer").innerHTML = value;
}
