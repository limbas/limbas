<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$layout_bootstrap = true; // TODO: remove -> check layout/parts

# --- Zeitmessung ---------------------------------------------------
function is_time(){
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}
$zeit_main = is_time();

/* --- Include Libary ------------------------------------------------ */
require_once(__DIR__ . '/lib/session.lib');

require_once(COREPATH . 'lib/db/db_'.$DBA['DB']."_admin.lib");
require_once(COREPATH . 'lib/include_admin.lib');
require_once(COREPATH . 'lib/context.lib');
require_once(COREPATH . 'extra/snapshot/snapshot.lib');
require_once(COREPATH . 'extra/lmbObject/log/LimbasLogger.php');

// include extensions
if ($gLmbExt["ext_main_admin.inc"]) {
    foreach ($gLmbExt["ext_main_admin.inc"] as $key => $extfile) {
        require_once ($extfile);
    }
}

if (in_array($action, ['setup_form_main','setup_form_menu','setup_report_main','setup_report_menu'])) {
    $layout_bootstrap = false;
}

# body Layout
$bodyclass = "main";

/* --- Aktion :-------------------------------------------------------- */


/* ------------------ group ------------------------ */
if ($action == "setup_group_erg" AND $LINK[$action] == 1) {
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
	$require2 = "admin/tools/sysupdate/grusrref.php";
	$BODYHEADER = $lang[$LINK["desc"][50]];
}
elseif ($action == "setup_linkref" AND $LINK[$action] == 1) {
	$require1 = "admin/group/group.lib";
	$require2 = "admin/tools/sysupdate/linkref.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_tabtools" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/tabtools.dao";
	$require3 = "admin/tools/tabtools.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_php_editor" AND $LINK[$action] == 1) {
    $require1 = "admin/tools/php_editor.dao";
    $require3 = "admin/tools/php_editor.php";
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
elseif ($action == "setup_update" AND $LINK[$action] == 1) {
	$require1 = "admin/tools/update/html/update.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_currency" AND $LINK[$action] == 1) {
    $require1 = "admin/tools/currency.dao";
    $require2 = "admin/tools/currency.php";
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
elseif ($action == "setup_jobs_cron" AND $LINK[$action] == 1) {
    $require1 = "admin/tools/jobs_ext.lib";
    $require2 = "admin/tools/jobs_ext.dao";
    $require3 = "admin/tools/jobs_cron.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
/* ------------------ setup ------------------------ */
elseif ($action == "setup_umgvar" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/umgvar.dao";
	$require2 = "admin/setup/umgvar.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_custvar" AND $LINK[$action] == 1) {
    $require1 = "admin/setup/custvar.dao";
    $require2 = "admin/setup/custvar.php";
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
elseif ($action == "setup_menueditor" AND $LINK[$action] == 1) {
	$require1 = "admin/setup/menueditor.dao";
	$require2 = "admin/setup/menueditor.php";
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
elseif ($action == "setup_verkn_editor" AND $LINK[$action] == 1) {
	$require1 = "admin/tables/tab.lib";
	$require2 = "admin/tables/verkn_editor.dao";
    $require3 = "admin/tables/verkn_editor.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == "setup_grouping_editor" AND $LINK[$action] == 1) {
	$require1 = "admin/tables/grouping_editor.php";
	$BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == 'setup_external_storage' AND $LINK[$action] == 1) {
    $require1 = 'admin/setup/externalStorage.dao';
    $require2 = 'admin/setup/externalStorage.php';
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
}
elseif ($action == 'setup_multitenant' AND $LINK[$action] == 1) {
    $require1 = 'admin/setup/multitenant.dao';
    $require2 = 'admin/setup/multitenant.php';
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
}
elseif ($action == 'setup_printers' AND $LINK[$action] == 1) {
    $require1 = 'admin/setup/printers.dao';
    $require2 = 'admin/setup/printers.php';
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
}
elseif ($action == 'setup_keymanager' AND $LINK[$action] == 1) {
    $require1 = 'admin/setup/keyManager.dao';
    $require2 = 'admin/setup/keyManager.php';
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
}elseif ($action == "intro" or $action == "nav_info") {
    $require2 = "help/intro.php";
    $BODYHEADER = $lang[$LINK['desc'][$LINK_ID[$action]]];
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
    $require3 = "admin/report/report.lib";
	$require4 = "admin/report/report_frameset.php";
}
elseif ($action == "setup_report_main" AND $LINK["setup_report_select"] == 1) {
	$form_typ = 2;
	$require1 = "admin/report/report.dao";
	$require2 = "admin/report/report_main.php";
	$ONKEYDOWN = "OnKeydown=\"movex(event);\"";
	# TODO ! also in form
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

elseif ($action == "gtab_form" and $LINK[132] == 1) {
    require_once (COREPATH . 'gtab/gtab.lib');
    require_once (COREPATH . 'gtab/gtab_form.lib');
    require_once (COREPATH . 'gtab/gtab_type.lib');
    require_once (COREPATH . 'gtab/gtab_type_erg.lib');
    $require1 = "gtab/html/gtab_form.php";

    if ($HEADER) {
        $BODYHEADER = $HEADER;
    } else {
        $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    }
}

else{
	die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B></CENTER></BODY>");
}

if($BODY != 1){
?>

<?php require_once(Layout::getFilePath('main/head.php')); ?>

<script src="assets/js/lib/global.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/jquery/jquery.min.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/jquery-ui/jquery-ui.min.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/colresizeable/colResizable.min.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/jquery-ui/jquery.ui.datepicker-de.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/vendor/jquery-ui/jquery-ui-timepicker-addon.js?v=<?=$umgvar["version"]?>"></script>
<link rel="stylesheet" type="text/css" href="assets/vendor/jquery-ui/jquery-ui.min.css?v=<?=$umgvar["version"]?>">
<link rel="stylesheet" type="text/css" href="assets/vendor/jquery-ui/jquery-ui-timepicker-addon.css?v=<?=$umgvar["version"]?>">


    <?php

//load layout / choose between new and legacy based on action and template //TODO: remove legacy switch in future version
    if ($layout_bootstrap) { ?>
        <link rel="stylesheet" type="text/css" href="assets/css/<?=$session['css']?>?v=<?=$umgvar["version"]?>">
        <script type="text/javascript" src="assets/vendor/bootstrap/bootstrap.bundle.min.js?v=<?=$umgvar["version"]?>"></script>
    <?php } else { ?>
        <link rel="stylesheet" type="text/css" href="assets/css/<?=$session['legacyCss']?>?v=<?=$umgvar["version"]?>">
    <?php } ?>
        

    

<?php if($action == "setup_user_tracking" OR $action == "setup_user_change_admin" OR $action == "setup_stat_user_det"){?>
<?php }elseif($action == "setup_form_main"){?>
	<script type="text/javascript" src="assets/vendor/jsgraphics/jsgraphics.js?v=<?=$umgvar["version"]?>"></script>
	<script type="text/javascript" src="assets/js/admin/form/form_main.js?v=<?=$umgvar["version"]?>"></script>
<?php }elseif($action == "setup_report_main"){?>
	<script type="text/javascript" src="assets/vendor/jsgraphics/jsgraphics.js?v=<?=$umgvar["version"]?>"></script>
	<script type="text/javascript" src="assets/js/admin/report/report.js?v=<?=$umgvar["version"]?>"></script>
<?php }elseif($action == "setup_gtab_view" OR $action == "setup_tabschema"){?>
	<script type="text/javascript" src="assets/vendor/jsgraphics/jsgraphics.js?v=<?=$umgvar["version"]?>"></script>
	<script type="text/javascript" src="assets/js/admin/tables/tabschema.js?v=<?=$umgvar["version"]?>"></script>
<?php }elseif($action == "setup_color_schema"){?>
    <script type="text/javascript" src="assets/vendor/spectrum/spectrum.js?v=<?=$umgvar["version"]?>"></script>
    <link rel="stylesheet" type="text/css" href="assets/vendor/spectrum/spectrum.css?v=<?=$umgvar["version"]?>">
<?php }?>



<?php

    require_once(Layout::getFilePath('main/body.php'));
}

# EXTENSION
if($LINK["extension"][$LINK_ID[$action]] AND is_file($umgvar["pfad"].$LINK["extension"][$LINK_ID[$action]])){
	require($umgvar["pfad"].$LINK["extension"][$LINK_ID[$action]]);
}else{
	if ($require1) { require_once(COREPATH . $require1); }
	if ($require2) { require_once(COREPATH . $require2); }
	if ($require3) { require_once(COREPATH . $require3); }
	if ($require4) { require_once(COREPATH . $require4); }
	if ($require5) { require_once(COREPATH . $require5); }
}

/* -------------------------------------------------------------------- */

# ---- History Eintrag ---------
if(!$ltmp["history_action"] AND $session["logging"] == 2){
	history_action($gtabid,$ID,$LINK_ID[$action],2);
}

if($BODY != 1){
    require_once(Layout::getFilePath('main/foot.php'));
}

/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {
	lmbdb_close($db);
}


#$zeit_now = is_time();
#echo $zeit_now - $zeit_main;
?>
