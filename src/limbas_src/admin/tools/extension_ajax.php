<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



global $umgvar;

if(isset($todo)){
	if($todo == "uploadFileFromDesktop"){
		if(lmb_strrpos($fileDest,trim($umgvar["pfad"],"/"))===false){
			$fileDest = trim($umgvar["pfad"],"/") . "/" . trim($fileDest,"/");
		}
		if(is_file("/" . $fileDest)){
			$tempFiles = explode("/",$fileDest);
			array_pop($tempFiles);
			$fileDest = implode("/",$tempFiles);
		}
		move_uploaded_file($_FILES['src']['tmp_name'], "/" . trim($fileDest,"/") . "/" . basename($_FILES['src']['name']));
		
	}elseif($todo == "moveFileDragAndDrop"){
		if(lmb_strrpos($fileSrc,trim($umgvar["pfad"],"/"))===false){
			$fileSrc = trim($umgvar["pfad"],"/") . "/" . trim($fileSrc,"/");
		}
		if(lmb_strrpos($fileDest,trim($umgvar["pfad"],"/"))===false){
			$fileDest = trim($umgvar["pfad"],"/") . "/" . trim($fileDest,"/");
		}
		rename("/" . trim($fileSrc,"/"), "/" . trim($fileDest,"/") . "/" . basename($fileSrc));
	}
}


function readExtDirPeter($path,$open){
	global $umgvar;
	
	//Depth of folderstructure (beginning with 0)
	$depth=lmb_count(explode("/",trim(str_replace($umgvar["pfad"],"",$path),"/")))-1;
	
	//If you dont want to open the folder, abort
	//if($open=='false'){return;}
	
	//Make sure path starts with path of installation
	if(lmb_strrpos($path,$umgvar["pfad"])===false){
		$path = $umgvar["pfad"] . "/" . trim($path,"/") . "/";
	}
	
	//collect data
		$handle = opendir($path);
		
		//folders
		$folders = array();
		while($file = readdir($handle)) {
			if(lmb_substr($file,0,1) != "." && is_dir($path.$file) && $file != "system") {
				array_push($folders,$file);
			}
		}
		
		//files
		rewinddir($handle);
		$files = array();
		while($file = readdir($handle)) {
			  if(lmb_substr($file,0,1) != "." && is_file($path.$file)) {
			      array_push($files,$file);
			}
		}
		
		closedir($handle);	
	
	//data output
		echo "<table cellspacing=\"0\">";
		//folders
		for($i=0;$i<lmb_count($folders);$i++){
			$sub_dir = $path . $folders[$i] . "/";	
			
			//choose right outliner
			$img_source = get_outliner("plus");			
			if($depth == 0){
				if(lmb_count($files) == 0){
					if(lmb_count($folders) == 1){ //no topfolder, no files, only folder
						$img_source = get_outliner("plusonly");
					}elseif($i == 0){ //no topfolder, no files, first folder
						$img_source = get_outliner("plustop");
					}elseif($i == lmb_count($folders)-1){ //no topfolder, no files, last folder
						$img_source = get_outliner("plusbottom");
					}
				}elseif($i == 0){ //no topfolder, only folder
				$img_source = get_outliner("plustop");
				}
			}elseif(lmb_count($files) == 0 && $i==lmb_count($folders)-1){ //no files, last folder
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
			if((lmb_count($files)>0) or ($i!=lmb_count($folders)-1)){
				$img = get_outliner("line");
			}else{
				$img = get_outliner("blank");
			}
			echo "<tr class=\"nodrag\"><td style=\"background-image:url(".$img.");background-repeat:repeat-y;\"></td><td colspan=\"2\" id=\"" . md5($sub_dir) . "\"></td></tr>";
							   
		}
		//droppable for folders
		echo "<ul id=\"ul1_" . md5($path) . "\">";
		for($i=0;$i<lmb_count($folders);$i++){
			echo "<li>" . md5($path . $folders[$i] . "/") . "</li>";
		}
		echo "</ul>";
				
		//files
		for($i=0;$i<lmb_count($files);$i++) {
			//choose right outliner
			if($i==lmb_count($files)-1){
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
		for($i=0;$i<lmb_count($files);$i++){
			echo "<li>" . md5(trim($path . $files[$i],'/')) . "</li>";
		}
		echo "</ul>";
				
		//end table
		echo "</table>";
}

function get_outliner($img){
	return "assets/images/legacy/outliner/".$img.".gif";
}


?>

