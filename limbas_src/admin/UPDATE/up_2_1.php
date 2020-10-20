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


$sqlquery = "DELETE FROM LMB_UMGVAR WHERE NORM = 'currency'";
patch_db(1,"2.1",$sqlquery,"currency not longer needed");

$sqlquery = "ALTER TABLE LMB_RULES_TABLES ADD INDICATOR VARCHAR(250)";
patch_db(2,"2.1",$sqlquery,"new indicator functionality");

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD INDICATOR VARCHAR(250)";
patch_db(3,"2.1",$sqlquery,"new indicator functionality");

$sqlquery = dbf_17(array("LMB_GTAB_STATUS","LMB_GTAB_STATUS_SAVE"));
patch_db(4,"2.1",$sqlquery,"new indicator functionality");

$sqlquery = "CREATE TABLE LMB_UGLST (UGID SMALLINT,TABID SMALLINT,FIELDID SMALLINT,DATID ".LMB_DBTYPE_FIXED.",TYP CHAR(1))";
patch_db(5,"2.1",$sqlquery,"new indicator functionality");

$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD EDITRULE VARCHAR(250)";
patch_db(6,"2.1",$sqlquery,"global editrules");

$sqlquery = "alter table lmb_conf_fields add potency smallint";
patch_db(7,"2.1",$sqlquery,"number potency");

$sqlquery = "insert into lmb_umgvar values (114,126,'allocate_namespace','','range to allocate Table IDs (50-100)',11)";
patch_db(8,"2.1",$sqlquery,"update umgvar");

$sqlquery = "insert into lmb_umgvar values (115,127,'allocate_freeid','0','search free config ID',11)";
patch_db(9,"2.1",$sqlquery,"update umgvar");

$sqlquery = "alter table lmb_conf_fields add inherit_search boolean";
patch_db(10,"2.1",$sqlquery,"extend inherit function");

$sqlquery = "alter table lmb_conf_fields add inherit_eval boolean";
patch_db(11,"2.1",$sqlquery,"extend inherit function");

$sqlquery = "alter table lmb_conf_fields add inherit_filter varchar(250)";
patch_db(12,"2.1",$sqlquery,"extend inherit function");

$sqlquery = "alter table lmb_conf_fields add inherit_group smallint";
patch_db(13,"2.1",$sqlquery,"extend inherit function");

$sqlquery = "alter table lmb_report_list add listmode boolean";
patch_db(14,"2.1",$sqlquery,"new report mode");

$sqlquery = "insert into lmb_umgvar values (116,128,'calendar_viewmode','3','default viewmode (1=day/2=week/3=month)',2)";
patch_db(15,"2.1",$sqlquery,"update umgvar");

$sqlquery = "insert into lmb_umgvar values (116,129,'default_numberformat','2,'','',''.''','default numberformat for currency',0)";
patch_db(16,"2.1",$sqlquery,"update umgvar");

$sqlquery = "update lmb_conf_fields set nformat = '2,'','',''.''' where nformat = '2|,|.'";
patch_db(17,"2.1",$sqlquery,"update number format");


#$sqlquery = "insert into lmb_umgvar values (113,124,'multiframe','default','default multiframe',2)";
#patch_db(6,"2.1",$sqlquery,"update umgvar");
###########################

#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>