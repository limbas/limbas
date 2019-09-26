<?php

/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH (support@limbas.org)
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
require_once ("inc/include_db.lib");
require_once ("lib/include.lib");
require_once ("lib/session.lib");
require_once ("lib/context.lib");

if ($action == 'null') {
    $action = null;
}

// EXTENSIONS add
if ($gLmbExt["ext_main.inc"]) {
    foreach ($gLmbExt["ext_main.inc"] as $key => $extfile) {
        require_once ($extfile);
    }
}

// Temporäre Suchkriterien inizialisieren
if ($rgsr) {
    $gsr = $rgsr;
}

/* -------------------------------------------------------------- */
if (lmb_substr($action, 0, 4) == "gtab") {
    if (! $gtab["tab_id"][$gtabid] and $action != 'gtab_form') {
        if ($db) {
            odbc_close($db);
        }
        die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B></CENTER></BODY>");
    } elseif ($gtab["typ"][$gtabid] != 5 and count($gfield[$gtabid]["field_id"]) <= 0 and $action != 'gtab_form') {
        if ($db) {
            odbc_close($db);
        }
        die("<BODY ><BR><BR><CENTER><B><H3><FONT COLOR=\"red\" FACE=\"VERDANA,ARIAL,HELVETICA\">$lang[1]</FONT></H1></B><BR><BR><B>&nbsp;$lang[118]</CENTER></BODY>");
    }

    require_once ("extra/snapshot/snapshot.lib");
    $require_ = "gtab/gtab.lib";
    require_once ($require_);
    $require_ = "gtab/gtab_form.lib";
    require_once ($require_);
    $require_ = "gtab/gtab_type.lib";
    require_once ($require_);
    $require_ = "gtab/gtab_type_erg.lib";
    require_once ($require_);
    
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
        if ($verkn["verknpf"] == 2 and $verkn and ! $ID and $action != 'gtab_neu') {
            $ID = r_verknpf($gtabid, $verkn);
            // Pool Verknüpfung
        } elseif (! $ID and $verkn and ($action == 'gtab_change' or $action == 'gtab_deterg')) {
            $ID = p_verknpf($gtabid, $verkn);
        }
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
if ($action == "layoutframe") {
    $layoutframe = preg_replace('/[^a-z0-9-]/i', "", $layoutframe);
    $require2 = "layout/frame_main_" . $layoutframe . ".php";
    if (file_exists("layout/" . $session["layout"] . "/frame_main_" . $layoutframe . ".php")) {
        $require2 = "layout/" . $session["layout"] . "/frame_main_" . $layoutframe . ".php";
    }
    $bodyclass = "main_" . $layoutframe;
} elseif ($action == "top") {
    $require1 = "gtab/gtab.lib";
    $require2 = "layout/top.php";
    if (file_exists("layout/" . $session["layout"] . "/top.php")) {
        $require2 = "layout/" . $session["layout"] . "/top.php";
    }
    #$projekt_file = array(
    #    1,
    #    2
    #);
    //$STYLE = "border-top:1px solid black;";
    $GLOBALS["ltmp"]["history_action"] = 1;
    $bodyclass = "top";
} elseif ($action == "top2" and $LINK[252]) {
    $require2 = "layout/top2.php";
    if (file_exists("layout/" . $session["layout"] . "/top2.php")) {
        $require2 = "layout/" . $session["layout"] . "/top2.php";
    }
    if (! $session["e_setting"][4]) {
        $session["e_setting"][4] = 20;
    }
    $GLOBALS["ltmp"]["history_action"] = 1;
    $bodyclass = "top2";
} elseif ($action == "nav" and $LINK[251]) {
    $require1 = "gtab/gtab.lib";
    $require2 = "layout/nav.dao";
    if (file_exists("layout/" . $session["layout"] . "/nav.dao")) {
        $require2 = "layout/" . $session["layout"] . "/nav.dao";
    }
    $require3 = "layout/nav.php";
    if (file_exists("layout/" . $session["layout"] . "/nav.php")) {
        $require3 = "layout/" . $session["layout"] . "/nav.php";
    }
    // if($refresh != "no"){$ONLOAD = "this.link_1.click();";}
    $GLOBALS["ltmp"]["history_action"] = 1;
    $bodyclass = "nav";
} elseif ($action == "multiframe" and $session["multiframe"] and $LINK[253]) {
    $require1 = "layout/multiframe.dao";
    if (file_exists("layout/" . $session["layout"] . "/multiframe.dao")) {
        $require1 = "layout/" . $session["layout"] . "/multiframe.dao";
    }
    $require2 = "layout/multiframe.php";
    if (file_exists("layout/" . $session["layout"] . "/multiframe.php")) {
        $require2 = "layout/" . $session["layout"] . "/multiframe.php";
    }
    //$STYLE = "border-top:1px solid " . $farbschema["WEB4"] . ";";
    $STYLE = "";
    $GLOBALS["ltmp"]["history_action"] = 1;
    $ONCLICK = "OnClick=\"body_click();\"";
    $ONLOAD = "startautoopen();";
    $bodyclass = "multiframe";
} 

elseif ($action == "intro" or $action == "nav_info") {
    $require2 = "help/intro.php";
    $GLOBALS["ltmp"]["history_action"] = 1;
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} elseif ($action == "help") {
    $require1 = "help/help.php";
}

elseif ($action == "help_user") {
    $ONLOAD = "document.location.href='help/DB3_0.9.9.pdf'";
} elseif ($action == "help_admin") {
    $ONLOAD = "document.location.href='help/DB3_Admin_0.9.9.pdf'";
} elseif ($action == "abfrage_admin") /**
 * @TODO right
 */
{
    require ("abfragen/abfragen.php");
} elseif ($action == "gtab_erg" and $LINK[$action] == 1) {
    // is view
    if ($gtab["typ"][$gtabid] == 5 and ! $gfield[$gtabid]["id"]) {
        $require2 = "gtab/html/gtab_view.php";
    } else {
        $checkmod = 2;
        $require2 = "gtab/gtab_register.lib";
        $require3 = "extra/explorer/filestructure.lib";
        // Gtab Lösch/Kopier-Routine
        if ($use_record) {
            $require4 = "gtab/sql/gtab_use.dao";
        }
        $require5 = "gtab/sql/gtab_erg.dao";
        $require6 = "gtab/html/gtab_erg.php";
        
        $ONLOAD = "gtabSetTablePosition('$posx','$posy')";
        $ONKEYDOWN = "OnKeydown=\"return sendkeydown(event);\"";
        $ONCLICK = "OnClick=\"body_click();\"";
    }
    
    $BODYHEADER = $tabgroup["name"][$gtab["tab_group"][$gtabid]];
} 

elseif ($action == "gtab_exp" and $LINK[$action] == 1) {
    $require2 = "gtab/gtab_register.lib";
    $require4 = "gtab/html/gtab_erg_exp.php";
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
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
} 

elseif ($action == "add_select" and $LINK[$action] == 1) {
    $require1 = "gtab/gtab.lib";
    $require2 = "gtab/sql/add_select.dao";
    $require3 = "gtab/html/add_select.php";
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
} elseif ($action == "explorer_download" and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/explorer_download.php";
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
    $require1 = "gtab/html/gtab_erg.lib";
    $require2 = "gtab/gtab_type.lib";
    $require3 = "extra/calendar/fullcalendar/cal.dao";
    $require4 = "extra/calendar/fullcalendar/cal.php";
    $BODYHEADER = $lang[$LINK["desc"][$LINK_ID[$action]]];
    $ONCLICK = "OnClick=\"body_click();\"";
}/* ---------------------- Kanban -------------------------- */
elseif ($action == "kanban") {// and $LINK[$action] == 1) {
    $require1 = "gtab/html/gtab_erg.lib";
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
    if (! $report_medium) {
        $report_medium = "pdf";
    }
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/report/report.dao";
    $require3 = "extra/report/report_" . lmb_substr($report_medium, 0, 3) . ".php";
    $ONLOAD = "window.focus();";
    $BODY = 1;
} 

elseif ($action == "diag_erg") {
    $require1 = "extra/diagram.php";
    $BODYHEADER = $HEADER;
    if ($noheader OR $gdiaglist[$gdiaglist['gtabid'][$diag_id]]["noheader"][$diag_id]) {
        $BODY = 1;
    }
}

elseif ($action == "download" and $LINK[$action] == 1) {
    $require1 = "extra/explorer/filestructure.lib";
    $require2 = "extra/explorer/download.php";
    $BODYHEADER = $HEADER;
    $BODY = 1;
} elseif ($LINK["extension"][$LINK_ID[$action]] and is_file($umgvar["pfad"] . $LINK["extension"][$LINK_ID[$action]])) {} elseif ($action == "null") {} else {
    $bzm = 0;
    foreach ($LINK["name"] as $key => $value) {
        if ($LINK["typ"][$key] == 5 and $LINK[$action] and $action == $LINK_ACTION[$key]) {
            $a = 1;
        }
        $bzm ++;
    }
    if (! $a) {
        if ($db) {
            odbc_close($db);
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

$ONLOAD = "OnLoad=\"browserType();$ONLOAD;$ONWINDOWUNLOAD\"";
if ($BODY != 1) {
    /* --------------------- KOPF --------------------------- */
    $STYLE = "STYLE=\"$STYLE\"";
    echo "<html $STYLE>\n";
    ?>
<HEAD>
<link rel="SHORTCUT ICON" href="favicon.ico">
<TITLE>Limbas - Enterprise Unifying Framework V <?=$umgvar["version"]?></TITLE>

<!-- meta -->
<META NAME="Title" CONTENT="Limbas Enterprise Unifying Framework V <?=$umgvar["version"]?>">
<meta NAME="author" content="LIMBAS GmbH">
<META NAME="Publisher" CONTENT="LIMBAS GmbH">
<META NAME="Copyright" CONTENT="LIMBAS GmbH">
<meta NAME="description" content="Enterprise Unifying Framework">
<meta NAME="version" content="<?=$umgvar["version"]?>">
<meta NAME="date" content="2017-10-23">
<meta HTTP-EQUIV="content-language" content="de">
<meta HTTP-EQUIV="Content-Type" content="text/html; charset=<?=$umgvar["charset"]?>">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta HTTP-EQUIV="Pragma" content="no-cache">
<meta HTTP-EQUIV="Cache-Control" content="no-cache, no-store, post-check=0, pre-check=0, must-revalidate">
<meta HTTP-EQUIV="Expires" content="0">
<meta HTTP-EQUIV="X-UA-Compatible" content="ie=edge,chrome=1" />


<script type="text/javascript" src="lib/global.js"></script>
<script type="text/javascript" src="USER/<?=$session["user_id"]?>/syntaxcheck.js" language="javascript"></script>
<script type="text/javascript" src="extern/jquery/jquery-1.11.0.min.js"></script>

<?php
    if ($action == 'gtab_erg' or $action == 'gtab_change' or $action == 'gtab_deterg' or $action == 'gtab_neu' or $action == 'gtab_search' or $action == 'gtab_form' or $action == 'diag_erg' or $action == 'diag_erg' or $action == 'explorer_tree' or $action == 'explorer_main' or $action == 'message_detail' or $action == 'message_main' or $action == 'message_tree' or $action == 'kalender' or $action == 'kanban' or $action == 'mini_explorer') {
        echo "
	<script type=\"text/javascript\" src=\"extra/explorer/file-cache.js\"></script>\n
	<script type=\"text/javascript\" src=\"EXTENSIONS/system/ext.js\"></script>\n
	<script type=\"text/javascript\" src=\"extern/jquery/jquery-ui-1.12.1.min.js\"></script>
	<script type=\"text/javascript\" src=\"extern/jquery/jquery.ui.datepicker-de.js\"></script>
	<script type=\"text/javascript\" src=\"extern/jquery/jquery-ui-timepicker-addon.js\"></script>
	<style type=\"text/css\">@import url(extern/jquery/theme/jquery-ui-1.12.1.min.css);</style>
	<style type=\"text/css\">@import url(extern/jquery/theme/jquery-ui-timepicker-addon.css);</style>";
        if ($umgvar["use_jsgraphics"]) {
            echo "<script type=\"text/javascript\" src=\"extern/jsgraphics/jsgraphics.js\"></script>\n";
        }
    }
    
    if ($action == "explorer_main" or $action == "explorer_detail" or $action == "message_detail" or $action == "message_main" or $action == "mini_explorer" or $action == "gtab_change" or $action == "gtab_deterg" or $action == "gtab_neu") {
        echo "<script type=\"text/javascript\" src=\"extra/explorer/explorer.js\"></script>\n";
    }
    
    if ($action == "kalender") {
        echo "
	<script type=\"text/javascript\" src=\"gtab/html/gtab.js\"></script>\n
	<script type=\"text/javascript\" src=\"gtab/html/gtab_change.js\"></script>\n
    <link rel='stylesheet' type='text/css' href='extern/fullcalendar/fullcalendar.css' />
	<link rel='stylesheet' type='text/css' href='extern/fullcalendar/fullcalendar.print.css' media='print' />
	<script type='text/javascript' src='extern/fullcalendar/fullcalendar.js'></script>
	<script type='text/javascript' src='extra/calendar/fullcalendar/cal.js'></script>  
	";
    }
    
    if ($action == "kanban") {
        echo "
	<script type=\"text/javascript\" src=\"gtab/html/gtab.js\"></script>\n
	<script type=\"text/javascript\" src=\"gtab/html/gtab_change.js\"></script>\n
	<script type='text/javascript' src='extra/kanban/kanban.js'></script>  
	";
    }
    
    if ($action == "gtab_erg") {
        echo "<script type=\"text/javascript\" src=\"gtab/html/gtab.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"gtab/html/gtab_erg.js\"></script>\n";
    } elseif ($action == "gtab_change" or $action == "gtab_deterg" or $action == "gtab_neu" or $action == "explorer_detail") {
        echo "<script type=\"text/javascript\" src=\"gtab/html/gtab.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"gtab/html/gtab_change.js\"></script>\n";
    }
    
    if ($gfield[$gtabid]["wysiwyg"]) {
        if ($umgvar["wysiwygeditor"] == "openwysiwyg") {
            echo "<script type=\"text/javascript\" src=\"extern/wysiwyg/openwysiwyg/scripts/wysiwyg.js\"></script>\n";
        } elseif ($umgvar["wysiwygeditor"] == "TinyMCE") {
            // echo "<script language=\"javascript\" type=\"text/javascript\" src=\"extern/wysiwyg/tiny_mce/tiny_mce.js\"></script>\n";
            echo "<script language=\"javascript\" type=\"text/javascript\" src=\"extern/wysiwyg/tinymce/tinymce.min.js\"></script>\n";
        }
    }
    
    // main CSS
    if ($action != "report_html") {
        echo "<style type=\"text/css\">@import url(USER/" . $session["user_id"] . "/layout.css);</style>\n";
    }
    
    // EXTENSIONS js
    if ($gLmbExt["ext_main.js"]) {
        foreach ($gLmbExt["ext_main.js"] as $key => $extfile) {
            echo "<script language=\"javascript\" type=\"text/javascript\" src=\"$extfile\"></script>\n";
        }
    }
    
    // EXTENSIONS css
    if ($gLmbExt["ext_main.css"]) {
        foreach ($gLmbExt["ext_main.css"] as $key => $extfile) {
            echo "<link rel='stylesheet' type='text/css' href='$extfile'>";
        }
    }
    
    if ($SCRIPT) {
        echo $SCRIPT;
    }
    
    echo "</HEAD>";
    
    // Page - Header
    if ($BODY != 1) {
        echo "\n<BODY CLASS=\"$bodyclass\" TOPMARGIN=\"0\" Marginheight=\"0\" Marginwidth=\"0\" BORDER=\"0\" LINK=\"$ALINK\" VLINK=\"$VLINK\" LINK=\"$NLINK\" TEXT=\"$TEXT1\" $ONLOAD $ONUNLOAD $ONKEYDOWN $ONCLICK $ONBLUR $ONMOUSEDOWN $ONMOUSEUP >\n";
        if ($BODYHEADER) {
            if (file_exists("layout/" . $session["layout"] . "/main_header.php")) {
                require_once ("layout/" . $session["layout"] . "/main_header.php");
            }
            echo "<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%;padding:0px;margin:0px\"><tr><td valign=\"top\" class=\"lmbfringeFrameMain\" id=\"lmbfringeFrameMain\">";
        }
    }
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
if ($LINK["extension"][$LINK_ID[$action]] and is_file($umgvar["pfad"] . $LINK["extension"][$LINK_ID[$action]])) {
    require_once ($umgvar["pfad"] . $LINK["extension"][$LINK_ID[$action]]);
} else {
    if ($require1) {
        require_once ($require1);
    }
    if ($require2) {
        
        require_once ($require2);
    }
    if ($require3) {
        require_once ($require3);
    }
    if ($require4) {
        require_once ($require4);
    }
    if ($require5) {
        require_once ($require5);
    }
    if ($require6) {
        require_once ($require6);
    }
    if ($require7) {
        require_once ($require7);
    }
    if ($require8) {
        require_once ($require8);
    }
    if ($require9) {
        require_once ($require9);
    }
    if ($require10) {
        require_once ($require10);
    }
    if ($require11) {
        require_once ($require11);
    }
}

/* -------------------------------------------------------------------- */

// ---- History Eintrag ---------
if (! $ltmp["history_action"] and $session["logging"] == 2 and ! $BODY) {
    history_action($gtabid, $ID, $LINK_ID[$action], 2);
}

// --- J-Script ---
if ($BODY != 1) {
    // --- Versionierung ---
    if ($confirm_fileversion) {
        print_versioning();
    }
    ?>
	<script language="JavaScript">
	window.defaultStatus = 'Limbas Enterprise Unifying Framework V <?=$umgvar["version"]?>';
	window.onerror = null;
	<?php
    // --- Fehlermeldungen -----
    if (is_array($alert)) {
        if ($alert and ! is_array($alert)) {
            $alert = array(
                $alert
            );
        }
        $alert = array_unique($alert);
        echo "showAlert('" . implode("\\n", $alert) . "');\n";
    }
    
    // ------------ Schnapschuß Navigationsrefresh -------------
    if ($new_snapshot) {
        echo "if(parent.nav){parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&tab_group=1&refresh=no&sess_refresh=1';};\nif(parent.parent.nav){parent.parent.nav.document.location.href = 'main.php?action=nav&sparte=gtab&tab_group=1&refresh=no&sess_refresh=1';};\n";
    }
    echo "var frameLoaded = true;\n";
    echo "</script>\n";
    if ($BODYHEADER) {
        echo "</td></tr></table>";
    }
    
    ?>
	
	</BODY>
	</HTML>

	
<?php

}

// --- History ---------
if ($lhist and $session["logging"]) {
    lhist($lhist);
}

/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {
    odbc_close($db);
}

// save session variables
save_sessionvars($globvars);

#$zeit_now = is_firsttime();
#echo "<div style=\"z-index:99999;position:absolute;left:50px;top:50px;background-color:white\">".($zeit_now - $zeit_main)."</div>";
#var_dump($GLOBALS['_PHPA']);


#echo round((memory_get_peak_usage()/ 1024 / 1024),2) . " MB";

?>