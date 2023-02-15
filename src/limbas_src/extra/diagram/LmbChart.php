<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


require_once(COREPATH . 'gtab/gtab.lib');

class LmbChart
{

    private $fontsize = null;
    private $isLine = null;
    private $isBar = null;
    private $isPie = null;
    private $fontlocation = null;

    /**
     * Creates a chart instance and returns the location of saved png-image
     *
     * @param $diag_id
     * @param $gsr
     * @param $filter
     * @param $verkn
     * @param $extension
     * @param $width
     * @param $height
     * @param $style
     * @param $chartjs
     * @return false|string|null
     */
    public static function makeChart($diag_id, $gsr = null, $filter = null, $verkn = null, $extension = null, $width = null, $height = null, $style = array(), $chartjs = false): bool|string|null
    {
        return (new self)->createChart($diag_id, $gsr, $filter, $verkn, $extension, $width, $height, $style, $chartjs);
    }


    /**
     * Creates png-image of diagram
     * Returns: location of saved png-image
     *
     * @param $diag_id
     * @param $gsr
     * @param $filter
     * @param $verkn
     * @param $extension
     * @param $width
     * @param $height
     * @param $style
     * @param $chartjs
     * @return false|string|void
     */
    private function createChart($diag_id, $gsr = null, $filter = null, $verkn = null, $extension = null, $width = null, $height = null, $style = array(), $chartjs = false)
    {
        global $db;
        global $gfield;
        global $gdiaglist;
        global $session;
        global $umgvar;

        // parse style
        $bgcolor = $style[21] ? $style[21] : 'ffffff';
        $this->fontsize = $style[3] ? $style[3] : null; // use fontsize from database
        $this->fontlocation = $this->getFontLocation($style);
        $fontColorArr = $style[9] ? $this->getColorAsArray($style[9]) : array();

        $gtabid = $gdiaglist['gtabid'][$diag_id];
        if (!$gdiaglist[$gtabid]['id'][$diag_id]) {
            return false;
        }

        $settingNames = array(
            'TAB_ID',
            'DIAG_TYPE',
            'DIAG_WIDTH',
            'DIAG_HEIGHT',
            'TEXT_X',
            'TEXT_Y',
            'FONT_SIZE',
            'PADDING_LEFT',
            'PADDING_TOP',
            'PADDING_RIGHT',
            'PADDING_BOTTOM',
            'LEGEND_X',
            'LEGEND_Y',
            'LEGEND_MODE',
            'PIE_WRITE_VALUES',
            'PIE_RADIUS',
            'TRANSPOSED'
        );

        /* Get customization-settings from database */
        $sqlquery = "SELECT " . implode(',', $settingNames) . " FROM LMB_CHART_LIST WHERE ID=" . parse_db_int($diag_id);
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (lmbdb_fetch_row($rs)) {

            $dbSettings = array();
            foreach ($settingNames as $name) {
                $dbSettings[$name] = lmbdb_result($rs, $name);
            }

            // get width/height from parameters instead of database
            if (!$width) {
                $width = lmbdb_result($rs, "DIAG_WIDTH");
            }
            if (!$height) {
                $height = lmbdb_result($rs, "DIAG_HEIGHT");
            }
            $dbSettings['DIAG_WIDTH'] = $width;
            $dbSettings['DIAG_HEIGHT'] = $height;

            // fixed settings
            $text_x = lmbdb_result($rs, "TEXT_X");
            $text_y = lmbdb_result($rs, "TEXT_Y");
            if (!$this->fontsize) {
                $this->fontsize = lmbdb_result($rs, "FONT_SIZE");
            }
            $legend_mode = lmbdb_result($rs, "LEGEND_MODE");
            $pie_write_values = lmbdb_result($rs, "PIE_WRITE_VALUES");
            $diag_tab_id = lmbdb_result($rs, "TAB_ID");
            $diag_type = lmbdb_result($rs, "DIAG_TYPE");
            $diagname = $gdiaglist[$gtabid]["name"][$diag_id];

            /* Define transposed-mode */
            $isTransposed = lmbdb_result($rs, "TRANSPOSED");

            /* Define chart types */
            $this->isLine = ($diag_type == "Line-Graph");
            $this->isBar = ($diag_type == "Bar-Chart");
            $this->isPie = ($diag_type == "Pie-Chart");

        }

        /* Define axis types */
        define("DATA_AXIS", "1");
        define("CAPTION_AXIS", "2");

        /* Get fields from database */
        $sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS,COLOR FROM LMB_CHARTS WHERE CHART_ID = $diag_id";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $fields = array();
        $num_data_axes = 0;
        $num_caption_axes = 0;
        $bzm = 0;
        while (lmbdb_fetch_row($rs)) {
            $fields[$bzm]['field_id'] = lmbdb_result($rs, "FIELD_ID");
            $tmp_axis = lmbdb_result($rs, "AXIS");
            if ($tmp_axis == DATA_AXIS) {
                $num_data_axes++;
            } elseif ($tmp_axis == CAPTION_AXIS) {
                $num_caption_axes++;
            }
            $fields[$bzm]['axis'] = $tmp_axis;
            $fields[$bzm]['color'] = lmbdb_result($rs, "COLOR");
            $bzm++;
        }

        /* Collect data from database */
        $fieldids = array();
        for ($i = 0; $i < lmb_count($fields); $i++) {
            $fieldids[] = $fields[$i]['field_id'];
        }

        $data = get_gresult($diag_tab_id, 1, $filter, $gsr, $verkn, array($diag_tab_id => $fieldids), null, $extension);
        $data = $data[$diag_tab_id];

        /* Check if number of selected axes matches diagram type */
        $err_wrong_diag = false;
        $err_wrong_axes_count = false;
        $warning_too_much_data = false;

        if ($isTransposed) {
            if ($this->isBar || $this->isLine) {
                $err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
            } elseif ($this->isPie) {
                $err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 0);
                $warning_too_much_data = $this->isPie && $data['max_count'] > 1;
            } else {
                $err_wrong_diag = true;
            }
        } else {
            if ($this->isBar || $this->isLine) {
                $err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
            } elseif ($this->isPie) {
                $err_wrong_axes_count = ($num_data_axes != 1 || $num_caption_axes > 1);
            } else {
                $err_wrong_diag = true;
            }
        }

