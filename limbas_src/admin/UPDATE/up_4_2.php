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


if (file_exists('update.lib')) {
    require_once('update.lib');
} elseif (file_exists($umgvar['path'] . '/admin/UPDATE/update.lib')) {
    require_once($umgvar['pfad'] . '/admin/UPDATE/update.lib');
}

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,1,'postgres_use_fulltextsearch','0','activate postgres fulltextsearch instead of limbas native (0/1)',2995)";
patch_db(1, 2, $sqlquery, 'update umgvar: add postgres_use_fulltextsearch', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,2,'postgres_indize_lang','all','language from pg_ts_config used for indexing: Either fixed (e.g. ''german'') or autodetect (e.g. ''german,english'' or ''all'')',2995)";
patch_db(2, 2, $sqlquery, 'update umgvar: add postgres_indize_lang', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,3,'postgres_headline','1','DMS: use postgres headline (''1'') instead of limbas headline (''0'') to show document context around search results. Yields better results, but is slower',2995)";
patch_db(3, 2, $sqlquery, 'update umgvar: add postgres_headline', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,4,'postgres_strip_tsvector','0','strip word position information from postgres tsvector columns. Reduces column size but disables phrase search (''0''/''1'')',2995)";
patch_db(4, 2, $sqlquery, 'update umgvar: add postgres_strip_tsvector', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,5,'postgres_rank_func','TS_RANK','DMS: the postgres function to order search results by relevance (''TS_RANK''/''TS_RANK_CD''). Leave empty for no order',2995)";
patch_db(5, 2, $sqlquery, 'update umgvar: add postgres_rank_func', 4);

function patch_6(){
    global $db;
    dbq_16(array($GLOBALS['DBA']['DBSCHEMA'],1));
    return true;
}
patch_scr(6,2,"patch_6","rebuild trigger procedure",4);

$sqlquery = dbq_29(array($DBA['DBSCHEMA'],'LMB_REPORT_LIST','CSS','VARCHAR(120)'));
patch_db(7, 2, $sqlquery, 'update lmb_report_list: add css field', 4);

echo "--><br>";

#lmb_rebuildSequences();
$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz");

$releasenotes = '
<li>adding postgresql vector fulltextsearch indexing functionality</li>
<li>trigger option to ignore archived relations</li>
<li>support multitenant for Views</li>
<li>support runtime variables for databases</li>
<li>add boolean display format as checkbox, radio or select</li>
<li>template-System: Check edit rights</li>
<li>template-System: Support for custom css class</li>
<li>template-System: Support for 1:1 relation and numeric function param</li>
<li>template-System: adding css class to tcpdf reports</li>

';

echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new menu functions added! To use it you have to refresh the menu rights! <a onclick=\"nu = open('main_admin.php?action=setup_linkref', 'refresh', 'toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";
echo "<div class=\"alert alert-info showIfPDO\" role=\"alert\"><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new system tablefields added! To use it you have to refresh the table rights! <a onclick=\"nu = open('main_admin.php?action=setup_grusrref&check_all=1','refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\"><u><b>refresh</b></u></a></div>";
