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


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}



function patch_1(){
	global $db;
	$nid = next_db_id("lmb_colorschemes");
	$sqlquery = "insert into lmb_colorschemes values($nid,'basic (comet)','#d0d0d0','#444444','#C0C0C0','#BBBBBB','#C0C0C0','#C0C0C0','#97C5AB','#EEEEEE',".LMB_DBDEF_TRUE.",'#EEEEEE','#eaf3ee','#FDFEFD','#909090','#FFFFFF','#F5F5F5')";
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(0,0,'patch_1','',3);



#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>