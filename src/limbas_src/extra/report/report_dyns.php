<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\report\Report;
use Limbas\extra\report\ReportOutput;


/**
 * @param $report_id
 * @param $gtabid
 * @param $ID
 * @param $report_output
 * @param $report_medium
 * @param $report_rename
 * @param $use_record
 * @param $report_printer
 * @param $report_ext
 * @param $resolvedTemplateGroups
 * @param $resolvedDynamicData
 * @param $relation
 * @param $return
 *
 * @deprecated Use Report->generateReport instead.
 * 
 * @return bool|string
 */
function limbasGenerateReport($report_id, $gtabid, $ID, $report_output, $report_medium, $report_rename, $use_record, $report_printer, $report_ext, $resolvedTemplateGroups, $resolvedDynamicData, $relation = null, $return = false) {
    trigger_error('Method ' . __METHOD__ . ' is deprecated. Use Report->generateReport instead.', E_USER_DEPRECATED);
    $report = Report::create(intval($gtabid), intval($report_id));
    $reportOutput = ReportOutput::tryFrom($report_output) ?? ReportOutput::TEMP;
    return $report->generateReport(intval($report_id), $gtabid, $ID, $reportOutput, $report_rename, $use_record, intval($report_printer), $resolvedTemplateGroups, $resolvedDynamicData, $relation);
}

/**
 * @param $report_medium
 * @param $report_id
 * @param $gtabid
 * @param $ID
 * @param $report_output
 * @param $report_rename
 * @param $gsr
 * @param $filter
 * @param $use_record
 * @param $verkn
 * @param $report_printer
 * @param $params
 * 
 * @deprecated Use Report->createReport instead.
 * 
 * @return bool|string
 */
function limbasGenerateReportPdf($report_medium, $report_id,$gtabid,$ID,$report_output,$report_rename,$gsr,$filter,$use_record,$verkn,$report_printer,$params): bool|string
{
    trigger_error('Method ' . __METHOD__ . ' is deprecated. Use Report->createReport instead.', E_USER_DEPRECATED);
    $report = Report::create(intval($gtabid), intval($report_id));
    $reportOutput = ReportOutput::tryFrom($report_output) ?? ReportOutput::TEMP;
    return $report->createReport( intval($report_id),$gtabid,$ID,$reportOutput,$report_rename,$gsr,$filter,$use_record,$verkn,intval($report_printer));
}
