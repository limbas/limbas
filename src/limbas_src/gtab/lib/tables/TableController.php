<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\gtab\lib\tables;
use Limbas\Controllers\LimbasController;
use Symfony\Component\HttpFoundation\Request;

class TableController extends LimbasController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return [];
    }
    

    public function show(Request $request): string
    {
        global $gtab;
        global $gfield;
        global $verkn;
        
        require COREPATH . 'Controllers/injectGlobals.php';

        $tabId = intval($request->get('gtabid'));
        $useRecord = boolval($request->get('use_record'));
        
        ob_start();
        if ($gtab['typ'][$tabId] == 5 && !$gfield[$tabId]['id']) {
            require(COREPATH . 'gtab/html/view/gtab_view.php');
        } else {
            
            require_once( COREPATH . 'gtab/gtab_register.lib');
            require_once( COREPATH . 'extra/explorer/filestructure.lib');
            if($useRecord) {
                require_once( COREPATH . 'gtab/sql/gtab_use.dao');
            }
            require_once( COREPATH . 'gtab/sql/gtab_erg.dao');
            
            require(COREPATH . 'gtab/html/table/gtab_erg.php');
        }
        return ob_get_clean() ?: '';   
    }
    
}
