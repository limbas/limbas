<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\Controllers;

use Limbas\extra\rest\classes\ErrorHandler;
use Limbas\extra\rest\classes\RestException;
use Limbas\extra\rest\classes\Router;
use Limbas\lib\http\Headers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


# TODO:
# Verknüpfungsobjekte: attributes
# Sonderfeldtypen
# Versionierung
# Delete auf ganze Tabelle nur mit Filter erlauben
# 1:1 table relation: später


# ?id=1
# ?id[eq]=1
# ?kontaktetest[id]=1
# ?kontaktetest[id][eq]=1

class ApiController extends AbstractController
{
    
    public function get(Request $request, string $path) : Response
    {
        return $this->handle($path, 'GET');
    }

    public function post(Request $request, string $path) : Response
    {
        return $this->handle($path, 'POST');
    }

    public function put(Request $request, string $path) : Response
    {
        return $this->patch($request, $path);
    }

    public function patch(Request $request, string $path) : Response
    {
        return $this->handle($path, 'PATCH');
    }

    public function delete(Request $request, string $path) : Response
    {
        return $this->handle($path, 'DELETE');
    }

    public function mainRest(): Response
    {
        error_log('/main_rest.php is deprecated. Use /api instead.');

        $path = Router::getLegacyPath();
        return $this->handle($path, $_SERVER['REQUEST_METHOD']);
    }
    
    
    public function handle(string $path, string $method): JsonResponse
    {
        define('IS_REST', true);

        require_once(COREPATH . 'gtab/gtab.lib');
        require_once(COREPATH . 'gtab/gtab_type_erg.lib');
        
        $headers = [];
        $responseCode = 200;
        $controlHeaders = (new Headers())->getAccessControlHeaders();
        try {
            ErrorHandler::registerShutdownHandler();

            $router = new Router($path, $method);
            $request = $router->getRequest();
            $requestHandler = $request->getHandler();
            $response = $requestHandler->getAnswer();

            ErrorHandler::unregisterShutdownHandler();
            ErrorHandler::checkLmbLog();

            $headers = $requestHandler->getHeaders();
            $responseCode = $requestHandler->getResponseCode();

        } catch (RestException $e) {
            ErrorHandler::unregisterShutdownHandler();
            ErrorHandler::addError($e->getCode(), $e->getMessage());
            $response = ErrorHandler::getErrors();
        }
        
        $headers = array_merge($headers, $controlHeaders);
        
        return $this->json($response,$responseCode,$headers)->setEncodingOptions( JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
