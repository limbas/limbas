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

# change db-include_db.lib file
/*
function patch_0(){
    if (!is_writable("{$DBA['LMPATH']}/inc/include_db.lib")) {
        echo '<div class="alert alert-danger showIfPDO">This update requires a change to the "<b>dependent/inc/include_db.lib</b>" file! You must make the file writable for this process!</div>';
        return;
    } else {
        $include_db = file_get_contents("inc/include_db.lib");
        $include_db = str_replace('require_once("{$DBA[\'LMPATH\']}/lib/db/db_{$DBA[\'DB\']}.lib");', '', $include_db);
        file_put_contents('inc/include_db.lib', $include_db);
    }
}
patch_scr(0,1,"patch_0","change db-include_db.lib file",4);
*/

# Tabelle LMB_CUSTVAR anlegen
$sqlquery = 'CREATE TABLE LMB_CUSTVAR (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    CKEY ' . LMB_DBTYPE_VARCHAR . '(50),
    CVALUE ' . LMB_DBTYPE_VARCHAR . '(200),
    DESCRIPTION ' . LMB_DBTYPE_VARCHAR . ' (240),
    OVERRIDABLE ' . LMB_DBTYPE_BOOLEAN . ',
    ACTIVE ' . LMB_DBTYPE_BOOLEAN . '
)';
patch_db(1,1,$sqlquery,'adding TABLE lmb_custvar',4);



# Tabelle LMB_CUSTVAR_DEPEND anlegen
function patch_2(){
    global $db;
    global $umgvar;
    global $action;

    require_once($umgvar['pfad'] . '/admin/setup/language.lib');
    require_once($umgvar['pfad'] . '/admin/tables/tab.lib');
    $sqlquery = "SELECT TAB_GROUP FROM LMB_CONF_TABLES WHERE UPPER(TABELLE) LIKE 'LDMS_%' LIMIT 1";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $tab_group = 0;
    while(lmbdb_fetch_row($rs)){
        $tab_group = lmbdb_result($rs,"TAB_GROUP");
    }
    if($tab_id = add_tab('LMB_CUSTVAR_DEPEND',$tab_group,'lmb_custvar_depend',0,3,1)){
        # Felder anlegen
        $GLOBALS['lang'][2957] = 'überschreibbar';
        $nfield = extended_fields_custvar();
        add_extended_fields($nfield,$tab_id[0],1);
    }

    return true;
}
patch_scr(2,1,"patch_2","adding TABLE lmb_custvar_depend",4);


$sqlquery = 'ALTER TABLE LMB_CONF_TABLES ADD MULTITENANT '.LMB_DBTYPE_BOOLEAN;
patch_db(3, 1, $sqlquery, 'adding MULTITENANT to lmb_conf_tables table', 4);


$sqlquery = "CREATE TABLE LMB_MULTITENANT (ID ".LMB_DBTYPE_SMALLINT." NOT NULL, NAME VARCHAR(50), MID ".LMB_DBTYPE_INTEGER.", PRIMARY KEY (ID))";
patch_db(4, 1, $sqlquery, 'adding table LMB_MULTITENANT', 4);

$sqlquery = 'ALTER TABLE LMB_USERDB ADD MULTITENANT VARCHAR(50)';
patch_db(5, 1, $sqlquery, 'adding MULTITENANT to lmb_userdb table', 4);


# Tabelle LMB_CURRENCY_RATE anlegen
$sqlquery = 'CREATE TABLE LMB_CURRENCY_RATE (
    ID ' . LMB_DBTYPE_SMALLINT . ' ' . LMB_DBFUNC_PRIMARY_KEY . ',
    CURFROM ' . LMB_DBTYPE_INTEGER . ',
    CURTO ' . LMB_DBTYPE_INTEGER . ',
    RATE ' . LMB_DBTYPE_NUMERIC . '(12, 4),
    RDAY ' . LMB_DBTYPE_DATE . '
)';
patch_db(6,1,$sqlquery,'adding TABLE LMB_CURRENCY_RATE',4);

