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
 * ID: 182
 */


/* --- Tabellenarray ------------------------------- */
$sqlquery = "SELECT TAB_ID,BESCHREIBUNG FROM LMB_CONF_TABLES";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm = 1;
while(odbc_fetch_row($rs,$bzm)) {
	$tabarray[odbc_result($rs,"TAB_ID")] = odbc_result($rs,"BESCHREIBUNG");
$bzm++;
}
/* --- Felderarray ------------------------------- */
$sqlquery = "SELECT FIELD_ID,TAB_ID,SPELLING FROM LMB_CONF_FIELDS";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm = 1;
while(odbc_fetch_row($rs,$bzm)) {
	$fieldarray[odbc_result($rs,"TAB_ID")][odbc_result($rs,"FIELD_ID")] = odbc_result($rs,"SPELLING");
$bzm++;
}

# --- Zeitperiode -----
if($periodid){
	$sqlquery = "SELECT LOGIN_DATE,UPDATE_DATE FROM LMB_HISTORY_USER WHERE ID = $periodid";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(odbc_fetch_row($rs,1)) {
		$diag_von = get_date(odbc_result($rs,"LOGIN_DATE"),1);
		$diag_bis = get_date(odbc_result($rs,"UPDATE_DATE"),1);
	}
}

?>


<script type="text/javascript" src="extern/jscalendar/calendar.js"></script>
<script type="text/javascript" src="extern/jscalendar/lang/calendar-de.js"></script>
<style type="text/css">@import url(extern/jscalendar/jscalendar.css);</style>

<Script language="JavaScript">
var calendar = null;
var elfieldid = null;
var elfieldname = null;
var eltab = null;


// ---------------- Sendkeypress----------------------
function sendkeydown(evt) {
if(evt.keyCode == 13){
document.form1.submit();
}
}

function selected(cal, date) {
eval("document.form1." + elfieldname + ".value = date;");
}

function closeHandler(cal) {
cal.hide();
}

function showCalendar(event,sell,fieldname,value) {
	elfieldname = fieldname;
	var sel = document.getElementById('diagv');
	var cal = new Calendar(true, null, selected, closeHandler);
	calendar = cal;
	cal.create();
	calendar.setDateFormat("%d.%m.%Y");
	calendar.sel = sel;
	if(value){calendar.parseDate(value);}
	calendar.showAtElement(sel);
	return false;
}


// --- Subtabellen-Popup-funktion ----------
function poprecord(id){
	if(document.getElementById(id)){
		if(document.getElementById(id).style.display){
			document.getElementById(id).style.display='';
		}else{
			document.getElementById(id).style.display='none';
		}
	}
}

function newwin(GTAB,ID) {
	spalte = open("main.php?<?=SID?>&action=gtab_change&ID=" + ID + "&gtabid=" + GTAB + "" ,"Datensatzdetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=0,width=700,height=600");
}


</SCRIPT>

<?
if(!$diag_von){$diag_von = date("d.m.Y");}
if(!$diag_bis){$diag_bis = date("d.m.Y");}
?>




<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_user_tracking">
<input type="hidden" name="userid" VALUE="<?=$userid?>">
<input type="hidden" name="typ" VALUE="<?=$typ?>">
<input type="hidden" name="order" VALUE="<?=$order?>">
<input type="hidden" name="loglevel" VALUE="<?=$loglevel?>">

<DIV>

