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

$sqlquery = "alter table lmb_gtab_pattern add viewid smallint default 0";
patch_db(1,"2.2",$sqlquery,"view");

$sqlquery = "update lmb_gtab_pattern set viewid = 0";
patch_db(2,"2.2",$sqlquery,"view");

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],"lmb_gtab_pattern","tabid","varchar(50)"));
patch_db(3,"2.2",$sqlquery,"view");

$sqlquery = "create table lmb_conf_viewfields (id ".LMB_DBTYPE_FIXED.", viewid integer, tablename varchar(50), qfield ".LMB_DBTYPE_LONG.", qfilter ".LMB_DBTYPE_LONG.", qorder smallint, qalias varchar(50), sort smallint, qshow boolean default true, qfunc smallint)"; 
patch_db(4,"2.2",$sqlquery,"view");

$sqlquery = "alter table lmb_conf_views add relation ".LMB_DBTYPE_LONG;
patch_db(5,"2.2",$sqlquery,"view");

$sqlquery = "alter table lmb_conf_views add usesystabs boolean default false";
patch_db(7,"2.2",$sqlquery,"view");

$sqlquery = "alter table LMB_REPORT_LIST drop MENUEXTENSION";
patch_db(8,"2.2",$sqlquery,"report");

$sqlquery = "alter table LMB_CONF_FIELDS add VERKNVIEW varchar(20)";
patch_db(9,"2.2",$sqlquery,"relation view");

$sqlquery = "alter table LMB_CONF_FIELDS add VERKNFIND varchar(20)";
patch_db(10,"2.2",$sqlquery,"relation view");

$sqlquery = "alter table LMB_CONF_FIELDS add AJAXPOST BOOLEAN";
patch_db(11,"2.2",$sqlquery,"dynamic save");

$sqlquery = "alter table lmb_gtab_userdat drop tab_group";
patch_db(12,"2.2",$sqlquery,"reminder");

$sqlquery = "alter table lmb_gtab_userdat drop group_id";
patch_db(13,"2.2",$sqlquery,"reminder");

$sqlquery = "alter table lmb_gtab_userdat add fromuser smallint";
patch_db(14,"2.2",$sqlquery,"reminder");

$sqlquery = "alter table lmb_gtab_userdat add CONTENT varchar(160)";
patch_db(15,"2.2",$sqlquery,"reminder");

$sqlquery = "alter table lmb_trigger add position varchar(6)";
patch_db(16,"2.2",$sqlquery,"trigger");

$sqlquery = "insert into lmb_umgvar values (117,130,'detail_viewmode','editmode','view detail as (editmode/viewmode)',3)";
patch_db(17,"2.2",$sqlquery,"update umgvar");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"lmb_forms","id","idxxx"));
patch_db(18,"2.2",$sqlquery,"formular");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"lmb_forms","keyid","id"));
patch_db(19,"2.2",$sqlquery,"formular");

$sqlquery = dbq_7(array($DBA["DBSCHEMA"],"lmb_forms","idxxx","keyid"));
patch_db(20,"2.2",$sqlquery,"formular");

$sqlquery = "insert into lmb_umgvar values (118,131,'upload_progress','1','view uploadprogress (need php extension)',5)";
patch_db(21,"2.2",$sqlquery,"update umgvar");

$sqlquery = "alter table lmb_userdb drop e_setting";
patch_db(22,"2.2",$sqlquery,"update e_setting");

$sqlquery = "alter table lmb_userdb add e_setting ".LMB_DBTYPE_LONG;
patch_db(23,"2.2",$sqlquery,"update e_setting");

if($DBA["DB"] == "postgres"){
	$sqlquery = "";
}else{
	$sqlquery = dbq_21();
}
patch_db(24,"2.2",$sqlquery,"drop trigger procedure");

function patch_25(){
	return dbq_16(array($DBA["DBSCHEMA"]));
}
patch_scr(25,"2.2","patch_25","update trigger procedure");

$sqlquery = "insert into lmb_umgvar values (119,132,'wysiwygeditor','openwysiwyg','WYSIWYG Editor (openwysiwyg/TinyMCE)',2)";
patch_db(26,"2.2",$sqlquery,"update umgvar");

$sqlquery = "delete from lmb_umgvar where form_name = 'ps_compression'";
patch_db(27,"2.2",$sqlquery,"update umgvar");

$sqlquery = "update lmb_umgvar set beschreibung = 'use to compress pdfs' where form_name = 'ps_downsample'";
patch_db(28,"2.2",$sqlquery,"update umgvar");

#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz");
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>