<!-- [gui_welcome.tpl] -->
<script language="javascript" type="text/javascript">//<!--
	/* get default sort options */
	var sort_col = C.get('mail_sort_col');
	var sort_desc = C.get('mail_sort_desc');
	var markedmail = C.get('mail_marked');
	sort_desc = (sort_desc) ? parseInt(sort_desc) : 0;

	/* get default gui options */
	var hsizer_pos = C.get('mail_hsizer');
	var vsizer_pos = C.get('mail_vsizer');
	
	document.writeln("<h2>Cookies</h2><ul>");
	document.writeln("<li><h3>Selection</h3>");
	document.writeln("<i>selection</i> :: " + (markedmail ? markedmail : '-'));
	document.writeln("<li><h3>GUI</h3>");
	document.writeln("<i>hsizer</i> :: " + (hsizer_pos ? (hsizer_pos+'%') : 'default') + "<br>");
	document.writeln("<i>vsizer</i> :: " + (vsizer_pos ? (vsizer_pos+'%') : 'default') + "<br>");
	document.writeln("<li><h3>List</h3>");
	document.writeln("<i>sort</i> :: " +	(sort_col
		? ("'" + sort_col + "', " + (sort_desc ? 'descending' : 'ascending'))
		: 'default' ) + "<br></ul>");
//--></script>

<small><xmp>$Id: gui_welcome.tpl 402 2009-10-25 15:24:00Z daniel $</xmp></small>

<!-- [/gui_welcome.tpl] -->
