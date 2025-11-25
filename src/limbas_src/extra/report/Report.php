<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report;

require_once(COREPATH . 'gtab/gtab.lib');
require_once(COREPATH . 'extra/report/report.dao');
require_once(COREPATH . 'extra/explorer/filestructure.lib');

use Exception;
use Limbas\extra\printer\PrinterOptions;
use Limbas\lib\general\Log\Log;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use Throwable;

abstract class Report
{
    protected string $type;

    protected array $archivedFileIds;
    
    protected bool $standardApplied = false;

    public static function create(int $tabId, int $reportId): ?Report
    {
        global $greportlist;

        $type = $greportlist[$tabId]['defformat'][$reportId];

        return match ($type) {
            'tcpdf' => new ReportTcpdf(),
            default => new ReportMpdf()
        };
    }

    public function __construct()
    {
        $this->archivedFileIds = [];
    }


    /**
     * @param int $reportId
     * @param $gtabid
     * @param $ID
     * @param ReportOutput $reportOutput
     * @param $report_rename
     * @param $use_record
     * @param $resolvedTemplateGroups
     * @param $resolvedDynamicData
     * @param null $relation
     * @param PrinterOptions|null $printerOptions
     * @return bool|string
     */
    public function generateReport(int $reportId, $gtabid, $ID, ReportOutput $reportOutput, $report_rename, $use_record, $resolvedTemplateGroups, $resolvedDynamicData, $relation = null, PrinterOptions $printerOptions = null): bool|string
    {
        global $greportlist;
        global $filter, $gsr;
        global $umgvar;

        $reportspec = $greportlist[$gtabid];
        if (!$reportspec) {
            return false;
        }

        // optional
        if ($relation) {
            $verkn = set_verknpf($relation['gtabid'], $relation['fieldid'], $relation['ID'], null, null, $relation['showonly'], 1);
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

        $generatedReport = $this->createReport($reportId, $gtabid, $ID, $reportOutput, $report_rename, $gsr, $filter, $use_record, $verkn, $printerOptions);


        if ($generatedReport === false) {
            return false;
        }

        if (!file_exists($generatedReport)) {
            return false;
        }

        return trim(str_replace($umgvar['pfad'], '', $generatedReport), '/');
    }

    /**
     * @param int $reportId
     * @param null $gtabid
     * @param null $ID
     * @param ReportOutput $reportOutput
     * @param null $report_rename
     * @param null $gsr
     * @param null $filter
     * @param null $use_record
     * @param null $verkn
     * @param PrinterOptions|null $printerOptions
     * @return bool|string
     */
    public function createReport(int $reportId, $gtabid = null, $ID = null, ReportOutput $reportOutput = ReportOutput::TEMP, $report_rename = null, $gsr = null, $filter = null, $use_record = null, $verkn = null, PrinterOptions $printerOptions = null): bool|string
    {
        global $session;
        global $greportlist;
        global $lang;
        $generatedReport = false;

        if (!$gtabid) {
            $gtabid = $greportlist["argresult_tabid"][$reportId];
        }
        if ($gtabid <= 0) {
            $gtabid = -1;
        }

        $listmode = $greportlist[$gtabid]["listmode"][$reportId];
        $extension = $greportlist[$gtabid]["extension"][$reportId];

        try {

            if ($extension) {
                if (file_exists(EXTENSIONSPATH . $extension)) {
                    require_once(EXTENSIONSPATH . $extension);
                }
            }

            $rep_ = get_report($reportId, null);
            if ($rep_['extension']) {
                if (file_exists(EXTENSIONSPATH . $rep_['extension'])) {
                    require_once(EXTENSIONSPATH . $rep_['extension']);
                }
            }
        } catch (Throwable) {
        }


        try {
            if ($ID) {
                # --- Einzelbericht eines Datensatzes -----------------------------------
                $generatedReport = $this->createPDF($reportId, $ID, $reportOutput, $report_rename, null, null, $printerOptions);
            } elseif ($use_record and !$listmode) {
                # --- Einzelbericht mehrerer Datensätze (gefiltert)  -----------------------------------
                $generatedReport = $this->createReportForSelected($gtabid, $reportId, $reportOutput, $use_record, $report_rename, $printerOptions);
            } elseif (!$listmode) {
                # --- Listenbericht gefiltert -----------------------------------
                $generatedReport = $this->createReportForFiltered($gtabid, $reportId, $reportOutput, $filter, $gsr, $verkn, $report_rename, $printerOptions);
            } elseif ($reportId and $listmode and $gtabid) {
                # --- Einzelbericht mehrerer Datensätze (ausgewählt) -----------------------------------
                $generatedReport = $this->createPDF($reportId, 1, $reportOutput, $report_rename, $gsr, $filter, $printerOptions);
            } elseif ($reportId and !$gtabid) {
                # --- freier Einzelbericht ohne Tabelle -----------------------------------
                $generatedReport = $this->createPDF($reportId, 0, $reportOutput, $report_rename, null, null, $printerOptions);
            }
        } catch (Exception $e) {

            if ($session["debug"]) {
                Log::limbasError($e, $e->getMessage());
            } else {
                Log::limbasError($e, $lang[56]);
            }
        }

        return $generatedReport;

    }

    /**
     * Einzelbericht eines Datensatzes in DMS
     *
     * @param $report_id
     * @param null $gtabid
     * @param null $ID
     * @param null $level
     * @param null $report_name
     * @param null $relation
     * @param null $gsr
     * @param null $filter
     * @return array|bool|float|int|string
     */
    public function createPDFtoDMS($report_id, $gtabid = null, $ID = null, $level = null, $report_name = null, $relation = null, $gsr = null, $filter = null)
    {
        global $greportlist;

        if ($relation and !is_array($relation['gtabid'])) {
            $relation['gtabid'][0] = $relation['gtabid'];
            $relation['fieldid'][0] = $relation['fieldid'];
            $relation['ID'][0] = $relation['ID'];
        }

        if (!$gtabid) {
            $gtabid = $greportlist["argresult_tabid"][$report_id];
        }

        if (!$level) {
            $level = $greportlist[$gtabid]["target"][$report_id];
        }

        // workaround - archive_fileID - todo
        $GLOBALS['archive_fileID'] = next_db_id('LDMS_FILES', 'ID', 1);
        if (!new_record($GLOBALS['gtab']['argresult_id']['LDMS_FILES'], forceNewID: $GLOBALS['archive_fileID'], insertdata: array($GLOBALS['gtab']['argresult_id']['LDMS_FILES'] => array('LEVEL' => $level)))) {
            return false;
        }

        $generatedReport = $this->createReport(intval($report_id), $gtabid, $ID, ReportOutput::TEMP, $report_name, $gsr, $filter);

        if (file_exists($generatedReport)) {

            $filename = explode('/', $generatedReport);
            $filename = $filename[lmb_count($filename) - 1];

            // upload in Filesystem
            $file["file"][0] = $generatedReport;
            $file["file_name"][0] = $filename;
            $file["forceNewID"][0] = $GLOBALS['archive_fileID']; // workaround - archive_fileID - todo
            $fileid = lmb_fileUpload($file, $level, null, 1);

            // Verknüpfen
            if ($relation and is_array($relation['gtabid'])) {
                foreach ($relation['gtabid'] as $key => $value) {
                    $verkn = init_relation($relation['gtabid'][0], $relation['fieldid'][0], $relation['ID'][0], $fileid);
                    $verkn["linkParam"]["LID"] = $level;
                    set_joins($relation['gtabid'][0], $verkn);
                }
            }

            return $fileid;
        }

        return $generatedReport;

    }


    /**
     * @param LmbMpdf|LmbTcpdf $pdf
     * @param $file
     * @param $page
     * @param $posx
     * @param $posy
     * @return void
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    public function appendPdf(LmbMpdf|LmbTcpdf &$pdf, $file, $page = 1, $posx = 0, $posy = 0): void
    {

        if ($page == 'all') {
            @exec('pdfinfo ' . $file . ' | awk \'/Pages/ {print $2}\'', $output);
            $pages = $output[0];
            for ($i = 1; $i <= $pages; $i++) {
                $this->appendPdf($pdf, $file, $i, $posx, $posy);
            }
            return;
        }

        $pageCount = $pdf->setSourceFile($file);
        $tplIdx = $pdf->importPage($page);
        $pdf->addPage();
        $pdf->useTemplate($tplIdx, $posx, $posy);
    }


    /**
     * PDF name
     *
     * @param string $reportName
     * @param string $reportSaveName
     * @param int $id
     * @param string|null $reportRename
     * @param bool $addTimestamp
     * @return string
     */
    public function reportSaveName(string $reportName, string $reportSaveName, int $id, ?string $reportRename = null, bool $addTimestamp = false): string
    {
        
        if (is_string($reportRename) && !empty($reportRename)) {
            # own name
            $name = trim($reportRename);
        } elseif (!empty($reportSaveName) && $reportSaveName !== 'default') {
            # defined name
            $name = trim($reportSaveName);
            if(str_starts_with($name, 'reportName')) {
                if(function_exists( $name)){
                    $name = $name($id,$reportName,$reportRename);
                } else {
                    $name = '';
                }
            }
        }
        
        
        if(empty($name)) {
            # default name
            $idSuffix = '';
            if ($id) {
                $idSuffix = '_' . $id;
            }
            $name = $reportName . $idSuffix . ($addTimestamp ? ('_' . date('U')) : '') . '.pdf';
        }

        $name = str_replace([' ', '.pdf'], ['_', ''], $name);

        return $name . '.pdf';
    }


    /**
     * @return array
     */
    public function getArchivedFileIds(): array
    {
        return $this->archivedFileIds;
    }

    /**
     * verbinde Berichte
     *
     * @param array $fileList
     * @param int $ID
     * @param null $report_rename
     * @return false|string
     * @throws MpdfException
     */
    public function mergeReport(array $fileList, int $ID, $report_rename = null): bool|string
    {
        global $session;
        global $umgvar;

        $report_name = $this->reportSaveName($GLOBALS['report_name'] ?? '', $GLOBALS['report_savename'] ?? '', $ID, $report_rename, true);

        if ($umgvar['use_gs']) { # use gs
            # use postcsript
            if ($umgvar['ps_compatibilitylevel'] <= 1) {
                $ps_comp = '1.4';
            } else {
                $ps_comp = $umgvar['ps_compatibilitylevel'];
            }
            if ($umgvar['ps_output']) {
                $ps_output = $umgvar['ps_output'];
            } else {
                $ps_output = 'default';
            }
            if ($umgvar['ps_downsample']) {
                $ps_downsample = ' -dDownsampleColorImages=true -dColorImageDownsampleType=/Average -dColorImageDownsampleThreshold=1.3';
                if ($umgvar['ps_imageresolution']) {
                    $ps_downsample .= ' -dColorImageResolution=' . $umgvar['ps_imageresolution'];
                }
            }
            $sys = exec('cd ' . USERPATH . $session['user_id'] . "/temp/; gs -dNOPAUSE -dBATCH -dCompatibilityLevel=$ps_comp -dPDFSETTINGS=/$ps_output $ps_downsample -sDEVICE=pdfwrite -sOutputFile=" . $report_name . " " . implode(' ', $fileList));
        } else { # use fpdi

            if ($this->type === 'tcpdf') {
                $pdfx = new LmbTcpdf();
            } else {
                $pdfx = new LmbMpdf();
            }

            $pdfx->concat($fileList);
            $pdfx->Output(USERPATH . $session['user_id'] . '/temp/' . $report_name, 'F');
        }

        if (file_exists(USERPATH . $session['user_id'] . '/temp/' . $report_name)) {
            return USERPATH . $session['user_id'] . '/temp/' . $report_name;
        } else {
            return false;
        }
    }

    /**
     * @param array $report
     * @param int $ID
     * @return LmbTcpdf|LmbMpdf
     */
    protected abstract function create_pdf(array &$report, int $ID): LmbTcpdf|LmbMpdf;


    /**
     * @param LmbMpdf|LmbTcpdf $pdf
     * @param array $report
     * @param int $ID
     */
    protected abstract function renderContent(LmbMpdf|LmbTcpdf &$pdf, array $report, int $ID): void;


    /**
     * Generiere Berichte
     *
     * @param int $reportId
     * @param $ID
     * @param ReportOutput $reportOutput
     * @param null $report_rename
     * @param null $gsr
     * @param null $filter
     * @param PrinterOptions|null $printerOptions
     * @return false|string
     * @throws MpdfException
     */
    protected function createPDF(int $reportId, $ID, ReportOutput $reportOutput, $report_rename = null, $gsr = null, $filter = null, PrinterOptions $printerOptions = null): bool|string
    {
        global $LINK;
        global $umgvar;

        // setting filter for listmode








        $GLOBALS['rgsr'] = $gsr;
        $GLOBALS['rfilter'] = $filter;

        $report = get_report($reportId, 0);
        $report['report_output'] = $reportOutput->value;
        $report['report_rename'] = $report_rename;
        $report['archive_fileID'] = false;


        // get new ldms_files ID / create DMS record if archived
        if (
            $reportOutput === ReportOutput::ARCHIVE ||
            ($reportOutput === ReportOutput::PRINT && $umgvar['printer_cache'])
        ) {
            # new DMS record
            $report['archive_fileID'] = next_db_id('LDMS_FILES', 'ID', 1);
            if (!new_record($GLOBALS['gtab']['argresult_id']['LDMS_FILES'], null, null, null, null, null, null, $report['archive_fileID'])) {
                return false;
            }
        }

        // workaround - use archive_fileID from lmb_reportCreatePDFtoDMS() - todo
        if ($GLOBALS['archive_fileID']) {
            $report['archive_fileID'] = $GLOBALS['archive_fileID'];
        }


        // generate PDF
        $pdf = $this->create_pdf($report, intval($ID));
        if (!$pdf) {
            return false;
        }

        if (function_exists('lmbAfterReportObjectCreated')) {
            lmbAfterReportObjectCreated($pdf, $report, intval($ID), $reportOutput, $report_rename);
        }
        
        # fill PDF with specific content
        $this->renderContent($pdf, $report, intval($ID));


        # save PDF
        $tempFilePath = $this->storePdf($pdf, $report, intval($ID), $reportOutput, $report_rename);

        # clear memory (workaround)
        /*$pdf->Close();
        foreach ($pdf as &$value) {
            $value = null;
        }
        $pdf = null;
        unset($pdf);*/
        if (!$tempFilePath) {
            return false;
        }

        if (function_exists('lmbAfterReportCreated')) {
            lmbAfterReportCreated($this, $reportId, $ID, $tempFilePath, $reportOutput);
        }

        $report['temp_file_path'] = $tempFilePath;

        # archive to DMS
        if ($reportOutput === ReportOutput::ARCHIVE ||
            ($reportOutput === ReportOutput::PRINT && $umgvar['printer_cache'])
        ) {

            $forceId = null;
            if (array_key_exists('archive_fileID', $report)) {
                $forceId = intval($report['archive_fileID']);
            }

            # new DMS record
            $archiveFileId = $this->reportArchive($tempFilePath, $report, intval($ID), $forceId);

            if ($archiveFileId !== false) {
                $report['archive_fileID'] = intval($archiveFileId);
            } else {
                return false;
            }
        }

        if (array_key_exists('archive_fileID', $report)) {
            $this->archivedFileIds[] = intval($report['archive_fileID']);
        }

        # to printer cache
        if ($reportOutput === ReportOutput::PRINT && $LINK[304]) {

            $forcePrint = $printerOptions?->directPrint ?? false;
            
            $printed = lmbPrint($printerOptions?->printerId, $tempFilePath, $report['archive_fileID'], $forcePrint, $printerOptions);

            if (!$printed) {
                return false;
            }
        }


        return $tempFilePath;
    }


    /**
     * PDF archivieren
     *
     * @param string $tempFilePath
     * @param array $report
     * @param int $ID
     * @param int|null $forceId
     * @return array|bool|float|int|string
     */
    protected function reportArchive(string $tempFilePath, array $report, int $ID, ?int $forceId = null)
    {
        global $db;

        $name = explode("/", $tempFilePath);
        $name = $name[lmb_count($name) - 1];

        if (file_exists($tempFilePath)) {

            # ------- pdf archivieren ---------
            if (is_numeric($report["target"])) {
                $sqlquery = "SELECT FIELD_ID,TAB_ID,TYP,LEVEL,ID FROM LDMS_STRUCTURE WHERE ID = " . $report["target"];
                $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, '', __FILE__, __LINE__);

                if (!$rs) {
                    $commit = 1;
                }
                if (lmbdb_result($rs, "ID")) {

                    $level = lmbdb_result($rs, "ID");
                    $field_id = lmbdb_result($rs, "FIELD_ID");
                    $dublicate["forceDelnm"][0] = 1;

                    // use relation destination from LDMS_STRUCTURE
                    #if(lmbdb_result($rs, "TYP") == 7){
                    if ($ID and $field_id) {
                        $relation = array("datid" => $ID, "gtabid" => $report["referenz_tab"], "fieldid" => $field_id);
                    }

                    $file["file"][0] = $tempFilePath;
                    $file["file_name"][0] = $name;
                    $file["file_type"][0] = 0;
                    $file["file_archiv"][0] = 0;
                    if ($forceId !== null) {
                        $file['forceNewID'][0] = $forceId;
                    }
                    $fileid = lmb_fileUpload($file, $level, $relation, 1, $dublicate);
                    if (!$fileid) {
                        return false;
                    }

                    return $fileid;
                }
            }
        }

        return false;
    }


