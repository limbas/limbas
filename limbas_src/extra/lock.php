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
 * ID: 185
 */

function lmbGetLockmessage() {
    global $session;
    global $action;
    global $db;

    # user message
    $sqlquery = "SELECT LOCK_TXT FROM LMB_USERDB WHERE USERNAME='{$session['username']}'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if ($message = trim(lmbdb_result($rs, 'LOCK_TXT'))) {
        return $message;
    }

    # global message from lock.txt
    $path = 'TEMP/lock.txt';
    if(file_exists($path)){
        if ($content = trim(file_get_contents($path))) {
            return $content;
        }
    }

    # default message
    return 'This LIMBAS application is down for maintenance work!<br>please come back again soon.';
}

global $session;
?>

<body style="background-color: white;">
    <img src="pic/wartungsarbeiten.gif" style="border: none; position: absolute; left: 25%; top: 25%">
    <div style="position: absolute; left: 25%; top: 25%; color:#777777; font-size:18px;">
        <div style="position:relative; left:220px; top:100px; width:300px;">Dear <?= $session['vorname'] ?> <?= $session['name'] ?></div>
        <div style="position:relative; left:120px; top:220px; width:450px;"><?= lmbGetLockmessage() ?></div>
    </div>
</body>

