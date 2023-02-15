<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



/**
 * # report menu
 *
 * @param array $params
 * @return void
 */
function dyns_menuReportOption($params){
    global $greportlist;

    $reportspec = $greportlist[$params['gtabid']];
    if(!$reportspec){return;}

    require_once(COREPATH . 'extra/report/report.dao');
    lmbReportLoadClasses();

    $report_id = $params['report_id'];
    $report_defformat = $reportspec['defformat'][$report_id];
    $report_medium = $params['report_medium'];
    $report_output = $params['report_output'];
    $gtabid = $params['gtabid'];
    $use_record = $params['use_record'];
    $ID = $params['ID'];
    $listmode = $greportlist[$gtabid]['listmode'][$report_id];
    if(!$report_medium){$report_medium = $report_defformat;}
    $preview = $params['preview'];
    $context = $params['context'];

    $action = $params['action'];
    $report_rename = $params['report_rename'];

    $resolvedTemplateGroups = $params['resolvedTemplateGroups'];
    $resolvedDynamicData = $params['resolvedDynamicData'];
    $saveAsTemplate = $params['saveAsTemplate'];

    $nodata = false;
    if ($preview) {
        $report_output = 0;
        $nodata = true;
        $resolvedDynamicData = null;
    }

    $menuRender = ReportMenuRenderFactory::getMenuRender($context);

    //if template based report -> check if something needs to be resolved
    if ($report_defformat === 'tcpdf' || $report_defformat === 'mpdf') { #REPTYPE#
        if ($menuRender->printReportTemplateOptions($preview,$gtabid, $report_id, $ID, $report_output, $use_record, $resolvedTemplateGroups, $listmode, $nodata, $resolvedDynamicData, $saveAsTemplate)) {
            return;
        }
    }

    //open the preview of a report in a new tab
    if ($preview) {
        $menuRender->printReportPreviewUrl($report_id, $gtabid, $ID, $use_record, $resolvedTemplateGroups);
        return;
    }
    
    
    //if everything is resolved and user has selected an action => generate final report
    if($report_output){
        dyns_reportAction($params);
    }

    
    //if the user did not open the preview -> print acton form as last step
    $menuRender->printReportPrintForm($action, $report_id, $gtabid, $ID, $resolvedTemplateGroups, $resolvedDynamicData);

}


/**
 * # report menu
 *
 * @param array $params
 * @return void
 */
function dyns_reportAction($params){
    global $greportlist;

    $reportspec = $greportlist[$params['gtabid']];
    if(!$reportspec){return;}

    require_once(COREPATH . 'extra/report/report.dao');
    lmbReportLoadClasses();

    $report_output = $params['report_output'];
    
    
    if (empty($report_output)) {
        echo 'No action specified';
        return;
    }


    $relation = null;
    
    if ($params['verkn_ID']) {
        $relation = [
            'gtabid' => $params['verkn_tabid'],
            'fieldid' => $params['verkn_fieldid'],
            'ID' => $params['verkn_ID'],
            'showonly' => $params['verkn_showonly']
            ];
    }
    
    

    limbasGenerateReport($params['report_id'], $params['gtabid'], $params['ID'], $params['report_output'], $params['report_medium'], $params['report_rename'], $params['use_record'], $params['report_printer'], $params['report_ext'], $params['resolvedTemplateGroups'], $params['resolvedDynamicData'], $relation);
}


function dyns_lmbGetReportList($params) {
    header('Content-Type: application/json');

    require_once COREPATH . 'extra/report/report_select/report_select.php';

    $reportlist = LmbReportSelect::getReportListTable($params['gtabid'],$params['search'],$params['page'],$params['perPage']);
    echo json_encode($reportlist);
}



function limbasGenerateReport($report_id, $gtabid, $ID, $report_output, $report_medium, $report_rename, $use_record, $report_printer, $report_ext, $resolvedTemplateGroups, $resolvedDynamicData, $relation = null, $return = false) {
    global $greportlist;
    global $filter, $gsr;
    global $umgvar;
    global $LINK;

    $params = true;
    $reportspec = $greportlist[$gtabid];
    if (!$reportspec) {
        return false;
    }

    require_once(COREPATH . 'extra/report/report.dao');

    // get params
    $report_output = intval($report_output);

    // optional
    if($relation){
        require_once(COREPATH . 'gtab/gtab.lib');
        $verkn = set_verknpf($relation['gtabid'],$relation['fieldid'],$relation['ID'],null,null,$relation['showonly'],1);
    }

    // calculated params
    $report_name = $reportspec['name'][$report_id];
    $report_savename = $reportspec['savename'][$report_id];
    $report_defformat = $reportspec['defformat'][$report_id];
    $listmode = $greportlist[$gtabid]['listmode'][$report_id];
    if(!$report_medium){
        $report_medium = $report_defformat;
    }

    // will be used in report_tcpdf.lib::print_templ
    if (!is_array($resolvedTemplateGroups)) {
        $resolvedTemplateGroups = json_decode(urldecode($resolvedTemplateGroups), true);
    }
    if (!is_array($resolvedDynamicData)) {
        $resolvedDynamicData = json_decode(urldecode($resolvedDynamicData), true);
    }
    $GLOBALS['resolvedTemplateGroups'] = $resolvedTemplateGroups;
    $GLOBALS['resolvedDynamicData'] = $resolvedDynamicData;



    require_once(COREPATH . 'extra/explorer/filestructure.lib');
    require_once(COREPATH . 'extra/report/report_'.lmb_substr($report_medium,0,3).'.php');
    
    

    if(!file_exists(lmb_utf8_encode($generatedReport))){
        return false;
    }

    $filePath = trim(str_replace($umgvar['pfad'], '', $generatedReport), '/') . '?v=' . date('U');
    
    $output = '';
    switch ($report_output) {
        case 2: // archive
            //echo $report['archive_fileID'];
            break;
        case 4: // print
            if ($LINK[304] && !lmbPrint($report_printer, lmb_utf8_encode($generatedReport))) {
                lmb_log::error('print failed', 'print failed!', $gtabid);
            }           
            break;
        case 5:
            $output = $filePath;
            break;
        case 1:
        case 3:
        default:
            $output = '<Script language="JavaScript">open("'.$filePath.'");</Script>'; // todo - wrong html header
    }
    
    if ($return) {
        return $output;
    }
    
    echo $output;
    
    return true;
}



function lmbReportLoadClasses() {
    require_once(__DIR__.'/report_menu/ReportMenuRender.php');
    require_once(__DIR__.'/report_menu/ReportMenuRenderBs4.php');
    require_once(__DIR__.'/report_menu/ReportMenuRenderOld.php');
    require_once(__DIR__.'/report_menu/ReportMenuRenderFactory.php');
}




function lmbSaveReportTemplate($gtabid, $report_id,$saveAsTemplate,$resolvedTemplateGroups) {

    $reportextension = null;

    require_once COREPATH . 'admin/report/report.lib';
    #REPTYPE#
    lmb_report_create($saveAsTemplate,'','',$gtabid,$reportextension,'','tcpdf',$report_id,$resolvedTemplateGroups);
}

?>
