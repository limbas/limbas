<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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


require_once(__DIR__ . '/../lib/session.lib');
require_once(COREPATH . 'gtab/gtab.lib');

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
        new CalDAV\CalendarRoot($principalBackend, $calendarBackend)
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
