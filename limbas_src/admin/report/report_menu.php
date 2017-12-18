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
 * ID: 169
 */
?>

<script language="JavaScript">

<?
if($alert){
	echo "parent.report_main.document.form1.submit();\n";
}
?>

/* ---------------- Sendkeypress---------------------- */
function sendkeydown(evt) {
        if(evt.keyCode == 13){
                window.focus();
        }
}

function resetmenu(){
		document.getElementById('new_ureport_area').style.display = 'none';
		document.getElementById('new_chart_area').style.display = 'none';
        document.getElementById('new_bild_area').style.display = 'none';
        document.getElementById('bild').style.borderStyle = 'outset';
        document.getElementById('bild').style.backgroundColor = '';
        document.getElementById('new_dbdat_area').style.display = 'none';
        <?if($referenz_tab > 0){?>document.getElementById('dbdat').style.borderStyle = 'outset';
        document.getElementById('dbdat').style.backgroundColor = '';<?}?>
        document.getElementById('text').style.borderStyle = 'outset';
        document.getElementById('text').style.backgroundColor = '';
        <?if($umgvar["use_jsgraphics"]){?>
        document.getElementById('line').style.borderStyle = 'outset';
        document.getElementById('line').style.backgroundColor = '';
        document.getElementById('ellipse').style.borderStyle = 'outset';
        document.getElementById('ellipse').style.backgroundColor = '';
        <?}?>
        document.getElementById('ureport').style.borderStyle = 'outset';
        document.getElementById('ureport').style.backgroundColor = '';
        document.getElementById('chart').style.borderStyle = 'outset';
        document.getElementById('chart').style.backgroundColor = '';
        document.getElementById('rect').style.borderStyle = 'outset';
        document.getElementById('rect').style.backgroundColor = '';
        document.getElementById('tab').style.borderStyle = 'outset';
        document.getElementById('tab').style.backgroundColor = '';
        document.getElementById('datum').style.borderStyle = 'outset';
        document.getElementById('datum').style.backgroundColor = '';
        document.getElementById('snr').style.borderStyle = 'outset';
        document.getElementById('snr').style.backgroundColor = '';
        document.getElementById('formel').style.borderStyle = 'outset';
        document.getElementById('formel').style.backgroundColor = '';
}

function pressbutton(id,st,col){
	document.form1.report_add.value = id;

	resetmenu();
	var objst = document.getElementById(id).style;
	objst.borderStyle = st;
	objst.backgroundColor = col;
}

function actbutton(id,st,opt){
	document.form1.report_add.value = id;
	
	var objst = document.getElementById(id).style;
	var stati = document.getElementById(st);
	resetmenu();
	if(stati.style.display == 'none'){
		stati.style.display = '';
		objst.borderStyle = 'inset';
		objst.backgroundColor = '<?=$farbschema[WEB7]?>';
	}else{
		stati.style.display = 'none';
		objst.borderStyle = 'outset';
		objst.backgroundColor = '<?=$farbschema[WEB10]?>';
	}
}

function start_uploadlevel(){
        var w = document.getElementById('uploadlevel').style.width;
        var w = parseInt(w) + 2;
        if(w > 190){w = 1;}
        document.getElementById('uploadlevel').style.width = w;
        setTimeout("start_uploadlevel()",100);
}

// --- Datensatzfeld hinzufügen ----------------------------------
function add_dbfield(el,evt){
		
	if(parent.report_main.document.form1.report_replace_element.value > 0 || document.form1.listmode.checked == true || evt.ctrlKey){
		el.className = "markAsActive";
		send();
	}else{
		if(el.className == "markAsActive"){
			el.className = "";
		}else{
			el.className = "markAsActive";
		}
	}
	
}

