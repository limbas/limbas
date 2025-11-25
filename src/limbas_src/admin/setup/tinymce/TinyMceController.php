<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\tinymce;

use Limbas\Controllers\LimbasController;
use Symfony\Component\HttpFoundation\Request;

class TinyMceController extends LimbasController
{

    /**
     * @param array|Request $request
     * @return array|false[]
     */
    public function handleRequest(array|Request $request): array
    {
        return match ($request->get('action')) {
            'save' => $this->saveConfig($request),
            'delete' => $this->deleteConfig($request),
            default => ['success' => false],
        };
    }



    public function index(): string
    {
        global $lang;
        
        $configs = TinyMceConfig::all();
        
        ob_start();
        include(COREPATH . 'admin/setup/tinymce/html/index.php');
        return ob_get_clean() ?: '';
    }


    /**
     * @param Request $request
     * @return array
     */
    private function saveConfig(Request $request): array
    {
        global $LINK, $LINK_ID;

        $name = $request->get('name');
        if (!$LINK[$LINK_ID['setup_tinymce']] || empty($name)) {
            return ['success' => false];
        }

        $id = intval($request->get('id') ?? 0);
        
        $configRows = $request->get('config') ?? [];
        
        $config = [];
        foreach($configRows as $configRow) {
            if($configRow['json']) {
                $configRow['value'] = json_decode($configRow['value'], true);
            }
            $config[$configRow['key']] = $configRow['value'];
        }
        
        $config = new TinyMceConfig(
            $name,
            $config,
            $id,
            boolval($request->get('default'))
        );

        $success = $config->save();
        $html = '';
        if ($success) {
            ob_start();
            include(__DIR__ . '/html/tinymce-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }


    /**
     * @param array $request
     * @return array
     */
    private function deleteConfig(Request $request): array
    {
        global $LINK, $LINK_ID;

        if (!$LINK[$LINK_ID['setup_tinymce']]) {
            return ['success' => false];
        }

        $config = TinyMceConfig::get(intval($request->get('id')));

        if(empty($config)) {
            return ['success' => false];
        }

        return ['success' => $config->delete()];
    }

}
