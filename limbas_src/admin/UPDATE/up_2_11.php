<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}



$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD MULTILANG ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(1,11,$sqlquery,'multilanguage support',2);

function patch_2(){
	global $db;
	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,153,'multi_language','1','languages to translate (1,2,3)',1896)";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(2,11,'patch_2','multilanguage support',2);

function patch_3(){
	global $db;
	global $umgvar;
	
	require_once("admin/setup/language.lib");
	
	$sqlquery = "SELECT ID,NAME FROM LMB_REMINDER_LIST";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	while(odbc_fetch_row($rs)) {
		$id = odbc_result($rs, "ID");
		$name = odbc_result($rs, "NAME");
    	$name_id = lang_add($umgvar['default_language'],4,"Reminder: ".$name,$name,"_DEPEND");
	    $sqlquery1 = "UPDATE LMB_REMINDER_LIST SET NAME = '".parse_db_int($name_id)."' WHERE ID = $id";
	    $rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
		
	}
	return true;
}
patch_scr(3,11,'patch_3','reminder language support',2);


$sqlquery = "INSERT INTO LMB_FIELD_TYPES VALUES(23,1,33,2903,'VARCHAR',180,'^.{0,xx}$',27,2904,2,24,".LMB_DBDEF_FALSE.")";
patch_db(4,11,$sqlquery,'fieldtype picture',2);

#echo "-->";
#$impsystables = array("lmb_lang.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>