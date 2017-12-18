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
 * ID: 101
 */



/* --- Kopf --------------------------------------- */
echo "<CENTER><B><U>$lang[517]</U></B><BR><BR><BR></CENTER>";

?>

<CENTER>
<TABLE BORDER="0" cellspacing="0" cellpadding="2">

<FORM NAME="form0">
<?
if($result_argument["argument_typ"] == 15){
	echo "<TR><TD><B>$lang[518]</B></TD>";
	echo "<TD><SELECT NAME=\"umgvariablen\" OnChange=\"document.form1.argument.value=document.form1.argument.value + document.form0.umgvariablen.value;\"><OPTION>";
	echo "<OPTION VALUE=\"\$session[username]\">$lang[519]";
	echo "<OPTION VALUE=\"\$session[vorname] \$session[name]\">$lang[520]";
	echo "<OPTION VALUE=\"\$session[email]\">$lang[521]";
	echo "</SELECT></TD></TR>";


	echo "<TR><TD><B>$lang[1462]</B></TD>";
	echo "<TD><SELECT OnChange=\"document.form1.argument.value=document.form1.argument.value + this.value;\"><OPTION>";
	if($gfield[$tab_id]["field_id"]){
	foreach($gfield[$tab_id]["field_id"] as $key => $value){
		echo "<OPTION VALUE=\"#*".$gfield[$tab_id][field_id][$key]."#\">"."(".$gfield[$tab_id][field_id][$key].") ".$gfield[$tab_id][field_name][$key];
	}}
	echo "</SELECT>";
	echo "</TR></TD>";
}
?>

</FORM>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="setup_argument">
<input type="hidden" name="atid" value="<?echo $atid;?>">
<input type="hidden" name="tab_id" value="<?echo $tab_id;?>">
<input type="hidden" name="tab_group" value="<?echo $tab_group;?>">
<input type="hidden" name="fieldid" value="<?echo $fieldid;?>">
<input type="hidden" name="typ" value="<?echo $typ;?>">

<?
/* --- Ergebnisliste --------------------------------------- */
if($gfield[$tab_id]["argument_typ"][$fieldid] == 15 OR $argument_typ == 15){echo "<TR><TD COLSPAN=\"2\">eg ( return \"hello world #*1#\"; )</TD></TR>";}
else{echo "<TR><TD COLSPAN=\"2\">eg ( CUSTOMER.FIRSTNAME ".LMB_DBFUNC_CONCAT." CUSTOMER.LASTNAME )</TD></TR>";}
echo "<TR><TD COLSPAN=\"2\"><TEXTAREA NAME=\"argument\" ROWS=\"10\" COLS=\"50\">".str_replace("\"","&quot;",$result_argument[argument])."</TEXTAREA></TD></TR>";
echo "<TR><TD COLSPAN=\"2\" ALIGN=\"CENTER\"><INPUT TYPE=\"submit\" VALUE=\"$lang[522]\" name=\"argument_change\">&nbsp;&nbsp;";
if($gfield[$tab_id]["argument_typ"][$fieldid] == 15){echo "<INPUT TYPE=\"submit\" VALUE=\"$lang[1304]\" name=\"argument_refresh\"></TD></TR>";}

?>

</TABLE>
</CENTER>
</FORM>