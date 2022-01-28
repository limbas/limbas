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


$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,15,'use_unoconv','0','use unoconv converter if installed',5)";
patch_db(1,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "INSERT INTO LMB_UMGVAR VALUES($nid,129,'calendar_repetition','0','enables calendar repetitions',12)";
patch_db(2,"2.8",$sqlquery,"update umgvar");

require_once('admin/tables/tab.lib');
require_once('admin/setup/language.lib');

function patch_3(){
	global $lang;
	global $db;
	global $umgvar;

	$umgvar['allocate_freeid'] = 1;

	$nfield[] = array("field" => "FREEF","typ" => 2,"typ2" => 0,"size" => 1,"description" => 'freef',"spellingf" => 'freef',"default" => "","sort" => "");
	$nfield[] = array("field" => "ETAG","typ" => 13,"typ2" => 0,"size" => 20,"description" => 'ETAG',"spellingf" => 'ETAG',"default" => "","sort" => "");
	$nfield[] = array("field" => "CID","typ" => 13,"typ2" => 0,"size" => 36,"description" => 'CID',"spellingf" => 'CID',"default" => "","sort" => "");
	$nfield[] = array("field" => "REPETITION","typ" => 2,"typ2" => 0,"size" => 1,"description" => $lang[2791],"spellingf" => $lang[2791],"default" => "0","sort" => "", "ext" => "extendedCalRepetition");
	$nfield[] = array("field" => "REPEATUNTIL","typ" => 16,"typ2" => 0,"size" => 0,"description" => $lang[2792],"spellingf" => $lang[2792],"default" => "","sort" => "");
	$nfield[] = array("field" => "EXTRAPROPERTIES","typ" => 15,"typ2" => 0,"size" => 500,"description" => 'EXTRAPROPERTIES',"spellingf" => 'EXTRAPROPERTIES',"default" => "","sort" => "");
	$nfield[] = array("field" => "INTERVALS","typ" => 2,"typ2" => 0,"size" => 2,"description" => 'INTERVALS',"spellingf" => 'INTERVALS',"default" => "","sort" => "");

	$sqlquery = "SELECT TAB_ID FROM LMB_CONF_TABLES WHERE TYP = 2";
	$rs = lmbdb_exec($db,$sqlquery);

	while(lmbdb_fetch_row($rs)){
		$tabid = lmbdb_result($rs, "TAB_ID");
		add_extended_fields($nfield,$tabid,1);
	}
	
	return true;
	
}
patch_scr(3,"2.8","patch_3","update calender tables");


function patch_4(){
	global $db;

	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,137,'database_version','".dbf_version()."','database version',11)";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
	return true;
	
}
patch_scr(4,"2.8","patch_4","update umgvar");

$sqlquery = dbq_17(array('lmb_forms','ID'));
patch_db(5,"2.8",$sqlquery,"add primary key to lmb_forms if needed");

$sqlquery = dbq_17(array('lmb_form_list','ID'));
patch_db(6,"2.8",$sqlquery,"add primary key to lmb_form_list if needed");

function patch_7(){
	global $db;
	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,137,'csv_delimiter','".parse_db_string('\t')."','csv export delimiter (".parse_db_string('\t')." / ;)',11)";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(7,"2.8","patch_7","update umgvar");

function patch_8(){
	global $db;
	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,137,'csv_enclosure','','csv export enclosure (".parse_db_string('"').")',11)";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(8,"2.8","patch_8","update umgvar");

$sqlquery = "ALTER TABLE LMB_RULES_FIELDS ADD FIELDOPTION ".LMB_DBTYPE_BOOLEAN." DEFAULT ".LMB_DBDEF_TRUE;
patch_db(9,"2.8",$sqlquery,"add option rule");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,139,'server_auth','intern','server authentication method (intern/LDAP)',6)";
patch_db(10,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,138,'header_auth','basic','client browser authentication (basic/digest)',6)";
patch_db(11,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,140,'LDAP_domain','','a valid LDAP or domain server',6)";
patch_db(12,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,140,'LDAP_baseDn','DC=mydomain,DC=local','the base dn for your domain',6)";
patch_db(13,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,140,'LDAP_accountSuffix','@mydomain.local','the full account suffix for your domain',6)";
patch_db(14,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,140,'LDAP_defaultGroup','','default LIMBAS maingroup of all LDAP users',6)";
patch_db(15,"2.8",$sqlquery,"update umgvar");

$nid = next_db_id("lmb_umgvar","ID");
$sqlquery = "insert into lmb_umgvar values($nid,140,'debug_messages','','store all messages in messages.log',7)";
patch_db(16,"2.8",$sqlquery,"update umgvar");


function patch_17(){
	global $db;
	

	$sqlquery = "select tab_id from lmb_conf_tables where lower(tabelle) = 'ldms_files'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$tid = lmbdb_result($rs,'tab_id');

	$sqlquery = "select id from lmb_conf_fields where tab_id = $tid and field_name = 'TYP'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    if(!lmbdb_fetch_row($rs) and $tid){
		$nid = next_db_id("lmb_conf_fields","ID");
		$sqlquery = "insert into lmb_conf_fields (id,field_id,tab_id,tab_group,data_type,field_type,field_name,form_name,verkntabletype,beschreibung,spelling,field_size) values ($nid,6,$tid,4,16,5,'TYP','typ',1,10932,10933,5)";
	    $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    }
    
	$sqlquery = "select id from lmb_conf_fields where tab_id = $tid and field_name = 'VPID'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
    if(!lmbdb_fetch_row($rs) and $tid){
    	
		$sqlquery = "delete from lmb_conf_fields where tab_id = $tid and field_id = 27";
		$rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	
		$nid = next_db_id("lmb_conf_fields","ID");
		$sqlquery = "insert into lmb_conf_fields (id,field_id,tab_id,tab_group,data_type,field_type,field_name,form_name,verkntabletype,beschreibung,spelling,field_size) values ($nid,27,$tid,4,16,5,'VPID','vpid',1,10974,10975,18)";
	    $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    }
    
    if($rs1){
    
		require_once("admin/group/group.lib");
	
		$sqlquery = "SELECT COUNT(*) AS ANZAHL FROM LMB_GROUPS";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		$num_group =  lmbdb_result($rs,"ANZAHL");
			
		$sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		while(lmbdb_fetch_row($rs)){
			check_grouprights1(lmbdb_result($rs,"GROUP_ID"),lmbdb_result($rs,"NAME"),null,null);
		}
	
    }
    
	return true;
}
patch_scr(17,"2.8","patch_17","update dms typ");


$sqlquery = dbq_7(array($DBA["DBSCHEMA"],'lmb_rules_fields','OPTION','FIELDOPTION'));
patch_db(18,"2.8",$sqlquery,"rename option in lmb_rules_fields");



#echo "-->";
#$impsystables = array("lmb_lang.tar.gz","lmb_action.tar.gz","lmb_field_types.tar.gz");

###########################

if ($db AND !$action) {lmbdb_close($db);}
?>