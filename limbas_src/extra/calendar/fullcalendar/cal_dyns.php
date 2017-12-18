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

$gtabid = $params["gtabid"];
$tresult = array();
$lmb_calendar = new lmb_calendar($gtabid);


# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_calendar.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_calendar.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

if($params["verkn_ID"]){
	$verkn = init_relation($params["verkn_tabid"],$params["verkn_fieldid"],$params["verkn_ID"],null,null,1);
}

if($params["action"] == "context"){
	echo $lmb_calendar->lmb_calContext($params["ID"],$params["gtabid"]);
}elseif($params["action"] == "delete"){
	$lmb_calendar->lmb_deleteEvent($params["gtabid"],$params["ID"],$verkn);
}elseif($params["action"] == "drop" OR $params["action"] == "resize"){
	$lmb_calendar->lmb_dropEvent($params["gtabid"],$params["ID"],$params["dayDelta"],$params["minuteDelta"],$params["action"],$params["resource"]);
}elseif($params["action"] == "move"){
	$lmb_calendar->lmb_moveEvent($params["gtabid"],$params["ID"],$params["stamp"],$params["resource"]);
}elseif($params["action"] == "copy"){
	$lmb_calendar->lmb_copyEvent($params["gtabid"],$params["ID"],$params["stamp"],$params["resource"]);
}elseif($params["action"] == "add"){
	echo $lmb_calendar->lmb_addEvent($params["gtabid"],$params["title"],$params["start"],$params["end"],$params["allDay"],$params["resource"],$verkn,$params);
	#}elseif($params["action"] == "edit"){
	#	lmb_editEvent($params["gtabid"],$params["ID"],$params["title"],$params["start"],$params["title"]);

}elseif($params["action"] == "details" AND $params["form_id"]){
	require_once('gtab/gtab_form.lib');
	require_once('gtab/gtab_type.lib');
	require_once('gtab/sql/gtab_change.dao');
	
	$ID = floor($params["ID"]);
	
	if($params["act"] == 'gtab_change'){
		$gresult = get_gresult($gtabid,null,null,null,null,$gform[$params["form_id"]]["used_fields"],$ID);
		form_gresult($ID,$gtabid,$params["form_id"],$gresult); # need for fields of related tables
	}else{
		$ID = 0; # bei Anlage neuer Datensatz
	}
	formListElements($params["act"],$gtabid,$ID,$gresult,$params["form_id"]);
	
}elseif($params["action"] == "details"){
	require_once("gtab/gtab.lib");
	require_once("gtab/gtab_type.lib");
	$gtabid = $params["gtabid"];
	$ID = floor($params["ID"]);
	
	if($ID){
		$gresult = get_gresult($gtabid,1,null,null,null,null,$ID);
	}else{$ID = 0;}
	
	#REPEATUNTIL REPETITION
		?>
			<table cellpadding="1" cellspacing="0" width="100%">
			<tr><td valign="top" colspan="4"><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["SUBJECT"]]?><br>
			<?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["SUBJECT"],$ID,1,null,"width:100%;height:36px;");?><br><br></td></tr>

			<tr><td><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["STARTSTAMP"]]?>&nbsp;&nbsp;&nbsp;</td><td><?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["STARTSTAMP"],$ID,1,null,"width:100px;");?></td><td>&nbsp;&nbsp;&nbsp;<?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["ENDSTAMP"]]?>&nbsp;&nbsp;&nbsp;</td><td><?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["ENDSTAMP"],$ID,1,null,"width:100px;");?></td></tr>
			<tr><td><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["COLOR"]]?>&nbsp;&nbsp;&nbsp;</td><td><?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["COLOR"],$ID,1,null,"width:100px;");?></td><td>&nbsp;&nbsp;&nbsp;<?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["ALLDAY"]]?><td><?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["ALLDAY"],$ID,1);?></td></tr>
			<tr><td><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["REPEATUNTIL"]]?><td nowrap><?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["REPEATUNTIL"],$ID,1,null,"width:100px;");?></td></tr>

			<tr><td colspan="4"><br><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["DESCRIPTION"]]?><br>
			<?display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["DESCRIPTION"],$ID,1,null,"width:100%;height:100px");?></td></tr>
			<tr><td colspan="4">
			<input type="button" value="<?=$lang[33]?>" onclick="lmb_calEdit(event);">
			<?if($ID > 0){
				echo "<input type=\"button\" value=\"details\" onclick=\"lmb_popupDetails(event,'$gtabid','$ID')\">";
				echo "<input style=\"float:right\" onclick=\"lmb_calDelete(event,'$gtabid','$ID')\" type=\"button\" value=\"$lang[160]\"></td></tr>";
			}?>
			</table>
		<?
}elseif($params["action"] == "saveDetails"){
	
	# adding bulk dates
	if($params["calBulk_termStaH"]){
		$lmb_calendar->lmb_addBulkDate($params,$verkn);
	# add / change single date
	}else{
		$lmb_calendar->lmb_historyUpdate($params);
	}
	
}elseif($params["action"] == "resources"){
	
	$resources = $lmb_calendar->getResources($gtabid,$params);
	$GLOBALS["noencode"] = 1;
	
	if($resources){
		echo json_encode($resources);
	}else{
		echo 1;
	}
	
	return;
	
	#echo '[{"name":"Resource 2","id":"resource2"},{"name":"Resource 1","id":"resource1"},{"name":"Raum 4711","id":"r4711"}]';
}


if($params["reload"]){
	if($tresult = $lmb_calendar->lmb_getEvent($tresult,$params)){
		$tresult = lmb_arrayDecode($tresult);
		$GLOBALS["noencode"] = 1;
		echo json_encode($tresult);
	}else{
		echo 1;
	}
}


?>