    /**
     * PDF ausgeben
     *
     * @param LmbMpdf|LmbTcpdf $pdf
     * @param array $report
     * @param int $id
     * @param ReportOutput $reportOutput
     * @param null $report_rename
     * @return string
     * @throws MpdfException
     */
    protected function storePdf(LmbMpdf|LmbTcpdf &$pdf, array $report, int $id, ReportOutput $reportOutput, $report_rename = null): string
    {
        global $session;
        global $umgvar;

        # pdf name
        $name = $this->reportSaveName($report['name'] ?? '', $report['savename'] ?? '', $id, $report_rename, ($reportOutput->value >= 2) /* archive */);
        $out = USERPATH . $session['user_id'] . '/temp/' . $name;
        $path = USERPATH . $session['user_id'] . '/temp/' . $name;

        if (function_exists('lmbBeforeReportOutput')) {
            lmbBeforeReportOutput($pdf, $report, $id, $reportOutput, $report_rename);
        }
        
        # write pdf to file
        $pdf->Output($path, 'F');

        # use postscript to compress
        if (!$this->standardApplied && $umgvar['use_gs'] && file_exists($path)) {
            rename($path, $path . 'gs');
            $sys = exec('cd ' . USERPATH . $session['user_id'] . '/temp/; gs -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile="' . $name . '" "' . $path . 'gs"');
            unlink($path . 'gs');
        }

        return $out;
    }

