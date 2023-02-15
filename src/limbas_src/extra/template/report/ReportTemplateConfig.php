<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
require_once(__DIR__ . '/../base/TemplateConfig.php');
require_once(__DIR__ . '/ReportTemplateElement.php');
require_once(__DIR__ . '/ReportDataPlaceholder.php');

class ReportTemplateConfig extends TemplateConfig implements TemplateConfigInterface {

    public $report;
    public $parameter;

    /**
     * ReportTemplateConfig constructor.
     * @param $report array limbas report array
     * @param $parameter string limbas report editor param
     */
    public function __construct($report, $parameter, $datid) {
        $this->report = $report;
        $this->parameter = $parameter;
        $this->datIDs[] = $datid;
    }

    public function getGtabid() {
        return $this->report['referenz_tab'];
    }

    public function getFunctionPrefix() {
        return 'report_';
    }

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html,$id = 0, $gtabid = null, $datid = null, $recursion = 0) {
        return new ReportTemplateElement($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public function getDataPlaceholderInstance($chain, $options, $altValue) {
        return new ReportDataPlaceholder($chain, $options, $altValue,!$this->resolveDataPlaceholders);
    }

    public function getMedium() {
        return "report";
    }

    public function forTemporaryBaseTable($gtabid, $func) {
        $oldTab = $this->report['referenz_tab'];
        $this->report['referenz_tab'] = $gtabid;
        $result = $func();
        $this->report['referenz_tab'] = $oldTab;
        return $result;
    }

    public function resolveDataPlaceholdersForTemporaryBaseTable($baseTableID, $datid, &$placeholders) {
        $oldTab = $this->report['referenz_tab'];
        $this->report['referenz_tab'] = $baseTableID;
        ReportTemplateElement::resolveDataPlaceholdersForTable($datid, $placeholders);
        $this->report['referenz_tab'] = $oldTab;
    }
}

/**
 * Returns the current dataset ID.
 * If not in a repeated table row, the base dataset ID is returned
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function report_datid($depth=0) {
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return TemplateConfig::$instance->datIDs[$depth];
}

/**
 * Returns the current index of a table row
 * @param int $startFrom start counting from this value (default: 0, 1, 2, ...)
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function report_index($startFrom=0, $depth=0) {
    if (!$startFrom) {
        $startFrom = 0;
    } else {
        $startFrom = intval($startFrom);
    }
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return $startFrom + TemplateConfig::$instance->tableRowIndex[$depth];
}

/**
 * Returns the current column index of a table row
 * @param int $startFrom start counting from this value (default: 0, 1, 2, ...)
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function report_colIndex($startFrom=0, $depth=0) {
    if (!$startFrom) {
        $startFrom = 0;
    } else {
        $startFrom = intval($startFrom);
    }
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return $startFrom + TemplateConfig::$instance->tableColIndex[$depth];
}

/**
 * Returns current collected table row data (values of Data- and FunctionPlaceholders):
 * [->arrow->identifier][row][col][elementIndex] => value
 * Also contains data of subTables:
 * [->arrow->identifier][row][col][->sub->arrow->identifier][row]...
 * If this is called from inside a TableRow, data will only contain the data of that row.
 * If more data is needed, take a look at TemplateConfig::tableData for outer table row's data
 * @see TemplateConfig::tableData
 * @return array
 */
function report_tableRowData() {
    if (!TemplateConfig::$instance->tableRowIndex) {
        return TemplateConfig::$instance->tableData;
    } else {
        $currentRow = intval(report_index());
        return TemplateConfig::$instance->currentTableData[0][$currentRow];
    }
}

/**
 * Sets a background image for every page of the report.
 * If a page margin is set, an image cannot be used as background on the whole page. Please use a pdf instead.
 * <code>
 * ${=background(42)}                 // centered with margin
 * ${=background(42, 0, 0)}           // fill page completely
 * ${=background(42, 0, -1, -1, 200)} // no margin left/right, top margin of 200.
 * ${=background(42, 200, 400)}       // small rectangle in middle of page
 * </code>
 * @param string|int $pathOrId either Limbas file ID (e.g. 42) or path from Limbas DMS (e.g. "öffentlicher Ordner/Dokumente/Berichtsvorlage.png"). Images and PDFs are supported.
 * @param int|null $x x position of the image from upper left corner of the report. 0 for no margin. -1/null for default margin as specified in report.
 * @param int|null $y y position of the image from upper left corner of the report. 0 for no margin. -1/null for default margin as specified in report.
 * @param int|null $w width of the image. -1/null to render image centered on page.
 * @param int|null $h height of the image. -1/null to render image centered on page.
 */
function report_background($pathOrId, $x=null, $y=null, $w=null, $h=null) {
    global $lmbOnCreatePdf;

    if ($x == -1) {
        $x = null;
    }
    if ($y == -1) {
        $y = null;
    }
    if ($w == -1) {
        $w = null;
    }
    if ($h == -1) {
        $h = null;
    }

    require_once(COREPATH . 'extra/explorer/filestructure.lib');

    // path -> file id
    $fileID = null;
    if (is_numeric($pathOrId)) {
        $fileID = $pathOrId;
    } else {
        $fileparts = explode('/', html_entity_decode($pathOrId));
        $filename = array_pop($fileparts);
        $folder = implode('/', $fileparts);
        $fileID = lmb_getFileIDFromName(lmb_getLevelFromPath($folder), $filename);
    }

    // set callback for during pdf creation
    $previousOnCreatePdf = $lmbOnCreatePdf;
    $lmbOnCreatePdf = function($pdf) use ($previousOnCreatePdf, $fileID, $x, $y, $w, $h) {
        global $db, $action;
        global $gmimetypes;
        global $umgvar;

        // file id -> secname
        $sqlquery = 'SELECT SECNAME, MIMETYPE, LEVEL FROM LDMS_FILES WHERE ID='.parse_db_int($fileID);
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        $ext = $gmimetypes['ext'][lmbdb_result($rs, 'MIMETYPE')];
        #$secname = $umgvar['upload_pfad'] . lmbdb_result($rs, 'SECNAME') . '.' . $ext;
        $secname = lmb_getFilePath($fileID,lmbdb_result($rs, 'LEVEL'),lmbdb_result($rs, 'SECNAME'),$ext);

        // default x/y at margin. ignore margin when set manually
        if ($x === null) {
            $x = 0;
        } else {
            $x -= $pdf->rp['background']['page_style'][4];
        }
        if ($y === null) {
            $y = 0;
        } else {
            $y -= $pdf->rp['background']['page_style'][2];
        }

        // calculate auto width/height
        if ($w === null) {
            $w = intval($pdf->rp['background']['page_style'][0]) - 2 * $x - 2 * $pdf->rp['background']['page_style'][4];
        }
        if ($h === null) {
            $h = intval($pdf->rp['background']['page_style'][1]) - 2 * $y - 2 * $pdf->rp['background']['page_style'][2];
        }

        // fake limbas element
        $bzm0 = 0;
        if(is_array($pdf->rp['background']['id'])) {
            $bzm0 = lmb_count($pdf->rp['background']['id']);
        }
        $pdf->rp['background']["id"][$bzm0] = -1;
		$pdf->rp['background']["typ"][$bzm0] = 'bild';
		$pdf->rp['background']["posx"][$bzm0] = $x;
		$pdf->rp['background']["posy"][$bzm0] = $y;
		$pdf->rp['background']["posyabs"][$bzm0] = $y;
		$pdf->rp['background']["width"][$bzm0] = $w;
		$pdf->rp['background']["height"][$bzm0] = $h;
		$pdf->rp['background']["style"][$bzm0] = array();
		$pdf->rp['background']["data_type"][$bzm0] = 100;
		$pdf->rp['background']["tab_size"][$bzm0] = $secname;
		$pdf->rp['background']["pic_name"][$bzm0] = $secname;

		// call previous callback if exists
        if (is_callable($previousOnCreatePdf)) {
            $previousOnCreatePdf($pdf);
        }
    };
}

/**
 * Breaks the current page and continues on the next one.
 * @return string
 */
function report_pageBreak() {
    /** @noinspection HtmlUnknownAttribute */
    return defined('REPORT_TCPDF') ? '<br pagebreak="true">' : '<pagebreak>';
}



/**
 * Returns all relevant report parameters as array.
 * Useful as parameter of another function.
 * 
 * @return array
 */
function report_getReportParameters() {
    
    $report_parameters = [
        'gtabid' => TemplateConfig::$instance->report['referenz_tab'] ?? TemplateConfig::$instance->parameter['referenz_tab'] ?? 0,
        'datid' => TemplateConfig::$instance->datIDs,
        'report_id' => TemplateConfig::$instance->report['report_id'] ?? TemplateConfig::$instance->parameter['report_id'] ?? 0,
        'listmode' => TemplateConfig::$instance->report['listmode'] ?? TemplateConfig::$instance->parameter['listmode'] ?? false,
        'report_output' => TemplateConfig::$instance->report['report_output'] ?? TemplateConfig::$instance->parameter['report_output'] ?? 0,
        'name' => TemplateConfig::$instance->report['name'] ?? TemplateConfig::$instance->parameter['name'] ?? '',
        'savename' => TemplateConfig::$instance->report['report_rename'] ?? TemplateConfig::$instance->parameter['report_rename'] ?? '',
        'level_id' => TemplateConfig::$instance->report['target'] ?? TemplateConfig::$instance->parameter['target'] ?? 0,
        'archive_fileID' => TemplateConfig::$instance->report['archive_fileID'] ?? TemplateConfig::$instance->parameter['archive_fileID'] ?? 0,
    ];
    
    return $report_parameters;
}