function send() {
	
	obj = document.form1.report_add.value;
	
	parent.report_main.document.form1.default_font.value = document.form1.default_font.value;
	parent.report_main.document.form1.default_size.value = document.form1.default_size.value;

	if(parent.report_main.document.form1.report_add.value=='ureport'){
		parent.report_main.document.form1.ureport_id.value=document.form1.ureport_id.value;
		parent.report_main.document.form1.ureport_type.value=document.form1.ureport_type.value;
	}
	
	if(obj == 'dbdat' || obj == 'dbdesc' || obj == 'dbnew' || obj == 'dbsearch'){
		var gtabid = new Array();
		var parentrel = new Array();
		var fieldid = new Array();
		var datatype = new Array();
		$(".markAsActive").each(function( index,el ) {
			fieldid.push( el.getAttribute('lmfieldid') );
			gtabid.push( el.getAttribute('lmgtabid') );
			parentrel.push( el.getAttribute('lmparentrel') );
			datatype.push( el.getAttribute('lmdatatype') );
			el.className = "";
		});
		if(fieldid.length <= 0){
			document.form1.report_add.value = '';
			parent.report_main.window.set_posxy();
			parent.report_main.document.form1.report_posxy_edit.value = '1';
			parent.report_main.document.form1.submit();
			return;
		}
		
		parent.report_main.document.form1.report_add_field.value = fieldid.join(";");
		parent.report_main.document.form1.report_add_tab.value = gtabid.join(";");
		parent.report_main.document.form1.report_add_baum.value = parentrel.join("#");
		parent.report_main.document.form1.report_add_field_data_type.value = datatype.join(";");
		parent.report_main.document.form1.report_add.value = obj;
		parent.report_main.window.set_posxy();
		parent.report_main.document.form1.submit();
	
	}else if(obj == 'bild' && document.form1.new_pic.value){
		document.getElementById('send_bild_area').style.display = '';
		start_uploadlevel();
		document.form1.report_add.value = "bild";
		parent.report_main.document.form1.report_add.value='';
		document.form1.submit();
	}else if(obj == 'chart' && document.form1.chart_id.value){
		document.getElementById('new_chart_area').style.display = '';
		document.form1.report_add.value = '';
		parent.report_main.document.form1.report_add.value='chart';
		parent.report_main.document.form1.report_chart_id.value=document.form1.chart_id.value;
		parent.report_main.document.form1.submit();
	}else{
		parent.report_main.window.set_posxy();
		parent.report_main.document.form1.report_posxy_edit.value = '1';
		parent.report_main.document.form1.new_text.value = 'TEXTBLOCK';
		parent.report_main.document.form1.submit();
	}
	resetmenu();
}

function getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
}

function make_bold(ev){
	var el = getElement(ev);
	el.style.textDecoration = "underline";
}
function make_unbold(ev){
	var el = getElement(ev);
	el.style.textDecoration = "none";
}

var frmevent = null; 
function open_details(ev){
	var el = getElement(ev);
	var dv = 'div'+el.id.substr(2,10);

	parent.report_main.document.getElementById(dv).onmousedown(ev);
	// enable selectable function
	parent.report_main.$('#innenramen').selectable("enable");
	
	return;
}

// deprecated
function open_detailsxxxx(ev){
	var el = getElement(ev);
	var dv = 'div'+el.id.substr(2,10);
	//parent.form_main.aktivate(0,dv,0,0);
	var omo = parent.report_main.document.getElementById(dv).onmousedown;
	//alert(omo);
	omo = omo.toString();
	var par = omo.split('{');
	var par1 = par[1];
	var par2 = par1.substr(1,(par1.length)-2);
	var elfunction = "parent.report_main."+par2;
	elfunction = elfunction.replace(/[ ]/g, "");
	elfunction = elfunction.replace(/event/g, "ev");
	//alert(elfunction);
	eval(elfunction);
return;
	var el = getElement(ev);
	var id = el.id.substr(2,10);
	var dv = 'div'+el.id.substr(2,10);
	parent.report_main.aktivate(dv,id,0);
}


