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

if (is_numeric($ID)){
	if($file = file_download($ID,$convert)){
		if($sendas OR lmb_substr($file["name"],lmb_strlen($file["name"])-3,3) == "php"){
		    header("Content-Type: ".$file["mimetype"]);
		    header("Content-Length: ".filesize($file["path"]));
		    if($sendas == "a"){
		    	header('Content-Disposition: attachement; filename="'.$file["name"].'"');
		    }else{
		    	header('Content-Disposition: inline; filename="'.$file["name"].'"');
		    }
			readfile($file["path"]);
		}else{
			header("HTTP/1.1 301 Moved Permanently",true);
			header("Location: ".$file["url"]);
		}
	}else{
		header("HTTP/1.1 401 Unauthorized",true);
		echo "<BR><BR>".$lang[114];
	}
}else{
	header("HTTP/1.1 401 Unauthorized",true);
	echo "<BR><BR>".$lang[114];
}

?>