        if ($err_wrong_diag) {
            lmb_alert("invalid type: " . $diag_type);
            return;
        }
        if ($err_wrong_axes_count) {
            lmb_alert("invalid amount of fields/axis!");
            return;
        }
        if ($warning_too_much_data) {
            lmb_alert("too many data, only last line will be used!");
        }

        /* Chart-customization constants */
        $diagname = str_replace(lmb_utf8_decode("ä"), "ae", $diagname);
        $diagname = str_replace(lmb_utf8_decode("ö"), "oe", $diagname);
        $diagname = str_replace(lmb_utf8_decode("ü"), "ue", $diagname);
        $diagname = str_replace(lmb_utf8_decode("ß"), "ss", $diagname);
        $diagname = preg_replace("/[^[:alnum:]\.]|[ ]/", "_", $diagname);

        $saveLocation = USERPATH . $session['user_id'] . "/temp/" . $diagname . "_$diag_id.png";
        define("LINETHICKNESS", 0.5); // no linethickness means better antialiasing to pchart, but apparently does only work for tranposed graphs
        define("LABEL_ROTATION", 15);
        if (!file_exists($this->fontlocation) && !$chartjs) {
            lmb_alert('missing font ' . $this->fontlocation . '!');
            return false;
        }

        /* Include chart classes */
        require_once(LEGACYPATH . 'pChart/pDraw.class.php');
        require_once(LEGACYPATH . 'pChart/pImage.class.php');
        require_once(LEGACYPATH . 'pChart/pData.class.php');
        require_once(LEGACYPATH . 'pChart/pPie.class.php');

        /* Find id of caption-field */
        $captionid = -1;
        foreach ($fields as $field) {
            if ($field["axis"] == CAPTION_AXIS) {
                $captionid = $field["field_id"];
                break;
            }
        }

        /* Create dataset object */
        $myData = new pData();
        $myData->setAxisName(0, $text_y);

