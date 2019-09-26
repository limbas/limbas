<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */

require_once('gtab/gtab.lib');

/*
 * Author: Peter
 * Settings that will be needed to calculate the new settings: diag_width, diag_height, fontsize
 * Uses the database settings specified in $src to automatically calculate settings like legend-position or padding
 * $chartData is the data previously put into the chart in the pChart-data-format
 * Returns: indexed array of automatically calculated settings
 */
function lmb_diagramAutoSettings($src, $chartData) {
    global $isLine;
    global $isBar;
    global $isPie;
    
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
    if($isPie) {
        // diag_width = longestString + 2*radius + longestString
        // diag_height = 2*radius + 2*stringheight
        if(!$dest['PIE_RADIUS']) {
            $sts = stringSize(getLongestAbscissaValue($chartData));
            $longestStringWidth = $sts['w'];
            $maxRadiusWidth = ($src['DIAG_WIDTH'] - 2 * $longestStringWidth) / 2 - 35;
            $sts = stringSize("42");
            $maxRadiusHeight = ($src['DIAG_HEIGHT'] - 2 * $sts['h']) / 2 - 45;
            $dest['PIE_RADIUS'] = min($maxRadiusWidth, $maxRadiusHeight);
        }
        
        $dest['PIE_RADIUS'] = max(1, $dest['PIE_RADIUS']);
        
        // pie should be centered
        if(!$dest['PADDING_LEFT']) {
            $dest['PADDING_LEFT'] = $src['DIAG_WIDTH'] / 2;
        }
        if(!$dest['PADDING_RIGHT']) {
            $dest['PADDING_RIGHT'] = $src['DIAG_WIDTH'] / 2;
        }
        if(!$dest['PADDING_TOP']) {
            $dest['PADDING_TOP'] = $src['DIAG_HEIGHT'] / 2;
        }
        if(!$dest['PADDING_BOTTOM']) {
            $dest['PADDING_BOTTOM'] = $src['DIAG_HEIGHT'] / 2;        
        }
        
    }
    
    // line chart / bar chart
    if($isLine || $isBar) {
        // basepadding on all 4 sides
        $basePadding = 20;
        
        // legend size
        $legendBoxSize = 10;
        $legendWidth = 0;
        $legendHeight = 0;
        $legendNumRows = sizeof($chartData['Series']) - 1; // -1 for ignoring abscissa
        if($src['LEGEND_MODE'] == 'vertical') {
            $sts = stringSize(getLongestLegendEntry($chartData));
            $legendWidth = $legendBoxSize + $sts['w'];
            $sts = stringSize("42");
            $legendHeight = ($sts['h'] + 2) * $legendNumRows; // +2 for space between lines
        } else if($src['LEGEND_MODE'] == 'horizontal') {
            $sts = stringSize(getLongestLegendEntry($chartData));
            $legendWidth = ($legendBoxSize + $sts['w']) * $legendNumRows;
            $sts = stringSize("42");
            $legendHeight = $sts['h'];
        }
        
        // right side: basepadding + legendwidth + basepadding
        if(!$dest['PADDING_RIGHT']) {
            $dest['PADDING_RIGHT'] = 2 * $basePadding + $legendWidth;
        }
        
        // top side: basepadding + half of maximum y-scale entry
        if(!$dest['PADDING_TOP']) {
            $sts = stringSize("42");
            $dest['PADDING_TOP'] = $basePadding + $sts['h'] / 2;
        }
     
        // bottom side: basepadding + height of rotated x-axis texts + height of x-axis description
        $sts = stringSize(getLongestAbscissaValue($chartData), LABEL_ROTATION);
        $xAxisTextHeight = $sts['h'];
        $sts = stringSize($src['TEXT_X']);
        $xAxisDescriptionHeight = $sts['h'];
        if(!$dest['PADDING_BOTTOM']) {
            $dest['PADDING_BOTTOM'] = $basePadding + $xAxisTextHeight + $xAxisDescriptionHeight;
        }
        
        // left side: basepadding + height of y-axis description + width of longest y-scale entry + 5 (space added into pDraw class)
        $sts = stringSize($src['TEXT_Y']);
        $yAxisDescriptionHeight = $sts['h'];
        $sts = stringSize(getLongestYAxisValue($chartData));
        $yAxisTextWidth = $sts['w'];
        if(!$dest['PADDING_LEFT']) {
            $dest['PADDING_LEFT'] = $basePadding + $yAxisDescriptionHeight + $yAxisTextWidth + 5;
        }
        
        // center legend (use height of diagram without x-axis texts)
        if(!$dest['LEGEND_Y']) {
            $sts = stringSize("42");
            $dest['LEGEND_Y'] = $basePadding + $sts['h'] / 2 + $legendHeight / 2 - 1; //($src['DIAG_HEIGHT'] - $legendHeight - $xAxisTextHeight) / 2;
        }
        if(!$dest['LEGEND_X']) {
            $dest['LEGEND_X'] = $src['DIAG_WIDTH'] - $legendWidth - $basePadding - 1;
        }
    }
    
    return $dest;
}

