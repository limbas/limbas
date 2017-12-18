<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID:
 */

require_once('gtab/gtab.lib');

/*
 * Author: Peter
 * Creates png-image of diagram
 * Returns: img-dom-element as html-string
 */
function lmb_createDiagram($diag_id,$gsr=null,$filter=null){
	global $db;
	global $gfield;
	global $gdiaglist;
	global $session;
	global $umgvar;

	$gtabid = $gdiaglist['gtabid'][$diag_id];
	if(!$gdiaglist[$gtabid]['id'][$diag_id]){return false;}
	

	/* Get customization-settings from database */	
	$sqlquery = "SELECT TAB_ID,DIAG_TYPE,DIAG_WIDTH,DIAG_HEIGHT,TEXT_X,TEXT_Y,FONT_SIZE,PADDING_LEFT,PADDING_TOP,PADDING_RIGHT,PADDING_BOTTOM,LEGEND_X,LEGEND_Y,LEGEND_MODE,PIE_WRITE_VALUES,PIE_RADIUS,TRANSPOSED FROM LMB_CHART_LIST WHERE ID=$diag_id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	odbc_fetch_row($rs, 1);
		
	$width = odbc_result($rs, "DIAG_WIDTH");
	$height = odbc_result($rs, "DIAG_HEIGHT");
	$text_x = odbc_result($rs, "TEXT_X");
	$text_y = odbc_result($rs, "TEXT_Y");
	$fontsize = odbc_result($rs, "FONT_SIZE");
	$padding_left = odbc_result($rs, "PADDING_LEFT");
	$padding_top = odbc_result($rs, "PADDING_TOP");
	$padding_right = odbc_result($rs, "PADDING_RIGHT");
	$padding_bottom = odbc_result($rs, "PADDING_BOTTOM");
	$legend_x = odbc_result($rs, "LEGEND_X");
	$legend_y = odbc_result($rs, "LEGEND_Y");
	$legend_mode = odbc_result($rs, "LEGEND_MODE");
	$pie_write_values = odbc_result($rs, "PIE_WRITE_VALUES");
	$pie_radius = odbc_result($rs, "PIE_RADIUS");
	$diag_tab_id = odbc_result($rs, "TAB_ID");
	$diag_type = odbc_result($rs, "DIAG_TYPE");
	$diagname = $gdiaglist[$gtabid]["name"][$diag_id];
	
	
	/* Define transposed-mode */
	define("TRANSPOSED", odbc_result($rs, "TRANSPOSED"));

	/* Define chart types */
	define("LINE",$diag_type == "Line-Graph");
	define("BAR",$diag_type == "Bar-Chart");
	define("PIE",$diag_type == "Pie-Chart");
	
	/* Define axis types */
	define("DATA_AXIS","1");
	define("CAPTION_AXIS","2");	

	/* Get fields from database */
	$sqlquery = "SELECT CHART_ID, FIELD_ID, AXIS,COLOR FROM LMB_CHARTS WHERE CHART_ID = $diag_id";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$bzm = 1;
	$fields = array();
	$num_data_axes = 0;
	$num_caption_axes = 0;
	while(odbc_fetch_row($rs, $bzm)){
		$fields[$bzm-1]['field_id'] = odbc_result($rs, "FIELD_ID");
		$tmp_axis = odbc_result($rs, "AXIS");
		if($tmp_axis == DATA_AXIS){
			$num_data_axes++;
		}elseif($tmp_axis == CAPTION_AXIS){
			$num_caption_axes++;
		}
		$fields[$bzm-1]['axis'] = $tmp_axis;
		$fields[$bzm-1]['color'] = odbc_result($rs, "COLOR");
		$bzm++;
	}

	/* Collect data from database */
	$fieldids = array();
	for($i = 0; $i < count($fields); $i++){
		$fieldids[] = $fields[$i]['field_id'];
	}

	$data = get_gresult($diag_tab_id, 1, $filter, $gsr, null, array($diag_tab_id => $fieldids));
	$data = $data[$diag_tab_id];

	/* Check if number of selected axes matches diagram type */
	$err_wrong_diag = false;
	$err_wrong_axes_count = false;
	$warning_too_much_data = false;
	if(TRANSPOSED){
		if(BAR || LINE){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
		}elseif(PIE){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 0);
			$warning_too_much_data = PIE && $data['max_count'] > 1;
		}else{
			$err_wrong_diag = true;
		}
	}else{
		if(BAR || LINE){
			$err_wrong_axes_count = ($num_data_axes == 0 || $num_caption_axes > 1);
		}elseif(PIE){
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

	define("FONTLOCATION", "inc/fonts/DejaVuSans.ttf");
	define("SAVELOCATION", "USER/".$session['user_id']."/temp/".$diagname."_$diag_id.png");
	define("LINETHICKNESS", 1);
        if(!file_exists($umgvar['path'].'/'.FONTLOCATION)){
            lmb_alert('missing font '.FONTLOCATION.'!');
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
	if(!TRANSPOSED){		
		foreach($fieldids as $fieldid){
			$tmp_data = array();
			for($i = 0; $i < count($data[$fieldid]); $i++){
				/* Display values after caption name (PIE-Chart only) */
				if(PIE && $fieldid == $captionid && $pie_write_values != "none"){
					// Get id of data field
					$dataid;
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
					$tmp_data[$i] = $data[$fieldid][$i];
				}
			}
			$columnname = $gfield[$diag_tab_id]['spelling'][$fieldid];
			$myData->addPoints($tmp_data, $columnname);
			$myData->setSerieWeight($columnname, LINETHICKNESS);

			/* Color values */
			if(!PIE){
				// Find color of field
				$hexcolor;
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
	if(TRANSPOSED){
		/* fill data */
		$tmp_data;
		for($i = 0; $i < $data['max_count']; $i++){
			$tmp_data = array();
			foreach($fieldids as $fieldid){	
				if($fieldid != $captionid){
					$tmp_data[] = $data[$fieldid][$i];		
				}
			}
			/* get row name */
			if((LINE || BAR) && $num_caption_axes == 1){
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
				if(PIE && $pie_write_values == "percent"){
					$sum = 0;
					for($u = 0; $u < count($tmp_data); $u++){
						$sum += $tmp_data[$u];
					}
					$text .= " (" . number_format((float)($tmp_data[$i] / $sum)*100,1) . "%)";
				}elseif(PIE && $pie_write_values == "value"){
					$text .= " (" . ($tmp_data[$i]) . ")";
				}
				$abscissa[] = $text;		
				$i++;
			}
		}
		$myData->addPoints($abscissa, "abscissa");
		$myData->setAbscissa("abscissa");
		$myData->setAbscissaName($text_x);
	}
	
	/* Init chart */
	$myPicture = new pImage($width,$height,$myData);
	$myPicture->setGraphArea($padding_left, $padding_top, $width-$padding_right, $height-$padding_bottom);
	$myPicture->setFontProperties(array("FontName"=>FONTLOCATION,"FontSize"=>$fontsize));

	/* Differ between chart types */
	if(BAR || LINE){
		$myPicture->drawScale();
		if($legend_mode != "none"){
			$legend_mode = ($legend_mode=="vertical")?690901:690902;
			$myPicture->drawLegend($legend_x, $legend_y, array("Style"=>LEGEND_NOBORDER, "Mode"=>$legend_mode));
		}
		if($diag_type == BAR){
			$myPicture->drawBarChart(array("Rounded"=>FALSE, "Orientation"=>ORIENTATION_HORIZONTAL));
		}elseif($diag_type == LINE){
			$myPicture->drawLineChart();
		}
	}elseif(PIE){
		$PieChart = new pPie($myPicture,$myData);
		
		/* Pie-slice colors */
		if(PIE && TRANSPOSED){
			$hexcolor;
			for($i = 0; $i < count($fields); $i++){
				$hexcolor = $fields[$i]["color"];
				$PieChart->setSliceColor($i, lmb_getColorAsArray($hexcolor));
			}	
		}		
		
		$PieChart->draw2DPie($padding_left, $padding_top, array("DrawLabels"=>TRUE,"Border"=>TRUE,"Radius"=>$pie_radius));		
	}

	/* Save and return image (use ?<time> to force browser to reload img) */
	$myPicture->render(SAVELOCATION);
	
	if(file_exists($umgvar['path'].'/'.SAVELOCATION)){         
	   return SAVELOCATION;
	}
	#echo "<img src='" . SAVELOCATION . "?" . time() . "'></img>";
}

function lmb_getColorAsArray($hex){
	return array("R"=>hexdec(substr($hex,0,2)),"G"=>hexdec(substr($hex,2,2)),"B"=>hexdec(substr($hex,4,2)),"Alpha"=>100);
}

function lmb_getNIntVals($n){
	$int_vals = array();
	for($i = 1; $i <= $n; $i++){
		$int_vals[] = $i;
	}
	return $int_vals;
}
?>