        /* Fill dataset object (TYPE NORMAL) */
        if (!$isTransposed) {
            foreach ($fieldids as $fieldid) {
                $tmp_data = array();
                for ($i = 0; $i < lmb_count($data[$fieldid]); $i++) {
                    /* Display values after caption name (PIE-Chart only) */
                    if ($this->isPie && $fieldid == $captionid && $pie_write_values != "none") {
                        // Get id of data field
                        $dataid = null;
                        foreach ($fieldids as $tmp_fieldid) {
                            if ($tmp_fieldid != $captionid) {
                                $dataid = $tmp_fieldid;
                                break;
                            }
                        }
                        /* Differ between percent and value display */
                        $sum = 1;
                        if ($pie_write_values == "percent") {
                            $sum = 0;
                            for ($u = 0; $u < lmb_count($data[$dataid]); $u++) {
                                $sum += $data[$dataid][$u];
                            }
                            $tmp_data[$i] = $data[$fieldid][$i] . " (" . number_format((float)($data[$dataid][$i] / $sum) * 100, 1) . "%)";
                        } elseif ($pie_write_values == "value") {
                            $tmp_data[$i] = $data[$fieldid][$i] . " (" . ($data[$dataid][$i]) . ")";
                        }


                    } else {
                        $tmp_data[$i] = $this->convertFloatInternational($diag_tab_id, $fieldid, $data[$fieldid][$i]);
                    }
                }
                $columnname = $gfield[$diag_tab_id]['spelling'][$fieldid];
                $myData->addPoints($tmp_data, $columnname);
                $myData->setSerieWeight($columnname, LINETHICKNESS);

                /* Color values */
                if (!$this->isPie) {
                    // Find color of field
                    $hexcolor = null;
                    for ($i = 0; $i < lmb_count($fields); $i++) {
                        if ($fields[$i]["field_id"] == $fieldid) {
                            $hexcolor = $fields[$i]["color"];
                            break;
                        }
                    }
                    $myData->setPalette($columnname, $this->getColorAsArray($hexcolor));
                }
                /* Abscissa */
                if ($fieldid == $captionid) {
                    $myData->setAbscissa($columnname);
                    $myData->setAbscissaName($text_x);
                }
            }

            /* No caption-field -> set numbers as caption */
            if ($captionid == -1) {
                $int_vals = $this->getNIntVals(lmb_count($data[$fieldids[0]]));
                $myData->addPoints($int_vals, -1);
                $myData->setAbscissa(-1);
            }

        }

        /* Fill dataset object (TYPE TRANSPOSED) */
        if ($isTransposed) {
            /* fill data */
            $tmp_data = null;
            for ($i = 0; $i < $data['max_count']; $i++) {
                $tmp_data = array();
                foreach ($fieldids as $fieldid) {
                    if ($fieldid != $captionid) {
                        $tmp_data[] = $this->convertFloatInternational($diag_tab_id, $fieldid, $data[$fieldid][$i]);
                    }
                }
                /* get row name */
                if (($this->isLine || $this->isBar) && $num_caption_axes == 1) {
                    $myData->addPoints($tmp_data, $data[$captionid][$i]);
                    $myData->setSerieWeight($data[$captionid][$i], LINETHICKNESS);
                } else {
                    $myData->addPoints($tmp_data, "Zeile " . $i); //TODO: string ersetzen
                    $myData->setSerieWeight("Zeile " . $i, LINETHICKNESS); //TODO: string ersetzen
                }
            }
            /* set row-headers as abscissa, add percent/value if selected */
            $abscissa = array();
            $i = 0;
            foreach ($fieldids as $fieldid) {
                if ($fieldid != $captionid) {
                    $text = $gfield[$diag_tab_id]['spelling'][$fieldid];
                    if ($this->isPie && $pie_write_values == "percent") {
                        $sum = 0;
                        for ($u = 0; $u < lmb_count($tmp_data); $u++) {
                            $sum += $tmp_data[$u];
                        }
                        $text .= " (" . str_replace('.', ',', number_format((float)($tmp_data[$i] / $sum) * 100, 1)) . "%)";
                    } elseif ($this->isPie && $pie_write_values == "value") {
                        $text .= " (" . ($this->convertFloatGerman($diag_tab_id, $fieldid, $tmp_data[$i])) . ")";
                    }
                    $abscissa[] = $text;
                    $i++;
                }
            }
            $myData->addPoints($abscissa, "abscissa");
            $myData->setAbscissa("abscissa");
            $myData->setAbscissaName($text_x);
        }