# Tabellenspalte Unit löschen
$sqlquery = 'ALTER TABLE LMB_CURRENCY DROP COLUMN UNIT';
patch_db(7,1,$sqlquery,'drop COLUMN LMB_CURRENCY.unit',4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,159,'multitenant','0','activate multitenant mode ',2818)";
patch_db(8, 1, $sqlquery, 'update umgvar: add multitenant', 4);

$nid++;
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,160,'multitenant_length','','size of multitenant ID (default 5 Numeric)',2818)";
patch_db(9, 1, $sqlquery, 'update umgvar: add multitenant_length', 4);

$nid++;
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,161,'php_mailer','IsHTML=1;IsSMTP=1;HOST=mail.myserver.com;Port=587;Auth=true;Sec=tls;User=username;From=accountname;Pass=password','parameter of php_mailer',2818)";
patch_db(10, 1, $sqlquery, 'update umgvar: add php_mailer parameter', 4);

$sqlquery = "UPDATE LMB_UMGVAR SET BESCHREIBUNG = 'send password in user notification (image/plain/none)', NORM = '' WHERE FORM_NAME = 'password_as_image'";
patch_db(11, 1, $sqlquery, 'update umgvar: change php_mailer parameter', 4);

$sqlquery = "ALTER TABLE LMB_MULTITENANT ADD SYNCSLAVE SMALLINT";
patch_db(12, 1, $sqlquery, 'update lmb_multitenant: add syncslave', 4);

$sqlquery = "ALTER TABLE LMB_USERDB ADD DLANGUAGE SMALLINT";
patch_db(13, 1, $sqlquery, 'update LMB_USERDB: add DLANGUAGE', 4);

$sqlquery = "CREATE TABLE LMB_CUSTMENU_LIST (ID ".LMB_DBTYPE_SMALLINT." NOT NULL, NAME SMALLINT, TABID SMALLINT, FIELDID SMALLINT, TYPE ".LMB_DBTYPE_SMALLINT.", INACTIVE VARCHAR(150), DISABLED VARCHAR(150), PRIMARY KEY (ID))";
patch_db(14, 1, $sqlquery, 'adding table LMB_CUSTMENU_LIST', 4);

$sqlquery = dbq_4(array($DBA['DBSCHEMA'],'LMB_REMINDER_DATID','LMB_REMINDER','DAT_ID'));
patch_db(15, 1, $sqlquery, 'adding index to lmb_reminder', 4);

$sqlquery = dbq_4(array($DBA['DBSCHEMA'],'LMB_REMINDER_FRIST','LMB_REMINDER','FRIST'));
patch_db(16, 1, $sqlquery, 'adding index to lmb_reminder', 4);


# resize formular parameter
function patch_17(){
    global $db;

    $sqlquery =  dbq_7(array($DBA['DBSCHEMA'],'LMB_FORMS','PARAMETERS','PARAMETERS_'));
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    $sqlquery =  dbq_29(array($DBA['DBSCHEMA'],'LMB_FORMS','PARAMETERS',LMB_DBTYPE_LONG));
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    $sqlquery = "UPDATE LMB_FORMS SET PARAMETERS = PARAMETERS_";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    if($rs) {
        $sqlquery_ = dbq_22(array('LMB_FORMS', 'PARAMETERS_'));
        $rs = lmbdb_exec($db, $sqlquery_) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }else{
        echo "$sqlquery : failed! Check it manualy";
        return false;
    }

    return true;
}
patch_scr(17,1,"patch_17","resize formular parameter",4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,162,'soap_base64','0','use base46 encoding for soap requests',2818)";
patch_db(18, 1, $sqlquery, 'update umgvar: add soap_base64 ', 4);


