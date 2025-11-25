<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report\preview;

use Limbas\extra\report\Report;
use Limbas\extra\template\report\ReportTemplateResolver;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;

class ReportPreview
{

    /**
     * @return array
     */
    public function getRequestParameter(): array
    {
        global $LINK;
        global $gprinter;
        global $greportlist;
        global $gtabid;
        global $report_id;
        global $ID;
        global $use_record;
        global $umgvar;

        $gtabid = intval($_REQUEST['gtabid']);
        $report_id = intval($_REQUEST['report_id']);
        $ID = intval($_REQUEST['ID']);
        $use_record = urldecode($_REQUEST['use_record']);
        $resolvedTemplateGroups = json_decode(urldecode($_REQUEST['resolvedTemplateGroups']), true);
        $resolvedDynamicData = json_decode(urldecode($_REQUEST['resolvedDynamicData']), true);

        if (empty($resolvedTemplateGroups)) {
            $resolvedTemplateGroups = [];
        }
        if (empty($resolvedDynamicData)) {
            $resolvedDynamicData = [];
        }


        // get dynamic data placeholders

        $reportTemplateResolver = new ReportTemplateResolver($report_id);
        list($reportTemplateElements, $dynamicDataPlaceholdersExist, $unresolvedDynamicDataPlaceholdersExist)
            = $reportTemplateResolver->getDynamicDataPlaceholders($gtabid, $report_id, $ID, $use_record, $resolvedTemplateGroups, $resolvedDynamicData);

// define empty dynamic data placeholders for preview during editing
        if ($unresolvedDynamicDataPlaceholdersExist) {
            foreach ($reportTemplateElements as $reportTemplateElement) {
                foreach ($reportTemplateElement['unresolvedDynamicDataPlaceholders'] as $placeholder) {
                    $resolvedDynamicData[$placeholder->getIdentifier()] = '';
                }
            }
        }


        $resolvedTemplateGroups = $resolvedTemplateGroups ?? [];
        $resolvedDynamicData = $resolvedDynamicData ?? [];

        // get archive name        
        $report = Report::create($gtabid, $report_id);
        $report_name = $report->reportSaveName($greportlist[$gtabid]['name'][$report_id] ?? '', $greportlist[$gtabid]['savename'][$report_id] ?? '', $ID);

        return [
            $LINK,
            $gprinter,
            $greportlist,
            $umgvar,

            $gtabid,
            $report_id,
            $ID,
            $use_record,
            $report_name,

            $resolvedTemplateGroups,
            $resolvedDynamicData,
            $reportTemplateElements,
            $dynamicDataPlaceholdersExist,
            $unresolvedDynamicDataPlaceholdersExist
        ];
    }

    /**
     * @param int $tabId
     * @param int $reportId
     * @return string
     */
    public function getCss(int $tabId, int $reportId): string
    {
        global $greportlist;
        global $umgvar;


        $extCss = $umgvar['path'] . $greportlist[$tabId]['css'][$reportId];
        if (file_exists($extCss)) {
            $reportCss = file_get_contents($extCss);

            $scss = new Compiler();

            try {
                return $scss->compileString(
                    '#report_preview {' . $reportCss . '}'
                )->getCss();
            } catch (SassException) {
                return '';
            }
        }

        return '';
    }

    /**
     * @param int $tabId
     * @param int $reportId
     * @return string
     */
    public function getReportActions(int $tabId, int $reportId): string
    {
        global $LINK;
        global $gprinter;
        global $lang;


        ob_start();
        include(COREPATH . 'extra/report/preview/actions.php');
        $html = ob_get_clean();
        
        if (function_exists('lmbGetReportActions')) {
            lmbGetReportActions($tabId, $reportId, $html);
        }

        return $html;
    }

}
