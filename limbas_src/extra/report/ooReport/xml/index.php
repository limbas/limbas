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
define('PATHSEP', '/');

require_once("../../olMail/classes/php/olTemplate.class.php");
require_once("../olODFContainer.class.php");
require_once("../olODFPicture.class.php");
require_once("../olODFTemplate.class.php");
require_once("olXMLParser.class.php");

function sanitize_filename($fname){
	$ret = preg_replace('/\\*/', '', $fname);
	return preg_replace('/\.+/', '.', $ret);
}

if (isset($_GET['p'])){
	$xml_filename = sanitize_filename($_GET['p']);
	
	$container = new olODFContainer();
	$container->tempdir = "../{$container->tempdir}";
	$container->docdir = "../{$container->docdir}";

	$template = new olODFTemplate($container);

	$parser = new olXMLParser($xml_filename, $template);
	$parser->parse();

	/* update content, compress and download document. */
	if ($template->save()){
		if (!$container->download())
			echo "ERROR: failed downloading {$container->name}";
	} else
		echo "ERROR: failed updating content! (no write permissions for {$container->tempdir} folder?)";

	exit(0);
}

if (isset($_GET['v'])){
	$fname = sanitize_filename($_GET['v']);
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: text/html; charset=UTF-8');
	echo <<<EOD
<html>
<head><title>$fname</title></head>
<body>
<h1>XML Validator (xmllint)</h1>
<hr>
<pre><xmp style="font-weight:bold;font-size:9pt;">
EOD;
	$ret = system("./checkxml.sh $fname preliminary.dtd");
	echo <<<EOD
</xmp></pre>
<hr><a href="?p={$fname}">Parse {$fname} utilizing olXMLParser, olODFContainer olODFPicture and olODFTemplate php classes.</a>
</body>
</html>

EOD;

	exit(0);
}

if (isset($_POST['dtd'])){
	$fname = sanitize_filename($_POST['fname']);
	
	if (get_magic_quotes_gpc())
		$data = stripslashes($_POST['dtd']);
	else
		$data = $_POST['dtd'];
		
	if (!file_put_contents($fname, $data)){
		echo "ERROR: Could not write to file $fname.";
		exit(1);
	}
	
}

if (isset($_GET['f']) || isset($_POST['fname'])){
	$fname = sanitize_filename(isset($_GET['f']) ? $_GET['f'] :$_POST['fname']);
	header('Expires: 0');
	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: text/html; charset=UTF-8');
	header("Content-disposition: inline; filename=$fname");
	echo <<<EOD
<html>
<body style="margin:0px;overflow:hidden;">
<form method="POST" style="margin:0px;padding:0px;">
<input type="hidden" name="fname" value="$fname">
<textarea wrap="off" name="dtd" style="width:100%;height:90%;border:none;font-size:9pt;padding:4px;margin:0px;">
EOD;
	echo file_get_contents($fname);
	echo <<<EOD
</textarea>
<input type="submit" style="text-decoration:underline;cursor:pointer;width:100%;height:10%;border:none;" value="$fname speichern &amp; validieren">
</form>
</body>
</html>
EOD;
	exit(0);
}
?>
<html>
<head>
 <title>XML Validator (xmllint)</title>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <script src="../../olMail/classes/js/olEvent.js" type="text/javascript"></script>
 <script src="../../olMail/classes/js/olAjax.js" type="text/javascript"></script>
 <script type="text/javascript">//<!--
  function $(id) { return document.getElementById(id) }
  
  olEvent().observe(window, 'load', function(e){
	olEvent().observe($('dtd'), 'load', function(e){ parent.x.location.reload(); });
	olEvent().observe($('xml'), 'load', function(e){ parent.x.location.reload(); });
  });
  
 //--></script>
</head>
<body style="margin:0px;">
<div style="float:left;width:50%;height:50%;background-color:#ffeeee;">
	<iframe name="dtd" id="dtd"
		src="?f=preliminary.dtd"
		width="100%" height="100%"
		frameborder="0">
	</iframe>
</div>
<div style="float:left;width:50%;height:50%;background-color:#ffeeee;">
	<iframe name="xml" id="xml"
		src="?f=preliminary.xml"
		width="100%" height="100%"
		frameborder="0">
	</iframe>
</div>
<div style="clear:left;height:50%;overflow:hidden;">
	<div style="border-top:solid 1px black;">
		<iframe name="x" id="x"
			src="?v=preliminary.xml"
			width="100%" height="100%"
			frameborder="0" style="float:left;">
		</iframe>
	</div>
</div>
</body>
</html>
