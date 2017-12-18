<?php
/* hack-alert: this template and status_once.tpl is intended to be shown only within an (invisible) iframe! */
$_autoclose_timeout = $this->search("autoclose");

if ($_autoclose_timeout)
	$_js_code = <<<EOD
		parent.E.observe(window, "load", function(e){
			parent.olGUI.win_message.hide(parent.olGUI.win_message);
			try {
				window.setTimeout(function(){
					try {
						parent.olGUI.win_popup.hide(parent.olGUI.win_popup);
					} catch(e) {}
				}, $_autoclose_timeout);
			} catch(e) {}
		});
EOD;

$this->add("_js_code", (isset($_js_code)) ? $_js_code : '');

?>
<!-- [status.tpl] -->
<script language="javascript" type="text/javascript">//<!--
	content = '<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">';
	content += '<tr><td align="center">';
	content += '<table height="100%" border="0" cellpadding="4" cellspacing="0">';
	content += '<tr><td align="right"><img src="${_path}images/${image}" width="20" height="20" border="0">';
	content += '</td><td align="left">${message}</td></tr></table>';
	content += '</td></tr></table>';
	parent.olGUI.win_popup.show(content, false);
	${_js_code}
//--></script>
<!-- [/status.tpl] -->
