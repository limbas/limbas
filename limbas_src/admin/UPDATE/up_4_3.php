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


if (file_exists('update.lib')) {
    require_once('update.lib');
} elseif (file_exists($umgvar['path'] . '/admin/UPDATE/update.lib')) {
    require_once($umgvar['pfad'] . '/admin/UPDATE/update.lib');
}



$sqlquery = dbq_15(array($DBA['DBSCHEMA'],'LMB_CONF_FIELDS','MD5TAB','VARCHAR(32)'));
patch_db(1, 3, $sqlquery, 'resize relation field description', 4);

$sqlquery = dbq_29(array($DBA['DBSCHEMA'],'LMB_CONF_TABLES','VALIDITY',LMB_DBTYPE_FIXED.'(1)'));
patch_db(2, 3, $sqlquery, 'update lmb_conf_tables: add validity field', 4);

$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD RELPARAMS ".LMB_DBTYPE_LONG;
patch_db(3, 3,$sqlquery,"add RELPARAMS for LMB_CONF_FIELDS", 4);

$sqlquery = dbq_29(array($DBA['DBSCHEMA'],'LMB_RULES_TABLES','EDITVER',LMB_DBTYPE_BOOLEAN));
patch_db(4, 3, $sqlquery, 'update lmb_rules_tables: add validity field', 4);

$sqlquery = dbq_29(array($DBA['DBSCHEMA'],'LMB_RULES_TABLES','DELVER',LMB_DBTYPE_BOOLEAN));
patch_db(5, 3, $sqlquery, 'update lmb_rules_tables: add validity field', 4);

$sqlquery = dbq_15(array($DBA['DBSCHEMA'],'LMB_FONTS','FAMILY','VARCHAR(100)'));
patch_db(6, 3, $sqlquery, 'resize font family', 4);

$sqlquery = dbq_15(array($DBA['DBSCHEMA'],'LMB_FONTS','NAME','VARCHAR(100)'));
patch_db(7, 3, $sqlquery, 'resize font name', 4);

$sqlquery = "DELETE FROM LMB_UMGVAR WHERE FORM_NAME='searchcount'";
patch_db(8, 3, $sqlquery, 'remove umgvar searchcount', 4);

function patch_9(){
    global $db;
    $sqlquery = "SELECT ID,FORM_NAME FROM LMB_UMGVAR";
    $rs = lmbdb_exec($db, $sqlquery);
    while (lmbdb_fetch_row($rs)) {
        $sqlquery1 = "UPDATE LMB_UMGVAR SET FORM_NAME = '".trim(lmbdb_result($rs, "FORM_NAME"))."' WHERE ID = ".lmbdb_result($rs, "ID");
        $rs1 = lmbdb_exec($db, $sqlquery1);
    }
    return true;
}
patch_scr(9,3,"patch_9","update umgvar: trim all values",4);

$sqlquery = dbq_29(array($DBA['DBSCHEMA'],'LMB_CONF_FIELDS','SEARCHVERSION',LMB_DBTYPE_BOOLEAN));
patch_db(10, 3, $sqlquery, 'update lmb_conf_fields: add searchversion field', 4);

function patch_11(){
    global $db, $lang;

    require_once('admin/tables/tab.lib');
    require_once('admin/setup/language.lib');

    $sqlquery = "SELECT TAB_ID FROM LMB_CONF_TABLES WHERE TYP = 8";
    $rs = lmbdb_exec($db, $sqlquery);
    while (lmbdb_fetch_row($rs)) {
        add_field("FORTABLE",lmbdb_result($rs, "TAB_ID"),2,0,5,0,$lang[3067],$lang[3067],null,null, 1,0,0);
    }
    return true;
}
patch_scr(11,3,"patch_11","Add forTable field in template tables",4);

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD ORDERBY VARCHAR(150)";
patch_db(12, 3, $sqlquery, 'add orderby in lmb_conf_tables', 4);

