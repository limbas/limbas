<?php
foreach(array("_self", "mbox", "uid", "part") as $var)
	$$var = $this->search($var);

$this->add("url", "{$_self}&mbox={$mbox}&uid={$uid}&part={$part}"); // &stream=1
?>
<!-- [inline_image.tpl] -->
<img name="inline_image"
	style="width:100%;"
	src=""
	alt="${name}"
	title="Dateianhang (Bild): ${name}"
>
<script type="text/javascript" language="javascript">//<!--
	_inline_image.push("${url}");
//--></script>

<!-- [/inline_image] -->
 