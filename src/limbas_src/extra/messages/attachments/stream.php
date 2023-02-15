<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
