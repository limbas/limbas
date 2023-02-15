<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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

function lmb_getImapCfg($mailTabid) {
	global $session;
	global $umgvar;

    /* try to get configuration from user settings */
    $imap_cfg = $session['e_setting'][$mailTabid];
    if (!$imap_cfg)
    	return null;

    /* username, password and mailaddress are mandatory information.
     * invalidate the configuration, if one of these parameters is missing. */
    if ((!isset($imap_cfg['imap_username'])) || (trim($imap_cfg['imap_username']) == ''))
        return null;
    if ((!isset($imap_cfg['imap_password'])) || (trim($imap_cfg['imap_password']) == ''))
        return null;
    if ((!isset($imap_cfg['email_address'])) || (trim($imap_cfg['email_address']) == ''))
        return null;

    /* set some default values if we have a valid configuration */
    if (!$imap_cfg)
    	return null;

	if (!isset($imap_cfg['gui_style']))
		$imap_cfg['gui_style'] = MAILER_GUI_STYLE_DEFAULT;

	if (!isset($imap_cfg['gui_behaviour']))
		$imap_cfg['gui_behaviour'] = MAILER_GUI_BEHAVIOUR_DEFAULT;

	/* temporary files and attachments are kept in the current users temp folder. */
	$imap_cfg['temp_path'] = USERPATH . intval($session['user_id']) . '/temp/';
	$imap_cfg['arch_mbox'] = ($umgvar['mailarch_mbox'] != '') ? $umgvar['mailarch_mbox'] : 'LimbasArchive';
	$imap_cfg['arch_hostname'] = $umgvar['mailarch_hostname'];
	$imap_cfg['arch_port'] = $umgvar['mailarch_port'];
	$imap_cfg['arch_username'] = $umgvar['mailarch_user'];
	$imap_cfg['arch_password'] = $umgvar['mailarch_pass'];

	/* the "Organisation:" - header will only be included in outgoing mail,
	 * when umgvar["company"] is set. */
	$imap_cfg['organisation'] = ($umgvar['company']) ? $umgvar['company'] : null;

    return $imap_cfg;
}

?>
