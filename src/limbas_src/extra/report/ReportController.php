<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report;

use Limbas\extra\printer\Printer;
use Limbas\lib\LimbasController;

require_once(COREPATH . 'extra/report/report.dao');
require_once(COREPATH . 'extra/explorer/filestructure.lib');

class ReportController extends LimbasController
{

    /**
     * @param array $request
     * @return array
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'preview' => $this->handleReportRequest($request, ReportOutput::TEMP),
            'archive' => $this->handleReportRequest($request, ReportOutput::ARCHIVE),
            'print' => $this->handleReportRequest($request, ReportOutput::PRINT),
            'archivePrint' => $this->archivePrint($request),
            default => ['success' => false]
        };
    }


    /**
     * @param array $request
     * @param ReportOutput $reportOutput
     * @return array
     */
    private function handleReportRequest(array $request, ReportOutput $reportOutput): array
    {
        $printerOptions = Printer::getOptionsFromRequest($request);
        
        if ($reportOutput === ReportOutput::PRINT && array_key_exists('dmsIds', $request) && is_array($request['dmsIds']) && !empty($request['dmsIds'])) {

            lmb_printFileFromDMS(intval($request['report_printer']),$request['dmsIds'], $printerOptions);

            return [
                'success' => true
            ];
        }

        $relation = null;

        if ($request['verkn_ID']) {
            $relation = [
                'gtabid' => $request['verkn_tabid'],
                'fieldid' => $request['verkn_fieldid'],
                'ID' => $request['verkn_ID'],
                'showonly' => $request['verkn_showonly']
            ];
        }

        $report = Report::create(intval($request['gtabid']), intval($request['report_id']));
        
        $reportUrl = $report->generateReport(intval($request['report_id']), $request['gtabid'], $request['ID'], $reportOutput, $request['report_rename'], $request['use_record'], intval($request['report_printer']), $request['resolvedTemplateGroups'], $request['resolvedDynamicData'], $relation, $printerOptions);

        $output = [
            'success' => $reportUrl !== false,
            'url' => $reportUrl
        ];

        if ($reportOutput === ReportOutput::ARCHIVE || $reportOutput === ReportOutput::PRINT) {
            $archivedFileIds = $report->getArchivedFileIds();
        }

        if (!empty($archivedFileIds)) {
            $output['ids'] = $archivedFileIds;
        }

        return $output;
    }

    
    private function archivePrint(array $request): array
    {
        global $umgvar;
        
        // if printer cache is active there is no difference to default print
        if($umgvar['printer_cache']) {
            return $this->handleReportRequest($request, ReportOutput::PRINT);
        }
        
        // first archive
        $archived = $this->handleReportRequest($request, ReportOutput::ARCHIVE);
        if($archived['success'] !== true || !array_key_exists('ids',$archived)) {
            return $archived;
        }
        $request['dmsIds'] = $archived['ids'];
        
        // then print => should use previously generated pdf by their ids
        return $this->handleReportRequest($request, ReportOutput::PRINT);
    }
    
}
