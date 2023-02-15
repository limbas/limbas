<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
    $path = TEMPPATH . 'lock.txt';
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

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Maintenance</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta charset="utf-8">

    <style>
        body, html {
            width: 100%;
            height: 100%;
            font-family: sans-serif;
            overflow: hidden;
        }
        .flex-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .text-center {
            text-align: center;
        }
        
        .fs-3 {
            font-size: 3rem;
        }
    </style>
</head>
<body>
<div class="flex-container">
    <div class="text-center">
        <p class="fs-3">🚧</p>
        Dear <?= $session['vorname'] ?> <?= $session['name'] ?>,
        <br><br><?= lmbGetLockmessage() ?></div>
</div>
</body>
</html>
