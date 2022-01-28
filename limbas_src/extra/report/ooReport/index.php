<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */

define('PATHSEP', '/'); // forward slash for UN*X, backslash for Windoze server.

require_once("../../messages/classes/php/olTemplate.class.php");
require_once("olODFContainer.class.php");
require_once("olODFPicture.class.php");
require_once("olODFTemplate.class.php");
require_once("xml/olXMLParser.class.php");

/* remove potentially unwanted characters from filenames */
function sanitize_filename($fname){
	$ret = preg_replace('/\\*/', '', $fname);
	return preg_replace('/\.+/', '.', $ret);
}

/* at instantiation the files of the first ODT file found in tempdir are 
 * unpacked into docdir.
 */
$container = new olODFContainer();

/* --- handle template upload requests -------------------------------------- */
if (isset($_FILES) && isset($_FILES['template']))
	if (($ret = $container->upload($_FILES['template'])) != NULL){
		if (is_string($ret))
			echo "ERROR: {$ret}<br>";
		else
			var_dump($_FILES['template']);
	}

/* --- generate and output target document ---------------------------------- */
if (isset($_GET['parse'])){
	$xml_filename = 'xml' . PATHSEP . sanitize_filename($_GET['parse']);

	$template = new olODFTemplate($container);
	$parser   = new olXMLParser($xml_filename, $template);
	$parser->parse();
	
	/* update content, compress and download document. */
	if ($template->save()){
		if (!$container->download())
			echo "ERROR: failed downloading {$container->name}";
	} else
		echo "ERROR: failed updating content! (no write permissions for {$container->tempdir} folder?)";

	exit(0);
}

/* --- output webpage using ./main.tpl without caching ---------------------- */
header('Expires: 0');
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: text/html; charset=UTF-8');

$_fname = $container->getDocumentName(true);
$_ftime = filemtime($_fname);

$webpage = new olTemplate("main.tpl", false, ".");
$webpage->add('template_name', $container->getDocumentName());
$webpage->add('template_url', $_fname);
$webpage->add('template_date',	date('d.m.Y H:i:s',	$_ftime));
$webpage->output();

?>
