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
require_once('../config.php');
require_once('../classes/php/olIMAPdirect.class.php');
global $gtabid;
//ob_end_clean();

function __default($arr, $name, $value){
	return isset($arr[$name]) ? $arr[$name] : $value;
}

$mbox = (string) __default($_GET, "mbox", NULL);
$uid =  (string) __default($_GET, "uid",  NULL);
$part = (string) __default($_GET, "part", NULL);

if (!$mbox || !$uid || !$part){
	header("HTTP/1.1 500 Server Error");
	echo "Error: missing parameter.";
	exit(0);
}

$imap_cfg = lmb_getImapCfg($gtabid);
$IMAP = new olIMAPdirect($imap_cfg["imap_username"], $imap_cfg["imap_password"],
	$imap_cfg["imap_hostname"], isset($imap_cfg["imap_port"])
		? $imap_cfg["imap_port"] : 143);
		
if (!$IMAP->connect()){
	header("HTTP/1.1 500 Server Error");
	echo "Error: ".$IMAP->last_error();
	exit(0);
}

if ($IMAP->fetch_part($mbox, $uid, $part)==NULL){
	header("HTTP/1.1 500 Server Error");
	echo "Error: ".$IMAP->last_error();
}

$IMAP->disconnect();

?>
