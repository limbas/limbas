<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report;

use Limbas\extra\diagram\LmbChart;
use Limbas\extra\template\report\ReportTemplateRender;
use lmb_log;
use ParseError;

class ReportTcpdf extends Report
{

    public function __construct()
    {
        $this->type = 'tcpdf';

        $fontdir = $GLOBALS['umgvar']['pfad'] . '/inc/fonts/';
        if (file_exists($fontdir) && !defined('K_PATH_FONTS')) {
            define('K_PATH_FONTS', $fontdir);
        }

        parent::__construct();
    }

    # Diagramm einbinden
    public function append_subChart(LmbTcpdf &$pdf, &$report, $ID, $el, $chart_id, $height = 0, $width = 0)
    {
        global $gdiaglist;
        global $umgvar;
        global $gsr;
        global $filter;
        global $session;

        $gtabid = $gdiaglist['gtabid'][$chart_id];
        if (!$gdiaglist[$gtabid]['id'][$chart_id]) {
            return;
        }

        if ($file = $gdiaglist[$gtabid]['template'][$chart_id] and file_exists($umgvar['pfad'] . $file)) {
            # use a template
            require_once($umgvar['path'] . $file);
            $link = "USER/{$session['user_id']}/temp/chart_{$chart_id}.png";

        } else {
            # use the diag editor
            $link = LmbChart::makeChart($chart_id, $gsr, $filter);
        }
        if (!file_exists($link)) {
            return;
        }

        $h = $report['height'][$el];
        $w = $report['width'][$el];
        if ($height) {
            $w = 0;
        }
        if ($width) {
            $h = 0;
        }

        $printedCell = $pdf->printcell(
            $link,
            $report['posx'][$el],
            $report['posy'][$el],
            $w,
            $h,
            $report['style'][$el],
            'bild'
        );
        $pdf->print_element($printedCell);
    }


    /*------- Bild ---------*/
    public function print_bild(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $umgvar;

        $pic_orgname = $report['tab_size'][$el];
        $pic_name = $report['pic_name'][$el];
        $report_value = $report['value'][$el];
        $pic_quality = $report['data_type'][$el];
        $pic_style = $report['pic_style'][$el];

        $org_extension = explode('.', $pic_orgname);
        $org_extension = $org_extension[lmb_count($org_extension) - 1];

        if ($this->hide_element($pdf, $report['style'][$el][29])) {
            return true;
        }

        if (lmb_strtolower($org_extension) == 'pdf') { # PDF Integration
            if (lmb_strpos($pic_orgname, $umgvar['path']) === false) {
                $report_value = UPLOADPATH . 'report/' . $pic_orgname;
            } else {
                $report_value = $pic_orgname;
            }

            $pdf->setSourceFile($report_value);
            $tplidx = $pdf->ImportPage(1);
            $pdf->useTemplate($tplidx, $report['page_style'][4] + $report['posx'][$el], $report['page_style'][2] + $report['posy'][$el], $report['width'][$el], $report['height'][$el]);

            # Hintergrund-Rechteck ----------------------------
            # TODO maybe improve
            $par = $pdf->printcell($report_value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'bild');
            if (!$par[2] or !$par[3]) {
                $imsize = $pdf->getTemplateSize($tplidx, $par[2], $par[3]);
                if (!$par[2]) {
                    $par[2] = $imsize['w'] + $par[10][22];
                }
                if (!$par[3]) {
                    $par[3] = $imsize['h'] + $par[10][22] + 4;
                }
            }
            if ($par[6]) {
                $pdf->Rect($report['page_style'][4] + $par[0] - $par[10][19] / 2, $report['page_style'][2] + $par[1] - $par[10][19] / 2, $par[2] + $par[10][19], $par[3] + $par[10][19]);
            }

        } else { # Bilder
            if (lmb_strpos($pic_orgname, $umgvar['path']) === false) {
                if ($pic_style and file_exists(UPLOADPATH . 'report/' . $pic_orgname)) {
                    if (!file_exists(TEMPPATH . 'thumpnails/report/r_' . $pic_name)) {
                        $target = TEMPPATH . 'thumpnails/report/r_' . $pic_name;
                        copy(UPLOADPATH . 'report/' . $pic_orgname, $target);
                        $picstyle = explode(';', $pic_style);
                        foreach ($picstyle as $key => $value) {
                            $cmd = $umgvar['imagemagick'] . '/convert ' . $umgvar['imagemagicklimit'] . " -quality $pic_quality $value -strip $target $target";
                            system($cmd);
                        }
                        if (file_exists($target)) {
                            $report_value = TEMPPATH . 'thumpnails/report/r_' . $pic_name;
                        }
                    } else {
                        $report_value = TEMPPATH . 'thumpnails/report/r_' . $pic_name;
                    }
                } else {
                    $report_value = UPLOADPATH . 'report/' . $pic_orgname;
                }
            } else {
                $report_value = $pic_orgname;
            }
            $printedCell = $pdf->printcell($report_value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'bild');
            $pdf->print_element($printedCell);
        }
    }

