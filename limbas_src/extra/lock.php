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
 * ID: 185
 */
?>

<BODY BGCOLOR="FFFFFF">
<IMG SRC="pic/wartungsarbeiten.gif" BORDER="0" style="position:absolute;left:25%;top:25%">
<DIV style="position:absolute;left:25%;top:25%">

<?
function get_lockmessage($path){
	# --- get lock.txt ----------------------------
	if(file_exists($path)){
		$content = file($path);
		return implode("",$content);
	}
}


$txt = trim(get_lockmessage("TEMP/lock.txt"));
if(!$txt){
	$txt = "This LIMBAS application is down for maintenance work!<br>please come back again soon.";
}

$sqlquery = "SELECT LOCK_TXT,NAME,VORNAME FROM LMB_USERDB WHERE USERNAME = '".str_replace("'","''",$_SERVER['PHP_AUTH_USER'])."' AND PASSWORT = '".md5($_SERVER['PHP_AUTH_PW'])."'";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$res = trim(odbc_result($rs,"LOCK_TXT"));
if($res){$txt = $res;}
$name = odbc_result($rs,"VORNAME")." ".odbc_result($rs,"NAME");

if($name){
	echo "<div style=\"color:#777777;font-size:18px;position:relative;left:220px;top:100px;width:300px;\">Dear ".$name."</div>";
}

echo "<div style=\"color:#777777;font-size:18px;position:relative;left:120px;top:220px;width:450px;\">".$txt."</div>";

?>
</DIV>

</BODY>



