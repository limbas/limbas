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
 * ID: 212
 */


?>

<Script language="JavaScript">

img3=new Image();img3.src="pic/outliner/plusonly.gif";
img4=new Image();img4.src="pic/outliner/minusonly.gif";


function popup(ID,LEVEL){
	var cli;
	if(browser_ns5){cli = ".nextSibling";}else{cli = "";}
	eval("var nested = document.getElementById('f_"+ID+"').nextSibling"+cli);
	var picname = "i" + ID;
	if(document.images[picname].src == img4.src) {
		document.images[picname].src = img3.src;
		nested.style.display="none";
	}else{
		document.images[picname].src = img4.src;
		nested.style.display='';
	}
}

function popup_all(fid){
	if(browser_ns5){cli = ".nextSibling";}else{cli = "";}
	var ar = document.getElementsByTagName('div');
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,2) == 'f_' && cc.id == 'f_'+fid){
			eval("var nested = cc.nextSibling"+cli);
			while(nested){
				if(nested.style){
					nested.style.display='';
				}
				nested = nested.parentNode;
			}

		}
	}
}

function close_all(){
	if(browser_ns5){cli = ".nextSibling";}else{cli = "";}
	var ar = document.getElementsByTagName('div');
	for (var i = ar.length; i > 0;) {
		cc = ar[--i];
		if(cc.id.substring(0,2) == 'f_'){
			eval("var nested = cc.nextSibling"+cli);
			nested.style.display='none';
		}
	}
}

function show_val(val) {

	if(val){
		var arr1 = val.split(";");
		for (var i in arr1){
			var arr2 = arr1[i].split(",");
			if(arr2[0] == "field"){
				var el = "memo_" + arr2[1];
				if(document.form1[el]){
					document.form1[el].checked = 1;
				}
			}else if(arr2[0] == "file"){
				var el = document.getElementById("ifile_"+arr2[1]);
				if(el){
					if(arr2[2] == "s"){document.form1['subdir'].checked = 1;}
					popup_all(arr2[1]);
					el.checked = 1;
				}
			}
		}
	}else{
		var cc = null;
		var ar = document.getElementsByTagName("input");
		for (var i = ar.length; i > 0;) {
			cc = ar[--i];
			if(cc.type == "checkbox" && cc.name.substr(0,5) != "activ"){
				cc.checked = 0;
			}
		}
		document.form1['subdir'].checked = 0;
		close_all();
	}
}

function index_del(val) {
	var ok = confirm('delete job?');
	if(ok){
		document.form1.del_index.value = val;
		document.form1.submit();
	}
}

function index_refresh(val) {
	var ok = confirm('renew job?');
	if(ok){
		document.form1.refresh_index.value = val;
		document.form1.submit();
	}
}
</SCRIPT>


<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_indize_db">
<input type="hidden" name="del_index">
<input type="hidden" name="refresh_index">

<div class="lmbPositionContainerMain small">
<table class="tabfringe" border="0" cellspacing="0" cellpadding="1"><tr><td>


<TABLE BORDER="0" WIDTH="600" cellspacing="1" cellpadding="2">

<?php
if(!$kategorie){
	echo "<tr class=\"tabHeader\"><td colspan=\"7\" class=\"tabHeaderItem\">Auswahl</td></tr><tr><td>&nbsp;</td></tr><tr><td>";
	echo "<ul>";
	foreach ($jobdir["name"] as $key => $value){
		$kat = explode(".",$jobdir["name"][$key]);
		$kat = strtoupper($kat[0]);
		echo "<li><a href=\"main_admin.php?action=setup_indize_db&kategorie=".$jobdir["name"][$key]."\"><input type=\"button\" style=\"width:150px;\" value=\"".$jobdir["name"][$key]."\"></a></li>";
	}
	echo "</ul></td></tr>";
}else{
	echo "<tr class=\"tabHeader\"><td colspan=\"7\" class=\"tabHeaderItem\">";
	echo "<SELECT NAME=\"kategorie\" onchange=\"document.form1.submit();\"><OPTION>";
	foreach ($jobdir["name"] as $key => $value){
		$kat = explode(".",$value);
		$kat = strtoupper($kat[0]);
		if($kategorie == $value){$SELECTED = "SELECTED";$req = $jobdir["path"][$key].$value;$kategoriedesc = $kat;}else{$SELECTED = "";}
		echo "<option value=\"".$value."\" $SELECTED>".$kat."</option>";
	}
	echo "</SELECT></td></tr>";
}

