<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\report;


use Limbas\admin\setup\fonts\Font;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

class LmbTcpdf extends Fpdi {

    var $rp;
    var $report;
    var $report_id;
    var $ID;
    var $headerMarginSet = false;
    var $oldCellPadding = null;

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false) {
        # font subsetting has to be set to false before the constructor is called,
        #   as the constructor already adds the default font!
        $this->font_subsetting = false;
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
    }


    # -------------- concat pdf ---------------------
    function concat(&$files) {
        foreach ($files as $file) {
            $pagecount = $this->setSourceFile($file);
            for ($i = 1; $i <= $pagecount; $i++) {
                $tplidx = $this->ImportPage($i);
                $this->AddPage();
                $this->useTemplate($tplidx);
            }
        }
    }

    # RGB Color ----------------------------
    function make_color($val) {
        $val = trim($val,'#');
        $col[0] = hexdec(lmb_substr($val, 0, 2));
        $col[1] = hexdec(lmb_substr($val, 2, 2));
        $col[2] = hexdec(lmb_substr($val, 4, 2));
        return $col;
    }

    # Font Style ----------------------------
    function setFontStyle($style) {
        $font = substr($style[4], 0, 1) . lmb_substr($style[1], 0, 1) . lmb_substr($style[7], 0, 1);
        $font = str_replace('N', '', lmb_strtoupper($font));
        # font ----------------------------
        $this->SetFont($style[0], $font, $style[3]);
    }

    # Font Align ----------------------------
    function GetFontAlign($align) {
        if ($align) {
            return lmb_strtoupper(lmb_substr($align, 0, 1));
        } else {
            return 'J';
        }
    }

    # Font Valign ----------------------------
    function GetFontValign($valign) {
        if ($valign) {
            return lmb_strtoupper(lmb_substr($valign, 0, 1));
        } else {
            return 'T';
        }
    }

    # Border Type ----------------------------
    function GetBorderType($style) {
        $border = '';
        if ($style[14] AND $style[14] != 'none' AND $style[16]) {
            if ($style[17] != 'none') {
                $border .= 'L';
            }
            if ($style[18] != 'none') {
                $border .= 'R';
            }
            if ($style[19] != 'none') {
                $border .= 'T';
            }
            if ($style[20] != 'none') {
                $border .= 'B';
            }
            if (!$border) {
                $border = 1;
            }
        } else {
            return false;
        }

        return $border;
    }

    # Kopf Elemente
    function Header() {
        $this->Background();
        $report = new ReportTcpdf();
        $report->list_elements_pdf($this, $this->ID, $this->rp['header']);

        # set page margins for current page according to header height (if header elements exist)
//        if (!$this->headerMarginSet and $this->rp['header']['id']) {
//            $this->headerMarginSet = true;
        $y = $this->getY();
        if ($y < $this->report['page_style'][2]) {
            $y = $this->report['page_style'][2];
        }
        $this->setTopMargin($y);
//        }

    }

    # Fuß Elemente
    function Footer() {
        $report = new ReportTcpdf();
        $report->list_elements_pdf($this, $this->ID, $this->rp['footer']);
    }

    # Hintergrund
    function Background() {
        $sy = $this->GetY();
        $sx = $this->GetX();
        $report = new ReportTcpdf();
        $report->list_elements_pdf($this, $this->ID, $this->rp['background']);
        # position ----------------------------
        $this->SetY($sy);
        $this->SetX($sx);
    }

    # printcell ----------------------------
    function printcell($val, $x, $y, $w, $h, $style, $typ) {
        global $tablay;

        $par = array();

        if ($typ == 'tab') {
            $tablay['style'] = $style;
            $tablay['height'] = $h;
        }

        # Linien/Kreise ----------------------------
        if ($typ == 'line' OR $typ == 'ellipse') {
            $style[15] = $style[9];
            $style[16] = $style[3];
        }

        # Linie spiegeln ----------------------------
        if ($style[25] == 'true' AND $typ == 'line') {
            $y = ($y + $h);
            $h = ($y - $h + 1);
            $w = ($w + $x + 1);
        } elseif ($style[25] != 'true' AND $typ == 'line') {
            $w = ($w + $x - 1);
            $h = ($h + $y - 1);
        }

        # padding
        if ($style[22]) {
            $this->oldCellPadding = $this->getCellPaddings();
            $this->setCellPaddings($style[22], $style[22], $style[22], $style[22]);
        } else if ($this->oldCellPadding) {
            $this->setCellPaddings($this->oldCellPadding['L'], $this->oldCellPadding['T'], $this->oldCellPadding['R'], $this->oldCellPadding['B']);
        }

        # opacity ----------------------------
        if ($style[24]) {
            $this->setAlpha($style[24]);
        } else {
            $this->setAlpha(1);
        }

        # font ----------------------------
        $this->setFontStyle($style);

        # font-color ----------------------------
        if ($style[9]) {
            $par['c'] = $this->make_color($style[9]);
            $this->SetTextColor($par['c'][0], $par['c'][1], $par['c'][2]);
        } else {
            $this->SetTextColor(0, 0, 0);
        }

        # charspacing ----------------------------
        if (is_numeric($style[6]) AND $style[6] > 0) {
            $this->setFontSpacing($style[6]);
        } else {
            $this->setFontSpacing();
        }

        # fill-color ----------------------------
        if ($style[21] AND $style[21] != 'transparent') {
            $par['c'] = $this->make_color($style[21]);
            $this->SetFillColor($par['c'][0], $par['c'][1], $par['c'][2]);
            $par['f'] = 1;
            $par['rf'] = 'F';
        } else {
            $this->SetFillColor();
            $par['f'] = 0;
            $par['rf'] = '';
        }

        # border-color ----------------------------
        if ($style[15]) {
            $par['c'] = $this->make_color($style[15]);
            $this->SetDrawColor($par['c'][0], $par['c'][1], $par['c'][2]);
        }

        # border-width ----------------------------
        if ($style[16]) {
            $this->SetLineWidth($style[16]);
        }

        # border ----------------------------
        $par['b'] = $this->GetBorderType($style);
        if (!$par['b']) {
            $par['b'] = 0;
            $par['rd'] = '';
        } else {
            $par['rd'] = 'D';
        }

        # font-transform ----------------------------
        if ($style[8] == 'uppercase') {
            $val = lmb_strtoupper($val);
        } elseif ($style[8] == 'lowercase') {
            $val = lmb_strtolower($val);
        }

        # position ----------------------------
        if (isset($y)) {
            $this->SetY($this->report['page_style'][2] + intval($y));
        }
        if (isset($x)) {
            $this->SetX($this->report['page_style'][4] + intval($x));
        }

        # line height (relative to font size) ----------------------------
        if ($style[11]) {
            $par['h'] = intval($style[11]) / $style[3];
        } else {
            $par['h'] = 1.25; /* suggested by tcpdf */
        }
        $this->setCellHeightRatio($par['h']);

        # align ----------------------------
        if ($style[12]) {
            $par['a'] = $this->GetFontAlign($style[12]);
        }

        # valign
        $valign = null;
        if ($style[23]) {
            if (strcasecmp('bottom', $style[23]) === 0) {
                $valign = 'B';
            } elseif (strcasecmp('middle', $style[23]) === 0) {
                $valign = 'M';
            }
        }

        return array($x, $y, $w, $h, $par['h'], $val, $par['b'], $par['a'], $par['f'], $typ, $style, $par['rf'], $par['rd'], $valign);
    }

    # Print-Element ----------------------------
    function print_element(&$par) {
        $x = $par[0];
        $y = $par[1];
        $w = $par[2];
        $h = $par[3];
//        $par['h'] = $par[4]; # line height
        $val = $par[5];
        $border = $par[6];
        $align = $par[7];
        $fill = $par[8];
        $typ = $par[9];
        $style = $par[10];
//        $par[rf] = $par[11]; # fill?
//        $par[rd] = $par[12]; # ??
        $valign = $par[13];

        # rotate (has to be done here because it needs to be reset later) ----------------------------
        if ($style[43]) {
            $this->StartTransform();
            $this->Rotate($style[43], $this->GetX(), $this->GetY());
        }

        $absposy = $this->GetY();

        # TODO: $par[4] (line height) or $h (element height)?

        if ($typ == 'bild') {
            # Bildgröße in Rahmen einpassen
            #$cmd = "identify -format '%w,%h' {$val}[0]";
            #$picinf = explode(',', `$cmd`);

            # proportional -> dynamic width
            if ($style[26] && $style[26] !== 'false') {
                $w = '';
            }

            # picture ----------------------------
            $pityp = pathinfo($val, PATHINFO_EXTENSION);
            $this->Image($val, $this->GetX(), $this->GetY(), $w, $h, $pityp, '', $align, 2, 300, '', false, false, $border);
        } elseif ($typ == 'html') {
            # html ----------------------------
//            $this->MultiCell($w, $par[4], $val, $border, $align, $fill, 1, null, null, null, null, true);
//            $this->writeHTML($val, true, $fill, false, false, $align);
            $this->writeHTMLCell($w, $par[4], $this->GetX(), $this->GetY(), $val, $border, 1, $fill, true, $align);

        } elseif ($typ == 'rect') {
            # Rechteck ----------------------------
            $style19 = intval($style[19]);
            if ($style[36]) {
                $posy = $absposy;
            } else {
                $posy = $this->GetY() - $style19 / 2;
            }
            $this->Rect($this->GetX() - $style19 / 2, $posy, $w + $style19, $h + $style19, $par[11] . $par[12]);
        } elseif ($typ == 'line') {
            # Linie ----------------------------
            $this->Line($x, $y, $w, $h);
        } elseif ($typ == 'ellipse') {
            $this->Ellipse(($x + ($w / 2)), ($y + ($h / 2)), ($w / 2), ($h / 2), 0, 0, 360, $par[11] . 'D');
        } else {
            # multicell ----------------------------
            if ($valign) { /* valign requires a maximum height */
                $this->MultiCell($w, $h, $val, $border, $align, $fill, 1, $this->GetX(), $this->GetY(), true, 0, false, true, $h, $valign);
            } else {
                $this->MultiCell($w, $par[4], $val, $border, $align, $fill,1 , $this->GetX(), $this->GetY());
            }
        }

        # end rotate ----------------------------
        if ($style[43]) {
            $this->StopTransform();
        }
    }

    /*------- Add-Font ---------*/
    function add_font($font) {
        $sysfont = get_fonts();       
        
        if ($sysfont['family'][$font][0]) {
            # Font eintragen
            foreach ($sysfont['family'][$font] as $key => $val) {
                if (!isset($this->fonts[$font . $sysfont['style'][$font][$key]])) {
                    $this->AddFont($font, $sysfont['style'][$font][$key], $sysfont['name'][$font][$key] . '.ttf');
                }
            }
        }
    }

    protected function getFontsList() {
        global $umgvar;

        $isunicode = 'TrueType';
        if($umgvar['charset'] == 'UTF-8'){
            $isunicode = 'TrueTypeUnicode';
        }

        if (($fontsdir = opendir($umgvar['pfad'].'/inc/fonts/')) !== false) {
            while (($file = readdir($fontsdir)) !== false) {
                if (substr($file, -4) == '.ttf') {
                    TCPDF_FONTS::addTTFfont($umgvar['pfad'].'/inc/fonts/' . $file,$isunicode,$umgvar['charset']);
                }
            }
            closedir($fontsdir);
        }

        parent::getFontsList();
    }

}
