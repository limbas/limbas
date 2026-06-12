<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\Controllers\Admin\JobController;
use Limbas\lib\http\Route;

Route::group(['prefix' => 'admin', 'name' => 'admin.'], function () {
    Route::resource('jobs', JobController::class, 'jobs', options: ['exclude' => ['show']]);
    Route::get('jobs/{id}/run', [JobController::class, 'run'])->name('jobs.run');
});

return Route::getRoutes();
