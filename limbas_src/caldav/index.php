<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */
use
Sabre\DAV,
Sabre\CalDAV,
Sabre\DAVACL;
global $session;

$pt = isset($_SERVER['PATH_TRANSLATED']) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
$uri = $_SERVER['REQUEST_URI'];

$uri = $_SERVER['PHP_SELF'];
$uri = str_replace('index.php','',$uri);

$path = explode("/", $pt);
array_pop($path);
array_pop($path);
$path = implode("/",$path);

set_include_path($path);
chdir($path);


require_once('inc/include_db.lib');
require_once('lib/include.lib');
require_once('lib/session.lib');
require_once('gtab/gtab.lib');
require_once('extern/SabreDAV/autoload.php');

require_once 'PrincipalBackend.php';
require_once 'CalendarBackend.php';
require_once 'Auth.php';


// settings
date_default_timezone_set('Europe/Berlin');
$publicDir = '/';
$tmpDir = 'tmpdata';

// No error handler set

//  function exception_error_handler($errno, $errstr, $errfile, $errline ) {
//  if($errfile != '/srv/www/htdocs/limbas26/openlimbas/limbas_src/gtab/gtab.lib'){
//   error_log($errfile);
//   error_log(print_r($errstr,1));
//   }
//   set_error_handler("exception_error_handler");


// Backends
$authBackend = new Sabre\DAV\Auth\Backend\Limbas();
$principalBackend = new DAVACL\PrincipalBackend\Limbas();
$calendarBackend = new CalDAV\Backend\Limbas();

// Directory tree
$tree = array(
        new DAVACL\PrincipalCollection($principalBackend),
        new CalDAV\CalendarRootNode($principalBackend, $calendarBackend)
);      

// The object tree needs in turn to be passed to the server class
$server = new DAV\Server($tree);

// You are highly encouraged to set your WebDAV server base url. Without it,
// SabreDAV will guess, but the guess is not always correct. Putting the
// server on the root of the domain will improve compatibility.
$server->setBaseUri($uri);

// // Authentication plugin
$authPlugin = new DAV\Auth\Plugin($authBackend,'SabreDAV');
$server->addPlugin($authPlugin);

// CalDAV plugin
$caldavPlugin = new CalDAV\Plugin();
$server->addPlugin($caldavPlugin);

// // ACL plugin
$aclPlugin = new DAVACL\Plugin();
$server->addPlugin($aclPlugin);

// Support for html frontend
$browser = new DAV\Browser\Plugin();
$server->addPlugin($browser);

// And off we go!
$server->exec();
