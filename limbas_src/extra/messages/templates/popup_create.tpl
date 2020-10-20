<!-- [folder_create.tpl] -->

<form name="create_form" method="POST" action="${_self}">
	<input type="hidden" name="create_root" value="${mbox}">
	<table class="mail_desc" width="100%" height="100%" border="0" cellpadding="2" cellspacing="2">
		<tr><td><input id="folder_create" style="width:100%;" type="text" name="create" value=""></td></tr>
		<tr><td height="22"><input style="width:100%;" type="submit" value="Ordner erstellen"></td></tr>
	<table>
</form>

<script language="javascript" type="text/javascript">//<!--
	document.getElementById('folder_create').focus();
//--></script>

<!-- [/folder_create.tpl] -->
