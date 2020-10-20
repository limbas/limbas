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


$sqlquery = "ALTER TABLE LMB_FORM_LIST ADD DIMENSION VARCHAR(10)";
patch_db(1,"2.7",$sqlquery,"calendar form size");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'lmb_forms','URL','TITLE'));
patch_db(2,"2.7",$sqlquery,"calendar form size");

$sqlquery = "alter table lmb_conf_tables add lastmodified ".LMB_DBTYPE_TIMESTAMP;
patch_db(3,"2.7",$sqlquery,"last modified for tables");

$sqlquery = "delete from lmb_umgvar where form_name = 'show_ajax_header'";
patch_db(4,"2.7",$sqlquery,"modify umgvars");

$sqlquery = "alter table lmb_userdb add superadmin ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(5,"2.7",$sqlquery,"add superadmin status");

$sqlquery = "update lmb_userdb set superadmin = ".LMB_DBDEF_TRUE." WHERE USER_ID = 1";
patch_db(6,"2.7",$sqlquery,"change superadmin status");

$sqlquery = "alter table lmb_snap add EXT ".LMB_DBTYPE_LONG;
patch_db(7,"2.7",$sqlquery,"add snapshot extension");

$sqlquery = "update lmb_conf_fields set field_name = 'LMLOCK' where field_name = 'LOCK' and tab_id = (select tab_id from lmb_conf_tables where lower(tabelle) = 'ldms_files')";
patch_db(8,"2.7",$sqlquery,"BUGFIX - update lmb_rules_tables");

#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>