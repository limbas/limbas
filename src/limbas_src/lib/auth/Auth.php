<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

require_once COREPATH . 'lib/auth/AuthDefault.php';
/** 
 * Class Auth
 */
abstract class Auth {


    /**
     * @var int id of the current user
     */
    protected $authId;
    
    /**
     * @var string username of the current user
     */
    protected $authUser;

    /**
     * Factory to get configured authentication method class
     * 
     * @param null $method
     * @return false|Auth
     */
    public static function getAuthenticator($method=null) {
        
        if ($method === null) {
            $method = 'default';

            $db = Database::get();

            /* --- get settings from umgvar ------------------- */
            $sqlquery = "SELECT ID, FORM_NAME, NORM FROM LMB_UMGVAR WHERE FORM_NAME = 'server_auth' OR FORM_NAME = 'path'";
            $rs2 = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, 'get auth method', __FILE__, __LINE__);
            $umgvar = array();
            while (lmbdb_fetch_row($rs2)) {
                $umgvar[lmbdb_result($rs2, 'FORM_NAME')] = lmbdb_result($rs2, 'NORM');
            }

            if ($umgvar['server_auth'] != 'default') {
                $method = $umgvar['server_auth'];
            }

        }
        
        if(!empty($method)) {
            $class = 'Auth'.ucfirst($method);

            //search internal methods
            $file = COREPATH . 'lib/auth/'.$class.'.php';
            if (file_exists($file)) {
                require_once $file;
                return new $class();
            }

            //search in extensons
            $file = EXTENSIONSPATH . 'auth/'.$class.'.php';
            if (file_exists($file)) {
                require_once $file;
                return new $class();
            }
        }
        
        
        return new AuthDefault();
    }


    /**
     * Check if the user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated() {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            return true;
        }
        return false;
    }

    /**
     * Deny access to the system.
     * Calls beforeDeny as hook for specific methods
     */
    public static function deny() {
        header_remove();
        Session::destroy();
        static::beforeDeny();
        die();
    }

    /**
     * Checks based on the current request if the user wants to log out
     */
    public static function checkLogout() {
        Session::start();
        $loggedIn = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
        Session::abort();
        
        if ($loggedIn && (isset($_POST['logout']) || isset($_REQUEST['logout']))) {
            self::deny();
        }
    }

    /**
     * Hook for actions that should be executed before exiting the script
     */
    protected static function beforeDeny(){
        require_once (COREPATH . 'lib/auth/html/permission.php');
    }


    /**
     * Start the authorization flow
     */
    public function authenticate() {

        $auth = $this->handleAuthentication();
        
        if ($auth !== true || empty($this->authUser)) {
            $this->logAccess($this->authUser);
            self::deny();
        }
        
        //if (!$this->checkIpAdress()) {
            //self::deny();
        //}
        
        $_SESSION['authenticated'] = true;
        
        if (empty($_SESSION['authId']) || $_SESSION['authUser'] !== $this->authUser) {
            $_SESSION['authId'] = $this->authId;
        }
        $_SESSION['authUser'] = $this->authUser;
        
        //if previously logged out
        if(isset($_POST['logout'])) {
            $redirectTo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $redirectTo);
            die();
        }
    }

    /**
     * Handle the authorization flow specific to the method
     * 
     * @return mixed
     */
    protected abstract function handleAuthentication();


    /**
     * Checks if the current IP / network has access to the system
     * 
     * @return bool
     */
    protected function checkIpAdress() {
        global $action;
        
        $db = Database::get();
        
        $sqlquery = "SELECT * FROM LMB_USERDB WHERE USERNAME = '" . parse_db_string($this->authUser, 30) . "'";
        if ($this->authUser !== 'admin') {
            $sqlquery .= ' AND (VALIDDATE >= ' . LMB_DBDEF_TIMESTAMP . ' OR VALID = ' . LMB_DBDEF_FALSE . ') AND DEL = ' . LMB_DBDEF_FALSE;
        }
        $rs2 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        if (!$rs2) { 
            return false;
        }

        
        $ipok = false;
        if(lmbdb_fetch_row($rs2)) {
            $ipok = true;
            if(lmbdb_result($rs2,"IPRANGE") AND lmbdb_result($rs2,"IPRANGE") != '*.*.*.*'){
                $rma = explode(".",lmb_getIpAddr());
                $rmar = explode("\n",lmbdb_result($rs2,"IPRANGE"));
                $bzm = 0;
                while($rmar[$bzm]){
                    unset($ipcheck);
                    $rmau = explode(".",trim($rmar[$bzm]));
                    $i = 0;
                    foreach($rmau as $key => $value){
                        if($value == $rma[$i] OR $value == "*"){$ipcheck[] = 1;}else{$ipcheck[] = 0;}
                        $i++;
                    }
                    if(!in_array(0,$ipcheck)){$ipok_[] = 1;}else{$ipok_[] = 0;}
                    $bzm++;
                }
                
                if(!in_array(1,$ipok_)){
                    $ipok = false;
                }
            }
        }
        
        return $ipok;
    }

    
    /**
     * Log any failed attempt to log in to log file
     * 
     * @param string $auth_user
     */
    protected function logAccess($auth_user='') {
        $ip = lmb_getIpAddr();
        $port = '';
        if (array_key_exists('REMOTE_PORT',$_SERVER)) {
            $port = $_SERVER['REMOTE_PORT'];
        }
        
        
        // access.log
        if($rf = fopen(TEMPPATH . 'log/access.log', 'a')) {
            fputs($rf, '[' . date('D M d H:i:s') . substr((string)microtime(), 1, 7) . " " . date('Y') . "] [auth_basic:error] [pid " . getmypid() . "] [client " . $ip . ":" . $port . "] AH01617: user $auth_user: authentication failure for \"limbas/basic/auth\": Password Mismatch, referer: limbas/basic/auth\n");
            fclose($rf);
        }
    }

    /**
     * Checks if entered password matches stored password
     * If needed, converts from md5 to password_hash
     * @param $username
     * @param $enteredPassword
     * @param $storedPassword
     * @return bool true if password is correct, false otherwise
     */
    protected function lmbPasswordVerify($username, $enteredPassword, $storedPassword) {
        global $action;
        
        $db = Database::get();

        $hashFunc = self::getHashFunction();

        # use password_* functions
        if ($hashFunc) {
            # correct password
            if (password_verify($enteredPassword, $storedPassword)) {
                return true; # authenticated via password_verify
            }

            # leftover md5 string?
            if (strlen($storedPassword) === 32 AND $storedPassword === md5($enteredPassword)) {
                # convert from md5 to password_hash
                $hashedPassword = password_hash($enteredPassword, $hashFunc);
                $sqlquery = "UPDATE LMB_USERDB SET PASSWORT='$hashedPassword' WHERE USERNAME = '" . parse_db_string($username, 30) . "'";
                $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                return true; # authenticated via md5
            }
        } else if (strlen($storedPassword) === 32) {
            if ($storedPassword === md5($enteredPassword)) {
                return true; # authenticated via md5
            }
        }
        return false;
    }


    /**
     * Hashes the given password according to the security settings
     * @param $password
     * @return bool|string hashed password or false on failure
     */
    public static function lmbPasswordHash($password) {
        global $umgvar;

        $hashFunc = self::getHashFunction();
        if ($hashFunc) {
            return password_hash($password, intval($hashFunc));
        } else {
            return md5($password);
        }
    }

    /**
     * returns the hash function to use
     * 
     * @return int
     */
    protected static function getHashFunction() {
        $db = Database::get();

        $sqlquery2 = 'SELECT ID, FORM_NAME, NORM FROM LMB_UMGVAR WHERE FORM_NAME = \'password_hash\'';
        $rs2 = lmbdb_exec($db,$sqlquery2) or errorhandle(lmbdb_errormsg($db),$sqlquery2,'',__FILE__,__LINE__);
        $hashFunction = 1;
        while(lmbdb_fetch_row($rs2)){
            $hashFunction = (int) lmbdb_result($rs2,'NORM');
        }
        
        return $hashFunction;
    }
}
