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



$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD FULLSEARCH ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(1,4,$sqlquery,"adding full table search",3);


$sqlquery = dbq_15(array($DBA['SCHEMA'],'LMB_CONF_FIELDS','VERKNSEARCH','VARCHAR(50)'));
patch_db(2,4,$sqlquery,"resize VERKNSEARCH",3);
$sqlquery = dbq_15(array($DBA['SCHEMA'],'LMB_CONF_FIELDS','VERKNVIEW','VARCHAR(50)'));
patch_db(3,4,$sqlquery,"resize VERKNVIEW",3);
$sqlquery = dbq_15(array($DBA['SCHEMA'],'LMB_CONF_FIELDS','VERKNFIND','VARCHAR(50)'));
patch_db(4,4,$sqlquery,"resize VERKNFIND",3);


$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,158,'page_title','Limbas','custom title %s',1893)";
patch_db(5,4,$sqlquery,"update umgvar",3);

$sqlquery = "ALTER TABLE LMB_RULES_TABLES ADD VIEW_LFORM ".LMB_DBTYPE_SMALLINT;
patch_db(6,4,$sqlquery,"adding VIEW_LFORM to rule definition",3);

$sqlquery = "CREATE TABLE LMB_SQL_FAVORITES (ID ".LMB_DBTYPE_SMALLINT." NOT NULL, NAME VARCHAR(50) NOT NULL, STATEMENT ".LMB_DBTYPE_LONG." NOT NULL, PRIMARY KEY (ID))";
patch_db(7,4,$sqlquery,"adding table LMB_SQL_FAVORITES",3);


function patch_8(){
    global $db;
    $sqlquery = "UPDATE LMB_FORM_LIST SET CSS = '' WHERE CSS LIKE '/USER/%'";
    $rs = odbc_exec($db,$sqlquery);
    return true;
}
patch_scr(8,4,'patch_8','delete wrong formuar css entries',3);


$sqlquery = "ALTER TABLE LMB_CONF_VIEWS ADD ISVALID ".LMB_DBTYPE_BOOLEAN;
patch_db(9,4,$sqlquery,"adding VIEW_ISVALID to view definition table",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,158,'update_check','1','check for updates',1893)";
patch_db(10,4,$sqlquery,"update umgvar",3);

$sqlquery = "UPDATE LMB_ACTION SET LINK_URL = 'srefresh(this);' WHERE LINK_URL = 'srefresh();'";
patch_db(11,4,$sqlquery,'update session refresh action',3);

$sqlquery = "UPDATE LMB_ACTION SET LINK_URL = 'LmEx_showUploadField();LmEx_divclose();' WHERE LINK_URL = 'LmEx_multiupload(''1'');LmEx_divclose();'";
patch_db(12,4,$sqlquery,'update upload function name',3);

echo "-->";
#lmb_rebuildSequences();
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

// lmb_action -> srefresh(this);

?>