function getLongestLegendEntry($chartData) {
    $maxLength = 0;
    $maxValue = "";
    
    foreach($chartData['Series'] as $seriesName => $bla) {
        // abscissa has no entry in legend
        if($seriesName == $chartData['Abscissa']) {
            continue;
        }    
        
        if(lmb_strlen($seriesName) > $maxLength) {
            $maxLength = lmb_strlen($seriesName);
            $maxValue = $seriesName;
        }        
    }
    
    return $maxValue;
}

function getLongestYAxisValue($chartData) {
    $maxLength = 0;
    $maxValue = "";
    
    foreach($chartData['Series'] as $seriesName => $seriesData) {
        // abscissa has no values
        if($seriesName == $chartData['Abscissa']) {
            continue;
        }
        
        if(lmb_strlen($seriesData['Max']) > $maxLength) {
            $maxLength = lmb_strlen($seriesData['Max']);
            $maxValue = $seriesData['Max'];
        }
    }
    
    return $maxValue;
}

function getLongestAbscissaValue($chartData) {
    $abscissaName = $chartData['Abscissa'];
    $abscissaValues = $chartData['Series'][$abscissaName]['Data'];
    
    // find longest entry
    $maxLength = 0;
    $maxValue = "";
    foreach($abscissaValues as $value) {
        if(lmb_strlen($value) > $maxLength) {
            $maxLength = lmb_strlen($value);
            $maxValue = $value;
        }
    }

    return $maxValue;
}

