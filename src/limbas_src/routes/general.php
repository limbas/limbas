<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\Controllers\ApiController;
use Limbas\Controllers\DefaultController;
use Limbas\lib\http\Route;

Route::get('/', [DefaultController::class, 'index'], 'index');
Route::post('/', [DefaultController::class, 'index'], 'index.post');


Route::all('api/{path}', ApiController::class, 'api',['path' => '.+']);


return Route::getRoutes();
