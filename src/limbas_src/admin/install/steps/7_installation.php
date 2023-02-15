<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- run installation --- 
 *
 * needs
 * 
 *      db_vendor
 *      db_version
 *      setup_host
 *      setup_database
 *      setup_dbuser
 *      setup_dbpass
 *      setup_dbschema
 *      setup_dbport
 *      setup_dbdriver
 * 
 *      setup_dbpath
 *  
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}

global $session, $umgvar;

if ($alreadyInstalled) {
    return;
}

# will be overwritten during import, store for later
$backupdir = $_POST['backupdir'];
$installPackage = $backupdir;

$session['timezone'] = 'Europe/Berlin';
if ($_POST['setup_language'] == '2') {
    $session["setlocale"] = 'en_EN';
} else {
    $session["setlocale"] = 'de_DE';
}

if (!$configExists) {
    writeConfigFile($configFile);
}

seedDatabase($configFile);


# clean installation: remove demo-extension directories/files
if ($installPackage === 'demo.tar.gz') {
    $demoExtPath = EXTENSIONSPATH . 'demo.tar.gz';
    if (file_exists($demoExtPath)) {
        $success = system("tar xzf '$demoExtPath' -C '" . EXTENSIONSPATH . "'");
        if (!$success) {
            echo '<div class="alert alert-success">' . lang('If you want extension files for demonstration purposes, you can extract the file') . ' dependent/EXTENSIONS/demo.tar.gz</div>';
        }
    }
}


function writeConfigFile($configFile)
{

    $db_vendor = $_POST['db_vendor'];
    
    # database spec
    if (!$_POST['setup_dbschema']) {
        if (lmb_substr($db_vendor, 0, 5) == 'maxdb') {
            $_POST['setup_dbschema'] = lmb_strtoupper($_POST['setup_dbuser']);
            $_POST['setup_dbpath'] = '/opt/sdb/programs/bin';
        } elseif ($db_vendor == 'postgres') {
            $_POST['setup_dbschema'] = 'public';
        } elseif ($db_vendor == 'mysql') {
            $_POST['setup_dbschema'] = $_POST['setup_database'];
        } elseif ($db_vendor == 'ingres') {
            $_POST['setup_dbschema'] = 'ingres';
        } elseif ($db_vendor == 'mssql') {
            $_POST['setup_dbschema'] = 'dbo';
        }
    }

    # --- update include_db.lib ----------------------------
    $cfh = fopen($configFile, 'w+');
    $enc_key = bin2hex(random_bytes(32));

    $config =   '<?php' . "\n\n" .
                '$DBA[\'DB\']       = \'' . $db_vendor . '\';                   /* maxdb76 | masbd77 | postgres | ingres */' . "\n" .
                '$DBA[\'DBCUSER\']  = \'' . $_POST['setup_dbuser'] . '\'; 	    /* DB control user */' . "\n" .
                '$DBA[\'DBCPASS\']  = \'' . $_POST['setup_dbpass'] . '\'; 		/* DB control password */' . "\n" .
                '$DBA[\'DBCNAME\']  = \'' . $_POST['setup_database'] . '\'; 	/* DB control name */' . "\n" .
                '$DBA[\'DBUSER\']   = \'' . $_POST['setup_dbuser'] . '\';		/* DB username */' . "\n" .
                '$DBA[\'DBPASS\']   = \'' . $_POST['setup_dbpass'] . '\';       /* DB password */' . "\n" .
                '$DBA[\'DBNAME\']   = \'' . $_POST['setup_database'] . '\';		/* DB instance name */' . "\n" .
                '$DBA[\'DBSCHEMA\'] = \'' . $_POST['setup_dbschema'] . '\';		/* DB schema */' . "\n" .
                '$DBA[\'DBHOST\']   = \'' . $_POST['setup_host'] . '\';			/* DB hostname or IP */' . "\n" .
                '$DBA[\'LMHOST\']   = \'' . $_POST['setup_host'] . '\';			/* LIMBAS hostname or IP */' . "\n" .
                '$DBA[\'DBPATH\']   = \'' . $_POST['setup_dbpath'] . '\';	    /* Path to database */' . "\n" .
                '$DBA[\'ODBCDRIVER\'] = \'' . $_POST['setup_dbdriver'] . '\';	/* unixODBC Driver */' . "\n" .
                '$DBA[\'PORT\']     = \'' . $_POST['setup_dbport'] . '\';	    /* database Port */' . "\n" .
                '$DBA[\'VERSION\']  = \'' . $_POST['db_version'] . '\';	        /* database version */' . "\n" .
                '$DBA[\'CHARSET\']  = \'' . $_POST['setup_charset'] . '\';	        /* limbas charset */' . "\n" .
                'define(\'LMB_ENC_KEY\',\'' . $enc_key . '\');                  /* generated encryption key */';
    

    fputs($cfh, $config);
    fclose($cfh);


}


