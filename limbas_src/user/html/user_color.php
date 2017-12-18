<?php
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
 * ID: 47
 */
unset($linkid);
?>

<Script language="JavaScript">


var c_id = new Array();
var c_val = new Array();
var a_col = new Array();
var d_col = new Array();
var maxid = <?=$result_colors[maxid]?>

<?
$key = 0;
if($result_colors[id]){
	foreach($result_colors[id] AS $key => $value) {
		$tkey = "[".$key."]";
		echo "c_id".$tkey." = \"".$result_colors[id][$key]."\";\n";
		echo "c_val".$tkey." = \"".$result_colors[wert][$key]."\";\n";
	}
}?>

var maxkey = <?=$key?>;


function cslice(el){
	var cc = null;
	var ar = document.getElementsByTagName('span');
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,5) == 'slice'){
			cc.style.display = 'none';
		}
	}
	document.getElementById(el).style.display='';
}

function sel_color(col){
	document.getElementById('selcolor').style.backgroundColor = col;
	document.form1.selcolor.value = col;
}

function addcolor(col){
	a_col.push(col);
	maxid = maxid + 1;
	maxkey = maxkey + 1;
	c_id[maxkey] = maxid;
	c_val[maxkey] = col;
	create_list();
}

function delcolor(ev){
	var el = getElement(ev);
	var key = el.id.substring(2,10);
	d_col.push(c_val[key]);
	c_val[key] = 0;
	create_list();
}

function submit_changes(){
	document.form1.add_color.value = a_col.join(';');
	document.form1.del_color.value = d_col.join(';');
	document.form1.submit();
}

// --- Farben -----------------------------------
var color = new Array();
color[1] = "<?=$farbschema[WEB1]?>";
color[2] = "<?=$farbschema[WEB2]?>";
color[3] = "<?=$farbschema[WEB3]?>";
color[4] = "<?=$farbschema[WEB4]?>";
color[5] = "<?=$farbschema[WEB5]?>";
color[6] = "<?=$farbschema[WEB6]?>";
color[7] = "<?=$farbschema[WEB7]?>";
color[8] = "<?=$farbschema[WEB8]?>";
color[9] = "<?=$farbschema[WEB9]?>";
color[10] = "<?=$farbschema[WEB10]?>";

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

function getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
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
	var el_tab = document.createElement("table");
	el_tab.style.margin = "0px";
	el_tab.style.width = "130px";
	el_tab.style.borderCollapse = "collapse";
	var el_body = document.createElement("tbody");
	el_tab.appendChild(el_body);
	
	if(c_id){
	for (var key in c_id){
		if(c_val[key]){
		// ---- Zeilen -------
		var aktuTR = document.createElement("tr");
		el_body.appendChild(aktuTR);
		
		var aktuTD = document.createElement("td");
		var values = [color[8],0,td_h,0,0,1,"#CCCCCC","solid","middle","left","middle"];
		create_td(aktuTD,values);	
		aktuTD.style.fontSize = 9;
		var txt = c_val[key];
		aktuTD.appendChild(document.createTextNode(txt));
		aktuTR.appendChild(aktuTD);
		
		var aktuTD = document.createElement("td");
		var values = [c_val[key],30,td_h,0,0,1,"#CCCCCC","solid","middle","center","middle"];
		create_td(aktuTD,values);
		aktuTR.appendChild(aktuTD);
		
		var aktuTD = document.createElement("td");
		var values = [color[8],30,td_h,0,0,1,"#CCCCCC","solid","middle","center","middle"];
		create_td(aktuTD,values);
		aktuTD.style.cursor = 'pointer';	
		
                var aktuImg = document.createElement("i");		
                $(aktuImg).addClass('lmb-icon');
                $(aktuImg).addClass('lmb-trash');
		aktuImg.id = "ce"+key;
		addEvent(aktuImg,"click", delcolor);
		aktuTD.appendChild(aktuImg);
		aktuTR.appendChild(aktuTD);
		
		}
	}
	}
	kontainer.appendChild(el_tab);
}

