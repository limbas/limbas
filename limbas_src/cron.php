<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH (support@limbas.org)
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

/* 
$argv[0] = path of script
$argv[1] = jobID or filename in admin/tools/bash/
$argv[2] = username [optional]
$argv[3] = password [optional]
$argv[4] = additional data if $argv[1] is filename
*/

define("IS_CRON",1);

$lpath = $argv[0];
$job = $argv[1];

# -------- Systempfad ------------
$lpath = str_replace(basename(__FILE__),"",$lpath);

if(!is_dir($lpath)){die('wrong path');}

chdir($lpath);

# if login create session
if($argv[2] AND $argv[3]){
	$auth_user = $_REQUEST["auth_user"] = $argv[2];
	$auth_pass = $_REQUEST["auth_pass"] = $argv[3];
}else{
	$session["subgroup"] = array(1);
	$session["group_id"] = 1;
	$session["user_id"] = 1;
	$session["uploadsize"] = 104857600; # 100MB
	$session["pfad"] = $lpath;
	$session["path"] = $lpath;
	$umgvar["use_datetimeclass"] = 1;
}

$cronjob = 1;


require_once($lpath."inc/include_db.lib");
require_once($lpath."lib/db/db_".$DBA["DB"]."_admin.lib");
require_once($lpath."lib/include.lib");
require_once($lpath."lib/include_admin.lib");


// session present
if($auth_user){
	require_once($lpath."lib/session.lib");
// create needed vars
}else{
    
    # --- Datenbankverbindung -------------------------------------------
    $db = dbq_0($DBA["DBHOST"],$DBA["DBNAME"],$DBA["DBUSER"],$DBA["DBPASS"],$DBA["ODBCDRIVER"],$DBA["PORT"]);
    
    /* --- LMB_UMGVAR ------------------- */
	$sqlquery = "SELECT FORM_NAME,NORM FROM LMB_UMGVAR";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(odbc_fetch_row($rs)) {
		$umgvar[odbc_result($rs,"FORM_NAME")] = odbc_result($rs,"NORM");
	}
	$umgvar["pfad"] = $umgvar["path"];

	# --- mbstring include -------------------------------------------
	if(strtoupper($umgvar["charset"]) == "UTF-8"){
	    require_once("lib/include_mbstring.lib");
		ini_set('default_charset', 'utf-8');
	}else{
        require_once("lib/include_string.lib");
		ini_set('default_charset', lmb_strtoupper($umgvar["charset"]));
	}

	# --- time library -------------------------------------------
	if($umgvar["use_datetimeclass"]){
		require_once($lpath."lib/include_DateTime.lib");
	}else{
		require_once($lpath."lib/include_datetime.lib");
	}
	
	require_once($lpath."gtab/gtab_array.lib");
	
}




$umgvar["IS_CRON"] = 1;
$umgvar["uploadpfad"] = $umgvar["pfad"]."/UPLOAD/";

# Include own bash extension
if ($job && is_string($job) && !is_numeric($job)) {
    $filename = basename($job);
    $filepath = $umgvar['path'] . '/admin/tools/bash/' . $filename;
    if (!file_exists($filepath)) {
        error_log("Requested bash file '$filepath' doesn't exist!");
        return;
    }
    $additionalCronData = null;
    if ($argv[4]) {
        $additionalCronData = $argv[4];
    }
    require_once($filepath);
    if($db){odbc_close($db);}
    return;
}

# EXTENSION Dateien einbinden (needed before jobs_ext.lib)
if(!$auth_user){
if(is_dir($umgvar["pfad"]."/EXTENSIONS/")){
	if($extdir = read_dir($umgvar["pfad"]."/EXTENSIONS/",1)){
		foreach ($extdir["name"] as $key => $value){
			$filetype = explode(".",$value);
			if($extdir["typ"][$key] == "file" AND $filetype[count($filetype)-1] == "inc"){
				$gLmbExt[$value][] = $extdir["path"][$key].$value;
			}
		}
	}
}}
require_once($lpath."admin/tools/jobs_ext.lib");



$sqlquery = "SELECT * FROM LMB_CRONTAB WHERE ID = $job";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(odbc_fetch_row($rs)) {
	$cronvalue = odbc_result($rs,"VAL");
	$cron_id = odbc_result($rs,"ID");
	$kattempl = lmb_strtolower(odbc_result($rs,"KATEGORY"));
	
	# -------- BACKUP ----------
	if($kattempl == "backup" AND $cronvalue){
		$backup_alive = odbc_result($rs,"ALIVE");

		$val = explode(";",$cronvalue);
		foreach($val as $key => $value){
			$tmp = explode(",",$value);
			$par[$tmp[0]] = $tmp[1];
		}
		foreach($par as $key => $value){
			${"$key"} = $value;
		}
		require_once($lpath."admin/tools/backup.dao");
	# -------- INDIZE ----------
	}else{
		$templatefile = $lpath."admin/tools/jobs/$kattempl.lib";
		if(file_exists($templatefile)){
			require_once($templatefile);
			#$kategorie = "indize.lib";$kategoriedesc = "INDIZE";
			lmb_loghandle("$kattempl.log","starting job \t(".$cron_id.")");
			periodic_job($cronvalue);
			lmb_loghandle("$kattempl.log","ending job \t(".$cron_id.")");
		}
	}
}


/* --- DB-CLOSE ------------------------------------------------------ */
if($db){odbc_close($db);}
?>
