<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


//	header("Content-type: text/html; charset=UTF-8");
require_once("../classes/php/olTemplate.class.php");
require_once("../classes/php/olUpload.class.php");

$up = new olUpload(dirname($_SERVER['SCRIPT_FILENAME'])."/files", "../templates");

if (isset($_POST['fname'])){
	echo "newname=".$up->beautify_filename($_POST['fname']);
	exit(0);
} else if (isset($_FILES['Filedata'])){
	if ($up->handle())
		echo "OK";
	exit(0);
} 

$html_head = $up->html_header_code();
$content = $up->html_code();

echo <<<EOD
<body>
<head>
{$html_head}
<link href="../styles/olImap.css" rel="stylesheet" type="text/css">
<link href="../styles/olPopup.css" rel="stylesheet" type="text/css">
<style type="text/css">
body{
	background-color:#c0c0c0;
	overflow:hidden;
}</style>
</head>
<body>
{$content}
<div id="DEBUG"></div>
</body>
</html>
EOD;
exit(0);
?>
