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

global $umgvar;

if(isset($todo)){
	if($todo == "uploadFileFromDesktop"){
		if(strrpos($fileDest,trim($umgvar["pfad"],"/"))===false){
			$fileDest = trim($umgvar["pfad"],"/") . "/" . trim($fileDest,"/");
		}
		if(is_file("/" . $fileDest)){
			$tempFiles = explode("/",$fileDest);
			array_pop($tempFiles);
			$fileDest = implode("/",$tempFiles);
		}
		move_uploaded_file($_FILES['src']['tmp_name'], "/" . trim($fileDest,"/") . "/" . basename($_FILES['src']['name']));
		
	}elseif($todo == "moveFileDragAndDrop"){
		if(strrpos($fileSrc,trim($umgvar["pfad"],"/"))===false){
			$fileSrc = trim($umgvar["pfad"],"/") . "/" . trim($fileSrc,"/");
		}
		if(strrpos($fileDest,trim($umgvar["pfad"],"/"))===false){
			$fileDest = trim($umgvar["pfad"],"/") . "/" . trim($fileDest,"/");
		}
		rename("/" . trim($fileSrc,"/"), "/" . trim($fileDest,"/") . "/" . basename($fileSrc));
	}
}


function readExtDirPeter($path,$open){
	global $umgvar;
	
	//Depth of folderstructure (beginning with 0)
	$depth=count(explode("/",trim(str_replace($umgvar["pfad"],"",$path),"/")))-1;
	
	//If you dont want to open the folder, abort
	//if($open=='false'){return;}
	
	//Make sure path starts with path of installation
	if(strrpos($path,$umgvar["pfad"])===false){
		$path = $umgvar["pfad"] . "/" . trim($path,"/") . "/";
	}
	
	//collect data
		$handle = opendir($path);
		
		//folders
		$folders = array();
		while($file = readdir($handle)) {
			if(substr($file,0,1) != "." && is_dir($path.$file) && $file != "system") {
				array_push($folders,$file);
			}
		}
		
		//files
		rewinddir($handle);
		$files = array();
		while($file = readdir($handle)) {
			  if(substr($file,0,1) != "." && is_file($path.$file)) {
			      array_push($files,$file);
			}
		}
		
		closedir($handle);	
	
	//data output
		echo "<table cellspacing=\"0\">";
		//folders
		for($i=0;$i<count($folders);$i++){
			$sub_dir = $path . $folders[$i] . "/";	
			
			//choose right outliner
			$img_source = get_outliner("plus");			
			if($depth == 0){
				if(count($files) == 0){
					if(count($folders) == 1){ //no topfolder, no files, only folder
						$img_source = get_outliner("plusonly");
					}elseif($i == 0){ //no topfolder, no files, first folder
						$img_source = get_outliner("plustop");
					}elseif($i == count($folders)-1){ //no topfolder, no files, last folder
						$img_source = get_outliner("plusbottom");
					}
				}elseif($i == 0){ //no topfolder, only folder
				$img_source = get_outliner("plustop");
				}
			}elseif(count($files) == 0 && $i==count($folders)-1){ //no files, last folder
				$img_source = get_outliner("plusbottom");
			}
			
			//onclick parameter
			$img_onclick = "lmb_getTree('" . $sub_dir . "','" . md5($sub_dir) . "',true)";
			$span_onclick = "lmb_ExtOpenContextFolder(this,'" . trim(str_replace($umgvar["pfad"],"",$path) . $folders[$i],'/') . "','" . $folders[$i] . "')";
			
			//output
			echo "<tr type=\"folder\" open=\"false\" id=\"tr_" . md5($sub_dir) . "\" fullpath=\"" . trim($sub_dir,"/") . "\" actfile=\"" . trim(str_replace($umgvar["pfad"],"",$sub_dir),"/") . "\" actid=\"" . md5($sub_dir) . "\"><td>";
			
			//open-symbol
			echo "<img id=\"" . md5($sub_dir) . "_imgopen\" style=\"cursor:hand\" onclick=\"" . $img_onclick . "\" src=" . $img_source . ">";
			echo "</td><td width=\"20px\">";
			
			//folder-symbol
			echo "<i id=\"" . md5($sub_dir) . "_boxopen\" onclick=\"" . $img_onclick . "\" class=\"lmb-icon lmb-folder-closed\"></i>";
			echo "</td><td>";
			
			//file
			echo "<span style=\"cursor:pointer;\" onclick=\"" . $span_onclick . "\" onmouseover=\"this.style.textDecoration='underline'\" onmouseout=\"this.style.textDecoration=''\">";
			echo $folders[$i];
			echo "</span>";
			echo "</td></tr>";
			
			//invisible tr
			if((count($files)>0) or ($i!=count($folders)-1)){
				$img = get_outliner("line");
			}else{
				$img = get_outliner("blank");
			}
			echo "<tr class=\"nodrag\"><td style=\"background-image:url(".$img.");background-repeat:repeat-y;\"></td><td colspan=\"2\" id=\"" . md5($sub_dir) . "\"></td></tr>";
							   
		}
		//droppable for folders
		echo "<ul id=\"ul1_" . md5($path) . "\">";
		for($i=0;$i<count($folders);$i++){
			echo "<li>" . md5($path . $folders[$i] . "/") . "</li>";
		}
		echo "</ul>";
				
		//files
		for($i=0;$i<count($files);$i++) {
			//choose right outliner
			if($i==count($files)-1){
				$img = get_outliner("joinbottom"); //last file
			}else{
				$img = get_outliner("join");
			}
			
			//onclick-parameter
			$span_onclick = "lmb_ExtOpenContext(this,'" . trim(str_replace($umgvar["pfad"],"",$path) . $files[$i],'/') . "','" . $files[$i] . "')";
			
			//output
			echo "<tr><td><img src=" . $img . "></td><td colspan=\"2\">";
			echo "<div id=\"" . md5(trim($path . $files[$i],'/')) . "\" path=\"" . trim($path . $files[$i],'/') . "\">"; 
			echo "<span style=\"cursor:pointer;\" onclick=\"" . $span_onclick . "\" onmouseover=\"this.style.textDecoration='underline'\" onmouseout=\"this.style.textDecoration=''\">";
			echo $files[$i];
			echo "</span>";
			echo "</div>";
			echo "</td></tr>";
			
		}	
		
		//draggable for files
		echo "<ul id=\"ul2_" . md5($path) . "\">";
		for($i=0;$i<count($files);$i++){
			echo "<li>" . md5(trim($path . $files[$i],'/')) . "</li>";
		}
		echo "</ul>";
				
		//end table
		echo "</table>";
}

function get_outliner($img){
	return "pic/outliner/".$img.".gif";
}


?>

