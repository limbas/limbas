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

if($extsave AND $fvalue AND $fpath){
	if(strpos($fpath,"EXTENSIONS/") != 1){echo "only permission to edit files in EXETNSIONS directory!";return;}
	if(file_exists($umgvar["pfad"].$fpath)){
		$fvalue = utf8_encode(str_replace(chr(13),'',$fvalue));
		file_put_contents($umgvar["pfad"].$fpath,$fvalue);
	}
}


?>

<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta HTTP-EQUIV="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <style type="text/css">@import url(USER/<?=$session["user_id"]?>/layout.css);</style>
	<script src="extern/codemirror/lib/codemirror.js"></script>
	<link rel="stylesheet" href="extern/codemirror/lib/codemirror.css">
	<link rel="stylesheet" href="extern/codemirror/doc/docs.css">
	<script src="extern/codemirror/mode/htmlmixed/htmlmixed.js"></script>
	<script src="extern/codemirror/mode/xml/xml.js"></script>
	<script src="extern/codemirror/mode/javascript/javascript.js"></script>
	<script src="extern/codemirror/mode/css/css.js"></script>
	<script src="extern/codemirror/mode/clike/clike.js"></script>
	<script src="extern/codemirror/mode/php/php.js"></script>
  </head>
<body>


<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_exteditor">
<input type="hidden" name="fpath" value="<?=$fpath?>">

<h2><?=$fpath?></h2>

<textarea name="fvalue" id="extvalue" style="width:100%">
<?php
	if(strpos($fpath,"EXTENSIONS/" != 1)){echo "only permission to edit files in EXETNSIONS directory!";return;}
	
	if(file_exists($umgvar["pfad"].$fpath)){
		$value = file_get_contents($umgvar["pfad"].$fpath);

		echo htmlentities($value,ENT_QUOTES | ENT_SUBSTITUTE,'UTF-8');
	}
?>
</textarea>


<input type="text" value="save" name="extsave" OnClick="document.form1.submit()" style="cursor:pointer;text-align:center">&nbsp;
<input type="text" value="close" style="cursor:pointer;text-align:center" OnClick="window.close()">

</FORM>

<Script language="JavaScript">
	
      var editor = CodeMirror.fromTextArea(document.getElementById("extvalue"), {
        lineNumbers: true,
        matchBrackets: true,
        mode: "application/x-httpd-php",
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        setSize: "100%, 100%"
      })

</Script>


</body>
</html>