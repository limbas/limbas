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

# dms cloud updates -> moved to version 3.6

$sqlquery = "update lmb_umgvar set beschreibung = 'check for updates (0/1/auto)' where form_name = 'update_check'";
patch_db(7,5,$sqlquery,'update umgvar description for update_check',3);

$sqlquery = "update lmb_umgvar set norm = 'auto' where form_name = 'update_check' and norm = '1'";
patch_db(8,5,$sqlquery,'update umgvar default value for update_check',3);

$sqlquery = '
  CREATE TABLE LMB_PRINTERS (
    ID ' . LMB_DBTYPE_SMALLINT . ' NOT NULL ' . LMB_DBFUNC_PRIMARY_KEY . ',
    NAME ' . LMB_DBTYPE_VARCHAR . '(127) NOT NULL,
    SYSNAME ' . LMB_DBTYPE_VARCHAR . '(127) NOT NULL,
    CONFIG ' . LMB_DBTYPE_LONG . ',
    DEF ' . LMB_DBTYPE_BOOLEAN . '
  )';
patch_db(9,5,$sqlquery,'add lmb_printers table',3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,1,'lpstat_params','','options for command lpstat',2932)";
patch_db(10,5,$sqlquery,"update umgvar: add lpstat_params",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,2,'lpoptions_params','','options for command lpoptions',2932)";
patch_db(11,5,$sqlquery,"update umgvar: add lpoptions_params",3);

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,3,'lp_params','','options for command lp',2932)";
patch_db(12,5,$sqlquery,"update umgvar: add lp_params",3);

echo "-->";
#lmb_rebuildSequences();
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

// lmb_action -> srefresh(this);



?>
