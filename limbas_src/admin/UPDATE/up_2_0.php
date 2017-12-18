<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */


if(file_exists("update.lib")){require_once("update.lib");}
elseif(file_exists($umgvar["pfad"]."/admin/UPDATE/update.lib")){require_once($umgvar["pfad"]."/admin/UPDATE/update.lib");}


$sqlquery = "alter table lmb_conf_fields add defaultvalue varchar(25)";
patch_db(1,"2.0",$sqlquery,"defaultvalue");

$sqlquery = "alter table lmb_trigger add name varchar(25)";
patch_db(2,"2.0",$sqlquery,"trigger");

$sqlquery = "alter table lmb_conf_fields add hasrecverkn smallint";
patch_db(3,"2.0",$sqlquery,"trigger");

$sqlquery = "alter table lmb_trigger add dbvendor varchar(15)";
patch_db(4,"2.0",$sqlquery,"trigger");

$sqlquery = "create table lmb_tabletree (id smallint, erstuser smallint, erstdatum ".LMB_DBTYPE_TIMESTAMP." default ".LMB_DBDEF_TIMESTAMP.",poolid smallint, tabid smallint,target_poolid smallint, target_relation smallint, target_formid smallint,target_format ".LMB_DBTYPE_FIXED."(1),display_field smallint,display_icon varchar(30), display_title smallint,start boolean,poolname varchar(50), ".LMB_DBFUNC_PRIMARY_KEY."(ID))";
patch_db(5,"2.0",$sqlquery,"table tree");

$sqlquery = "alter table lmb_conf_fields add quicksearch boolean default false";
patch_db(6,"2.0",$sqlquery,"quicksearch");

$sqlquery = "alter table lmb_tabletree add TARGET_SNAP smallint";
patch_db(7,"2.0",$sqlquery,"table tree");

function patch_8(){
	global $db;
	
	return true;

	$sqlquery = "ALTER TABLE LMB_CONF_FIELDS ADD RELEXT2 ".LMB_DBTYPE_LONG;
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	
	$sqlquery1 = "UPDATE LMB_CONF_FIELDS SET RELEXT2 = RELEXT";
	$rs1 = odbc_exec($db,$sqlquery1);
	
	$sqlquery = "ALTER TABLE LMB_CONF_FIELDS DROP RELEXT";
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs OR !$rs1){return false;}
	
	$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"LMB_CONF_FIELDS","RELEXT2","RELEXT"));
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	
	return true;
}

patch_scr(8,"2.0","patch_8","verknextensions");

$sqlquery = "alter table lmb_userdb add ugtab ".LMB_DBTYPE_LONG;
patch_db(9,"2.0",$sqlquery,"session extension for userdb");


function patch_10(){
	global $db;

	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '18' where form_name = 'default_usercolor'";
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = 'manta' where form_name = 'default_layout'";
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '11' where form_name = 'fontsize'";
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '0' where form_name = 'admin_mode'";
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
	
	return true;
}

patch_scr(10,"2.0","patch_10","umgvar");

$sqlquery = "alter table lmb_userdb drop e_setting";
patch_db(11,"2.0",$sqlquery,"mailsystem");

$sqlquery = "alter table lmb_userdb add e_setting varchar(1000)";
patch_db(12,"2.0",$sqlquery,"mailsystem");

$sqlquery = "alter table lmb_select_w add level ".LMB_DBTYPE_INTEGER." DEFAULT 0";
patch_db(13,"2.0",$sqlquery,"fieldtype subselect");

$sqlquery =  dbq_15(array($DBA["DBSCHEMA"],"lmb_userdb","lock_txt","VARCHAR(500)"));
patch_db(14,"2.0",$sqlquery,"lock text");

$sqlquery = "alter table lmb_conf_fields add viewrule varchar(250)";
patch_db(15,"2.0",$sqlquery,"viewrule");

$sqlquery = "alter table lmb_select_w add haslevel ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(16,"2.0",$sqlquery,"fieldtype subselect");

$sqlquery = "alter table lmb_attribute_w add level ".LMB_DBTYPE_INTEGER." DEFAULT 0";
patch_db(17,"2.0",$sqlquery,"fieldtype subselect");

$sqlquery = "alter table lmb_attribute_w add haslevel ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(18,"2.0",$sqlquery,"fieldtype subselect");

$sqlquery = "update lmb_select_w set level = 0";
patch_db(19,"2.0",$sqlquery,"fieldtype subselect");

$sqlquery = "alter table lmb_report_list add menuextension VARCHAR(160)";
patch_db(20,"2.0",$sqlquery,"report list");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],"LMB_CONF_FIELDS","NFORMAT","VARCHAR(30)"));
patch_db(21,"2.0",$sqlquery,"conf fields");