    /*----------------- Element-Schleife PDF -------------------*/
    public function list_elements_pdf(LmbTcpdf &$pdf, $ID, &$report, $subform = null)
    {
        static $lastposy;

        if ($report['id']) {
            foreach ($report['id'] as $bzm => $value) {
                # background
                if ($report['background'][$bzm]) {
                    if (!$this->hide_element($pdf, $report['background'][$bzm])) {
                        continue;
                    }
                }

                # dependent on other element
                if ($otherEl = $report['style'][$bzm][39] and !$report['dbvalue'][$report['arg_result'][$otherEl]][0]) {
                    # speichere Y Position Elements
                    $lastposy[$bzm] = $pdf->GetY();
                    continue;
                }

                # PageBreak before
                if ($report['style'][$bzm][35] == 'true') {
                    $pdf->SetAutoPageBreak(false, 0);
                }
                if ($report['style'][$bzm][34] == 1) {
                    $pdf->AddPage();
                    $report['posy'][$bzm] = null;
                }

                # is subform
                if ($subform) {
                    $report['posy'][$bzm] = $report['posy'][$bzm] + $subform;
                }

                # has relative position
                if ($otherEl = $report['style'][$bzm][36]) {
                    # setze Position auf letzte posy des relativen Elternelements
                    $pdf->setY($lastposy[$report['arg_result'][$otherEl]]);

                    # Zeilenabstand berechnen
                    $ln = (-$report['posyabs'][$report['arg_result'][$otherEl]] - $report['height'][$report['arg_result'][$otherEl]] + $report['posyabs'][$bzm]);
                    $pdf->Ln($ln);
                    $report['posy'][$bzm] = null;
                }

                # element function
                $fname = 'print_' . $report['typ'][$bzm];
                $this->$fname($pdf, $ID, $report, $bzm);

                # PageBreak after
                if ($report['style'][$bzm][35] == 'true') {
                    $pdf->SetAutoPageBreak(true, $report['footerpos']);
                }
                if ($report['style'][$bzm][34] == 2) {
                    $pdf->AddPage();
                }

                # save Y position of element
                $lastposy[$bzm] = $pdf->GetY();
            }
        }
    }


    # Bericht anhängen
    public function append_report(LmbTcpdf &$pdf, $report_id, $ID)
    {
        $pdf->AddPage();
        $pdf->SetXY(0, 0);

        $report = get_report($report_id, 0);
        $this->fill_pdf($pdf, $report, $ID);
    }

# Unterbericht einfügen
    public function append_subReport(LmbTcpdf &$pdf, &$report, $ID, $el, $report_id, $height = 0, $width = 0)
    {
        $subreportfile = $this->createPDF(intval($report_id), $ID, ReportOutput::TEMP, 'temp_' . $report_id . '.pdf');
        $report['tab_size'][$el] = $subreportfile;
        if ($width) {
            $report['height'][$el] = 0;
        }
        if ($height) {
            $report['width'][$el] = 0;
        }
        $this->print_bild($pdf, $ID, $report, $el);
    }


    /**
     * PDF erzeugen
     *
     * @param array $report
     * @param int $ID
     * @return LmbTcpdf
     */
    protected function create_pdf(array &$report, int $ID): LmbTcpdf
    {
        global $lmbOnCreatePdf;

        #Header - Elemente
        $rp['header'] = element_list($ID, $report, 'header');
        #Footer - Elemente
        $rp['footer'] = element_list($ID, $report, 'footer');
        #Background - Elemente
        $rp['background'] = element_list($ID, $report, 'background');

        # --- neues PDF -----------------------------------
        $width = $report['page_style'][0];
        $height = $report['page_style'][1];
        $orientation = ($width > $height) ? 'l' : 'p';
        $isunicode = false;
        if ($GLOBALS['umgvar']['charset'] == 'UTF-8') {
            $isunicode = true;
        }

        $pdf = new LmbTcpdf($orientation, 'pt', array($width, $height), $isunicode, $GLOBALS['umgvar']['charset']);

        # Fußumbruch bei vorhandenen Fußelementen
        $this->SetPageBreak($pdf, $report['footerpos'], $report);

        $pdf->rp = $rp;
        $pdf->report = &$report;
        $pdf->report_id = $report['report_id'];
        $pdf->ID = $ID;

        $pdf->Open();
        $pdf->SetMargins($report['page_style'][4], $report['page_style'][2], $report['page_style'][5], true);

        # set all needed fonts
        set_fonts($pdf, $report);

        // callback to modify pdf or elements
        if (is_callable($lmbOnCreatePdf)) {
            $lmbOnCreatePdf($pdf);
        }

        $pdf->AddPage();
        $pdf->SetSubject($report['name'] . ' ' . date('Y.m.d'));
        $pdf->SetTitle($report['name'] . ' ' . date('Y.m.d'));
        $pdf->SetCompression(true);
        $pdf->SetCreator('LIMBAS');
        $pdf->SetFont($report['default_font']);
        $pdf->SetFontSize($report['default_font_size']);
        #$pdf->SetTextColor(12, 12, 12);

        return $pdf;
    }


