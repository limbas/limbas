<?php

# will be overwritten during import, store for later
$backupFile = $backupdir;

if($setup_language == '2') {
    $session["setlocale"] = "en_EN";
} else {
    $session["setlocale"] = "de_DE";
}

$session["timezone"] = "Europe/Berlin";
$umgvar["use_datetimeclass"] = 1;

if($DBA["DB"]){
    require("../../lib/db/db_".$DBA["DB"].".lib");
    require("../../lib/db/db_".$DBA["DB"]."_admin.lib");
}

if($setup_dbdriver == 'PDO'){
    require_once('../../lib/db/db_pdo.lib');
}else{
    require_once('../../lib/db/db_odbc.lib');
}

require("../../lib/include.lib");
require("../../lib/include_admin.lib");
require("../../lib/include_DateTime.lib");

if(strtoupper($setup_charset) == "UTF-8"){
    require_once("../../lib/include_mbstring.lib");
    $GLOBALS['umgvar']['charset'] = 'UTF-8';
    ini_set('default_charset', 'utf-8');
}else{
    require_once("../../lib/include_string.lib");
    $GLOBALS['umgvar']['charset'] = lmb_strtoupper($setup_charset);
    ini_set('default_charset', lmb_strtoupper($setup_charset));
}

setlocale(LC_ALL, $session["setlocale"]);
date_default_timezone_set($session["timezone"]);

# database spec
if(!$setup_dbschema){
    if(lmb_substr($DBA["DB"],0,5) == "maxdb"){$DBA["DBSCHEMA"] = lmb_strtoupper($setup_dbuser); $setup_dbpath = '/opt/sdb/programs/bin';}
    elseif($DBA["DB"] == "postgres"){$DBA["DBSCHEMA"] = "public";}
    elseif($DBA["DB"] == "mysql"){$DBA["DBSCHEMA"] = $setup_database;}
    elseif($DBA["DB"] == "ingres"){$DBA["DBSCHEMA"] = "ingres";}
    elseif($DBA["DB"] == "mssql"){$DBA["DBSCHEMA"] = "dbo";}
}else{
        $DBA["DBSCHEMA"] = $setup_dbschema;
}
$DBA["DBNAME"] = $setup_database;

$db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport);
$setup = 1;

/* --- default tabimport ------------------------------------------------------ */
unset($commit);
$GLOBALS["umgvar"]["pfad"] = $setup_path_project;

?>
<script type="text/javascript">
	$('.scrollcontainer').height($( document ).height() - 200);
</script>
<?php

require_once("../tools/import.dao");

import_complete(1);

/* --- update umgvar ------------------------------------------------------ */

if($setup_company){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_company' WHERE FORM_NAME = 'company'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}
if($setup_path_project){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '".parse_db_string($setup_path_project)."' WHERE FORM_NAME = 'path'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = 'localhost:///".$setup_path_project."/BACKUP' WHERE FORM_NAME = 'backup_default'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}
if(is_numeric($setup_language)){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_language' WHERE FORM_NAME = 'default_language'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $sqlquery = "UPDATE LMB_USERDB SET LANGUAGE = ".$setup_language;
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}
if(is_numeric($setup_dateformat)){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_dateformat' WHERE FORM_NAME = 'default_dateformat'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $sqlquery = "UPDATE LMB_USERDB SET DATEFORMAT = ".$setup_dateformat;
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}
if($setup_charset){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_charset' WHERE FORM_NAME = 'charset'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}
if($setup_version[0]){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '".$DBA['VERSION']."' WHERE FORM_NAME = 'database_version'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}

# update color scheme
if($setup_color_scheme){
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$setup_color_scheme' WHERE FORM_NAME = 'default_usercolor'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    $sqlquery = "UPDATE LMB_USERDB SET FARBSCHEMA = '$setup_color_scheme'";
    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
}



#$defaulturl = $SERVER_NAME.$REQUEST_URI;
#$defaulturl = str_replace("admin/install/index.php","",$defaulturl);
#$defaulturl = "http://".$defaulturl;

$defaulturl = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
$defaulturl = "http://{$_SERVER['SERVER_NAME']}{$defaulturl}/";

$sqlquery = "UPDATE LMB_UMGVAR SET NORM = '$defaulturl' WHERE FORM_NAME = 'url'";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

# --- update include_db.lib ----------------------------
$dblibvalue = fopen($setup_path_project."/inc/include_db.lib","w+");
$enc_key = bin2hex(random_bytes(32));

$line=<<<EOD
<?php
/*
Copyright notice
(c) 1998-2018 LIMBAS GmbH (info@limbas.com)
All rights reserved
This script is part of the LIMBAS project. The LIMBAS project is free software;
you can redistribute it and/or modify it on 2 Ways:
Under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your option) 
any later version. Or in a proprietary software license http://limbas.com/
The GNU General Public License can be found at
http://www.gnu.org/copyleft/gpl.html. A copy is found in the textfile GPL.txt
and important notices to the license from the author is found in LICENSE.txt 
distributed with these scripts.
This script is distributed WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU 
General Public License for more details.
This copyright notice MUST APPEAR in all copies of the script!
*/

\$DBA['DB'] = '{$DBA['DB']}';				/* maxdb76 | masbd77 | postgres | ingres */
\$DBA['DBCUSER'] = '$setup_dbuser'; 		/* DB control user */
\$DBA['DBCPASS'] = '$setup_dbpass'; 		/* DB control password */
\$DBA['DBCNAME'] = '$setup_database'; 		/* DB control name */
	
\$DBA['DBUSER'] = '$setup_dbuser';			/* DB username */
\$DBA['DBPASS'] = '$setup_dbpass';			/* DB password */
\$DBA['DBNAME'] = '$setup_database';		/* DB instance name */
\$DBA['DBSCHEMA'] = '$setup_dbschema';		/* DB schema */
\$DBA['DBHOST'] = '$setup_host';			/* DB hostname or IP */
\$DBA['LMHOST'] = '$setup_host';			/* LIMBAS hostname or IP */
\$DBA['DBPATH'] = '$setup_dbpath';	        /* Path to database */
\$DBA['LMPATH'] = '$setup_path_project';	/* Path to LIMBAS */
\$DBA['ODBCDRIVER'] = '$setup_dbdriver';	/* unixODBC Driver */
\$DBA['PORT'] = '$setup_dbport';			/* database Port */
\$DBA['VERSION'] = '{$DBA['VERSION']}';	    /* database version */
define('LMB_ENC_KEY','{$enc_key}');         /* generated encryption key */

?>
EOD;

fputs($dblibvalue,$line);
fclose($dblibvalue);



/* --- DB-CLOSE ------------------------------------------------------ */
if ($db){lmbdb_close($db);}

# clean installation: remove demo-extension directories/files
if ($backupFile === 'demo.tar.gz') {
    $demoExtPath = "{$path}/EXTENSIONS/demo.tar.gz";
    if (file_exists($demoExtPath)) {
        $success = system("tar xzf '{$demoExtPath}' -C '{$path}/EXTENSIONS'");
        if (!$success) {
            echo "<div class=\"alert alert-success\">If you want extension files for demonstration purposes, you can extract the file dependent/EXTENSIONS/demo.tar.gz</div>";
        }
    }
}