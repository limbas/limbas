<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\Controllers\DefaultController;
use Limbas\Limbas;
use Symfony\Component\HttpFoundation\Request;

require_once(__DIR__ . '/bootstrap.php');
require_once (COREPATH . 'lib/include.lib');

$requestUri = $_SERVER['REQUEST_URI'];

if(str_contains($requestUri,'install')) {
    
    $request = Request::createFromGlobals();
    $routes = require __DIR__ . '/routes/install.php';
    $limbas = new Limbas($routes);
    $response = $limbas->handle($request);
    $response->send();
    
    exit(1);
}

$file = basename($requestUri, '?' . $_SERVER['QUERY_STRING']);

// define IS_SOAP here because of remote update via soap
if($file === 'main_soap.php') {
    define('IS_SOAP', true);
}

$request = Request::createFromGlobals();
$routes = require __DIR__ . '/routes/routes.php';
$limbas = new Limbas($routes);

$limbas->authenticate();

try {
    require_once(COREPATH . 'lib/session.lib');
}
catch (Throwable $t) {
    $response = (new DefaultController())->error($t->getCode(), $t, $request);
    lmb_log::error($t->getMessage());
    $response->send();
    exit(1);
}

//check if rest
if (str_contains($requestUri,'main_rest.php')) {
    require COREPATH . 'main/main_rest.php';
    exit(1);
}

$response = $limbas->handle($request);
$response->send();