    protected function renderContent(LmbMpdf|LmbTcpdf &$pdf, array $report, int $ID): void
    {
        $this->fill_pdf($pdf, $report, $ID);
    }

#region FieldType Functions

    /*------- Text ---------*/
    private function print_text(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        $style = &$report['style'][$el];
        if (!$this->hide_element($pdf, $style[29])) {
            if ($style[42] == 'true') {
                $type = 'html';
            } else {
                $type = 'text';
            }
            $printedCell = $pdf->printcell($report['value'][$el], $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], $type);
            $pdf->print_element($printedCell);
        }
        $GLOBALS['glob_el'][$report['id'][$el]] = $report['value'][$el];
    }

    /*------- Rechteck ---------*/
    private function print_rect(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell('', $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'rect');
            $pdf->print_element($printedCell);
        }
    }

    /*------- Linie ---------*/
    /**
     * @deprecated
     */
    private function print_line(LmbTcpdf $pdf, $ID, $report, $el)
    {
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell('', $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'line');
            $pdf->print_element($printedCell);
        }
    }

    /*------- Ellipse ---------*/
    /**
     * @deprecated
     */
    private function print_ellipse(LmbTcpdf $pdf, $ID, $report, $el)
    {
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell('', $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'ellipse');
            $pdf->print_element($printedCell);
        }
    }

    /*------- Bericht Nr ---------*/
    /**
     * @deprecated
     */
    private function print_bnr(LmbTcpdf $pdf, $ID, $report, $el)
    {
        global $NEXT_B_NR;
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell($NEXT_B_NR, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'bnr');
            $pdf->print_element($printedCell);
        }
        $GLOBALS['glob_el'][$report['id'][$el]] = $NEXT_B_NR;
    }

    /*------- Datum ---------*/
    private function print_datum(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        $date = local_date(1);
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell($date, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'datum');
            $pdf->print_element($printedCell);
        }
        $GLOBALS['glob_el'][$report['id'][$el]] = $date;
    }

    /*------- Seiten-Nummer ---------*/
    private function print_snr(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        $pageNo = $pdf->PageNo();
        if (!$this->hide_element($pdf, $report['style'][$el][29])) {
            $printedCell = $pdf->printcell($pageNo, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'snr');
            $pdf->print_element($printedCell);
        }
        $GLOBALS['glob_el'][$report['id'][$el]] = $pageNo;
    }

    /*------- Unterbericht ---------*/
    private function print_ureport(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $greportlist;
        global $gfield;

        $subreportid = $report['pic_res'][$el];
        $parent_gtabid = $report['referenz_tab'];
        $gtabid = $greportlist['argresult_tabid'][$subreportid];

        # Bericht einbetten
        if ($report['pic_typ'][$el] == 1) {
            if (lmb_substr($report['parameter'][$el], 0, 6) == 'return') {
                # ID Array
                try {
                    $rval = eval($report['parameter'][$el]);
                    if ($rval and !isset($arg_result)) {
                        $arg_result = $rval;
                    }
                } catch (ParseError $e) {
                    lmb_log::error($e->getMessage());
                    $arg_result = array();
                }
                foreach ($arg_result as $key => $value) {
                    $this->merge_subReport($pdf, $value, $subreportid);
                }

            } else {
                # gresult erzeugen
                $filter['anzahl'][$gtabid] = 'all';
                $filter['nolimit'][$gtabid] = 1;

                # versuche Verknüpfung automatisch aufzulösen
                if ($relation = array_keys($gfield[$parent_gtabid]['verkntabid'], $gtabid)) {
                    $relation = $relation[0];
                    $verkn = set_verknpf($parent_gtabid, $relation, $ID, null, null, 1, 1);
                }

                # Parameter einbinden
                if ($report['parameter'][$el]) {
                    try {
                        eval($report['parameter'][$el]);
                    } catch (ParseError $e) {
                        lmb_log::error($e->getMessage());
                    }
                }

                $gresult = get_gresult($gtabid, 1, $filter, $gsr, $verkn, 'ID', null, $extension);
                foreach ($gresult[$gtabid]['id'] as $key => $value) {
                    $this->merge_subReport($pdf, $value, $subreportid);
                }
            }

        } elseif ($report['pic_typ'][$el] == 2) {
            # Bericht einhängen per fpdi
            $this->append_subReport($pdf, $report, $ID, $el, $subreportid, $report['width'][$el], $report['height'][$el]);
        }
    }

    /*------- Formel ---------*/
    private function print_formel(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $glob_el;
        global $gtab;
        global $gfield;
        global $session;
        global $umgvar;
        global $userdat;
        global $report_ext;

        $style = $report['style'][$el];

        # eval
        $report_value = trim($report['value'][$el]);
        if ($report_value) {
            try {
                $rval = eval($report_value);
                if ($rval and !isset($arg_result)) {
                    $arg_result = $rval;
                }
            } catch (ParseError $e) {
                lmb_log::error($e->getMessage());
                $arg_result = '';
            }
        }

        # hidden
        if (!$this->hide_element($pdf, $style[29]) and $arg_result) {
            # as HTML
            if ($style[42] == 'true') {
                $type = 'html';
            } else {
                $type = 'formel';
            }
            $printedCell = $pdf->printcell($arg_result, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], $type);
            $pdf->print_element($printedCell);
        }

        # TODO wiki?
        if (isset($garg_result)) {
            $GLOBALS['glob_el'][$report['id'][$el]] = $garg_result;
        } else {
            $GLOBALS['glob_el'][$report['id'][$el]] = $arg_result;
        }
    }

    /*------- Db Inhalte ---------*/