        /* calculate auto settings */
        $settings = $this->diagramAutoSettings($dbSettings, $myData->getData());

        if ($chartjs) {
            //return '<pre>'.print_r($myData->getData(),1).'</pre>';
            $this->isLine = ($diag_type == "Line-Graph");
            $this->isBar = ($diag_type == "Bar-Chart");
            $this->isPie = ($diag_type == "Pie-Chart");

            $chartjstype = ($this->isLine) ? 'line' : (($this->isBar) ? 'bar' : (($this->isPie) ? 'pie' : 'ext'));

            header('Content-Type: application/json');
            return json_encode($this->convertpDataToChartJS($myData->getData(), $chartjstype));
        }


        /* Init chart */
        $myPicture = new pImage($width, $height, $myData);
        $myPicture->setGraphArea($settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width - $settings['PADDING_RIGHT'], $height - $settings['PADDING_BOTTOM']);
        //$myPicture->setShadow(true);

        $fontProperties = $fontColorArr;
        $fontProperties['FontName'] = $this->fontlocation;
        $fontProperties['FontSize'] = $this->fontsize;
        $myPicture->setFontProperties($fontProperties);
        $myPicture->Antialias = TRUE;

        // draw background
        if ($bgcolor) {
            $bgColArr = $this->getColorAsArray($bgcolor);
            $bgColArr['Surrounding'] = 0;
            $myPicture->drawFilledRectangle(0, 0, $width, $height, $bgColArr);
        }

        // draw transparent black rectangle over graph area
        $myPicture->drawFilledRectangle(
            $settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width - $settings['PADDING_RIGHT'], $height - $settings['PADDING_BOTTOM'],
            array("R" => 0, "G" => 0, "B" => 0, "Surrounding" => -255, "Alpha" => 5)
        );

        // draw two black lines around the graph area
        $myPicture->drawLine($settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width - $settings['PADDING_RIGHT'], $settings['PADDING_TOP'],
            array("R" => 0, "G" => 0, "B" => 0)
        );
        $myPicture->drawLine($width - $settings['PADDING_RIGHT'], $settings['PADDING_TOP'], $width - $settings['PADDING_RIGHT'], $height - $settings['PADDING_BOTTOM'],
            array("R" => 0, "G" => 0, "B" => 0)
        );

        /* Differ between chart types */
        if ($this->isBar || $this->isLine) {
            $myPicture->drawScale(array('LabelRotation' => LABEL_ROTATION));
            if ($legend_mode != "none") {
                $legend_mode = ($legend_mode == "vertical") ? 690901 : 690902;
                $myPicture->drawLegend($settings['LEGEND_X'], $settings['LEGEND_Y'], array("Style" => LEGEND_BOX, "Mode" => $legend_mode, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 5, "BorderR" => 0, "BorderG" => 0, "BorderB" => 0));
            }
            if ($this->isBar) {
                $myPicture->drawBarChart(array("Rounded" => FALSE, "Orientation" => ORIENTATION_HORIZONTAL));
            } elseif ($this->isLine) {
                $myPicture->drawLineChart();
            }
        } elseif ($this->isPie) {
            $PieChart = new pPie($myPicture, $myData);

            /* Pie-slice colors */
            if ($this->isPie && $isTransposed) {
                for ($i = 0; $i < lmb_count($fields); $i++) {
                    $hexcolor = $fields[$i]["color"];
                    $PieChart->setSliceColor($i, $this->getColorAsArray($hexcolor));
                }
            }

            $PieChart->draw2DPie($settings['PADDING_LEFT'], $settings['PADDING_TOP'],
                array(
                    "DrawLabels" => TRUE,
                    "Border" => TRUE,
                    "Radius" => $settings['PIE_RADIUS'],
                    "LabelR" => $fontColorArr['R'],
                    "LabelG" => $fontColorArr['G'],
                    "LabelB" => $fontColorArr['B'],
                )
            );
        }

        /* Save and return image */
        $myPicture->render($saveLocation);

