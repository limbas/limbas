<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */


if($gfield[$gtabid]["field_name"][$field_id] AND $gtab["table"][$gtabid] AND $ID AND $gfield[$gtabid]["data_type"][$field_id] == 39){

	$formname = $gfield[$gtabid]["form_name"][$field_id];
	
	if($gfield[$gtabid]["sort"][$field_id] AND $gfield[$gtabid]["perm_edit"][$field_id]){
	
		# --- Inhalt ändern ---
		if(${$formname}){
			update_data("$gtabid,$field_id,$ID",1,null);
		}
	}
	
	$onlyfield[$gtabid] = array($field_id);
	$gresult = get_gresult($gtabid,1,null,null,null,$onlyfield,$ID);
	$longresult = $gresult[$gtabid][$field_id][0];
	
	
	if($umgvar["wysiwygeditor"] == "openwysiwyg"){
		echo "<script type=\"text/javascript\" src=\"extern/wysiwyg/openwysiwyg/scripts/wysiwyg.js\"></script>\n";
	}elseif($umgvar["wysiwygeditor"] == "TinyMCE"){
		#echo "<script language=\"javascript\" type=\"text/javascript\" src=\"extern/wysiwyg/tiny_mce/tiny_mce.js\"></script>\n";
		echo "<script language=\"javascript\" type=\"text/javascript\" src=\"extern/wysiwyg/tinymce/tinymce.min.js\"></script>\n";
	}
	
	?>
	
	<DIV class="lmbPositionContainerMain">
	
	
	<?php if($gfield[$gtabid]["perm_edit"][$field_id]){?>

		<FORM ACTION="main.php" METHOD="post" NAME="form1">
		<input type="hidden" name="action" value="edit_long">
		<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
		<input type="hidden" name="field_id" value="<?= $field_id ?>">
		<input type="hidden" name="ID" value="<?= $ID ?>">
		<textarea id="<?=$formname?>" NAME="<?=$formname?>" calss="gtabchange" style="width:100%;height:650px"><?=htmlentities($longresult,ENT_QUOTES,$umgvar["charset"])?></textarea>
		<hr>
		<input type="submit" value="<?=$lang[842]?>">
		</FORM>
		</DIV>

		
		<?= lmb_ini_wysiwyg($formname,null,null,1) ?>


	
	<?php
	}elseif($gfield[$gtabid]["sort"][$field_id]){
		echo htmlentities($gfield[$gtabid]["field_name"][$field_id],ENT_QUOTES,$umgvar["charset"]);
	}
	?>
	
	
<?php }else{echo '<BR><div style="text-align:center;">'.$lang[114].'</div>';}?>