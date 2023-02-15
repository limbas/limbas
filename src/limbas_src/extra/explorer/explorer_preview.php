<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
