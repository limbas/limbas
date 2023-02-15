<?php
/*
 * LIMBAS Copyright notice
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
