<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */
if(!$ID){echo "<BR><BR>".$lang[114];die();}

# --- preview ----
$searchwords = explode(" ",$ffilter["content"][$LID]);

if(!$filestruct){
	get_filestructure();
}
# --- Rechte ----
$sqlquery = "SELECT ID,LEVEL FROM LDMS_FILES WHERE LDMS_FILES.ID = $ID";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(lmbdb_fetch_row($rs) AND $filestruct["view"][lmbdb_result($rs, "LEVEL")]){
	$preview_archive_link = preview_archive(array($ID),$method,$searchwords,$format);
	if($preview_archive_link[0]){
		if($preview_archive_link[1]){
			echo "<BR><BR><BR>";
			foreach($preview_archive_link as $key => $value){
				if($value){
					echo "<A HREF=\"".$value."\">$value</A><BR>";
					echo "preview_$key = open(\"$value\" ,\"preview_$key\",\"toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1\");\n";
				}
			}
		}else{
			if($preview_archive_link){
				header("HTTP/1.1 301 Moved Permanently",true);
				header("Location: ".$preview_archive_link[0]);
			}else{
				header("HTTP/1.1 415 Unsupported media type",true);
				echo '<BR><BR><div style="text-align:center"><h2>'.$lang[2113].'</h2></div>';
			}
		}
	}else{
		header("HTTP/1.1 415 Unsupported media type",true);
		echo '<BR><BR><div style="text-align:center"><h2>'.$lang[2113].'</h2></div>';
	}
}else{
	header("HTTP/1.1 401 Unauthorized",true);
	echo '<BR><BR>'.$lang[114];
}

//echo "<pre>id[$ID]level[".lmbdb_result($rs, "LEVEL")."]";print_r($filestruct);
?>