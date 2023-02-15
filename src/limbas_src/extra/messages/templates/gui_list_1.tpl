<?php
/* parse and include gui_welcome template */
$tpl = new olTemplate("gui_welcome.tpl");
$this->add("content", $tpl->parse());
?>
<!-- [gui_list_1.tpl] -->
			<table height="100%" width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" id="mail_right_column">
						<div id="mail_list">&nbsp;</div>
					</td>
				</tr>
				<tr>
					<td id="mail_vsizer"></td>
				</tr>
				<tr>
					<td>
						<div id="mail_panel">${content}</div>
					</td>
				</tr>
			</table>
<!-- [/gui_list_1.tpl] -->