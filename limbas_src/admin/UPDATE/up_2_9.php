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

$sqlquery = "alter table lmb_conf_fields add verknparams smallint";
patch_db(1,"2.9",$sqlquery,"adding relation parameter");

$sqlquery = "alter table lmb_conf_tables add keyfield varchar(15) default 'ID'";
patch_db(2,"2.9",$sqlquery,"adding userdefined keyfield for tables");

$sqlquery = "update lmb_conf_tables set keyfield = 'ID'";
patch_db(3,"2.9",$sqlquery,"set keyfield to ID");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'lmb_rules_fields','OPTION','FIELDOPTION'));
patch_db(4,"2.9",$sqlquery,"rename option in lmb_rules_fields");

$sqlquery = "alter table lmb_conf_fields add listing_cut varchar(15)";
patch_db(5,"2.9",$sqlquery,"adding field parameter listing_cut");

$sqlquery = "alter table lmb_conf_fields add listing_viewmode ".LMB_DBTYPE_NUMERIC.'(1)';
patch_db(6,"2.9",$sqlquery,"adding field parameter listing_viewmode");

function patch_7(){
	global $db;

	$sqlquery = "UPDATE LMB_UMGVAR SET category=1893 WHERE category=0";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1894 WHERE category=1";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1895 WHERE category=2";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1896 WHERE category=3";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1898 WHERE category=4";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=2700 WHERE category=12";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1899 WHERE category=5";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1900 WHERE category=6";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=2818 WHERE category=11";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=1995 WHERE category=7";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=2819 WHERE category=8";
	$rs = lmbdb_exec($db,$sqlquery);
	
	$sqlquery = "UPDATE LMB_UMGVAR SET category=2820 WHERE category=10";
	$rs = lmbdb_exec($db,$sqlquery);	
	
	return true;

}
patch_scr(7,"2.9","patch_7","umgvar with flexible categories");

$sqlquery = "alter table lmb_conf_tables add params1 ".LMB_DBTYPE_NUMERIC.'(5)';
patch_db(8,"2.9",$sqlquery,"adding table params");

$sqlquery = "alter table lmb_conf_tables add params2 ".LMB_DBTYPE_VARCHAR."(500)";
patch_db(9,"2.9",$sqlquery,"adding table params");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,150,'LDAP_useSSL','0','use LDAP over SSL',1900)";
patch_db(10,"2.9",$sqlquery,"update umgvar");

$sqlquery = "alter table lmb_conf_fields add verkntree ".LMB_DBTYPE_VARCHAR."(32)";
patch_db(11,"2.9",$sqlquery,"tree-relation");


#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>