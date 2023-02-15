<?php
if ($this->search("print"))
	$_additional_code = <<<EOD
<script language="javascript" type="text/javascript">//<!--
	window.print();
	window.close();
//--></script>
EOD;
$this->add("_additional_code", isset($_additional_code)
	? $_additional_code : "");
?>
<!-- [message.tpl] -->
<script language="javascript" type="text/javascript">//<!--
var _inline_audio = null, player = null;
var _inline_image = [];

/* entry point */
var onDocumentLoaded = function(e){
	var _use_browser_cache = false;

	olFramework().load(_basePATH_ + 'classes/js/', [
		'olEvent',
		'olInlineAudio',
	], _use_browser_cache, function(loaded_modules){ // onFrameworkLoaded
		if (_inline_audio != null)
			player = new olInlineAudio('olInlineAudio',
				'${_path}attachments/olInlineAudio.swf', '100%', 8, _inline_audio);
		if ((typeof _inline_image) != undefined){
			var _img = document.getElementsByName('inline_image');
			for (var i=0; i<_inline_image.length; i++)
				_img[i].src = _inline_image[i];
		}
	});
};
if (window.addEventListener)
	window.addEventListener('load', onDocumentLoaded, false);
else
	window.attachEvent('onload', onDocumentLoaded);
//--></script>
<div id="mail_desc">
	<table class="mail_desc" width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr><td align="right" valign="top" width="80" class="mail_desc_name">Betreff:</td><td><b>${subject}</b></td></tr>
		<tr><td align="right" valign="top" class="mail_desc_name">Absender:</td><td><i>${fromaddr}</i></td></tr>
		<tr><td align="right" valign="top" class="mail_desc_name">Empfänger:</td><td><i>${toaddr}</i></td></tr>
		<tr><td align="right" valign="top" class="mail_desc_name">Datum:</td><td>${date}</td></tr>
	</table>
</div>
<div id="mail_body">
${body}
${attachments_inline}
</div>
${attachments_all}
${_additional_code}
<!-- [/message.tpl] -->
