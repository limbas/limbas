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

if($report_xml){
	require_once('extra/report/report_xml.lib');
}else{
	require_once('extra/report/report_xmls.lib');
}

if($report_id){
	# report Infos
	$report = get_report($report_id,1);
	$dom = xml_report($ID,$report_id,$use_record,$report);
	$report_rename = reportSavename($dom[1]["name"],$dom[1]["savename"],$ID,"xml",$report_rename);
	$dom[1]["name"] = $report_rename;
	if($report_medium == "xml"){
		if($generatedReport = store_xml($dom[0],$dom[1],$ID,$report_output)){
			# nur Report generieren
			if($report_output != 2 AND !$params){
				echo "<script language=\"JavaScript\">document.location.href = 'USER/".$session["user_id"]."/temp/".urlencode($generatedReport)."';</script>";
			}
		}
	}elseif($report_medium == "odt"){
		$generatedReport = store_xml($dom[0],$dom[1],$ID,1);
	}
	
	$generatedReport = $umgvar["pfad"]."/USER/".$session["user_id"]."/temp/".$generatedReport;
}

?>