    /**
     * alle Berichte einer Tabelle
     *
     * @param $gtabid
     * @param int $reportId
     * @param ReportOutput $reportOutput
     * @param $filter
     * @param $gsr
     * @param $verkn
     * @param null $report_rename
     * @param PrinterOptions|null $printerOptions
     * @return false|string
     * @throws MpdfException
     */
    private function createReportForFiltered($gtabid, int $reportId, ReportOutput $reportOutput, &$filter, $gsr, $verkn, $report_rename = null, PrinterOptions $printerOptions = null): bool|string
    {
        global $gresult;

        # Abfrage
        $filter['anzahl'][$gtabid] = 'all';
        $gresult = get_gresult($gtabid, 1, $filter, $gsr, $verkn);

        $ids = [];
        foreach ($gresult[$gtabid]['id'] as $id) {
            $ids[] = intval($id);
        }

        return $this->generateReportForIds($ids, $reportId, $reportOutput, $report_rename, $printerOptions);
    }


    /**
     * Berichtsliste mehrerer Datensätze
     *
     * @param $gtabid
     * @param int $reportId
     * @param ReportOutput $reportOutput
     * @param $use_record
     * @param null $report_rename
     * @param PrinterOptions|null $printerOptions
     * @return bool|string
     * @throws MpdfException
     */
    private function createReportForSelected($gtabid, int $reportId, ReportOutput $reportOutput, $use_record, $report_rename = null, PrinterOptions $printerOptions = null): bool|string
    {
        $use_record = explode(';', $use_record);
        $use_record = array_unique($use_record);

        $ids = [];
        foreach ($use_record as $value) {
            $recel = explode('_', $value);
            if ($recel[1] == $gtabid) {
                $ids[] = intval($recel[0]);
            }
        }
        return $this->generateReportForIds($ids, $reportId, $reportOutput, $report_rename, $printerOptions);
    }

    /**
     * @param array $ids
     * @param int $reportId
     * @param ReportOutput $reportOutput
     * @param string|null $reportRename
     * @param PrinterOptions|null $printerOptions
     * @return bool|string
     * @throws MpdfException
     */
    private function generateReportForIds(array $ids, int $reportId, ReportOutput $reportOutput, ?string $reportRename = null, PrinterOptions $printerOptions = null): bool|string
    {
        $ids = array_unique($ids);

        $pdfFilePath = false;
        $fileList = [];
        $fileCounter = 0;
        foreach ($ids as $id) {
            $pdfFilePath = $this->createPDF($reportId, $id, $reportOutput, $reportRename . '_' . $id, null, null, $printerOptions);
            if ($pdfFilePath !== false) {
                $fileList[] = $pdfFilePath;
                $fileCounter++;
            }
        }
        if ($fileCounter > 1) {
            return $this->mergeReport($fileList, 0, $reportRename);
        } elseif ($pdfFilePath) {
            return $pdfFilePath;
        }
        return false;
    }


}
