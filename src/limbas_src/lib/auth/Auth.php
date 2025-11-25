<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\auth;

use Limbas\lib\db\Database;

abstract class Auth
{


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
     * @return Auth
     */
    public static function getAuthenticator($method = null): Auth
    {

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


        //default methods
        if (in_array($method, array('default', 'htaccess'))) {
            return match ($method) {
                'default' => new AuthDefault(),
                'htaccess' => new AuthHtaccess(),
            };
        }

        if (!empty($method)) {
            $class = 'Auth' . ucfirst($method);
            //search in extensions
            $file = EXTENSIONSPATH . 'auth/' . $class . '.php';
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
    public function isAuthenticated(): bool
    {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            return true;
        }
        return false;
    }

    /**
     * Deny access to the system.
     * Calls beforeDeny as hook for specific methods
     */
    public static function deny(bool $blocked = false): void
    {
        header_remove();
        Session::destroy();

        if(defined('IS_REST')) {
            header('HTTP/1.1 401 Unauthorized');
            header('Access-Control-Allow-Origin: *');
            header('Content-type: application/json; charset=utf-8');
            echo json_encode(['errors' => [401=>'Unauthorized']]);
        }
        else {
            static::beforeDeny($blocked);   
        }
        die();
    }

    /**
     * Checks based on the current request if the user wants to log out
     */
    public static function checkLogout(): void
    {

        Session::start();
        $userid = $_SESSION['authId'];
        $loggedIn = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
        Session::abort();

        if ($loggedIn && (isset($_POST['logout']) || isset($_REQUEST['logout']))) {
            // --- temp-Verzeichnis löschen ----
            rmdirr(USERPATH . $userid . "/temp");
            self::deny();
        }
    }

    /**
     * Hook for actions that should be executed before exiting the script
     */
    protected static function beforeDeny(bool $blocked = false): void
    {
        require_once(COREPATH . 'lib/auth/html/permission.php');
    }


    /**
     * Start the authorization flow
     */
    public function authenticate(): void
    {
        if (!$this->checkAttempts()) {
            self::deny(true);
        }

        $auth = $this->handleAuthentication();

        if ($auth !== true || empty($this->authUser)) {
            $this->setAttempt();
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
        if (isset($_POST['logout'])) {
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
    protected abstract function handleAuthentication(): bool;


    /**
     * Log any failed attempt to log in to log file
     *
     * @param string $auth_user
     */
    protected function logAccess($auth_user = ''): void
    {
        $ip = Session::getIP();
        $port = '';
        if (array_key_exists('REMOTE_PORT', $_SERVER)) {
            $port = $_SERVER['REMOTE_PORT'];
        }


        // access.log
        if ($rf = fopen(TEMPPATH . 'log/access.log', 'a')) {
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
    protected function lmbPasswordVerify($username, $enteredPassword, $storedPassword): bool
    {
        global $action;

        $db = Database::get();

        $hashFunc = self::getHashFunction();

        # correct password
        if (password_verify($enteredPassword, $storedPassword)) {
            return true; # authenticated via password_verify
        }
        # leftover md5 string?
        elseif (strlen($storedPassword) === 32 and $storedPassword === md5($enteredPassword)) {
            # convert from md5 to password_hash
            $hashedPassword = password_hash($enteredPassword, $hashFunc);
            $sqlquery = "UPDATE LMB_USERDB SET PASSWORT='$hashedPassword' WHERE USERNAME = '" . parse_db_string($username, 30) . "'";
            lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            return true; # authenticated via md5
        }
        
        return false;
    }


    /**
     * Hashes the given password according to the security settings
     * @param $password
     * @return bool|string hashed password or false on failure
     */
    public static function lmbPasswordHash($password): bool|string
    {
        $hashFunc = self::getHashFunction();
        return password_hash($password, $hashFunc);
    }

    /**
     * returns the hash function to use
     *
     * @return string|int
     */
    protected static function getHashFunction(): string|int
    {
        $db = Database::get();

        $sqlquery2 = 'SELECT ID, FORM_NAME, NORM FROM LMB_UMGVAR WHERE FORM_NAME = \'password_hash\'';
        $rs2 = lmbdb_exec($db, $sqlquery2) or errorhandle(lmbdb_errormsg($db), $sqlquery2, '', __FILE__, __LINE__);
        $hashFunction = PASSWORD_DEFAULT;
        while (lmbdb_fetch_row($rs2)) {
            $hashFunction = (int)lmbdb_result($rs2, 'NORM');
        }

        return $hashFunction ?: PASSWORD_DEFAULT;
    }

    private function checkAttempts(): bool
    {
        $file = TEMPPATH . 'auth/' . md5(Session::getIP());
        if (!file_exists($file)) {
            return true;
        }
        $count = file_get_contents($file);
        if ($count >= 5) {
            if (filemtime($file) <= strtotime('-15 Seconds')) {
                unlink($file);
                return true;
            }
            return false;
        }
        return true;
    }

    private function setAttempt(): void
    {
        $filePath = TEMPPATH . 'auth';
        $file = $filePath . DIRECTORY_SEPARATOR . md5(Session::getIP());

        if (!is_dir($filePath)) {
            if (!mkdir($filePath, 0755, true)) {
                return;
            }
        }

        if (!file_exists($file)) {
            $count = 1;
        } else {
            $count = file_get_contents($file);
            $count++;
        }
        file_put_contents($file, $count);
    }

}
