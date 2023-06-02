<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $umgvar;
require_once(COREPATH . 'extra/report/report_tcpdf.lib');

$rep_ = get_report($report_id,null);
if($rep_["extension"]){
	if(file_exists($umgvar["pfad"].$rep_["extension"])){require_once($umgvar["pfad"].$rep_["extension"]);}
}
if(!$end){

    $generatedReport = lmb_reportPDF($report_id,$gtabid,$ID,$report_output,$report_rename,$gsr,$filter,$use_record,$verkn,$report_printer);

    // this should happen if list reports containing dynamic data placeholders are executed directly from the nav.
    // open preview
    $url = "main.php?". http_build_query(array(
            'action' => 'report_preview',
            'gtabid' => $gtabid,
            'report_id' => $report_id,
            'ID' => $ID,
            'use_record' => $use_record,
        ));
    //TODO: endless loop if already in preview
    //echo "<script language=\"JavaScript\">document.location.href = '{$url}';</script>";

	if($report_output != 2 AND $report_output != 5 AND !$params){
		view_report($generatedReport);
	}
}

?>
