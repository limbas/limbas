<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\Controllers;

use Limbas\layout\Layout;
use Limbas\lib\general\StackTrace;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class DefaultController
{

    public function index(Request $request): Response
    {
        global $umgvar;


        $intro = 'main.php?action=intro';
        
        if ($umgvar['introlink']) {
            $intro = $umgvar['introlink'];
        }
        
        $queryString = $request->getQueryString();
        if(!empty($queryString)) {
            $intro = 'main.php?' . $queryString;
        } elseif(!empty($umgvar['introlink'])) {
            $intro = $umgvar['introlink'];
        }
        
        $logoLink = $request->getBaseUrl();
        
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require(Layout::getFilePath('main/main.php'));
        return new Response(ob_get_clean() ?: '');
    }

    public function error(int $code, Throwable $error = null, Request $request = null): Response
    {
        global $session;

        $title = 'Server Error';
        $message = 'Server Error';

        switch ($code) {
            case 400:
                $title = 'Bad Request';
                $message = 'Bad Request';
                break;
            case 403:
                $title = 'Forbidden';
                $message = 'You don\'t have permission to access this page';
                break;
            case 404:
                $title = '404 Not Found';
                $message = 'The requested page was not found on this server';
                break;
            case 405:
                $title = '405 Method Not Allowed';
                $message = 'The requested method is not allowed';
                break;
            case 600:
                $title = '500 Server Error';
                $message = 'Database connection failed';
                $code = 500;
                break;
            default:
                $code = 500;
                break;
        }

        ob_end_clean();
        ob_start();
        if ($code === 500 && $error !== null && $request !== null && ($session['debug'] || defined('LIMBAS_INSTALL'))) {

            try {
                $stackTrace = new StackTrace($error);
                include(COREPATH . 'resources/views/errors/debugError.php');
            } catch (Throwable) {
                ob_end_clean();
                include(COREPATH . 'resources/views/errors/error.php');
            }

        } else {
            include(COREPATH . 'resources/views/errors/error.php');
        }

        return new Response(ob_get_clean() ?: '', $code);
    }
}
