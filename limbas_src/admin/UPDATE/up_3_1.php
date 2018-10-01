<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}




$sqlquery = "ALTER TABLE LDMS_STRUCTURE ADD PATH VARCHAR(160)";
patch_db(1,1,$sqlquery,'add storage-path for ldms_structure',3);

$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 'pdf,text,txt,html' WHERE FORM_NAME = 'indize_filetype'";
patch_db(2,1,$sqlquery,'update umgvar',3);

function patch_3(){
	@mkdir($umgvar['path'].'/UPLOAD/STORAGE');
	return true;
}
patch_scr(3,1,'patch_3','add storage-path UPLOAD/STORAGE',3);

$sqlquery = "UPDATE LMB_USERDB SET ID = USER_ID";
patch_db(4,1,$sqlquery,'update usertable with primary key',3);


function patch_5(){
    global $db;
    
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','PADDING_LEFT'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','padding_top'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','padding_right'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','padding_bottom'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','legend_x'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','legend_y'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    $sqlquery = dbq_9(array($DBA['SCHEMA'],'LMB_CHART_LIST','pie_radius'),'NULL');
    $rs = odbc_exec($db,$sqlquery);
    
    return true;
}
patch_scr(5,1,'patch_5','mofify default values LMB_CHART_LIST',3);




echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types_depend.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>