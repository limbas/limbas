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
 * ID: 227
 */



function schema_tab(&$pat,$tabname){
	global $farbschema;
	global $DBA;
	
	$posx = $pat["posx"][$tabname];
	$posy = $pat["posy"][$tabname];
	
	if($pat["width"][$tabname]){$width = "width:".$pat["width"][$tabname]."px;";}else{$width = "";}
	if($pat["height"][$tabname]){$height = "height:".$pat["height"][$tabname]."px;";$height2 = "height:".($pat["height"][$tabname]-50)."px;";}else{$height = "";$height2 = "";}
	
	echo "<TABLE class=\"lmb_container\" ID=\"tabsh_".$tabname."\" STYLE=\"position:absolute;".$width.$height."left:".$posx."px;top:".$posy."px;border:1px solid black;background-color:".$farbschema["WEB7"].";overflow:hidden;z-index:".$pat["id"][$tabname]."\">";
	echo "<TR><TD OnMousedown=\"iniDrag(event,'$tabname');\" STYLE=\"cursor:move;\"><B>".base64_decode($tabname)."</B><i style=\"padding-left:5px;float:right;cursor:pointer;\" OnClick=\"lmbAjax_ViewEditorPattern('$tabname;;;1')\" class=\"lmb-icon lmb-close-alt\"></i></TD></TR>";
	# &nbsp;<input type=\"text\" style=\"width:150px;opacity: 0.5\"> # table alias
	echo "<TR><TD><DIV ID=\"selsh_$tabname\" STYLE=\"border:1px solid black;background-color:".$farbschema["WEB9"].";overflow:auto;width:99%;$height2\" onmouseup=\"paint_lines();\">";
	
	$tabname_ = explode("#",base64_decode($tabname));
	$fields = dbf_5(array($DBA["DBSCHEMA"],$tabname_[0]));
	if($fields["columnname"]){
	foreach ($fields["columnname"] as $key => $fieldname) {
		echo "<span style=\"$style cursor:pointer\" ID=\"opt_".$tabname."_".str_replace("=","",base64_encode($fieldname))."\" OnMouseOver=\"this.style.fontWeight='bold'\" OnMouseOut=\"this.style.fontWeight='normal'\" OnMousedown=\"iniDrag(event,document.getElementById('relationSign'),this);return false;\" OnMouseUp=\"iniRelation(event,this)\">"
		.$fieldname.
		"</span><br>\n";
	}}
	
	echo "</DIV></TD></TR>";
	echo "<TR><TD STYLE=\"cursor:se-resize;\" OnMousedown=\"iniResize(event,'$tabname');\">&nbsp;</TD></TR>";
	echo "</TABLE>\n";
}


function schema_link(&$relationset,$key,$bzm){
	global $gfield;
	global $gtab;
	
	$fel = $relationset["tabl"][$key]."_".$relationset["fieldl"][$key];
	$tel = $relationset["tabr"][$key]."_".$relationset["fieldr"][$key];
	
echo "<DIV STYLE=\"position:absolute;\" ID=\"di_".$bzm."\">
<script type=\"text/javascript\">
var jg_$bzm = new jsGraphics(\"di_".$bzm."\");
s_link[$bzm] = \"".$relationset["tabl"][$key].",".$relationset["tabr"][$key].",,".$fel.",".$tel.",".$bzm.",".$dtp."\";
</script>
</DIV>
\n\n";
}


if($setdrag){
	lmb_SetTabschemaPattern($setdrag,$viewid,$gview["relationstring"]);
}

$pat = lmb_GetTabschemaPattern($viewid);

if($pat["id"]){
	foreach ($pat["id"] as $key => $value) {
		schema_tab($pat,$key);
	}
}

$relationset = lmb_getQuestRelation($viewid,$gview["relationstring"]);

if($relationset){
$bzm=0;
foreach ($relationset["tabl"] as $key => $value) {
	schema_link($relationset,$key,$bzm);
	$bzm++;
}}



?>