if($kategorie AND $req){
?>

<TR class="tabHeader">
    <TD class="tabHeaderItem"><?=$lang[2068]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2069]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2070]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2071]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2072]?></TD>
    <TD class="tabHeaderItem" align="center">start</TD>
    <TD class="tabHeaderItem" align="center"><?=$lang[2073]?></TD>
</TR>
<?php
$sqlquery = "SELECT * FROM LMB_CRONTAB WHERE KATEGORY = '".strtoupper($kategoriedesc)."' ORDER BY ERSTDATUM";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
$bzm = 1;
while(odbc_fetch_row($rs, $bzm)){
	$val = odbc_result($rs,"VAL");
	$val0 = explode(";",$val);
	if(odbc_result($rs,"KATEGORY") == "INDIZE"){$color = "#7AB491";}
	if(odbc_result($rs,"ACTIV")){$activ = "CHECKED";}else{$activ = "";}
	if(odbc_result($rs,"KATEGORY") == "TEMPLATE"){$template = "(".$val0[0].")";}else{$template = "";}
	echo "<TR class=\"tabBody\" OnClick=\"show_val('');show_val('".$val."');\">
	<TD>&nbsp;".odbc_result($rs,"ID")."&nbsp;</TD>
	<TD  BGCOLOR=\"$color\">&nbsp;".odbc_result($rs,"KATEGORY")."&nbsp;$template</TD>
	<TD>&nbsp;".odbc_result($rs,"START")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"DESCRIPTION")."&nbsp;</TD>
	<TD>&nbsp;<INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent;\" NAME=\"activ_".odbc_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_indize_db&kategorie=$kategorie&activate_job=".odbc_result($rs,"ID")."'\" $activ>&nbsp;</TD>
	<TD ALIGN=\"CENTER\">&nbsp;<i class=\"lmb-icon lmb-action\" STYLE=\"cursor:pointer;border:1px solid grey;\" NAME=\"activate_".odbc_result($rs,"ID")."\" OnClick=\"document.location.href='main_admin.php?&action=setup_indize_db&kategorie=$kategorie&run_job=".odbc_result($rs,"ID")."';limbasWaitsymbol(event,1);\"></i>&nbsp;</TD>
	<TD ALIGN=\"CENTER\">&nbsp;<A HREF=\"main_admin.php?&action=setup_indize_db&kategorie=$kategorie&del_job=".odbc_result($rs,"ID")."\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A>&nbsp;</TD></TR>";
	$cronvalue[] = str_replace(";"," ",odbc_result($rs,"START"))."\twebuser (/usr/local/bin/php \"".$umgvar["pfad"]."/cron.php\" < /bin/echo ".odbc_result($rs,"ID").")";
	$bzm++;
}

if($alert){$dspl = "";}else{$dspl = "none";}
?>
<TR><TD class="tabFooter" colspan="7"></TR>
</TABLE>

<BR>

<TABLE BORDER="0" cellspacing="1" cellpadding="2" class="tabfringe">
<TR class="tabHeader"><TD class="tabHeaderItem" colspan="6"><a href="#" OnClick="document.getElementById('crontab').style.display='';">cron</a></TD></TR>
<TR class="tabHeader">
    <TD class="tabHeaderItem"><?=$lang[2074]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2075]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2076]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2077]?></TD>
    <TD class="tabHeaderItem"><?=$lang[2078]?></TD>
