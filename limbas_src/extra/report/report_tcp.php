<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 93
 */

global $umgvar;
require_once("extra/report/report_tcpdf.lib");

$rep_ = get_report($report_id,null);
if($rep_["extension"]){
	if(file_exists($umgvar["pfad"].$rep_["extension"])){require_once($umgvar["pfad"].$rep_["extension"]);}
}
if(!$end){

    try {
        # --- Einzelbericht eines Datensatzes -----------------------------------
        if ($ID) {
            $generatedReport = lmb_reportCreatePDF($report_id,$ID,$report_output,$report_rename);
            # --- Berichtsliste mehrerer Datensätze (gefiltert) -----------------------------------
        } elseif ($use_record == "all") {
            $generatedReport = lmb_reportCreatePDFlistFiler($gtabid, $report_id, $report_output, $filter, $gsr, $verkn, $report_rename);
            # --- Berichtsliste mehrerer Datensätze (ausgewählt) -----------------------------------
        } elseif ($use_record) {
            $generatedReport = lmb_reportCreatePDFlistSel($gtabid, $report_id, $report_output, $use_record, $report_rename);
            # --- Einzelbericht als Daten-Liste -----------------------------------
        } elseif ($report_id AND $rep_["listmode"] AND $gtabid) {
            $generatedReport = lmb_reportCreatePDF($report_id,1,$report_output,$report_rename);
            # --- freier Einzelbericht ohne Tabelle -----------------------------------
        } elseif ($report_id AND !$gtabid) {
            $generatedReport = lmb_reportCreatePDF($report_id,0,1,$report_rename);
        }
    } catch (DynamicDataUnresolvedException $e) {
        // this should happen if list reports containing dynamic data placeholders are executed directly from the nav.
        // open preview
        $url = "main.php?". http_build_query(array(
                'action' => 'report_preview',
                'gtabid' => $gtabid,
                'report_id' => $report_id,
                'ID' => $ID,
                'use_record' => $use_record,
            ));
        echo "<script language=\"JavaScript\">document.location.href = '{$url}';</script>";
    }
	if($report_output != 2 AND $report_output != 5 AND !$params){
		view_report($generatedReport);
	}
}

?>
