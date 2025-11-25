<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\Controllers;

use Limbas\admin\setup\umgvar\UmgVar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UmgVarController extends LimbasController
{
    
    /**
     * @param array $request needs to include an 'action' and all parameters for that action
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return [];
    }

    public function index(): Response
    {
        global $lang;
        global $umgvar;

        $umgVars = UmgVar::all();

        $categories = [];

        foreach ($umgVars as $umgVar) {
            if (!isset($categories[$umgVar->category])) {
                $categories[$umgVar->category] = [];
            }
            $categories[$umgVar->category][] = $umgVar;
        }

        ob_start();
        include(COREPATH . 'admin/setup/umgvar/html/index.php');
        $output = ob_get_clean() ?: '';
        //return $this->respond($output);
        
        return $this->render('admin/setup/umgvar/html/test.html.twig', compact('categories', 'lang', 'umgvar'));
    }

    public function update(Request $request, int $model) : Response
    {
        global $umgvar;
        
        $umgVar = UmgVar::get($model);
        if ($umgVar === null) {
            return $this->respond(['success' => false, 'msg' => 'UmgVar not found!']);
        }

        $value = $request->get('value');
        $umgVar->value = $value;

        if (!$umgVar->save()) {
            return $this->respond(['success' => false]);
        }

        $umgvar[$umgVar->name] = $umgVar->value;
        
        if ($umgVar->name === 'thumbsize') {
            /* --- Thumpnails löschen --------------------------------------------- */
            $rsc = 'rm ' . TEMPPATH . 'thumpnails/*';
            system($rsc);
        } elseif ($umgVar->name === 'postgres_use_fulltextsearch' || $umgVar->name === 'postgres_indize_lang') {
            // add/remove postgres fulltextsearch fields
            require_once(COREPATH . 'admin/tools/jobs/indize.lib');
            postgresUpdateFtsFields();
        }

        return $this->respond(['success' => true]);
    }

}
