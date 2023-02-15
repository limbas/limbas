<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class AuthDefault extends Auth {


    protected function handleAuthentication()
    {
        if( ! (defined('IS_REST') || defined('IS_WSDL') || defined('IS_WEBDAV') || !empty($auth_token))) {
            Session::start();
            if ($this->isAuthenticated()) {
                $this->authId = $_SESSION['authId'];
                $this->authUser = $_SESSION['authUser'];
                return true;
            }
        }

        $auth_user = '';
        $auth_pass = '';
        $auth_token = '';

        if (isset($_REQUEST['auth_user'])) {
            $auth_user = $_REQUEST['auth_user'];
        }
        if (isset($_REQUEST['auth_pass'])) {
            $auth_pass = $_REQUEST['auth_pass'];
        }
        if (isset($_REQUEST['authToken'])) {
            $auth_token = $_REQUEST['authToken'];
        }

        if (isset($_POST['username'])) {
            $auth_user = $_POST['username'];
        }
        if (isset($_POST['password'])) {
            $auth_pass = $_POST['password'];
        }


        $this->setAuthorizationVars();
        
        if(empty($auth_user)){$auth_user = $_SERVER['PHP_AUTH_USER'];}
        if(empty($auth_pass)){$auth_pass = $_SERVER['PHP_AUTH_PW'];}
        
        $auth_user = dbf_7(substr($auth_user,0,30));
        $auth_pass = dbf_7(substr($auth_pass,0,30));
        $auth_token = dbf_7($auth_token);

        if(empty($auth_user) && empty($auth_token)){
            self::deny();
        }

        if(($auth_user AND $auth_pass) OR $auth_token){

            $this->authUser = $auth_user;

            // REST / WSDL - use credentials
            if(defined('IS_REST') OR defined('IS_WSDL') OR defined('IS_WEBDAV')) {
                return $this->apiAuth($auth_user, $auth_pass);
            }
            elseif ($auth_token) {
                return $this->tokenAuth($auth_token, $auth_user, $auth_pass);
            }

            return $this->defaultAuth($auth_user, $auth_pass);

        }



        return false;
    }

    /**
     * Checks if the given auth credentials are valid and, if it is, returns the corresponding user id
     * @param string $auth_user
     * @param string $auth_pass
     * @return int|false the limbas user_id or false on error
     */
    protected function lmbGetAuthCredentials(string $auth_user, string $auth_pass): bool|int
    {
        global $db;
        global $action;

        # check token exists
        $sqlquery = 'SELECT USER_ID,PASSWORT FROM LMB_USERDB WHERE USERNAME = \'' . parse_db_string($auth_user) . '\'';
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if (lmbdb_fetch_row($rs)) {
            $this->authId = lmbdb_result($rs, 'USER_ID');
            if (!$this->lmbPasswordVerify($auth_user, $auth_pass, lmbdb_result($rs, 'PASSWORT'))) {
                return false;
            }
        }else{
            return false;
        }

        if(!$this->authId){
            return false;
        }

        return $this->authId;
    }


    protected function apiAuth(string $auth_user, string $auth_pass): bool
    {
        global $action;
        $db = Database::get();
        $authId = $this->lmbGetAuthCredentials($auth_user, $auth_pass);

        if (empty($authId)) {
            self::deny();
        }
        
        # get session id
        $sqlquery = 'SELECT ID FROM LMB_SESSION WHERE USER_ID='.$this->authId.' ORDER BY ERSTDATUM DESC ' . LMB_DBFUNC_LIMIT . " 1";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if (lmbdb_fetch_row($rs)) {
            # load session of user
            $session_id = lmbdb_result($rs, 'ID');
            Session::start($session_id,false);
            return true;
        }
        Session::start();
        return self::defaultAuth($auth_user, $auth_pass);
    }

    protected function tokenAuth(string $auth_token, string $auth_user, string $auth_pass): bool
    {
        global $action;
        $db = Database::get();

        $this->authId = lmbGetAuthTokenUser($auth_token);
        if (empty($this->authId)) {
            self::deny();
        }

        # get session id
        $sqlquery = 'SELECT LMB_SESSION.ID, LMB_USERDB.USERNAME FROM LMB_SESSION INNER JOIN LMB_USERDB ON LMB_USERDB.USER_ID = LMB_SESSION.USER_ID WHERE LMB_SESSION.USER_ID ='.$this->authId.' ORDER BY ERSTDATUM DESC ' . LMB_DBFUNC_LIMIT . " 1";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

        # load session of user
        if (lmbdb_fetch_row($rs)) {
            # load session of user
            $session_id = lmbdb_result($rs, 'ID');
            $this->authUser = lmbdb_result($rs, 'USERNAME');
            Session::start($session_id);
            return true;
        }
        Session::start();
        return self::defaultAuth($auth_user, $auth_pass);
    }

    protected function defaultAuth(string $auth_user, string $auth_pass): bool
    {
        global $umgvar;
        global $action;
        $db = Database::get();

        if ($this->isAuthenticated()) {
            $this->authId = $_SESSION['authId'];
            $this->authUser = $_SESSION['authUser'];
            return true;
        }
        
        # check for authentication method / charset
        $sqlquery2 = "SELECT ID,FORM_NAME,NORM FROM LMB_UMGVAR WHERE FORM_NAME LIKE '%_auth' OR FORM_NAME = 'charset'";   #CATEGORY = 6 OR CATEGORY = 0
        $rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
        $umgvar = array();
        while(lmbdb_fetch_row($rs2)){
            $umgvar[lmbdb_result($rs2,"FORM_NAME")] = lmbdb_result($rs2,"NORM");
        }
        
        
        $sqlquery2 = "SELECT ID, PASSWORT FROM LMB_USERDB WHERE USERNAME = '" . parse_db_string($auth_user, 30) . "'";
        if ($auth_user !== 'admin') {
            $sqlquery2 .= ' AND (VALIDDATE >= ' . LMB_DBDEF_TIMESTAMP . ' OR VALID = ' . LMB_DBDEF_FALSE . ') AND DEL = ' . LMB_DBDEF_FALSE;
        }
        $rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,$action,__FILE__,__LINE__);
        if (!$rs2) {
            return false;
        }

        
        if(lmbdb_fetch_row($rs2)) {
            $this->authId = lmbdb_result($rs2, 'ID');
            return $this->lmbPasswordVerify($auth_user, $auth_pass, lmbdb_result($rs2, 'PASSWORT'));
        }
        
        return false;
    }
    

    protected static function beforeDeny() {
        if((isset($_POST['username']) && !empty($_POST['username'])) || (isset($_POST['password']) && !empty($_POST['password']))) {
            $wrongCredentials = true;
        }
        require_once (COREPATH . 'lib/auth/html/login.php');
    }


    /*
     * set Authorization Vars - workaround for some server configurations
     *
     */
    protected function setAuthorizationVars() {
        if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_PW']) {
            return;
        }

        $servfield = null;
        if (isset($_SERVER['HTTP_AUTHORIZATION']) && (strlen($_SERVER['HTTP_AUTHORIZATION']) > 0)) {
            $servfield = 'HTTP_AUTHORIZATION';
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) && (strlen($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) > 0)) {
            $servfield = 'REDIRECT_HTTP_AUTHORIZATION';
        }
        if ($servfield){
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER[$servfield], 6)));
            if( strlen($_SERVER['PHP_AUTH_USER']) == 0 || strlen($_SERVER['PHP_AUTH_PW']) == 0 )
            {
                unset($_SERVER['PHP_AUTH_USER']);
                unset($_SERVER['PHP_AUTH_PW']);
            }
        }
    }
    
}