function stringSize($text, $angle=0) {
    global $fontsize;
    global $umgvar;
    global $fontlocation;
    
    $fontPath = $umgvar['path'] . '/' . $fontlocation;
    $bBox = imagettfbbox($fontsize, $angle, $fontPath, $text);
    
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

/*
 * Author: Peter
 * Creates png-image of diagram
 * Returns: location of saved png-image
 */
$fontsize = null;
$isLine = null;
$isBar = null;
$isPie = null;
$fontlocation = null;
        
function lmb_createDiagram($diag_id, $gsr=null, $filter=null, $verkn=null, $extension=null, $width=null, $height=null, $style=array()){
	global $db;
	global $gfield;
	global $gdiaglist;
	global $session;
	global $umgvar;
        global $fontsize;
        global $isLine;
        global $isBar;
        global $isPie;
        global $fontlocation;

        // parse style
        $bgcolor = $style[21] ? $style[21] : 'ffffff';
        $fontsize = $style[3] ? $style[3] : null; // use fontsize from database
        $fontlocation = lmb_getFontLocation($style);
        $fontColorArr = $style[9] ? lmb_getColorAsArray($style[9]) : array();
            
        $gtabid = $gdiaglist['gtabid'][$diag_id];
	if(!$gdiaglist[$gtabid]['id'][$diag_id]){return false;}
	
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
	$sqlquery = "SELECT " . implode(',', $settingNames) . " FROM LMB_CHART_LIST WHERE ID=".parse_db_int($diag_id);
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(odbc_fetch_row($rs)){

        $dbSettings = array();
        foreach($settingNames as $name) {
            $dbSettings[$name] = odbc_result($rs, $name);
        }

            // get width/height from parameters instead of database
        if(!$width){$width = odbc_result($rs, "DIAG_WIDTH");}
        if(!$height){$height = odbc_result($rs, "DIAG_HEIGHT");}
        $dbSettings['DIAG_WIDTH'] = $width;
        $dbSettings['DIAG_HEIGHT'] = $height;

            // fixed settings
        $text_x = odbc_result($rs, "TEXT_X");
        $text_y = odbc_result($rs, "TEXT_Y");
        if(!$fontsize){ $fontsize = odbc_result($rs, "FONT_SIZE"); }
        $legend_mode = odbc_result($rs, "LEGEND_MODE");
        $pie_write_values = odbc_result($rs, "PIE_WRITE_VALUES");
        $diag_tab_id = odbc_result($rs, "TAB_ID");
        $diag_type = odbc_result($rs, "DIAG_TYPE");
        $diagname = $gdiaglist[$gtabid]["name"][$diag_id];

        /* Define transposed-mode */
        $isTransposed = odbc_result($rs, "TRANSPOSED");

        /* Define chart types */
        $isLine = ($diag_type == "Line-Graph");
        $isBar = ($diag_type == "Bar-Chart");
        $isPie = ($diag_type == "Pie-Chart");

    }
        	
	/* Define axis types */
	define("DATA_AXIS", "1");
	define("CAPTION_AXIS", "2");

	/* Get fields from database */
	$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS,COLOR FROM LMB_CHARTS WHERE CHART_ID = $diag_id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$fields = array();
	$num_data_axes = 0;
	$num_caption_axes = 0;
	$bzm=0;
	while(odbc_fetch_row($rs)){
		$fields[$bzm]['field_id'] = odbc_result($rs, "FIELD_ID");
		$tmp_axis = odbc_result($rs, "AXIS");
		if($tmp_axis == DATA_AXIS){
			$num_data_axes++;
		}elseif($tmp_axis == CAPTION_AXIS){
			$num_caption_axes++;
		}
		$fields[$bzm]['axis'] = $tmp_axis;
		$fields[$bzm]['color'] = odbc_result($rs, "COLOR");
		$bzm++;
	}

	/* Collect data from database */
	$fieldids = array();
	for($i = 0; $i < count($fields); $i++){
		$fieldids[] = $fields[$i]['field_id'];
	}

	$data = get_gresult($diag_tab_id, 1, $filter, $gsr, $verkn, array($diag_tab_id => $fieldids),null,$extension);
	$data = $data[$diag_tab_id];

	/* Check if number of selected axes matches diagram type */
	$err_wrong_diag = false;
	$err_wrong_axes_count = false;
	$warning_too_much_data = false;
                
	if($isTransposed){
		if($isBar || $isLine){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
		}elseif($isPie){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 0);
			$warning_too_much_data = $isPie && $data['max_count'] > 1;
		}else{
			$err_wrong_diag = true;
		}
	}else{
		if($isBar || $isLine){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
		}elseif($isPie){
			$err_wrong_axes_count = ($num_data_axes != 1 || $num_caption_axes > 1);
		}else{
			$err_wrong_diag = true;
		}
	}
		
	if($err_wrong_diag){
		lmb_alert("invalid type: " . $diag_type);
		return;
	}
	if($err_wrong_axes_count){
		lmb_alert("invalid amount of fields/axis!");
		return;
	}
	if($warning_too_much_data){
		lmb_alert("too many data, only last line will be used!");
	}	

	/* Chart-customization constants */
	$diagname = str_replace(lmb_utf8_decode("ä"),"ae",$diagname);
	$diagname = str_replace(lmb_utf8_decode("ö"),"oe",$diagname);
	$diagname = str_replace(lmb_utf8_decode("ü"),"ue",$diagname);
	$diagname = str_replace(lmb_utf8_decode("ß"),"ss",$diagname);
	$diagname = preg_replace("/[^[:alnum:]\.]|[ ]/","_",$diagname);

	$saveLocation = "USER/". $session['user_id'] ."/temp/" . $diagname . "_$diag_id.png";
	define("LINETHICKNESS", 0.5); // no linethickness means better antialiasing to pchart, but apparently does only work for tranposed graphs
        define("LABEL_ROTATION", 15);
        if(!file_exists($umgvar['path'].'/'.$fontlocation)){
            lmb_alert('missing font '.$fontlocation.'!');
            return false;            
        }

        /* Include chart classes */
	require_once("extern/pChart/pDraw.class.php");
	require_once("extern/pChart/pImage.class.php");
	require_once("extern/pChart/pData.class.php");
	require_once("extern/pChart/pPie.class.php");

	/* Find id of caption-field */
	$captionid = -1;
	foreach($fields as $field){
		if($field["axis"] == CAPTION_AXIS){
			$captionid = $field["field_id"];
			break;
		}
	}	
	
	/* Create dataset object */
	$myData = new pData();
	$myData->setAxisName(0, $text_y);

	/* Fill dataset object (TYPE NORMAL) */
	if(!$isTransposed){		
		foreach($fieldids as $fieldid){
			$tmp_data = array();
			for($i = 0; $i < count($data[$fieldid]); $i++){
				/* Display values after caption name (PIE-Chart only) */
				if($isPie && $fieldid == $captionid && $pie_write_values != "none"){
					// Get id of data field
					$dataid = null;
					foreach($fieldids as $tmp_fieldid){
						if($tmp_fieldid != $captionid){
							$dataid = $tmp_fieldid;
							break;
						}
					}
					/* Differ between percent and value display */
					$sum = 1;
					if($pie_write_values == "percent"){
						$sum = 0;
						for($u = 0; $u < count($data[$dataid]); $u++){
							$sum += $data[$dataid][$u];
						}
						$tmp_data[$i] = $data[$fieldid][$i] . " (" . number_format((float)($data[$dataid][$i] / $sum)*100,1) . "%)";
					}elseif($pie_write_values == "value"){
						$tmp_data[$i] = $data[$fieldid][$i] . " (" . ($data[$dataid][$i]) . ")";
					}
					
					
				}else{
					$tmp_data[$i] = lmb_convertFloatInternational($diag_tab_id, $fieldid, $data[$fieldid][$i]);
				}
			}                                                
			$columnname = $gfield[$diag_tab_id]['spelling'][$fieldid];
			$myData->addPoints($tmp_data, $columnname);
			$myData->setSerieWeight($columnname, LINETHICKNESS);

			/* Color values */
			if(!$isPie){
				// Find color of field
				$hexcolor = null;
				for($i = 0; $i < count($fields); $i++){
					if($fields[$i]["field_id"] == $fieldid){
						$hexcolor = $fields[$i]["color"];
						break;
					}
				}
				$myData->setPalette($columnname, lmb_getColorAsArray($hexcolor));
			}
			/* Abscissa */
			if($fieldid == $captionid){
				$myData->setAbscissa($columnname);
				$myData->setAbscissaName($text_x);
			}
		}
		
		/* No caption-field -> set numbers as caption */
		if($captionid == -1){
			$int_vals = lmb_getNIntVals(count($data[$fieldids[0]]));			
			$myData->addPoints($int_vals, -1);
			$myData->setAbscissa(-1);
		}

	}
	
	/* Fill dataset object (TYPE TRANSPOSED) */
	if($isTransposed){
		/* fill data */
		$tmp_data = null;
		for($i = 0; $i < $data['max_count']; $i++){
			$tmp_data = array();
			foreach($fieldids as $fieldid){	
				if($fieldid != $captionid){                                        
					$tmp_data[] = lmb_convertFloatInternational($diag_tab_id, $fieldid, $data[$fieldid][$i]);		
				}
			}
			/* get row name */
			if(($isLine || $isBar) && $num_caption_axes == 1){
				$myData->addPoints($tmp_data, $data[$captionid][$i]);
				$myData->setSerieWeight($data[$captionid][$i], LINETHICKNESS);
			}else{
				$myData->addPoints($tmp_data, "Zeile ".$i); //TODO: string ersetzen
				$myData->setSerieWeight("Zeile ".$i, LINETHICKNESS); //TODO: string ersetzen
			}
		}
		/* set row-headers as abscissa, add percent/value if selected */
		$abscissa = array();
		$i = 0;
		foreach($fieldids as $fieldid){
			if($fieldid != $captionid){
				$text = $gfield[$diag_tab_id]['spelling'][$fieldid];
				if($isPie && $pie_write_values == "percent"){
					$sum = 0;
					for($u = 0; $u < count($tmp_data); $u++){
						$sum += $tmp_data[$u];
					}
					$text .= " (" . str_replace('.', ',', number_format((float)($tmp_data[$i] / $sum)*100,1)) . "%)";
				}elseif($isPie && $pie_write_values == "value"){
					$text .= " (" . (lmb_convertFloatGerman($diag_tab_id, $fieldid, $tmp_data[$i])) . ")";
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
    $settings = lmb_diagramAutoSettings($dbSettings, $myData->getData());      
	
	/* Init chart */
	$myPicture = new pImage($width,$height,$myData);
	$myPicture->setGraphArea($settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width-$settings['PADDING_RIGHT'], $height-$settings['PADDING_BOTTOM']);
	//$myPicture->setShadow(true);
    
    $fontProperties = $fontColorArr;
    $fontProperties['FontName'] = $fontlocation;
    $fontProperties['FontSize'] = $fontsize;
    $myPicture->setFontProperties($fontProperties);
    $myPicture->Antialias = TRUE;

    // draw background
    if($bgcolor) {
        $bgColArr = lmb_getColorAsArray($bgcolor);
        $bgColArr['Surrounding'] = 0;
        $myPicture->drawFilledRectangle(0, 0, $width, $height, $bgColArr);
    }
    
    // draw transparent black rectangle over graph area
    $myPicture->drawFilledRectangle(
            $settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width-$settings['PADDING_RIGHT'], $height-$settings['PADDING_BOTTOM'],
            array("R"=>0,"G"=>0,"B"=>0,"Surrounding"=>-255,"Alpha"=>5)
    );
    
    // draw two black lines around the graph area
    $myPicture->drawLine($settings['PADDING_LEFT'], $settings['PADDING_TOP'], $width-$settings['PADDING_RIGHT'], $settings['PADDING_TOP'],
            array("R"=>0,"G"=>0,"B"=>0)
    );
    $myPicture->drawLine($width-$settings['PADDING_RIGHT'], $settings['PADDING_TOP'], $width-$settings['PADDING_RIGHT'], $height-$settings['PADDING_BOTTOM'],
            array("R"=>0,"G"=>0,"B"=>0)
    );
    
	/* Differ between chart types */
	if($isBar || $isLine){
		$myPicture->drawScale(array('LabelRotation'=>LABEL_ROTATION));
		if($legend_mode != "none"){
			$legend_mode = ($legend_mode=="vertical")?690901:690902;
			$myPicture->drawLegend($settings['LEGEND_X'], $settings['LEGEND_Y'], array("Style"=>LEGEND_BOX, "Mode"=>$legend_mode, "R"=>0,"G"=>0,"B"=>0, "Alpha"=>5, "BorderR"=>0, "BorderG"=>0, "BorderB"=>0));
		}
		if($isBar){
			$myPicture->drawBarChart(array("Rounded"=>FALSE, "Orientation"=>ORIENTATION_HORIZONTAL));
		}elseif($isLine){
			$myPicture->drawLineChart();
		}
	}elseif($isPie){
		$PieChart = new pPie($myPicture,$myData);

        /* Pie-slice colors */
		if($isPie && $isTransposed){
			for($i = 0; $i < count($fields); $i++){
				$hexcolor = $fields[$i]["color"];
				$PieChart->setSliceColor($i, lmb_getColorAsArray($hexcolor));
			}	
		}		
		
		$PieChart->draw2DPie($settings['PADDING_LEFT'], $settings['PADDING_TOP'], 
                array(
                    "DrawLabels"=>TRUE,
                    "Border"=>TRUE,
                    "Radius"=>$settings['PIE_RADIUS'],
                    "LabelR" => $fontColorArr['R'],
                    "LabelG" => $fontColorArr['G'],
                    "LabelB" => $fontColorArr['B'],
                )
        );		
	}

	/* Save and return image */
	$myPicture->render($saveLocation);
	
	if(file_exists($umgvar['path'].'/'.$saveLocation)){         
        return $saveLocation;
	}
}

function lmb_getFontLocation($style) {
    global $db;

    // default font
    $default = 'inc/fonts/DejaVuSans.ttf';
    
    // if no font family is specified, return default font
    if(!$style[0]) {
        return $default;
    }
    
    // extract family, bold and italic from style array
    $family = $style[0];
    $b = $style[4] == 'bold' ? 'B' :'';
    $i = $style[1] == 'italic' ? 'I' : '';
    
    // get name of font file from lmb_fonts
    $sqlquery = "SELECT NAME FROM LMB_FONTS WHERE STYLE='$b$i' AND FAMILY='$family'";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

    // abort if error
    if(!$rs) {
        return $default;
    }
    
    if(odbc_fetch_row($rs)) {
        $name = odbc_result($rs, 'NAME');
    }
    
    // abort if no name was found
    if(!$name) {
        return $default;
    }

    return "inc/fonts/$name.ttf";
}

function lmb_getColorAsArray($hex){
	return array("R"=>hexdec(lmb_substr($hex,0,2)),"G"=>hexdec(lmb_substr($hex,2,2)),"B"=>hexdec(lmb_substr($hex,4,2)),"Alpha"=>255);
}

function lmb_getNIntVals($n){
	$int_vals = array();
	for($i = 1; $i <= $n; $i++){
		$int_vals[] = $i;
	}
	return $int_vals;
}

// converts the decimal comma in $value to a decimal point, if the field $tabId->$fieldId is of data type float
function lmb_convertFloatInternational($tabId, $fieldId, $value) {
        global $gfield;
        
        // check if field is float
        if($gfield[$tabId]['parse_type'][ $gfield[$tabId]['id'][$fieldId] ] == "6") {
                return str_replace(',', '.', $value);
        } else {
                return $value;
        }
}
// converts the decimal point in $value to a decimal comma, if the field $tabId->$fieldId is of data type float
function lmb_convertFloatGerman($tabId, $fieldId, $value) {
        global $gfield;
        
        // check if data type of field is float (49)
        if($gfield[$tabId]['data_type'][ $gfield[$tabId]['id'][$fieldId] ] == "49") {
                return str_replace('.', ',', $value);
        } else {
                return $value;
        }
}

?>
