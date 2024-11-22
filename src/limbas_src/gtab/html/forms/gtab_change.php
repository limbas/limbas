<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\form\Form;
use Limbas\admin\form\FormMode;

global $result_report;
global $filter_save;
global $gtabid;
global $form_id;
global $filter;
global $filter_groupheader;
global $filter_groupheaderKey;
global $filter_gwidth;
global $filter_tabulatorKey;
global $view_all_verkn;
global $verknpf;
global $verkn_tabid;
global $verkn_fieldid;
global $verkn_ID;
global $verkn_add_ID;
global $verkn_del_ID;
global $verkn_showonly;
global $scrollto;
global $ID;
global $gsr;
global $verkn;
global $db;
global $lang;
global $gformlist;
global $action;
global $session;
global $umgvar;
global $wfl_id;
global $wfl_inst;
global $gtab;
global $gfield;
global $gform;
global $gresult;
global $form_id;
global $readonly;
global $verkn_addfrom;
global $lmcurrency;

$result_report["count"] = 0;

$useExtensionForm = ($gformlist[$gtabid]["extension"][$form_id] AND file_exists(EXTENSIONSPATH.$gformlist[$gtabid]["extension"][$form_id]));
$useCustomForm = ($gform[$form_id] && $gformlist[$gtabid]['id'][$form_id] && $gformlist[$gtabid]['typ'][$form_id] == 1);

$form = null;
if($useCustomForm && !$useExtensionForm) {
    $form = Form::get($form_id);
}


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


//if($gtab["typ"][$i] == 5){$action = "gtab_deterg";}

?>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="lmbZoomContainer" class="ajax_container" style="position:absolute;display:none;font-size:18px;font-weight:bold" OnClick="activ_menu=1;"></div>

<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.min.js"></script>
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

