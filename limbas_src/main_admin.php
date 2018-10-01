<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5  
 */

/*
 * ID: 3
 */

# --- Zeitmessung ---------------------------------------------------
function is_time(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
$zeit_main = is_time();

/* --- Include Libary ------------------------------------------------ */
require_once("inc/include_db.lib");
require_once("lib/db/db_".$DBA["DB"]."_admin.lib");
require_once("lib/include.lib");
require_once("lib/include_admin.lib");
require_once("lib/session.lib");
require_once("lib/context.lib");
require_once("extra/snapshot/snapshot.lib");

#$STYLE = "style=\"border-left:1px inset black;border-top:1px inset black;\"";

// EXTENSIONS add
if ($gLmbExt["ext_main_admin.inc"]) {
    foreach ($gLmbExt["ext_main_admin.inc"] as $key => $extfile) {
        require_once ($extfile);
    }
}

# body Layout
$bodyclass = "main";

/* --- Aktion :-------------------------------------------------------- */
if ($action == "nav_admin") {
	$require1 = "layout/".$session["layout"]."/nav_admin.php";

	#$BGCOLOR = "BGCOLOR=\"".$farbschema[WEB8]."\"";
	$ONLOAD = "onload='this.link_0.click();'";
}

/* ------------------ group ------------------------ */
elseif ($action == "setup_group_erg" AND $LINK[$action] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/user/user_del.dao";
	$require3 = "admin/group/group.lib";
	$require4 = "admin/group/group_erg.dao";
	$require5 = "admin/group/group_erg.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_nutzrechte" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_nutzrechte.dao";
	$require3 = "admin/group/group_nutzrechte.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_reportrechte" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_reportrechte.dao";
	$require3 = "admin/group/group_reportrechte.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_forms" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_reportrechte.dao";
	$require3 = "admin/group/group_forms.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_diags" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_reportrechte.dao";
	$require3 = "admin/group/group_diags.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_dateirechte" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_dateirechte.dao";
	$require3 = "admin/group/group_dateirechte.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_tab" AND $LINK[$action] == 1) {
	$require2 = "admin/group/group_tab.dao";
	$require4 = "admin/group/group_tab.php";
	$ONCLICK = "OnClick='body_click();'";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_reminder" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_reportrechte.dao";
	$require3 = "admin/group/group_reminder.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_workfl" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group_reportrechte.dao";
	$require3 = "admin/group/group_workflow.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_neu" AND $LINK[$action] == 1) {
	$require2 = "admin/group/group_neu.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_add" AND $LINK[138] == 1) {
	$userview = "all";
	$require1 = "admin/group/group.lib";
	$require2 = "admin/group/group_erg.dao";
	$require3 = "admin/group/group_erg.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ user ------------------------ */
elseif ($action == "setup_user") {
	$require1 = "admin/user/user_frameset.php";
}
elseif ($action == "setup_user_tree") {
	$require1 = "admin/user/user_tree.dao";
	$require2 = "admin/user/user_tree.php";
	$BODYHEADER = 0;
}
elseif ($action == "setup_user_erg") {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/user/user_del.dao";
	$require3 = "admin/user/user_erg.dao";
	$require4 = "admin/user/user_erg.php";
	$action = "setup_user";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_user_find" AND $LINK[$action] == 1) {
	$require1 = "admin/user/user_find.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_user_change_admin" OR $action == "setup_user_neu" AND $LINK[$action] == 1) {
	$require1 = "admin/user/user_change_admin.lib";
	if($action != "setup_user_neu"){$require2 = "admin/user/user_change_admin.dao";}
	$require3 = "admin/user/user_change_admin.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_user_tracking" AND $LINK[$action] == 1) {
	$require1 = "admin/user/user_tracking.php";
	$ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
	if($typ == 3){$ONLOAD="OnLoad=\"inusetime();\"";}
	$BODYHEADER = $userdat['username'][$userid].": ".$userdat['bezeichnung'][$userid]." (".$userdat['groupname'][$userid].")";
}
elseif ($action == "setup_user_overview" AND $LINK[$action] == 1) {
	$require1 = "admin/user/user_overview.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ tools ------------------------ */
elseif ($action == "setup_sysupdate" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/language.lib";
	$require2 = "admin/tools/sysupdate.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_grusrref" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group.lib";
	$require2 = "admin/tools/grusrref.php";
	$BODYHEADER = $lang[$LINK["desc"][50]];
}
elseif ($action == "setup_linkref" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group.lib";
	$require2 = "admin/tools/linkref.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_tabtools" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/tabtools.dao";
	$require3 = "admin/tools/tabtools.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_export" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/export.lib";
	$require2 = "admin/tools/export.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_import" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/language.lib";
	$require2 = "admin/tools/import.dao";
	$require3 = "admin/tools/imports.php";
	#$require4 = "admin/tools/import.lib";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_error_msg" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/error_msg.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_revisioner" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/revisioner.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_backup_hist" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/backup.dao";
	$require2 = "admin/tools/backup_hist.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_backup_cron" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/backup.dao";
	$require2 = "admin/tools/backup_cron.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_backup_man" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/backup.dao";
	$require2 = "admin/tools/backup_man.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_sysarray" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/sysarray.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_snap" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/snapshot.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	$ONCLICK = "OnClick=\"divclose()\"";
}
elseif ($action == "setup_trigger" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/trigger.dao";
	$require2 = "admin/tools/trigger.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_datasync" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/datasync.dao";
	$require2 = "admin/tools/datasync.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
# AND $LINK[$action] == 1
elseif ($action == "setup_update") {
	$require1 = "admin/tools/update.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/*-------------- intern jobs -----------------------*/
elseif ($action == "setup_indize_db" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/jobs_ext.lib";
	$require2 = "admin/tools/jobs_ext.dao";
	$require3 = "admin/tools/jobs_ext.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_indize_hist" AND $LINK[$action] == 1) {
	$require1 = "extra/explorer/metadata.lib";
	$require2 = "admin/tools/jobs_ext.lib";
	$require3 = "admin/tools/indize_hist.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_index" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/index.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
/* ------------------ setup ------------------------ */
elseif ($action == "setup_umgvar" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/umgvar.dao";
	$require2 = "admin/setup/umgvar.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_ftype" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/language.lib";
	$require2 = "admin/setup/fieldtype.dao";
	$require3 = "admin/setup/fieldtype.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_color" AND $LINK[$action] == 1) {
	$require1 = "user/sql/user_color.dao";
	$require2 = "user/html/user_color.php";
	$ONLOAD = "OnLoad=\"top.main.focus();create_list();\"";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_color_schema" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/color_schema.dao";
	$require2 = "admin/setup/color_schema.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_links" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/links.dao";
	$require2 = "admin/setup/links.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_language" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/language.lib";
	$require2 = "admin/setup/language.dao";
	$require3 = "admin/setup/language.php";
        if($language_typ == 2) {
            $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action . '_local']]];
        } else {
            $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
        }
}
elseif ($action == "setup_fonts" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/fonts.dao";
	$require2 = "admin/setup/fonts.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_group_trigger" AND $LINK[$action] == 1) {
	$require1 = "admin/group/trigger.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_gtab_ftype" AND $LINK[$action] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/tables/tab.lib";
	$require3 = "admin/setup/language.lib";
	$require4 = "admin/tables/gtab_ftype.dao";
	$require5 = "admin/tables/gtab_ftype.php";
	$ONCLICK = "OnClick=\"body_click()\"";
	if($LINK[215]){
		$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	}
}
elseif ($action == "setup_gtab_ftype_detail" AND $LINK[156] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/tables/tab.lib";
	$require3 = "admin/setup/language.lib";
	$require4 = "admin/tables/gtab_ftype.dao";
	$require5 = "admin/tables/gtab_ftype_detail.php";
	if($LINK[215]){
		$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	}
}
elseif ($action == "setup_tab" AND $LINK[$action] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/tables/tab.lib";
	$require3 = "admin/setup/language.lib";
	$require4 = "admin/tables/tab.dao";
	$require5 = "admin/tables/tab.php";
	$ONCLICK = "OnClick=\"body_click()\"";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	if($LINK[215]){
		$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	}
}
elseif ($action == "setup_tabschema" AND $LINK[$action] == 1) {
	$require2 = "admin/tables/tabschema.dao";
	$require3 = "admin/tables/tabschema.php";
	$ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
	$ONLOAD = "OnLoad=\"paint_lines();\"";
	#$ONMOUSEUP = "OnMouseUp=\"paint_lines();\"";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_tabschema_dyn" AND $LINK['setup_gtab_ftype'] == 1) {
	$require1 = "admin/tables/tabschema_dyn.php";
	$BODY = 1;
}
elseif ($action == "setup_fieldselect" AND $LINK['setup_gtab_ftype'] == 1) {
	$require2 = "admin/tables/fieldselect.dao";
	$require3 = "admin/tables/fieldselect.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_mimetypes" AND $LINK[$action] == 1) {
	$require2 = "admin/setup/mimetypes.dao";
	$require3 = "admin/setup/mimetypes.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_genlink" AND $LINK['setup_gtab_ftype'] == 1) {
	$require2 = "admin/tables/genlink.dao";
	$require3 = "admin/tables/genlink.php";
	$ONUNLOAD = "onUnload=opener.location.href='main_admin.php?action=setup_$typ&tab_group=$tab_group&atid=$atid'";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_argument" AND $LINK['setup_gtab_ftype'] == 1) {
	$require3 = "admin/tables/argument.dao";
	$require4 = "admin/tables/argument.php";
	#$ONUNLOAD = "onUnload=opener.location.href='main_admin.php?action=setup_$typ&tab_group=$tab_group&atid=$atid'";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_verknfield" AND $LINK['setup_gtab_ftype'] == 1) {
	$require3 = "admin/tables/verknfield.dao";
	$require4 = "admin/tables/verknfield.php";
	$ONUNLOAD = "onUnload=opener.location.href='main_admin.php?action=setup_$typ&tab_group=$tab_group&atid=$tabid'";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_verkn_editor" AND $LINK[$action] == 1) {
	$require1 = "admin/tables/verkn_editor.dao";
	$require2 = "admin/tables/verkn_editor.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_upload_editor" AND $LINK[$action] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/tables/upload_editor.dao";
	$require3 = "admin/tables/upload_editor.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_grouping_editor" AND $LINK[$action] == 1) {
	$require1 = "admin/tables/grouping_editor.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == 'setup_printers' AND $LINK[$action] == 1) {
    $require1 = 'admin/setup/printers.dao';
    $require2 = 'admin/setup/printers.php';
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
}

/* ------------------ statistic ------------------------ */
elseif ($action == "setup_stat_user_det" AND $LINK['setup_stat_user_dia'] == 1) {
	$require1 = "admin/statistic/stat_user_det.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_stat_user_deterg" AND $LINK['setup_stat_user_dia'] == 1) {
	$require1 = "admin/statistic/stat_user_deterg.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_stat_user_dia" AND $LINK[$action] == 1) {
	$require1 = "extra/diagramm.php";
	#$require1 = "admin/statistic/stat_user_dia";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ form ------------------------ */
elseif ($action == "setup_form_frameset" AND $LINK["setup_form"] == 1) {
	$require1 = "admin/form/form_frameset.php";
}
elseif ($action == "setup_form_main" AND $LINK["setup_form"] == 1) {
	#$form_typ = 1;
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "admin/form/form.dao";
	$require3 = "admin/form/form_main.php";
	$ONKEYDOWN = "OnKeydown=\"movex(event);\"";
	$ONLOAD = "OnLoad=\"window.setTimeout('create_list()',500)\"";
	#$ONCLICK = "OnClick=\"formBodyClick(event)\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID["setup_form"]]];
}
elseif ($action == "setup_form_menu" AND $LINK["setup_form"] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "extra/explorer/metadata.lib";
	$require3 = "admin/form/form.dao";
	$require4 = "admin/form/form_menu.php";
}
elseif ($action == "setup_form_select" AND $LINK["setup_form"] == 1) {
	$require1 = "admin/form/form_select.php";
	$action = "setup_form";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ report ------------------------ */
elseif ($action == "setup_report_frameset" AND $LINK["setup_report_select"] == 1) {
	$require1 = "admin/tools/add_filestruct.lib";
	$require2 = "extra/explorer/filestructure.lib";
	$require3 = "admin/report/report_frameset.php";
}
elseif ($action == "setup_report_main" AND $LINK["setup_report_select"] == 1) {
	$form_typ = 2;
	$require1 = "admin/report/report.dao";
	$require2 = "admin/report/report_main.php";
	$ONKEYDOWN = "OnKeydown=\"movex(event);\"";
	$ONLOAD = "OnLoad=\"window.setTimeout('create_list()',500)\"";
	#$ONCLICK = "OnClick=\"reportBodyClick('menu')\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID["setup_report_select"]]];
}
elseif ($action == "setup_report_menu" AND $LINK["setup_report_select"] == 1) {
	$require1 = "extra/explorer/filestructure.lib";
	$require2 = "extra/explorer/metadata.lib";
	$require3 = "admin/report/report.dao";
	$require4 = "admin/report/report_menu.php";
	$ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
}
elseif ($action == "setup_report_select" AND $LINK["setup_report_select"] == 1) {
	$require1 = "admin/tools/add_filestruct.lib";
	$require2 = "extra/explorer/filestructure.lib";
	$require3 = "admin/report/report_select.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ workflow ------------------------ */
elseif ($action == "setup_workflow" AND $LINK['setup_form'] == 1) {
	$require1 = "admin/workflow/workflow.dao";
	$require2 = "admin/workflow/workflow.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID["setup_workflow"]]];
}

elseif ($action == "setup_reminder" AND $LINK['setup_reminder'] == 1) {
	$require1 = "admin/workflow/reminder.dao";
	$require2 = "admin/workflow/reminder.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID["setup_reminder"]]];
}

/* ------------------ tabletree ------------------------ */
elseif ($action == "setup_tabletree" AND $LINK[$action] == 1) {
	$require1 = "admin/tabletrees/tabletree.lib";
	$require2 = "admin/tabletrees/tabletree.php";
	$ONCLICK = "OnClick=\"body_click();\"";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ diagram ------------------------ */
elseif ($action == "setup_diag" AND $LINK[$action] == 1) {
	$require1 = "admin/diagram/diag_list.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

/* ------------------ view ------------------------ */
elseif ($action == "setup_gtab_view" AND $LINK[$action] == 1) {
	$require1 = "admin/tables/view.dao";
	$require2 = "admin/tables/view.php";
	
	$ONCLICK = "OnClick=\"body_click();\"";
	$ONLOAD = "OnLoad=\"paint_lines();\"";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	$ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
}

/* ------------------ extensioneditor ------------------------ */
elseif ($action == "setup_exteditor" AND $LINK[$action] == 1) {
	if($fpath){
		$require1 = "admin/tools/extension_editor.php";
		$BODY = 1;
	}else{
		$require1 = "admin/tools/extension_menu.php";
		$ONCLICK = "OnClick=\"body_click()\"";
                $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
	}
}

elseif ($action == "game") {
	$require1 = "extra/game.php";
	$BODYHEADER = 'Limbas Pinguin Snake';
}

else{
	die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B></CENTER></BODY>");
}

if($BODY != 1){
?>

<html <?= $STYLE ?>>

<head>
<meta NAME="Title" CONTENT="Limbas Enterprise Unifying Framework V <?=$umgvar['version']?>">
<meta NAME="author" content="LIMBAS GmbH">
<meta NAME="Publisher" CONTENT="LIMBAS GmbH">
<meta NAME="Copyright" CONTENT="LIMBAS GmbH">
<meta NAME="description" content="Enterprise Unifying Framework">
<meta NAME="version" content="<?= $umgvar['version'] ?>">
<meta NAME="date" content="2017-10-23">
<meta HTTP-EQUIV="content-language" content="de">
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=<?= $umgvar['charset'] ?>">
<meta HTTP-EQUIV="Pragma" content="no-cache">
<meta HTTP-EQUIV="Cache-Control" content="no-cache, no-store, post-check=0, pre-check=0, must-revalidate">
<meta HTTP-EQUIV="Expires" content="0">
<meta http-equiv="Content-Style-Type" content="text/css">
<title>Limbas Enterprise Unifying Framework V <?=$umgvar['version']?></title>

<script type="text/javascript" src="lib/global.js"></script>
<script type='text/javascript' src='extern/jquery/jquery-1.7.1.min.js'></script>
<script type='text/javascript' src='extern/jquery/jquery-ui-1.9.2.min.js'></script>
<script type='text/javascript' src='extern/jquery/colResizable-1.3.min.js'></script>
<script type='text/javascript' src='extern/jquery/jquery.ui.datepicker-de.js'></script>
<script type='text/javascript' src='extern/jquery/jquery-ui-timepicker-addon.js'></script>

<style type='text/css'>@import url(extern/jquery/theme/jquery-ui-1.9.2.custom.css);</style>
<style type='text/css'>@import url(extern/jquery/theme/jquery-ui-timepicker-addon.css);</style>
<style type='text/css'>@import url(extern/jquery/colResizable.css);</style>
<style type="text/css">@import url(USER/<?=$session['user_id']?>/layout.css);</style>

<?php if($action == "setup_user_tracking" OR $action == "setup_user_change_admin" OR $action == "setup_stat_user_det"){?>
<?php }elseif($action == "setup_form_main"){?>
	<script type="text/javascript" src="extern/jsgraphics/jsgraphics.js"></script>
	<script type="text/javascript" src="admin/form/form_main.js"></script>
<?php }elseif($action == "setup_report_main"){?>
	<script type="text/javascript" src="extern/jsgraphics/jsgraphics.js"></script>
	<script type="text/javascript" src="admin/report/report.js"></script>
<?php }elseif($action == "setup_gtab_view" OR $action == "setup_tabschema"){?>
	<script type="text/javascript" src="extern/jsgraphics/jsgraphics.js"></script>
	<script type="text/javascript" src="admin/tables/tabschema.js"></script>
<?php }?>

<script language="JavaScript">browserType();</script>
</head>


<?php

# Page - Header
	echo "<BODY CLASS=\"$bodyclass\" TOPMARGIN=\"0\" Marginheight=\"0\" Marginwidth=\"0\" BORDER=\"0\" LINK=\"$ALINK\" VLINK=\"$VLINK\" LINK=\"$NLINK\" TEXT=\"$TEXT1\" $ONLOAD $ONUNLOAD $ONKEYDOWN $ONCLICK $ONBLUR $ONMOUSEDOWN $ONMOUSEUP >";
	if($BODYHEADER){
		if(file_exists("layout/".$session["layout"]."/main_header.php")){require_once("layout/".$session["layout"]."/main_header.php");}
		echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;height:100%\"><tr><td valign=\"top\" class=\"lmbfringeFrameMain\" id=\"lmbfringeFrameMain\">";
	}
}

# EXTENSION
if($LINK["extension"][$LINK_ID[$action]] AND is_file($umgvar["pfad"].$LINK["extension"][$LINK_ID[$action]])){
	require($umgvar["pfad"].$LINK["extension"][$LINK_ID[$action]]);
}else{
	if ($require1) { require_once($require1); }
	if ($require2) { require_once($require2); }
	if ($require3) { require_once($require3); }
	if ($require4) { require_once($require4); }
	if ($require5) { require_once($require5); }
}

/* -------------------------------------------------------------------- */

# ---- History Eintrag ---------
if(!$ltmp["history_action"] AND $session["logging"] == 2){
	history_action($gtabid,$ID,$LINK_ID[$action],2);
}

if($BODY != 1){
?>
	</BODY>
	<script language="JavaScript">
	window.defaultStatus = 'Limbas Enterprise Unifying Framework V <?=$umgvar["version"]?>';
	window.onerror = null;
	<?php
	# --- Fehlermeldungen -----
	if(is_array($alert)){
		if($alert AND !is_array($alert)){$alert = array($alert);}
		array_unique($alert);
		#echo "lmb_alert('".implode("\\n",$alert)."');\n";
		echo "showAlert('".implode('\n',$alert)."');\n";
	}

	echo "</script>\n";
	if($BODYHEADER){
	echo "</td></tr></table>";
	}
	
	echo "\n</html>\n";

}

/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {
	odbc_close($db);
}


#$zeit_now = is_time();
#echo $zeit_now - $zeit_main;
?>