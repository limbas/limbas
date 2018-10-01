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

if($activelist AND $download_archive AND $LINK[190]){
	if($download_link = download_archive($activelist,$LID,$download_archive)){
		header("HTTP/1.1 301 Moved Permanently",true);
		header("Location: $download_link",true);
		header("Content-Disposition:attachment; filename=test.pdf");
	}else{
		# --- Fehlermeldungen -----
		header("HTTP/1.1 401 Unauthorized",true);
		if(is_array($alert)){
			if($alert AND !is_array($alert)){$alert = array($alert);}
			$alert = array_unique($alert);
			echo "<script language=\"JavaScript\">";
			echo "alert('".implode("\\n",$alert)."');\n";
			echo "</script>";
		}
	}
}else{
	header("HTTP/1.1 401 Unauthorized",true);
	echo "<BR><BR>".$lang[114];
}
?>