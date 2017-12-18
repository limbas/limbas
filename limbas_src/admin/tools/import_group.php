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
 * ID: 151
 */


$pfad = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/";
$sys = system("rm ".$pfad."*");
$copy = copy ($fileatm, $pfad.$fileatm_name);
$sys = system("tar -x -z -C ".$pfad." -f ".$pfad."/".$fileatm_name);
$sys = system("rm ".$pfad.$fileatm_name);
/* --- Liste zu importierender Felder --------------------------------------------- */

if($path = read_dir($pfad)){
foreach($path["name"] as $key => $value){
	$value_ = explode(".",$value);
	if($path["typ"][$key] == "file"){
		$imptabgroup[] = lmb_strtoupper(preg_replace("/^[^0-9a-zA-z]$/","",$value_[0]));
	}
}
}

sort($imptabgroup);

/* ---------------------- Spaltenliste ---------------------- */?>
<FORM ACTION="main_admin.php" METHOD="post" name="form2">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_import">
<INPUT TYPE="hidden" NAME="partimport" VALUE="1">
<INPUT TYPE="hidden" NAME="import_typ" VALUE="atm">
<INPUT TYPE="hidden" NAME="hold_id" VALUE="1">
<INPUT TYPE="hidden" NAME="impopt" VALUE="<?=$impopt?>">
<INPUT TYPE="hidden" NAME="import_overwrite" VALUE="<?=$import_overwrite?>">
<INPUT TYPE="hidden" NAME="import_count" VALUE="group">


<BR><HR>
<?
echo "<TABLE>";
echo "<TR BGCOLOR=\"".$farbschema[WEB7]."\"><TD>$lang[1024]</TD><TD>$lang[1039]</TD><TD>$lang[1040]</TD>";
$bzm = 0;
while($imptabgroup[$bzm]){
        echo "<TR><TD>$imptabgroup[$bzm]</TD>";
        if(in_array($imptabgroup[$bzm],$existingfields)){echo "<TD ALIGN=\"CENTER\"><i class=\"lmb-icon lmb-aktiv\" BORDER=\"0\"></i></TD>";}
        else{echo "<TD></TD>";}
        echo "<TD ALIGN=\"CENTER\"><INPUT TYPE=\"CHECKBOX\" NAME=\"tablegrouplist[".$imptabgroup[$bzm]."]\" CHECKED></TD></TR>";
$bzm++;
}

echo "<TR><TD COLSPAN=\"3\">&nbsp;</TD></TR>";
echo "<TR><TD COLSPAN=\"3\" ALIGN=\"CENTER\"><INPUT TYPE=\"SUBMIT\" NAME=\"start_groupimport\" VALUE=\"$lang[1040]\"></TD></TR>";
echo "</TABLE>";
echo "</FORM>";
?>

