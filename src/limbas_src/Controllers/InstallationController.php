<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\Controllers;

use Limbas\admin\install\Installer;
use Limbas\admin\install\InstallMessage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class InstallationController extends DefaultController
{

    public function index(Request $request): Response
    {
        define('LIMBAS_INSTALL', true);
        
        $this->handleLang($request);

        $installer = new Installer();
        
        $currentStep = $installer->getCurrentStep($request);
        
        require_once(COREPATH . 'admin/install/install.lib');

        return match ($currentStep) {
            1 => $this->step1(),
            2 => $this->step2($request),
            3 => $this->step3($request),
            default => $this->error(404),
        };
    }
    
    public function redirectToTrailingSlash(Request $request): RedirectResponse
    {        
        return new RedirectResponse($request->getBaseUrl() . '/install/',301);
    }
    
    
    private function handleLang(Request $request): void
    {
        $language = $request->get('lang');
        
        $available = ['en','de','fr'];
        if ( empty($language) && isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ) {
            $languages = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );
            foreach ( $languages as $lang ){
                $lang = substr( $lang, 0, 2 );
                if( in_array( $lang, $available ) ) {
                    $language = $lang;
                    break;
                }
            }
        }

        switch ($language) {
            case 'de':
                require_once COREPATH . 'admin/install/lang/de.php';
                define('LANG', 'de');
                break;
            case 'fr':
                require_once COREPATH . 'admin/install/lang/fr.php';
                define('LANG', 'fr');
                break;
            default:
                define('LANG', 'en');
        }
    }

    /**
     * Step 1: check if server fulfills requirements
     * 
     * @return Response
     */
    private function step1(): Response
    {
        $dockerFlag = DEPENDENTPATH . 'inc/docker';
        $isDocker = file_exists($dockerFlag);

        if($isDocker) {
            $serverStatus = InstallMessage::OK;
        }
        else {
            $installer = new Installer();
            $serverStatus = $installer->checkServerRequirements();
            $phpIniMessages = $installer->checkPhpIni();
            $writePermissionMessages = $installer->checkWritePermissions();
            $dependencyMessages = $installer->checkDependencies();
        }
        
        $version = '';
        if(file_exists(COREPATH . 'lib/version.inf')){
            $fullVersion = file_get_contents(COREPATH . 'lib/version.inf');
            $version = substr($fullVersion,0,strrpos($fullVersion,'.'));
        }
        
        ob_start();
        $contentFile = COREPATH . 'admin/install/html/step1.php';
        require COREPATH . 'admin/install/html/html.php';
        return new Response(ob_get_clean() ?: '');
    }

    /**
     * Step 2: database settings
     *
     * @param Request $request
     * @return Response
     */
    private function step2(Request $request): Response
    {
        $installer = new Installer();

        $dockerFlag = DEPENDENTPATH . 'inc/docker';
        $isDocker = file_exists($dockerFlag);

        if(!$isDocker) {
            $serverStatus = $installer->checkServerRequirements();
            if($serverStatus === InstallMessage::ERROR) {
                return $this->step1();
            }
        }
        
        
        
        $shouldValidate = $request->get('validate', false);
        $connectionMethod = $request->get('connection', 'pdo');
        $dbVendor = $request->get('vendor', '');
        $dbHost = $request->get('host', 'localhost');
        $dbPort = $request->get('port', '');
        $dbName = $request->get('name', '');
        $dbUser = $request->get('user', '');
        $dbPassword = $request->get('password', '');
        $dbSchema = $request->get('schema', '');
        $dbDriver = $request->get('driver', '');
        
        $dbVendors = [
            'postgres' => [
                'name' => 'PostgreSQL',
                'pdo' => true,
                'port' => '5432',
                'schema' => 'public',
                'driver' => 'PSQL'
            ],
            'mysql' => [
                'name' => 'MariaDB / MySQL',
                'pdo' => true,
                'port' => '3306',
                'schema' => '',
                'driver' => 'MySQL'
            ],
            'maxdb76' => [
                'name' => 'MaxDB V7.6 / V7.9',
                'pdo' => false,
                'port' => '7210',
                'schema' => '',
                'driver' => 'MAXDBSQL'
            ],
            'mssql' => [
                'name' => 'MSSQL',
                'pdo' => false,
                'port' => '1433',
                'schema' => 'dbo',
                'driver' => 'MSSQL'
            ],
            'hana' => [
                'name' => 'HANA',
                'pdo' => false,
                'port' => '30015',
                'schema' => '',
                'driver' => ''
            ],
            'ingres' => [
                'name' => 'Ingres 10',
                'pdo' => false,
                'port' => '',
                'schema' => 'ingres',
                'driver' => 'IngresSQL'
            ],
            'oracle' => [
                'name' => 'Oracle',
                'pdo' => false,
                'port' => '',
                'schema' => '',
                'driver' => ''
            ]
        ];
        
        if(!array_key_exists($dbVendor, $dbVendors)) {
            $shouldValidate = false;
        }

        $status = null;
        $messages = [];
        $configWritten = true;
        if($shouldValidate) {

            if ($connectionMethod === 'pdo') {
                $dbDriver = 'PDO';
            }
            
            if(empty($dbPort)) {
                $dbPort = $dbVendors[$dbVendor]['port'];
            }
            
            $config = [
                'vendor' => $dbVendor,
                'host' => $dbHost,
                'port' => $dbPort,
                'name' => $dbName,
                'user' => $dbUser,
                'password' => $dbPassword,
                'schema' => $dbSchema,
                'driver' => $dbDriver,
            ];
            
            $databaseStatus = $installer->checkDatabaseStatus($config);
            if($databaseStatus <= 0) {
                $status = InstallMessage::ERROR;
            }
            else {
                $messages = $installer->testDatabase($config);
                $status = $installer->checkMessages($messages);
            }
            
            if($status === InstallMessage::OK) {
                $configWritten = $installer->writeConfigFile($config);
                if(!$configWritten) {
                    $status = InstallMessage::ERROR;
                }
            }
            
        }


        ob_start();
        $contentFile = COREPATH . 'admin/install/html/step2.php';
        require COREPATH . 'admin/install/html/html.php';
        return new Response(ob_get_clean() ?: '');
    }


    /**
     * Step 3: User, system data and data package
     * @param Request $request
     * @return Response
     */
    private function step3(Request $request): Response
    {
        if($request->get('install')) {
            return $this->step4($request);
        }
        
        $installer = new Installer();
        $packages = $installer->getInstallableDataPackages();

        $hasClean = false;
        $cleanIndex = array_search('clean.tar.gz', $packages);
        if($cleanIndex !== false) {
            $hasClean = true;
            unset($packages[$cleanIndex]);
        }

        $language = $request->get('language', 'en');
        $dateFormat = $request->get('dateformat', 1);
        $company = $request->get('company', '');
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $package = $request->get('package', '');
        
        
        ob_start();
        $contentFile = COREPATH . 'admin/install/html/step3.php';
        require COREPATH . 'admin/install/html/html.php';
        return new Response(ob_get_clean() ?: '');
    }


    /**
     * Step 4: Run installation
     * @param Request $request
     * @return Response
     */
    private function step4(Request $request): Response
    {
        $installer = new Installer();

        $language = intval($request->get('language', 2));
        $dateFormat = intval($request->get('dateformat', 1));
        $company = $request->get('company', '');
        $username = $request->get('username', '');
        $password = $request->get('password', '');
        $package = $request->get('package', '');
        
        
        if(!in_array($language,[1,2]) || !in_array($dateFormat,[1,2,3]) || empty($company) || empty($username) || empty($password) || empty($package)
        || !file_exists(BACKUPPATH . $package)
        ) {
            $request->request->remove('install');
            return $this->step3($request);
        }
        

        ob_start();
        $contentFile = COREPATH . 'admin/install/html/step4.php';
        require COREPATH . 'admin/install/html/html.php';
        return new Response(ob_get_clean() ?: '');
    }
}
