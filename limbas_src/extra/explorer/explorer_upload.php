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

# ---------------- Datei-upload -----------------------
if(is_numeric($LID) AND $LINK[128] AND $_FILES){
	
	$file_["file"] = $_FILES['file']['tmp_name'];
	$file_["file_name"] = $_FILES['file']['name'];
	$file_["file_type"] = $_FILES['file']['type'];
	$file_["file_archiv"] = $file_archiv;

	if($ufileId = upload($file_,$LID,array("datid" => $f_datid,"gtabid" => $f_tabid,"fieldid" => $f_fieldid),0,$dublicate)){
		lmb_EndTransaction(0);
	}else{
		lmb_EndTransaction(1);
	}
	
	# link file to dataset / not by versioning because versioning use same relation as old version
	if($f_tabid AND $f_fieldid AND $f_datid AND $dublicate['type'][1] != 'versioning'){
		$verkn = set_verknpf($f_tabid,$f_fieldid,$f_datid,$ufileId,0,0,0);
		$verkn["linkParam"]["LID"] = $LID;
		set_joins($f_tabid,$verkn);
	}
	
}

?>