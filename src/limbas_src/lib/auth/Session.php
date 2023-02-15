<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
use admin\tools\update\Updater;

class Session {
    
    public static array $globvars = array('session','umgvar','custvar','userdat','groupdat','user_colors','lmcurrency','lmfieldtype','LINK','LINK_ID','LINK_ACTION','farbschema','lang','gsr','filter','mfilter','ffilter','popc','popg','gsnap','gform','gformlist','greportlist','gdiaglist','gtabletree','tabgroup','gtab','grule','gfield','gverkn','gfile','ufile','filestruct','verknpool','gmimetypes','gtrigger','greminder','gwfl','gLmbExt','externalStorage','gprinter','lmmultitenants','gcustmenu');
    
    
    public static function start($sess_id = null, $use_cookies = null): void
    {
        
        session_name('LMB_SESS_ID');

        
        if(session_status() !== PHP_SESSION_NONE) {
            session_write_close();
        }

        session_set_cookie_params([
            'samesite' => 'strict',
            //'lifetime' => 900,
            'httponly' => true
        ]);
        
        if ($use_cookies !== null) {
            ini_set('session.use_cookies', 0);
        }

        if ($sess_id !== null) {
            session_id($sess_id);
        }
        
        session_start();
    }
    
    public static function destroy(): void
    {
        global $action;
        
        $db = Database::get();
        $session_id = session_id();
        
        if(!empty($session_id) && $db){
            /* --- delete session --------------------------------- */
            $sqlquery = "DELETE FROM LMB_SESSION WHERE ID = '".parse_db_string($session_id)."'";
            lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }
        
        setcookie('LMB_SESS_ID', '', [
            'expires' => time() - 42000,
            'path' => '/',
            'samesite' => 'Strict',
        ]);

        if(session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }        
    }

    public static function abort(): void
    {
        if(session_status() === PHP_SESSION_ACTIVE) {
            session_abort();
        }
    }
    
    
    public static function delete(): void
    {
        global $action;
        
        $db = Database::get();
        
        if (isset($_SESSION['authId']) && !empty($_SESSION['authId'])) {
            $sessionId = session_id();
            $sqlquery = 'DELETE FROM LMB_SESSION WHERE USER_ID = ' . $_SESSION['authId'] . (!empty($sessionId) ? ' AND ID = \'' . parse_db_string($sessionId) . '\'' : '');
            lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }

        foreach (Session::$globvars as $gvar) {
            $GLOBALS[$gvar] = array();
        }
    }

    
    
    
    
    public static function load(): void
    {
        global $action;
        global $session;
        
        self::setAction();
        
        self::assignSessionVars();

        /* --- Session löschen --------------------------------------------- */
        if($action === 'sess_refresh' || isset($_REQUEST['sess_refresh'])){
            self::delete();
        }
        
        if (empty($session)) {
            self::init();
        } else {
            if (!self::loadSessionFromDB()) {
                Auth::deny();
            }
            self::loadExisting();
        }

        if ($action !== 'setup_update' && $action !== 'maintenance') {
            Updater::checkVersion();
        }
        
        
        self::checkLocked();

        self::setDbVariables();
    }

    private static function init(): void
    {
        global $action;
        
        $db = Database::get();

        /* --- SESSION löschen die älter als 3 Tage --------------------------------- */
        $sqlquery4 = "DELETE FROM LMB_SESSION WHERE ".LMB_DBFUNC_DATE."ERSTDATUM) < '".convert_stamp(mktime(0,0,0,date("m"),date("d")-3,date("Y")),1)."'";
        $rs4 = lmbdb_exec($db,$sqlquery4) or errorhandle(lmbdb_errormsg($db),$sqlquery4,$action,__FILE__,__LINE__);
        if(!$rs4) {$commit = 1;}

        /* --- Session eintragen ---------------------------------------- */
        $sqlquery5 = "DELETE FROM LMB_SESSION WHERE ID = '".session_id()."'";
        $rs5 = lmbdb_exec($db,$sqlquery5) or errorhandle(lmbdb_errormsg($db),$sqlquery5,$action,__FILE__,__LINE__);
        if(!$rs5) {$commit = 1;}

        $sqlquery6 = "INSERT INTO LMB_SESSION (ID,USER_ID,GROUP_ID,LOGOUT,IP) VALUES ('".session_id()."',".$_SESSION['authId'].",0,".LMB_DBDEF_FALSE.",'".lmb_getIpAddr()."')";
        $rs6 = lmbdb_exec($db,$sqlquery6) or errorhandle(lmbdb_errormsg($db),$sqlquery6,$action,__FILE__,__LINE__);
        if(!$rs6) {$commit = 1;}
        
        require_once(COREPATH . 'lib/session_auth.lib');
    }
    
