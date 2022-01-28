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

use limbas\rest\ErrorHandler;
use limbas\rest\RestException;
use limbas\rest\Router;

header('Content-type: application/json; charset=utf-8');

require_once('classes/ErrorHandler.php');
require_once('classes/Request.php');
require_once('classes/RequestHandlers/RequestHandler.php');
require_once('classes/RequestHandlers/Delete/DeleteRequestHandler.php');
require_once('classes/RequestHandlers/Delete/DeleteDatasetRequestHandler.php');
require_once('classes/RequestHandlers/Delete/DeleteRelationRequestHandler.php');
require_once('classes/RequestHandlers/Get/GetRequestHandler.php');
require_once('classes/RequestHandlers/Get/GetTableRequestHandler.php');
require_once('classes/RequestHandlers/Get/GetDatasetRequestHandler.php');
require_once('classes/RequestHandlers/Get/GetRelationRequestHandler.php');
require_once('classes/RequestHandlers/Patch/PatchRequestHandler.php');
require_once('classes/RequestHandlers/Patch/PatchDatasetRequestHandler.php');
require_once('classes/RequestHandlers/Patch/PatchRelationRequestHandler.php');
require_once('classes/RequestHandlers/Post/PostRequestHandler.php');
require_once('classes/RequestHandlers/Post/PostTableRequestHandler.php');
require_once('classes/RequestHandlers/Post/PostRelationRequestHandler.php');
require_once('classes/Router.php');
require_once('classes/RestException.php');

require_once('gtab/gtab.lib');
require_once("gtab/gtab_type_erg.lib");

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





try {
    ErrorHandler::registerShutdownHandler();

    $router = new Router();
    $request = $router->getRequest();
    $requestHandler = $request->getHandler();
    $answer = &$requestHandler->getAnswer();
    $json = &json_encode($answer, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if (!$json) {
        throw new RestException('Internal server error', 500);
    }

    ErrorHandler::unregisterShutdownHandler();
    ErrorHandler::checkLmbLog();
    $requestHandler->sendHeader();
    echo $json;

} catch (RestException $e) {
    ErrorHandler::unregisterShutdownHandler();
    # TODO include log
    ErrorHandler::t($e);
}