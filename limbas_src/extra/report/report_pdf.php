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
 * ID: 93
 */


require_once("extra/report/report_pdf.lib");

$rep_ = get_report($report_id,null);
if($rep_["extension"]){
	if(file_exists($umgvar["pfad"].$rep_["extension"])){require_once($umgvar["pfad"].$rep_["extension"]);}
}
if(!$end){

	# --- Einzelbericht eines Datensatzes -----------------------------------
	if($ID){
		$generatedReport = LMB_pdfReportUnit($gtabid,$report_id,$report_output,$ID,$report_rename);
	# --- alle Berichte einer Tabelle -----------------------------------
	}elseif($use_record == "all"){
		$generatedReport = LMB_pdfReportAll($gtabid,$report_id,$report_output,$filter,$gsr,$verkn,$report_rename);
	# --- Berichtsliste mehrerer Datensätze -----------------------------------
	}elseif($use_record){
		$generatedReport = LMB_pdfReportRecords($gtabid,$report_id,$report_output,$use_record,$report_rename);
	# --- Einzelbericht als Daten-Liste -----------------------------------
	}elseif($report_id AND $rep_["listmode"] AND $gtabid){
		$generatedReport = LMB_pdfReportUnit($gtabid,$report_id,$report_output,1,$report_rename);
	# --- freier Einzelbericht ohne Tabelle -----------------------------------
	}elseif($report_id AND !$gtabid){
		$generatedReport = LMB_pdfReportUnit(-1,$report_id,1,0,$report_rename);
	}
	
	if($report_output != 2 AND !$params){
		view_report($generatedReport);
	}
}

?>