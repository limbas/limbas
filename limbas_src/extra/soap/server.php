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

require_once('extra/soap/soap.lib');

set_time_limit(1200); #20min

# --- Zeitmessung ---------------------------------------------------
function is_time(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
$zeit_main = is_time();

# EXTENSIONS
if($gLmbExt["ext_soap.inc"]){
	foreach ($gLmbExt["ext_soap.inc"] as $key => $value){
		require_once($value);
	}
}

# --- Version 5 -------

if(!$LIM){
	$lsrv = new SoapServer(NULL,array(
	'uri' => "urn:xmethodsLimbasServer",
	"style" => SOAP_RPC,
	"use" => SOAP_ENCODED,
	"compression" => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE,
	"encoding"=>"ISO-8859-1"
	#",soap_version"=>"SOAP_1_2" # dont work with PHP 5.3.8
	));
	$lsrv->addFunction('runlmb');
	$lsrv->handle();
}




?>