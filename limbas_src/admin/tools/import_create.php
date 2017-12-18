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
 * ID: 150
 */


# check fieldtype for import
if($import_typ == "txt"){
	$parsefile = import_parse_uploadfile($txt_terminate,$txt_calculate,$txt_enclosure);
	$e = $parsefile["findtype"];
	$header = $parsefile["header"];
	$tabname = $parsefile["tabname"];
# check fieldtype for convert
}elseif($convertimport AND $tabname){
	$import_typ = "tab";
	/* --- Tabellenfelder auslesen --------------------------------------------- */
	$columns = dbf_5(array($DBA["DBSCHEMA"],$tabname));
	foreach ($columns["columnname"] as $key => $value){
		$header[] = $value;
		$e["field_type"][] = $columns["datatype"][$key];
		$e["length"][] = $columns["length"][$key];
	}
}


/* --- Tabellengruppen ----------------------------------------------- */
$sqlquery = "SELECT ID,LEVEL,NAME,BESCHREIBUNG,SORT FROM LMB_CONF_GROUPS ORDER BY LMB_CONF_GROUPS.SORT";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(odbc_fetch_row($rs)) {
	$tabgroup_["id"][] = odbc_result($rs, "ID");
	$tabgroup_["name"][] = $lang[odbc_result($rs,"NAME")];
}



/* --- Feldtyp --------------------------------------------- */
$sqlquery = "SELECT DISTINCT * FROM LMB_FIELD_TYPES WHERE DATA_TYPE NOT IN (18,31,32,27,24,43,48,41,15,47,13,38,46) AND DATA_TYPE < 100 AND DATA_TYPE > 0 ORDER BY SORT";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
unset($result_type);
$bzm = 1;
while(odbc_fetch_row($rs, $bzm)) {
	$tid = odbc_result($rs, "ID");
	$result_type["id"][$tid] = odbc_result($rs, "ID");
	$result_type["beschreibung"][$tid] = odbc_result($rs, "DATA_TYPE_EXP");
	$result_type["size"][$tid] = odbc_result($rs, "SIZE");
	$result_type["data_type"][$tid] = odbc_result($rs, "DATA_TYPE");
	$result_type["hassize"][$tid] = odbc_result($rs, "HASSIZE");
	$bzm++;
}


/* ---------------------- Spaltenliste ---------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form2">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_import">
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="tab_group" VALUE="<?=$tab_group?>">
<INPUT TYPE="hidden" NAME="tabcount" VALUE="<?=$tabcount?>">
<INPUT TYPE="hidden" NAME="tabexist" VALUE="<?=$tabexist?>">
<INPUT TYPE="hidden" NAME="import_typ" VALUE="<?=$import_typ?>">
<INPUT TYPE="hidden" NAME="txt_calculate" VALUE="<?=$txt_calculate?>">
<INPUT TYPE="hidden" NAME="filetxt_name" VALUE="<?=$filetxt_name?>">
<INPUT TYPE="hidden" NAME="convertimport" VALUE="<?=$convertimport?>">
<INPUT TYPE="hidden" NAME="txt_terminate" VALUE="<?=$txt_terminate?>">
<INPUT TYPE="hidden" NAME="txt_enclosure" VALUE="<?=$txt_enclosure?>">
<INPUT TYPE="hidden" NAME="txt_encode" VALUE="<?=$txt_encode?>">

<BR><HR>
<?
# decode/encode utf8
if($txt_encode){
	if($umgvar["charset"] == "UTF-8"){
		$tabname = utf8_encode($tabname);
	}else{
		$tabname = iconv("UTF-8",$GLOBALS["umgvar"]["charset"],$tabname);
	}
}
		
echo "<TABLE>";
/* --- Tabelle ------------- */
echo "<TR BGCOLOR=\"".$farbschema["WEB5"]."\"><TD COLSPAN=\"7\" ALIGN=\"CENTER\"><B>$lang[1023]</B></TD></TR>";
echo "<TR BGCOLOR=\"".$farbschema["WEB6"]."\"><TD><B>$lang[1027]</B></TD><TD><B>$lang[1028]</B></TD><TD><B>$lang[1029]</B></TD><TD COLSPAN=\"4\"></TD></TR>";
if($convertimport){$readonly = "READONLY";}
echo "<TR BGCOLOR=\"".$farbschema["WEB7"]."\"><TD><INPUT TYPE=\"TEXT\" NAME=\"tab_name\" VALUE=\"".$tabname."\" $readonly></TD><TD><INPUT TYPE=\"TEXT\" SIZE=\"20\" VALUE=\"".$tabname."\" NAME=\"tab_spelling\"></TD>";
echo "<TD><SELECT NAME=\"tab_group\">";

foreach($tabgroup_["id"] as $bzm => $val){
	if($tabgroup_["id"][$bzm] == $tab_group){$SELECTED = "SELECTED";}else{$SELECTED = "";}
	echo "<OPTION VALUE=\"".$tabgroup_["id"][$bzm]."\" $SELECTED>".$tabgroup_["name"][$bzm]."</OPTION>";
}


echo "</TD>";
echo "<TD COLSPAN=\"4\"></TD>";