<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
<?if($typ == 1){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD class="<?=$class?>" OnClick="document.form1.typ.value='1';document.form1.submit();"><?=$lang[1431]?></TD>
<?if($typ == 2){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD class="<?=$class?>" OnClick="document.form1.typ.value='2';document.form1.submit();"><?=$lang[1432]?></TD>
<?if($typ == 3){$class="tabpoolItemActive";}else{$class="tabpoolItemInactive";}?>
<TD class="<?=$class?>" OnClick="document.form1.typ.value='3';document.form1.submit();"><?=$lang[1433]?></TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>
<TR><TD class="tabpoolfringe">


<?if($typ != 3){?>
<TABLE BORDER="0" cellspacing="1" cellpadding="0" width="100%">

<TR><TD class="tabSubHeaderItem">
von:&nbsp;<INPUT TYPE="TEXT" NAME="diag_von" <?if($typ != 3){echo "VALUE=\"".$diag_von."\"";}?>style="cursor:pointer;width:115px;" READONLY onclick="showCalendar(event,'diagv','diag_von',document.form1.diag_von.value)">
<span id="diagv" style="position:absolute;"></span>
<i class="lmb-icon lmb-calendar-alt" ALIGN="middle" BORDER="0" style="cursor:pointer;" onclick="showCalendar(event,'diagv','diag_von',document.form1.diag_von.value)"></i>
&nbsp;&nbsp;&nbsp;bis:&nbsp;<INPUT TYPE="TEXT" NAME="diag_bis" <?if($typ != 3){echo "VALUE=\"".$diag_bis."\"";}?> style="cursor:pointer;width:115px;" onclick="showCalendar(event,'diagb','diag_bis',document.form1.diag_bis.value)">
<span id="diagb" style="position:absolute;"></span>
<i class="lmb-icon lmb-calendar-alt" ALIGN="middle" BORDER="0" ALT="" style="cursor:pointer;" onclick="showCalendar(event,'diagb','diag_bis',document.form1.diag_bis.value)"></i>
<?if($typ == 2){?>
&nbsp;<SELECT NAME="loglevel" onchange="document.form1.submit();"><OPTION VALUE="1" <?if($loglevel == 1){echo "SELECTED";}?>>Level 1<OPTION VALUE="2" <?if($loglevel == 2){echo "SELECTED";}?>>Level 2</SELECT>
<?}?>
</TD></TR>
</TABLE>
<hr>
<?}?>


<TABLE BORDER="0" cellspacing="1" cellpadding="0" width="100%">
<?

if($typ == 3){$where2 .= " AND ".LMB_DBFUNC_DATE."ERSTDATUM) = '".date("Y-m-d",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y")))."'";}
else{
	if(!$diag_von){$diag_von = date("d.m.Y");}
	if(!$diag_bis){$diag_bis = date("d.m.Y");}
	if(convert_date($diag_von)){$where = "AND ".LMB_DBFUNC_DATE."LOGIN_DATE) >= '".lmb_substr(convert_date($diag_von),0,10)."'";$where2 = "AND ".LMB_DBFUNC_DATE."ERSTDATUM) >= '".lmb_substr(convert_date($diag_von),0,10)."'";}else{$where = "AND ".LMB_DBFUNC_DATE."LOGIN_DATE) >= '".convert_date(date("Y-m-d 00:00:00."))."'";$where2 = "AND ".LMB_DBFUNC_DATE."ERSTDATUM) >= '".convert_date(date("Y-m-d 00:00:00."))."'";}
	if(convert_date($diag_bis)){$where .= " AND ".LMB_DBFUNC_DATE."LOGIN_DATE) <= '".lmb_substr(convert_date($diag_bis),0,10)."'";$where2 .= " AND ".LMB_DBFUNC_DATE."ERSTDATUM) <= '".lmb_substr(convert_date($diag_bis),0,10)."'";}
}
if($typ == 2){
	if(!$loglevel){$loglevel = 1;}
	$where2 .= " AND LOGLEVEL <= $loglevel";
}


if($typ == 1){
	?>
	<TR class="tabHeader">
	<TD class="tabHeaderItem"><?=$lang[1800]?></TD>
	<TD class="tabHeaderItem"><?=$lang[1801]?></TD>
	<TD class="tabHeaderItem"><?=$lang[1803]?></TD>
	<TD class="tabHeaderItem"><?=$lang[1802]?></TD>
	</TR>
	<?
	$sqlquery =  "SELECT DISTINCT ID,LOGIN_DATE, UPDATE_DATE, IP, HOST, ".dbf_9(array('LOGIN_DATE','UPDATE_DATE'))." AS DAUER FROM LMB_HISTORY_USER WHERE USERID = $userid $where ORDER BY LOGIN_DATE";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$bzm = 1;
	while(odbc_fetch_row($rs, $bzm)) {
	        if($BGCOLOR == $farbschema[WEB7]){$BGCOLOR = $farbschema[WEB8];} else {$BGCOLOR = $farbschema[WEB7];}
	        echo"<TR BGCOLOR=\"$BGCOLOR\">";
	        echo"  <TD NOWRAP>".get_date(odbc_result($rs,"LOGIN_DATE"),2)."&nbsp;&nbsp;</TD>";
	        echo"  <TD NOWRAP>".get_date(odbc_result($rs,"UPDATE_DATE"),2)."&nbsp;&nbsp;</TD>";
	        echo"  <TD NOWRAP>".lmb_substr(odbc_result($rs,"DAUER"),0,8)."&nbsp;&nbsp;</TD>";
	        echo"  <TD NOWRAP>".odbc_result($rs,"IP")."&nbsp;&nbsp;</TD>";
	        echo"</TR>";
	$bzm++;
	}
}elseif($typ == 2 OR $typ == 3){
	if($typ == 3){?>
		<Script language="JavaScript">
		function reload(){
			document.form1.submit();
		}
		var firsttime = null;
		function inusetime() {
			now = new Date();
			if(!firsttime){firsttime = now.getTime();}
			locktime = 30;
			thistime = now.getTime();
			leftsec = (thistime - firsttime) / 1000;
			leftsec = Math.round(leftsec);
			showtime = locktime - leftsec;
			lefttime = Math.floor(showtime/3600) + "." + Math.floor(showtime/60) + "." + (showtime - Math.floor(showtime/60)*60);

			if(showtime > 1){
				Timer = setTimeout("inusetime()",1000);
				document.getElementById("usetime").firstChild.nodeValue = lefttime;
			}else{
				reload();
			}
		}

		</Script>
		<div style="position:absolute;top:15px;left:340px" ID="usetime">refresh</div>
	<?}

	if($order == "ERSTDATUM"){$or_erst = "ERSTDATUM DESC";}else{$or_erst = "ERSTDATUM";}
	if($order == "ACTION"){$or_act = "ACTION DESC";}else{$or_act = "ACTION";}
	if($order == "TAB"){$or_tab = "TAB DESC";}else{$or_tab = "TAB";}
	?>
	<TR>
	<TD class="tabHeaderItem" STYLE="cursor:pointer" OnClick="document.form1.order.value='<?=$or_erst?>';document.form1.submit();"><?=$lang[1804]?></TD>
	<TD class="tabHeaderItem" STYLE="cursor:pointer" OnClick="document.form1.order.value='<?=$or_act?>';document.form1.submit();"><?=$lang[1805]?></TD>
	<TD class="tabHeaderItem" STYLE="cursor:pointer" OnClick="document.form1.order.value='<?=$or_tab?>';document.form1.submit();"><?=$lang[1806]?>&nbsp;&nbsp;</TD>
	<TD class="tabHeaderItem"><?=$lang[1807]?>&nbsp;&nbsp;</TD>
	</TR>
	<?
	if(!$order){
		$order = "ERSTDATUM";
		if($typ == 3){$order = "ERSTDATUM DESC";}
	}elseif($typ == 3){
		if($order == "ERSTDATUM"){$order = "ERSTDATUM DESC";}else{$order .= ",ERSTDATUM DESC";}
	}

	$popuparray = array(3,128,171,129,130,203,190,116,200,195,119);

	$sqlquery =  "SELECT * FROM LMB_HISTORY_ACTION WHERE USERID = $userid $where2 ORDER BY $order";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$bzm = 1;
	while(odbc_fetch_row($rs, $bzm)) {
		if(odbc_result($rs,"DATAID")){$dat_id = odbc_result($rs,"DATAID");}else{$dat_id = "";}
		$action_id = odbc_result($rs,"ACTION");
		if($action_id == 1){$action = "<SPAN STYLE=\"color:blue\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
		elseif($action_id == 11){$action = "<SPAN STYLE=\"color:red\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
		elseif($action_id == 164){$action = "<SPAN STYLE=\"color:orange\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
		elseif($action_id == 166){$action = "<SPAN STYLE=\"color:purple\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
		elseif($action_id == 201){$action = "<SPAN STYLE=\"color:brown\">".$lang[$LINK["desc"][$action_id]]."</SPAN>";}
		else{$action = $lang[$LINK["desc"][$action_id]];}

		if($BGCOLOR == $farbschema["WEB7"]){$BGCOLOR = $farbschema["WEB8"];} else {$BGCOLOR = $farbschema["WEB7"];}

		if($LINK["typ"][$action_id] == 3){$BGCOLOR1 = "";}
		elseif($LINK["typ"][$action_id] == 4){$BGCOLOR1 = "";}
		elseif($LINK["typ"][$action_id] == 2){$BGCOLOR1 = "#F1C2C2";}
		else{$BGCOLOR1 = "";}

		echo "<TR BGCOLOR=\"$BGCOLOR\" ";
		if(in_array($action_id,$popuparray)){
			$sqlquery1 = "SELECT DISTINCT ID,ERSTDATUM,USERID,TAB,FIELD,DATAID,FIELDVALUE FROM LMB_HISTORY_UPDATE WHERE DATAID = ".odbc_result($rs, "DATAID")." AND ACTION_ID = ".odbc_result($rs, "ID")." AND TAB = ".odbc_result($rs, "TAB")." ORDER BY ERSTDATUM";
			$sqlquery1c = "SELECT COUNT(*) AS RESULT FROM LMB_HISTORY_UPDATE WHERE DATAID = ".odbc_result($rs, "DATAID")." AND ACTION_ID = ".odbc_result($rs, "ID")." AND TAB = ".odbc_result($rs, "TAB");
			$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
			$numrows = lmb_num_rows($rs1,$sqlquery1c);
			if($numrows){
				echo "STYLE=\"cursor:pointer\" OnClick=\"poprecord('tr_$bzm')\" ";
				if($action_id == 3){$num = "(".$numrows.")";}
				$action = "<SPAN STYLE=\"color:green;\">".$lang[$LINK["desc"][$action_id]]."</SPAN> ".$num;
			}else{
				$action = $lang[$LINK["desc"][$action_id]];
			}
		}
		echo ">";
		echo"<TD NOWRAP BGCOLOR=\"$BGCOLOR1\">".get_date(odbc_result($rs,"ERSTDATUM"),2)."&nbsp;&nbsp;</TD>";
		echo"<TD NOWRAP>".$action."&nbsp;&nbsp;</TD>";
		echo"<TD NOWRAP>".$lang[$tabarray[odbc_result($rs,"TAB")]]."&nbsp;&nbsp;</TD>";
		echo"<TD NOWRAP>".$dat_id."&nbsp;&nbsp;</TD>";
		echo"</TR>";
		if(in_array($action_id,$popuparray)){
			echo "<TR STYLE=\"display:none\" ID=\"tr_$bzm\"><TD COLSPAN=\"4\"><TABLE BORDER=\"0\">";
			$bzm1 = 1;
			while(odbc_fetch_row($rs1, $bzm1)) {
				unset($val);
				if($action_id == 3){$nowr = "";}else{$nowr = "NOWRAP";}
				$ftype = $gfield[odbc_result($rs1, "TAB")][field_type][odbc_result($rs1, "FIELD")];
				if($ftype == 11){
					$links = explode(";",odbc_result($rs1, "FIELDVALUE"));
					if($links){
						foreach($links as $key1 => $value1){
							$lid = lmb_substr($value1,1,10);
							$val[] = "<A HREF=\"#\" OnClick=\"newwin('".$gfield[odbc_result($rs1, "TAB")]["verkntabid"][odbc_result($rs1, "FIELD")]."','".$lid."');\">$value1</A>";
						}
						echo "<TR><TD $nowr STYLE=\"width:50px\">&nbsp;</TD><TD STYLE=\"width:100px\" VALIGN=\"TOP\">".$lang[$fieldarray[odbc_result($rs,"TAB")][odbc_result($rs1, "FIELD")]]."</TD><TD $nowr>".implode("|",$val)."</TD></TR>";
					}
				}else{
					echo "<TR><TD $nowr STYLE=\"width:50px\">&nbsp;</TD><TD STYLE=\"width:100px\" VALIGN=\"TOP\">".$lang[$fieldarray[odbc_result($rs,"TAB")][odbc_result($rs1, "FIELD")]]."</TD><TD $nowr>".nl2br(htmlentities(odbc_result($rs1, "FIELDVALUE"),ENT_QUOTES,$umgvar["charset"]))."</TD></TR>";
				}
				$bzm1++;
			}
			echo "</TABLE></TD></TR>";
		}
		$bzm++;
		if($typ == 3 AND $bzm >= 31){$bzm = 0;}
		if($typ == 2 AND $bzm >= 500){$bzm = 0;}
	}
}
?>



</TABLE>

</TD></TR></TABLE>

</FORM>