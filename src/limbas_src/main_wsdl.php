<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



define("IS_WSDL",1);
define('_DEBUG',0);

$auth_user = $_SERVER['PHP_AUTH_USER'];
$auth_pass = $_SERVER['PHP_AUTH_PW'];

#$auth_user = $_REQUEST["auth_user"] = 'soapsoap';
#$auth_pass = $_REQUEST["auth_pass"] = 'soapsoap';

ini_set('session.use_only_cookies',0);

# ------- Limbas include Dateien --------
require_once(__DIR__ . '/lib/session.lib');

ini_set('soap.wsdl_cache_dir',ini_get('session.save_path'));
#ini_set('soap.wsdl_cache_dir', TEMPPATH .'wsdl');

if($session['wsdl_cache']){
	ini_set("soap.wsdl_cache_enabled", 1);
	ini_set("soap.wsdl_cache", 1);
}else{
	ini_set("soap.wsdl_cache_enabled", 0);
}

require_once(COREPATH . 'gtab/gtab.lib');
require_once(COREPATH . 'gtab/gtab_type_erg.lib');
require_once(COREPATH . 'extra/soap/wsdl/soapserver.php');



if ($db) {lmbdb_close($db);}
