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


$sqlquery = "CREATE TABLE LMB_REMINDER_LIST (ID SMALLINT NOT NULL, ERSTUSER SMALLINT, ERSTDATUM ".LMB_DBTYPE_TIMESTAMP.", NAME VARCHAR(50), TAB_ID SMALLINT,FORMD_ID SMALLINT, FORML_ID SMALLINT, GROUPBASED ".LMB_DBTYPE_BOOLEAN.", PRIMARY KEY (ID) )";
patch_db(1,"2.5",$sqlquery,"extended reminder");

$sqlquery = dbf_17(array("LMB_GTAB_USERDAT","LMB_REMINDER"));
patch_db(2,"2.5",$sqlquery,"extended reminder");

$sqlquery = "ALTER TABLE LMB_REMINDER ADD CATEGORY SMALLINT";
patch_db(3,"2.5",$sqlquery,"extended reminder");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'ldms_rules','lock','lmlock'));
patch_db(4,"2.5",$sqlquery,"mysql - update ldms_rules");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'lmb_rules_tables','lock','lmlock'));
patch_db(5,"2.5",$sqlquery,"mysql - update lmb_rules_tables");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'lmb_userdb','lock','lmlock'));
patch_db(6,"2.5",$sqlquery,"mysql - update lmb_userdb");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'ldms_files','lock','lmlock'));
patch_db(7,"2.5",$sqlquery,"mysql - update ldms_files");

$sqlquery = "CREATE TABLE LMB_WFL (ID SMALLINT NOT NULL, name varchar(50), descr varchar(250), startid smallint, params ".LMB_DBTYPE_LONG." , PRIMARY KEY (ID) )";
patch_db(8,"2.5",$sqlquery,"workflow");

$sqlquery = "CREATE TABLE LMB_WFL_TASK (ID SMALLINT NOT NULL, name varchar(50), descr varchar(250), wfl_id smallint, tab_id smallint, params ".LMB_DBTYPE_LONG.", SORT SMALLINT, TASKS_USABLE VARCHAR(50), PRIMARY KEY (ID) )";
patch_db(9,"2.5",$sqlquery,"workflow");

$sqlquery = "CREATE TABLE LMB_WFL_INST (ID SMALLINT NOT NULL, tab_id smallint, dat_id ".LMB_DBTYPE_FIXED.", task_id smallint, wfl_id smallint, PRIMARY KEY (ID) )";
patch_db(10,"2.5",$sqlquery,"workflow");

$sqlquery = "ALTER TABLE LMB_REMINDER ADD GROUP_ID SMALLINT";
patch_db(11,"2.5",$sqlquery,"extended reminder");

$sqlquery = "ALTER TABLE LMB_REMINDER ADD WFL_INST ".LMB_DBTYPE_FIXED;
patch_db(12,"2.5",$sqlquery,"workflow");

$sqlquery = "create table lmb_wfl_history (ID ".LMB_DBTYPE_FIXED." NOT NULL, erstdatum ".LMB_DBTYPE_TIMESTAMP.", INST_ID ".LMB_DBTYPE_FIXED." , TASK_ID smallint, USER_ID smallint , WFL_ID smallint, TAB_ID smallint, DAT_ID ".LMB_DBTYPE_FIXED.", PRIMARY KEY (ID))";
patch_db(13,"2.5",$sqlquery,"workflow");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,135,'menurefresh','5','refresh of multiframe content in (minutes)',3)";
patch_db(14,"2.5",$sqlquery,"update umgvar");

$sqlquery = "UPDATE LMB_REMINDER SET CATEGORY = 0";
patch_db(15,"2.5",$sqlquery,"extended reminder");

$sqlquery = dbq_9(array($DBA["DBSCHEMA"],'LMB_REMINDER','CATEGORY',0));
patch_db(16,"2.5",$sqlquery,"extended reminder");

function patch_17(){
	global $db;
	lmb_rebuildSequences();
	dbq_16(array($DBA["DBSCHEMA"],1));
	return true;
}
patch_scr(17,"2.5","patch_17","System");

$sqlquery = "update lmb_conf_fields set field_name = 'LMLOCK' where field_name = 'LOCK' and tab_id = (select tab_id from lmb_conf_tables where lower(tabelle) = 'ldms_files')";
patch_db(18,"2.5",$sqlquery,"mysql - update lmb_rules_tables");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],'LMB_GTAB_ROWSIZE','ROW_SIZE','VARCHAR(2000)'));
patch_db(19,"2.5",$sqlquery,"mysql - update lmb_rules_tables");

$sqlquery = "ALTER TABLE LMB_CONF_TABLES DROP QUEST";
patch_db(20,"2.5",$sqlquery,"mysql - update lmb_conf_tables");

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD EVENT VARCHAR(500)";
patch_db(21,"2.5",$sqlquery,"mysql - update lmb_conf_tables");

#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>