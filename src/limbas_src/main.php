<?php

use Limbas\extra\report\Report;

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



// echo "<pre>";
// print_r($_REQUEST);

// --- Zeitmessung ---------------------------------------------------
function is_firsttime()
{
    list ($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

$zeit_main = is_firsttime();

$DBA["DBCUSER"] = 0;
$DBA["DBCPASS"] = 0;
$DBA["DBCNAME"] = 0;
unset($confirm_fileversion);
unset($lhist);

/* -------------------------------------------------------------- */
unset($require1);
unset($require2);
unset($require3);
unset($require4);
unset($require5);
unset($require6);
unset($require7);
unset($require8);
unset($require9);
unset($require10);
unset($require11);

/* --- Include Libary ------------------------------------------------ */
require_once (__DIR__ . '/lib/session.lib');
require_once (COREPATH . 'lib/context.lib');

global $layout_bootstrap;
$layout_bootstrap = false;

if ($action == 'null') {
    $action = null;
}

// include extensions
if ($gLmbExt["ext_main.inc"]) {
    foreach ($gLmbExt["ext_main.inc"] as $key => $extfile) {
        require_once ($extfile);
    }
}

// Temporäre Suchkriterien inizialisieren
if ($rgsr) {
    $gsr = $rgsr;
}

//load layout / choose between new and legacy based on action and template //TODO: remove legacy switch in future version

if (in_array($action,['user_color','report_preview','intro','nav_info','quickstart','maintenance','intro','edit_long','user_printer_cache','user_change', 'user_snapshot', 'user_w_vorlage', 'user_lock','user_mails','mail_preview'])) {
    $layout_bootstrap = true;
}

/* -------------------------------------------------------------- */
if (lmb_substr($action, 0, 4) == "gtab") {
    if (! $gtab["tab_id"][$gtabid] and $action != 'gtab_form') {
        if ($db) {
            lmbdb_close($db);
        }
        die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B></CENTER></BODY>");
    } elseif ($gtab["typ"][$gtabid] != 5 and lmb_count($gfield[$gtabid]["field_id"]) <= 0 and $action != 'gtab_form') {
        if ($db) {
            lmbdb_close($db);
        }
        die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B><BR><BR><B>&nbsp;$lang[118]</CENTER></BODY>");
    }

    require_once (COREPATH . 'extra/snapshot/snapshot.lib');
    require_once (COREPATH . 'gtab/gtab.lib');
    require_once (COREPATH . 'gtab/gtab_form.lib');
    require_once (COREPATH . 'gtab/gtab_type.lib');
    require_once (COREPATH . 'gtab/gtab_type_erg.lib');
    
    if ($gformlist[$gtabid]["typ"][$gtab["tab_view_form"][$gtabid]]) {
        if (! $form_id and $form_id != '0' and $gtab["tab_view_form"][$gtabid]) {
            $form_id = $gtab["tab_view_form"][$gtabid];
        }
        if ($form_id) {
            if ($action == "gtab_erg" and $gformlist[$gtabid]["typ"][$form_id] == 1) {
                $form_id = null;
            }
        }
    }


    if ($verknpf) {
        $verkn = set_verknpf($verkn_tabid, $verkn_fieldid, $verkn_ID, 0, 0, $verkn_showonly, $verknpf);
        // Rückwertige Verknüpfung
        /*
        if ($verkn["verknpf"] == 2 and $verkn and $ID!=0 and $action != 'gtab_neu') {
            $ID = r_verknpf($gtabid, $verkn);
        // Pool Verknüpfung
        } elseif (!$ID and $ID!=0 and $verkn and ($action == 'gtab_change' or $action == 'gtab_deterg')) {
            $ID = p_verknpf($gtabid, $verkn);
        }
        */
    }

    $require1 = null;
}

if ($action == "gtab_change" and $LINK[$action] != 1) {
    $action = "gtab_deterg";
}

// body Layout
if (! $bodyclass) {
    $bodyclass = "main";
}

/* --- Aktion :-------------------------------------------------------- */
if ($action == 'sess_refresh') {
    die('1');
}
elseif ($action == 'nav_info') {
    $require2 = 'extra/dashboard/html/intro.php';
    $GLOBALS['ltmp']['history_action'] = 1;
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action == 'intro') {
    $require2 = 'extra/dashboard/html/dashboard.php';
    $GLOBALS['ltmp']["history_action"] = 1;
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}
elseif ($action === 'maintenance') {
    header('HTTP/1.1 503 Service Temporarily Unavailable', true, 503);
    header('Retry-After: 120');
    $require1 = 'admin/tools/update/html/maintenance.php';

} elseif ($action == "gtab_erg" and $LINK[$action] == 1) {
    // is view
    if ($gtab["typ"][$gtabid] == 5 and ! $gfield[$gtabid]["id"]) {
        $layout_bootstrap = true;
        $require2 = "gtab/html/view/gtab_view.php";
    } else {
        $checkmod = 2;
        $require2 = "gtab/gtab_register.lib";
        $require3 = "extra/explorer/filestructure.lib";
        // Gtab Lösch/Kopier-Routine
        if ($use_record) {
            $require4 = "gtab/sql/gtab_use.dao";
        }
        $require5 = "gtab/sql/gtab_erg.dao";
        $require6 = "gtab/html/table/gtab_erg.php";
        
        if ($gtab['theme'][$gtabid]) {
            $layout_bootstrap = true;
        }
        
        // check if form is a list form and disable bootstrap
        if($form_id AND $gformlist[$gtabid]["id"][$form_id] AND $gformlist[$gtabid]["typ"][$form_id] == 2) {
            $layout_bootstrap = false;
        }
        
        $ONLOAD = "gtabSetTablePosition('$posx','$posy')";
        $ONKEYDOWN = "OnKeydown=\"return sendkeydown(event);\"";
        $ONCLICK = "OnClick=\"body_click();\"";
    }

    if ($HEADER) {
        $BODYHEADER = $HEADER;
    } else {
        $BODYHEADER = $tabgroup["name"][$gtab["tab_group"][$gtabid]];
    }

} 

elseif ($action == "gtab_exp" and $LINK[$action] == 1) {
    $require2 = "gtab/gtab_register.lib";
    $require4 = 'gtab/export/gtab_erg_exp.php';
    $BODY = 1;
}

elseif ($action == "gtab_deterg" and $LINK[$action] == 1) {
    $userview = "all";
    $require2 = "extra/explorer/filestructure.lib";
    // $require3 = "gtab/gtab_type.lib";
    $require4 = "gtab/gtab_register.lib";
    // Gtab Lösch-Routine
    if ($use_record and $ID) {
        $require4 = "gtab/sql/gtab_use.dao";
    }
    $require5 = "gtab/sql/gtab_change.dao";
    $require6 = "gtab/html/gtab_change.php";

    if($set_form OR $set_form == "0"){
        $form_id = $set_form;
    }
    if ( ($gtab['theme'][$gtabid] && empty($form_id)) || isset($gformlist[$gtabid]['theme'][$form_id])) {
        $layout_bootstrap = true;
    }
    
    $ONCLICK = "OnClick=\"body_click();\"";
    $ONLOAD = "inusetime('$gtabid','$ID');gtabSetTablePosition()";
    if ($uform) {
        $BODYHEADER = $HEADER;
    } elseif ($form_id != 'null') {
        $BODYHEADER = $tabgroup["name"][$gtab["tab_group"][$gtabid]];
    }
} 

elseif ($action == "gtab_change" and $LINK[$action] == 1) {
    $checkmod = 3;
    $userview = "all";
    $require2 = "extra/explorer/filestructure.lib";
    // $require3 = "gtab/gtab_type.lib";
    $require4 = "gtab/gtab_register.lib";
    // Gtab Lösch-Routine
    if (($use_record or $change_ok) and isset($ID)) {
        $require5 = "gtab/sql/gtab_use.dao";
    }
    $require6 = "gtab/sql/gtab_change.dao";
    $require7 = "gtab/html/gtab_change.php";

    if($set_form OR $set_form == "0"){
        $form_id = $set_form;
    }
    if ( ($gtab['theme'][$gtabid] && empty($form_id)) || isset($gformlist[$gtabid]['theme'][$form_id])) {
        $layout_bootstrap = true;
    }
    
    $ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
    $ONCLICK = "OnClick=\"body_click();\"";
    $ONLOAD = "inusetime('$gtabid','$ID');gtabSetTablePosition('','$posy')";
    $ONWINDOWUNLOAD = "function() { return check_change(); }";
    if ($uform) {
        $BODYHEADER = $HEADER;
    } elseif ($form_id != 'null') {
        $BODYHEADER = $tabgroup["name"][$gtab["tab_group"][$gtabid]];
    }
} elseif ($action == "gtab_neu" and $LINK[$action] == 1) {
    $checkmod = 3;
    $userview = "all";
    // $require2 = "gtab/gtab_type.lib";
    $require3 = "gtab/gtab_register.lib";
    $require4 = "gtab/sql/gtab_use.dao";
    $require5 = "gtab/sql/gtab_change.dao";
    $require7 = "gtab/html/gtab_change.php";

    if($set_form OR $set_form == "0"){
        $form_id = $set_form;
    }
    if ( ($gtab['theme'][$gtabid] && empty($form_id)) || isset($gformlist[$gtabid]['theme'][$form_id])) {
        $layout_bootstrap = true;
    }
    
    $ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
    $ONCLICK = "OnClick=\"body_click();\"";
    if ($gtab["reserveid"][$gtabid]) {
        $ONLOAD .= "inusetime('$gtabid','$ID');";
    }
    $ONLOAD .= "gtabSetTablePosition();";
    if (! $gtab["reserveid"][$gtabid]) {
        $ONWINDOWUNLOAD .= "function() { return check_new(); }";
    }
    if ($uform) {
        $BODYHEADER = $HEADER;
    } elseif ($form_id != 'null') {
        $BODYHEADER = $tabgroup["name"][$gtab["tab_group"][$gtabid]];
    }
} elseif ($action == "gtab_change_col" and $LINK[$action] == 1) {
    $userview = "all";
    $require2 = "gtab/html/gtab_change_col.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $ONLOAD = "window.focus();";
}

elseif ($action == "gtab_form" and $LINK[132] == 1) {
    $require2 = "gtab/html/gtab_form.php";
    if ($HEADER) {
        $BODYHEADER = $HEADER;
    } else {
        $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    }
}

elseif ($action == "add_select" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "gtab/sql/add_select.dao";
    $require3 = "gtab/html/contextmenus/add_select.php";
    $ONLOAD = "window.focus();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} 

elseif ($action == "edit_long" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "gtab/html/edit_long.php";
} 

elseif ($action == "user_change" and $LINK[$action] == 1) {
    $require1 = "user/sql/user_change.dao";
    $require2 = "user/html/user_change.php";
    $ONLOAD = "";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "user_w_vorlage" and $LINK[$action] == 1) {
    $require1 = "user/sql/user_w_vorlage.dao";
    $require2 = "user/html/user_w_vorlage.php";
    $ONLOAD = "";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "user_color" and $LINK[$action] == 1) {
    $require1 = "user/sql/user_color.dao";
    $require2 = "user/html/user_color.php";
    $ONLOAD = "create_list();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "user_snapshot" and $LINK[$action] == 1) {
    $require1 = "extra/snapshot/snapshot.lib";
    $require2 = "user/html/user_snapshot.php";
    $ONLOAD = "";
    $ONCLICK = "OnClick=\"divclose();\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}// elseif (($action == "my_workflow" OR $action == "use_workflow") AND $LINK[$action] == 1) {
// $require1 = "user/html/workflow/USER_workflow.dao";
// $require2 = "user/html/workflow/user_workflow.php";
// $ONLOAD = "top.main.focus();";
// $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
// }
elseif ($action == "user_lock" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "user/html/user_lock.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "user_check_email") {
    $require1 = "user/html/user_check_email.php";
}/* ---------------------- Nachrichten -------------------------- */
elseif ($action == "messages" and $LINK[$action] == 1) {
    unset($ID);
    $require1 = "extra/messages/index.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];

}/* --------------------- Explorer --------------------------- */
elseif (($action == "explorer") and $LINK[$action] == 1) {
    $require1 = "extra/explorer/explorer_frameset.php";
} elseif ($action == "explorer_tree" and $LINK["explorer"] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_tree.dao";
    $require3 = "extra/explorer/explorer_tree.php";
    $BODYHEADER = "<IMG SRC=\"" . $LINK["icon_url"][142] . "\">&nbsp;&nbsp;" . $lang[$LINK["desc"][$LINK_ID["explorer"]]];
    $ONLOAD = "rebuild_tree();";
    $ONCLICK = "OnClick=\"body_click();\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "explorer_main" and $LINK["explorer"] == 1) {
    $require1 = "extra/explorer/metadata.lib";
    $require2 = "gtab/gtab.lib";
    $require3 = "gtab/gtab_type_erg.lib";
    $require4 = "extra/explorer/filestructure.lib";
    $require5 = "extra/explorer/explorer_main.dao";
    $require6 = "extra/explorer/explorer_main.php";
    $ONKEYDOWN = "OnKeydown=\"LmEx_sendkeydown(event);\"";
    if (! $preview_archive) {
        $ONLOAD .= "if(top.main){window.focus();}";
    }
    $ONCLICK = "OnClick=\"LmEx_body_click();\"";
    if (! $gtabid) {
        $BODYHEADER = $lang[$LINK["desc"][$LINK_ID["explorer"]]];
    }
} elseif ($action == "explorer_search" and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_search.php";
    $ONLOAD = "window.focus();";
    $ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "explorer_detail" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "gtab/gtab_type.lib";
    $require3 = "extra/explorer/filestructure.lib";
    $require4 = "extra/explorer/metadata.lib";
    $require5 = "gtab/sql/gtab_change.dao";
    $require6 = "extra/explorer/explorer_detail.dao";
    $require7 = "extra/explorer/explorer_detail.php";
    if ($show_part) {
        $ONLOAD = "window.focus();show_part('$show_part');";
    } else {
        $ONLOAD = "window.focus();show_part('format');";
    }
    $ONKEYDOWN = "OnKeydown=\"sendkeydown(event);\"";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "explorer_convert" and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_preview.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $BODY = 1;
}elseif ($action == "download" and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/download.php";
    $BODYHEADER = $HEADER;
    $BODY = 1;
} elseif ($action == "explorer_upload") {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_upload.php";
    $BODY = 0;
} elseif (($action == "mini_explorer") and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "gtab/gtab.lib";
    $require3 = "extra/explorer/explorer_main.lib";
    $require4 = "extra/explorer/mini_explorer.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $ONLOAD = "window.focus()";
} elseif (($action == "explorer_dublicates") and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_dublicates.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $ONLOAD = "window.focus()";
}/* ---------------------- Kalender -------------------------- */
elseif ($action == "kalender" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab_erg.lib";
    $require2 = "gtab/gtab_type.lib";
    $require3 = "extra/calendar/fullcalendar/cal.dao";
    $require4 = "extra/calendar/fullcalendar/cal.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $ONCLICK = "OnClick=\"body_click();\"";
}/* ---------------------- Kanban -------------------------- */
elseif ($action == "kanban") {// and $LINK[$action] == 1) {
    $require1 = "gtab/gtab_erg.lib";
    $require2 = "gtab/gtab_type.lib";
    $require3 = "gtab/gtab.lib";
    $require4 = "gtab/gtab_type_erg.lib";
    $require5 = "extra/kanban/kanban.dao";
    $require6 = "extra/kanban/kanban.php";
    $ONCLICK = "OnClick=\"body_click();\"";
}

/* ---------------------- userstat -------------------------- */
elseif ($action == "userstat") {
    $require2 = "extra/calendar/dom/cal.php";
    $require3 = "gtab/gtab_register.lib";
} elseif ($action == "userstat_set") {
    $require1 = "extra/calendar/dom/cal_set.php";
    $ONLOAD = "window.focus();showFlatCalendar();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "userstat_list") {
    $require2 = "extra/calendar/dom/cal_list.php";
    $ONLOAD = "window.focus();";
} elseif ($action == "userstat_main") {
    $require2 = "gtab/gtab.lib";
    $require4 = "extra/calendar/dom/cal.dao";
    $require5 = "extra/calendar/dom/cal_main.php";
    $ONLOAD = "window.focus();call_year('" . date("Y") . "','" . (date("m") - 1) . "','" . date("d") . "','tag',91); ";
    $BODYHEADER = "<IMG SRC=\"" . $LINK["icon_url"][169] . "\">&nbsp;&nbsp;" . $lang[$LINK["desc"][$LINK_ID["kalender"]]];
}

/* ---------------------- workflow -------------------------- */
elseif ($action == "lwf_mytask" and $LINK[$action] == 1) {
    $require1 = "extra/workflow/lwf.lib";
    $require2 = "extra/workflow/lwf_mytask.php";
    $ONLOAD = "window.focus();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
}

elseif ($action == "history" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "extra/history.dao";
    $require3 = "extra/history.php";
    $ONLOAD = "window.focus();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "report" and $LINK[176] == 1) {
    if (!$report_medium || $report_medium == 'pdf') {
        $report_medium = 'tcpdf';
    }
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/report/report.dao";
    $require3 = "extra/report/report_dyns.php";
    $ONLOAD = "window.focus();";
    $BODY = 1;

/* ---------------------- printer cache -------------------------- */
}elseif ($action == "user_printer_cache" and $LINK[$action] == 1) {
    $require1 = "user/html/user_printer_cache.php";
    #$require2 = "extra/workflow/lwf_mytask.php";
    $ONLOAD = "window.focus();";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];

} elseif ($action == 'report_preview') {
    $require1 = 'extra/report/preview/preview.php';
}

// deprecated - move to dashboard
elseif ($action == "diag_erg") {
    if ($gdiaglist[$gtabid]["template"][$diag_id] AND file_exists(DEPENDENTPATH . $gdiaglist[$gtabid]["template"][$diag_id])) {
        require_once (DEPENDENTPATH . $gdiaglist[$gtabid]["template"][$diag_id]);
    }else {
        $require1 = 'extra/diagram/LmbChart.php';
    }
    $BODYHEADER = $HEADER;
    if ($noheader OR $gdiaglist[$gdiaglist['gtabid'][$diag_id]]["noheader"][$diag_id]) {
        $BODY = 1;
    }
}

elseif ($action == "user_reportmanager" || $action == "user_templatemanager") {
    require_once(COREPATH . 'extra/reportManager/reportManager.php');
    exit();
}
elseif($action === 'syntaxcheckjs') {
    require_once(COREPATH . 'lib/syntaxcheck.php');
    exit();
}
elseif ($action == 'user_mails'  and $LINK[$action] == 1) {
    $require1 = 'admin/setup/mail/mail_user.dao';
    $require2 = 'admin/setup/mail/mail.php';
}
elseif ($action == 'mail_preview') {
    $require1 = 'extra/mail/html/init.php';
}
elseif ($LINK["extension"][$LINK_ID[$action]] and is_file($umgvar["pfad"] . $LINK["extension"][$LINK_ID[$action]])) {} elseif ($action == "null") {} else {
    $bzm = 0;
    foreach ($LINK["name"] as $key => $value) {
        if ($LINK["typ"][$key] == 5 and $LINK[$action] and $action == $LINK_ACTION[$key]) {
            $a = 1;
        }
        $bzm ++;
    }
    if (! $a) {
        if ($db) {
            lmbdb_close($db);
        }
        die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B></CENTER></BODY>");
    }
}

// set onbeforeunload to execute the function in $ONWINDOWUNLOAD and set that string as a confirmation to the user to leave the page
if($ONWINDOWUNLOAD) {
    $ONWINDOWUNLOAD = "
        window.onbeforeunload = function (e) {
            var e = e || window.event;
            var msg = $ONWINDOWUNLOAD();
            if(msg) {
                // For IE and Firefox
                if (e) {
                  e.returnValue = msg;
                }
                // For Safari
                return msg;
            }
        };";
} else {
    $ONWINDOWUNLOAD = "";
}

$ONLOAD = "OnLoad=\"$ONLOAD;$ONWINDOWUNLOAD\"";
if ($BODY != 1) {
?>

<?php
    
    require_once(Layout::getFilePath('main/head.php'));
    
?>

    <script src="main.php?action=syntaxcheckjs"></script>

<?php if ($action == 'gtab_erg' or $action == 'gtab_change' or $action == 'gtab_deterg' or $action == 'gtab_neu' or $action == 'gtab_search' or $action == 'gtab_form' or $action == 'diag_erg' or $action == 'diag_erg' or $action == 'explorer_tree' or $action == 'explorer_main' or $action == 'message_detail' or $action == 'message_main' or $action == 'message_tree' or $action == 'kalender' or $action == 'kanban' or $action == 'mini_explorer' or $action=="report_preview") {?>
	<script src="assets/js/extra/explorer/file-cache.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/js/extra/extensions/ext.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/jquery-ui/jquery-ui.min.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/jquery-ui/jquery.ui.datepicker-de.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/vendor/jquery-ui/jquery-ui-timepicker-addon.js?v=<?=$umgvar["version"]?>"></script>
	<style type="text/css">@import url(assets/vendor/jquery-ui/jquery-ui.min.css?v=<?=$umgvar["version"]?>);</style>
	<style type="text/css">@import url(assets/vendor/jquery-ui/jquery-ui-timepicker-addon.css?v=<?=$umgvar["version"]?>);</style>
    <?php if ($umgvar["use_jsgraphics"]) {?>
         <script src="assets/vendor/jsgraphics/jsgraphics.js?v=<?=$umgvar["version"]?>"></script>
    <?php }
}
    
if ($action == "explorer_main" or $action == "explorer_detail" or $action == "message_detail" or $action == "message_main" or $action == "mini_explorer" or $action == "gtab_change" or $action == "gtab_deterg" or $action == "gtab_neu") {?>
    <script src="assets/js/extra/explorer/explorer.js?v=<?=$umgvar["version"]?>"></script>
<?php }

if ($action == "kalender") {?>
    <script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/js/gtab/html/gtab_change.js?v=<?=$umgvar["version"]?>"></script>
    <link rel="stylesheet" type="text/css" href="assets/vendor/fullcalendar/fullcalendar.css?v=<?=$umgvar["version"]?>" />
    <link rel="stylesheet" type="text/css" href="assets/vendor/fullcalendar/fullcalendar.print.css?v=<?=$umgvar["version"]?>" media='print' />
    <script type="text/javascript" src="assets/vendor/fullcalendar/fullcalendar.js?v=<?=$umgvar["version"]?>"></script>
    <script type="text/javascript" src="assets/js/extra/calendar/fullcalendar/cal.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/select2/select2.full.min.js"></script>
    <script src="assets/vendor/select2/i18n/<?= getLangShort() ?>.js"></script>
<?php }

if ($action == "intro") { ?>
        <link rel="stylesheet" href="assets/vendor/gridstack/gridstack.min.css?v=<?=$umgvar["version"]?>" />
        <script src="assets/vendor/gridstack/gridstack-all.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/vendor/chart.js/chart.min.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/js/extra/dashboard/widgets/Widget.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/js/extra/dashboard/widgets/WidgetChart.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/js/extra/dashboard/dashboard.js?v=<?=$umgvar["version"]?>"></script>
<?php }
    
if ($action == "kanban") {?>
	<script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/js/gtab/html/gtab_change.js?v=<?=$umgvar["version"]?>"></script>
	<script src="assets/js/extra/kanban/kanban.js?v=<?=$umgvar["version"]?>"></script>
    <script src="assets/vendor/select2/select2.full.min.js"></script>
    <script src="assets/vendor/select2/i18n/<?= getLangShort() ?>.js"></script>
<?php }

if ($action == "gtab_erg" OR $action == "explorer_main") {?>
        <script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/js/gtab/html/gtab_erg.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/vendor/select2/select2.full.min.js"></script>
        <script src="assets/vendor/select2/i18n/<?= getLangShort() ?>.js"></script>

<?php } elseif ($action == "gtab_change" or $action == "gtab_deterg" or $action == "gtab_neu" or $action == "explorer_detail" or $action == 'gtab_form') {?>
        <script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar["version"]?>"></script>
        <script src="assets/js/gtab/html/gtab_change.js?v=<?=$umgvar["version"]?>"></script>
<?php }
    
if ($gfield[$gtabid]["wysiwyg"]) {
    
    ?>
    <script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar["version"]?>"></script>
    <?php
}


    
if ($layout_bootstrap) { ?>
    <link rel="stylesheet" type="text/css" href="<?=$session['css']?>?v=<?=$umgvar["version"]?>">
    <script src="assets/vendor/bootstrap/bootstrap.bundle.min.js?v=<?=$umgvar['version']?>"></script>
    <?php } elseif($action != "report_html") { ?>
    <link rel="stylesheet" type="text/css" href="<?=$session['legacyCss']?>?v=<?=$umgvar["version"]?>">
<?php }
    
// include extensions
if ($gLmbExt["ext_main.js"] AND $BODY != 1) {
    foreach ($gLmbExt["ext_main.js"] as $key => $extfile) {?>
        <script src="<?=$extfile.'?v='.$umgvar["version"]?>"></script>
    <?php }
}
    
// include extensions
if ($gLmbExt["ext_main.css"] AND $BODY != 1) {
    foreach ($gLmbExt["ext_main.css"] as $key => $extfile) {?>
        <link rel="stylesheet" type="text/css" href="<?=$extfile.'?v='.$umgvar["version"]?>">
    <?php }
}

require_once(Layout::getFilePath('main/body.php'));

}

// ajax - without output
if ($ajax) {
    for ($i = 1; $i <= 10; $i ++) {
        if (lmb_substr(${"require" . $i}, lmb_strlen(${"require" . $i} - 3, 3)) == "php") {
            ${"require" . $i} = "";
        }
    }
}

// EXTENSION replace
if ($LINK["extension"][$LINK_ID[$action]] and is_file(DEPENDENTPATH . $LINK["extension"][$LINK_ID[$action]])) {
    require_once (DEPENDENTPATH . $LINK["extension"][$LINK_ID[$action]]);
} else {
    if ($require1) {
        require_once (COREPATH . $require1);
    }
    if ($require2) {
        require_once (COREPATH . $require2);
    }
    if ($require3) {
        require_once (COREPATH . $require3);
    }
    if ($require4) {
        require_once (COREPATH . $require4);
    }
    if ($require5) {
        require_once (COREPATH . $require5);
    }
    if ($require6) {
        require_once (COREPATH . $require6);
    }
    if ($require7) {
        require_once (COREPATH . $require7);
    }
    if ($require8) {
        require_once (COREPATH . $require8);
    }
    if ($require9) {
        require_once (COREPATH . $require9);
    }
    if ($require10) {
        require_once (COREPATH . $require10);
    }
    if ($require11) {
        require_once (COREPATH . $require11);
    }
}
/* -------------------------------------------------------------------- */

// ---- History Eintrag ---------
if (! $ltmp["history_action"] and $session["logging"] == 2 and ! $BODY) {
    history_action($gtabid, $ID, $LINK_ID[$action], 2);
}

// --- J-Script ---
if($BODY != 1){
    
    echo '<script>';
    
    // ------------ Schnapschuß Navigationsrefresh -------------
    if ($new_snapshot) {
        //TODO: frame does not exit in bootstrap layout
        echo "if(parent.nav){parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&tab_group=1&refresh=no&sess_refresh=1';};\nif(parent.parent.nav){parent.parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&tab_group=1&refresh=no&sess_refresh=1';};\n";
    }
    echo "var frameLoaded = true;\n";
    
    echo '</script>';
    
    require_once(Layout::getFilePath('main/foot.php'));
}

// --- History ---------
if ($lhist and $session["logging"]) {
    lhist($lhist);
}

/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {
    lmbdb_close($db);
}

// save session variables
save_sessionvars($globvars);

#$zeit_now = is_firsttime();
#echo "<div style=\"z-index:99999;position:absolute;left:50px;top:50px;background-color:white\">".($zeit_now - $zeit_main)."</div>";
#var_dump($GLOBALS['_PHPA']);


#echo round((memory_get_peak_usage()/ 1024 / 1024),2) . " MB";

?>
