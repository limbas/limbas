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
spl_autoload_register(function ($className) {
	if (file_exists($className . '.php')) { 
		require_once $className . '.php';
		return true;
	}
// 	else {
// 		throw new Exception('Class \'' . $className . '\' not found', 0x0001);
// 	}
	return false;
});

include 'LimbasDefinition.php';


try {
	$client = new SoapClient('http://cwi:asdf@localhost/limbas/soapserver.php?Service=Personen&WSDL', array(
			'trace'=> true,
			'exceptions' => true,
			'cache_wsdl' => WSDL_CACHE_NONE,
			//'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
			'classmap' => array('Personen', 'PersonenArray', 'PersonenQuery', 'PersonenJoin', 'Korrespondenz', 'KorrespondenzQuery', 'InsertResult', 'InsertResultArray'),
					
			'login' => 'cwi',
			'password' => 'asdf'
	));
}
catch (SoapFault $soapFault) {
	echo '<pre>new SoapClient '; var_dump($soapFault); echo '</pre>';
	exit();
}

try {
	echo '-------------- query ---------------<br/>';

	$query = CLimbasSoapFactory::getInstance()->createQuery('Personen');
	echo " query: all<br/><pre>";
	$personen = $client->query();
	echo 'Cookies: ' . print_r($client->_cookies, true);
	//$headers = getHeaderValues(explode("\r\n",$client->__getLastResponseHeaders()));
	//print_r($headers) . "\n";
	$client->__setCookie('PHPSESSID', $client->_cookies['PHPSESSID'][0]);
	echo '<pre>' . print_r($personen, true) . '</pre>';
// 	foreach($personen as $soapVar) {
// 		$person = $soapVar->enc_value;
// 		echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
// 	}
	echo '</pre><br/>';
	
/*
	$query = CLimbasSoapFactory::getInstance()->createQuery('Personen');
	$query->VORNAME = 'Christian';
	echo " query: '" . $query->VORNAME . "', '" . $query->NAME . "'<br/><pre>";
	$personen = $client->query($query);
	echo 'Cookies: ' . print_r($client->_cookies, true);
	//$headers = getHeaderValues(explode("\r\n",$client->__getLastResponseHeaders()));
	//print_r($headers) . "\n";
	//echo '<pre>' . print_r($personen, true) . '</pre>';
	foreach($personen as $soapVar) {
		$person = $soapVar->enc_value;
		echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	}
	echo '</pre><br/>';

	$query = CLimbasSoapFactory::getInstance()->createQuery('Personen');
	$query->NAME = 'Wittkowski';
	echo " query: '" . $query->VORNAME . "', '" . $query->NAME . "'<br/><pre>";
	$personen = $client->query($query);
	echo 'Cookies: ' . print_r($client->_cookies, true);
	//echo '<pre>' . print_r($personen, true) . '</pre>';
	foreach($personen as $soapVar) {
		$person = $soapVar->enc_value;
		echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	}
	echo '</pre>';
*/	
}
catch (SoapFault $soapFault) {
	echo '<pre>'; var_dump($soapFault); echo '</pre>';
	echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($client->__getLastResponse()), "<br>";
}


try {
	echo '-------------- insert "Katrina Westhagen" ---------------<br/><pre>';
	$person = CLimbasSoapFactory::getInstance()->createTable('Personen');
	$person->VORNAME = 'Katrina';
	$person->NAME = 'Westhagen';
	$items = new PersonenArray();
	$items->items[] = $person;
	$person = CLimbasSoapFactory::getInstance()->createTable('Personen');
	$person->VORNAME = 'Moritz';
	$person->NAME = 'Westhagen';
	$items->items[] = $person;
	$result = $client->insert($items);
	echo '<pre>' . print_r($result, true) . '</pre>';
	echo '</pre>';
}
catch (SoapFault $soapFault) {
	echo '<pre>'; var_dump($soapFault); echo '</pre>';
	echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($client->__getLastResponse()), "<br>";
}
exit();
try {
	echo '-------------- getByPk(-1) ---------------<br/><pre>';
	$person = $client->getByPk(-1);
	echo 'Cookies: ' . print_r($client->_cookies, true);
	echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	echo '</pre>';
}
catch (SoapFault $soapFault) {
	echo '<pre>'; var_dump($soapFault); echo '</pre>';
	echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($client->__getLastResponse()), "<br>";
}

try {
	echo '-------------- getByPk(1) ---------------<br/><pre>';
	$person = $client->getByPk(1);
	echo 'Cookies: ' . print_r($client->_cookies, true);
	echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	echo '</pre>';
	echo '-------------- update(1, "Christian" => "Sonja") ---------------<br/><pre>';
	$person->VORNAME = 'Sonja';
	echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	$client->update($person);
	echo '</pre>';
	echo '-------------- getByPk(1) ---------------<br/><pre>';
	$person = $client->getByPk(1);
	echo 'Cookies: ' . print_r($client->_cookies, true);
	echo '   ' . $person->ID . ' ' . $person->VORNAME . ' ' . $person->NAME . "\n";
	echo '</pre>';
}
catch (SoapFault $soapFault) {
	echo '<pre>'; var_dump($soapFault); echo '</pre>';
	echo "Request :<br>", htmlentities($client->__getLastRequest()), "<br>";
	echo "Response :<br>", htmlentities($client->__getLastResponse()), "<br>";
}
echo '</pre>';

function getHeaderValues($header){

	if(! lmb_strpos($header[0], '200')){
		//echo '<pre>' . print_r($header, true) . '</pre>';
		header($header[0], true);
		exit;
	}

	$headers = array();
	foreach($header as $key => $val){
		$val = trim($val);
		$pos = lmb_strpos($val, ':');
		if (false !== $pos) {
			$name = lmb_substr($val, 0, $pos);
			$val = lmb_substr($val, $pos + 1);
			echo $name . ': ' . $val . "\n";
			$headers[$name] = $val;
			if ('Set-Cookie' === $name) {
				if (!isset($headers['cookies'])) {
					$headers['cookies'] = array();
				}
				$cookiedata = explode(';', $val);
				$cookie = explode('=', trim($cookiedata[0]));
				$cookiepath  = explode('=', trim($cookiedata[1]));
				$headers['cookies'][$cookie[0]] = array('name' => $cookie[0], 'value' => $cookie[1], 'path' => $cookiepath[1]);
			}
		}
	}
/*
	$headers = array();
	foreach($header as $key => $val){
		$nameVal = explode(';', trim($val));
		if($nameVal){
			foreach ($nameVal as $key2 => $val2){
				$nameVal2 = explode(':',trim($val2));
				if($nameVal2 && isset($nameVal2[1]) && trim($nameVal2[1])){
					$headers[$nameVal2[0]] = preg_replace("[\"']","",trim($nameVal2[1]));
				}
				$nameVal2 = explode('=',trim($val2));
				if($nameVal2 && isset($nameVal2[1]) && trim($nameVal2[1])){
					$headers[$nameVal2[0]] = preg_replace("[\"']","",trim($nameVal2[1]));
				}
			}
		}
	}
*/
	return $headers;
}