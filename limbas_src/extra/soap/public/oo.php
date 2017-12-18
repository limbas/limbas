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


require("lib/include.lib");

if($action=="tree"){
	if(!$GLOBALS["filestruct"]){
		$lmpar[0]["action"] = "explorer_main";
		$lmpar[0]["getvars"] = array('filestruct');
		$sresult = call_client($lmpar);
		if(!$GLOBALS["filestruct"]){
			$filestruct = $sresult[0]["filestruct"];
		}
	}
	
	$xml = DMSTreeToXML(0,null);
	$xml = "<tree>\n".$xml."</tree>";
	echo $xml;
	
}elseif($action=="upload" AND $ufile AND is_numeric($level)){
	
	$file["file_size"] = $ufile_size;
	$file["file_name"] = $ufile_name;
	$file["file_type"] = $ufile_type;
	$file["file"] = $ufile;
	$file["dublicate"] = $dublicate;
	$file["dublicate_subj"] = $dublicate_subj;
	
	if($file = LMB_fileUpload($file)){
		$lmpar[0]["action"] = "file_upload";
		$lmpar[0]["file"] = $file;
		$lmpar[0]["level"] = $level;
		
		$sresult = call_client($lmpar);
		echo "<id>".$sresult[0]."</id>";
	}
	

}


function DMSTreeToXML($LEVEL,$xml){
	global $filestruct;

	if($filestruct["id"]){
	foreach($filestruct["id"] as $key => $value){
		if($filestruct["level"][$key] == $LEVEL AND $filestruct["view"][$key]){
		
			$xml .= "<folder id=\"".$filestruct["id"][$key]."\">\n";
			$xml .= "<description>".$filestruct["name"][$key]."</description>\n";
			$xml .= "<perm_add>".$filestruct["add"][$key]."</perm_add>\n";
			$xml .= "<perm_addf>".$filestruct["addf"][$key]."</perm_addf>\n";
			$xml .= "<perm_edit>".$filestruct["edit"][$key]."</perm_edit>\n";
			$xml .= "<created>".$filestruct["erstdatum"][$key]."</created>\n";
			$xml .= "<modified>".$filestruct["editdatum"][$key]."</modified>\n";
			
			if(in_array($filestruct["id"][$key],$filestruct["level"])){
				$xml .= "<subfolders>\n";
				$sxml = DMSTreeToXML($filestruct["id"][$key],$xml);
				$xml = $sxml."</subfolders>\n";
			}
			
			$xml .= "</folder>\n";
		}
	}
	}
	
	return $xml;

}



?>

<FORM ACTION="oo.php" METHOD="post" name="form2" enctype="multipart/form-data">
<input type="hidden" name="action" value="upload">
<input type="hidden" name="level" value="160">

<input type="file" name="ufile" value="upload">

<input type="submit" value="send">

</form>