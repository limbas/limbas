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
 * ID: 13
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
	die("<BODY BGCOLOR=\"$farbschema[WEB8]\"><Script language=\"JavaScript\">\nalert('$lang[764]');\nfunction inusetime(){};</SCRIPT></BODY>");
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

		$ufileId = upload($singlefile,$f_datid,array("datid" => $f_datid),0,$dublicate);
		get_filestructure();

		$verkn = set_verknpf($f_gtabid,$f_fieldid,$f_datid,$ufileId,0,0,0);
		$verkn["linkParam"]["LID"] = $f_LID;
		set_joins($f_gtabid,$verkn);
	}
}
*/

if(!isset($ID) AND !$form_id){
	lmb_alert($lang["98"]);
}


if($gtab["typ"][$i] == 5){$action = "gtab_deterg";}

?>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="lmbZoomContainer" class="ajax_container" style="position:absolute;display:none;font-size:18px;font-weight:bold" OnClick="activ_menu=1;"></div>
<div id="dublicateCheckLayer" style="position:absolute;top:25%;left:25%;display:none;z-index:999"></div>

<Script language="JavaScript">
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

<?php
if($form_id){echo "jsvar[\"is_form\"] = \"1\";\n";}
if(count($gformlist[$gtabid]["id"]) > 0){echo "jsvar[\"is_formlist\"] = \"1\";\n";}

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

</SCRIPT>


<?php

if($action == 'gtab_neu'){$action = 'gtab_change';}

# --- zeige Formular-Ansicht EXTENSION -----
if($gformlist[$gtabid]["extension"][$form_id]){
	require_once($gformlist[$gtabid]["extension"][$form_id]);
# --- zeige Formular-Ansicht -----
}elseif($gform[$form_id] AND $gformlist[$gtabid]["id"][$form_id] AND $gformlist[$gtabid]["typ"][$form_id] == 1){

	if($ID){
		if($gtab["typ"][$gtabid] == 5){
			# need filter and search-params for using pointer
			$gresult = get_gresult($gtabid,1,$filter,$gsr,0,$gform[$form_id]["used_fields"],$ID);
		}else{
			$gresult = get_gresult($gtabid,null,null,null,0,$gform[$form_id]["used_fields"],$ID);
		}

        # ----------- multitenant permission  -----------
        if($umgvar['multitenant'] AND $gtab['multitenant'][$gtabid] AND $lmmultitenants['mid'][$session['mid']] != $gresult[$gtabid]['MID'][0] AND !$session["superadmin"]){
            $action = 'gtab_deterg';
        }

		// relation parameters fields | parent relation fields
		if($gform[$form_id]["parentrel"] OR $gform[$form_id]["paramrel"]){
		    $verkn = null;
		    if($verkn_tabid AND $verkn_fieldid){
                $verkn = set_verknpf($verkn_tabid,$verkn_fieldid,$verkn_ID,$verkn_add_ID,$verkn_del_ID,$verkn_showonly,$verknpf);
		    }
			form_gresult($ID,$gtabid,$form_id,$gresult,null,$verkn);
		}
	}elseif($ID == 0) {
        $gresult = get_default_values($gtabid);
    }
	
	$readonly = 0;
	# ----------- edit Permission -----------
	if($gtab["editrule"][$gtabid]){
		$readonly = check_GtabRules($ID,$gtabid,null,$gtab["editrule"][$gtabid],0,$gresult);
	}
	# --- specific user/grouprules  --------------------------------------
	if($gtab["has_userrules"][$gtabid] AND !$readonly AND !$gtab["edit_userrules"][$gtabid]){
		$readonly = !check_GtabUserRules($gtabid,$ID,$session["user_id"],"edit");
	}

	printContextMenus($gtabid,$form_id,$ID,$gresult,$readonly);
	form_view($gtabid,$ID,$gresult,$form_id,$readonly);
# --- zeige Standard-Ansicht -----
}else{
	if($ID){
		if($gtab["typ"][$gtabid] == 5){
			# need filter and search-params for using pointer
			$gresult = get_gresult($gtabid,1,$filter,$gsr,0,0,$ID);
		}else{
			$gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
		}
	}elseif($ID == 0) {
        $gresult = get_default_values($gtabid);
    }

    # ----------- multitenant permission  -----------
    if($umgvar['multitenant'] AND $gtab['multitenant'][$gtabid] AND $lmmultitenants['mid'][$session['mid']] != $gresult[$gtabid]['MID'][0] AND !$session["superadmin"]){
        $action = 'gtab_deterg';
    }
	
	$readonly = 0;
	# ----------- edit Permission -----------
	if($gtab["editrule"][$gtabid]){
		$readonly = check_GtabRules($ID,$gtabid,null,$gtab["editrule"][$gtabid],0,$gresult);
	}
	# --- specific user/grouprules  --------------------------------------
	if($gtab["has_userrules"][$gtabid] AND !$readonly AND !$gtab["edit_userrules"][$gtabid]){
		$readonly = !check_GtabUserRules($gtabid,$ID,$session["user_id"],"edit");
	}
	
	printContextMenus($gtabid,$form_id,$ID,$gresult,$readonly);
	defaultView($gtabid,$ID,$gresult,$readonly,$filter);
}

unset($commit);
?>

<span id="myExtForms"></span>

</form>