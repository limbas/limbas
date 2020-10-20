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

/* --- dynsPress ----------------------------------- */
var dyns_time  = null;
var dyns_evt = null;
var dyns_el  = null;
var dyns_actid  = null;
var dyns_fname  = null;
var dyns_par1  = null;
var dyns_par2  = null;
var dyns_par3  = null;
var dyns_par4  = null;
var dyns_par5  = null;

function dynsearchkeypress(evt,el,actid,fname,par1,par2,par3,par4,par5) {
	dyns_actid = actid;
	dyns_par1 = par1;
	dyns_par2 = par2;
	dyns_par3 = par3;
	dyns_par4 = par4;
	dyns_par5 = par5;
	dyns_fname = fname;
	dyns_evt = evt;
	dyns_el  = el;
	
	if (evt.keyCode == 27) {
		var el = document.getElementById(dyns_el.name+"l");
		el.innerHTML = '';
	}else{
		if (dyns_time) {window.clearTimeout(dyns_time);}
		dyns_time = window.setTimeout("dynsearchGet()",400);
	}
}

/* --- dynsGet ----------------------------------- */
function dynsearchGet() {
	eval('var dyns_value = document.form1.'+dyns_el.name+'.value;');
	if(dyns_value.length > 1){
		if (window.ActiveXObject) {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}else if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}
		
		xmlhttp.open("GET", "lib/dynsearch.php?&actid="+dyns_actid+"&form_name="+dyns_fname+"&value="+dyns_value+"&par1="+dyns_par1+"&par2="+dyns_par2+"&par3="+dyns_par3+"&par4="+dyns_par4+"&par5="+dyns_par5,true);
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){
				dynsShow(xmlhttp.responseText);
			}
		};
		xmlhttp.send(null);
	}
}

/* --- dynsShow ----------------------------------- */
function dynsShow(value) {
	var el = document.getElementById(dyns_el.name+"l");
	hide_selects(1);
	el.innerHTML = value;
}

/* --- dynsClose ----------------------------------- */
function dynsClose(id) {
	var el = document.getElementById(id);
	hide_selects(2);
	el.innerHTML = '';
}



/* --- ajaxGet (neue Function) ----------------------------------- */
function ajaxGet(evt,url,actid,parameters,functionName) {
	
	if(evt){
		setxypos(evt,'lmbAjaxContainer');
	}
	
	url += "?actid=" + actid;
	var separator = "&";
	
	if(null!=parameters){
		for(intI=0;intI<parameters.length;intI++)
		{
			var value = "";
			if(parameters[intI].indexOf(".")>0){
				value = eval(parameters[intI]).value;
				parameters[intI] = parameters[intI].split(".");
				parameters[intI] = parameters[intI][parameters[intI].length-1];
			}else if(document.getElementById(parameters[intI])){
				value = document.getElementById(parameters[intI]).value;
			}else if(document.getElementsByName(parameters[intI])[0]){
				value = document.getElementsByName(parameters[intI])[0].value;
			}
			
			url += separator + parameters[intI] + "=" + encodeURIComponent(value);
			separator = "&";
			//globalLmbAjax[globalLmbAjaxKey][parameters[intI]] = escape(value);
		}
		//globalLmbAjaxKey++;
	}
	
	if(url.length > 1){
		if (window.ActiveXObject) {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}else if(window.XMLHttpRequest){
			xmlhttp = new XMLHttpRequest();
		}
		
		xmlhttp.open("GET", url,true);
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){
				if(functionName=='')
					functionName = 'ajaxShow';
				eval(functionName + "(xmlhttp.responseText)");
			}
		};
		xmlhttp.send(null);
	}
}


/* --- Browser ----------------------------------- */
function browserType() {
  this.version = navigator.appVersion;			        //Version string
  this.dom=document.getElementById?1:0;			                //w3-dom
  browser_ie5=(this.dom && this.version.indexOf('MSIE ') > -1)?1:0;	     //IE5, IE6?
  browser_ns5=(this.dom && this.version.indexOf('MSIE ') == -1)?1:0; //NS6, Mozilla5
  browser_ie6=(this.dom && this.version.indexOf('MSIE 6')!=-1 && this.version.indexOf('MSIE 5')==-1)?1:0 //IE6
}

/* --- Selectfelder verstecken ------------------- */
function hide_selects(act){
	browserType();
	if(browser_ie5){
		var el = this.element;
		var ar = document.getElementsByTagName("select");
		var cc = null;
		
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			if(act == 1){
				cc.style.visibility = "hidden";
			}else if(act == 2){
				cc.style.visibility = "visible";
			}
		}
	}
} 


/* --- Eingabe�berpr�fung ------------------- */
function checkvalue(obj,form,reg,desc){
	if (obj != ''){
		if (!reg.exec(obj)){
			alert('falscher Eingabewert\n'+desc);
			eval("document.form1."+form+".value = '';");
		}
	}
}

/* --- Formular senden ------------------- */
function send_form(need){
	var nd = need.split(',');
	var ndlist = new Array();
	var i = 0;
	for (var e in nd){
		eval("var el = document.form1."+nd[e]+".value;");
		if(!el){
			ndlist[i] = nd[e];
			i++;
		}
	}
	if(ndlist[0]){
		var list = nd.join(" | ");
		alert("Es m�ssen mindestens folgende Felder ausgef�llt werden:\n"+list);
	}else{
		document.form1.submit();
	}
}