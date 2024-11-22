<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\http;

use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class Route
{

    protected static ?RouteCollection $routes = null;

    public static function getRoutes(bool $clear = true): RouteCollection
    {
        $outputRoutes = self::$routes;

        if ($clear) {
            self::$routes = null;
        }

        return $outputRoutes ?? new RouteCollection();
    }


    public static function get(string $path, array $controller, string $name): void
    {
        self::addRoute($path, $controller, $name, ['GET', 'HEAD', 'OPTIONS']);
    }

    public static function post(string $path, array $controller, string $name): void
    {
        self::addRoute($path, $controller, $name, ['POST']);
    }

    public static function put(string $path, array $controller, string $name): void
    {
        self::addRoute($path, $controller, $name, ['PUT']);
    }

    public static function delete(string $path, array $controller, string $name): void
    {
        self::addRoute($path, $controller, $name, ['DELETE']);
    }


    protected static function addRoute(string $path, array $controller, string $name, array $methods): void
    {
        self::initRouteCollection();
        self::$routes->add($name, new SymfonyRoute($path, [
            '_controller' => $controller,
        ], methods: $methods));
    }

    private static function initRouteCollection(): void
    {
        if (self::$routes === null) {
            self::$routes = new RouteCollection();
        }
    }

}
