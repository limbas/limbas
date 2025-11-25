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


    public static function get(string $path, array $controller, string $name, array $requirements = []): void
    {
        self::addRoute($path, $controller, $name, ['GET', 'HEAD', 'OPTIONS'], $requirements);
    }

    public static function post(string $path, array $controller, string $name, array $requirements = []): void
    {
        self::addRoute($path, $controller, $name, ['POST'], $requirements);
    }

    public static function put(string $path, array $controller, string $name, array $requirements = []): void
    {
        self::addRoute($path, $controller, $name, ['PUT'], $requirements);
    }

    public static function patch(string $path, array $controller, string $name, array $requirements = []): void
    {
        self::addRoute($path, $controller, $name, ['PATCH'], $requirements);
    }

    public static function delete(string $path, array $controller, string $name, array $requirements = []): void
    {
        self::addRoute($path, $controller, $name, ['DELETE'], $requirements);
    }
    
    public static function all(string $path, string $controller, string $name, array $requirements = []): void
    {
        self::get($path, [$controller,'get'], $name.'.get', $requirements);
        self::post($path, [$controller,'post'], $name.'.post', $requirements);
        self::put($path, [$controller,'put'], $name.'.put', $requirements);
        self::patch($path, [$controller,'patch'], $name.'.patch', $requirements);
        self::delete($path, [$controller,'delete'], $name.'.delete', $requirements);
    }

    public static function resource(string $path, string $controller, string $name, array $options = []): void
    {
        $exclude = [];
        if(array_key_exists('exclude', $options)) {
            $exclude = $options['exclude'];
        }

        if(array_key_exists('only', $options)) {
            $possibleRoutes = ['index','show','create','store','edit','update','delete'];
            $exclude = array_diff($possibleRoutes, $options['only']);
        }
        
        if(!in_array('index', $exclude)) {
            self::get($path, [$controller, 'index'], $name . '.index');
        }
        if(!in_array('show', $exclude)) {
            self::get($path . '/{id}', [$controller, 'show'], $name . '.show');
        }
        if(!in_array('create', $exclude)) {
            self::get($path . '/create', [$controller, 'create'], $name . '.create');
        }
        if(!in_array('store', $exclude)) {
            self::post($path, [$controller, 'store'], $name . '.store');
        }
        if(!in_array('edit', $exclude)) {
            self::get($path . '/{id}/edit', [$controller, 'edit'], $name . '.edit');
        }
        if(!in_array('update', $exclude)) {
            self::put($path . '/{id}', [$controller, 'update'], $name . '.update');
        }
        if(!in_array('delete', $exclude)) {
            self::delete($path . '/{id}', [$controller, 'delete'], $name . '.delete');
        }
    }
    

    protected static function addRoute(string $path, array $controller, string $name, array $methods, array $requirements = []): void
    {
        self::initRouteCollection();
        self::$routes->add($name, new SymfonyRoute($path, [
            '_controller' => $controller,
        ], $requirements, methods: $methods));
    }

    private static function initRouteCollection(): void
    {
        if (self::$routes === null) {
            self::$routes = new RouteCollection();
        }
    }

}
