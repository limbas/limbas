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

if($report_xml){
	require_once('extra/report/report_xml.lib');
}else{
	require_once('extra/report/report_xmls.lib');
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
			if($report_output != 2 AND !$params){
				echo "<script language=\"JavaScript\">document.location.href = 'USER/".$session["user_id"]."/temp/".$generatedReport."';</script>";
			}
		}
	}elseif($report_medium == "odt"){
		$generatedReport = store_xml($dom[0],$report_,$ID,1);
	}

	$generatedReport = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/".$generatedReport;
}

?>