<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\template\select\TemplateSelector;

# include extensions
if($GLOBALS["gLmbExt"]["ext_gtab_erg.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_gtab_erg.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

#if (!($gtab['theme'][$gtabid])) {
    require(COREPATH . 'gtab/html/table/contextmenus.php');
#}

?>

<?php if($gfrist){?>
<div ID="limbasDivMenuReminder" class="lmbContextMenu" style="display:none;z-index:995" OnClick="activ_menu = 1;">
<FORM NAME="reminder_form">
<?php #----------------- Reminder-Menü -------------------
pop_header(null,$lang[425],$elementWidth);
pop_left();
echo "<table>
<tr><td>".$lang[1445]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_from.value=this.value+' ';checktyp(11,'lmb_ReminderFilterFrom','','','',this.value)\" name=\"lmb_ReminderFilterFrom\" value=\"".$filter['reminder_from'][$gfrist]."\" MAX=16>
<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" onclick=\"lmb_datepicker(event,this,'lmb_ReminderFilterFrom',this.previousSibling.value,'".dateStringToDatepicker(setDateFormat(0,1))."',10);\"></i></td>
</tr>
<tr><td>".$lang[1446]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_to.value=this.value+' ';checktyp(11,'lmb_ReminderFilterTo','','','',this.value)\" name=\"lmb_ReminderFilterTo\" value=\"".$filter['reminder_to'][$gfrist]."\" MAX=16>
<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" onclick=\"lmb_datepicker(event,this,'lmb_ReminderFilterTo',this.previousSibling.value,'".dateStringToDatepicker(setDateFormat(0,1))."',10);\"></i></td>
</tr>
<tr><td>".$lang[1445]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_create.value=this.value+' ';checktyp(1,'lmb_ReminderFilterCreate','','','',this.value)\" name=\"lmb_ReminderFilterCreate\" value=\"".$filter['reminder_create'][$gfrist]."\" MAX=30></td></tr>
</table>
";
pop_right();
pop_submit($lang[30]);
pop_bottom();
?>
</FORM></div>
<?php }?>

<?php /*----------------- Javascript Functionen -------------------*/?>


<Script language="JavaScript">

// ----- Js-Script-Variablen --------
jsvar["gtabid"] = "<?=$gtabid?>";
jsvar["snap_id"] = "<?=$snap_id?>";
jsvar["user_id"] = "<?=$session["user_id"]?>";
jsvar["verkn_ID"] = "<?=$verkn_ID?>";
jsvar["form_id"] = "<?=$form_id?>";
jsvar["tablename"] = "<?=$gtab["desc"][$gtabid]?>";
jsvar["wfl_id"] = "<?=$wfl_id?>";
jsvar["wfl_inst"] = "<?=$wfl_inst?>";
jsvar["resultspace"] = "<?=$umgvar["resultspace"]?>";
jsvar["usetablebody"] = "<?=$umgvar["usetablebody"]?>";
jsvar["action"] = "<?=$action?>";
jsvar["detail_viewmode"] = "<?=$umgvar["detail_viewmode"]?>";
jsvar["ajaxpost"] = "<?=$gtab["ajaxpost"][$gtabid]?>";
jsvar["confirm_level"] = "<?=$umgvar["confirm_level"]?>";
jsvar["modal_size"] = "<?=$umgvar["modal_size"]?>";
jsvar["modal_opener"] = "<?=$umgvar["modal_opener"]?>";
</SCRIPT>

<?php

TemplateSelector::printTemplateSelectModal(intval($gtabid));

 ?>

	<?php /*-------------------------------------------- Formularkopf ----------------------------------------*/?>
	<form action="main.php" method="post" id="lmbForm2" name="form2" autocomplete="off">
	<input type="hidden" name="action">
	<input type="hidden" name="old_action" value="<?=$action?>">
	<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
	<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
	<input type="hidden" name="wfl_id" value="<?=$wfl_id;?>">
	<input type="hidden" name="wfl_inst" value="<?=$wfl_inst;?>">
	<input type="hidden" name="snap_id" value="<?=$snap_id;?>">
	<input type="hidden" name="request_gsr" value="<?=$request_gsr;?>">
	<input type="hidden" name="request_flt" value="<?=$request_flt;?>">
	<input type="hidden" name="gfrist" VALUE="<?=$gfrist;?>">
	<input type="hidden" name="formlist_id" value="<?=$form_id;?>">
	<input type="hidden" name="form_id">
	<input type="hidden" name="ID">
	<input type="hidden" name="use_record">
	<input type="hidden" name="posx">
	<input type="hidden" name="posy">
	<input type="hidden" name="funcid">
	<?php /* --------------------- Verknüpfungszusatz ------------------------ */?>
	<input type="hidden" name="verknpf" VALUE="<?= $verknpf ?>">
	<input type="hidden" name="verkn_ID" VALUE="<?= $verkn_ID ?>">
	<input type="hidden" name="verkn_add_ID" VALUE="<?= $verkn_add_ID ?>">
	<input type="hidden" name="verkn_del_ID" VALUE="<?= $verkn_del_ID ?>">
	<input type="hidden" name="verkn_tabid" VALUE="<?= $verkn_tabid ?>">
	<input type="hidden" name="verkn_fieldid" VALUE="<?= $verkn_fieldid ?>">
	<input type="hidden" name="verkn_showonly" VALUE="<?= $verkn_showonly ?>">
	<?php /*<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom?>">  todo  */?>
	<input type="hidden" name="verkn_poolid" VALUE="<?=$verkn_poolid;?>">
	<span id="myExtForms2"></span>
	</form>


	<form action="main.php" method="post" id="form1" name="form1" autocomplete="off">
	<input type="hidden" name="action" value="gtab_erg">
	<input type="hidden" name="old_action" value="<?=$action?>">
	<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
	<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
	<input type="hidden" name="order">
	<input type="hidden" name="select">
	<input type="hidden" name="exp_typ">
	<input type="hidden" name="exp_medium">
	<input type="hidden" name="ID">
	<input type="hidden" name="LID">
	<input type="hidden" name="posx">
	<input type="hidden" name="posy">
	<input type="hidden" name="rowsize">
	<input type="hidden" name="change">
	<input type="hidden" name="funcid">
	<input type="hidden" name="snapshot">
	<input type="hidden" name="gfrist" VALUE="<?=$gfrist;?>">
	<input type="hidden" name="form_id" value="<?=$form_id;?>">
	<input type="hidden" name="snap_id" value="<?=$snap_id;?>">
	<input type="hidden" name="wfl_id" value="<?=$wfl_id;?>">
	<input type="hidden" name="wfl_inst" value="<?=$wfl_inst;?>">
	<input type="hidden" name="limbaswfid">
	<input type="hidden" name="request_gsr" value="<?=$request_gsr;?>">
	<input type="hidden" name="request_flt" value="<?=$request_flt;?>">
	<input type="hidden" name="lockingtime">
	<input type="hidden" name="popup_links">
	<input type="hidden" name="view_symbolbar">
	<input type="hidden" name="view_version_status" value="<?=$view_version_status;?>">
	<input type="hidden" name="td_sort">
	<input type="hidden" name="td_size_global">
	<input type="hidden" name="use_record">
	<input type="hidden" name="use_typ">
	<input type="hidden" name="readonly" value="<?=$readonly;?>">
	
	<input type="hidden" name="filter_frame_type">
	<input type="hidden" name="filter_reset">
	<input type="hidden" name="filter_indicator">
	<input type="hidden" name="filter_popups">
	<input type="hidden" name="filter_alter">
	<input type="hidden" name="filter_status">
	<input type="hidden" name="filter_hidecols">
	<input type="hidden" name="filter_hidelocked">
	<input type="hidden" name="filter_locked">
	<input type="hidden" name="filter_nolimit">
	<input type="hidden" name="filter_version">
	<input type="hidden" name="filter_nosverkn">
	<input type="hidden" name="filter_userrules">
	<input type="hidden" name="filter_sum">
	<input type="hidden" name="filter_reminder_from">
	<input type="hidden" name="filter_reminder_to">
	<input type="hidden" name="filter_reminder_create">
	<input type="hidden" name="filter_force_delete">
    <input type="hidden" name="filter_tabulatorKey">
    <input type="hidden" name="filter_multitenant">
    <input type="hidden" name="filter_validity">
    <input type="hidden" name="filter_validity_all">
	<input type="hidden" name="pop_choice">
	<input type="hidden" name="grp_choice">
	
	<input type="hidden" name="history_fields">
	<input type="hidden" name="history_search">
	<input type="hidden" name="verknpf" VALUE="<?= $verknpf ?>">
	<input type="hidden" name="verkn_poolid" VALUE="<?=$verkn_poolid;?>">
	<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom;?>">
	<?php #--------------------- Verknüpfungszusatz ------------------------
	if($verknpf){?>
	<input type="hidden" name="verkn_ID" VALUE="<?= $verkn_ID ?>">
	<input type="hidden" name="verkn_add_ID" VALUE="<?= $verkn_add_ID ?>">
	<input type="hidden" name="verkn_del_ID" VALUE="<?= $verkn_del_ID ?>">
	<input type="hidden" name="verkn_tabid" VALUE="<?= $verkn_tabid ?>">
	<input type="hidden" name="verkn_fieldid" VALUE="<?= $verkn_fieldid ?>">
	<input type="hidden" name="verkn_showonly" VALUE="<?= $verkn_showonly ?>">
	<?php }



$still_done = array();
if (!$filter["anzahl"][$gtabid]){$filter["anzahl"][$gtabid] = $session["maxresult"];}

# Layout lib
require_once(COREPATH . 'gtab/gtab_erg.lib');

#---- Tabellen-funktionen aufrufen -------

# get tabsize
if(!$filter["hidecols"][$gtabid][0]){
	lmbGetGtabWidth($gtabid,$filter["hidecols"][$gtabid]);
}

# ----- Formular -------
if($form_id AND $gformlist[$gtabid]["id"][$form_id] AND $gformlist[$gtabid]["typ"][$form_id] == 2){

    // display custmenu
    if($cm = $gformlist[$gtabid]["custmenu"][$form_id]){
        echo "
        <script>
        top.openMenu({$cm});
        </script>
        ";
    }

	if($gformlist[$gtabid]["css"][$form_id]){
		echo"<style type=\"text/css\">@import url(".$gformlist[$gtabid]["css"][$form_id]."?v={$umgvar['version']});</style>\n";
	}
    echo '<div class="legacy-table">';
	form_ergview($gtabid,$gresult,$form_id);
    echo '</div>';
}else{
    if ($gtab['theme'][$gtabid]) {
        require(COREPATH . 'gtab/html/table/new/default.php');
    } else {

        // display custmenu
        if($cm = $gtab["custmenu"][$gtabid]){
            echo "
            <script>
            top.openMenu({$cm});
            </script>
            ";
        }

        # ----- Gruppiert -------
        if($popg[$gtabid][0]['null']){
            foreach($popg[$gtabid][0]['null'] as $key => $value){
                if($value and $gfield[$gtabid]["groupable"][$key]){$group_fields[] = $key;}
            }
            # --- Prüfe ob Feld ausgewählt oder auf "ohne" gesetzt
            if($group_fields){
                $gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn);
                lmbGlistOpen();
                lmbGlistTabs($gtabid,$verkn_poolid,$snap_id);
                lmbGlistMenu($gtabid);
                lmbGlistSearch($gtabid,0,0);
                lmbGlistHeader($gtabid,$gresult,0);
                table_group($gtabid,$group_fields,$filter,0,$gsr,$verkn);
                lmbGlistFooter($gtabid,$gresult,$filter);
                lmbGlistClose();
            }else{
                $usedef = 1;
                unset($popg[$gtabid]);
            }
        }else{
            $usedef = 1;
        }

        if($usedef){
            lmbGlistOpen();
            lmbGlistTabs($gtabid,$verkn_poolid,$snap_id);
            lmbGlistMenu($gtabid);

            # scrolling Header X-position
            echo "<tr><td><div id=\"GtabTableFull\" style=\"overflow-x:auto;overflow-y:hidden;\"><table cellpadding=\"0\" cellspacing=\"0\">";
            lmbGlistSearch($gtabid,$verknpf,$verkn);
            lmbGlistHeader($gtabid,$gresult,0);
            lmbGlistBody($gtabid,$gresult,0,$verkn,$verknpf,$filter,0,0,0,0,1);
            echo "</table></div></td></tr>";

            lmbGlistFooter($gtabid,$gresult,$filter);
            lmbGlistClose();
        }
	}
}


# Kopierten Datensatz darstellen
if($use_typ == 'copy' AND $use_record){
$rec = explode(";",$use_record);
$rec = explode("_",$rec[0]);
echo "
<Script language=\"JavaScript\">
view_copychange(".$rec[0].",".$rec[1].");
</Script>

</form>
";
}
