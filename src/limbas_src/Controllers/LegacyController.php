<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LegacyController extends AbstractController
{
    public function main(Request $request): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main.php';
        return new Response(ob_get_clean() ?: '');
    }

    public function mainAdmin(): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main_admin.php';
        return new Response(ob_get_clean() ?: '');
    }

    public function mainDyns(): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main_dyns.php';
        return new Response(ob_get_clean() ?: '');
    }

    public function mainDynsAdmin(): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main_dyns_admin.php';
        return new Response(ob_get_clean() ?: '');
    }

    public function mainSoap(): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main_soap.php';
        return new Response(ob_get_clean() ?: '');
    }

    public function mainWsdl(): Response
    {
        ob_start();
        require COREPATH . 'Controllers/injectGlobals.php';
        require COREPATH . 'main/main_wsdl.php';
        return new Response(ob_get_clean() ?: '');
    }

}
