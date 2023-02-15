<!-- [folder_rename.tpl] -->

<form name="rename_form" method="POST" action="${_self}">
	<input type="hidden" name="rename" value="${mbox}">
	<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="2" cellspacing="2">
		<tr><td><input id="to" style="width:100%;" type="text" name="to" value="${mbox}"></td></tr>
		<tr><td height="22"><input style="width:100%;" type="submit" value="Ordner umbenennen"></td></tr>
	<table>
</form>

<script language="javascript" type="text/javascript">//<!--
	document.getElementById('to').focus();
//--></script>

<!-- [/folder_rename.tpl] -->
