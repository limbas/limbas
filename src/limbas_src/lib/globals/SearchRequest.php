<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\lib\globals;

use Symfony\Component\HttpFoundation\Request;

class SearchRequest
{

    
    public static function fillFromRequest(Request $request = null): void
    {
        global $gsr;
        global $filter;
        
        if($request === null) {
            $request = Request::createFromGlobals();
        }
        
        $gs = $request->get('gs',[]);
        $tabId = intval($request->get('gtabid'));
        
        //dump($gs);
        
        if(empty($gs) || empty($tabId)) {
            return;
        }

        $historySearch = $request->get('history_search',[]);
        $superSearch = boolval($request->get('supersearch'));
        $gsSearch = boolval($request->get('gssearch'));
        
        if(!empty($historySearch)){
            $historySearch = explode(';',$historySearch);
            $filter['page'][$tabId] = 1;
            foreach($historySearch as $key => $value){
                $search_el = explode(',',$historySearch[$key]);
                $gsr[$search_el[0]][$search_el[1]][0] = $gs[$search_el[0]][$search_el[1]][0];
            }

            self::cleanGsr($tabId,$gsr);

        # suche ohne history_search
        }elseif($superSearch OR $gsSearch){
            $gsr = $gs;
            self::cleanGsr($tabId,$gsr);
        }
        
    }

    
    /**
     * Cleans the array from empty search entries
     * 
     * @param int $tabId
     * @param array $gsr
     * @return void
     */
    private static function cleanGsr(int $tabId, array &$gsr): void
    {
        if(empty($gsr[$tabId])){
            $gsr[$tabId] = [];
        }

        $attr = array('txt','num','cs','andor','string','neg');

        // first level
        foreach($gsr[$tabId] as $fieldId => $searchParams) {

            foreach($searchParams as $key => $value) {

                // skip 'txt', 'num', ...
                if (!is_numeric($key)) {
                    continue;
                }

                if (!$value and $value !== '0' and !$gsr[$tabId][$fieldId]['txt'][$key] >= 7 and !$gsr[$tabId][$fieldId]['num'][$key] >= 7) { // not unset 0, IS NULL, IS NOT NULL
                    self::cleanGsrAttribute($gsr[$tabId][$fieldId],$key,$attr);
                }

                if(!$gsr[$tabId][$fieldId]){
                    unset($gsr[$tabId][$fieldId]);
                }
            }

            if(is_array($gsr[$tabId]) AND !is_numeric(key($gsr[$tabId])) ){
                unset($gsr[$tabId]['andor']);
            }

            if(!$gsr[$tabId]){
                unset($gsr[$tabId]);
            }
        }
        
    }

    private static function cleanGsrAttribute(&$gsr, $key1, &$attr): void
    {

        unset($gsr[$key1]);
        foreach($attr as $attrk => $attrv){
            unset($gsr[$attrv][$key1]);
            if (!$gsr[$attrv]) {
                unset($gsr[$attrv]);
            }
        }
    }
    
}
