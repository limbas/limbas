<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */
#defined('_DEBUG') or define('_DEBUG', true);

//function __autoload($className) {
spl_autoload_register(function ($className) {
	if (file_exists($umgvar['url']."extra/soap/wsdl/$className" . '.php')) { 
		require_once $umgvar['url'].'extra/soap/wsdl/'.$className . '.php';
	}
// 	else {
// 		throw new Exception('Class \'' . $className . '\' not found', 0x0001);
// 	}
	return false;
});


$login = '';
if((isset($_SERVER['PHP_AUTH_USER']))) {
	$login = $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW'] . '@';
}



if($_SERVER['HTTPS']){
	$baseUrl = "https://".$login.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}else{
	$baseUrl = "http://".$login.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}


$classMap = null;
$serviceOptions = array();



if (!isset($_GET['Service'])) {
	header('HTTP/1.0 400 Bad Request', true, 400);
	exit;
}
else {
	try {
		$service = new CLimbasSoapService($_GET['Service'], $baseUrl);
		if(is_array($classMap)) {
			$service->classMap = $classMap;
		}
		foreach($serviceOptions as $name => $value) {
			$service->$name = $value;
		}
		if(isset($_GET['WSDL'])) {
			$service->renderWsdl();
		}
		else {
			$service->run();
		}
	}
	catch(Exception $e) {
		error_log(print_r($e,1));
		header('HTTP/1.0 404 Not Found', true, 404);
		exit;
	}
}