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
 * ID:
 */
define('MAILER_GUI_STYLE_POPUP',  0);
define('MAILER_GUI_STYLE_PANEL',  1);
define('MAILER_GUI_STYLE_WINDOW', 2);

define('MAILER_GUI_BEHAVIOUR_WEB', 0);
define('MAILER_GUI_BEHAVIOUR_MS', 1);

define('MAILER_NAME', 'LIMBAS eMail');
define('MAILER_VERSION', '$Id: config.php 1174 2015-12-04 17:04:22Z axel $');

define('MAILER_GUI_STYLE_DEFAULT', MAILER_GUI_STYLE_WINDOW); /* popup, panel or window */
define('MAILER_GUI_BEHAVIOUR_DEFAULT', MAILER_GUI_BEHAVIOUR_MS); /* web or ms style GUI behaviour*/

define('MAILER_DEBUG', true); /* mandatory. TODO: fix. */

global $session, $gtabid, $imap_cfg; // something broken, isnt it?
global $project_base, $project_root;

/* set base url and path of the Messages.Subsystem within Limbas */
$project_base = "main_dyns.php?actid=messages&gtabid={$gtabid}";	// used for requests
$project_root = 'extra/messages/';	// used for images and templates

/* try to get configuration from user settings */
if (isset($gtabid))
	$imap_cfg = ($session["e_setting"][$gtabid]) ? $session["e_setting"][$gtabid] : null;

/* username, password and mailaddress are mandatory information.
 * invalidate the configuration, if one of these parameters is missing. */
if ((!isset($imap_cfg['imap_username'])) || (trim($imap_cfg['imap_username']) == ''))
	$imap_cfg = null;
if ((!isset($imap_cfg['imap_password'])) || (trim($imap_cfg['imap_password']) == ''))
	$imap_cfg = null;
if ((!isset($imap_cfg['email_address'])) || (trim($imap_cfg['email_address']) == ''))
	$imap_cfg = null;

/* set some default values if we have a valid configuration */
if ($imap_cfg){
	if (!isset($imap_cfg['gui_style']))
		$imap_cfg["gui_style"] = MAILER_GUI_STYLE_DEFAULT;

	if (!isset($imap_cfg['gui_behaviour']))
		$imap_cfg["gui_behaviour"] = MAILER_GUI_BEHAVIOUR_DEFAULT;

	/* temporary files and attachments are kept in the current users temp folder. */
	$imap_cfg["temp_path"] = dirname($_SERVER['SCRIPT_FILENAME']).
				"/USER/".intval($session['user_id'])."/temp/";

	$imap_cfg["arch_mbox"] = ($umgvar["mailarch_hostname"] != '') ? $umgvar["mailarch_hostname"] : 'INBOX.LIMBAS Archiv';
        $imap_cfg["arch_hostname"] = $umgvar["mailarch_hostname"];
        $imap_cfg["arch_port"] = $umgvar["mailarch_port"];
        $imap_cfg["arch_username"] = $umgvar["mailarch_user"];
        $imap_cfg["arch_password"] = $umgvar["mailarch_pass"];
        
	
	/* the "Organisation:" - header will only be included in outgoing mail,
	 * when umgvar["company"] is set. */
	$imap_cfg["organisation"] = ($umgvar["company"]) ? $umgvar["company"] : null;
}
?>