// display custmenu
if ($gtab["custmenu"][$gtabid]): ?>
    <script>
        alert(<?=e($gtabid)?>;
        top.openMenu(<?=$gtab["custmenu"][$gtabid]?>);
    </script>
<?php endif; ?>


<?php


$action = $GLOBALS["action"];
if($GLOBALS["old_action"] == 'gtab_readonly'){$action = 'gtab_change';} # for scrolling after locked or versioned readonly dataset

if($useCustomForm):

// display custmenu
    if($cm = $gformlist[$gtabid]["custmenu"][$form_id] OR $cm = $gtab["custmenu"][$gtabid]){
        ?>
        <script>
            top.openMenu(<?=e($cm)?>);
        </script>
        <?php
    }

    // set custom page title
    if($gformlist[$gtabid]['header'][$form_id]){
        $title = eval('return ' . $gformlist[$gtabid]['header'][$form_id] . ";");
        if(is_array($title)){
            ?>
            <script>
                lmb_setPageTitle('<?=e($title['document'])?>','<?=e($title['header'])?>');
            </script>
            <?php
        } elseif($title){
            ?>
            <script>
                lmb_setPageTitle('<?=e($title)?>');
            </script>
            <?php
        }
    }

endif;

    if(!$useExtensionForm) {
        require(COREPATH . 'gtab/html/forms/parts/data.php');
    }

?>

<form action="main.php" method="post" name="form1" id="form1" enctype="multipart/form-data" autocomplete="off" <?=!empty($form) && $form->mode !== FormMode::NEW ? 'class="form-switch-new"' : ''?>>


<input type="hidden" name="empty">
<input type="hidden" name="ID" value="<?=$GLOBALS["ID"]?>">
<input type="hidden" name="action" value="<?=$action?>">
<input type="hidden" name="old_action" value="<?=$GLOBALS["action"]?>">
<input type="hidden" name="gtabid" value="<?=$GLOBALS["gtabid"]?>">
<input type="hidden" name="tab_group" value="<?=$GLOBALS["gtab"]["tab_group"][$GLOBALS["gtabid"]]?>">
<input type="hidden" name="change_field">
<input type="hidden" name="form_id" VALUE="<?=$GLOBALS["form_id"]?>">
<input type="hidden" name="formlist_id" value="<?=$GLOBALS["formlist_id"];?>">
<input type="hidden" name="snap_id" value="<?=$GLOBALS["snap_id"]?>">
<input type="hidden" name="wfl_id" value="<?=$GLOBALS["wfl_id"]?>">
<input type="hidden" name="wfl_inst" value="<?=$GLOBALS["wfl_inst"]?>">
<input type="hidden" name="set_form">
<input type="hidden" name="change_ok">
<input type="hidden" name="view_symbolbar">
<input type="hidden" name="view_all_verkn">
<input type="hidden" name="filter_reset">
<input type="hidden" name="filter_groupheader">
<input type="hidden" name="filter_groupheaderKey">
<input type="hidden" name="filter_tabulatorKey">
<input type="hidden" name="filter_save">
<input type="hidden" name="filter_gwidth">
<input type="hidden" name="filter_validity">
<input type="hidden" name="filter_status">
<input type="hidden" name="filter_force_delete">
<input type="hidden" name="versdesc">
<input type="hidden" name="lockingtime">
<input type="hidden" name="history_search">
<input type="hidden" name="request_flt" value="<?=$GLOBALS["request_flt"]?>">
<input type="hidden" name="deterg">
<input type="hidden" name="del_">
<input type="hidden" name="use_record">
<input type="hidden" name="use_typ">
<input type="hidden" name="history_fields">
<input type="hidden" name="verkn_key_field">
<input type="hidden" name="posy">
<input type="hidden" name="funcid">
<input type="hidden" name="gfrist" VALUE="<?=$GLOBALS["gfrist"]?>">
<input type="hidden" name="gfrist_desc">
<input type="hidden" name="scrollto">
<input type="hidden" name="verknpf" VALUE="<?=$GLOBALS["verknpf"]?>">
<input type="hidden" name="verkn_addfrom" VALUE="<?=$GLOBALS["verkn_addfrom"]?>">
<input type="hidden" name="verkn_poolid" VALUE="<?=$GLOBALS["verkn_poolid"]?>">
<input type="hidden" name="wind_force_close">
<input type="hidden" name="is_new_win" value="<?=$GLOBALS["is_new_win"]?>">


<?php if($GLOBALS["verknpf"]): ?>
    <input type="hidden" name="verkn_ID" VALUE="<?=$GLOBALS["verkn_ID"]?>">
    <input type="hidden" name="verkn_tabid" VALUE="<?=$GLOBALS["verkn_tabid"]?>">
    <input type="hidden" name="verkn_fieldid" VALUE="<?=$GLOBALS["verkn_fieldid"]?>">
    <input type="hidden" name="verkn_showonly" VALUE="<?=$GLOBALS["verkn_showonly"]?>">
    <input type="hidden" name="verkn_formid" VALUE="<?=$GLOBALS["verkn_formid"]?>">
<?php else: ?>
    <input type="hidden" name="verkn_ID">
    <input type="hidden" name="verkn_tabid">
    <input type="hidden" name="verkn_fieldid">
    <input type="hidden" name="verkn_showonly">
<?php endif; ?>

<?php

# --- zeige Formular-Ansicht EXTENSION -----
if($useExtensionForm){
	
    require_once(EXTENSIONSPATH.$gformlist[$gtabid]['extension'][$form_id]);
    
# --- zeige Formular-Ansicht -----
}else {
    
    if($useCustomForm){
        
        if(!empty($form) && $form->mode === FormMode::NEW) {
            echo '<style>' . $form->css . '</style>';
            echo $form->render();
        }
        else {
            form_view($gtabid,$ID,$gresult,$form_id,$readonly);
        }

# --- zeige Standard-Ansicht -----
    }else{


        /*$lmbForm = new LMBForm();

$lmbForm->assignData(intval($gtabid),intval($ID),$gresult);
$lmbForm->render(boolval($readonly));*/
        
        require(__DIR__ . '/default.php' );

    }
    
}

unset($commit);
?>

<span id="myExtForms"></span>

</form>
