<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<script language="JavaScript">


/* --- Tabmenusteuerung ----------------------------------- */
pic_plusonly = new Image(); pic_plusonly.src = "assets/images/legacy/outliner/plusonly.gif";
pic_minusonly = new Image(); pic_minusonly.src = "assets/images/legacy/outliner/minusonly.gif";

// Ajax request
function LmAdm_getFields(gtabid,actel,parent_tab,parent_field,parentrelpath){

	var tab = "tab_"+actel;
	if( eval("document.tab_"+actel+"_plusminus.src == pic_plusonly.src") ){
		eval("document.tab_"+actel+"_plusminus.src = pic_minusonly.src");
		var url = "main_dyns_admin.php";
		dynfunc = function(result){LmAdm_formTabFieldListPost(result,actel);};
		var actid = "formTabFieldList&gtabid=" + gtabid + "&parent_tab="+ parent_tab + "&parent_field="+ parent_field+"&parentrelpath="+parentrelpath;
		ajaxGet(null,url,actid,null,"dynfunc");
	}else{
		eval("document.tab_"+actel+"_plusminus.src = pic_plusonly.src");
		document.getElementById("el_"+actel).innerHTML = '';
	}
}

// Ajax output
function LmAdm_formTabFieldListPost(result,actel){
	document.getElementById("el_"+actel).innerHTML = result;
}
	
</script>

<?php

function tabpool($gtabid){
	global $gtab;
	global $gfield;

	foreach ($gtab["raverkn"][$gtab["verkn"][$gtabid]] as $key => $value){
	
		$globid = rand(1,10000);
	
		echo "<TABLE BORDER=\"0\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n";
		echo "<TR><TD>";
		echo "<A HREF=\"Javascript:LmAdm_getFields('$value','$globid','','','')\"><IMG SRC=\"assets/images/legacy/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B>".$gtab["desc"][$value]."</B></TD></TR>\n";
		echo "<TR><TD ID=\"el_$globid\">";
		
		echo "</TR></TD>";
		echo "</TABLE>\n";
	}
	
}

tabpool($referenz_tab);
?>
