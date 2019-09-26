<?php
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
 * ID: 194
 */
?>

<script type="text/javascript" src="extra/calendar/dom/cal.js"></script>

<Script language="JavaScript">

var calendar = null;
// --- Termin-Array -----------------------------------
var t_id = new Array();
var t_key = new Array();
var t_start = new Array();
var t_end = new Array();
var t_start_date = new Array();
var t_end_date = new Array();
var t_title = new Array();
var t_color = new Array();

var dayofweek = ["Mo","Di","Mi","Do","Fr","Sa","So"];
var monthofyear = ["Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember","Januar"];

var a_key = new Array();
var l_id = new Array();
var d_day = 1;
var dd_id;
var dayofyear = 0;
var type = "<?=$type?>";
var userstat = "<?=$userstat?>";

<?php
$bzm = 0;
if($termin['id']){
	foreach($termin['id'] AS $key => $value) {
		$tkey = "[".$key."]";
		echo "t_id".$tkey." = \"".$termin['id'][$key]."\";\n";
		echo "t_key".$tkey." = \"".$key."\";\n";
		echo "t_start".$tkey." = \"".$termin['start_stamp'][$key]."\";\n";
		echo "t_end".$tkey." = \"".$termin['end_stamp'][$key]."\";\n";
		echo "t_start_date".$tkey." = \"".get_date($termin['start_date'][$key],2)."\";\n";
		echo "t_end_date".$tkey." = \"".get_date($termin['end_date'][$key],2)."\";\n";
		echo "t_title".$tkey." = \"".str_replace("\"","'",$termin['title'][$key])."\";\n";
		echo "t_color".$tkey." = \"".str_replace("\"","'",$termin['color'][$key])."\";\n\n";
		$bzm++;
	}
}
?>



// --- Farben -----------------------------------
var color = new Array();
color[1] = "<?=$farbschema['WEB1']?>";
color[2] = "<?=$farbschema['WEB2']?>";
color[3] = "<?=$farbschema['WEB3']?>";
color[4] = "<?=$farbschema['WEB4']?>";
color[5] = "<?=$farbschema['WEB5']?>";
color[6] = "<?=$farbschema['WEB6']?>";
color[7] = "<?=$farbschema['WEB7']?>";
color[8] = "<?=$farbschema['WEB8']?>";
color[9] = "<?=$farbschema['WEB9']?>";
color[10] = "<?=$farbschema['WEB10']?>";

// --- call-Funktion -----------------------------------
function call(y,m,d,del) {
	var kontainer = document.getElementById("kontainer");
	var listkontainer = parent.cal_list.document.getElementById("list_kontainer");
	var stp = new Date(y,m,d);
	stp = stp.getTime() / 1000;
	d_day = 1;	
	// ---- lösche alten Inhalt -----
	if(del == 1){
		del_body(kontainer);
		del_body(listkontainer);
	}
	if(document.form1.typ.value == 'tag'){
		call_day(y,m,d,"tag",91);
	}
	if(document.form1.typ.value == 'woche'){
		call_week(y,m,d,"woche",91);
	}
	if(document.form1.typ.value == 'monat'){
		call_month(y,m,d,"monat",91);
	}
	if(document.form1.typ.value == 'yahr'){
		call_year(y,m,d,"yahr",91);
	}
	make_list(listkontainer,stp,y,m,d);
	l_id = new Array();   // lösche listenarray
}



// ------------------- Hauptpunkte ----------------
var active;

function activate(sel,evt) {
	if(sel != active){
		document.getElementById(sel).style.fontWeight = evt;
	}
}

function view(sel,el,typ) {
	document.getElementById('new').style.fontWeight = 'normal';
	document.getElementById('day').style.fontWeight = 'normal';
	document.getElementById('week').style.fontWeight = 'normal';
	document.getElementById('month').style.fontWeight = 'normal';
	document.getElementById('year').style.fontWeight = 'normal';
	
	parent.cal_main.document.getElementById('el_create').style.display = 'none';
	parent.cal_main.document.getElementById('kontainer').style.display = 'none';
	parent.cal_main.document.getElementById(el).style.display = '';
	parent.cal_main.document.form1.typ.value = typ;
	document.getElementById(sel).style.fontWeight = 'bold';
	active = sel;
	var y = parent.cal_main.document.form1.y.value;
	var m = parent.cal_main.document.form1.m.value;
	var d = parent.cal_main.document.form1.d.value;
	parent.cal_main.call(y,m,d,1);
}


</Script>

