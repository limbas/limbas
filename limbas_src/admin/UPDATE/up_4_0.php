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

$sqlquery = 'ALTER TABLE LMB_CONF_VIEWS ADD SETMANUALLY ' . LMB_DBTYPE_BOOLEAN;
patch_db(1, 0, $sqlquery, 'adding VIEW_SETMANUALLY to view definition table', 4);



// update color table


function patch_2()
{
    global $db;

    $sqlquery = "SELECT ID,WERT FROM LMB_USER_COLORS";
    $rs = lmbdb_exec($db, $sqlquery);
    while (lmbdb_fetch_row($rs)) {
        $color = '#'.ltrim(lmbdb_result($rs, "WERT"),'#');
        $id = lmbdb_result($rs, "ID");

        $sqlquery1 = "UPDATE LMB_USER_COLORS SET WERT = '".$color."' WHERE ID = $id";
        $rs1 = lmbdb_exec($db, $sqlquery1);
    }
    return true;
}
patch_scr(2,0,"patch_2","update colors",4);




$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_W ADD COLOR VARCHAR(7)';
patch_db(3, 0, $sqlquery, 'adding COLOR to atrribute_w table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_W ADD ATTRPOOL SMALLINT';
patch_db(4, 0, $sqlquery, 'adding ATTRPOOL to atrribute_w table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_W ADD HIDETAG BOOLEAN';
patch_db(5, 0, $sqlquery, 'adding HIDETAG to atrribute_w table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_P ADD TAGMODE BOOLEAN';
patch_db(6, 0, $sqlquery, 'adding TAGMODE to LMB_ATTRIBUTE_P table', 4);

$sqlquery = 'ALTER TABLE LMB_SELECT_P ADD TAGMODE BOOLEAN';
patch_db(7, 0, $sqlquery, 'adding TAGMODE to LMB_SELECT_P table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_P ADD MULTIMODE BOOLEAN';
patch_db(8, 0, $sqlquery, 'adding MULTIMODE to LMB_ATTRIBUTE_P table', 4);

$sqlquery = 'ALTER TABLE LMB_SELECT_P ADD MULTIMODE BOOLEAN';
patch_db(9, 0, $sqlquery, 'adding MULTIMODE to LMB_SELECT_P table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_W ADD MANDATORY BOOLEAN';
patch_db(10, 0, $sqlquery, 'adding MANDATORY to LMB_ATTRIBUTE_W table', 4);

$sqlquery = 'ALTER TABLE LMB_SELECT_W ADD COLOR VARCHAR(7)';
patch_db(11, 0, $sqlquery, 'adding COLOR to LMB_SELECT_W table', 4);

$sqlquery = 'ALTER TABLE LMB_ATTRIBUTE_D ADD VALUE_DATE '.LMB_DBTYPE_TIMESTAMP;
patch_db(12, 0, $sqlquery, 'adding COLOR to LMB_SELECT_W table', 4);

$sqlquery = 'ALTER TABLE LMB_CRONTAB ADD JOB_USER SMALLINT';
patch_db(13, 0, $sqlquery, 'adding JOB_USER To Table LMB_CRONTAB', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR values($nid,150,'date_max_two_digit_year_addend','20','Amount of years (e.g. 20) to add to the current year (e.g. 2019 -> 2039) to<br>determine the timespan (1940-2039) that will be used to calculate the<br>full year when entered as the last two digits (e.g. 39 -> 2039, but 40 -> 1940)',1893)";
patch_db(14, 0, $sqlquery, 'update umgvar: add date_max_two_digit_year_addend', 4);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,150,'restpath','main_rest.php','different url path to REST api. (e.g. mylimbas.com/api -> api). Default is main_rest.php ',1893)";
patch_db(15, 0, $sqlquery, 'update umgvar: add restpath', 4);


echo "-->";
lmb_rebuildSequences();
$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

echo "<h5><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new menu functions added! To use it you have to refresh the menu rights! <a onclick=\"nu = open('main_admin.php?action=setup_linkref', 'refresh', 'toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\">refresh</a></h5>";
echo "<h5><i class=\"lmbMenuItemImage lmb-icon lmb-exclamation-triangle\"></i>new system tablefields added! To use it you have to refresh the table rights! <a onclick=\"nu = open('main_admin.php?action=setup_grusrref&check_all=1','refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400');\">refresh</a></h5>";


?>
