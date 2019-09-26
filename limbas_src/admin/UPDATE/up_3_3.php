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



$sqlquery = "CREATE TABLE LMB_REVISION (ID SMALLINT NOT NULL, ERSTUSER SMALLINT, ERSTDATUM ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.", REVISION SMALLINT, VERSION SMALLINT, COREV VARCHAR(20),DESCRIPTION ".LMB_DBTYPE_LONG." , PRIMARY KEY (ID) )";
patch_db(1,3,$sqlquery,"adding table LMB_REVISION",3);

$sqlquery = "CREATE TABLE LMB_SYNC_SLAVES (ID SMALLINT NOT NULL, ERSTUSER SMALLINT, ERSTDATUM ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.", NAME VARCHAR(50), SLAVE_URL VARCHAR(100), SLAVE_USERNAME VARCHAR(20), SLAVE_PASS VARCHAR(20) , PRIMARY KEY (ID))";
patch_db(2,3,$sqlquery,"adding table LMB_SYNC_SLAVES",3);

$sqlquery = "CREATE TABLE LMB_SYNC_CACHE (TABID SMALLINT, FIELDID SMALLINT, DATID ".LMB_DBTYPE_FIXED."(16), SLAVE_ID SMALLINT, SLAVE_DATID ".LMB_DBTYPE_FIXED."(16) , TYPE ".LMB_DBTYPE_FIXED."(1), ERSTDATUM ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.")";
patch_db(3,3,$sqlquery,"adding table LMB_SYNC_CACHE",3);

$sqlquery = "CREATE TABLE LMB_SYNC_CONF (ID SMALLINT NOT NULL, TEMPLATE SMALLINT, TABID SMALLINT, FIELDID SMALLINT, MASTER ".LMB_DBTYPE_BOOLEAN.", SLAVE ".LMB_DBTYPE_BOOLEAN.", PRIMARY KEY (ID))";
patch_db(4,3,$sqlquery,"adding table LMB_SYNC_CONF",3);

$sqlquery = "CREATE TABLE LMB_SYNC_TEMPLATE (ID SMALLINT NOT NULL, NAME VARCHAR(50), TABID SMALLINT, SETTING ".LMB_DBTYPE_LONG.", CONFLICT_MODE SMALLINT, PRIMARY KEY (ID))";
patch_db(5,3,$sqlquery,"adding table LMB_SYNC_TEMPLATE",3);

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD DATASYNC ".LMB_DBTYPE_BOOLEAN;
patch_db(6,3,$sqlquery,"adding data sync to table definition",3);

$sqlquery = "ALTER TABLE LMB_RULES_FIELDS ADD LISTEDIT ".LMB_DBTYPE_BOOLEAN;
patch_db(7,3,$sqlquery,"adding listedit to rule definition",3);

$sqlquery = "ALTER TABLE LMB_RULES_FIELDS ADD SPEECHREC ".LMB_DBTYPE_BOOLEAN;
patch_db(8,3,$sqlquery,"adding speechrec to rule definition",3);

$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD POPUPDEFAULT ".LMB_DBTYPE_BOOLEAN;
patch_db(9,3,$sqlquery,"adding popupdefault to rule definition",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,154,'sync_mode','0','0=master, 1=slave',2818)";
patch_db(10,3,$sqlquery,"update umgvar",3);

$sqlquery = "CREATE TABLE LMB_SYNC_LOG (ERSTDATUM  ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.", TYPE VARCHAR(50), TABID SMALLINT,  DATID ".LMB_DBTYPE_FIXED.", FIELDID SMALLINT, ORIGIN SMALLINT, ERRORCODE SMALLINT , MESSAGE varchar(100)) ";
patch_db(11,3,$sqlquery,"adding table LMB_SYNC_LOG",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,155,'sync_port','9004','php socket port',2818)";
patch_db(12,3,$sqlquery,"update umgvar",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,156,'sync_method','socket','synchronization method (socket / soap)',2818)";
patch_db(13,3,$sqlquery,"update umgvar",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,157,'sync_timeout','10','numer of connection attempts',2818)";
patch_db(14,3,$sqlquery,"update umgvar",3);

#$sqlquery = "DELETE FROM LMB_LANG";
$sqlquery = "SELECT 1 FROM LMB_LANG";
patch_db(15,3,$sqlquery,"empty lmb_lang before adding primary key",3);

#$sqlquery = dbq_17(array('LMB_LANG','ID'));
$sqlquery = "SELECT 1 FROM LMB_LANG";
patch_db(16,3,$sqlquery,"adding primary key to lmb_lang",3);



echo "-->";
lmb_rebuildSequences();
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

?>
