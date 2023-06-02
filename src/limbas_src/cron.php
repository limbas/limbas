<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/* 
$argv[0] = path of script
$argv[1] = jobID or filename in admin/tools/bash/
$argv[2] = username [optional]
$argv[3] = password [optional]
$argv[4] = additional data if $argv[1] is filename
*/

define("IS_CRON",true);

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
}

$cronjob = 1;

require_once(__DIR__ . '/bootstrap.php');
require_once(COREPATH . 'lib/include.lib');
require_once(COREPATH . 'lib/include_admin.lib');
require_once(COREPATH . 'lib/db/db_wrapper.lib');
require_once(COREPATH . 'lib/db/db_' . $DBA['DB'] . '_admin.lib');

// session present
if($auth_user){
	require_once(COREPATH . 'lib/session.lib');
// create needed vars
}else{
    require_once(COREPATH . 'lib/auth/Session.php');

    # --- Datenbankverbindung -------------------------------------------
    $db = dbq_0($DBA["DBHOST"],$DBA["DBNAME"],$DBA["DBUSER"],$DBA["DBPASS"],$DBA["ODBCDRIVER"],$DBA["PORT"]);
    
    /* --- LMB_UMGVAR ------------------- */
	$sqlquery = "SELECT FORM_NAME,NORM FROM LMB_UMGVAR";
	$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
	while(lmbdb_fetch_row($rs)) {
		$umgvar[lmbdb_result($rs,"FORM_NAME")] = lmbdb_result($rs,"NORM");
	}
	$umgvar["pfad"] = $umgvar["path"];

    # -------- Eigene globale Variablen auslesen --------
    $sqlquery3 = 'SELECT CKEY, CVALUE FROM LMB_CUSTVAR WHERE ACTIVE = ' . LMB_DBDEF_TRUE;
    $rs3 = lmbdb_exec($db, $sqlquery3) or errorhandle(lmbdb_errormsg($db), $sqlquery3, $action, __FILE__, __LINE__);
    if (!$rs3) {
        $commit = 1;
    }
    while (lmbdb_fetch_row($rs3)) {
        $custvar[lmbdb_result($rs3, "CKEY")] = lmbdb_result($rs3, "CVALUE");
    }

    /* --- load custom variables depend ------------------- */
    $sqlquery3 = 'SELECT LMB_CUSTVAR_DEPEND.CKEY, LMB_CUSTVAR_DEPEND.CVALUE FROM LMB_CUSTVAR_DEPEND LEFT JOIN LMB_CUSTVAR ON LMB_CUSTVAR_DEPEND.CKEY = LMB_CUSTVAR.CKEY WHERE LMB_CUSTVAR.OVERRIDABLE = ' . LMB_DBDEF_TRUE . ' AND LMB_CUSTVAR.ACTIVE = ' . LMB_DBDEF_TRUE . ' AND LMB_CUSTVAR_DEPEND.ACTIVE = ' . LMB_DBDEF_TRUE;
    $rs3 = lmbdb_exec($db, $sqlquery3) or errorhandle(lmbdb_errormsg($db), $sqlquery3, $action, __FILE__, __LINE__);
    if (!$rs3) {
        $commit = 1;
    }
    while (lmbdb_fetch_row($rs3)) {
        $custvar[lmbdb_result($rs3, "CKEY")] = lmbdb_result($rs3, "CVALUE");
    }

	# --- mbstring include -------------------------------------------
	if(strtoupper($umgvar["charset"]) == "UTF-8"){
	    require_once(COREPATH . 'lib/include_mbstring.lib');
		ini_set('default_charset', 'utf-8');
	}else{
        require_once(COREPATH . 'lib/include_string.lib');
		ini_set('default_charset', lmb_strtoupper($umgvar["charset"]));
	}

	# --- time library -------------------------------------------
    require_once(COREPATH . 'lib/include_DateTime.lib');

	/* --- Prüfung ob gesperrt --------------------------------- */
	if($umgvar["lock"]){
		die('System is locked!');
	}
	
	require_once(COREPATH . 'gtab/gtab_array.lib');
	
}




$umgvar["IS_CRON"] = 1;

# Include own bash extension
if ($job && is_string($job) && !is_numeric($job)) {
    $filename = basename($job);
    $filepath = COREPATH . 'admin/tools/bash/' . $filename;
    if (!file_exists($filepath)) {
        error_log("Requested bash file '$filepath' doesn't exist!");
        return;
    }
    $additionalCronData = null;
    if ($argv[4]) {
        $additionalCronData = $argv[4];
    }
    require_once($filepath);
    if($db){lmbdb_close($db);}
    return;
}

# EXTENSION Dateien einbinden (needed before jobs_ext.lib)
if(!$auth_user){
if(is_dir(EXTENSIONSPATH)){
	if($extdir = read_dir(EXTENSIONSPATH,1)){
		foreach ($extdir["name"] as $key => $value){
			$filetype = explode(".",$value);
			if($extdir["typ"][$key] == "file" AND $filetype[lmb_count($filetype)-1] == "inc"){
				$gLmbExt[$value][] = $extdir["path"][$key].$value;
			}
		}
	}
}}
require_once(COREPATH . 'admin/tools/jobs_ext.lib');



$sqlquery = "SELECT * FROM LMB_CRONTAB WHERE ID = $job AND ACTIV = ".LMB_DBDEF_TRUE;
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(lmbdb_fetch_row($rs)) {
	$cronvalue = lmbdb_result($rs,"VAL");
	$cron_id = lmbdb_result($rs,"ID");
	$kattempl = lmb_strtolower(lmbdb_result($rs,"KATEGORY"));
	
	# -------- BACKUP ----------
	if($kattempl == "backup" AND $cronvalue){
		$backup_alive = lmbdb_result($rs,"ALIVE");

		$val = explode(";",$cronvalue);
		foreach($val as $key => $value){
			$tmp = explode(",",$value);
			$par[$tmp[0]] = $tmp[1];
		}
		foreach($par as $key => $value){
			${"$key"} = $value;
		}
		require_once(COREPATH . 'admin/tools/backup.dao');

	}else{
		$templatefile = COREPATH . "admin/tools/jobs/$kattempl.lib";
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
if($db){lmbdb_close($db);}