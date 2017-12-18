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


# generate xml report
require_once("extra/report/report_xmls.lib");

# use excel class
include 'extern/PHPExcel/Classes/PHPExcel/IOFactory.php';

$sqlquery1 = "SELECT DISTINCT LDMS_FILES.NAME,LDMS_FILES.SECNAME,LMB_MIMETYPES.EXT FROM LDMS_FILES,LMB_MIMETYPES WHERE LDMS_FILES.ID = ".parse_db_int($greportlist[$greportlist["argresult_tabid"][$report_id]]["ods_template"][$report_id])." AND LDMS_FILES.MIMETYPE = LMB_MIMETYPES.ID";
$rs1 = odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
$ootemplate = odbc_result($rs1,"SECNAME").".".odbc_result($rs1,"EXT");
$ootemplatepath = $umgvar["pfad"]."/UPLOAD/".$ootemplate;
if(!file_exists($ootemplatepath)){
	echo "no Office Template found!";
	return false;
}

$report = get_report($report_id,1);
$dom = xml_report($ID,$report_id,$use_record,$report);
if(!$umgvar['report_calc_output']){$umgvar['report_calc_output'] = 'xls';}
$report_rename = reportSavename($dom[1]["name"],$dom[1]["savename"],$ID,$umgvar['report_calc_output'],$report_rename);

$dom = $dom[0];

$objPHPExcel = PHPExcel_IOFactory::load($ootemplatepath);
$sheetData = $objPHPExcel->getSheet(0)->toArray(null,true,true,true);
$sheet = $objPHPExcel->setActiveSheetIndex(0);
$sheet->setShowGridlines(false);



foreach ($sheetData as $rowkey => $row){
	foreach ($row as $colkey => $value){
		if(lmb_substr($value,0,2) == '${'){
		
			$name = lmb_OOparseName($value);
			$el = $dom->getElementByID($name);
			
			# table
			$nodename = $el->nodeName;
			if($nodename == 'table'){
				# rows
				$rowNodes = $el->childNodes;
				if($rowNodes){
					$nrowkey = $rowkey;
					foreach($rowNodes as $rkey=>$rowNode){
						# cols
						$colNodes = $rowNode->childNodes;
						if($colNodes){
							$ncolkey = $colkey;
							foreach($colNodes as $ckey=>$colNode){
								$nvalue = $colNode->nodeValue;
								$nstyle = $colNode->getAttribute('style');
								
								$sheet->setCellValue($ncolkey.$nrowkey, $nvalue);
								lmb_OOformatCell($sheet,$ncolkey,$nrowkey,$nstyle);

								$ncolkey++;
							}
						}
						$nrowkey++;
					}
				}
			}elseif($nodename == 'text' OR $nodename == 'html'){
				$nvalue = $el->nodeValue;
				$nstyle = $el->getAttribute('style');
				$sheet->setCellValue($colkey.$rowkey, $nvalue);
				lmb_OOformatCell($sheet,$colkey,$rowkey,$nstyle);
			}
		}
	
	}
}


/*------- Style ---------*/
function lmb_OOsetStyle($stylestring){
	$stylestring = explode(";",$stylestring);
	foreach ($stylestring as $key => $value){
		$stylepart = explode(":",$value);
		$style[$stylepart[0]] = $stylepart[1];
		
	}
	return $style;
}


function lmb_OOformatCell($sheet,$ncolkey,$nrowkey,$style){

	$style = lmb_OOsetStyle($style);
	$styleArray = null;

	# border
	if($style['border-style'] AND $style['border-width']){

		if($style['border-width'] < 1){
			$width = PHPExcel_Style_Border::BORDER_HAIR;
		}elseif($style['border-width'] <= 2){
			$width = PHPExcel_Style_Border::BORDER_THIN;
		}else{
			$width = PHPExcel_Style_Border::BORDER_THICK;
		}

		$styleArray['borders']['outline'] = array(
		'style' => $width,
		'color' => array('rgb' => $style['border-color'])
		);
		if($style['border-left']){
			$styleArray['borders']['left']['style'] = 'none';
		}
		if($style['border-right']){
			$styleArray['borders']['right']['style'] = 'none';
		}
		if($style['border-top']){
			$styleArray['borders']['top']['style'] = 'none';
		}
		if($style['border-bottom']){
			$styleArray['borders']['bottom']['style'] = 'none';
		}

	}
	
	# font weight
	if($style['font-weight'] == 'bold'){
		$styleArray['font']['bold'] = true;
	}
	
	# font color
	if($style['color']){
		$styleArray['font']['color'] = array('rgb'=>$style['color']);
	}
	
	# font size
	if($style['font-size']){
		$styleArray['font']['size'] = $style['font-size'];
	}
	
	# font decoration
	if($style['text-decoration'] == 'underline'){
		$styleArray['font']['underline'] = true;
	}
	
	# font decoration
	if($style['text-decoration'] == 'strike'){
		$styleArray['font']['strike'] = true;
	}
	
	# alignment
	if($style['text-align']){
		$styleArray['alignment']['horizontal'] = $style['text-align'];
	}
	
	# alignment
	if($style['vertical-align']){
		$styleArray['alignment']['vertical'] = $style['vertical-align'];
	}
	
	# background
	if($style['background-color']){
		$styleArray['fill']['type'] = 'solid';
		$styleArray['fill']['startcolor'] = array('rgb'=>$style['background-color']);
	}
	
	if($styleArray){
		$sheet->getStyle($ncolkey.$nrowkey)->applyFromArray($styleArray);
	}
	
	# colspan
	if($style['colspan']){
		$nccolk = $ncolkey;
		for($i = 1; $i < $style['colspan']; $i++){$nccolk++;}
		$sheet->mergeCells($ncolkey.$nrowkey.":".$nccolk.$nrowkey);
	}
	
	/*
	
	# first define relation size e.g. $style['width'] / $factor
	
	if($style['width']){
		$sheet->getColumnDimension($ncolkey)->setWidth($style['width']);
	}

	if(!$style['width']){
		$sheet->getColumnDimension($ncolkey)->setAutoSize(true);
	}
	*/
	
	$sheet->getColumnDimension($ncolkey)->setAutoSize(true);
	#$sheet->getColumnDimension($ncolkey)->setOutlineLevel(5);

}



function lmb_OOparseName($name){
	
	return lmb_substr($name,2,lmb_strlen($name)-3);

}


if($umgvar['report_calc_output'] == 'xlsx'){$output = 'Excel2007';}
elseif($umgvar['report_calc_output'] == 'xls'){$output = 'Excel5';}
else{$output = 'Excel5';}

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $output);
$generatedReport = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/".$report_rename;
$objWriter -> save($generatedReport);

if($report_output != 2 AND !$params){
	view_report($generatedReport);
}
?>