/* --- Felder ------------- */
echo "<TR BGCOLOR=\"".$farbschema["WEB5"]."\"><TD COLSPAN=\"7\" ALIGN=\"CENTER\"><B>$lang[1031]</B></TD></TR>";
echo "<TR BGCOLOR=\"".$farbschema["WEB6"]."\"><TD><B>$lang[1032]</B></TD><TD><B>$lang[1028]</B></TD><TD><B>$lang[1033]</B></TD><TD><B>$lang[1034]</B></TD><TD><B>$lang[2654]</B></TD><TD colspan=\"2\" align=\"right\"><B>$lang[1035]</B></TD></TR>";
#$reserved_fields = array("ID","ERSTDATUM","EDITDATUM","ERSTUSER","EDITUSER","INUSE_TIME","INUSE_USER","DEL");
$bzm = 0;
foreach ($header as $bzm => $hval){

	# csv
	#if($txt_terminate == ";"){
	#	$hval = trim($hval,"\"");
	#}

	if(trim($hval)){
		
		# decode/encode utf8
		if($txt_encode){
			if($umgvar["charset"] == "UTF-8"){
				$field_name = utf8_encode($field_name);
				$hval = utf8_encode($hval);
				$header2[$bzm] = utf8_encode($header2[$bzm]);
			}else{
				$field_name = iconv("UTF-8",$GLOBALS["umgvar"]["charset"],$field_name);
				$hval = iconv("UTF-8",$GLOBALS["umgvar"]["charset"],$hval);
				$header2[$bzm] = iconv("UTF-8",$GLOBALS["umgvar"]["charset"],$header2[$bzm]);
			}
		}
		/* --- Feld umbenennen ------------- */
		$field_name = parse_db_syntax($hval,0);
		/* --- Feldliste ------------- */
		if($BGCOLOR == $farbschema["WEB7"]){$BGCOLOR = $farbschema["WEB6"];} else {$BGCOLOR = $farbschema["WEB7"];}
		#if($field_name != $hval){$BGCOLOR = "#CCCCCC";}
		if($ifield_name[$bzm]){$field_name = $ifield_name[$bzm];}
		if($ifield_spelling[$bzm]){$hval = $ifield_spelling[$bzm];}
		if($ifield_desc[$bzm]){$header2[$bzm] = $ifield_desc[$bzm];}else{$header2[$bzm] = $hval;}
		
		echo "<TR BGCOLOR=\"$BGCOLOR\">\n";
		echo "<TD><INPUT TYPE=\"TEXT\" SIZE=\"20\" NAME=\"ifield_name[$bzm]\" VALUE=\"".$field_name."\" MAXLENGHT=\"18\"><INPUT TYPE=\"HIDDEN\" NAME=\"ifield_header[$bzm]\" VALUE=\"".$hval."\"></TD>\n";
		echo "<TD><INPUT TYPE=\"TEXT\" SIZE=\"20\" NAME=\"ifield_spelling[$bzm]\" VALUE=\"".$hval."\"></TD>\n";
		echo "<TD><INPUT TYPE=\"TEXT\" SIZE=\"20\" NAME=\"ifield_desc[$bzm]\" VALUE=\"".$header2[$bzm]."\"></TD>\n";
		echo  "<TD><SELECT NAME=\"ifield_typ[$bzm]\">\n";
		$ctype = $e["field_type"][$bzm]." (".$e["length"][$bzm].")";
		$fype = explode("(",$ctype);
		$fype_type = lmb_strtoupper(trim($fype[0]));
		$fype_size = lmb_substr($fype[1],0,lmb_strlen($fype[1])-1);
		if($import_typ == "tab"){$fype_type = constant("LMB_DBRETYPE_".trim(lmb_strtoupper($fype_type)));}
		$result_typeid = translate_fieldtype($fype_type,$fype_size,0,$hval);
		foreach($result_type["id"] as $bzm2 => $value2){
			if($result_type["id"][$bzm2] == $result_typeid){$SELECTED = "selected";}else{$SELECTED = "";}
			echo "<OPTION VALUE=\"".$result_type["id"][$bzm2]."\" $SELECTED>".$lang[$result_type["beschreibung"][$bzm2]]."\n";
		}
		echo "</SELECT></TD>";
		echo "<td>";
		if($result_type["hassize"][$result_typeid]){
			echo "<input type=\"text\" style=\"width:40px;\" name=\"ifield_size[$bzm]\" value=\"".$fype_size."\">";
		}
		echo "</td>";
		echo "<TD nowrap ALIGN=\"LEFT\">".$ctype."</TD>";
		if($ifield_select[$bzm] == "on" OR !$ifield_select[$bzm]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<TD ALIGN=\"CENTER\">";
		#if($field_name == $hval){
			echo "<INPUT TYPE=\"CHECKBOX\" NAME=\"ifield_select[$bzm]\" $CHECKED>\n\n";
		#}
		echo "</TD></TR>\n\n";
	}
}
/* --- Tool-Leiste ------------- */
echo "<TR><TD>&nbsp;</TD></TR>";
echo "<TR><TD COLSPAN=\"7\">$lang[1036]:&nbsp;<INPUT TYPE=\"RADIO\" NAME=\"ifield_data\" VALUE=\"1\">";
echo "&nbsp;&nbsp;&nbsp;$lang[1037]:&nbsp;<INPUT TYPE=\"RADIO\" NAME=\"ifield_data\" VALUE=\"2\" CHECKED>";
echo "&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"SUBMIT\" NAME=\"import_create\" VALUE=\"$lang[1038]\"></TD></TR>";
echo "</TABLE>";
$bzm2 = 0;
while($result_type["id"][$bzm2]){
	if($result_type["data_type"][$bzm2] == 39){$size = 6;}else{$size = $result_type["size"][$bzm2];}
	echo "<INPUT TYPE=\"HIDDEN\" NAME=\"size_".$result_type[id][$bzm2]."\" VALUE=\"".$size."\">\n";
	$bzm2++;
}
echo "</FORM>";
if($txtdat){fclose($txtdat);}
?>

<BR><BR>