$sqlquery = "alter table lmb_rules_repform add hidden ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(22,"2.0",$sqlquery,"report form hidden");

$sqlquery = "alter table lmb_rules_tables add HIRAPRIVILEGE ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_FALSE;
patch_db(23,"2.0",$sqlquery,"hierarchic dataset userrules");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],"lmb_conf_tables","tabelle","varchar(32)"));
patch_db(24,"2.0",$sqlquery,"resize field tabelle");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],"lmb_userdb","time_zone","varchar(20)"));
patch_db(25,"2.0",$sqlquery,"resize field time_zone");

$sqlquery = "update lmb_userdb set time_zone = ''";
patch_db(26,"2.0",$sqlquery,"reset field time_zone");



function patch_27(){
	global $db;
	
	$rellist = dbf_20(array($DBA["DBSCHEMA"],dbf_4("VERK_%"),"'TABLE'"));
	foreach ($rellist["table_name"] as $key => $tablename){
		$sqlquery = "ALTER TABLE $tablename ADD SORT SMALLINT";
		$rs = @odbc_exec($db,$sqlquery);
	}
	return true;
}

patch_scr(27,"2.0","patch_27","relation order");

$sqlquery = "create table lmb_conf_views (id smallint not null, viewdef ".LMB_DBTYPE_LONG.", ispublic ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE.", hasid ".LMB_DBTYPE_BOOLEAN." default ".LMB_DBDEF_FALSE.", primary key (id))";
patch_db(28,"2.0",$sqlquery,"view definition");

$sqlquery = "alter table lmb_rules_tables add specificprivilege varchar(250)";
patch_db(29,"2.0",$sqlquery,"specific rules");

$sqlquery = "update lmb_umgvar set norm = '-quiet' where id = 29";
patch_db(30,"2.0",$sqlquery,"umgvar settings");

$sqlquery = "alter table lmb_action add help_url varchar(60)";
patch_db(31,"2.0",$sqlquery,"help link");

$sqlquery = "alter table lmb_action_depend add help_url varchar(60)";
patch_db(32,"2.0",$sqlquery,"help link local");

$sqlquery = "update lmb_umgvar set norm = 'http://www.limbas.org/Wiki' where form_name = 'helplink'";
patch_db(33,"2.0",$sqlquery,"help link umgvar");

$sqlquery = "alter table lmb_userdb drop fileview";
patch_db(34,"2.0",$sqlquery,"drop fileview from userdb");

$sqlquery = "alter table lmb_userdb drop multiframe";
patch_db(35,"2.0",$sqlquery,"drop multiframe from userdb");

$sqlquery = "alter table lmb_userdb drop gzip";
patch_db(36,"2.0",$sqlquery,"drop gzip from userdb");

$sqlquery = "insert into lmb_umgvar values (112,124,'multiframe','default','default multiframe',2)";
patch_db(37,"2.0",$sqlquery,"update umgvar");

$sqlquery = "alter table lmb_forms add subelement smallint";
patch_db(38,"2.0",$sqlquery,"formular subelements");

$sqlquery = "update lmb_forms set typ = 'menue' where typ = 'fmenue'";
patch_db(39,"2.0",$sqlquery,"formular subelements");

$sqlquery = "alter table lmb_forms add categorie smallint default 0";
patch_db(40,"2.0",$sqlquery,"formular subelements");

$sqlquery = "alter table lmb_report_list add ootemplate ".LMB_DBTYPE_FIXED."(18)";
patch_db(41,"2.0",$sqlquery,"ooffice reports");

$sqlquery = "alter table lmb_report_list add defformat varchar(10) default 'pdf'";
patch_db(42,"2.0",$sqlquery,"ooffice reports");

$sqlquery = "update lmb_umgvar set norm = '0' where form_name = 'use_jsgraphics'";
patch_db(43,"2.0",$sqlquery,"disable use_jsgraphics");

$sqlquery = "update lmb_report_list set defformat = 'pdf'";
patch_db(44,"2.0",$sqlquery,"ooffice reports");

$sqlquery = dbq_9(array(0,"lmb_userdb","layout","''"));
patch_db(45,"2.0",$sqlquery,"user defaults");

#$nid = next_db_id("lmb_umgvar");
#$sid = next_db_id("lmb_umgvar","SORT");
#$sqlquery = "insert into lmb_umgvar add($nid,$sid,'usetablebody',1,'inovation settings for tabelview (only firefox) BETA',3)";
#patch_db(20,"2.0",$sqlquery,"fieldtype subselect");


###########################

$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>