<!-- [compose.tpl] -->
<div id="olWin_inner" class="popup_inner">
<form method="POST" action="${_self}" target="ifrm">
	<input type="hidden" name="compose" value="1">
	
		<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td align="right" class="mail_desc_name" width="60" height="22">Absender:</td>
				<td>
					<select name="fromaddr" style="border:solid 1px silver; border-right:0; width:100%;">
					<option>${fromaddr}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="right" class="mail_desc_name" height="22">Empf�nger:</td>
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
				<tr style="height:100%;" id="compose_body"><td><textarea name="body" cols="40" rows="5" style="
	border:0;
	width:100%;
	height:100%;
	padding-left:2px;
	margin:0px;
">Die LIMBAS GmbH entwickelt webbasierte Free und Open Source Software. Der Hauptfokus von LIMBAS ist Unternehmensl�sungen und Fachverfahren mit dem eigenen Open Application Development System umzusetzen. Dies ist ein webbasiertes Framework um Datenbankanwendungen �ber eine grafische Oberfl�che einfach umzusetzen.

Open LIMBAS erg�nzt Ihr Open Office und ist insbesondere dazu geeignet, Access Applikationen abzul�sen. Es liefert einen hohen Anteil an Basis Funktionalit�ten wie Tabellen, Formularen, Berichten, Abfragen und mehr. LIMBAS bietet Ihnen Vorteile, die Sie bisher mit anderen Systemen nicht nutzen konnten. Sie arbeiten mit einer zentralen und unabh�ngigen Datenbank, genie�en den Vorzug einer zentralen Verwaltung und Rechtevergabe und haben die M�glichkeit einer delegierten Prozessentwicklung. So arbeiten Ihre Mitarbeiter nicht nur schneller und effizienter zusammen sondern Sie sparen Kosten.</textarea></td></tr><tr><td id="upload_page" style="display:none;">
<div class="upload_page">
	<div id="upload_inner"></div>
	<div id="upload_error"></div>
</div>

					</td></tr></table>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" height="22">
<!--					<a href="#" onclick="show_upload(); return false">Datei(en) anh�ngen</a>-->
	<span id="upload_container">
		<form name="imgup" enctype="multipart/form-data" method="POST" action="${self}">
			<input type="hidden" name="MAX_FILE_SIZE" value="${max_file_size}">
			<input id="upload_fileinput" type="file" name="Filedata" onclick="document.getElementById('upload_fileinput').style.display='none'; return true;">
			<input type="submit" value="Datei anh�ngen">
			<input type="hidden" name="sid" value="${sid}">
			(max. ${max_file_size} Bytes)
		</form>
	</span>

					<input style="margin-left:2px;float:left;border:solid 1px black;background-color:#cccccc;height:22px;font-size:9pt;font-family:Arial;" type="submit" value="Nachricht senden"
						onclick="mail_popup_status('Nachricht wird versandt...', 'wait.gif'); return true">
				</td>
			</tr>
		</table>

	<iframe style="display:none;" href="about:blank" name="ifrm"></iframe>
	
</form>
</div>
<!-- [/compose.tpl] -->
