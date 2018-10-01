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


$sqlquery = "update lmb_umgvar set norm = 'month',category = 12, beschreibung = 'default viewmode (month/agendaWeek/agendaDay/basicWeek/basicDay)'  where form_name = 'calendar_viewmode'";
patch_db(1,"2.4",$sqlquery,"update umgvar");

$sqlquery = "delete from lmb_umgvar where form_name = 'mailto' or form_name = 'mailfrom' or form_name = 'top_frame'";
patch_db(2,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set form_name = 'default_currency', sort = 127, beschreibung = 'default currency code defined in table lmb_currency'  where form_name = 'currency'";
patch_db(3,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set form_name = 'path', sort = 46, beschreibung = 'absolute path of installation' where form_name = 'pfad'";
patch_db(4,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'Base URL of installation' where form_name = 'url'";
patch_db(5,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'reduced memosize for list (digits)' where form_name = 'memolength'";
patch_db(6,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'locktime of used dataset (minutes)' where form_name = 'inusetime'";
patch_db(7,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'default uloadsize for new users (MB)' where form_name = 'default_uloadsize'";
patch_db(8,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'default usercolor for new users (id of lmb_colorschemes)' where form_name = 'default_usercolor'";
patch_db(9,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'default language for new users (language_id of lmb_lang)' where form_name = 'default_language'";
patch_db(10,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'default dateformat for new users (1=german;2=US;3=fra)' where form_name = 'default_dateformat'";
patch_db(11,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'default count of datasets per page for new users' where form_name = 'default_results'";
patch_db(12,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set sort = 47 where form_name = 'url'";
patch_db(13,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set norm = '1', beschreibung = 'first day of week (0=Sunday, 1=Monday, 2=Tuesday ..)',category = 12,form_name='calendar_firstday' where form_name = 'stuffit'";
patch_db(14,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set norm = '1', beschreibung = 'show weekends',category = 12,form_name='calendar_weekends' where form_name = 'save_upload_copy'";
patch_db(15,"2.4",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set category = 10 where form_name = 'use_gs'";
patch_db(16,"2.4",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,4,'calendar_firsthour','6','determines the first hour that will be visible in the scroll pane',12)";
patch_db(17,"2.4",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,4,'calendar_slotminutes','30','The frequency for displaying time slots, in minutes',12)";
patch_db(18,"2.4",$sqlquery,"update umgvar");

function patch_19(){
	global $db;

	$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD DATETIMESEC ".LMB_DBTYPE_NUMERIC."(1)";
	$rs = odbc_exec($db,$sqlquery);

	$sqlquery = "SELECT ID,DATETIME FROM LMB_CONF_FIELDS";
	$rs = odbc_exec($db,$sqlquery);
	while(odbc_fetch_row($rs)){
		if(odbc_result($rs,"DATETIME")){
			$sqlquery1 = "UPDATE LMB_CONF_FIELDS SET DATETIMESEC = 1 WHERE ID = ".odbc_result($rs,"ID");
			$rs1 = odbc_exec($db,$sqlquery1);
		}
	}
	$sqlquery = "ALTER TABLE LMB_CONF_FIELDS DROP DATETIME";
	$rs = odbc_exec($db,$sqlquery);
	$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_CONF_FIELDS","DATETIMESEC","DATETIME"));
	$rs = odbc_exec($db,$sqlquery);

	if($rs){return true;}
	return false;
}
patch_scr(19,"2.4","patch_19","datetime extension for seconds");

$sqlquery = "alter table lmb_conf_tables add reserveid ".LMB_DBTYPE_BOOLEAN;
patch_db(20,"2.4",$sqlquery,"reserve ID");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],'LMB_CHARTS','TEMPLATE','varchar(255)'));
patch_db(21,"2.4",$sqlquery,"resize graph template");


$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,133,'default_loglevel','1','default loglevel for new users (0=none;1=simple;2=full) ',0)";
patch_db(22,"2.4",$sqlquery,"update umgvar");


$sqlquery = "alter table lmb_tabletree drop poolid";
patch_db(23,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree drop target_poolid";
patch_db(24,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree drop target_relation";
patch_db(25,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree drop target_format";
patch_db(26,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree drop start";
patch_db(27,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add display ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_TRUE;
patch_db(28,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add display_sort smallint";
patch_db(29,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add display_rule varchar(250)";
patch_db(30,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add treeid smallint";
patch_db(31,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add relationid varchar(20)";
patch_db(32,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_tabletree add itemtab smallint";
patch_db(33,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "delete from lmb_tabletree";
patch_db(34,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = dbq_9(array($DBA["DBSCHEMA"],"lmb_tabletree","display",LMB_DBDEF_FALSE));
patch_db(35,"2.4",$sqlquery,"tabletree rebuild");

$sqlquery = "alter table lmb_userdb add tel varchar(25)";
patch_db(36,"2.4",$sqlquery,"extend usertable");

$sqlquery = "alter table lmb_userdb add fax varchar(25)";
patch_db(37,"2.4",$sqlquery,"extend usertable");

$sqlquery = "alter table lmb_userdb add position varchar(50)";
patch_db(38,"2.4",$sqlquery,"extend usertable");

$sqlquery = "alter table lmb_conf_fields add aggregate varchar(10)";
patch_db(39,"2.4",$sqlquery,"aggregate function for fields");

$sqlquery = "alter table lmb_conf_groups add icon varchar(250)";
patch_db(40,"2.4",$sqlquery,"iconset for tablegroups");

$sqlquery = "alter table lmb_conf_fields add scale smallint";
patch_db(41,"2.4",$sqlquery,"skale fielddefinition");

$sqlquery = "alter table lmb_rules_tables add copy ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE;
patch_db(42,"2.4",$sqlquery,"skale fielddefinition");

$sqlquery = "alter table lmb_forms add tab_el smallint";
patch_db(43,"2.4",$sqlquery,"formular subtables");

$sqlquery = "update lmb_forms set tab_el = tab where typ != 'tab'";
patch_db(44,"2.4",$sqlquery,"formular subtables");

function patch_45(){
	global $db;
	lmb_rebuildSequences();
	dbq_16(array($DBA["DBSCHEMA"],1));
	return true;
}
patch_scr(45,"2.4","patch_45","System");

#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}

echo "
	<script language='JavaScript'>
	refresh = open('main_admin.php?action=setup_linkref' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=650');
	</script>
";
?>