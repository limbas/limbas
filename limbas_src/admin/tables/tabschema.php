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

<div class="lmbPositionContainerMain" style="height:calc(100% - 50px);">
<FORM ACTION="main_admin.php" METHOD="post" name="form1" style="float:
left;z-index:10000;position:relative;"
onmouseover="document.form1.view_save.style.visibility='visible';"
onmouseout="document.form1.view_save.style.visibility='hidden';">
+<FORM ACTION="main_admin.php" METHOD="post" name="form1">
 <input type="hidden" name="action" value="<?=$action?>">
 <input type="hidden" name="setdrag">
-<input type="submit" onclick="setDrag();ajaxGet(null,'main_admin.php', '&action=<?=$action?>&setdrag=' +document.form1.setdrag.value,null, 'void');return false;" value="<?=$lang[1997]?>" name="view_save">
 </FORM>


<DIV class="ajax_container" ID="fieldinfo" style="width:300px;position:absolute;z-index:99999;border:1px solid black;padding:4px;visibility:hidden;background-color:<?=$farbschema[WEB11]?>"></DIV>


<?php
function schema_tab(&$pat,$gtabid,$posx,$posy){
	global $gtab;
	global $gfield;
	global $farbschema;
	
	if($pat["width"][$gtabid]){$width = "width:".$pat["width"][$gtabid]."px;";}else{$width = "";}
	if($pat["height"][$gtabid]){$height = "height:".$pat["height"][$gtabid]."px;";$height2 = "height:".($pat["height"][$gtabid]-50)."px;";}else{$height = "";$height2 = "";}
	
	echo "<TABLE class=\"lmb_container\" ID=\"tabsh_$gtabid\" STYLE=\"position:absolute;".$width.$height."left:".$posx."px;top:".$posy."px;border:1px solid black;background-color:".$farbschema["WEB7"].";overflow:hidden;z-index:$gtabid;\">";
	echo "<TR><TD TITLE=\"".$gtab["desc"][$gtabid]."\" OnMousedown=\"iniDrag(event,'$gtabid');\" STYLE=\"cursor:move;\"><B>".$gtab["table"][$gtabid]."</B> ($gtabid)</TD></TR>";
	
	echo "<TR><TD><DIV ID=\"selsh_$gtabid\" STYLE=\"border:1px solid black;background-color:".$farbschema["WEB9"].";overflow:auto;width:99%;$height2\" onmouseup=\"paint_lines();\">";
	if($gfield[$gtabid]["id"]){
	foreach ($gfield[$gtabid]["id"] as $key => $fieldid) {
		if($gfield[$gtabid]["field_type"][$key] == 11){$style = "color:green;";}
		else{$style = "color:black;";}
		echo "<SPAN CLASS=\"lmb-red-hover\" STYLE=\"$style cursor:help;\" ID=\"opt_".$gtabid."_".$fieldid."\" OnMouseOver=\"set_color(this,'red')\" OnMouseOut=\"set_color(this,'black')\" OnClick=\"lmbAjax_fieldinfo(event,'1','$gtabid','$fieldid');\" TITLE=".$gfield[$gtabid]["spelling"][$fieldid].">".$gfield[$gtabid]["field_name"][$fieldid]." (".$fieldid.")</SPAN><BR>";
	}}
	echo "</DIV></TD></TR>";	

	echo "<TR><TD STYLE=\"cursor:se-resize;\" OnMousedown=\"iniResize(event,'$gtabid');\">&nbsp;</TD></TR>";
	echo "</TABLE>\n";
	

}


function schema_link($gtabid){
	global $gfield;
	global $gtab;
	
	if(!$GLOBALS["linknr"]){$GLOBALS["linknr"] = 0;}
	
	$bzm = $GLOBALS["linknr"];
	foreach ($gfield[$gtabid]["id"] as $key => $fieldid) {
		if($gfield[$gtabid]["field_type"][$key] == 11 AND $gfield[$gtabid]["verkntabletype"][$key] == 1){
			if($gfield[$gtabid]["data_type"][$key] == 27){$dtp = "1:n";}
			elseif($gfield[$gtabid]["data_type"][$key] == 24){$dtp = "n:m";}
			$fel = $gtabid."_".$fieldid;
			$tel = $gfield[$gtabid]["verkntabid"][$key]."_".$gfield[$gtabid]["verknfieldid"][$key];
			echo "
			<DIV STYLE=\"position:absolute;\" ID=\"di_".$fel."\">
			<script type=\"text/javascript\">
			var jg_".$fel." = new jsGraphics(\"di_".$fel."\");
			s_link[$bzm]=\"".$gtabid.",".$gfield[$gtabid]["verkntabid"][$key].",".$fieldid.",".$fel.",".$tel.",".$fel.",".$dtp."\";
			</script>
			</DIV>
			\n\n";
			$bzm++;
		}
	}
	$GLOBALS["linknr"] = $bzm;
}




if($setdrag){
	lmb_SetTabschemaPattern($setdrag);
}

$pat = lmb_GetTabschemaPattern();

$posx = 0;
foreach ($gtab["tab_id"] as $key => $gtabid) {
	if($gtab["typ"][$key] != 5){
		if($pat["posx"][$gtabid]){$posx = $pat["posx"][$gtabid];}
		if($pat["posy"][$gtabid]){$posy = $pat["posy"][$gtabid];}else{$posy = 60;}
		schema_tab($pat,$gtabid,$posx,$posy);
		$posx += 200;
	}
}
if($gtab["tab_id"]){
foreach ($gtab["tab_id"] as $key => $gtabid) {
	if($gfield[$gtabid]["id"]){
		schema_link($gtabid);
	}
}}

?>
</div>