function setNewZindex(){
	parent.report_main.document.form1.set_new_zindex.value = '1';
	send();
}

function setOrderBy(val){
	document.form1.setOrder.value = val;
	document.form1.submit();
}


</script>

<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="setup_report_menu">
<input type="hidden" name="report_id" value="<?=$report_id;?>">
<input type="hidden" name="report_name" VALUE="<?=$report["name"];?>">
<input type="hidden" name="referenz_tab" VALUE="<?=$referenz_tab;?>">
<input type="hidden" name="report_add">
<input type="hidden" name="report_tab">
<input type="hidden" name="report_tab_el">
<input type="hidden" name="setOrder">
<input type="hidden" name="change_listmode">

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD WIDTH="10">&nbsp;</TD><TD>
<BR>

<TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD class="formeditorPanelHead" COLSPAN="4"><?=$lang[1160]?></TD></TR>
<TR><TD VALIGN="TOP"><b><?=$lang[1137]?></TD><TD><?=$report["name"];?></TD></TR>
<TR><TD VALIGN="TOP"><b><?=$lang[164]?></TD><TD><?=$gtab["desc"][$referenz_tab];?></TD></TR>
</TABLE>

<BR>

<TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD COLSPAN="4" class="formeditorPanelHead"><?=$lang[2782]?></TD></TR>
<TR><TD STYLE="height:14px;"><B>X:</B></TD><TD><INPUT STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40px;height:13px;" NAME="XPOSI" OnChange="parent.report_main.posxy_change(this.value,'');"></TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>W:</B>&nbsp;&nbsp;</TD><TD><INPUT TYPE="TEXT" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40px;height:13px;" NAME="WPOSI" OnChange="parent.report_main.sizexy_change('',this.value);"></TD></TR>
<TR><TD STYLE="height:14px;"><B>Y:</B></TD><TD><INPUT STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40px;height:13px;" NAME="YPOSI" OnChange="parent.report_main.posxy_change('',this.value);"></TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;<B>H:</B>&nbsp;&nbsp;</TD><TD><INPUT TYPE="TEXT" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40px;height:13px;" NAME="HPOSI" OnChange="parent.report_main.sizexy_change(this.value,'');"></TD></TR>
</TD></TR></TABLE>

<BR>

