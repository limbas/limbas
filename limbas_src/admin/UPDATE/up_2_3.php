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

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LDMS_RULES","VIEW","LMVIEW"));
patch_db(1,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LDMS_RULES","ADD","LMADD"));
patch_db(2,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_CONF_FIELDS","TRIGGER","LMTRIGGER"));
patch_db(3,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_CONF_TABLES","TRIGGER","LMTRIGGER"));
patch_db(4,"2.3",$sqlquery,"mssql");

$sqlquery = "DROP TABLE LMB_ERRORS";
patch_db(5,"2.3",$sqlquery,"error-handling");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_LANG","FILE","LMFILE"));
patch_db(6,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_LANG_DEPEND","FILE","LMFILE"));
patch_db(7,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_FIELDS","TRIGGER","LMTRIGGER"));
patch_db(8,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_FIELDS","VIEW","LMVIEW"));
patch_db(9,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_REPFORM","VIEW","LMVIEW"));
patch_db(10,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_TABLES","VIEW","LMVIEW"));
patch_db(11,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_TABLES","ADD","LMADD"));
patch_db(12,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_RULES_TABLES","TRIGGER","LMTRIGGER"));
patch_db(13,"2.3",$sqlquery,"mssql");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_FIELD_TYPES","RULE","LMRULE"));
patch_db(14,"2.3",$sqlquery,"mssql");

$sqlquery = "alter table lmb_conf_fields add field_size smallint";
patch_db(15,"2.3",$sqlquery,"usable field size");

$sqlquery = "update lmb_conf_fields set field_size = (select lmb_field_types.size from lmb_field_types where lmb_field_types.data_type = lmb_conf_fields.data_type) where data_type in (1,2,3,4,5,6,7,8,9,10,16,17,19,21,33,28,29,42,49,30)";
patch_db(16,"2.3",$sqlquery,"usable field size");

$sqlquery = "update lmb_conf_fields set data_type = 16 where data_type in (17,33)";
patch_db(17,"2.3",$sqlquery,"usable field size");

$sqlquery = "update lmb_conf_fields set data_type = 1 where data_type in (2,3,4,5,6,7,8)";
patch_db(18,"2.3",$sqlquery,"usable field size");

$sqlquery = "update lmb_conf_fields set data_type = 10 where data_type in (9)";
patch_db(19,"2.3",$sqlquery,"usable field size");

$sqlquery = "alter table lmb_conf_views add viewtype smallint";
patch_db(20,"2.3",$sqlquery,"view extensions");

$sqlquery = "update lmb_umgvar set form_name = 'default_fieldwidth',norm = '100', beschreibung = 'default width of fields in px if not set'  where form_name = 'usetablebody'";
patch_db(21,"2.3",$sqlquery,"update umgvar");

$sqlquery = "DROP TABLE \"".dbf_4("lmb_snap_archive")."\"";
patch_db(22,"2.3",$sqlquery,"drop deprecated snapshot table");

$sqlquery = "alter table lmb_snap_shared add edit ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE;
patch_db(23,"2.3",$sqlquery,"extend snapshot");

$sqlquery = "alter table lmb_snap_shared add del ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE;
patch_db(24,"2.3",$sqlquery,"extend snapshot");

$sqlquery = "alter table lmb_userdb add m_setting ".LMB_DBTYPE_LONG;
patch_db(25,"2.3",$sqlquery,"save frame settings");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],dbf_4("lmb_forms"),dbf_4("parameters"),"varchar(500)"));
patch_db(26,"2.3",$sqlquery,"extend form parameter");

$sqlquery = "ALTER TABLE LMB_CONF_TABLES ADD AJAXPOST ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE;
patch_db(27,"2.3",$sqlquery,"extend form parameter");

$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD COLLREPLACE ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE;
patch_db(28,"2.3",$sqlquery,"extend collective replace");

$sqlquery = "alter table lmb_conf_tables add numrowcalc smallint";
patch_db(29,"2.3",$sqlquery,"numrow calculation");

$sqlquery = "delete from lmb_umgvar where form_name = 'showcount'";
patch_db(30,"2.3",$sqlquery,"numrow calculation");

function patch_31(){
	global $db;

	$NEXTKEYID = next_db_id("LMB_REPORTS","KEYID");
	
	$sqlquery = "SELECT ID,BERICHT_ID,TAB,TAB_SIZE FROM LMB_REPORTS WHERE TYP = 'tab'";
	$rs = lmbdb_exec($db,$sqlquery);

	while(lmbdb_fetch_row($rs)){
		$tabid = lmbdb_result($rs,"TAB");
		$report_id = lmbdb_result($rs,"BERICHT_ID");
		$tab_size = explode(";",lmbdb_result($rs,"TAB_SIZE"));
		
		$sqlquery1 = "SELECT MAX(ID) AS NEXTID FROM LMB_REPORTS WHERE BERICHT_ID = $report_id";
		$rs1 = lmbdb_exec($db,$sqlquery1);
		$NEXTID = lmbdb_result($rs1,"NEXTID") + 1;
		
		$sqlquery1 = "SELECT DISTINCT TAB_EL_COL_SIZE,TAB_EL_COL FROM LMB_REPORTS WHERE BERICHT_ID = $report_id AND TAB = $tabid AND TAB_EL_ROW > 0 ORDER BY TAB_EL_COL";
		$rs1 = lmbdb_exec($db,$sqlquery1);
		$oldsize = array();
		$oldsize[] = 0;
		while(lmbdb_fetch_row($rs1)){
			$oldsize[] = lmbdb_result($rs1,"TAB_EL_COL_SIZE");
		}
		
		for($ti=1;$ti<=$tab_size[0];$ti++){
			if($oldsize[$ti]){$size = $oldsize[$ti];}else{$size = 60;}
			$sqlquery2 = "SELECT ID FROM LMB_REPORTS WHERE BERICHT_ID = $report_id AND TAB = $tabid AND TAB_EL_ROW = 0 AND TAB_EL_COL = $ti AND TYP = 'tabhead'";
			$rs2 = lmbdb_exec($db,$sqlquery2);
			if(!lmbdb_fetch_row($rs2, 1)){
				$sqlquery3 = "INSERT INTO LMB_REPORTS (KEYID,ID,ERSTUSER,BERICHT_ID,TYP,TAB,TAB_EL_ROW,TAB_EL_COL,TAB_EL_COL_SIZE) VALUES ($NEXTKEYID,$NEXTID,1,$report_id,'tabhead',$tabid,0,$ti,$size)";
				$rs3 = lmbdb_exec($db,$sqlquery3);
				$NEXTKEYID++;
				$NEXTID++;
			}
		}
	}
	
	return true;
}
patch_scr(31,"2.3","patch_31","report tables");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],dbf_4("LMB_TRIGGER"),dbf_4("TABLE_NAME"),"varchar(50)"));
patch_db(32,"2.3",$sqlquery,"resize trigger table");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],dbf_4("LMB_REPORTS"),dbf_4("ID"),dbf_4("EL_ID")));
patch_db(33,"2.3",$sqlquery,"new KEY-ID for report table");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],dbf_4("LMB_REPORTS"),dbf_4("KEYID"),dbf_4("ID")));
patch_db(34,"2.3",$sqlquery,"new KEY-ID for report table");

function patch_35(){
	lmb_rebuildSequences();
	return true;
}
patch_scr(35,"2.3","patch_35","sequenz tables");

$sqlquery = "INSERT INTO LMB_COLORSCHEMES VALUES ((SELECT MAX(ID)+1 FROM LMB_COLORSCHEMES),'basic (skalar)','#D6D6CE','#BBBBBB','#C0C0C0','#BBBBBB','#C0C0C0','#C0C0C0','#FBE16B','#EEEEEE','0','#EEEEEE','#FBE16B','#E6E6E6','#909090','#FFFFFF','#F5F5F5')";
patch_db(36,"2.3",$sqlquery,"new COLORSCHEMES for skalar");

#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>