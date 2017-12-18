<!-- [find.tpl] -->

<form name="find_form" method="POST">
    <table class="mail_desc" width="100%" height="100%" border="0" cellpadding="2" cellspacing="2">
		<tr>
			<td align="right"  class="mail_desc_name" height="22">Bereich:</td>
			<td>
				<select id="range" style="width:100%;">
					<option value="ALL">alle</option>
					<option value="SEEN">gelesene</option>
					<option value="UNSEEN">ungelesene</option>
					<option value="ANSWERED">beantwortete</option>
					<option value="UNANSWERED">unbeantwortete</option>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"  class="mail_desc_name" height="22">Kriterium:</td>
			<td>
				<select id="criteria" style="width:100%;">
					<option value="FROM">Absender</option>
					<option value="TO">Empf&auml;nger</option>
					<option value="SUBJECT">Betreff</option>
					<option value="BODY">Text</option>
				<!--
					<option name="SINCE">sucht nach "datum" gesendete Nachrichten</option>
					<option name="ON">die Nachrichten wurden am angegebenen "datum" gesendet</option>
					<option name="BEFORE">die gesuchten Nachrichten wurden vor "datum" gesendet</option>
				-->
				</select>
			</td>
		</tr>
		<tr>
			<td align="right"  class="mail_desc_name" valign="top">Suchbegriff(e):</td>
			<td>
			<!--<input id="find" style="width:100%;" type="text" name="find" value="">-->
			<textarea id="find" rows="1" cols="32" style="font-family:inherit;font-size:inherit;width:100%;height:100%;" name="find"></textarea>
			</td>
		</tr>
		<tr>
                    <td colspan="2" height="22" style="text-align:center"><hr><input type="button" onclick="return mail_search()" value="Nachrichten suchen"></td>
		</tr>
	<table>
</form>

<script language="javascript" type="text/javascript">//<!--
	$('find').focus();
//--></script>

<!-- [/find.tpl] -->
