<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


global $session;

if($report_xml){
	require_once(COREPATH . 'extra/report/report_xml.lib');
}else{
	require_once(COREPATH . 'extra/report/report_xmls.lib');
}

if($report_id){
	# report Infos
	$report = get_report($report_id,1);
	$report_rename = reportSavename($report['name'],$report['savename'],$ID,"xml",$report_rename);
    $report['name'] = $report_rename;
    $report_ = $report;

    $report['name'] = lmb_utf8_encode($report['name']);
    $report['savename'] = lmb_utf8_encode($report['savename']);

	$dom = xml_report($ID,$report_id,$use_record,$report);

	if($report_medium == "xml"){
		if($generatedReport = store_xml($dom[0],$report_,$ID,$report_output)){
			# nur Report generieren
			if($report_output != 2 AND $report_output != 5 AND !$params){
				echo "<script language=\"JavaScript\">document.location.href = 'USER/".$session["user_id"]."/temp/".$generatedReport."';</script>";
			}
		}
	}elseif($report_medium == "odt"){
		$generatedReport = store_xml($dom[0],$report_,$ID,1);
	}

	$generatedReport = USERPATH.$session["user_id"]."/temp/".$generatedReport;
}

?>
