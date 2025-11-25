<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas;

use Exception;
use Limbas\Controllers\DefaultController;
use Limbas\lib\auth\Auth;
use Limbas\lib\auth\Session;
use Limbas\lib\db\Database;
use Limbas\lib\general\Log\Log;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class Limbas extends HttpKernel
{

    public static RequestContext $context;
    public static RouteCollection $routes;

    private bool $needsAuthentication;
    private UrlMatcher $urlMatcher;

    public function __construct(RouteCollection $routes)
    {
        $request = Request::createFromGlobals();
        $context = new RequestContext();
        $context->fromRequest($request);
        $this->urlMatcher = new UrlMatcher($routes, $context);
        $requestStack = new RequestStack();
        
        self::$context = $context;
        self::$routes = $routes;

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($this->urlMatcher, $requestStack, debug: false));
        $dispatcher->addSubscriber(new ResponseListener('UTF-8'));

        parent::__construct($dispatcher, $controllerResolver, $requestStack, $argumentResolver);
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        define('BASE_URL', $request->getBaseUrl() . '/');
        self::$context->setBaseUrl($request->getBaseUrl());
        try {
            require_once(COREPATH . 'lib/include.lib');

            $routeName = $this->checkRoutes($request);

            $installed = $this->checkIfInstalled($routeName);
            if ($installed === false) {
                return new RedirectResponse(route('install.index'));
            }

            $this->loadMainIncludes(!$installed);

            $this->authenticate($request);

            $this->loadGlobalExtensions();

            $response = parent::handle($request);
        } catch (BadRequestException|BadRequestHttpException) {
            $response = (new DefaultController())->error(400);
        } catch (ResourceNotFoundException|NotFoundHttpException) {
            $response = (new DefaultController())->error(404);
        } catch (MethodNotAllowedException|MethodNotAllowedHttpException) {
            $response = (new DefaultController())->error(405);
        } catch (AccessDeniedHttpException) {
            $response = (new DefaultController())->error(403);
        } catch (Throwable $t) {
            $response = (new DefaultController())->error($t->getCode() ?? 500, $t, $request);
            Log::error($t);
        }
        return $response;
    }


    private function authenticate(Request $request): void
    {
        if (!$this->needsAuthentication) {
            return;
        }

        // get login method
        $authenticator = Auth::getAuthenticator();

        $authenticator::checkLogout();

        // check if user is authenticated
        try {
            //try to authenticate user
            $authenticator->authenticate();
        } catch (Throwable) {
            $authenticator::deny();
        }

        // load limbas session
        Session::load($request);
    }

    /**
     * @param string $routeName
     * @return bool|null
     * @throws Exception
     */
    private function checkIfInstalled(string $routeName): ?bool
    {
        global $DBA;
        
        if (str_starts_with($routeName, 'install.')) {
            return null;
        }
        if (!file_exists(DEPENDENTPATH . 'inc/include_db.lib')) {
            return false;
        }

        require_once(COREPATH . 'lib/db/db_wrapper.lib');
        
        return Database::checkIfInstalled();
    }


    private function checkRoutes(Request $request): string
    {
        $this->needsAuthentication = true;
        $parameters = $this->urlMatcher->matchRequest($request);
        $routeName = $parameters['_route'] ?? '';

        if (str_starts_with($routeName, 'legacy.main-soap.')) {
            define('IS_SOAP', true);
        } elseif (str_starts_with($routeName, 'legacy.main-rest.') || str_starts_with($routeName, 'api.')) {
            define('IS_REST', true);
        } elseif (str_starts_with($routeName, 'install.')) {
            $this->needsAuthentication = false;
        }
        return $routeName;
    }

    private function loadMainIncludes(bool $skipDb = false): void
    {
        global $DBA;

        if (!$skipDb) {
            require_once(COREPATH . 'lib/db/db_wrapper.lib');
        }

        if (!isset($DBA['CHARSET'])) {
            $DBA['CHARSET'] = 'UTF-8';
        }

        # --- mbstring include -------------------------------------------
        if (strtoupper($DBA['CHARSET']) == 'UTF-8') {
            require_once(COREPATH . 'lib/include_mbstring.lib');
            ini_set('default_charset', 'utf-8');
        } else {
            require_once(COREPATH . 'lib/include_string.lib');
            ini_set('default_charset', lmb_strtoupper($DBA['CHARSET']));
        }

        # --- time library -------------------------------------------
        require_once(COREPATH . 'lib/include_DateTime.lib');
    }

    private function loadGlobalExtensions(): void
    {
        global $gLmbExt;
        if ($gLmbExt['ext_global.inc']) {
            foreach ($gLmbExt['ext_global.inc'] as $key => $extfile) {
                require_once($extfile);
            }
        }
    }

}
