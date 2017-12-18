<!-- [status_once.tpl] -->
<script language="javascript" type="text/javascript">//<!--
function display_errors(e){
//	parent.$('compose_ifrm').style.display = 'block';
//	parent.$('mail_error_panel').style.display = 'block';
	parent.E.remove(window, "load", display_errors);
	
	if (e.target.body.innerHTML){
		content = '<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">';
		content += '<tr><td align="center">';
		content += '<table height="100%" border="0" cellpadding="4" cellspacing="0">';
		content += '<tr><td align="right"><img src="${_path}images/error.gif" width="20" height="20" border="0">';
		content += '</td><td align="left">'+e.target.body.innerHTML+'</td></tr></table>';
		content += '</td></tr></table>';

		parent.olGUI.win_popup.show(content, false);
	}
}
parent.E.observe(window, "load", display_errors);
//--></script>
<!-- [/status_once.tpl] -->
