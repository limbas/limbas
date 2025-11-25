<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\install;

use DirectoryIterator;
use FilesystemIterator;
use Limbas\lib\db\Database;
use Limbas\lib\db\functions\Dbf;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class Installer
{
    
    public function autoInstall(): bool
    {
        define('LANG', 'en');
        require_once(COREPATH . 'admin/install/install.lib');
        
        echo 'Starting auto install';
        
        $configFile = DEPENDENTPATH . 'inc/include_db.lib';
        $dockerFlag = DEPENDENTPATH . 'inc/docker';
        $isDocker = file_exists($dockerFlag);

        if(!file_exists($configFile)) {
            echo 'Automatic installation not possible without config file.';
            return false;
        }
        
        if(!$isDocker) {
            echo 'Automatic installation is currently only available when using docker.';
            return false;
        }

        $databaseConnection = $this->checkDatabaseStatus(configFile: $configFile);
        if($databaseConnection !== 1) {
            if($databaseConnection === 0) {
                echo 'Auto install could not be completed due to missing database connection.';
            }
            elseif($databaseConnection === 2) {
                echo 'Auto install could not be completed due already installed system.';
            }
            return false;
        }
        
        

        $language = getenv_docker('LIMBAS_LANGUAGE', 2);
        $username = getenv_docker('LIMBAS_USERNAME', null);
        $password = getenv_docker('LIMBAS_PASSWORD', null);
        $package = getenv_docker('LIMBAS_PACKAGE', null);
        
        if(!in_array($language,[1,2])) {
            $language = 2;
        }


        if(empty($username) || empty($password) || empty($package)
            || !file_exists(BACKUPPATH . $package)
        ) {
            echo 'Auto install could not be completed due to missing parameters';
            return false;
        }

        ob_start();

        $this->seedDatabase($language, 1, 'Company name', $username, $password, $package);
        echo 'Database seeded.';
        
        $this->extractDemoFiles($package);

        ob_end_clean();

        echo 'Auto install successfully finished';
        
        return true;
    }

    public function getCurrentStep(Request $request): int
    {
        
        $configFile = DEPENDENTPATH . 'inc/include_db.lib';
        
        $configExists = file_exists($configFile);

        if(!$configExists) {
            return $request->get('step', 1);
        }

        $databaseStatus = $this->checkDatabaseStatus(configFile: $configFile);

        if ($databaseStatus === 2) {
            // db connection was successful and limbas tables were found => fully installed
            return 0;
        } elseif ($databaseStatus === 1) {
            // db connection was successful but no limbas tables were found => not installed but DB connection is good
            return 3;
        } elseif ($databaseStatus === 0) {
            // db connection failed
            return -1;
        }


        return $request->get('step', 1);
    }
    
    public function checkServerRequirements(): int
    {        
        $phpIni = $this->checkPhpIni();
        $status = $this->checkMessages($phpIni);
        
        if($status !== InstallMessage::ERROR) {
            $writePermissions = $this->checkWritePermissions();
            $status = $this->checkMessages($writePermissions);
            if($status !== InstallMessage::ERROR) {
                $dependencies = $this->checkDependencies();
                $status = $this->checkMessages($dependencies);
            }
        }
        
        return $status;
    }
    
    public function checkMessages(array $messages): int
    {
        $status = InstallMessage::OK;
        /** @var InstallMessage $message */
        foreach ($messages as $message) {
            if($message->type === InstallMessage::WARN || $message->type === InstallMessage::OKWARN) {
                $status = InstallMessage::WARN;
            }
            if($message->type === InstallMessage::ERROR) {
                $status = InstallMessage::ERROR;
                break;
            }
        }
        return $status;
    }

    
    public function checkPhpIni(): array
    {
        
        $messages = [];


        # php-version
        if (version_compare(PHP_VERSION, '8.2.0') >= 0) {
            $messages[] = new InstallMessage('PHP Version',InstallMessage::OK, '(' . PHP_VERSION . ')');
        }
        elseif (version_compare(PHP_VERSION, '8.1.0') >= 0) {
            $messages[] = new InstallMessage('PHP Version',InstallMessage::OKWARN, '(' . PHP_VERSION . ' >> >=8.2.0)');
        }
        else {
            $messages[] = new InstallMessage('PHP Version',InstallMessage::ERROR, '(' . PHP_VERSION . ' >> >=8.2.0)');
        }
        
        
        // php-ini - mbstring
        if (function_exists('mb_strlen')) {
            if (ini_get('mbstring.func_overload') > 0) {
                $messages[] = new InstallMessage('mbstring',InstallMessage::OK, iLang('...mbstring present, but func_overload > 0 (must be set to 0)'));
            } else {
                $messages[] = new InstallMessage('mbstring',InstallMessage::OK);
            }
        } else {
            $messages[] = new InstallMessage( 'mbstring', InstallMessage::ERROR, iLang('mbstring extension must be enabled to use utf8'));
        }
        
       
        // php-ini - file_uploads
        if (ini_get('file_uploads')) {
            $messages[] = new InstallMessage('file_uploads',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('file_uploads',InstallMessage::OKWARN, iLang('...must be enabled to activate file uploads'));
        }
        
        // php-ini - upload_max_filesize
        $uploadMaxFilesize = ini_get('upload_max_filesize');
        if ($uploadMaxFilesize) {
            $messages[] = new InstallMessage('upload_max_filesize',InstallMessage::OK, $uploadMaxFilesize);
        } else {
            $messages[] = new InstallMessage('upload_max_filesize',InstallMessage::OKWARN);
        }
        
        // php-ini - func_ini_post
        $postMaxSize = ini_get('post_max_size');
        if ($postMaxSize) {
            $messages[] = new InstallMessage('post_max_size',InstallMessage::OK, $postMaxSize);
        } else {
            $messages[] = new InstallMessage('post_max_size',InstallMessage::OKWARN);
        }
        
        // php-ini - max_input_vars
        $maxInputVars = ini_get('max_input_vars');
        if ($maxInputVars >= 10000) {
            $messages[] = new InstallMessage('max_input_vars',InstallMessage::OK, $maxInputVars);
        } elseif ($maxInputVars) {
            $messages[] = new InstallMessage('max_input_vars',InstallMessage::OKWARN, '<b>' . $maxInputVars . '</b> (>= 10000)');
        }


        // php-ini - defaultlrl
        $odbcDefaultLrl = get_cfg_var('odbc.defaultlrl');
        if ($odbcDefaultLrl) {
            $messages[] = new InstallMessage('odbc.defaultlrl',InstallMessage::OK, $odbcDefaultLrl . ' bytes');
        } else {
            $messages[] = new InstallMessage('odbc.defaultlrl',InstallMessage::OKWARN);
        }


        // php-ini - timezone
        $dateTimezone = ini_get('date.timezone');
        if ($dateTimezone) {
            $messages[] = new InstallMessage('date.timezone',InstallMessage::OK, $dateTimezone);
        } else {
            $messages[] = new InstallMessage('date.timezone',InstallMessage::ERROR, iLang('Must be set! (e.g. Europe/Berlin)'));
        }


        // php-ini - display_errors
        if (ini_get('display_errors')) {
            $messages[] = new InstallMessage('display_errors',InstallMessage::OKWARN, iLang('yes'));
        } else {
            $messages[] = new InstallMessage('display_errors',InstallMessage::OK, iLang('no'));
        }

        // php-ini - log_errors
        $logErrors = ini_get('log_errors');
        if ($logErrors) {
            $messages[] = new InstallMessage('log_errors',InstallMessage::OK, iLang('yes'));
        } else {
            $messages[] = new InstallMessage('log_errors',InstallMessage::OKWARN, iLang('no'));
        }
        
        return $messages;
        
    }
    
    
    public function checkWritePermissions($afterInstallation = false): array
    {

        
        // include_db.lib
        if (file_exists(DEPENDENTPATH . 'inc/include_db.lib')) {
            if (is_writable(DEPENDENTPATH . 'inc/include_db.lib')) {
                if($afterInstallation) {
                    $messages[] = new InstallMessage('inc/include_db',InstallMessage::OKWARN, iLang('... you should set readonly!'));
                }
                else {
                    $messages[] = new InstallMessage('inc/include_db',InstallMessage::OKWARN, iLang('... you should set readonly after installation!'));
                }
            }
            else {
                if($afterInstallation) {
                    $messages[] = new InstallMessage('inc/include_db',InstallMessage::OK);
                }
                else {
                    $messages[] = new InstallMessage('inc/include_db',InstallMessage::ERROR);
                }
            }
        } else {
            $handle = fopen(DEPENDENTPATH . 'inc/include_db.lib', 'wt');
            if (is_resource($handle)) {
                fclose($handle);
                unlink(DEPENDENTPATH . 'inc/include_db.lib');
                $messages[] = new InstallMessage('inc/include_db',InstallMessage::OK, iLang('file can be created'));
                
            } else {
                $messages[] = new InstallMessage('inc/include_db',InstallMessage::ERROR, iLang('file does not exist .. try to create FAILED'));
            }
        }
        
        

        if (is_writable(BACKUPPATH)) {
            $messages[] = new InstallMessage('BACKUP',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('BACKUP',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }

        if (is_writable(TEMPPATH)) {
            $messages[] = new InstallMessage('TEMP',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('TEMP',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }

        if (is_writable(UPLOADPATH)) {
            $messages[] = new InstallMessage('UPLOAD',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('UPLOAD',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }

        if (is_writable(USERPATH)) {
            $messages[] = new InstallMessage('USER',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('USER',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }

        if (is_writable(EXTENSIONSPATH)) {
            $messages[] = new InstallMessage('EXTENSIONS',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('EXTENSIONS',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }
        
        
        
        $adminPath = USERPATH . '1/temp';
        if (!file_exists(USERPATH . '1')) {
            mkdir(USERPATH . '1');
        }
        if (!file_exists($adminPath)) {
            mkdir($adminPath);
        }
        if (is_writable($adminPath)) {
            $messages[] = new InstallMessage('USER/1/temp',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('USER/1/temp',InstallMessage::ERROR, iLang('apache needs recursive write permissions'));
        }
        
        

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(DEPENDENTPATH, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        $failedPaths = [];
        foreach ($iterator as $path => $dir) {
            if ($dir->isDir() && !is_link($path) && !str_contains($path, 'EXTENSIONS')) {
                if (!is_writable($path)) {
                    $failedPaths[] = $path;
                }
            }
        }

        if (empty($failedPaths)) {
            $messages[] = new InstallMessage('dependent/.*',InstallMessage::OK);
        } else {
            $paths = implode('', array_map(fn($p) => '<li>' . $p . '</li>', $failedPaths));
            $messages[] = new InstallMessage('dependent/.*',InstallMessage::OKWARN, iLang('apache might need recursive write permissions') . $paths);
        }
        
        return $messages;
    }
    
    public function checkDependencies(): array
    {


        /* --- php odbc --- */
        $vendorNames = array(
            'PostgreSQL',
            'mysql',
            'MaxDB V7.6 / V7.9',
            'MSSQL',
            'Sybase',
            'HANA',
            'oracle'
        );
        
        $odbcLoaded = extension_loaded('odbc');
        $pdoLoaded = extension_loaded('pdo');
        $pdoMySqlLoaded = extension_loaded('pdo_mysql');
        $pdoPgSqlLoaded = extension_loaded('pdo_pgsql');

        if ($odbcLoaded) {
            $messages[] = new InstallMessage('ODBC',InstallMessage::OK, '<br>' . iLang('You can use ODBC for database connection. Available databases are:') . '<br>' . implode(', ', $vendorNames));
        }
        
        
        if($pdoLoaded && ($pdoMySqlLoaded || $pdoPgSqlLoaded)) {
            $messages[] = new InstallMessage('PDO',InstallMessage::OK, iLang('You can use PDO for database connection.<br>PDO support is only for <b>mysql</b> or <b>PostgreSQL</b>. For other databases use ODBC') );
            if($pdoMySqlLoaded) {
                $messages[] = new InstallMessage('pdo_mysql',InstallMessage::OK);
            }
            if($pdoPgSqlLoaded) {
                $messages[] = new InstallMessage('pdo_pgsql',InstallMessage::OK);
            }
        }
        else {
            $pdoLoaded = false;
        }
        
        if(!$odbcLoaded && !$pdoLoaded) {
            $messages[] = new InstallMessage('Database Connector',InstallMessage::ERROR, iLang('PDO or ODBC are required to connect to the database!'));
        }        
        

        /* --- imagedestroy --- */
        if (function_exists('imagedestroy')) {
            $gdInfo = gd_info();
            $messages[] = new InstallMessage('GDlib',InstallMessage::OK, $gdInfo['GD Version']);
            $messages[] = new InstallMessage('GDlib - FreeType Support',$gdInfo['FreeType Support'] ? InstallMessage::OK : InstallMessage::WARN, $gdInfo['FreeType Support']);
            $messages[] = new InstallMessage('GDlib - GIF Read Support',$gdInfo['GIF Read Support'] ? InstallMessage::OK : InstallMessage::WARN);
            $messages[] = new InstallMessage('GDlib - GIF Create Support',$gdInfo['GIF Create Support'] ? InstallMessage::OK : InstallMessage::WARN);
            $messages[] = new InstallMessage('GDlib - JPG Support',$gdInfo['JPG Support'] ? InstallMessage::OK : InstallMessage::WARN);
            $messages[] = new InstallMessage('GDlib - PNG Support',$gdInfo['PNG Support'] ? InstallMessage::OK : InstallMessage::WARN);
        } else {
            $messages[] = new InstallMessage('GDlib',InstallMessage::WARN);
        }
        
        

        /* --- imagemagick --- */
        chdir(COREPATH . 'admin/install/');

        $cmd = 'convert --version';
        $imagickVersionString = shell_exec($cmd);
        $imagickVersionParts = explode("\n", $imagickVersionString);
        $imagickVersion = $imagickVersionParts[0] ?? '';

        $cmd = "convert -auto-orient -thumbnail 'x30>' -gravity center -extent x30 " .
            COREPATH . 'admin/install/testfiles/test.jpg ' .
            TEMPPATH . 'test.png';

        exec($cmd . ' 2>/dev/null');

        if (is_file(TEMPPATH . 'test.png')) {
            $messages[] = new InstallMessage('ImageMagick',InstallMessage::OK, $imagickVersion);
        } elseif ($imagickVersion) {
            $messages[] = new InstallMessage('ImageMagick',InstallMessage::WARN, '(version: ' . $imagickVersion . ')<br>V 6.3.x or higher needed!');
        } else {
            $messages[] = new InstallMessage('ImageMagick',InstallMessage::WARN);
        }
        if (file_exists(TEMPPATH . 'test.png')) {
            unlink(TEMPPATH . 'test.png');
        }

        
        // imap
        if (function_exists('imap_open')) {
            $messages[] = new InstallMessage('imap',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('imap',InstallMessage::WARN);
        }

        // intl
        if (function_exists('collator_create')) {
            $messages[] = new InstallMessage('intl',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('intl',InstallMessage::WARN);
        }

        // gzip
        if (function_exists('gzopen')) {
            $messages[] = new InstallMessage('gzip',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('gzip',InstallMessage::WARN);
        }

        // zip
        $out = exec('zip');
        if ($out) {
            $messages[] = new InstallMessage('zip',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('zip',InstallMessage::WARN);
        }
        

        // TODO: pdftotext needed?
        $cmd = 'pdftotext ' . COREPATH . 'admin/install/testfiles/test.pdf ' . TEMPPATH . 'test.txt';
        exec($cmd);
        if (is_file(TEMPPATH . 'test.txt')) {
            $messages[] = new InstallMessage('pdftotext',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('pdftotext',InstallMessage::WARN);
        }
        if (file_exists(TEMPPATH . 'test.txt')) {
            unlink(TEMPPATH . 'test.txt');
        }
        

        // TODO: pdftohtml needed?
        $cmd = 'pdftohtml ' . COREPATH . 'admin/install/testfiles/test.pdf ' . TEMPPATH. 'test.html';
        exec($cmd);
        if (is_file(TEMPPATH . 'test.html')) {
            $messages[] = new InstallMessage('pdftohtml',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('pdftohtml',InstallMessage::WARN);
        }
        if (file_exists(TEMPPATH . 'test.html')) {
            unlink(TEMPPATH . 'test.html');
            unlink(TEMPPATH . 'tests.html');
            unlink(TEMPPATH . 'test_ind.html');
        }
        

        // exiftool
        $cmd = 'exiftool -ver';
        $exiftool = shell_exec($cmd);
        $exiftoolVer = explode('.', $exiftool);
        if ($exiftool && $exiftoolVer[0] >= 9) {
            $messages[] = new InstallMessage('exiftool',InstallMessage::OK, $exiftool);
        } else {
            $messages[] = new InstallMessage('exiftool',InstallMessage::WARN, "($exiftool < 9)");
        }
        
        

        // ghostscript
        $cmd = 'cd ' . COREPATH . 'admin/install/testfiles/; gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=' . TEMPPATH . 'test.pdf test.pdf';
        exec($cmd);
        if (is_file(TEMPPATH . 'test.pdf')) {
            $messages[] = new InstallMessage('ghostscript',InstallMessage::OK);
        } else {
            $messages[] = new InstallMessage('ghostscript',InstallMessage::WARN);
        }
        if (file_exists(TEMPPATH . 'test.pdf')) {
            unlink(TEMPPATH . 'test.pdf');
        }
        
        
        return $messages;
    }


    private function checkDatabaseConfig(array $config = [], ?string $configFile = null): bool
    {
        global $DBA;
        $DBA = [];

        if (empty($configFile)) {

            $DBA['DBSCHEMA'] = $config['schema'];
            $DBA['DBNAME'] = $config['name'];

            $DBA['DB'] = $config['vendor'];

            if (!$DBA['DB']) {
                $DBA['DB'] = 'postgres';
            }

            $DBA['ODBCDRIVER'] = $config['driver'];

            $DBA['DBUSER'] = $config['user'];
            $DBA['DBPASS'] = $config['password'];

            $DBA['DBHOST'] = $config['host'];
            $DBA['PORT'] = $config['port'];

        } else {
            require $configFile;
        }

        //check if all keys are present
        $keys = ['DBHOST', 'DBNAME', 'DBUSER', 'DBPASS', 'ODBCDRIVER', 'PORT'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $DBA)) {
                return false;
            }
        }
        
        return true;
    }
    
    public function checkDatabaseStatus(array $config = [], string $configFile = null): int
    {
        global $DBA;

        if(!$this->checkDatabaseConfig($config, $configFile)) {
            return 0;
        }
        

        if ($DBA['ODBCDRIVER'] === 'PDO') {
            require_once(COREPATH  . 'lib/db/db_pdo.lib');
        } else {
            require_once(COREPATH  . 'lib/db/db_odbc.lib');
        }

        try {

            require_once(COREPATH  . 'lib/db/db_' . $DBA['DB'] . '.lib');

            ob_start();
            $db = Database::get();
            ob_end_clean();

            if($db === false) {
                return 0;
            }
            
            // check if limbas is installed
            $sql = 'SELECT * FROM LMB_UMGVAR';
            $rs1 = lmbdb_exec($db, $sql);
            if ($rs1) {
                // db connection was successful and limbas tables were found
                return 2;
            } else {
                //db connection was successful but no limbas tables were found
                return 1;
            }
            
        } catch (Throwable) {}

        return 0;
    }
    
    public function testDatabase(array $config = [], string $configFile = null): array|false
    {
        global $DBA;

        if(!$this->checkDatabaseConfig($config, $configFile)) {
            return false;
        }

        if ($DBA['ODBCDRIVER'] === 'PDO') {
            require_once(COREPATH  . 'lib/db/db_pdo.lib');
        } else {
            require_once(COREPATH  . 'lib/db/db_odbc.lib');
        }
        
        

        require_once(COREPATH  . 'lib/db/db_' . $DBA['DB'] . '.lib');
        require_once(COREPATH  . 'lib/db/db_' . $DBA['DB'] . '_admin.lib');
        require_once(COREPATH  . 'lib/include.lib');
        require_once(COREPATH  . 'lib/include_mbstring.lib');
        

        $messages = [];
        
        try {

            ob_start();
            $db = Database::get();
            ob_end_clean();

            if ($DBA['DB'] === 'mysql' and $DBA['ODBCDRIVER'] !== 'PDO') { // check for lower case table names
                $sql = 'SELECT CASE WHEN @@lower_case_table_names = 1 THEN 1 ELSE 0 END AS CSENSITIV';
                $rs = lmbdb_exec($db, $sql);
                
                if ($rs && lmbdb_result($rs, 'CSENSITIV') == 1) {
                    $messages[] = new InstallMessage('Lower case table names',InstallMessage::ERROR, iLang('configure mysql without lower_case_table_names = 1 in /etc/my.cnf'));
                } else {
                    $messages[] = new InstallMessage('Lower case table names',InstallMessage::OK);
                }
                
            } elseif ($DBA['DB'] == 'postgres') {

                $sqlquery2 = 'SHOW SYNCHRONOUS_COMMIT';
                $rs2 = lmbdb_exec($db, $sqlquery2) or errorhandle(lmbdb_errormsg($db), $sqlquery2, 'install', __FILE__, __LINE__);
                $SYNCHRONOUS_COMMIT = lmbdb_result($rs2, 'SYNCHRONOUS_COMMIT');

                // check version
                $sqlquery3 = 'SHOW SERVER_VERSION';
                $rs3 = lmbdb_exec($db, $sqlquery3) or errorhandle(lmbdb_errormsg($db), $sqlquery3, '', __FILE__, __LINE__);
                $version = lmbdb_result($rs3, 'SERVER_VERSION');

                if ( floatval($version) >= 10) {
                    $messages[] = new InstallMessage('Version',InstallMessage::OK, $version . ' (synchronous_commit = ' . $SYNCHRONOUS_COMMIT . ')');
                }else{
                    $messages[] = new InstallMessage('Version',InstallMessage::OK, 'V. '.$version.' - Version must be >= 10');
                }
                
                $rs = Database::select('pg_database',['datname', 'datcollate'], ['DATNAME'=>$DBA['DBNAME']]);
                $collate = lmbdb_result($rs, 'datcollate');

                if (!$rs) {
                    $messages[] = new InstallMessage('Localisation', InstallMessage::OKWARN, 'Could not be checked.');
                }
                elseif($collate === 'C') {
                    $messages[] = new InstallMessage('Localisation',InstallMessage::OK);
                }
                else {
                    $messages[] = new InstallMessage('Localisation',InstallMessage::ERROR, "wrong localisation! Create database cluster or database with locale=C or alternative create database with: create database {{$DBA['DBNAME']}} WITH ENCODING 'UTF-8' LC_COLLATE 'C' LC_CTYPE 'C' OWNER {{$DBA['DBUSER']}} TEMPLATE template0;");
                }

            }

            lmb_StartTransaction(1);
            
            $sqlTest = [
                'CREATE' => 'CREATE TABLE ' . Dbf::handleCaseSensitive('LIMBASTEST') . ' (ID ' . LMB_DBTYPE_INTEGER . ',ERSTDATUM ' . LMB_DBTYPE_TIMESTAMP . ' DEFAULT ' . LMB_DBDEF_TIMESTAMP . ',TXT ' . LMB_DBTYPE_VARCHAR . '(6))',
                'INSERT' => 'INSERT INTO LIMBASTEST (ID,TXT)  VALUES (1,\'a1ä2Ü3\')',
                'SELECT' => 'SELECT * FROM LIMBASTEST',
                'DELETE' => 'DELETE FROM LIMBASTEST',
                'DROP' => 'DROP TABLE ' . Dbf::handleCaseSensitive('LIMBASTEST')
            ];
            
            
            foreach ($sqlTest as $test => $sql) {
                
                // run odbc cursor check before delete
                if($test === 'DELETE' && $DBA['ODBCDRIVER'] !== 'PDO') {
                    $sqlCursor = 'SELECT * FROM LIMBASTEST';
                    $rs = lmbdb_exec($db, $sqlCursor);
                    $cursor = false;
                    if (lmbdb_fetch_row($rs, 1)) {
                        lmbdb_result($rs, 'ID');
                        if (lmbdb_fetch_row($rs, 1)) {
                            if (lmbdb_result($rs, 'ID')) {
                                $cursor = true;
                            } else {
                                $cursor = false;
                            }
                        }
                    }
                    if ($cursor) {
                        $messages[] = new InstallMessage('CURSOR',InstallMessage::OK);
                    }
                    else {
                        $messages[] = new InstallMessage('CURSOR',InstallMessage::ERROR);
                    }
                }
                
                $rs = lmbdb_exec($db, $sql);
                if ($rs) {
                    $messages[] = new InstallMessage($test,InstallMessage::OK);
                } else {
                    $messages[] = new InstallMessage($test,InstallMessage::ERROR);
                }
            }


            lmb_EndTransaction(false);
            lmbdb_close($db);            
            
        } catch (Throwable $t) {
            $messages[] = new InstallMessage('Error',InstallMessage::ERROR, iLang('Error during database check') . ': ' . $t->getMessage());
        }

        return $messages;
    }

    public function writeConfigFile(array $config): bool
    {
        
        $configFile = DEPENDENTPATH . 'inc/include_db.lib';
        
        $config['version'] = '';
        

        $dbVendor = $config['vendor'];

        # database spec
        if (empty($config['schema'])) {
            if (lmb_substr($dbVendor, 0, 5) === 'maxdb') {
                $config['schema'] = lmb_strtoupper($config['user']);
                $config['setup_dbpath'] = '/opt/sdb/programs/bin';
            } elseif ($dbVendor === 'postgres') {
                $config['schema'] = 'public';
            } elseif ($dbVendor === 'mysql') {
                $config['schema'] = $config['name'];
            } elseif ($dbVendor === 'ingres') {
                $config['schema'] = 'ingres';
            } elseif ($dbVendor === 'mssql') {
                $config['schema'] = 'dbo';
            }
            else {
                $config['schema'] = 'public';
            }
        }

        # --- update include_db.lib ----------------------------
        $enc_key = bin2hex(random_bytes(32));

        $config =   '<?php' . "\n\n" .
            '$DBA[\'DB\']       = \'' . $config['vendor'] . '\';                   /* maxdb76 | masbd77 | postgres | ingres */' . "\n" .
            '$DBA[\'DBCUSER\']  = \'' . $config['user'] . '\'; 	    /* DB control user */' . "\n" .
            '$DBA[\'DBCPASS\']  = \'' . $config['password'] . '\'; 		/* DB control password */' . "\n" .
            '$DBA[\'DBCNAME\']  = \'' . $config['name'] . '\'; 	/* DB control name */' . "\n" .
            '$DBA[\'DBUSER\']   = \'' . $config['user'] . '\';		/* DB username */' . "\n" .
            '$DBA[\'DBPASS\']   = \'' . $config['password'] . '\';       /* DB password */' . "\n" .
            '$DBA[\'DBNAME\']   = \'' . $config['name'] . '\';		/* DB instance name */' . "\n" .
            '$DBA[\'DBSCHEMA\'] = \'' . $config['schema'] . '\';		/* DB schema */' . "\n" .
            '$DBA[\'DBHOST\']   = \'' . $config['host'] . '\';			/* DB hostname or IP */' . "\n" .
            '$DBA[\'LMHOST\']   = \'' . $config['host'] . '\';			/* LIMBAS hostname or IP */' . "\n" .
            '$DBA[\'DBPATH\']   = \'' . $config['setup_dbpath'] . '\';	    /* Path to database */' . "\n" .
            '$DBA[\'ODBCDRIVER\'] = \'' . $config['driver'] . '\';	/* unixODBC Driver */' . "\n" .
            '$DBA[\'PORT\']     = \'' . $config['port'] . '\';	    /* database Port */' . "\n" .
            '$DBA[\'CHARSET\']  = \'UTF-8\';	        /* limbas charset */' . "\n" .
            'define(\'LMB_ENC_KEY\',\'' . $enc_key . '\');                  /* generated encryption key */';
        
        return file_put_contents($configFile, $config) !== false;
    }
    
    public function getInstallableDataPackages(): array
    {
        $packages = [];
        foreach(new DirectoryIterator(BACKUPPATH) as $item) {
            if (!$item->isDot() && $item->isFile() && $item->getExtension() === 'gz') {
                $packages[] = $item->getFilename();
            }
        }
        rsort($packages);
        
        return $packages;
    }
    
    
    public function seedDatabase(int $language, int $dateFormat, string $company, string $username, string $password, string $package): void
    {        
        global $backupdir;
        global $session, $umgvar;
        global $db;
        global $DBA;
        global $action;
        global $install;

        $install = true;
        $configFile = DEPENDENTPATH . 'inc/include_db.lib';

        $backupdir = $package;
        

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
        require_once( COREPATH . 'lib/include_mbstring.lib');
        require_once( COREPATH . 'extra/lmbObject/log/LimbasLogger.php');

        $session['timezone'] = 'Europe/Berlin';
        if ($language === 2) {
            $session["setlocale"] = 'en_EN';
        } else {
            $session["setlocale"] = 'de_DE';
        }
        
        setlocale(LC_ALL, $session['setlocale']);
        date_default_timezone_set($session['timezone']);
        
        
        $db = Database::get();

        // import tables
        unset($commit);
        $GLOBALS['umgvar']['pfad'] = DEPENDENTPATH;
        $action = 'install';

        require_once(COREPATH . 'admin/tools/import.dao');
        import_complete(1,isInstaller: true);



        // update umgvar

        Database::update('LMB_UMGVAR',['NORM'=>$company], ['FORM_NAME'=>'company']);
        Database::update('LMB_UMGVAR',['NORM'=>DEPENDENTPATH], ['FORM_NAME'=>'path']);
        Database::update('LMB_UMGVAR',['NORM'=>'localhost:///' . BACKUPPATH], ['FORM_NAME'=>'backup_default']);
        Database::update('LMB_UMGVAR',['NORM'=>$language], ['FORM_NAME'=>'default_language']);
        Database::update('LMB_USERDB',['LANGUAGE'=>$language], []);
        Database::update('LMB_UMGVAR',['NORM'=>$dateFormat], ['FORM_NAME'=>'default_dateformat']);
        Database::update('LMB_USERDB',['DATEFORMAT'=>$dateFormat], []);
        Database::update('LMB_UMGVAR',['NORM'=>$DBA['VERSION']], ['FORM_NAME'=>'database_version']);
        Database::update('LMB_UMGVAR',['NORM'=>''], ['FORM_NAME'=>'url']);
        Database::update('LMB_UMGVAR',['NORM'=>'default'], ['FORM_NAME'=>'server_auth']);


        // update user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        Database::update('LMB_USERDB',['USERNAME'=>$username,'PASSWORT'=>$hashedPassword], ['ID'=>1]);
        
        
        if ($db) {
            lmbdb_close($db);
        }


    }

    
    public function extractDemoFiles(string $package): bool
    {
        $output = true;
        if ($package === 'demo.tar.gz') {
            $demoExtPath = EXTENSIONSPATH . 'demo.tar.gz';
            if (file_exists($demoExtPath)) {
                $success = system("tar xzf '$demoExtPath' -C '" . EXTENSIONSPATH . "' && echo 1");
                if (!$success) {
                    $output = false;
                }
            }
            $demoAssetsPath = LOCALASSETSPATH . 'demo.tar.gz';
            if (file_exists($demoAssetsPath)) {
                $success = system("tar xzf '$demoAssetsPath' -C '" . LOCALASSETSPATH . "' && echo 1");
                if (!$success) {
                    $output = false;
                }
            }
        }
        return $output;
    }


}
