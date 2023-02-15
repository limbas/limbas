<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
abstract class ReportMenuRender {

    public abstract function printReportTemplateOptions($preview,$gtabid, $report_id, $ID, $report_output, $use_record, $resolvedTemplateGroups, $listmode, $nodata = false, $resolvedDynamicData = '', $saveAsTemplate='');

    public function printReportPreviewUrl($report_id, $gtabid, $ID, $use_record, $resolvedTemplateGroups) {
        global $greportlist;
        // open preview in new tab
        $url = "main.php?". http_build_query(array(
                'action' => 'report_preview',
                'gtabid' => $gtabid,
                'report_id' => $report_id,
                'ID' => $ID,
                'use_record' => $use_record,
                'resolvedTemplateGroups' => urldecode($resolvedTemplateGroups),
            ));
        //TODO: if not bootstrap layout
        echo "<script language=\"JavaScript\">limbasReportShowPreview('{$url}', '{$greportlist[$gtabid]['name'][$report_id]}');</script>";
    }
    
    public function printReportPrintForm($action, $report_id, $gtabid, $ID, $resolvedTemplateGroups, $resolvedDynamicData) {
        global $greportlist;

        //TODO: show data placeholders with filled data

        $reportspec = $greportlist[$gtabid];
        if(!$reportspec){return;}


        $report_name = $reportspec['name'][$report_id];
        $report_savename = $reportspec['savename'][$report_id];
        $report_medium = $reportspec['defformat'][$report_id];
        $listmode = $greportlist[$gtabid]['listmode'][$report_id];


        $report_rename = reportSavename($report_name,$report_savename,$ID,$report_medium,'', false);
        $resolvedTemplateGroupsStr = $resolvedTemplateGroups ? htmlspecialchars(urldecode($resolvedTemplateGroups), ENT_QUOTES) : '{}';
        $resolvedDynamicDataStr = $resolvedDynamicData ? htmlspecialchars(urldecode($resolvedDynamicData), ENT_QUOTES) : '{}';


        $report_medium_opt = [
            'val' => ['xml'],
            'desc' => ['xml']
        ];

        # use either pdf or tcpdf in background, dependent on report configuration
        if ($report_medium === 'pdf' || $report_medium === 'tcpdf' || $report_medium === 'mpdf') { #REPTYPE#
            array_unshift($report_medium_opt['val'], $report_medium);
            array_unshift($report_medium_opt['desc'], 'pdf');
        }

        if($reportspec['odt_template'][$report_id]){
            $report_medium_opt['val'][] = 'odt';
            $report_medium_opt['desc'][] = 'text';
        }
        if($reportspec['ods_template'][$report_id]){
            $report_medium_opt['val'][] = 'ods';
            $report_medium_opt['desc'][] = 'spreadsheet';
        }


        
        $this->printReportPrintFormInternal($action, $report_id, $gtabid, $ID, $listmode, $report_medium, $report_medium_opt, $report_rename, $resolvedTemplateGroupsStr, $resolvedDynamicDataStr);
        
    }
    
    protected abstract function printReportPrintFormInternal($action, $report_id, $gtabid, $ID, $listmode, $report_medium, $report_medium_opt, $report_rename, $resolvedTemplateGroupsStr, $resolvedDynamicDataStr);
    
}