function seedDatabase($configFile)
{
    global $backupdir;
    global $session, $umgvar;
    global $db;
    global $DBA;
    global $action;

    require $configFile;

    if ($DBA['ODBCDRIVER'] == 'PDO') {
        require_once( COREPATH . 'lib/db/db_pdo.lib');
    } else {
        require_once( COREPATH . 'lib/db/db_odbc.lib');
    }

    require_once( COREPATH . 'lib/db/db_' . $DBA['DB'] . '.lib');
    require_once( COREPATH . 'lib/db/db_' . $DBA['DB'] . '_admin.lib');
    require_once( COREPATH . 'lib/include.lib');
    require_once( COREPATH . 'lib/include_admin.lib');
    require_once( COREPATH . 'lib/include_DateTime.lib');

    setlocale(LC_ALL, $session["setlocale"]);
    date_default_timezone_set($session["timezone"]);

# database spec
    $db = dbq_0($DBA['DBHOST'], $DBA['DBNAME'], $DBA['DBUSER'], $DBA['DBPASS'], $DBA['ODBCDRIVER'], $DBA['PORT']);

    /* --- check basic connection and get database Version --- */
    $setup_version = dbf_version($DBA);
    $DBA['VERSION'] = $setup_version[1];

    /* --- default tabimport ------------------------------------------------------ */
    unset($commit);
    $GLOBALS["umgvar"]["pfad"] = DEPENDENTPATH;
    $action = 'install';

    ?>
    <script type="text/javascript">
        $('.scrollcontainer').height($(document).height() - 200);
    </script>
    <?php

    require_once(COREPATH . 'admin/tools/import.dao');
    import_complete(1);



    /* --- update umgvar ------------------------------------------------------ */    


    if ($_POST['setup_company']) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '{$_POST['setup_company']}' WHERE FORM_NAME = 'company'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
    
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '" . parse_db_string(DEPENDENTPATH) . "' WHERE FORM_NAME = 'path'";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = 'localhost:///" . BACKUPPATH . "' WHERE FORM_NAME = 'backup_default'";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    
    if (is_numeric($_POST['setup_language'])) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '{$_POST['setup_language']}' WHERE FORM_NAME = 'default_language'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $sqlquery = "UPDATE LMB_USERDB SET LANGUAGE = " . $_POST['setup_language'];
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
    if (is_numeric($_POST['setup_dateformat'])) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '{$_POST['setup_dateformat']}' WHERE FORM_NAME = 'default_dateformat'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $sqlquery = "UPDATE LMB_USERDB SET DATEFORMAT = " . $_POST['setup_dateformat'];
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
    if ($_POST['setup_charset']) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '{$_POST['setup_charset']}' WHERE FORM_NAME = 'charset'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }
    if ($setup_version[0]) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '" . $DBA['VERSION'] . "' WHERE FORM_NAME = 'database_version'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }

# update color scheme
    if ($_POST['setup_color_scheme']) {
        $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '{$_POST['setup_color_scheme']}' WHERE FORM_NAME = 'default_usercolor'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $sqlquery = "UPDATE LMB_USERDB SET FARBSCHEMA = '{$_POST['setup_color_scheme']}'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
    }



/**
    $defaulturl = dirname(dirname(dirname($_SERVER['PHP_SELF'])));
    $defaulturl = "http://{$_SERVER['SERVER_NAME']}{$defaulturl}/";
 */
    $sqlquery = "UPDATE LMB_UMGVAR SET NORM = '' WHERE FORM_NAME = 'url'";
    $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);



    /* --- DB-CLOSE ------------------------------------------------------ */
    if ($db) {
        lmbdb_close($db);
    }
    
    
}
