<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\Controllers\ApiController;
use Limbas\Controllers\LegacyController;
use Limbas\lib\http\Route;

Route::all('/main_admin.php', [LegacyController::class, 'mainAdmin'], 'admin.legacy.main');
Route::all('/main_dyns_admin.php', [LegacyController::class, 'mainDynsAdmin'], 'admin.legacy.main-dyns');
Route::all('/main.php', [LegacyController::class, 'main'], 'legacy.main');
Route::all('/main_dyns.php', [LegacyController::class, 'mainDyns'], 'legacy.main-dyns');
Route::all('/main_soap.php', [LegacyController::class, 'mainSoap'],'legacy.main-soap');
Route::all('/main_wsdl.php', [LegacyController::class, 'mainWsdl'], 'legacy.main-wsdl');
Route::all('main_rest.php/{path}', [ApiController::class, 'mainRest'], 'legacy.main-rest',['path' => '.+']);


return Route::getRoutes();
