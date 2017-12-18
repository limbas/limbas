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



function show_attach_file($gtabid,$header){
	global $gfield;
	global $lang;
	global $ifield;
	
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		if($gfield[$gtabid]["field_type"][$key] >= 100 OR $gfield[$gtabid]["data_type"][$key] == 22){continue;}
		$isactive = 0;
		echo "<TR>\n";
		echo "<TD nowrap>".$gfield[$gtabid]["field_name"][$key]."</b> (<i>".$gfield[$gtabid]["spelling"][$key]."</i>)</TD>\n";
		echo "<TD>".$gfield[$gtabid]["data_type_exp"][$key]."</TD>\n";
		echo "<td></td>";
		
		echo "<td><select name=\"ifield[colname][".$key."]\" OnChange=\"document.getElementById('ifield_used_$key').checked=true;\"><option value=\"0\"></option>";
		foreach ($header as $fkey => $fval){
			if($ifield["colname"][$key]){
				if(lmb_strtolower(trim($ifield["colname"][$key])) == lmb_strtolower(trim($fval))){$SELECTED = "SELECTED";}else{$SELECTED = "";}
			}else{
				if(lmb_strtolower(trim($fval)) == lmb_strtolower(trim($gfield[$gtabid]["field_name"][$key])) OR lmb_strtolower(trim($fval)) == lmb_strtolower(trim($gfield[$gtabid]["field_name"][$key]))){$SELECTED = "SELECTED";}else{$SELECTED = "";}
			}
			if($SELECTED){$isactive=1;}
			echo "<option value=\"".$fval."\" $SELECTED>".$fval."</option>";
		}
		echo "</select></td>";
		
		if($ifield["used"]){
			if($ifield["used"][$key]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		}elseif($isactive){
			$CHECKED = "checked";
		}else{
			$CHECKED = "";
		}
		echo "<td align=\"center\"><input type=\"checkbox\" id=\"ifield_used_$key\" name=\"ifield[used][".$key."]\" $CHECKED></td>";
	
		echo "</TR>";
	}

	echo "<TR><TD colspan=\"5\"><hr></TD></TR>";
	echo "<TR><TD valign=\"top\"><input type=\"submit\" name=\"import_attach\" value=\"$lang[1040]\">&nbsp;&nbsp;&nbsp;<input type=\"button\" value=\"Einstellung laden\" OnClick=\"document.form2.partimport.value=1;document.form2.import_attach_loadsetting.value=1;document.form2.submit();\"></TD>
	<TD colspan=\"4\" valign=\"top\" align=\"right\">
	<input type=\"button\" value=\"Einstellung speichern\" OnClick=\"document.form2.partimport.value=1;document.form2.import_attach_savesetting.value=1;document.form2.submit();\">
	</TD></TR>";
	echo "<TR><TD colspan=\"5\" class=\"tabFooter\"></TD></TR>";
}


/* ---------------------- Spaltenliste ---------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form2">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_import">
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="partimport">
<INPUT TYPE="hidden" NAME="tab_group" VALUE="<?=$tab_group?>">
<INPUT TYPE="hidden" NAME="tabcount" VALUE="<?=$tabcount?>">
<INPUT TYPE="hidden" NAME="tabexist" VALUE="<?=$tabexist?>">
<INPUT TYPE="hidden" NAME="import_typ" VALUE="<?=$import_typ?>">
<INPUT TYPE="hidden" NAME="attach_gtabid" VALUE="<?=$attach_gtabid?>">
<INPUT TYPE="hidden" NAME="txt_calculate" VALUE="<?=$txt_calculate?>">
<INPUT TYPE="hidden" NAME="filetxt_name" VALUE="<?=$filetxt_name?>">
<INPUT TYPE="hidden" NAME="convertimport" VALUE="<?=$convertimport?>">
<INPUT TYPE="hidden" NAME="txt_terminate" VALUE="<?=$txt_terminate?>">
<INPUT TYPE="hidden" NAME="txt_enclosure" VALUE="<?=$txt_enclosure?>">
<INPUT TYPE="hidden" NAME="txt_encode" VALUE="<?=$txt_encode?>">
<INPUT TYPE="hidden" NAME="tabname" VALUE="<?=$tabname?>">
<INPUT TYPE="hidden" NAME="import_attach_savesetting">
<INPUT TYPE="hidden" NAME="import_attach_loadsetting">
<BR><HR>
<?php

echo "<TABLE class=\"tabBody\" width=\"100%\">";
/* --- Tabelle ------------- */

echo "<tr>
<td class=\"tabheader\" colspan=\"2\" align=\"center\"><b>".$lang[1023]." ".$gtab["table"][$attach_gtabid]."</b></td>
<td align=\"center\"><i class=\"lmb-icon lmb-arrow-right\"></i></td>
<td class=\"tabheader\" align=\"center\"><b>file ".$filename."</b></td>
<td class=\"tabheader\" align=\"center\"></td>
</tr>";

$parsefile = import_parse_uploadfile($txt_terminate,$txt_calculate,$txt_enclosure);
$header = $parsefile["header"];

if(!is_dir($umgvar["pfad"]."/USER/".$session["user_id"]."/settings")){
	mkdir($umgvar["pfad"]."/USER/".$session["user_id"]."/settings");
}

$settpath = $umgvar["pfad"]."/USER/".$session["user_id"]."/settings/import_attach.conf";
$tabname = $gtab["table"][$attach_gtabid];
if($import_attach_savesetting AND $ifield AND $tabname){
	if(file_exists($settpath)){
		$settingsfile = file_get_contents($settpath);
		$attachsettings = unserialize($settingsfile);
	}
	$attachsettings[$tabname] = $ifield;
	file_put_contents($settpath,serialize($attachsettings));
}elseif($import_attach_loadsetting AND $tabname){
	if(file_exists($settpath)){
		$settingsfile = file_get_contents($settpath);
		$attachsettings = unserialize($settingsfile);
		if($attachsettings[$tabname]){
			$ifield = $attachsettings[$tabname];
		}
	}
}

show_attach_file($attach_gtabid,$header);

?>
<BR><BR>
</FORM>