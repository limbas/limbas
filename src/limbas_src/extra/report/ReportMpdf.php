<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report;

use Limbas\extra\template\report\ReportTemplateRender;
use lmb_log;
use Mpdf\MpdfException;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

class ReportMpdf extends Report
{

    public function __construct()
    {
        $this->type = 'mpdf';
        parent::__construct();
    }

    /**
     * PDF erzeugen
     *
     * @param array $report
     * @param int $ID
     * @return LmbMpdf
     * @throws MpdfException
     */
    protected function create_pdf(array &$report, int $ID): LmbMpdf
    {
        global $lmbOnCreatePdf;
        global $umgvar;

        // page style
        if (is_numeric($report['page_style'][0]) and is_numeric($report['page_style'][1])) {
            $width = $report['page_style'][0];
            $height = $report['page_style'][1];
            $format = [$width, $height];
        } elseif (is_string($report['page_style'][0])) {
            $format = $report['page_style'][0];
        }

        $isunicode = null;
        if ($umgvar['charset'] == 'UTF-8') {
            $isunicode = 'utf-8';
        }
        $fontdir = array($umgvar['pfad'] . '/inc/fonts/');
        $fonts = set_fonts(null, $report);

        $report['default_font'] = 'dejavusans';
        $report['default_font_size'] = 9;

        if (key($fonts)) {
            $report['default_font'] = key($fonts);
        }

        if (!$report['dpi']) {
            $report['dpi'] = 96;
        }

        $mpdf_init = array(
            'dpi' => $report['dpi'],
            'img_dpi' => $report['dpi'],
            'mode' => $isunicode,
            'format' => $format,
            'orientation' => $report['orientation'],
            'default_font' => $report['default_font'],
            'default_font_size' => $report['default_font_size'],
            'margin_top' => $report['page_style'][2],
            'margin_bottom' => $report['page_style'][3],
            'margin_right' => $report['page_style'][4],
            'margin_left' => $report['page_style'][5],
            'margin_header' => 0,
            'margin_footer' => 0,
            'fontDir' => $fontdir,
            'tempDir' => TEMPPATH
        );

        if ($fonts) {
            $mpdf_init['fontdata'] = $fonts;
        }

        if ($report['css'] && file_exists(EXTENSIONSPATH . 'css/' . $report['css'])) {
            $mpdf_init['defaultCssFile'] = EXTENSIONSPATH . 'css/' . $report['css'];
        }


        $pdf = new LmbMpdf($mpdf_init);


        if (!$isunicode) {
            $pdf->charset_in = $umgvar['charset'];
        }


        # Fußumbruch bei vorhandenen Fußelementen
        //SetPageBreak($pdf, $report['footerpos'], $report);

        $pdf->rp = $rp;
        $pdf->report = &$report;
        $pdf->report_id = $report['report_id'];
        $pdf->ID = $ID;

        // callback to modify pdf or elements
        if (is_callable($lmbOnCreatePdf)) {
            $lmbOnCreatePdf($pdf);
        }

        $pdf->SetSubject($report['name'] . ' ' . date('Y.m.d'));
        $pdf->SetTitle($report['name'] . ' ' . date('Y.m.d'));
        $pdf->SetCompression(true);
        $pdf->SetCreator('LIMBAS');

        $pdf->autoPageBreak = true;
        $pdf->shrink_tables_to_fit = 1;
        $pdf->setAutoBottomMargin = 'stretch';
        $pdf->setAutoTopMargin = 'stretch';

        //enable watermark
        $pdf->watermark_font = $report['default_font'];
        $pdf->showWatermarkText = true;

        return $pdf;
    }


    /**
     * @param LmbMpdf|LmbTcpdf $pdf
     * @param $report
     * @param $ID
     * @return void
     * @throws MpdfException
     */
    protected function renderContent(LmbMpdf|LmbTcpdf &$pdf, array $report, int $ID): void
    {
        $templateRender = new ReportTemplateRender();
        $html = $templateRender->getHtml($report, intval($report['root_template']), intval($report['root_template_id']), intval($report['referenz_tab'] ?? 0), $ID, $GLOBALS['resolvedTemplateGroups'] ?? [], $GLOBALS['resolvedDynamicData'] ?? []);
        $pdf->WriteHTML($html);
    }


}
