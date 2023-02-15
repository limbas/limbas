<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
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
