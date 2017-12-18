<?php
$_path = $this->search('_path');
$fwd_message = $this->search('fwd_message');
if ($fwd_message)
	$_additional_javascript_code = <<<EOD
	var el = $('mail_upload_panel');
	el.innerHTML = "<h3>Dateianhang</h3>";
	__upload_add('{$fwd_message}');
EOD;


if ($this->search('gui_style') == MAILER_GUI_STYLE_WINDOW){
	$_additional_html_code = <<<EOD
<script src="{$_path}classes/js/olEvent.js" language="javascript" type="text/javascript"></script>
<script src="{$_path}classes/js/olPopup.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
var E = new olEvent();

function _olWindow_mini(){
	this.hide = function(){
		window.close();
	};
	return this;
}

function _olGUI_mini(){
	this.win_message = new _olWindow_mini();
	this.win_popup = new olPopup('olPop', 'Nachrichtenversand', 320, 128, _baseURL_);
	return this;
}

var olGUI = new _olGUI_mini();

function mail_popup_status(msg, img){
	olGUI.win_popup.show('<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" '+
		'cellspacing="0"><tr><td align="center"><table height="100%" border="0" cellpadding="4"  '+
		'cellspacing="0"><tr><td align="right"><img src="'+_basePATH_+'images/'+img+'" width="20" height="20"  '+
		'border="0"></td><td align="left">'+msg+'</td></tr></table></td>'+
		'</tr></table>', false, false);
}
//--></script>

EOD;
}
$this->add("_additional_html_code",
	isset($_additional_html_code) ? $_additional_html_code : '');

$this->add("_additional_javascript_code",
	isset($_additional_javascript_code) ? $_additional_javascript_code : '');

?>
<!-- [compose.tpl] -->
${_additional_html_code}
<script src="${_path}classes/js/olUpload.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
function __upload_add(filename){
	/* add a hidden input field for the attachment */
	var i = document.createElement("input");
	i.name = "attachment_" + filename;
	i.value = filename;
	i.type = "hidden";
	$('compose_form').appendChild(i);

	/* add an icon */
	var el = $('mail_upload_panel');
	el.innerHTML += '<div><i class="lmb-icon lmb-mail" border="0" align="top" hspace="4"></i>'+filename+'</div>';
	el.style.display = 'block';
}
function __upload_show(){
	alert('TODO: implement __upload_show()!');
}
//--></script>
<form id="compose_form" method="POST" action="${_self}" target="ifrm">
	<input type="hidden" name="compose" value="1">
		<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20%" align="right" class="mail_desc_name" height="22">Absender:</td>
				<td width="80%">
					<select name="fromaddr" style="border:solid 1px silver; border-right:0; width:100%;">
					<option>${fromaddr}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" class="mail_desc_name" height="22">Empfänger:</td>
				<td><input name="toaddr" type="text" size="40" 
					style="border:solid 1px silver; border-right:0; width:100%;"
					maxlen="320" value="${toaddr}"></td>
			</tr>
			<tr>
				<td align="right" class="mail_desc_name" height="22">Betreff:</td>
				<td><input name="subject" type="text" size="40"
					style="border:solid 1px silver; border-right:0; width:100%;"
					maxlen="255" value="${subject}"></td>
			</tr>
			<tr>
				<td colspan="2" style="
					border-top:solid 1px silver;
					border-bottom:solid 1px silver;
				">
				<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
					<tr style="height:100%;" id="compose_body">
						<td id="mail_body_panel">
							<textarea id="mail_body" name="body" cols="20" rows="5"
								style="width:100%;height:100%;padding:2px;border:0px;"
								>${body}</textarea>
						</td>
					</tr>
					<tr>
						<td id="mail_upload_panel" style="display:none;">
						</td>
					</tr>
					<tr id="mail_error_panel" style="display:none;border-top:solid 1px gray;background-color:pink;">
						<td style="overflow:hidden;" height="50">
							<iframe width="400" height="50" style="border:none;display:none;" href="about:blank"
								id="compose_ifrm" name="ifrm"></iframe>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" height="22">
					<!--<a href="#" onclick="__upload_show(); return false">Datei(en) anhängen</a>-->
					<input type="submit" value="Nachricht senden"
						onclick="mail_popup_status('Nachrichtenversand wird vorbereitet...', 'wait.gif'); return true">				  </td>
			</tr>
		</table>
</form>
<script language="javascript" type="text/javascript">//<!--
${_additional_javascript_code}
//--></script>
<!-- [/compose.tpl] -->
