<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

spl_autoload_register(function ($class) {

    $prefix = 'Limbas\\';
    
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $prefixLength = strlen($prefix);
    $namespace = substr($class, $prefixLength);
    $file = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', '/', $namespace) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});