</TR>
<TR class="tabBody"><TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[0]" VALUE="0" STYLE="width:100%"></TD><TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[1]" VALUE="1" STYLE="width:100%"></TD><TD style="width:50px;"><INPUT TYPE="TEXT" VALUE="*" NAME="cron[2]" STYLE="width:100%"></TD><TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[3]" VALUE="*" STYLE="width:100%"></TD><TD style="width:50px;"><INPUT TYPE="TEXT" NAME="cron[4]" VALUE="*" STYLE="width:100%"></TD><TD style="width:50px;"><INPUT TYPE="SUBMIT" NAME="add_job" VALUE="<?=$lang[2079]?>"></TD></TR>
<TR class="tabBody" ID="crontab" STYLE="display:<?=$dspl?>"><TD colspan="7"><TEXTAREA STYLE="width:600px;height:100px;font-size:9px;overflow:hidden;">
<?php
if($cronvalue){
	foreach($cronvalue as $key => $value){
		echo str_replace(";"," ",$value)."\n";
	}
}
?>
</TEXTAREA></TD></TR>
<TR><TD class="tabFooter" colspan="7"></TR>
</TABLE>
<BR>



<?php
if($kategoriedesc == "INDIZE" OR $kategoriedesc == "OCR"){
?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR>
	<TD VALIGN="TOP"><TABLE cellspacing="0" STYLE="border:1px solid <?=$farbschema["WEB4"]?>;"><TR><TD STYLE="border-bottom:1px solid <?=$farbschema[WEB4]?>;background-color:<?=$farbschema[WEB7]?>"><?=$lang[2080]?></TD><TD VALIGN="TOP" STYLE="border-bottom:1px solid <?=$farbschema[WEB4]?>;background-color:<?=$farbschema[WEB7]?>"><?=$lang[2081]?><input type="checkbox" name="subdir">&nbsp;&nbsp;</TD></TR>
	<TR><TD COLSPAN="2">
	<?php

	# --- Dateiordner ---
	function files1($LEVEL){
		global $file_struct;
		global $farbschema;
		global $lang;
	
		if($LEVEL){
			if($LEVEL){$vis = "style=\"display:none\"";}else{$vis = "";}
			echo "<div id=\"foldinglist\" $vis>\n";
			echo "<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\" STYLE=\"border-collapse:collapse;\"><TR><TD WIDTH=\"10\">&nbsp;</TD><TD>\n";
		}
		$bzm = 0;
		while($file_struct["id"][$bzm]){
			if($file_struct[level][$bzm] == $LEVEL){
				if(in_array($file_struct["id"][$bzm],$file_struct[level])){
					$next = 1;
					$pic = "<IMG SRC=\"pic/outliner/plusonly.gif\" NAME=\"i".$file_struct["id"][$bzm]."\" OnClick=\"popup('".$file_struct["id"][$bzm]."','$LEVEL')\" STYLE=\"cursor:hand\">";
				}else{
					$next = 0;
					$pic = "<IMG SRC=\"pic/outliner/blank.gif\">";
				}
				echo "<div ID=\"f_".$file_struct["id"][$bzm]."\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\" WIDTH=\"100%\">";
				echo "<TR><TD WIDTH=\"20\">$pic</TD><TD WIDTH=\"20\"><i class=\"lmb-icon lmb-folder-closed\" ID=\"p".$file_struct["id"][$bzm]."\" NAME=\"p".$file_struct["id"][$bzm]."\" STYLE=\"cursor:hand\"></i></TD><TD>&nbsp;".$file_struct[name][$bzm]."</TD>";
				echo "<TD ALIGN=\"RIGHT\"><TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" BORDER=\"0\"><TR>";
				# --- view ---
				if(!$file_struct[readonly][$bzm]){
					echo "<TD><INPUT TYPE=\"CHECKBOX\" ID=\"ifile_".$file_struct["id"][$bzm]."\" NAME=\"ifile[".$file_struct["id"][$bzm]."]\" STYLE=\"border:none;background-color:transparent;\"></TD>";
					echo "<TD><i class=\"lmb-icon lmb-trash\" TITLE=\"drop jobresults\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_del('file_".$file_struct["id"][$bzm]."');\"></i><TD><i class=\"lmb-icon lmb-action\" TITLE=\"run job\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_refresh('file_".$file_struct["id"][$bzm]."');\"></i></TD>";
				}
				echo "</TR></TABLE></TD></TR></TABLE></div>\n";
				if($next){
					files1($file_struct["id"][$bzm]);
				}else{
					echo "<div id=\"foldinglist\" style=\"display:none;\"></div>\n";
				}
	
			}
			$bzm++;
		}
		if($LEVEL){
			echo "</TD></TR></TABLE>\n";
			echo "</div>\n";
		}
	}
	files1(0);
	?>
	</TD></TR></TABLE>
	</TD><TD>&nbsp;&nbsp;&nbsp;</TD>
	
	
	<?php
	# --- Tabellenfelder ---
	if($gtab["table"] AND $kategoriedesc == "INDIZE"){
		foreach($gtab["table"] as $key => $value){
			if($gfield[$key]["indize"]){
				if(in_array("1",$gfield[$key]["indize"])){
					echo "<TD VALIGN=\"TOP\"><TABLE cellspacing=\"0\" cellpadding=\"3\" STYLE=\"border:1px solid ".$farbschema[WEB4].";\"><TR><TD COLSPAN=\"4\" STYLE=\"border-bottom:1px solid ".$farbschema[WEB4].";background-color:".$farbschema[WEB7]."\">".$gtab[desc][$key]."</TD></TR>\n";
					foreach($gfield[$key]["id"] as $key1 => $value1){
						if($gfield[$key][indize][$key1] AND $gfield[$key][data_type][$key1] == 39){
							echo "<TR><TD STYLE=\"border:none;color:green\" TITLE=\"".$gfield[$key][beschreibung][$key1]."\">".$gfield[$key][spelling][$key1]."</TD><TD><INPUT TYPE=\"CHECKBOX\" STYLE=\"border:none;background-color:transparent\" NAME=\"memo_".$key."_".$key1."\"></TD><TD><i class=\"lmb-icon lmb-trash\" TITLE=\"drop Index\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_del('field_".$key."_".$key1."');\"></i><TD><i class=\"lmb-icon lmb-action\" TITLE=\"renew Index\" BORDER=\"0\" STYLE=\"cursor:pointer\" OnClick=\"index_refresh('field_".$key."_".$key1."');\"></i></TD></TR>\n";
						}
					}
					echo "</TABLE></TD><TD>&nbsp;&nbsp;&nbsp;</TD>\n";
				}
			}
		}
	}
}


}