# TODO tagmod?
    private function print_dbdat(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        if (!$report['dbvalue'][$el]) {
            return true;
        }

        $dbvalue = $report['dbvalue'][$el];
        $style = $report['style'][$el];

        if ($style[40]) {
            $seperator = str_replace("\\\\t", "\t", str_replace("\\\\n", "\n", $style[40]));
            $report_dbvalue = trim(implode($seperator, $dbvalue));
        } else {
            $report_dbvalue = implode("\n", $dbvalue);
        }

        $GLOBALS['glob_el'][$report['id'][$el]] = $dbvalue;
        $report_dbvalue = explode('#*#', $report_dbvalue, 2);
        if (lmb_count($report_dbvalue) == 2) {
            $valuetype = $report_dbvalue[0];
            $value = $report_dbvalue[1];
        } else {
            $valuetype = null;
            $value = $report_dbvalue[0];
        }

        if (!$value) {
            return true;
        }

        # hidden
        if ($this->hide_element($pdf, $style[29])) {
            return true;
        }

        if ($valuetype == 'IMAGE') {
            $printedCell = $pdf->printcell($value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], null, $report['style'][$el], 'bild');
            $pdf->print_element($printedCell);
        } elseif ($valuetype == 'PDF') {
            $pdf->setSourceFile($value);
            $tplidx = $pdf->ImportPage(1);
            $pdf->useTemplate($tplidx, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el]);
        } elseif ($valuetype == 'HTML') {
            $printedCell = $pdf->printcell($value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'html');
            $pdf->print_element($printedCell);
        } elseif ($valuetype == 'SCTCNR') { # TODO ??
            $printedCell = $pdf->printcell($value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'html');
            $pdf->print_element($printedCell);
        } else {
            $printedCell = $pdf->printcell($value, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'dbdat');
            $pdf->print_element($printedCell);
        }
    }


    /*------- Chart ---------*/
    private function print_chart(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $gsr;
        global $filter;
        global $verkn;
        global $extension;

        if ($this->hide_element($pdf, $report['style'][$el][29])) {
            return true;
        }

        $width = $report['width'][$el];
        $height = $report['height'][$el];
        $diag_id = $report['pic_res'][$el];
        #if($report['style'][$el][21]){$bgcolor = $report['style'][$el][21];}
        if ($report['parameter'][$el]) {
            try {
                eval($report['parameter'][$el]);
            } catch (ParseError $e) {
                lmb_log::error($e->getMessage());
            }
        }

        if ($url = LmbChart::makeChart($diag_id, $gsr, $filter, $verkn, $extension, ($width * 3), ($height * 3), $report['style'][$el])) {
            $printedCell = $pdf->printcell($url, $report['posx'][$el], $report['posy'][$el], $width, $height, $report['style'][$el], 'bild');
            $pdf->print_element($printedCell);
        }
    }

    /*------- report template ---------*/
    private function print_templ(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $greportlist;
        global $umgvar;

        $style = &$report['style'][$el];
        if ($this->hide_element($pdf, $style[29])) {
            return;
        }

        if (!defined('REPORT_TCPDF')) {
            define('REPORT_TCPDF', true);
        }

        $reportID = $report['report_id'];
        $reportTabID = $report['referenz_tab'];
        $templGtabid = $report['pic_typ'][$el];
        $content = &$report['value'][$el];
        $parameter = &$report['parameter'][$el];

        $templateRender = new ReportTemplateRender();
        $html = $templateRender->getHtml($report, intval($templGtabid), 0, intval($reportTabID), $ID, $GLOBALS['resolvedTemplateGroups'] ?? [], $GLOBALS['resolvedDynamicData'] ?? [], $content);

        // add report's css file if set
        $cssFile = $greportlist[$reportTabID]['css'][$reportID];
        if (!empty($cssFile) && file_exists(EXTENSIONSPATH . 'css/' . $cssFile)) {
            $html = '<style>' . file_get_contents(EXTENSIONSPATH . 'css/' . $cssFile) . '</style>' . $html;
        }
        $printedCell = $pdf->printcell($html, $report['posx'][$el], $report['posy'][$el], $report['width'][$el], $report['height'][$el], $report['style'][$el], 'html');
        $pdf->print_element($printedCell);
    }

    private function print_tab(LmbTcpdf &$pdf, $ID, &$report, $el)
    {
        global $action;
        global $db;
        global $umgvar;
        global $glob_el;
        global $glob_rowel;

        $report_id = $report['report_id'];
        $report_style = $report['style'][$el];
        $report_tab = $report['tab'][$el];
        $report_tab_size = $report['tab_size'][$el];

        # table size
        $tab_size = explode(';', $report_tab_size);
        $report_tab_cols = $tab_size[0];
        $report_tab_rows = $tab_size[1];

        # column size
        $colWidths = explode(';', $report['parameter'][$el]);
        array_unshift($colWidths, null);

        $sqlquery = "
        SELECT
            EL_ID,
            TYP,
            INHALT,
            TAB_EL_ROW,
            TAB_EL_COL,
            TAB_EL_COL_SIZE,
            STYLE,
            WIDTH
        FROM
            LMB_REPORTS
        WHERE
            BERICHT_ID = $report_id
            AND TAB_EL = $report_tab
        ORDER BY
            TAB_EL_ROW,
            TAB_EL_COL,
            CASE WHEN TYP = 'tabcell' THEN 1 ELSE 2 END,
            EL_ID";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);

        # get table cell information
        $cells = array();
        while (lmbdb_fetch_row($rs)) {
            $row = lmbdb_result($rs, 'TAB_EL_ROW');
            $col = lmbdb_result($rs, 'TAB_EL_COL');
            $typ = lmbdb_result($rs, 'TYP');

            if ($typ == 'tabcell') {
                $cells[$row][$col] = array(
                    'STYLE' => explode(';', lmbdb_result($rs, 'STYLE')),
                    'CHILDREN' => array()
                );
                continue;
            }

            $cells[$row][$col]['CHILDREN'][] = array(
                'INHALT' => lmbdb_result($rs, 'INHALT'),
                'TYP' => lmbdb_result($rs, 'TYP'),
                'EL_ID' => lmbdb_result($rs, 'EL_ID'),
                'STYLE' => explode(';', lmbdb_result($rs, 'STYLE')),
                'WIDTH' => lmbdb_result($rs, 'WIDTH')
            );
        }

        $tableWidth = array_sum($colWidths);

        # style
        $attrs = array();
        $attrs[] = $this->lmbStyleToTcpdfHtmlAttr($report_style, false);

        # cell padding - tcpdf only supports padding for whole table
        if ($report_style[22]) {
            $report_style[22] = str_replace('px', '', $report_style[22]);
            $attrs[] = 'cellpadding="' . $report_style[22] . '"';
        }

        // style from table
        if ($report_style[44]) {
            # border inheritance
            for ($i = 13; $i <= 20; $i++) {
                unset ($report['style'][$el][$i]);
            }
        }

        $htmlArr = array();


        $htmlArr[] = '<table width="' . $tableWidth . '" ' . implode(' ', $attrs) . '><thead>';

        # all rows of the table, differentiation between:
        #  - editor-row ($row)
        #  - pdf-row ($dynRow) (might be higher than $row because of relation datasets)
        for ($row = 1, $dynRow = 1; $row <= $report_tab_rows; $row++, $dynRow++) {
            # get data for whole editor-row (possibly multiple pdf-rows)
            $dataArray = get_dataArray($ID, $report_id, $report_tab, $report, $report_tab_cols, $row, $dynRow);
            if ($dataArray['count']) {
                $datasetCount = $dataArray['count']; # number of datasets in this row
                $dynRow = $dataArray['rowid'];
            } else {
                $datasetCount = 1;
            }

            # all columns of the current editor-row
            $cell_value = array();
            $separator = array();
            $dataElementIndex = 0;
            for ($col = 1; $col <= $report_tab_cols; $col++) {
                $currentCell = &$cells[$row][$col];

                # no elements in table cell?
                if (!$currentCell['CHILDREN']) {
                    $cell_value[$col][1][] = ' ';
                    continue;
                }

                if ($sep = $currentCell['STYLE'][40]) {
                    $separator[$row][$col] = $sep;
                }

                # all elements of the current cell
                foreach ($currentCell['CHILDREN'] as $elementIndex => &$cellElement) {
                    $type = $cellElement['TYP'];
                    $style = $this->lmbStyleInheritFromParent($currentCell['STYLE'], $cellElement['STYLE']);

                    # database field or external data source
                    if ($type == 'dbdat' or ($type == 'formel' and $style[33] == 'true')) {
                        $value = $dataArray[$dataElementIndex++];
                        if (!$value) {
                            continue;
                        }

                        # TODO list mode?
                        $value = array_map(function ($value) {
                            $valueParts = explode('#*#', $value, 2);
                            if (lmb_count($valueParts) == 2) {
                                $valuetype = $valueParts[0];
                                $value = $valueParts[1];
                                if ($valuetype == 'HTML') {
                                    return $value;
                                } else if ($valuetype == 'IMAGE') {
                                    return '<img src="' . $value . '">';
                                }
                            } else {
                                $value = $valueParts[0];
                            }
                            return nl2br(htmlentities($value));
                        }, $value);

                        # TODO doesnt work in tcpdf
                        if ($this->hide_element($pdf, $style[29])) {
                            $cell_value[$col][$elementIndex + 1] = null;
                        } else {
                            $cell_value[$col][$elementIndex + 1] = $value;
                        }

                        $GLOBALS['glob_el'][$cellElement['EL_ID']] = $value;
                    }
                }

                # all elements of the current cell/column
                foreach ($currentCell['CHILDREN'] as $elementIndex => &$cellElement) {
                    $value = trim($cellElement['INHALT']);
                    $type = $cellElement['TYP'];
                    $style = $cellElement['STYLE'];

                    if ($type == 'datum') {
                        $cell_value[$col][$elementIndex + 1] = array_fill(0, $datasetCount, local_date(1));
                    } else if ($type == 'formel' and $style[33] != 'true' /* not external data source */) {
                        $value = trim($value);
                        if ($value) {
                            for ($i = 0; $i < $datasetCount; $i++) {
                                $ROWNR = $dynRow - $datasetCount + $i + 1;
                                unset($arg_result);
                                try {
                                    $rval = eval($value);
                                    if ($rval and !isset($arg_result)) {
                                        $arg_result = $rval;
                                    }
                                } catch (ParseError $e) {
                                    lmb_log::error($e->getMessage());
                                    $arg_result = '';
                                }
                                $GLOBALS['glob_el'][$cellElement['EL_ID']][$ROWNR] = $arg_result;
                                $cell_value[$col][$elementIndex + 1][] = nl2br(htmlentities($arg_result));
                            }
                        }
                    } else if ($type != 'dbdat' and !($type == 'formel' and $style[33] == 'true')) {
                        if (!$style[40]) {
                            $value = nl2br(htmlentities($value));
                        }
                        $cell_value[$col][$elementIndex + 1] = array_fill(0, $datasetCount, $value);
                    }
                }
                $cell_value['count'][$col] = lmb_count($currentCell['CHILDREN']);
            }

            # extension for table array
            # TODO wiki?
            if (isset($call_extFct) and function_exists($call_extFct)) {
                $cell_value = $call_extFct($cell_value, $datasetCount);
                $call_extFct = null;
            }

            # dataset rows (editor-rows -> pdf-rows)
            for ($datasetIndex = 0; $datasetIndex < $datasetCount; $datasetIndex++) {
                $HIDEROW = 0;

                $rowHtmlArr = array();
                $rowHtmlArr[] = '<tr nobr="true">';
                # columns
                for ($col = 1; $col <= $report_tab_cols; $col++) {
                    $cell = &$cells[$row][$col];
                    $cellStyle = $this->lmbStyleInheritFromParent($report_style, $cell['STYLE']);

                    # elements in cell
                    $elementsHtmlArr = array();
                    for ($elementIndex = 1; $elementIndex <= $cell_value['count'][$col]; $elementIndex++) {
                        $elementStyle = $this->lmbStyleInheritFromParent(array_replace($report_style, $cellStyle), $cell['CHILDREN'][$elementIndex - 1]['STYLE']);
                        $attributeStr = '';
                        if ($elemWidth = $cell['CHILDREN'][$elementIndex - 1]['WIDTH']) {
                            $attributeStr .= 'width="' . $cell['CHILDREN'][$elementIndex - 1]['WIDTH'] . '" ';
                        }
                        $attributeStr .= $this->lmbStyleToTcpdfHtmlAttr($elementStyle);

                        $cellValue = $cell_value[$col][$elementIndex][$datasetIndex];

                        if (!$cellValue) {
                            continue;
                        }


                        # font-transform
                        if ($elementStyle[8] === 'uppercase') {
                            $cellValue = lmb_strtoupper($cellValue);
                        } elseif ($elementStyle[8] === 'lowercase') {
                            $cellValue = lmb_strtolower($cellValue);
                        }

                        $elementsHtmlArr[] = "<span $attributeStr>{$cellValue}</span>";
                        if ($cell_value[$col][$elementIndex][$datasetIndex] == '#HIDEROW') {
                            $HIDEROW = 1;
                            break 2;
                        }
                        if (isset($cell_style) and $cell_style[$col][$elementIndex][$datasetIndex]) {
                            # TODO not sure if correct
                            $cell['CHILDREN'][$elementIndex - 1]['STYLE'] = lmb_array_merge($cell['CHILDREN'][$elementIndex - 1]['STYLE'], $cell_style[$col][$elementIndex][$datasetIndex]);
                        }
                    }

                    if ($elementsHtmlArr) {
                        if ($cellSeparator = $separator[$row][$col] and lmb_substr($cellSeparator, 0, 1) != '#') {
                            $cellSeparator = str_replace("\\\\t", "\t", str_replace("\\\\n", "\n", $cellSeparator));
                        } else {
                            $cellSeparator = ' ';
                        }
                        $cellSeparator = '<span style="font-family:' . $report['default_font'] . ';font-size:' . $report['default_font_size'] . ';color:#000000;">' . $cellSeparator . '</span>';

                        $elementsHtml = trim(implode($cellSeparator, $elementsHtmlArr));
                    } else {
                        $elementsHtml = '';
                    }

                    # style
                    $attributeStr = $this->lmbStyleToTcpdfHtmlAttr($cellStyle);

                    $tdWidth = $colWidths[$col];
                    if ($colspan = $cellStyle[37]) {
                        $attributeStr .= " colspan=\"$colspan\" ";
                        for ($i = 1; $i < $colspan; $i++) {
                            $tdWidth += $colWidths[$col + $i]; # add widths of following tds to this one
                        }
                        $col += $colspan - 1;
                    }

                    if ($row == 1) { # header
                        $rowHtmlArr[] = "<th width=\"{$tdWidth}\" $attributeStr>$elementsHtml</th>";
                    } else { # data
                        $rowHtmlArr[] = "<td width=\"{$tdWidth}\" $attributeStr>$elementsHtml</td>";
                    }
                }

                $rowHtmlArr[] = '</tr>';

                if (!$HIDEROW) {
                    $htmlArr = array_merge($htmlArr, $rowHtmlArr);
                }

                if ($row == 1) {
                    $htmlArr[] = '</thead><tbody>';
                }
            }
        }
        $htmlArr[] = '</tbody></table>';

        $printedCell = $pdf->printcell(implode('', $htmlArr), $report['posx'][$el], $report['posy'][$el], ($tableWidth + 2 * parse_db_int($report_style[22])) /* compensation for missing padding in report editor */, null, $report['style'][$el], 'html');
        $pdf->print_element($printedCell);
    }

    private function lmbStyleInheritFromParent($parentStyle, $cellStyle)
    {
        static $inheritableProperties = array(
            0, 1, 2, 3, 4, 5, 6, /* font */
            8, 9, /* text-transform, color */
            11, 12 /* line-height, text-align */
        );

        $rval = array();
        for ($i = 0; $i < max(lmb_count($parentStyle), lmb_count($cellStyle)); $i++) {
            # use cell value if not inheritable or parent has different value
            if (!in_array($i, $inheritableProperties) or $cellStyle[$i] !== $parentStyle[$i]) {
                $rval[$i] = $cellStyle[$i];
            }
        }

        # border inheritance from table to td
        if ($parentStyle[44]) {
            # border
            if (!$rval[14] or $rval[14] == 'none' or !$rval[16]) {
                $rval[13] = $parentStyle[13];
                $rval[14] = $parentStyle[14];
                $rval[15] = $parentStyle[15];
                $rval[16] = $parentStyle[16];
                $rval[17] = $parentStyle[17];
                $rval[18] = $parentStyle[18];
                $rval[19] = $parentStyle[19];
                $rval[20] = $parentStyle[20];
            }
        }
        return $rval;
    }

    private function lmbStyleToTcpdfHtmlAttr($cellStyle, $border = true)
    {
        $attributes = array();
        $styleArr = array();

        # ---- font ----
        if ($cellStyle[0]) {
            $styleArr[] = "font-family:{$cellStyle[0]};";
        }
        if ($cellStyle[1] and $cellStyle[1] != 'normal') {
            $styleArr[] = "font-style:{$cellStyle[1]};";
        }
        # TODO 2: font-variant
        if ($cellStyle[3]) {
            $unit = (intval($cellStyle[3]) === $cellStyle[3]) ? 'pt' : '';
            $styleArr[] = "font-size:{$cellStyle[3]}{$unit};";
        }
        if ($cellStyle[4] and $cellStyle[4] != 'normal') {
            $styleArr[] = "font-weight:{$cellStyle[4]};";
        }
        # TODO 5: word-spacing
        if ($cellStyle[6]) {
            $styleArr[] = "letter-spacing:{$cellStyle[6]};";
        }
        if ($cellStyle[7] and $cellStyle[7] != 'none') {
            $styleArr[] = "text-decoration:{$cellStyle[7]};";
        }
        # TODO 8 text-transform
        if ($cellStyle[9]) {
            if (lmb_substr($cellStyle[9], 0, 1) !== '#') {
                $cellStyle[9] = '#' . $cellStyle[9];
            }
            $styleArr[] = "color:{$cellStyle[9]};";
        }
        # TODO 10: text-shadow?
        if ($cellStyle[11]) {
            $styleArr[] = "line-height:{$cellStyle[11]};";
        }
        if ($cellStyle[12] and $cellStyle[12] != 'left') {
            $styleArr[] = "text-align:{$cellStyle[12]};";
        }
        if ($cellStyle[22]) {
            $styleArr[] = "padding:{$cellStyle[22]};";
        }

        if ($border and $cellStyle[14] and $cellStyle[14] !== 'none' and $cellStyle[15] and $cellStyle[16]) {
            $borderStr = "{$cellStyle[16]} {$cellStyle[14]} #{$cellStyle[15]};"; // e.g. 1px solid #000000

            if ($cellStyle[17] != 'none'
                && $cellStyle[17] === $cellStyle[18]
                && $cellStyle[17] === $cellStyle[19]
                && $cellStyle[17] === $cellStyle[20]) { # border on all sides
                $styleArr[] = 'border:' . $borderStr;
            } else { # only border on some sides
                if (isset($cellStyle[17]) and $cellStyle[17] != 'none') {
                    $styleArr[] = 'border-left:' . $borderStr;
                }
                if (isset($cellStyle[18]) and $cellStyle[18] != 'none') {
                    $styleArr[] = 'border-right:' . $borderStr;
                }
                if (isset($cellStyle[19]) and $cellStyle[19] != 'none') {
                    $styleArr[] = 'border-top:' . $borderStr;
                }
                if (isset($cellStyle[20]) and $cellStyle[20] != 'none') {
                    $styleArr[] = 'border-bottom:' . $borderStr;
                }
            }
        }
        if ($cellStyle[21]) {
            if (lmb_substr($cellStyle[21], 0, 1) !== '#') {
                $cellStyle[21] = '#' . $cellStyle[21];
            }
            $styleArr[] = "background-color:{$cellStyle[21]};";
        }

//                        22 => 'cellpadding',
//                        23 => 'vertical-align',
//                        24 => 'opacity'

        if ($styleArr) {
            $attributes[] = 'style="' . implode('', $styleArr) . '"';
        }

        return implode(' ', $attributes);
    }