function create_list(){
	del_body(document.getElementById("itemlist_area"));
	make_list(document.getElementById("itemlist_area"));
}

</Script>


<FORM ACTION="<?=$main_action?>" METHOD=post name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="<?=$action?>">
<INPUT TYPE="hidden" NAME="del_color">
<INPUT TYPE="hidden" NAME="add_color">

<div class="lmbPositionContainerMain" style="width: 450px;">


<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="1"><TR>
<TR class="tabHeader"><TD class="tabHeaderItem">&nbsp;<?=$lang[154]?></TD><TD COLSPAN="3"></TD><TD class="tabHeaderItem">&nbsp;<?=$lang[155]?></TD><TD class="tabHeaderItem">&nbsp;<?=$lang[156]?>&nbsp;</TD></TR>


<TR class="tabBody">
<TD ID="itemlist_area" VALIGN="TOP"></TD>
<TD WIDTH="15"></TD>
<TD WIDTH="1" STYLE="background-color:<?=$farbschema[WEB6];?>;"></TD>
<TD WIDTH="15"></TD>
<TD width="200" height="200" VALIGN="TOP">
<?
$bbzm = 1;
$b = 0;
while($b <= 255){
	if($b > 0){$disp = "none";}else{$disp = "";}
	echo "<SPAN ID=\"slice_$bbzm\" STYLE=\"display:$disp\"><TABLE BORDER=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
	$g = 0;
	while($g <= 255){
		echo "<TR>";
		$r = 0;
		while($r <= 255){
			$rcol = dechex($r);
			if(!$rcol){$rcol = "00";}elseif(lmb_strlen($rcol) == 1){$rcol = "0".$rcol;}
			$gcol = dechex($g);
			if(!$gcol){$gcol = "00";}elseif(lmb_strlen($gcol) == 1){$gcol = "0".$gcol;}
			$bcol = dechex($b);
			if(!$bcol){$bcol = "00";}elseif(lmb_strlen($bcol) == 1){$bcol = "0".$bcol;}
			$col = lmb_strtoupper($rcol.$gcol.$bcol);		
			echo "<TD OnClick=\"addcolor('$col');\" OnMouseOver=\"sel_color('$col');\" STYLE=\"height:10px;width:10px;overflow:hidden;cursor:pointer;background-color:#$col\"></TD>";		
			$r += 15;
		}
		echo "</TR>\n";
		$g += 15;
	}
	echo "</TABLE></SPAN>\n";
	$b += 30;
	$bbzm++;
}
?>
<DIV><INPUT ID="selcolor" NAME="selcolor" TYPE="TEXT" OnChange="addcolor(this.value);this.style.backgroundColor=this.value;" STYLE="width:180px;height:15px;border:none;background-color:FFFFFF"></DIV>
<BR><BR>
<INPUT TYPE="button" VALUE="<?=$lang[157]?>" OnClick="submit_changes()">
</TD><TD VALIGN="TOP">

<TABLE BORDER="0" cellspacing="0" cellpadding="0" STYLE="height:180px;width:20px;border:1px solid grey;">
<?
$bbzm = 1;
$b = 0;
while($b <= 255){
	$bcol = dechex($b);
	if(!$bcol){$bcol = "00";}elseif(lmb_strlen($bcol) == 1){$bcol = "0".$bcol;}
	$col = "0000".$bcol;
	echo "<TR><TD STYLE=\"witdh:20px;heigh:20px;background-color:#$col;cursor:pointer\" OnMouseOver=\"cslice('slice_$bbzm')\"></TD></TR>";
	$b += 30;
	$bbzm++;
}
?>
</TABLE></TD></TR>
<TR class="tabfooter"><TD colspan="6"></TD></TR>
</TABLE>

</div>
</FORM>

