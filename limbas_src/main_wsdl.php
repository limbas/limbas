<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6  
 */

/*
 * ID:
 */

define("IS_WSDL",1);
define('_DEBUG',0);

$auth_user = $_SERVER['PHP_AUTH_USER'];
$auth_pass = $_SERVER['PHP_AUTH_PW'];

#$auth_user = $_REQUEST["auth_user"] = 'soapsoap';
#$auth_pass = $_REQUEST["auth_pass"] = 'soapsoap';

ini_set('session.use_only_cookies',0);

# ------- Limbas include Dateien --------
require_once("inc/include_db.lib");
require_once("lib/db/db_".$DBA["DB"].".lib");
require_once("lib/include.lib");
require_once("lib/session.lib");

ini_set('soap.wsdl_cache_dir',ini_get('session.save_path'));
#ini_set('soap.wsdl_cache_dir',$session['path'].'/TEMP/wsdl');

if($session['wsdl_cache']){
	ini_set("soap.wsdl_cache_enabled", 1);
	ini_set("soap.wsdl_cache", 1);
}else{
	ini_set("soap.wsdl_cache_enabled", 0);
}

#require_once("extra/explorer/metadata.lib");
require_once("gtab/gtab.lib");
require_once("gtab/gtab_type_erg.lib");
require_once('extra/soap/wsdl/soapserver.php');



if ($db) {odbc_close($db);}
?>