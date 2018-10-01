<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 192
 */
?>


<Script language="JavaScript">
var calendar = null;


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
	hide_selects(1);
	
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


function showCalendar(event,inputf,container,datum,value) {
	hide_selects(1);
	var datum = datum.split("-");
	var zeit = datum[2].split(":");
	datum[2] = datum[2].substring(0,2);
	zeit[0] = zeit[0].substring(5,2);
	var sel = document.getElementById(container);
	
	var calendar = new Calendar(true, null, selected, closeHandler);
	calendar.showsTime = true;
	calendar.setRange(2000, 2010);
	calendar.create();
	calendar.setDate(new Date(datum[0],datum[1],datum[2],zeit[0],zeit[1]));
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

</Script>

<?php /*----------------- Farbe DIV -------------------*/?>
<div ID="element4" style="position:absolute;visibility:hidden;top:-1000px;left:-1000px;z-index:10002; height:100%; width:100%; filter:Shadow(color=<?=$farbschema['WEB4']?>, direction=135)">
<TABLE BORDER="0" WIDTH="114" cellspacing="0" cellpadding="0"><FORM NAME="fcolor_form">
<TR><TD ALIGN="LEFT">
<?php
$bzm = 0;
$bzm1 = 1;
while($user_colors['name'][$bzm]) {
	if($bzm1 == 6){$bzm1 = 1;echo "</TD></TR><TR><TD ALIGN=\"LEFT\">";}
	?><SPAN><IMG SRC="pic/transp.gif" STYLE="width:6px;height:20px"></SPAN><SPAN STYLE="border:1px solid black;cursor:pointer;background-color:<?= $user_colors['wert'][$bzm] ?>;" OnClick="setcolor('<?= $user_colors['wert'][$bzm] ?>');"><IMG SRC="pic/transp.gif" STYLE="width:14px;height:14px"></SPAN>
    <?php
	$bzm++;
	$bzm1++;
}
?>
</TD></TR>
</FORM></TABLE>
</div>

<BR><BR>
<TABLE BORDER="0" cellpadding="0" cellspacing="0"><TD WIDTH="20">&nbsp;</TD><TD>
<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="kalender_det">
<input type="hidden" name="ID" value="<?=$ID?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="field_id" value="<?=$field_id?>">
<input type="hidden" name="dat_id" value="<?=$dat_id?>">
<input type="hidden" name="y" value="<?=$y?>">
<input type="hidden" name="m" value="<?=$m?>">
<input type="hidden" name="d" value="<?=$d?>">
<input type="hidden" name="typ" value="<?=$typ?>">


<TABLE BORDER="0" cellpadding="0" cellspacing="4">
    <TR><TD VALIGN="TOP"><B><?=$lang[1445]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_from" STYLE="width:130px" VALUE="<?=get_date($termin['start_date'],2)?>">&nbsp;<span id="cal_from"><i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showCalendar(event,'inp_from','cal_from','<?=$termin['start_date']?>',document.form1.inp_from.value)"></i></span></TD></TR>
    <TR><TD VALIGN="TOP"><B><?=$lang[1446]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_to" STYLE="width:130px" VALUE="<?=get_date($termin['end_date'],2)?>">&nbsp;<span id="cal_to"><i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showCalendar(event,'inp_to','cal_to','<?=$termin['end_date']?>',document.form1.inp_to.value)"></i></span></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1447]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_title" STYLE="width:400px" VALUE="<?=htmlentities($termin['title'],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>"></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1448]?></B>&nbsp;</TD><TD><TEXTAREA NAME="inp_desc" STYLE="width:400px;height:200px"><?=htmlentities($termin['desc'],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></TEXTAREA></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1449]?></B>&nbsp;</TD><TD><SELECT NAME="inp_typ" STYLE="width:120px">
<OPTION VALUE="1" <?php if($termin['typ'] == 1){echo "SELECTED";}?>><?=$lang[1219]?>
<OPTION VALUE="2" <?php if($termin['typ'] == 2){echo "SELECTED";}?>><?=$lang[1441]?>
<OPTION VALUE="3" <?php if($termin['typ'] == 3){echo "SELECTED";}?>><?=$lang[1442]?>
</SELECT></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1443]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_color" STYLE="width:130px" VALUE="<?=htmlentities($termin['color'],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>">&nbsp;<i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showdiv(event,'element4')"></i></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1444]?></B>&nbsp;</TD><TD><INPUT TYPE="CHECKBOX" NAME="inp_alert" VALUE="1" STYLE="background-color:transparent;border:none;" <?php if($termin['alert']){echo "CHECKED";}?>></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR><TD VALIGN="TOP">&nbsp;</TD><TD><INPUT TYPE="SUBMIT" NAME="inp_change" VALUE="<?=$lang[522]?>!">&nbsp;&nbsp;<INPUT TYPE="SUBMIT" NAME="inp_del" VALUE="<?=$lang[1451]?>"></TD></TR>
</TABLE>
</FORM>
</TD></TR></TABLE>