#endregion

    /**
     * Returns true if the element should be hidden on the current page
     * @param LmbTcpdf $pdf
     * @param number $hide
     * @return bool yes if hidden, no if shown
     */
    private function hide_element(LmbTcpdf &$pdf, $hide)
    {
        $pageNo = $pdf->PageNo();
        $odd = ($pageNo % 2 != 0);

        if ($hide == 1 or $hide == 'true' /* hide always */
            or ($hide == 2 and $pageNo == 1) /* hide on first page */
            or ($hide == 3 and $pageNo > 1) /* hide on not-first page */
            or ($hide == 4 and !$odd) /* hide on even pages */
            or ($hide == 5 and $odd) /* hide on odd pages */
        ) {
            return true;
        } else {
            return false;
        }
    }


    private function SetPageBreak(LmbTcpdf &$pdf, $footerpos, &$report)
    {
        if ($footerpos) { # Fußumbruch bei vorhandenen Fußelementen
            $pdf->SetAutoPageBreak(true, $footerpos);
        } elseif ($report['page_style'][4]) { # Fußumbruch bei vorhandenem Seitenrahmen
            $footerpos = $report['page_style'][3];
            $pdf->SetAutoPageBreak(true, $footerpos);
        } else { # kein Fußumbruch
            $pdf->SetAutoPageBreak(false, 0);
        }
    }

# --- PDF füllen ---------------------------------
    private function fill_pdf(LmbTcpdf &$pdf, &$report, $ID, $subform = 0)
    {
        $rep = element_list($ID, $report, '');
        $this->list_elements_pdf($pdf, $ID, $rep, $subform);
    }


# Unterbericht integrieren
    private function merge_subReport(LmbTcpdf &$pdf, $ID, $report_id)
    {
        $report_ = get_report($report_id, 0);
        $this->fill_pdf($pdf, $report_, $ID, $pdf->GetY());
    }

}
