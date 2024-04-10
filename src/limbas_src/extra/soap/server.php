<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

# include extensions
if($gLmbExt["ext_soap.inc"]){
    foreach ($gLmbExt["ext_soap.inc"] as $key => $extfile){
        require_once($extfile);
    }
}

require_once(COREPATH . 'extra/soap/soap.lib');

set_time_limit(1200); #20min

# --- Zeitmessung ---------------------------------------------------
function is_time(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
$zeit_main = is_time();

# include extensions
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
	"encoding"=>"UTF-8"
	#",soap_version"=>"SOAP_1_2" # dont work with PHP 5.3.8
	));
	$lsrv->addFunction('runlmb');
	$lsrv->handle();
}




?>
