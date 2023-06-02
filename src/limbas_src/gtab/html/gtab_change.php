<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$result_report["count"] = 0;


#------------------- Ansicht zurücksetzen --------------------------------
if($filter_save){
	reset_viewSettings($gtabid,$form_id);
}

if($filter_groupheader OR $filter_groupheaderKey OR $filter_gwidth OR $filter_tabulatorKey){
	save_viewSettings(null,$gtabid);
}

#----------------- zeige alle Verknpf. -------------------
$view_all_verkn = explode(";",$view_all_verkn);

#----------------- Verknpfzusatz -------------------
if($verknpf){
	$verkn = set_verknpf($verkn_tabid,$verkn_fieldid,$verkn_ID,$verkn_add_ID,$verkn_del_ID,$verkn_showonly,0);
}

#----------------- Scrollfunktionen -------------------
if($scrollto){
	$ID = scroll_to($scrollto,$gtabid,$ID,$gsr,$filter,$verkn);
}

#----------------- Plausibilitätsprüfung -------------------
if(!is_numeric($ID)){
	if($db){lmbdb_close($db);}

    lmb_alert($lang[764]);
    error_showalert($GLOBALS['alert']);
    die("</BODY>");

	#die("<BODY BGCOLOR=\"$farbschema[WEB8]\"><Script language=\"JavaScript\">\nalert('$lang[764]');\nfunction inusetime(){};</SCRIPT></BODY>");
}

if(!isset($ID) AND !$form_id){
	lmb_alert($lang["98"]);
}

# ---------------- File-upload für EXT Explorer -----------------------
/*
if($_FILES AND $f_LID AND $LINK[128]){

	$filelist["file"] = $_FILES['file']['tmp_name'];
	$filelist["file_name"] = $_FILES['file']['name'];
	$filelist["file_type"] = $_FILES['file']['type'];

	foreach ($filelist["file"] as $key => $value){
	    if(!$value){continue;}
		$singlefile = array();
		$singlefile["file"][1] = $filelist["file"][$key];
		$singlefile["file_name"][1] = $filelist["file_name"][$key];
		$singlefile["file_type"][1] = $filelist["file_type"][$key];

		$ufileId = lmb_fileUpload($singlefile,$f_datid,array("datid" => $f_datid),0,$dublicate);
		get_filestructure();

		$verkn = set_verknpf($f_gtabid,$f_fieldid,$f_datid,$ufileId,0,0,0);
		$verkn["linkParam"]["LID"] = $f_LID;
		set_joins($f_gtabid,$verkn);
	}
}
*/


if($gtab["typ"][$i] == 5){$action = "gtab_deterg";}

?>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="lmbZoomContainer" class="ajax_container" style="position:absolute;display:none;font-size:18px;font-weight:bold" OnClick="activ_menu=1;"></div>

<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/jquery.dataTables.js"></script>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>

<script>
// ----- Js-Script-Variablen --------
jsvar["action"] = "<?=$action?>";
jsvar["ID"] = "<?=$ID?>";
jsvar["gtabid"] = "<?=$gtabid?>";
jsvar["verknpf"] = "<?=$verknpf?>";
jsvar["datum"] = "<?=local_date(0)?>";
jsvar["username"] = "<?=$session["username"]?>";
jsvar["userfullname"] = "<?=$session["vorname"]." ".$session["name"]?>";
jsvar["lockable"] = "<?=$gtab["lockable"][$gtabid]?>";
jsvar["inusetime"] = "<?=$umgvar["inusetime"]?>";
jsvar["reportlevel"] = "<?=$gtab["reportlevel"][$gtabid]?>";
jsvar["wfl_id"] = "<?=$wfl_id?>";
jsvar["wfl_inst"] = "<?=$wfl_inst?>";
jsvar["ajaxpost"] = "<?=$gtab["ajaxpost"][$gtabid]?>";
jsvar["mainfield"] = "<?=$gfield[$gtabid]["mainfield"]?>";
jsvar["tablename"] = "<?=$gtab["desc"][$gtabid]?>";
jsvar["confirm_level"] = "<?=$umgvar["confirm_level"]?>";
jsvar["modal_size"] = "<?=$umgvar["modal_size"]?>";
jsvar["modal_level"] = "<?=$umgvar["modal_level"]?>";
jsvar["modal_opener"] = "<?=$umgvar["modal_opener"]?>";

<?php
if($form_id){echo "jsvar[\"is_form\"] = \"1\";\n";}
if($gformlist[$gtabid] AND lmb_count($gformlist[$gtabid]["id"]) > 0){echo "jsvar[\"is_formlist\"] = \"1\";\n";}

#echo "jsvar[\"currency\"] = new Array('".implode("','",$lmcurrency["currency"])."');\n";
if($lmcurrency){
	echo "jsvar[\"currency_code\"] = ['".implode("','",$lmcurrency["code"])."'];\n";
    echo "jsvar[\"currency_id\"] = ".json_encode($lmcurrency["id"]).";\n";
	echo "jsvar[\"currency_rate\"] = ".json_encode($lmcurrency["rate"]).";\n";
    echo "jsvar[\"default_currency\"] = '".$umgvar['default_currency']."';\n";
}

# reload list
#if($change_ok){echo "reload_frame('list');\n";}

?>


</script>


<?php

if($action == 'gtab_neu'){$action = 'gtab_change';}

# --- zeige Formular-Ansicht EXTENSION -----
if($gformlist[$gtabid]["extension"][$form_id] AND file_exists(EXTENSIONSPATH.$gformlist[$gtabid]["extension"][$form_id])){
	require_once(EXTENSIONSPATH.$gformlist[$gtabid]["extension"][$form_id]);
# --- zeige Formular-Ansicht -----
}elseif($gform[$form_id] AND $gformlist[$gtabid]["id"][$form_id] AND $gformlist[$gtabid]["typ"][$form_id] == 1){

    require(__DIR__ . '/forms/customForm.php');
	
# --- zeige Standard-Ansicht -----
}else{

    require(__DIR__ . '/forms/defaultForm.php');
    
}

unset($commit);
?>

<span id="myExtForms"></span>

</form>
