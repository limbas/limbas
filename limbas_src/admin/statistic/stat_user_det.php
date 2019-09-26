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
 * ID:
 */


if(!$diag_bis){$diag_bis = date("d.m.Y",mktime(0,0,0,date("m"),date("d")+1,date("Y")));}
if(!$diag_von){$diag_von = date("d.m.Y",mktime(0,0,0,date("m"),1,date("Y")));}
if(!$diag_myd){$diag_myd = 2;}
?>


<Script language="JavaScript">
var calendar = null;
var elfieldid = null;
var elfieldname = null;
var eltab = null;

function selected(cal, date) {
eval("document.form1." + elfieldname + ".value = date;");
}

function closeHandler(cal) {
cal.hide();
}

function showCalendar(event,sell,fieldname,value) {
        elfieldname = fieldname;
        var sel = document.getElementById(sell);

        if (calendar != null) {
                calendar.hide();
        } else {
                var cal = new Calendar(true, null, selected, closeHandler);
                calendar = cal;
                cal.setRange(2000, 2010);
                cal.create();
        }

        calendar.setDateFormat("dd.mm.y");    // set the specified date format
        calendar.sel = sel;                 // inform it what input field we use
        if(value){calendar.parseDate(value);}      // try to parse the text in field
        calendar.showAtElement(sel);        // show the calendar below it
        return false;
}

function view_detail(USER,ID,DIAG_VON,DIAG_BIS){
        document.location.href = "main_admin.php?action=setup_stat_user_deterg&user="+USER+"&ID="+ID+"&diag_von="+DIAG_VON+"&diag_bis="+DIAG_BIS+"";
}

function send_form(){
	var el = this.element;
	var ar = document.getElementsByTagName("input");
	var cc = null;
        document.form1.diag_user.value = "";

	for (var i = ar.length; i > 0;) {
	        cc = ar[--i];
                if(cc.name.substring(0,4) == "user" && cc.checked){
                        document.form1.diag_user.value = cc.value + ";" + document.form1.diag_user.value;
                }
        }
        document.form1.submit();
}

</Script>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_stat_user_dia">
<input type="hidden" name="diag_id" value="<?=$diag_id?>">
<input type="hidden" name="diag_user">

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" cellspacing="1" cellpadding="1">
<TR><TD COLSPAN="4">&nbsp;</TD></TR>

<TR BGCOLOR="<?= $farbschema['WEB3'] ?>">
<TD><B>Auswahl</B></TD>
<TD><B>User</B></TD>
<TD><B>Erster Login</B></TD>
<TD><B>Letzter Login</B></TD>
</TR>

<?php
$sqlquery =  "SELECT * FROM LMB_USERDB";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(odbc_fetch_row($rs)) {
        $ID = odbc_result($rs,"USER_ID");
        $sqlquery2 =  "SELECT MIN(LOGIN_DATE) AS MIN_DATE, MAX(LOGIN_DATE) AS MAX_DATE FROM LMB_HISTORY_USER WHERE USERID = ".$ID;
        $rs2 = odbc_exec($db,$sqlquery2) or errorhandle(odbc_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
        $bin2 = odbc_binmode($rs2,2);
        $bzm2 = 1;

        if($BGCOLOR == $farbschema['WEB7']){$BGCOLOR = $farbschema['WEB8'];} else {$BGCOLOR = $farbschema['WEB7'];}
        echo "<TR BGCOLOR=\"$BGCOLOR\">";
        echo"<TD ALIGN=\"CENTER\"><INPUT TYPE=\"CHECKBOX\" NAME=\"user\" VALUE=\"$ID\"></TD>";
        echo"<TD><A HREF=\"Javascript:view_detail('".urlencode(odbc_result($rs,"VORNAME")." ".odbc_result($rs,"NAME"))."','$ID',document.form1.diag_von.value,document.form1.diag_bis.value);\">".odbc_result($rs,"VORNAME")." ".odbc_result($rs,"NAME")."</A></TD>";
        echo"<TD>".get_date(odbc_result($rs2,"MIN_DATE"),2)."&nbsp;&nbsp;</TD>";
        echo"<TD>".get_date(odbc_result($rs2,"MAX_DATE"),2)."&nbsp;&nbsp;</TD>";
        echo"</TR>";
}
?>

<TR><TD COLSPAN="4"><HR></TD></TR>
<TR><TD><B>Zeitraum:</B></TD><TD COLSPAN="3">von:&nbsp;<INPUT TYPE="TEXT" NAME="diag_von" VALUE="<?=$diag_von?>" style="cursor:pointer;width:115px;" READONLY onclick="showCalendar(event,'diagv','diag_von',document.form1.diag_von.value)">
<span id="diagv" style="position:absolute;"></span>
<i class="lmb-icon lmb-calendar-alt" ALIGN="middle" BORDER="0" ALT="" style="cursor:pointer;" onclick="showCalendar(event,'diagv','diag_von',document.form1.diag_von.value)"></i>
<BR>bis:&nbsp;&nbsp;<INPUT TYPE="TEXT" NAME="diag_bis" VALUE="<?=$diag_bis?>" style="cursor:pointer;width:115px;" onclick="showCalendar(event,'diagb','diag_bis',document.form1.diag_bis.value)">
<span id="diagb" style="position:absolute;"></span>
<i class="lmb-icon lmb-calendar-alt" ALIGN="middle" BORDER="0" ALT="" style="cursor:pointer;" onclick="showCalendar(event,'diagb','diag_bis',document.form1.diag_bis.value)"></i></TD></TR>

<TR><TD><B>Statistik-Typ:&nbsp;</B></TD><TD COLSPAN="3"><SELECT NAME="diag_id" STYLE="width:150px"><OPTION VALUE="4">Anmeldedauer<OPTION VALUE="5">Anmeldehäufigkeit<OPTION VALUE="6">Zeitbreich</SELECT>&nbsp;<i class="lmb-icon lmb-bar-chart" BORDER="0"></i></TD></TR>
<TR><TD><B>Gliederung:</B></TD><TD COLSPAN="3"><SELECT NAME="diag_myd" STYLE="width:150px"><OPTION VALUE="1" <?php if($diag_myd == 1){echo "SELECTED";}?>>Jährlich<OPTION VALUE="2" <?php if($diag_myd == 2){echo "SELECTED";}?>>Monatlich<OPTION VALUE="3" <?php if($diag_myd == 3){echo "SELECTED";}?>>Täglich</SELECT></TD></TR>
<TR><TD>&nbsp;</TD><TD COLSPAN="3" HEIGHT="30" VALIGN="BOTTOM"><INPUT TYPE="BUTTON" value="berechne" OnClick="send_form();"></TD></TR>

</TABLE>
</TD></TR></TABLE>
</FORM>
