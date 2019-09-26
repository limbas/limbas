<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH (support@limbas.org)
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
 * ID: 1
 */
require_once("inc/include_db.lib");
require_once("lib/include.lib");
require_once("lib/session.lib");

# redirect group 
if($groupdat["redirect"][$session["group_id"]]){
	if(lmb_strtolower(lmb_substr($groupdat["redirect"][$session["group_id"]],0,4)) == "http"){
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$groupdat["redirect"][$session["group_id"]]);
		return;
	}else{
	    $_REQUEST['action'] = "redirect";
	    $src = $groupdat["redirect"][$session["group_id"]];
		//header("HTTP/1.1 301 Moved Permanently");
		//header("Location: index.php?action=redirect&src=".urlencode($groupdat["redirect"][$session["group_id"]]));
	}
}

?>
<HTML>
<HEAD>
<META NAME="Title" CONTENT="Limbas Enterprise Unifying Framework V <?= $umgvar["version"] ?>">
<meta NAME="author" content="LIMBAS GmbH">
<META NAME="Publisher" CONTENT="LIMBAS GmbH">
<META NAME="Copyright" CONTENT="LIMBAS GmbH">
<meta NAME="description" content="Enterprise Unifying Framework">
<meta NAME="version" content="<?= $umgvar["version"] ?>">
<meta NAME="date" content="2017-10-23">
<meta HTTP-EQUIV="content-language" content="de">
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=<?= $umgvar["charset"] ?>">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta HTTP-EQUIV="Pragma" content="no-cache">
<meta HTTP-EQUIV="Cache-Control" content="no-cache, no-store, post-check=0, pre-check=0, must-revalidate">
<meta HTTP-EQUIV="Expires" content="0">
<link rel="shortcut icon" href="favicon.ico" />
</HEAD>

<?php /* --- Frameset ---------------------------------------------------------- */

if($session["layout"]){
	require("lib/session.lib");
	$require = "layout/frameset.php";
	if(file_exists("layout/".$session["layout"]."/frameset.php")){
		$require = "layout/".$session["layout"]."/frameset.php";	
	}
	require($require);	
}

?>

</HTML>

<?php
/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {odbc_close($db);}
?>