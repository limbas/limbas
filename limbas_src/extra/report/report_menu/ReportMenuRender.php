<?php


abstract class ReportMenuRender {

    public abstract function printReportTemplateOptions($preview,$gtabid, $report_id, $ID, $report_output, $use_record, $resolvedTemplateGroups, $listmode, $nodata = false, $resolvedDynamicData = '', $saveAsTemplate='');

    public function printReportPreviewUrl($report_id, $gtabid, $ID, $use_record, $resolvedTemplateGroups) {
        // open preview in new tab
        $url = "main.php?". http_build_query(array(
                'action' => 'report_preview',
                'gtabid' => $gtabid,
                'report_id' => $report_id,
                'ID' => $ID,
                'use_record' => $use_record,
                'resolvedTemplateGroups' => urldecode($resolvedTemplateGroups),
            ));
        echo "<script language=\"JavaScript\">open('{$url}', '_blank');</script>";
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
        if ($report_medium === 'pdf' or $report_medium === 'tcpdf') {
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
