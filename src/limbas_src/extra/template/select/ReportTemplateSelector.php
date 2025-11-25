<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\select;

use Limbas\extra\template\base\TemplateResolver;
use Limbas\extra\template\report\ReportTemplateResolver;
use Limbas\lib\db\Database;


class ReportTemplateSelector extends TemplateSelector
{

    public function __construct(string $type)
    {
        $this->type = $type;
    }


    protected function getElementList(int $gtabid, string $search = '', int $page = 1, int $perPage = 10): array
    {
        global $greportlist;
        global $session;
        global $umgvar;

        $tenantId = !empty($session['mid']) ? $session['mid'] : 0;
        
        if(array_key_exists('resolved', $greportlist[$gtabid]) && array_key_exists($tenantId, $greportlist[$gtabid]['resolved'])) {
            $resolvedList = $greportlist[$gtabid]['resolved'][$tenantId];
        }
        else {
            $resolvedList = [];
            $duplicateFilter = [];

            foreach ($greportlist[$gtabid]["id"] as $key => $reportid) {
                if ($greportlist[$gtabid]["hidden"][$key]) {
                    continue;
                }

                if ($greportlist[$gtabid]["is_template"][$key]) {

                    $resolvedList = array_merge($resolvedList, $this->getPartsListOfTemplate($key, $gtabid, intval($greportlist[$gtabid]['parent_id'][$key]), json_decode($greportlist[$gtabid]['saved_template'][$key], true) ?: [], $duplicateFilter));

                } else {

                    $resolvedList[] = [
                        'id' => $reportid,
                        'name' => $greportlist[$gtabid]['name'][$key],
                        'gtabid' => $gtabid,
                        'resolved' => '{}'
                    ];
                }


            }

            if(!array_key_exists('resolved', $greportlist[$gtabid])) {
                $greportlist[$gtabid]['resolved'] = [];
            }
            
            $greportlist[$gtabid]['resolved'][$tenantId] = $resolvedList;
            
            if($umgvar['report_resolve_cache']) {
                Database::update('LMB_CONF_TABLES',[
                    'REPORT_RESOLVE_CACHE' => json_encode($greportlist[$gtabid]['resolved']),
                ],[
                    'TAB_ID' => $gtabid
                ]);
            }
            
        }

        return $this->paginateElementList($this->getFilteredElementList($resolvedList, $search), $page, $perPage);
    }


    private function getPartsListOfTemplate(int $key, int $gtabid, int $report_id, array $resolvedTemplateGroups, &$duplicateFilter): array
    {
        global $greportlist;

        $output = [];

        $reportTemplateResolver = new ReportTemplateResolver(intval($report_id));
        $templateElements = $reportTemplateResolver->resolveReportToElements($gtabid, $report_id, $resolvedTemplateGroups, 0, true);

        $elementCount = lmb_count($templateElements);

        if ($elementCount <= 0 || $elementCount > 1) {

            //nothing has to be resolved => treat like normal report
            //more than one subgroup => let user resolve and just pass through settings

            $output[] = [
                'id' => $report_id,
                'name' => $greportlist[$gtabid]['name'][$key],
                'gtabid' => $gtabid,
                'resolved' => htmlspecialchars(json_encode($resolvedTemplateGroups), ENT_QUOTES)
            ];

        } elseif ($elementCount == 1) {

            //exactly one subgroup => every child is a report
            $unresolvedGroupName = $templateElements[0]['name'];

            $append = '';

            //if this group was already resolved => skip
            if (in_array($unresolvedGroupName, $duplicateFilter)) {
                $append = ' (' . $greportlist[$gtabid]["name"][$key] . ')';
                //return [];
            }
            $duplicateFilter[] = $unresolvedGroupName;

            foreach ($templateElements[0]['children'] as $childtemplate) {

                $resolvedTemplateGroups[$unresolvedGroupName] = $childtemplate['name'];

                $output[] = [
                    'id' => $report_id,
                    'name' => $childtemplate['name'] . $append,
                    'gtabid' => $gtabid,
                    'resolved' => htmlspecialchars(json_encode($resolvedTemplateGroups), ENT_QUOTES)
                ];
            }

        }

        return $output;
    }


    public function resolveSelect($params): array
    {
        global $greportlist;

        $reportspec = $greportlist[$params['gtabid']];
        if (!$reportspec) {
            return [
                'resolved' => false,
                'html' => '',
                'type' => $this->type
            ];
        }

        return parent::resolveSelect($params);
    }

    protected function getFinalResolvedParameters(int $elementId, int $gtabid, ?int $id, array $ids, $use_record, $resolvedTemplateGroups, array $appendData = []): array
    {
        global $greportlist;


        $queryData = [
            'action' => 'report_preview',
            'gtabid' => $gtabid,
            'report_id' => $elementId,
            'ID' => $id,
            'use_record' => $use_record,
            'resolvedTemplateGroups' => urldecode($resolvedTemplateGroups),
        ];

        if(!empty($appendData)) {
            $queryData['appendData'] = $appendData;
        }
        
        $url = 'main.php?' . http_build_query($queryData);

        return [
            'url' => $url,
            'name' => $greportlist[$gtabid]['name'][$elementId]
        ];
    }

    protected function getTemplateResolver(int $elementId): TemplateResolver
    {
        return new ReportTemplateResolver($elementId);
    }
}




