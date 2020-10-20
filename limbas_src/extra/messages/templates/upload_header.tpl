<!-- [upload_header.tpl] -->
<link href="../styles/olUpload.css" rel="stylesheet" type="text/css">
<script src="../olEvent.js" language="javascript" type="text/javascript"></script>
<script src="../olUpload.js" language="javascript" type="text/javascript"></script>
<script language="javascript" type="text/javascript">//<!--
	var upload = null;
	var E = new olEvent();

	/* initialize the upload object on page load. */
	E.observe(window, 'load', function (){
		upload = olUpload('${self}', 140, 22, '${sid}', ${max_file_size}, 0);
	});
	 
	/* Terrible: An external interface wrapper for the flash object is needed,
	 * because Flash does not allow us to call a JavaScript object's method directly. */
	function upload_browse(a, b)   { upload.browse(a, b); }
	function upload_start(a, b)    { upload.start(a, b); }
	function upload_begin(a, b)    { upload.begin(a, b); }
	function upload_status(a, b)   { upload.status(a, b); }
	function upload_complete(a, b) { upload.complete(a, b); }
	function upload_finished(a, b) { upload.finished(a, b); }
	function upload_error(a, b)    { upload.error(a, b); }
//--></script>
<!-- [/upload_header.tpl] -->