<TABLE cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD class="formeditorPanelHead" COLSPAN="4"><?=$lang[2781]?></TD></TR>
<TR><TD><?=$lang[1138]?>:</TD><TD>
<SELECT NAME="default_font" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:70;height:16;">
<?
foreach($sysfont as $key => $value){
	echo "<OPTION VALUE=\"".$value."\">".$value."\n";
}
?>
</SELECT>
</TD><TD><?=$lang[1139]?>:</TD><TD><INPUT TYPE="TEXT" NAME="default_size" VALUE="10" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD></TR>
<TR><TD COLSPAN="4"><U><?=$lang[1140]?>:</U></TD></TR>
<TR><TD><?=$lang[1141]?>:</TD><TD><INPUT TYPE="TEXT" NAME="page_width" VALUE="<?if($report[page_style]){echo $report[page_style][0];}else{echo "210";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD><TD><?=$lang[1142]?>:</TD><TD><INPUT TYPE="TEXT" NAME="page_height" VALUE="<?if($report[page_style][1]){echo $report[page_style][1];}else{echo "295";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD></TR>

<?/*
<TR ><TD COLSPAN="4"><U><?=$lang[1143]?>:</U></TD></TR>
<TR ><TD><?=$lang[1144]?>:</TD><TD><INPUT TYPE="TEXT" NAME="border_top" VALUE="<?if($report[page_style][2]){echo $report[page_style][2];}else{echo "5";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD><TD><?=$lang[1145]?>:</TD><TD><INPUT TYPE="TEXT" NAME="border_bottom" VALUE="<?if($report[page_style][3]){echo $report[page_style][3];}else{echo "5";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD></TR>
<TR ><TD><?=$lang[1146]?>:</B></TD><TD><INPUT TYPE="TEXT" NAME="border_left" VALUE="<?if($report[page_style][4]){echo $report[page_style][4];}else{echo "5";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD><TD><?=$lang[1147]?>:</TD><TD><INPUT TYPE="TEXT" NAME="border_right" VALUE="<?if($report[page_style][5]){echo $report[page_style][5];}else{echo "5";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:40;height:13;"></TD></TR>
*/?>

<TR><TD COLSPAN="5"><div style="overflow:hidden;height:1px;width:100%;background-color:grey;"></div></TD></TR>
<TR><TD>name:</TD><TD colspan="3"><INPUT TYPE="TEXT" NAME="savename" VALUE="<?if($report["savename"]){echo htmlentities($report["savename"],ENT_QUOTES,$umgvar["charset"]);}else{echo "default";}?>" OnChange="this.form.submit();" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;width:155;height:15;"></TD></TR>
<TR ><TD COLSPAN="5"><div style="overflow:hidden;height:1px;width:100%;background-color:grey;"></div></TD></TR>
<?if($report["listmode"]){$checked="checked";}?>




<TR ><TD COLSPAN="3">Raster:</TD><TD><INPUT TYPE="TEXT" NAME="raster" VALUE="10" STYLE="width:30px;border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB8];?>;"></TD></TR>
<TR ><TD COLSPAN="3"><?=$lang[2649]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="listmode" id="listmode" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;" <?=$checked?> OnChange="this.form.change_listmode.value=1;this.form.submit();"></TD></TR>
<TR ><TD COLSPAN="3"><?=$lang[1148]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="prop" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;"></TD></TR>
<TR ><TD COLSPAN="3"><?=$lang[2063]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="set_zindex" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;"></TD></TR>
<TR ><TD COLSPAN="3"><?=$lang[2067]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="set_new_zindex" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;" onclick="setNewZindex();"></TD></TR>
<TR ><TD COLSPAN="5"><div style="overflow:hidden;height:1px;width:100%;background-color:grey;"></div></TD></TR>
<TR ><TD COLSPAN="4" nowrap>
<?=$lang[2346]?>:&nbsp;
<?if($report["orderby"] == "zindex"){$isOrderByZ = "CHECKED";}else{$isOrderByP = "CHECKED";}?>
<?=$lang[2347]?><INPUT TYPE="RADIO" NAME="set_oderby" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;" onclick="setOrderBy('zindex');" <?=$isOrderByZ?>>
<?=$lang[2348]?><INPUT TYPE="RADIO" NAME="set_oderby" STYLE="border:none;BACKGROUND-COLOR:<?echo $farbschema[WEB7];?>;" onclick="setOrderBy('ypos');" <?=$isOrderByP?>>
</TD></TR>

</TABLE>

<BR>


<TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
<TR><TD COLSPAN="10" class="formeditorPanelHead"><?=$lang[2780]?></TD></TR>
<TR ><TD><TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR >
    <TD VALIGN="TOP"><i ID="text" class="lmb-icon lmb-rep-txt btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1149]?>" VALUE="text" OnMouseDown="pressbutton('text','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('text','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='text';send();"></i></TD>
<?if($referenz_tab > 0){?><TD VALIGN="TOP"><i ID="dbdat" class="lmb-icon lmb-rep-db btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1150]?>" VALUE="dbdat" OnClick="parent.report_main.document.form1.report_add.value='dbdat';actbutton('dbdat','new_dbdat_area',0);"></i></TD><?}?>
<TD VALIGN="TOP"><i ID="bild" class="lmb-icon lmb-rep-pic btn" STYLE="border:2px outset grey"TITLE="<?=$lang[1151]?>" VALUE="bild" OnClick="parent.report_main.document.form1.report_add.value='bild';actbutton('bild','new_bild_area',0);"></i></TD>
<?if($umgvar["use_jsgraphics"]){?>
<TD VALIGN="TOP"><i ID="line" class="lmb-icon lmb-rep-line btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1152]?>" VALUE="line" OnMouseDown="pressbutton('line','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('line','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='line';send();"></i></TD>
<TD VALIGN="TOP"><i ID="ellipse" class="lmb-icon lmb-rep-circ btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1154]?>" VALUE="ellipse" OnMouseDown="pressbutton('ellipse','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('ellipse','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='ellipse';send();"></i></TD>
<?}?>
<TD VALIGN="TOP"><i ID="rect" class="lmb-icon lmb-rep-rect btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1153]?>" VALUE="rect" OnMouseDown="pressbutton('rect','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('rect','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='rect';send();"></i></TD>
<TD VALIGN="TOP"><i ID="tab" class="lmb-icon lmb-rep-tab btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1155]?>" VALUE="tab" OnMouseDown="pressbutton('tab','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('tab','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='tab';send();"></i></TD>
<TD VALIGN="TOP"><i ID="datum" class="lmb-icon lmb-rep-date btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1156]?>" VALUE="datum" OnMouseDown="pressbutton('datum','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('datum','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='datum';send();"></i></TD>
<TD VALIGN="TOP"><i ID="ureport" class="lmb-icon-cus lmb-rep-uform btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1171]?>" VALUE="ureport" OnMouseDown="pressbutton('ureport','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('ureport','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='ureport';actbutton('ureport','new_ureport_area',0);"></i></TD>
</TR><TR >
<TD VALIGN="TOP"><i ID="chart" class="lmb-icon lmb-line-chart btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1171]?>" VALUE="chart" OnMouseDown="pressbutton('chart','inset','<?echo $farbschema[WEB10];?>');" OnClick="parent.report_main.document.form1.report_add.value='chart';actbutton('chart','new_chart_area',0);"></i></TD>
<TD VALIGN="TOP"><i ID="snr" class="lmb-icon lmb-rep-snr btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1157]?>" VALUE="snr" OnMouseDown="pressbutton('snr','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('snr','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='snr';send();"></i></TD>
<TD VALIGN="TOP"><i ID="formel" class="lmb-icon lmb-rep-php btn" STYLE="border:2px outset grey" TITLE="<?=$lang[1772]?>" VALUE="formel" OnMouseDown="pressbutton('formel','inset','<?echo $farbschema[WEB10];?>');" OnMouseUp="pressbutton('formel','outset','<?echo $farbschema[WEB7];?>');parent.report_main.document.form1.report_add.value='formel';send();"></i></TD>
</TR></TABLE>
</TD></TR></TABLE>


<div ID="new_bild_area" style="display:none;">
<br>
<TABLE  cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD colspan="2" class="formeditorPanelHead" align="center"></TD></TR>
<TR><TD HEIGHT="25" colspan="2"><INPUT TYPE="FILE" NAME="new_pic" SIZE="20" STYLE="background-color:<?echo $farbschema[WEB7];?>;width:200px;height:17px;"></TD></TR>
<TR><TD><?=$lang[925]?></td>
<td><SELECT STYLE="width:60px;" NAME="pic_type">
<OPTION VALUE="jpg">jpg
<OPTION VALUE="png">png
</SELECT>
</td></tr>
<TR><TD><?=$lang[1176]?></td>
<td><SELECT STYLE="width:60px;" NAME="pic_compress">
<OPTION VALUE="30">30%<
<OPTION VALUE="40">40%
<OPTION VALUE="50">50%
<OPTION VALUE="60">60%
<OPTION VALUE="70">70%
<OPTION VALUE="75">75%
<OPTION VALUE="80">80%
<OPTION VALUE="85">85%
<OPTION VALUE="90">90%
<OPTION VALUE="95">95%
<OPTION VALUE="100" SELECTED>100%
</SELECT>
</TD></TR>
<TR ID="send_bild_area" style="display:none;"><TD  STYLE="height:30px;" VALIGN="CENTER">&nbsp;<SPAN ID="uploadlevel" STYLE="width:1px;height:15px;border:2px inset grey;background-color:<?echo $farbschema[WEB10];?>">&nbsp;</SPAN></TD></TR>
</TABLE>
</div>

<div ID="new_dbdat_area" style="display:none;">
<br>
<TABLE  cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD class="formeditorPanelHead" align="center"><?=$lang[972]?></TD></TR>
<tr><td>
<SELECT NAME="source_table" style="width:200px;" onchange="LmAdm_getFields(this.value,0,'')">"><OPTION VALUE="-1"></OPTION>
<?php
if(!$source_table){$source_table = $referenz_tab;}
foreach ($tabgroup["id"] as $key0 => $value0) {
	echo "<OPTION VALUE=\"0\">(".$tabgroup["name"][$key0].")";
	foreach ($gtab["tab_id"] as $key => $value) {
		if($gtab["tab_group"][$key] == $value0){
			if($source_table == $value){$selected = "selected";}else{$selected = "";}
			echo "<OPTION VALUE=\"".$value."\" $selected>&nbsp;&nbsp;".$gtab["desc"][$key]."</OPTION>";
		}
	}
}
?>
</SELECT>
</td></tr>
<tr><td>
<div ID="el_0">
<?php
/*----------------- Tabellenliste -------------------*/
include_once("admin/report/report_tabliste.php");
?>
</div>
</td></tr></table>
</div>


<div ID="new_chart_area" style="display:none;">
<br>
<TABLE  cellspacing="0" cellpadding="2" class="formeditorPanel">
<TR><TD class="formeditorPanelHead" align="center"><?=$lang[972]?></TD></TR>
<tr><td>
<SELECT NAME="chart_id" style="width:200px;"><OPTION VALUE="-1"></OPTION>
<?php
foreach ($gdiaglist as $keyk => $valuek) {
    foreach ($valuek["name"] as $key => $value) {
        echo "<OPTION VALUE=\"" . $key . "\">" . $value;
    }
}
?>
</SELECT>
</td></tr>
</table>
</div>


<div ID="new_ureport_area" style="display:none;">
<br>
<TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
<TR><TD class="formeditorPanelHead"><?=$lang[2779]?></TD></TR>
<TR><TD  VALIGN="TOP">
<div style=";padding:2px;"><?=$lang[1177]?>:<BR>
<SELECT NAME="ureport_type" STYLE="width:190px">
<OPTION VALUE="1"><?=$lang[2777]?>
<OPTION VALUE="2"><?=$lang[2778]?>
</SELECT></div>

<div style="padding:2px;"><?=$lang[1179]?>:<BR>
<SELECT NAME="ureport_id" STYLE="width:190px;"><option>
<?php
foreach ($greportlist["argresult_tabid"] as $rkey => $rval){
	if($rkey == $report_id){continue;}
	echo "<OPTION VALUE=\"".$rkey."\">".$greportlist[$rval]["name"][$rkey];
}
?>
</SELECT></div>
</TD></TR>
</TABLE>
</div>



<BR>
<TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
<TR><TD STYLE="height:15px"class="formeditorPanelHead"></TD></TR>
<TR><TD  STYLE="height:15px" ALIGN="CENTER"><INPUT TYPE="BUTTON" STYLE="border:1px solid grey;cursor:pointer" VALUE="<?=$lang[2515]?>" OnCliCk="send();"></TD></TR>
</TABLE>



<BR>
<TABLE cellspacing="0" cellpadding="0" class="formeditorPanel">
<TR><TD class="formeditorPanelHead"><?=$lang[2783]?></TD></TR></TABLE>
<div ID="itemlist_area" class="formeditorPanel" style="margin-top:0"></div>



</FORM>
</TD></TR></TABLE>