    private static function loadExisting(): void
    {
        global $session;
        global $umgvar;
        global $action;
        
        $db = Database::get();
        
        setlocale(LC_ALL, $session["setlocale"]);
        date_default_timezone_set($session["timezone"]);

        if($session["debug"]){
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);
            ini_set("display_errors","1");
        }else{
            ini_set("display_errors","0");
        }
        

        /* --- Userstatistik eintragen ---------------------------------------- */
        $sqlquery1 = "UPDATE LMB_HISTORY_USER SET UPDATE_DATE = '".convert_stamp(time())."',LOGIN_TIME = ".(date("U") - $session["login_date"])." WHERE SESSIONID = '".session_id()."' AND USERID = ".$session["user_id"]." AND LOGIN_DATE >= '".convert_stamp($session["login_date"])."'";
        lmbdb_exec($db,$sqlquery1) or errorhandle(lmbdb_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);

        # php.ini set
        if($umgvar["ini_defaultlrl"]){
            ini_set("defaultlrl",$umgvar["ini_defaultlrl"]);
        }
        if($umgvar["ini_maxsize"]){
            ini_set("upload_max_filesize",$umgvar["ini_maxsize"]."M");
            ini_set("post_max_size",$umgvar["ini_maxsize"]."M");
        }
    }
    
    
    private static function assignSessionVars(): void
    {
        foreach (self::$globvars as $gvar) {
            if (isset($_SESSION[$gvar])) {
                $GLOBALS[$gvar] = $_SESSION[$gvar];   
            }
        }        
    }
    
    private static function setAction(): array|string|null
    {
        global $action;
        $action = '';
        if (array_key_exists('action',$_REQUEST)) {
            $action = preg_replace('/[^0-9a-z]+/i','_',$_REQUEST['action']);
        }        
        return $action;
    }
    
    private static function setDbVariables(): void
    {
        
        $db = Database::get();
        
        /* --- set database variables after session init --------------------------------- */
        if (function_exists('dbf_setVariables')) {
            dbf_setVariables($db);
        }
        
    }
    
    private static function checkLocked(): void
    {
        global $session;
        global $umgvar;
        
        /* --- Prüfung ob Backend gesperrt --------------------------------- */
        if($session["lockbackend"]){

            if(!defined('IS_SOAP') AND !defined('IS_WEBDAV') AND defined('IS_WSDL') AND !lmb_strpos($umgvar["allowed_proxys"],lmb_getIpAddr())){
                require (COREPATH . 'lib/auth/html/lock.php');
                Session::destroy();
                die();
            }
        }
    }

    
    private static function loadSessionFromDB()
    {
        global $gsnap;
        
        $db = Database::get();
        
        $session_id = session_id();
        
        $sqlquery = "SELECT ID,IP,FILESTRUCT_CHANGED,TABLE_CHANGED,SNAP_CHANGED FROM LMB_SESSION WHERE ID = '".parse_db_string($session_id)."'";
        $rs = lmbdb_exec($db,$sqlquery);
        if(!$rs){
            if(strpos(lmbdb_errormsg($db),"Unknown table name") > 0){
                die("<Script language=\"JavaScript\">\ndocument.location.href='admin/install/setup.php';\n</Script>\n");
            }
            return true;
        }

        # check for some changes in snapshots
        if(lmbdb_result($rs,"SNAP_CHANGED")){
            require_once(COREPATH . 'extra/snapshot/snapshot.lib');
            $gsnap = SNAP_loadInSession(1);
        }

        # check for some changes in filestructure / execute get_filestructure()
        //$umgvar["get_new_filestruct"] = lmbdb_result($rs,"FILESTRUCT_CHANGED");

        # check for some changes in tables
        #$umgvar["get_new_tablestruct"] = lmbdb_result($rs,"TABLE_CHANGED");
        
        $userId = lmbdb_result($rs,'ID');

        if(!empty($userId)){
            return true;
        }
        
        return false;
    }

}
