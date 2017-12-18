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
$this->add("title", "OpenOffice.org - Vorlagen mit Limbas");

$code = new olTemplate("code.tpl", false, ".");
$this->add("code", $code->parse());
?>
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <title>${title}</title>
 <link href="main.css" rel="stylesheet" type="text/css">
 <script src="../olMail/classes/js/olEvent.js" type="text/javascript"></script>
 <script type="text/javascript">//<!--
  function $(id) { return document.getElementById(id) }
  olEvent().observe(window, 'load', function(e){
   // update thumbnail circuumventing any local browser caching...
   $('thumb').src = 'temp/doc/Thumbnails/thumbnail.png?_=' +
    parseInt(Math.random() * 99999);
  });
 //--></script>
</head>
<body>
<h1>${title}</h1>
<h2>Vorlage (ODT)</h2>
<div id="upload"> 
	<form method="POST" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="1048576"><!-- 1MB -->
		<input type="file" name="template">
		<input type="submit" value="Vorlage hochladen">
	</form>
	<p>Aktuelle Vorlage: <a title="Dokumentvorlage öffnen"
	 href="${template_url}">${template_name}</a> vom ${template_date}
</div>

<!-- ---------------------------------------------------------------------- -->
<a title="Dokumentvorlage öffnen" href="${template_url}"><img width="96"
  id="thumb" border="0"></a>

<!-- ---------------------------------------------------------------------- -->
<div id="variables">

<h2>Daten (XML)</h2>
 <a href="xml" target="_blank">DTD &amp; XML - Validierung</a> 
 (<a href="xml/preliminary.xml" target="_blank">preliminary.xml</a>)<br>
 <hr>

<h2>Verarbeitung</h2>
<strong><a href="?parse=preliminary.xml">Dokument erstellen</a></strong>
<hr>
<h2>Code</h2>
<div class="code">${code}</div>
</div>
<!-- ---------------------------------------------------------------------- -->
<hr style="clear:both;">
<small>$Id: main.tpl 29 2010-02-17 01:28:36Z daniel $</small>
</body>
</html>
