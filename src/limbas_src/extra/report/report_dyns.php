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
function dyns_reportAction($params){
    global $greportlist;

    $reportspec = $greportlist[$params['gtabid']];
    if(!$reportspec){return;}

    require_once(COREPATH . 'extra/report/report.dao');

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
            $output = $filePath;
            break;
        case 4: // print
            $output = $filePath;
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




function lmbSaveReportTemplate($gtabid, $report_id,$saveAsTemplate,$resolvedTemplateGroups) {

    $reportextension = null;

    require_once COREPATH . 'admin/report/report.lib';
    #REPTYPE#
    lmb_report_create($saveAsTemplate,'','',$gtabid,$reportextension,'','tcpdf',$report_id,$resolvedTemplateGroups);
}

?>
