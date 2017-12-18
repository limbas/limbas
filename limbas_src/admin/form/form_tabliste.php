<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */
?>


<script language="JavaScript">


/* --- Tabmenusteuerung ----------------------------------- */
pic_plusonly = new Image(); pic_plusonly.src = "pic/outliner/plusonly.gif";
pic_minusonly = new Image(); pic_minusonly.src = "pic/outliner/minusonly.gif";

// Ajax request
function LmAdm_getFields(gtabid,actel,parent_tab,parent_field){

	var tab = "tab_"+actel;
	if( eval("document.tab_"+actel+"_plusminus.src == pic_plusonly.src") ){
		eval("document.tab_"+actel+"_plusminus.src = pic_minusonly.src");
		var url = "main_dyns_admin.php";
		dynfunc = function(result){LmAdm_formTabFieldListPost(result,actel,gtabid,parent_tab,parent_field);};
		actid = "formTabFieldList&gtabid=" + gtabid + "&parent_tab="+ parent_tab + "&parent_field="+ parent_field;
		ajaxGet(null,url,actid,null,"dynfunc");
	}else{
		eval("document.tab_"+actel+"_plusminus.src = pic_plusonly.src");
		document.getElementById("el_"+actel).innerHTML = '';
	}
}

// Ajax output
function LmAdm_formTabFieldListPost(result,actel,gtabid,parent_tab,parent_field){
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
		echo "<A HREF=\"Javascript:LmAdm_getFields('$value','$globid','')\"><IMG SRC=\"pic/outliner/plusonly.gif\" WIDTH=\"18\" HEIGHT=\"16\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_plusminus\"><i class=\"lmb-icon lmb-folder-closed\" WIDTH=\"16\" HEIGHT=\"13\" ALIGN=\"TOP\" BORDER=\"0\" NAME=\"tab_".$globid."_box\"></i></A> <B>".$gtab["desc"][$value]."</B></TD></TR>\n";
		echo "<TR><TD ID=\"el_$globid\">";
		
		echo "</TR></TD>";
		echo "</TABLE>\n";
	}
	
}

tabpool($referenz_tab);
?>