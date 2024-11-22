<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



if ($extsave AND $fvalue AND $fpath) {
    if (lmb_strpos($fpath, 'EXTENSIONS/') != 1) {
        echo "only permission to edit files in EXTENSIONS directory!";
        return;
    }
    if (file_exists($umgvar['pfad'] . $fpath)) {
        $fvalue = str_replace(chr(13), '', $fvalue);
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
    <link rel="stylesheet" type="text/css" href="<?=$session['legacyCss']?>?v=<?=$umgvar["version"]?>">
    <script src="assets/vendor/codemirror/lib/codemirror.js?v=<?=$umgvar["version"]?>"></script>
    <link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css?v=<?=$umgvar["version"]?>">
    <script src="assets/vendor/codemirror/addon/edit/matchbrackets.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/addon/edit/matchtags.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/htmlmixed/htmlmixed.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/xml/xml.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/javascript/javascript.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/css/css.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/clike/clike.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/codemirror/mode/php/php.js?v=<?=$umgvar["version"]?>"></script>
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