        if (file_exists($saveLocation)) {
            return $saveLocation;
        }
    }

    /**
     * Author: Peter
     * Settings that will be needed to calculate the new settings: diag_width, diag_height, fontsize
     * Uses the database settings specified in $src to automatically calculate settings like legend-position or padding
     *
     * @param array $src
     * @param array $chartData is the data previously put into the chart in the pChart-data-format
     * @return array indexed array of automatically calculated settings
     */
    private function diagramAutoSettings(array $src, array $chartData): array
    {

        // returned array
        $dest = array(
            'PADDING_LEFT' => $src['PADDING_LEFT'],
            'PADDING_TOP' => $src['PADDING_TOP'],
            'PADDING_RIGHT' => $src['PADDING_RIGHT'],
            'PADDING_BOTTOM' => $src['PADDING_BOTTOM'],
            'LEGEND_X' => $src['LEGEND_X'],
            'LEGEND_Y' => $src['LEGEND_Y'],
            'PIE_RADIUS' => $src['PIE_RADIUS']
        );

        // pie chart
        if ($this->isPie) {
            // diag_width = longestString + 2*radius + longestString
            // diag_height = 2*radius + 2*stringheight
            if (!$dest['PIE_RADIUS']) {
                $sts = $this->stringSize($this->getLongestAbscissaValue($chartData));
                $longestStringWidth = $sts['w'];
                $maxRadiusWidth = ($src['DIAG_WIDTH'] - 2 * $longestStringWidth) / 2 - 35;
                $sts = $this->stringSize("42");
                $maxRadiusHeight = ($src['DIAG_HEIGHT'] - 2 * $sts['h']) / 2 - 45;
                $dest['PIE_RADIUS'] = min($maxRadiusWidth, $maxRadiusHeight);
            }

            $dest['PIE_RADIUS'] = max(1, $dest['PIE_RADIUS']);

            // pie should be centered
            if (!$dest['PADDING_LEFT']) {
                $dest['PADDING_LEFT'] = $src['DIAG_WIDTH'] / 2;
            }
            if (!$dest['PADDING_RIGHT']) {
                $dest['PADDING_RIGHT'] = $src['DIAG_WIDTH'] / 2;
            }
            if (!$dest['PADDING_TOP']) {
                $dest['PADDING_TOP'] = $src['DIAG_HEIGHT'] / 2;
            }
            if (!$dest['PADDING_BOTTOM']) {
                $dest['PADDING_BOTTOM'] = $src['DIAG_HEIGHT'] / 2;
            }

        }

        // line chart / bar chart
        if ($this->isLine || $this->isBar) {
            // basepadding on all 4 sides
            $basePadding = 20;

            // legend size
            $legendBoxSize = 10;
            $legendWidth = 0;
            $legendHeight = 0;
            $legendNumRows = sizeof($chartData['Series']) - 1; // -1 for ignoring abscissa
            if ($src['LEGEND_MODE'] == 'vertical') {
                $sts = $this->stringSize($this->getLongestLegendEntry($chartData));
                $legendWidth = $legendBoxSize + $sts['w'];
                $sts = $this->stringSize("42");
                $legendHeight = ($sts['h'] + 2) * $legendNumRows; // +2 for space between lines
            } else if ($src['LEGEND_MODE'] == 'horizontal') {
                $sts = $this->stringSize($this->getLongestLegendEntry($chartData));
                $legendWidth = ($legendBoxSize + $sts['w']) * $legendNumRows;
                $sts = $this->stringSize("42");
                $legendHeight = $sts['h'];
            }

            // right side: basepadding + legendwidth + basepadding
            if (!$dest['PADDING_RIGHT']) {
                $dest['PADDING_RIGHT'] = 2 * $basePadding + $legendWidth;
            }

            // top side: basepadding + half of maximum y-scale entry
            if (!$dest['PADDING_TOP']) {
                $sts = $this->stringSize("42");
                $dest['PADDING_TOP'] = $basePadding + $sts['h'] / 2;
            }

            // bottom side: basepadding + height of rotated x-axis texts + height of x-axis description
            $sts = $this->stringSize($this->getLongestAbscissaValue($chartData), LABEL_ROTATION);
            $xAxisTextHeight = $sts['h'];
            $sts = $this->stringSize($src['TEXT_X']);
            $xAxisDescriptionHeight = $sts['h'];
            if (!$dest['PADDING_BOTTOM']) {
                $dest['PADDING_BOTTOM'] = $basePadding + $xAxisTextHeight + $xAxisDescriptionHeight;
            }

            // left side: basepadding + height of y-axis description + width of longest y-scale entry + 5 (space added into pDraw class)
            $sts = $this->stringSize($src['TEXT_Y']);
            $yAxisDescriptionHeight = $sts['h'];
            $sts = $this->stringSize($this->getLongestYAxisValue($chartData));
            $yAxisTextWidth = $sts['w'];
            if (!$dest['PADDING_LEFT']) {
                $dest['PADDING_LEFT'] = $basePadding + $yAxisDescriptionHeight + $yAxisTextWidth + 5;
            }

            // center legend (use height of diagram without x-axis texts)
            if (!$dest['LEGEND_Y']) {
                $sts = $this->stringSize("42");
                $dest['LEGEND_Y'] = $basePadding + $sts['h'] / 2 + $legendHeight / 2 - 1; //($src['DIAG_HEIGHT'] - $legendHeight - $xAxisTextHeight) / 2;
            }
            if (!$dest['LEGEND_X']) {
                $dest['LEGEND_X'] = $src['DIAG_WIDTH'] - $legendWidth - $basePadding - 1;
            }
        }

        return $dest;
    }

    /**
     * @param array $chartData
     * @return int|string
     */
    private function getLongestLegendEntry(array $chartData): int|string
    {
        $maxLength = 0;
        $maxValue = "";

        foreach ($chartData['Series'] as $seriesName => $bla) {
            // abscissa has no entry in legend
            if ($seriesName == $chartData['Abscissa']) {
                continue;
            }

            if (lmb_strlen($seriesName) > $maxLength) {
                $maxLength = lmb_strlen($seriesName);
                $maxValue = $seriesName;
            }
        }

        return $maxValue;
    }

    /**
     * @param array $chartData
     * @return mixed|string
     */
    private function getLongestYAxisValue(array $chartData): mixed
    {
        $maxLength = 0;
        $maxValue = '';

        foreach ($chartData['Series'] as $seriesName => $seriesData) {
            // abscissa has no values
            if ($seriesName == $chartData['Abscissa']) {
                continue;
            }

            if (lmb_strlen($seriesData['Max']) > $maxLength) {
                $maxLength = lmb_strlen($seriesData['Max']);
                $maxValue = $seriesData['Max'];
            }
        }

        return $maxValue;
    }

    /**
     * @param array $chartData
     * @return mixed|string
     */
    private function getLongestAbscissaValue(array $chartData): mixed
    {
        $abscissaName = $chartData['Abscissa'];
        $abscissaValues = $chartData['Series'][$abscissaName]['Data'];

        // find the longest entry
        $maxLength = 0;
        $maxValue = '';
        foreach ($abscissaValues as $value) {
            if (lmb_strlen($value) > $maxLength) {
                $maxLength = lmb_strlen($value);
                $maxValue = $value;
            }
        }

        return $maxValue;
    }

    /**
     * @param string $text
     * @param int $angle
     * @return array
     */
    private function stringSize(?string $text, int $angle = 0): array
    {
        global $umgvar;

        if (empty($text)) {
            $text = '';
        }

        $fontPath = $umgvar['path'] . '/' . $this->fontlocation;
        $bBox = imagettfbbox(floatval($this->fontsize), $angle, $fontPath, $text);

        // calculate width and height. note: always take the maximum width, depending on the angle
        // width: max of either distance[lower-right, upper-left] or distance[upper-right, lower-left]
        $width = max(abs($bBox[2] - $bBox[6]), abs($bBox[4] - $bBox[0]));

        // height: max of either distance[upper-right, lower-left] or distance[lower-right, upper-left]
        $height = max(abs($bBox[5] - $bBox[1]), abs($bBox[3] - $bBox[7]));

        return array(
            'w' => $width,
            'h' => $height
        );
    }


    /**
     * @param array $style
     * @return string
     */
    private function getFontLocation(array $style): string
    {
        global $db;

        // default font
        $default = DEPENDENTPATH.'inc/fonts/DejaVuSans.ttf';

        // if no font family is specified, return default font
        if (!$style[0]) {
            return $default;
        }

        // extract family, bold and italic from style array
        $family = $style[0];
        $b = $style[4] == 'bold' ? 'B' : '';
        $i = $style[1] == 'italic' ? 'I' : '';

        // get name of font file from lmb_fonts
        $sqlquery = "SELECT NAME FROM LMB_FONTS WHERE STYLE='$b$i' AND FAMILY='$family'";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);

        // abort if error
        if (!$rs) {
            return $default;
        }

        if (lmbdb_fetch_row($rs)) {
            $name = lmbdb_result($rs, 'NAME');
        }

        // abort if no name was found
        if (!$name) {
            return $default;
        }

        return DEPENDENTPATH."inc/fonts/$name.ttf";
    }

    /**
     * @param string $hex
     * @return array
     */
    private function getColorAsArray(string $hex): array
    {
        return array('R' => hexdec(lmb_substr($hex, 0, 2)), 'G' => hexdec(lmb_substr($hex, 2, 2)), 'B' => hexdec(lmb_substr($hex, 4, 2)), 'Alpha' => 255);
    }

    /**
     * @param int $n
     * @return array
     */
    private function getNIntVals(int $n): array
    {
        $int_vals = array();
        for ($i = 1; $i <= $n; $i++) {
            $int_vals[] = $i;
        }
        return $int_vals;
    }

    /**
     * converts the decimal comma in $value to a decimal point, if the field $tabId->$fieldId is of data type float
     *
     * @param $tabId
     * @param $fieldId
     * @param $value
     * @return array|mixed|string|string[]
     */
    private function convertFloatInternational($tabId, $fieldId, $value): mixed
    {
        global $gfield;

        // check if field is float
        if ($gfield[$tabId]['parse_type'][$gfield[$tabId]['id'][$fieldId]] == "6") {
            return str_replace(',', '.', $value);
        } else {
            return $value;
        }
    }

    /**
     * converts the decimal point in $value to a decimal comma, if the field $tabId->$fieldId is of data type float
     *
     * @param $tabId
     * @param $fieldId
     * @param $value
     * @return array|mixed|string|string[]
     */
    private function convertFloatGerman($tabId, $fieldId, $value): mixed
    {
        global $gfield;

        // check if data type of field is float (49)
        if ($gfield[$tabId]['data_type'][$gfield[$tabId]['id'][$fieldId]] == "49") {
            return str_replace('.', ',', $value);
        } else {
            return $value;
        }
    }

    /**
     * @param array $pData
     * @param string $type
     * @return array
     */
    private function convertpDataToChartJS(array $pData, string $type): array
    {

        $labels = [];
        $datasets = [];

        // bar / pie chart:
        // labels = x axis
        // data:
        // backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850"],
        // data: [2478,5267,734,784,433]


        // line chart:
        // data:
        // data: [2478,5267,734,784,433]
        // label: data name
        // borderColor: "#3e95cd",
        // fill: false


        // determine label dataset
        $labelkey = $pData['Abscissa'];

        $textxaxis = $pData['AbscissaName'];
        $textyaxis = '';

        if (array_key_exists('Axis', $pData) && !empty($pData['Axis'])) {
            $textyaxis = $pData['Axis'][0]['Name'];
        }

        foreach ($pData['Series'] as $key => $series) {

            if ($labelkey == $key) {
                $labels = $series['Data'];
                continue;
            }


            $data = [
                'data' => $series['Data']
            ];


            $color = sprintf("#%02x%02x%02x", $series['Color']['R'], $series['Color']['G'], $series['Color']['B']);


            if ($type == 'line') {
                $data['borderColor'] = $color;
                $data['fill'] = false;
            } else {
                $data['backgroundColor'] = array_fill(0, count($series['Data']), $color);
            }

            if ($type == 'bar' || $type == 'line') {
                $data['label'] = $key;
            }


            $datasets[] = $data;
        }


        $options = [
            'maintainAspectRatio' => false
        ];


        if ($type == 'bar' || $type == 'line') {
            $options['scales'] = [];

            if (!empty($textyaxis)) {
                $options['scales']['y'] = [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $textyaxis
                    ]
                ];
            }

            if (!empty($textxaxis)) {
                $options['scales']['x'] = [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => $textxaxis
                    ]
                ];
            }

        }

        return ['type' => $type, 'labels' => $labels, 'datasets' => $datasets, 'options' => $options];
    }
}
