<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\http;

use Limbas\Limbas;
use LogicException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class Route
{

    protected static ?RouteCollection $routes = null;

    protected static int $counter = 0;
    private static string $groupPrefix = '';
    private static string $groupNamePrefix = '';


    public static function getRoutes(bool $clear = true): RouteCollection
    {
        $routes = self::$routes ?? new RouteCollection();

        if ($clear) {
            self::$routes = null;
        }

        return $routes;
    }


    public static function get(string $path, array $controller): RouteDefinition
    {
        return self::addRoute($path, $controller, ['GET', 'HEAD', 'OPTIONS']);
    }

    public static function post(string $path, array $controller): RouteDefinition
    {
        return self::addRoute($path, $controller, ['POST']);
    }

    public static function put(string $path, array $controller): RouteDefinition
    {
        return self::addRoute($path, $controller, ['PUT']);
    }

    public static function patch(string $path, array $controller): RouteDefinition
    {
        return self::addRoute($path, $controller, ['PATCH']);
    }

    public static function delete(string $path, array $controller): RouteDefinition
    {
        return self::addRoute($path, $controller, ['DELETE']);
    }

    public static function all(string $path, string|array $controller, string $name, array $requirements = []): void
    {
        $method = null;
        if (is_array($controller)) {
            $method = $controller[1] ?? null;
            $controller = $controller[0];
        }

        self::get($path, [$controller, $method ?? 'get'])->name($name . '.get')->where($requirements);
        self::post($path, [$controller, $method ?? 'post'])->name($name . '.post')->where($requirements);
        self::put($path, [$controller, $method ?? 'put'])->name($name . '.put')->where($requirements);
        self::patch($path, [$controller, $method ?? 'patch'])->name($name . '.patch')->where($requirements);
        self::delete($path, [$controller, $method ?? 'delete'])->name($name . '.delete')->where($requirements);
    }

    public static function resource(string $path, string $controller, string $name, array $options = []): void
    {
        $exclude = [];
        if (array_key_exists('exclude', $options)) {
            $exclude = $options['exclude'];
        }

        if (array_key_exists('only', $options)) {
            $possibleRoutes = ['index', 'show', 'create', 'store', 'edit', 'update', 'delete'];
            $exclude = array_diff($possibleRoutes, $options['only']);
        }
        
        if (!in_array('index', $exclude)) {
            self::get($path, [$controller, 'index'])->name($name . '.index');
        }
        if (!in_array('show', $exclude)) {
            self::get($path . '/{id}', [$controller, 'show'])->name($name . '.show');
        }
        if (!in_array('create', $exclude)) {
            self::get($path . '/create', [$controller, 'create'])->name($name . '.create');
        }
        if (!in_array('store', $exclude)) {
            self::post($path, [$controller, 'store'])->name($name . '.store');
        }
        if (!in_array('edit', $exclude)) {
            self::get($path . '/{id}/edit', [$controller, 'edit'])->name($name . '.edit');
        }
        if (!in_array('update', $exclude)) {
            self::put($path . '/{id}', [$controller, 'update'])->name($name . '.update');
        }
        if (!in_array('delete', $exclude)) {
            self::delete($path . '/{id}', [$controller, 'delete'])->name($name . '.delete');
        }
    }

    public static function group(array $attributes, callable $callback): void
    {
        // Set prefix and name prefix if provided
        $prefix = $attributes['prefix'] ?? '';
        $namePrefix = $attributes['name'] ?? '';

        // Create a temporary context for the group
        self::$groupPrefix = $prefix;
        self::$groupNamePrefix = $namePrefix;

        // Call the callback to register the routes
        $callback();

        // Clear the group context after the callback
        self::$groupPrefix = '';
        self::$groupNamePrefix = '';
    }

    public static function renameRoute(string $oldName, string $newName): void
    {
        self::initRouteCollection();

        $route = self::$routes->get($oldName);

        if (!$route) {
            throw new LogicException("Route [$oldName] does not exist.");
        }

        self::$routes->remove($oldName);
        self::$routes->add(self::$groupNamePrefix . $newName, $route);
    }

    public static function setRequirements(string $routeName, array $newRequirements): void
    {
        self::initRouteCollection();

        $route = self::$routes->get($routeName);

        if (!$route) {
            throw new LogicException("Route [$routeName] does not exist.");
        }

        $route->setRequirements($newRequirements);

        self::$routes->remove($routeName);
        self::$routes->add($routeName, $route);
    }

    public static function url(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        $route = Limbas::$routes->get($name); // self::$routes->get($routeName)
        if (empty($route)) {
            throw new LogicException("Route [$name] does not exist.");
        }

        $routeParameters = [];
        if (!empty($parameters)) {
            $compiledRoute = $route->compile();
            $variables = $compiledRoute->getVariables();
            foreach ($parameters as $key => $value) {
                if (in_array($key, $variables)) {
                    $routeParameters[$key] = $value;
                }
            }
        }

        $urlGenerator = new UrlGenerator(Limbas::$routes, Limbas::$context);
        return $urlGenerator->generate($name, $routeParameters, $schemeRelative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
    }


    protected static function generateInternalName(): string
    {
        return '__route_' . (++self::$counter);
    }

    protected static function register(RouteDefinition $routeDefinition): void
    {
        self::initRouteCollection();

        if (!$routeDefinition->name) {
            throw new LogicException('Route name is required.');
        }

        self::$routes->add(
            $routeDefinition->name,
            new SymfonyRoute(
                $routeDefinition->path,
                ['_controller' => $routeDefinition->controller],
                methods: $routeDefinition->methods
            )
        );
    }


    protected static function addRoute(string $path, array $controller, array $methods): RouteDefinition
    {
        $path = self::$groupPrefix . '/' . $path;
        $name = self::$groupNamePrefix . self::generateInternalName();

        $routeDefinition = new RouteDefinition(
            $path,
            $controller,
            $methods,
            $name
        );
        self::register($routeDefinition);
        return $routeDefinition;
    }

    private static function initRouteCollection(): void
    {
        if (self::$routes === null) {
            self::$routes = new RouteCollection();
        }
    }

}