# rebuild umgvar id
function patch_19()
{
    global $db;
    $sqlquery = "SELECT ID,SORT FROM LMB_UMGVAR ORDER BY ID";
    $rs = lmbdb_exec($db,$sqlquery);

    while(lmbdb_fetch_row($rs)) {
        $res['id'][] = lmbdb_result($rs, 'ID');
        $res['sort'][] = lmbdb_result($rs, 'SORT');
    }
    $bzm = 1;
    foreach($res['id'] as $key => $val){
        $sqlquery = "UPDATE LMB_UMGVAR SET ID = ".($bzm+1000).", SORT = ".($bzm+1000)." WHERE ID = ".$res['id'][$key]." AND SORT = ".$res['sort'][$key];
        $rs = lmbdb_exec($db,$sqlquery);
        $bzm++;
    }

    $sqlquery = "UPDATE LMB_UMGVAR SET ID = (ID - 1000), SORT = (SORT - 1000)";
    $rs = lmbdb_exec($db,$sqlquery);

    return true;

}

patch_scr(19,1,"patch_19","rebuild umgvar id",4);


function patch_20()
{
    global $db;
    global $DBA;

    $sqlquery = "UPDATE LMB_CONF_FIELDS SET VERKNGROUP = '1'";
    $rs = lmbdb_exec($db,$sqlquery);

    dbq_16(array($DBA["DBSCHEMA"],1));
    lmb_rebuildTrigger(1);

    return true;
}

patch_scr(20,1,"patch_20","update VERKNGROUP for trigger calculation",4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,162,'db-custerrors','0','activate customized error parsing &lt;LMB & LMB&gt;',1995)";
patch_db(21, 1, $sqlquery, 'update umgvar: add custerrors ', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,162,'enc_cipher','AES-256-CBC','method used for data encryption',1900)";
patch_db(22, 1, $sqlquery, 'update umgvar: add enc_cipher ', 4);

$sqlquery = dbf_17(array('lmb_sql_favorites', 'lmb_code_favorites'));
patch_db(23, 1, $sqlquery, 'rename lmb_sql_favorites to lmb_code_favorites', 4);

$sqlquery = dbq_29(array($GLOBALS['DBA']['DBSCHEMA'], 'lmb_code_favorites', 'type', LMB_DBTYPE_SMALLINT, "'0'"));
patch_db(24, 1, $sqlquery, 'add type column to lmb_code_favorties', 4);

$sqlquery = dbq_4(array($GLOBALS['DBA']['DBSCHEMA'], 'LMBIND_6091EEA56B33', 'LMB_HISTORY_ACTION', 'USERID'));
patch_db(25, 1, $sqlquery, 'add index to LMB_HISTORY_ACTION', 4);

function patch_26(){
    global $db;
    dbq_16(array($GLOBALS['DBA']['DBSCHEMA'],1));
    return true;
}
patch_scr(26,1,"patch_26","rebuild trigger procedure",4);

echo "--><br>";

#lmb_rebuildSequences();
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz");

$releasenotes = '
<li>adding support for PDO and ODBC simultaneously<br><b><i style="color:red">"odbc_*" functions not longer supported. You have to change them to "lmbdb_*" in your EXTENSIONS</i></b></li>
<li>update "dependent/inc/include_db.lib"<br><i style="color:red">you can remove / uncomment one line includes "require_once(..." at the end of file</i></li>
<li>adding basic multitenant functionality</li>
<li>adding additional menueditors for context and tabelmenus</li>
<li>adding customized parameter table like environment variables</li>
<li>adding SAP HANA Database support</li>
<li>adding currency conversion with validity support and exchange rates</li>
<li>adding native PHP editor in admin tools</li>
';

echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new menu functions added! To use it you have to refresh the menu rights! <a onclick=\"nu = open('main_admin.php?action=setup_linkref', 'refresh', 'toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";
echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new system tablefields added! To use it you have to refresh the table rights! <a onclick=\"nu = open('main_admin.php?action=setup_grusrref&check_all=1','refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";
