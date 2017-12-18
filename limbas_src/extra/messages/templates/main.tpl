<?php
/* define gui_list_template in dependancy of gui_style */
$gui_style = intval($this->search("gui_style"));
$tpl_list = new olTemplate("gui_list_{$gui_style}.tpl");
$this->add("gui_list_template", $tpl_list->parse());

/* parse and include gui_menu template */
$tpl_menu = new olTemplate("gui_menu.tpl");
$this->add("gui_menu_template", $tpl_menu->parse());

global $farbschema;
$this->add("mail_left_bgcolor", $farbschema['WEB8']);
?>
<!-- [main.tpl] -->
<script src="${_path}main.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
	function _(){
		var dbg = new olPopup('olWin',  'DEBUG Window', 480, 320, _baseURL_);
		var content = '';
		var cnt=0;
		for (var i in arguments[0]){
			cnt++;
			t = typeof arguments[0][i];
			content += "<p><b>"+ i + "</b>("+t+") = " + arguments[0][i] + "</p>";
		}
		dbg.setCaption(arguments[0]);
		dbg.setStatus(cnt + ' properties.');
		dbg.show(content, false, false);
	}
//--></script>
<div class="lmbPositionContainerMain">
<table class="tabfringe" width="100%" height="100%" cellspacing="0" cellpadding="0"><tr>
<td class="lmbfringeFrameMain" valign="top">
<table id="mail_main" height="100%" width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
            <td id="mail_left_column" valign="top">
				<div id="mail_folders">
					<a href="${_self}&amp;folders=1"
						onclick="return mail_folders()">Ordnerliste abrufen</a>
				</div>
		</td>
		<td id="mail_menu" colspan="3" valign="top">${gui_menu_template}</td>
	</tr>
	<tr>
                <td></td>
		<td width="2" id="mail_hsizer">&nbsp;</td>
		<td valign="top">
			${gui_list_template}
		</td>
	</tr>
	<tr>
            <td></td>
		<td id="mail_status" colspan="3" class="gtabFooterTAB" style="width:20px">${status}</td>
	</tr>
	<tr>
		<td class="lmbGtabBottom" style="width:0;" colspan="3"></td>
	</tr>
</table>
</td></tr></table></div>
<!-- [/main.tpl] -->