$sqlquery = "ALTER TABLE LMB_RULES_FIELDS DROP SORT";
patch_db(13, 3, $sqlquery, 'drop sort in lmb_rules_fields', 4);

$sqlquery = "ALTER TABLE LMB_RULES_TABLES ADD ORDERBY VARCHAR(150)";
patch_db(14, 3, $sqlquery, 'add orderby in lmb_rules_tables', 4);

$sqlquery = "ALTER TABLE LMB_FORMS ADD EXTENSION ".LMB_DBTYPE_LONG;
patch_db(15, 3, $sqlquery, 'add extension in lmb_rules_tables', 4);

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD VALIDATE ".LMB_DBTYPE_BOOLEAN;
patch_db(16, 3, $sqlquery, 'add validate in lmb_conf_tables', 4);

$sqlquery = "ALTER TABLE LMB_REPORT_LIST ADD SAVED_TEMPLATE ". LMB_DBTYPE_LONG;
patch_db(17, 3, $sqlquery, 'add validate in lmb_conf_tables', 4);

$sqlquery = "ALTER TABLE LMB_REPORT_LIST ADD PARENT_ID ".LMB_DBTYPE_INTEGER;
patch_db(18, 3, $sqlquery, 'add validate in lmb_conf_tables', 4);

$sqlquery = "ALTER TABLE LMB_RULES_REPFORM ADD EDITABLE ".LMB_DBTYPE_BOOLEAN;
patch_db(19, 3, $sqlquery, 'add validate in lmb_conf_tables', 4);

$sqlquery = "ALTER TABLE LMB_HISTORY_UPDATE ADD LMB_MID ".LMB_DBTYPE_INTEGER;
patch_db(20, 3, $sqlquery, 'add validate in lmb_conf_tables', 4);

$sqlquery = "CREATE TABLE LMB_SYNC_PARAMS (ID ".LMB_DBTYPE_SMALLINT." NOT NULL, TEMPLATE ".LMB_DBTYPE_SMALLINT.", TABID ".LMB_DBTYPE_SMALLINT.", PARAMS VARCHAR(400), PRIMARY KEY (ID))";
patch_db(21, 3, $sqlquery, 'adding table lmb_sync_params', 4);


$sqlquery = 'CREATE TABLE LMB_CRYPTO_PUBLIC_KEYS (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    NAME ' . LMB_DBTYPE_VARCHAR . '(50),
    KEY ' . LMB_DBTYPE_LONG.',
    MID ' . LMB_DBTYPE_SMALLINT . ',
    TYPE ' . LMB_DBTYPE_SMALLINT . ',
    ACTIVE ' . LMB_DBTYPE_BOOLEAN . '
)';
patch_db(22,3,$sqlquery,'adding public key table',4);

$sqlquery = 'CREATE TABLE LMB_CRYPTO_PRIVATE_KEYS (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    NAME ' . LMB_DBTYPE_VARCHAR . '(50),
    KEY ' . LMB_DBTYPE_LONG.',
    MID ' . LMB_DBTYPE_SMALLINT . ',
    TYPE ' . LMB_DBTYPE_SMALLINT . ',
    ACTIVE ' . LMB_DBTYPE_BOOLEAN . '
)';
patch_db(23,3,$sqlquery,'adding private key table',4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,163,'file_encryption','','encrypt files with pgp ',1900)";
patch_db(24, 3, $sqlquery, 'update umgvar: add file_encryption', 4);

$sqlquery = dbq_15(array($DBA['DBSCHEMA'],'LDMS_FILES','SECNAME','VARCHAR(50)'));
patch_db(25, 3, $sqlquery, 'update LDMS_FILES: resize SECNAME', 4);

$sqlquery = "UPDATE LMB_CONF_FIELDS SET FIELD_SIZE = 50 WHERE FIELD_NAME = 'SECNAME' AND TAB_ID = (SELECT TAB_ID FROM LMB_CONF_TABLES WHERE TABELLE = 'LDMS_FILES')";
patch_db(26, 3, $sqlquery, 'update LDMS_FILES: resize SECNAME', 4);

