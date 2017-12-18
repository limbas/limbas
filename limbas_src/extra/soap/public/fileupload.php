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

function call_soap(){
	$lmpar[1]["getvars"] = array('fresult','filestruct');		# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
	$lmpar[1]["action"] = "explorer_main";			# you can use tables [gtab_erg] or filemanager [explorer_main]
	$lmpar[1]["LID"] = "58";						# ID of folder where searched

	#if($lmpar[1]["upload_file"]["content"][0] = base64_encode(file_get_contents("/mypath/test.jpg"))){	# you have to encode the filecontent to base64
	#	$lmpar[1]["upload_file"]["name"][0] = "test.jpg";												# name of the file
	#	$lmpar[1]["upload_file"]["mimetype"][0] = "image/jpeg";											# mimetype of file
	#	$lmpar[1]["upload_file"]["dublicate"][0] = "overwrite";											# you can overwrite, rename, or versioning files
	#	$lmpar[1]["upload_file"]["relation"][0] = null; #array([datid],[tableid],[fieldid]);			# you can create a relation to a specific dataset

		return call_client($lmpar);
	#}
}

$lmb = call_soap();


echo "<pre>";
print_r($lmb[1]);

foreach($lmb[1]["ffile"]["id"] as $key => $value){
	echo $lmb[1]["ffile"]["name"][$key].", ".$lmb[1]["ffile"]["mimetype"][$key]."<br>";
}

?>