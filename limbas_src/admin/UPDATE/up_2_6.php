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


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}


$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'LMB_REPORT_LIST','OOTEMPLATE','ODT_TEMPLATE'));
patch_db(1,"2.6",$sqlquery,"odt template");

$sqlquery = "ALTER TABLE LMB_REPORT_LIST ADD ODS_TEMPLATE SMALLINT";
patch_db(2,"2.6",$sqlquery,"ods template");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,1,'report_calc_output','xls','report output format of type calc (xls/xlsx/CSV)',10)";
patch_db(3,"2.6",$sqlquery,"update umgvar");

$sqlquery = "alter table lmb_reminder_list add sort smallint";
patch_db(4,"2.6",$sqlquery,"reminder sort");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,128,'wsdl_cache','0','use wsdl_cache',11)";
patch_db(5,"2.6",$sqlquery,"update umgvar");

#$nid = next_db_id("lmb_umgvar","ID");
#$sqlquery = "insert into lmb_umgvar values($nid,1,'report_calc_output','Excel2007','report output format of type calc (Excel2007/Excel5/CSV)',5)";
#patch_db(4,"2.6",$sqlquery,"update umgvar");

#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>