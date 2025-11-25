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

        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        $gs = $request->get('gs', []);
        $tabId = intval($request->get('gtabid'));

        if (empty($gs) || empty($tabId)) {
            return;
        }

        $historySearch = $request->get('history_search', []);
        $superSearch = boolval($request->get('supersearch'));
        $gsSearch = boolval($request->get('gssearch'));
        $search = $request->get('search');
        $applySearch = $request->get('apply_search');

        if ($applySearch && !empty($search) && is_array($search)) {
            $gsr = $gs;
            $filter['page'][$tabId] = 1;
            self::cleanGsr($tabId, $gsr);
            $searchGsr = self::searchToGsr($search, originalGsr: $gsr ?? []);
            foreach ($searchGsr as $tabId => $values) {
                $gsr[$tabId] = $values;
            }
        } elseif (!empty($historySearch)) {
            $historySearch = explode(';', $historySearch);
            foreach ($historySearch as $value) {
                if(empty($value)){continue;}
                $search_el = explode(',', $value);
                $gsr[$search_el[0]][$search_el[1]][0] = $gs[$search_el[0]][$search_el[1]][0];
            }
            $filter['page'][$tabId] = 1;
            self::cleanGsr($tabId, $gsr);

            # suche ohne history_search
        } elseif ($superSearch or $gsSearch) {
            $gsr = $gs;
            $filter['page'][$tabId] = 1;
            self::cleanGsr($tabId, $gsr);
        }
        #error_log($filter['page'][$tabId]);

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

        global $gfield;

        if (empty($gsr[$tabId])) {
            $gsr[$tabId] = [];
        }

        $attr = array('txt', 'num', 'cs', 'andor', 'string', 'neg');

        // first level
        foreach ($gsr[$tabId] as $fieldId => $searchParams) {

            foreach ($searchParams as $key => $value) {

                // skip 'txt', 'num', ...
                if (!is_numeric($key)) {
                    continue;
                }

                // recursive relation handling
                if(is_array($gsr[$tabId][$fieldId][1]) && $gfield[$tabId]['field_type'][$fieldId] == 11){
                    self::cleanGsr(key($gsr[$tabId][$fieldId][1]), $gsr[$tabId][$fieldId][1]);
                    if(!$gsr[$tabId][$fieldId][1]){
                        unset($gsr[$tabId][$fieldId]);
                    }
                }

                if (!$value and $value !== '0' and !$gsr[$tabId][$fieldId]['txt'][$key] >= 7 and !$gsr[$tabId][$fieldId]['num'][$key] >= 7) { // not unset 0, IS NULL, IS NOT NULL
                    self::cleanGsrAttribute($gsr[$tabId][$fieldId], $key, $attr);
                }

                if (!$gsr[$tabId][$fieldId]) {
                    unset($gsr[$tabId][$fieldId]);
                }
            }

            if (is_array($gsr[$tabId]) and !is_numeric(key($gsr[$tabId]))) {
                unset($gsr[$tabId]['andor']);
            }

            if (!$gsr[$tabId]) {
                unset($gsr[$tabId]);
            }
        }

    }

    private static function cleanGsrAttribute(&$gsr, $key1, &$attr): void
    {

        unset($gsr[$key1]);
        foreach ($attr as $attrk => $attrv) {
            unset($gsr[$attrv][$key1]);
            if (!$gsr[$attrv]) {
                unset($gsr[$attrv]);
            }
        }
    }


    private static function searchToGsr(array $search, bool $noAndOr = false, ?array $originalGsr = []): array
    {

        if (empty($search)) {
            return [];
        }

        $gsr = $originalGsr ?? [];

        foreach ($search as $tabId => $fields) {

            $fieldGsr = $originalGsr[$tabId] ?? [];
            foreach ($fields as $fieldId => $searchParams) {
                if (!is_numeric($fieldId)) {
                    continue;
                }

                if (array_key_exists('rel', $searchParams)) {

                    if (empty($searchParams['rel'])) {
                        continue;
                    }

                    $relSearch = self::searchToGsr($searchParams['rel'], true);

                    if (!empty($relSearch)) {
                        $fieldGsr[$fieldId] = [1 => $relSearch];
                    }
                    continue;
                }

                if (
                    !array_key_exists('m', $searchParams) ||
                    !array_key_exists('v', $searchParams) ||
                    !array_key_exists('o', $searchParams) ||
                    empty($searchParams['m']) ||
                    empty($searchParams['v']) ||
                    empty($searchParams['o'])
                ) {
                    continue;
                }


                $searchOperands = [];
                $valueSearchOperands = [];
                $valueSearchMethod = 'num';
                $searchValues = $fieldGsr[$fieldId] ?? [];
                $andOrValues = [];

                $counter = count($searchValues);
                $lastOperator = null;
                foreach ($searchParams['v'] as $key => $searchValue) {
                    $searchContent = false;

                    if (empty($searchValue) || (!array_key_exists($key, $searchParams['o']) && empty($lastOperator))) {
                        continue;
                    }

                    if (array_key_exists($key, $searchParams['o'])) {
                        $lastOperator = $searchParams['o'][$key];
                        if (str_contains($lastOperator, 'v')) {
                            $searchContent = true;
                            $lastOperator = substr($searchParams['o'][$key], 1);
                        }
                    }

                    if (!empty($searchValues)) {
                        $andOrValues[$counter] = 2;
                        if ($searchContent) {
                            $andOrValues[$counter] = 3;
                        }
                    }

                    $searchValues[$counter] = $searchValue;
                    if($searchContent) {
                        $valueSearchOperands[$counter] = $lastOperator;
                    }
                    else {
                        $searchOperands[$counter] = $lastOperator;
                    }


                    $counter++;
                }


                if (!empty($searchValues)) {
                    $fieldGsr[$fieldId] = $searchValues;
                    $fieldGsr[$fieldId][$searchParams['m']] = $searchOperands;
                    if(!empty($valueSearchOperands)) {
                        $fieldGsr[$fieldId][$valueSearchMethod] = $valueSearchOperands;
                    }
                    if (!empty($andOrValues)) {
                        $fieldGsr[$fieldId]['andor'] = $andOrValues;
                    }
                }

            }

            if (!empty($fieldGsr)) {
                $gsr[$tabId] = $fieldGsr;
                if (!$noAndOr) {
                    $gsr[$tabId]['andor'] = 1;
                }
            }

        }

        return $gsr;
    }

}
