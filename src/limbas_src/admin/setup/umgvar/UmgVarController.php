<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\umgvar;

use Limbas\Controllers\LimbasController;

class UmgVarController extends LimbasController
{
    /**
     * @param array $request needs to include an 'action' and all parameters for that action
     * @return array|false[]
     */

    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'create' => $this->create($request),
            'update' => $this->update($request),
            'delete' => $this->delete($request),
            default => ['success' => false],
        };
    }

    public function index(): string
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
        return ob_get_clean() ?: '';
    }

    private function create(array $request) : array
    {
        global $lang, $umgvar;

        $name = $request['name'];
        if (empty($name)) {
            return ['success' => false, 'msg' => 'Cannot add new UmgVar without specifying name.'];
        }
        $name = lmb_strtolower(preg_replace('/[^a-z_\/\-0-9]/', '', $name));
        $value = preg_replace("/ {2,}/"," ", $request['value']);
        $description = preg_replace("/ {2,}/"," ", $request['description']);

        $category = $request['category'];
        if(empty($category)){
            $newCategory = $request['newCategory'];
            if(empty($newCategory)) {
                return ['success' => false, 'msg' => 'Cannot add new UmgVar without specifying a category.'];
            }

            $newCategory = preg_replace("/ {2,}/", " ", $newCategory);

            require_once(COREPATH . 'admin/setup/language.lib');
            global $session;
            $category = lang_add($session['language'],3,'umgvar',$newCategory,'_DEPEND');
        }

        $umgVar = new UmgVar(0,0,$name,$value,$description,intval($category));
        $success = $umgVar->save();
        
        $html = '';
        $category = $umgVar->category;
        $categoryName = $lang[$umgVar->category] ?? '';
        $msg = '';
        
        if ($success) {
            ob_start();
            $isNew = true;
            include(COREPATH . 'admin/setup/umgvar/html/umgvar-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html', 'category', 'categoryName', 'msg');
    }

    private function update(array $request) : array {
        global $umgvar;
        
        $umgVar = UmgVar::get(intval($request['id']));
        if ($umgVar === null) {
            return ['success' => false, 'msg' => 'UmgVar not found!'];
        }

        if (!$request['only_category']) {
            $value = $request['value'];
            $umgVar->value = $value;
        }
        else {
            
            if ($umgvar['admin_mode']) {
                $umgVar->category = intval($request['category']);
            }
            else{
                return ['success' => false, 'msg' => 'No permission to change UmgVar categories!'];
            }
        }

        if (!$umgVar->save()) {
            return ['success' => false];
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

        return ['success' => true];
    }

    private function delete(array $request) : array
    {
        $umgVar = UmgVar::get(intval($request['id']));
        $success = true;
        if ($umgVar !== null) {
            $success = $umgVar->delete();
        }
        return compact('success');
    }
}
