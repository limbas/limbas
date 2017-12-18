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


function patch_1(){
	global $db;

    $sqlquery = "ALTER TABLE LMB_DBPATCH ADD MAJOR ".LMB_DBTYPE_SMALLINT;
	$rs = odbc_exec($db,$sqlquery);
    if(!$rs){return false;}
    
    $sqlquery = "ALTER TABLE LMB_DBPATCH ADD VERSION2 ".LMB_DBTYPE_SMALLINT;
	$rs = odbc_exec($db,$sqlquery);
    if(!$rs){return false;}
    
	$sqlquery = "SELECT * FROM LMB_DBPATCH";
	$rs = odbc_exec($db,$sqlquery);
    if(!$rs){return false;}

	while(odbc_fetch_row($rs)){
		$version = odbc_result($rs, "VERSION");
		$id = odbc_result($rs, "ID");
		$v = explode('.',$version);
		
		$sqlquery1 = "UPDATE LMB_DBPATCH SET VERSION2 = ".parse_db_int($v[1]).", MAJOR = ".parse_db_int($v[0])." WHERE ID = $id";
		$rs1 = odbc_exec($db,$sqlquery1);
	}
	
    $sqlquery = dbq_22(array('LMB_DBPATCH','VERSION'));
	$rs = odbc_exec($db,$sqlquery);
	if(!$rs){return false;}
    
    $sqlquery = dbq_7(array($DBA["DBSCHEMA"],'LMB_DBPATCH','VERSION2','VERSION'));
	$rs = odbc_exec($db,$sqlquery);
    if(!$rs){return false;}
	
	return true;
	
}
patch_scr(1,10,"patch_1","update dbpatch table",2);


$sqlquery = "ALTER TABLE LMB_CHARTS ADD NOHEADER ".LMB_DBTYPE_BOOLEAN;
patch_db(2,10,$sqlquery,"charts noheader attribute",2);


function patch_3(){
	global $db;
	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,137,'csv_escape','".parse_db_string('\\')."','csv escape char (".parse_db_string('\\')." / ~)',2818)";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(3,10,"patch_3","update umgvar",2);

$sqlquery = "UPDATE LDMS_FILES SET TYP = 0 WHERE TYP IS NULL";
patch_db(4,10,$sqlquery,'ldms_files fieldtype defaultproblem',2);

$sqlquery = dbq_9(array($DBA["DBSCHEMA"],'LDMS_FILES','TYP','0'));
patch_db(5,10,$sqlquery,'ldms_files fieldtype defaultproblem',2);

$sqlquery = dbq_15(array($DBA["DBSCHEMA"],'LMB_USERDB','GC_MAXLIFETIME','INTEGER'));
patch_db(6,10,$sqlquery,'ldms_files fieldtype defaultproblem',2);
    
function patch_7(){
	global $db;
	$nid = next_db_id("lmb_umgvar","ID");
	$sqlquery = "insert into lmb_umgvar values($nid,152,'use_phpmailer','1','use PHPMailer for sendmail',2818)";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	return true;
}
patch_scr(7,10,"patch_7","update umgvar",2);

function patch_8(){
	global $umgvar;
	mkdir($umgvar["pfad"]."/TEMP/conf");
	return true;
}
patch_scr(8,10,"patch_8","create conf directory",2);



function patch_9(){
	global $db;
	
	
	$sqlquery = dbf_17(array('LMB_CHARTS','LMB_CHART_LIST'));
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if(!$rs) {$commit = 1;return false;}

    $addfields[] = 'DIAG_TYPE '.LMB_DBTYPE_VARCHAR.'(50)';
    $addfields[] = 'DIAG_WIDTH '.LMB_DBTYPE_SMALLINT.' DEFAULT 800';
    $addfields[] = 'DIAG_HEIGHT '.LMB_DBTYPE_SMALLINT.' DEFAULT 600';
    $addfields[] = 'TEXT_X '.LMB_DBTYPE_VARCHAR.'(100)';
    $addfields[] = 'TEXT_Y '.LMB_DBTYPE_VARCHAR.'(100)';
    $addfields[] = 'FONT_SIZE '.LMB_DBTYPE_SMALLINT.' DEFAULT 12';
    $addfields[] = 'PADDING_LEFT '.LMB_DBTYPE_SMALLINT.' DEFAULT 20';
    $addfields[] = 'PADDING_TOP '.LMB_DBTYPE_SMALLINT.' DEFAULT 20';
    $addfields[] = 'PADDING_RIGHT '.LMB_DBTYPE_SMALLINT.' DEFAULT 20';
    $addfields[] = 'PADDING_BOTTOM '.LMB_DBTYPE_SMALLINT.' DEFAULT 20';
    $addfields[] = 'LEGEND_X '.LMB_DBTYPE_SMALLINT.' DEFAULT 40';
    $addfields[] = 'LEGEND_Y '.LMB_DBTYPE_SMALLINT.' DEFAULT 40';
    $addfields[] = 'LEGEND_MODE '.LMB_DBTYPE_VARCHAR.'(50)';
    $addfields[] = 'PIE_WRITE_VALUES '.LMB_DBTYPE_VARCHAR.'(50)';
    $addfields[] = 'PIE_RADIUS '.LMB_DBTYPE_SMALLINT.' DEFAULT 40';
    $addfields[] = 'TRANSPOSED '.LMB_DBTYPE_BOOLEAN;
    
    foreach($addfields as $key => $value){
        $sqlquery = "ALTER TABLE LMB_CHART_LIST ".LMB_DBFUNC_ADD_COLUMN_FIRST." ".$value;
        $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if(!$rs) {$commit = 1;return false;}
    }

	$sqlquery = 'CREATE TABLE LMB_CHARTS (ID '.LMB_DBTYPE_FIXED.'(10),CHART_ID '.LMB_DBTYPE_SMALLINT.', FIELD_ID '.LMB_DBTYPE_SMALLINT.', AXIS '.LMB_DBTYPE_SMALLINT.', FUNCTION '.LMB_DBTYPE_SMALLINT.', COLOR '.LMB_DBTYPE_VARCHAR.'(8) ,'.LMB_DBFUNC_PRIMARY_KEY.' (ID))';
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;return false;}
    
	return true;
}
patch_scr(9,10,"patch_9","chart system tables",2);


function patch_10(){
	global $db;

	$sqlquery = "update lmb_umgvar set norm = 'pdf,text,html',beschreibung = 'files to index (pdf,text,html OR 1 for all)' where form_name = 'indize_filetype'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
	$sqlquery = "update lmb_umgvar set beschreibung = 'read metadata from file (jpg,jpeg,tiff,pdf,gif OR 1 for all)' where form_name = 'read_metadata'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
	$sqlquery = "update lmb_umgvar set beschreibung = 'update metadata to file (jpg,jpeg,tiff,pdf,gif OR 1 for all)' where form_name = 'update_metadata'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
	$sqlquery = "update lmb_umgvar set beschreibung = 'update exif metadata (1=XMP, 2=IPTC)' where form_name = 'use_exif'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
	$sqlquery = "delete from lmb_umgvar where form_name = 'upload_progress'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    
	return true;
}
patch_scr(10,10,"patch_10","update umgvar",2);

$sqlquery = "update lmb_action set link_url = '".parse_db_string("f_3('setup_diag');")."' where id = 114";
patch_db(11,10,$sqlquery,'update diagramm action',2);



#echo "-->";
$impsystables = array("lmb_lang.tar.gz");

###########################

if ($db AND !$action) {odbc_close($db);}
?>