<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */

/* assert that $gtabid is allready set. */
if (!isset($gtabid)){
	header("HTTP/1.1 500 Server Error");
	echo '<p>Fehler: Ung�ltiger Aufruf.';
	return;
}

require_once('classes/php/olMail.class.php');

/* fail if we dont have php-imap support. */
if (!function_exists("imap_open")){
	header("HTTP/1.1 500 Server Error");
	echo "<p>Fehler: IMAP-Unterst�tzung f�r PHP fehlt!";
	return;
}

/* fail if configuration is missing. */
require_once('config.php');
$imap_cfg = lmb_getImapCfg($gtabid);
if (!$imap_cfg){
	header("HTTP/1.1 500 Server Error");
	echo '<p>Keine g�ltige <a href="main.php?action=user_change" target="main">'.
		'Nachrichten-Konfiguration</a> gefunden!';
	return;
}

/* connect to mailserver and exit if connection failed. */
$mail = new olMail($imap_cfg);
	
if (!$mail->is_connected()){
	header("HTTP/1.1 500 Server Error");
	echo "<p>Fehler: ".imap_last_error();
	return;
}

/* output message count for INBOX */
$INBOX = $mail->preview_inbox();

/*
echo "<b>Posteingang</b>: {$INBOX['all']}&nbsp;Nachricht".
	(($INBOX['all']==1) ? '' : 'en').(($INBOX['new']>0)
		? "&nbsp;(<i><b>{$INBOX['new']}</b>&nbsp;neue<i>)"
		: "");
*/

echo "&nbsp;{$INBOX['new']} neue Nachricht".(($INBOX['new']==1) ? '' : 'en');
		
/* disconnect from mailserver */
$mail->disconnect();

/* display marked message count, if any */
$mail_marked = $_COOKIE['mail_marked'];
if ($mail_marked){
	$cnt = 0;
	$_tmp = explode(':', $mail_marked);
	$mbox = $_tmp[0];
	$uids = explode(',', $_tmp[1]);
	foreach($uids as $uid)
		if ($uid!='')
			$cnt++;
	echo "<p><i>({$cnt} vorgemerkte Nachricht".(($cnt==1) ? '' : 'en').")</i>";
}

?>