<?php /*----------------- Farbe DIV -------------------*/?>
<div ID="element4" style="position:absolute;visibility:hidden;top:-1000px;left:-1000px;z-index:10002; height:100%; width:100%; filter:Shadow(color=<?=$farbschema['WEB4']?>, direction=135)">
<TABLE BORDER="0" WIDTH="114" cellspacing="0" cellpadding="0"><FORM NAME="fcolor_form">
<TR><TD ALIGN="LEFT">
<?php
$bzm = 0;
$bzm1 = 1;
while($user_colors['wert'][$bzm]) {
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


<TABLE BORDER="0" cellpadding="0" cellspacing="0" WIDTH="100%" style="padding-top:30px;padding-left:20px;"><TR><TD>
<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="userstat_main">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="field_id" value="<?=$field_id?>">
<input type="hidden" name="dat_id" value="<?=$dat_id?>">
<input type="hidden" name="typ" value="<?=$typ?>">
<input type="hidden" name="y" value="<?=date("Y",mktime())?>">
<input type="hidden" name="m" value="<?=(date("n",mktime()) - 1)?>">
<input type="hidden" name="d" value="<?=date("j",mktime())?>">






<TABLE BORDER="0" cellpadding="0" cellspacing="1" WIDTH="93%"><TR bgcolor="<?=$farbschema['WEB3']?>">
<TD WIDTH="18" HEIGHT="20">&nbsp;</TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER" ID="new" OnMouseOver="activate('new','bold') ;" OnMouseOut="activate('new','normal');" OnClick="view('new','el_create','tag')"><?=$lang[571]?></TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER">&nbsp</TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER" ID="day" OnMouseOver="activate('day','bold') ;" OnMouseOut="activate('day','normal');" OnClick="view('day','kontainer','tag')"><?=$lang[1435]?></TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER" ID="week" OnMouseOver="activate('week','bold') ;" OnMouseOut="activate('week','normal');" OnClick="view('week','kontainer','woche')"><?=$lang[1436]?></TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER" ID="month" OnMouseOver="activate('month','bold') ;" OnMouseOut="activate('month','normal');" OnClick="view('month','kontainer','monat')"><?=$lang[1437]?></TD>
<TD STYLE="border-bottom:1px solid grey; border-right:1px solid grey; cursor:pointer;" WIDTH="70" ALIGN="CENTER" ID="year" OnMouseOver="activate('year','bold') ;" OnMouseOut="activate('year','normal');" OnClick="view('year','kontainer','yahr')"><?=$lang[1438]?></TD>
<TD>&nbsp;</TD>
</TR></TABLE>



<?php #---------------------------- neuer Termin ----------------------------?>
<DIV ID="el_create" STYLE="display:none">
<TABLE BORDER="0" cellpadding="0" cellspacing="4">
    <TR><TD VALIGN="TOP"><B><?=$lang[1445]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_from" STYLE="width:130px" VALUE="<?=$inp_from?>">&nbsp;<span id="cal_from"><i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showCalendar(event,'inp_from','cal_from',document.form1.inp_from.value)"></i></span></TD></TR>
    <TR><TD VALIGN="TOP"><B><?=$lang[1446]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_to" STYLE="width:130px" VALUE="<?=$inp_to?>" OnClick="this.value=this.form.inp_from.value">&nbsp;<span id="cal_to"><i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showCalendar(event,'inp_to','cal_to',document.form1.inp_to.value)"></i></span></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1447]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_title" STYLE="width:400px" VALUE="<?=htmlentities($inp_title,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>"></TD></TR>
<TR><TD VALIGN="TOP"><B><?=$lang[1448]?></B>&nbsp;</TD><TD><TEXTAREA NAME="inp_desc" STYLE="width:400px;height:200px"><?=htmlentities($inp_desc,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></TEXTAREA></TD></TR>
<?php if($type != "tab"){?><TR><TD VALIGN="TOP"><B><?=$lang[1449]?></B>&nbsp;</TD><TD><SELECT NAME="inp_typ" STYLE="width:120px">
<OPTION VALUE="1" <?php if($inp_typ == 1){echo "SELECTED";}?>><?=$lang[1219]?>
<OPTION VALUE="2" <?php if($inp_typ == 2){echo "SELECTED";}?>><?=$lang[1441]?>
<OPTION VALUE="3" <?php if($inp_typ == 3){echo "SELECTED";}?>><?=$lang[1442]?>
</SELECT></TD></TR><?php }?>
<TR><TD VALIGN="TOP"><B><?=$lang[1443]?></B>&nbsp;</TD><TD><INPUT TYPE="TEXT" NAME="inp_color" STYLE="width:130px" VALUE="<?=htmlentities($inp_color,ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?>">&nbsp;<i class="lmb-icon lmb-pencil" BORDER="0" STYLE="cursor:pointer" OnCLick="showdiv(event,'element4')"></i></TD></TR>
<?php if($type != "tab"){?><TR><TD VALIGN="TOP"><B><?=$lang[1444]?></B>&nbsp;</TD><TD><INPUT TYPE="CHECKBOX" NAME="inp_alert" VALUE="1" STYLE="background-color:transparent;border:none;" <?php if($inp_alert){echo "CHECKED";}?>></TD></TR><?php }?>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>
<TR><TD VALIGN="TOP">&nbsp;</TD><TD><INPUT TYPE="SUBMIT" NAME="inp_create" VALUE="<?=$lang[1453]?>"></TD></TR>
</TABLE>
</DIV>


<?php #---------------------------- kontainer ----------------------------?>
<DIV ID="kontainer"></DIV>

</TD></TR></FORM></TABLE>


