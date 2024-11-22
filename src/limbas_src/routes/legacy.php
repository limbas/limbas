<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\Controllers\LegacyController;
use Limbas\lib\http\Route;


Route::get('/main_admin.php', [LegacyController::class, 'mainAdmin'], 'admin.legacy.main.get');
Route::post('/main_admin.php', [LegacyController::class, 'mainAdmin'], 'admin.legacy.main.post');
Route::put('/main_admin.php', [LegacyController::class, 'mainAdmin'], 'admin.legacy.main.put');
Route::delete('/main_admin.php', [LegacyController::class, 'mainAdmin'], 'admin.legacy.main.delete');

Route::get('/main_dyns_admin.php', [LegacyController::class, 'mainDynsAdmin'], 'admin.legacy.main-dyns.get');
Route::post('/main_dyns_admin.php', [LegacyController::class, 'mainDynsAdmin'], 'admin.legacy.main-dyns.post');
Route::put('/main_dyns_admin.php', [LegacyController::class, 'mainDynsAdmin'], 'admin.legacy.main-dyns.put');
Route::delete('/main_dyns_admin.php', [LegacyController::class, 'mainDynsAdmin'], 'admin.legacy.main-dyns.delete');


Route::get('/main.php', [LegacyController::class, 'main'], 'legacy.main.get');
Route::post('/main.php', [LegacyController::class, 'main'], 'legacy.main.post');
Route::put('/main.php', [LegacyController::class, 'main'], 'legacy.main.put');
Route::delete('/main.php', [LegacyController::class, 'main'], 'legacy.main.delete');

Route::get('/main_dyns.php', [LegacyController::class, 'mainDyns'], 'legacy.main-dyns.get');
Route::post('/main_dyns.php', [LegacyController::class, 'mainDyns'], 'legacy.main-dyns.post');
Route::put('/main_dyns.php', [LegacyController::class, 'mainDyns'], 'legacy.main-dyns.put');
Route::delete('/main_dyns.php', [LegacyController::class, 'mainDyns'], 'legacy.main-dyns.delete');

Route::get('/main_soap.php', [LegacyController::class, 'mainSoap'], 'legacy.main-soap.get');
Route::post('/main_soap.php', [LegacyController::class, 'mainSoap'], 'legacy.main-soap.post');
Route::put('/main_soap.php', [LegacyController::class, 'mainSoap'], 'legacy.main-soap.put');
Route::delete('/main_soap.php', [LegacyController::class, 'mainSoap'], 'legacy.main-soap.delete');

Route::get('/main_wsdl.php', [LegacyController::class, 'mainWsdl'], 'legacy.main-wsdl.get');
Route::post('/main_wsdl.php', [LegacyController::class, 'mainWsdl'], 'legacy.main-wsdl.post');
Route::put('/main_wsdl.php', [LegacyController::class, 'mainWsdl'], 'legacy.main-wsdl.put');
Route::delete('/main_wsdl.php', [LegacyController::class, 'mainWsdl'], 'legacy.main-wsdl.delete');


return Route::getRoutes();