$sqlquery = dbq_4(array($DBA['DBSCHEMA'],'lmbind_'.substr(0,12,md5('LMB_HISTORY_USER_SESSIONID')), 'LMB_HISTORY_USER', 'SESSIONID'));
patch_db(27, 3, $sqlquery, 'create Index to LMB_HISTORY_USER.SESSIONID', 4);

#$sqlquery = "ALTER TABLE LMB_SYNC_CONF ADD SYNC ".LMB_DBTYPE_BOOLEAN;
#patch_db(28, 3, $sqlquery, 'adding field SYNC in LMB_SYNC_CONF', 4);

$sqlquery = "ALTER TABLE LMB_SYNC_SLAVES ADD ACTIVE ".LMB_DBTYPE_BOOLEAN;
patch_db(29, 3, $sqlquery, 'adding field ACTIVE in LMB_SYNC_SLAVES', 4);

$sqlquery = "ALTER TABLE LMB_SYNC_SLAVES ADD RS_USER VARCHAR(20)";
patch_db(30, 3, $sqlquery, 'adding field RS_USER in LMB_SYNC_SLAVES', 4);

$sqlquery = "ALTER TABLE LMB_SYNC_SLAVES ADD RS_PARAMS VARCHAR(100)";
patch_db(31, 3, $sqlquery, 'adding field RS_PARAMS in LMB_SYNC_SLAVES', 4);

$sqlquery = "ALTER TABLE LMB_SYNC_SLAVES ADD RS_PATH VARCHAR(100)";
patch_db(32, 3, $sqlquery, 'adding field RS_PATH in LMB_SYNC_SLAVES', 4);

function patch_33(){
    global $db;
    $sqlquery = 'ALTER TABLE LMB_CONF_TABLES DROP COLUMN DATASYNC';
    $rs = lmbdb_exec($db, $sqlquery);
    $sqlquery = 'ALTER TABLE LMB_CONF_TABLES ADD COLUMN DATASYNC '.LMB_DBTYPE_FIXED.'(1)';
    $rs = lmbdb_exec($db, $sqlquery);
    return true;
}
patch_scr(33,3,"patch_33","adding field DATASYNC in LMB_CONF_TABLES",4);

$sqlquery = 'CREATE TABLE LMB_COLORVARS (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    SCHEMA ' . LMB_DBTYPE_SMALLINT . ',
    NAME ' . LMB_DBTYPE_VARCHAR.'(100),
    VALUE ' . LMB_DBTYPE_VARCHAR . '(100)
)';
patch_db(34, 3, $sqlquery, 'adding LMB_COLORVARS table', 4);

#$sqlquery = "ALTER TABLE LMB_MULTITENANT ADD COLUMN PUBLIC_KEY ".LMB_DBTYPE_SMALLINT;
#patch_db(23, 3, $sqlquery, 'add public_key in lmb_crypto_keys', 4);

#UPDATE LDMS_FILES.SECNAME -> varchar(30)

#create index lmb_history_user_sessionid ON lmb_history_user(sessionid)

//create new assets directory
$cssdir = $umgvar['path'].'/assets/css/';
if (!is_dir($cssdir) AND !mkdir($cssdir, 0755, true)) {
    echo 'Directory does not exists and could not be created (' . $cssdir . ')';
    return true;
}
patch_db(35, 3, false, 'create new assets directory', 4);

$sqlquery = 'ALTER TABLE LMB_SELECT_W ADD HIDETAG BOOLEAN';
patch_db(36, 3, $sqlquery, 'adding HIDETAG to atrribute_w table', 4);

echo "--><br>";

#lmb_rebuildSequences();
$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz");


$releasenotes = '
';

echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new menu functions added! To use it you have to refresh the menu rights! <a onclick=\"nu = open('main_admin.php?action=setup_linkref', 'refresh', 'toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";
echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new system tablefields added! To use it you have to refresh the table rights! <a onclick=\"nu = open('main_admin.php?action=setup_grusrref&check_all=1','refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";