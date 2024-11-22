<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



# --- Feldinformationen -----
function show_fieldinfo($gtabid,$fieldid){
	global $lang;
	global $gfield;
	global $gtab;
	global $db;
    global $lmfieldtype;
	
	echo "<TABLE cellpadding=\"0\" cellspacing=\"0\" STYLE=\"border-collapse:collapse;width:300px;\">";
	echo "<TR><TD colspan=\"2\" valign=\"top\" align=\"right\" style=\"cursor:pointer;\"><i class=\"lmb-icon lmb-close\" border=\"0\" onclick=\"document.getElementById('fieldinfo').style.visibility='hidden';\"></i></TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\" STYLE=\"width:100px;\"><B>ID</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$fieldid."</TD></TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[922]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['field_name'][$fieldid]."</TD></TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[923]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['beschreibung'][$fieldid]."</TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[924]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['spelling'][$fieldid]."</TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[925]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">"."<I>".$lmfieldtype["name"][$gfield[$gtabid]['data_type'][$fieldid]]."</I><BR> ".$lmfieldtype["description"][$gfield[$gtabid]['data_type'][$fieldid]]."</TD></TR>";
	if($gfield[$gtabid]['deflt'][$fieldid]){
		echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[928]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['deflt'][$fieldid]."</TD></TR>";
	}
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[210]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['size'][$fieldid]."</TD></TR>";
	echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1881]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><INPUT TYPE=\"TEXT\" STYLE=\"width:100%;background-color:transparent;border:none;\" VALUE=\"".htmlentities($gfield[$gtabid]["regel"][$fieldid],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."\"></TD></TR>";



	# --- Argument ---------------------------
	if($gfield[$gtabid]['argument'][$fieldid]){
		if($gfield[$gtabid]['artleiste'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1879]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
		if($gfield[$gtabid]['argument'][$fieldid]){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" VALIGN=\"TOP\"><B>".$lang[1375]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".htmlentities($gfield[$gtabid]["argument"][$fieldid],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</TD></TR>";}
		if($gfield[$gtabid]['argument_edit'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1879]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	}
	
	# --- Zahlenformat ----------------------
	if($gfield[$gtabid]['field_type'][$fieldid] == 5){
		if($gfield[$gtabid]['nformat'][$fieldid]){
			echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" VALIGN=\"TOP\"><B>".$lang[1880]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".implode("|",$gfield[$gtabid]['nformat'][$fieldid])."</TD></TR>";
		}
		# --- Währung -------
		if($gfield[$gtabid]['currency'][$fieldid]){
			echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" VALIGN=\"TOP\"><B>".$lang[1883]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".$gfield[$gtabid]['currency'][$fieldid]."</TD></TR>";
		}		
	}
	
	# --- Select Trennung ---
	if($gfield[$gtabid]['select_cut'][$fieldid]){
		echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1886]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\">".htmlentities($gfield[$gtabid]["select_cut"][$fieldid],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</TD></TR>";
	}

	# --- Verknüpfungen --- 
	# --- Anzahl ---
	if($gfield[$gtabid]['field_type'][$fieldid] == 11 AND $gfield[$gtabid]['md5tab'][$fieldid]){
		$sqlquery = "SELECT COUNT(*) RES FROM ".$gfield[$gtabid]['md5tab'][$fieldid];
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		echo "<TR><TD nowrap STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1887]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><INPUT TYPE=\"TEXT\" STYLE=\"width:100%;background-color:transparent;border:none;\" VALUE=\"".lmbdb_result($rs,"RES")."\"></TD></TR>";
	}
	
	# --- Datun incl. Zeit --- 
	if($gfield[$gtabid]['datetime'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1723]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}

	if($gfield[$gtabid]['fieldkey_id'] == $fieldid){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[926]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['indexed'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1720]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['indize'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1581]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['fieldkey_id'] == $fieldid){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[926]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['wysiwyg'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[1885]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['unique'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[927]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['dynsearch'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\" valign=\"top\"><B>".$lang[931]."</TD><TD STYLE=\"border:1px solid grey;padding:1px;overflow:hidden;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['artleiste'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;\" valign=\"top\"><B>".$lang[932]."</TD><TD STYLE=\"border:1px solid grey;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}
	if($gfield[$gtabid]['groupable'][$fieldid] == 1){echo "<TR><TD STYLE=\"border:1px solid grey;\" valign=\"top\"><B>".$lang[1459]."</TD><TD STYLE=\"border:1px solid grey;\"><i class=\"lmb-icon lmb-check-alt\"></i></TD></TR>";}


	
	echo "</TABLE>";
}

# --- Verknüpfungsinformationen -----
function show_linkinfo($gtabid,$fieldid,$vgtabid){
	global $lang;
	global $gfield;
	global $gtab;
	global $farbschema;

	if($gfield[$gtabid]['data_type'][$fieldid] == 25){
	    echo "<TABLE cellpadding=\"0\" cellspacing=\"0\" STYLE=\"border-collapse:collapse;width:300px;\">";
        echo "<TR><TD valign=\"top\" align=\"right\" style=\"cursor:pointer;\"><i class=\"lmb-icon lmb-close\" border=\"0\" onclick=\"document.getElementById('fieldinfo').style.visibility='hidden';\"></i></TD></TR>";
        echo "<TR><TD ALIGN=\"CENTER\"><TABLE cellpadding=\"0\" cellspacing=\"0\" STYLE=\"border-collapse:collapse;width:150px;border:1px solid black;\">";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB3'] . "\"><TD>" . $gfield[$gtabid]['md5tab'][$fieldid] . "</TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>&nbsp;</TD></TR></TABLE>";
	    return;
    }else {
        echo "<IMG class=\"lmb-image-as-icon\" SRC=\"assets/images/legacy/linkpfeil.gif\" STYLE=\"position:absolute;top:60px;left:5px;\">";
        echo "<IMG class=\"lmb-image-as-icon\" SRC=\"assets/images/legacy/linkpfeil.gif\" STYLE=\"position:absolute;top:93px;left:233px;\">";
        echo "<TABLE cellpadding=\"0\" cellspacing=\"0\" STYLE=\"border-collapse:collapse;width:300px;\">";
        echo "<TR><TD valign=\"top\" align=\"right\" style=\"cursor:pointer;\"><i class=\"lmb-icon lmb-close\" border=\"0\" onclick=\"document.getElementById('fieldinfo').style.visibility='hidden';\"></i></TD></TR>";
        echo "<TR><TD ALIGN=\"CENTER\"><TABLE cellpadding=\"0\" cellspacing=\"0\" STYLE=\"border-collapse:collapse;width:150px;border:1px solid black;\">";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB3'] . "\"><TD>" . $gfield[$gtabid]['md5tab'][$fieldid] . "</TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB4'] . "\"><TD STYLE=\"border-collapse:collapse;><DIV STYLE=\"height:1px;width:100%;\"></DIV></TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD TITLE=\"(primery key)\">KEY</TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD  TITLE=\"(create-user)\">ERSTDATUM</TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD  TITLE=\"(create-date)\">ERSTUSER</TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD>ID <I STYLE=\"color:green\">(ID " . $gtab['table'][$gtabid] . ")</I></TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD>VERKN_ID <I STYLE=\"color:green\">(ID " . $gtab['table'][$vgtabid] . ")</I></TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD  TITLE=\"(description)\">BEMERKUNG</TD></TR>";
        echo "<TR STYLE=\"background-color:" . $farbschema['WEB8'] . "\"><TD  TITLE=\"(activ link)\">AKTIV</TD></TR>";
        echo "</TABLE></TD></TR>";
        echo "<TR><TD>&nbsp;</TD></TR></TABLE>";
    }
}
?>
