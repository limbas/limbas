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

if ($extsave AND $fvalue AND $fpath) {
    if (lmb_strpos($fpath, 'EXTENSIONS/') != 1) {
        echo "only permission to edit files in EXTENSIONS directory!";
        return;
    }
    if (file_exists($umgvar['pfad'] . $fpath)) {
        $fvalue = lmb_utf8_encode(str_replace(chr(13), '', $fvalue));
        file_put_contents($umgvar['pfad'] . $fpath, $fvalue);
    }
}

?>

<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta HTTP-EQUIV="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <style type="text/css">@import url(USER/<?=$session['user_id']?>/layout.css);</style>
    <script src="extern/codemirror/lib/codemirror.js"></script>
    <link rel="stylesheet" href="extern/codemirror/lib/codemirror.css">
    <script src="extern/codemirror/edit/matchbrackets.js"></script>
    <script src="extern/codemirror/edit/matchtags.js"></script>
    <script src="extern/codemirror/mode/htmlmixed/htmlmixed.js"></script>
    <script src="extern/codemirror/mode/xml/xml.js"></script>
    <script src="extern/codemirror/mode/javascript/javascript.js"></script>
    <script src="extern/codemirror/mode/css/css.js"></script>
    <script src="extern/codemirror/mode/clike/clike.js"></script>
    <script src="extern/codemirror/mode/php/php.js"></script>
    <style>
        .CodeMirror {
            border: 1px solid<?=$farbschema['WEB3']?>;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 10px;
        }

        h2 + h3 {
            margin-top: 0;
        }

        input {
            width: 100px;
            height: 20px;
        }
        input + input {
            margin-left: 10px;
        }

        html, body, form {
            height: 100%;
        }
    </style>
</head>
<body>


<form action="main_admin.php" method="post" name="form1" style="display: flex;flex-direction:column;">
    <input type="hidden" name="action" value="setup_exteditor">
    <input type="hidden" name="fpath" value="<?= $fpath ?>">

    <h2><?= $fpath ?></h2>

    <?php if (!is_writable($umgvar['path'] . $fpath)): ?>
        <h3 style="color: red">No permission to write to this file! Changes wil not be saved!</h3>
    <?php endif; ?>

    <div style="flex-grow: 1; position: relative">
        <?php
        $value = '';
        if (file_exists($umgvar['path'] . $fpath)) {
            $value = file_get_contents($umgvar['path'] . $fpath);
        }
        ?>
        <textarea name="fvalue" id="extvalue" style="width:100%"><?= htmlentities($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></textarea>
    </div>
    <div style="margin-bottom: 10px;margin-top: 10px;">
        <input type="submit" value="save" name="extsave">
        <input type="button" value="close" OnClick="window.close()">
    </div>
</form>

<Script language="JavaScript">

    CodeMirror.fromTextArea(document.getElementById("extvalue"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "indent",
        tabMode: "shift"
    });

</Script>

</body>
</html>