if($kategoriedesc == "TEMPLATE"){
	?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="2" class="tabfringe">
	<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[2207]?> (*.job)</TD><TD class="tabHeaderItem"><?=$lang[126]?></TD></TR>
	<TR class="tabBody"><TD>
	<SELECT STYLE="width:120px;" NAME="job_template"><OPTION>
	<?php
	$extfiles = read_dir($umgvar["pfad"]."/EXTENSIONS",1);
	
	foreach ($extfiles["name"] as $key1 => $filename){
		$ext = explode(".",$filename);
		$ext = $ext[count($ext)-1];
		if($extfiles["typ"][$key1] == "file" AND strtolower($ext) == "job"){
			$path = substr($extfiles["path"][$key1],strlen($umgvar["pfad"]),100);
			echo "<OPTION VALUE=\"".$path.$filename."\">".str_replace("/EXTENSIONS/","",$path).$filename;
		}
	}
	?>
	</SELECT>
	</TD>
	<TD><INPUT TYPE="TEXT" NAME="job_desc" STYLE="width:250px;"></TD>
	</TR>
	
	<TR><TD class="tabFooter" colspan="2"></TR>
	</TABLE>
	<?
	
}
?>

</TR></TABLE>


<?if($kategoriedesc){?>
<BR><BR>
<TABLE BORDER="0" cellspacing="0" cellpadding="2" STYLE="collapse:collapse">
<TR class="tabHeader"><TD><U>Crontab Value</U></TD></TR>
<TR ID="crontab" BGCOLOR="<?=$farbschema[WEB6]?>"><TD><TEXTAREA STYLE="font-size:9px;width:600px;height:100px;overflow:hidden;">
<?php
if($cronvalue){
	foreach($cronvalue as $key => $value){
		echo str_replace(";"," ",$value)."\n";
	}
}
?>
</TEXTAREA></TD></TR>
</TABLE>
<?}?>



</tr></td></div>
</FORM>
