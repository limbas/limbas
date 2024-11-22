<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


$gtabid = $params["gtabid"];
$tresult = array();
$lmb_calendar = new lmb_calendar($gtabid);


# EXTENSIONS
if ($GLOBALS["gLmbExt"]["ext_calendar.inc"]) {
    foreach ($GLOBALS["gLmbExt"]["ext_calendar.inc"] as $key => $extfile) {
        require_once($extfile);
    }
}

if ($params["verkn_ID"]) {
    $verkn = init_relation($params["verkn_tabid"], $params["verkn_fieldid"], $params["verkn_ID"], null, null, 1);
}

switch ($params['action']) {
    case 'context':
        echo $lmb_calendar->lmb_calContext($params["ID"], $params["gtabid"]);
        break;
    case 'delete':
        $lmb_calendar->lmb_deleteEvent($params["gtabid"], $params["ID"], $verkn);
        break;
    case 'drop':
    case 'resize':
        $lmb_calendar->lmb_dropEvent($params["gtabid"], $params["ID"], $params["newStartDate"], $params["newEndDate"], $params["action"], $params["allDay"], $params["oldAllDay"]);
        break;
    case 'move':
        $lmb_calendar->lmb_moveEvent($params["gtabid"], $params["ID"], $params["stamp"], $params["resource"]);
        break;
    case 'copy':
        $lmb_calendar->lmb_copyEvent($params["gtabid"], $params["ID"], $params["stamp"], $params["resource"]);
        break;
    case 'add':
        echo $lmb_calendar->lmb_addEvent($params["gtabid"], $params["title"], $params["start"], $params["end"], $params["allDay"], $verkn, $params);
        break;
    case 'details':
        if ($params['form_id']) {
            require_once(COREPATH . 'gtab/gtab_form.lib');
            require_once(COREPATH . 'gtab/gtab_type.lib');
            require_once(COREPATH . 'gtab/sql/gtab_change.dao');

            $ID = floor($params["ID"]);

            if ($params["act"] == 'gtab_change') {
                $gresult = get_gresult($gtabid, null, null, null, null, $gform[$params["form_id"]]["used_fields"], $ID);
                form_gresult($ID, $gtabid, $params["form_id"], $gresult); # need for fields of related tables
            } else {
                $ID = 0; # bei Anlage neuer Datensatz
            }
            formListElements($params["act"], $gtabid, $ID, $gresult, $params["form_id"]);
        } else {
            require_once(COREPATH . 'gtab/gtab.lib');
            require_once(COREPATH . 'gtab/gtab_type.lib');
            $gtabid = $params["gtabid"];
            $ID = floor($params["ID"]);
            if ($ID) {
                $gresult = get_gresult($gtabid, 1, null, null, null, null, $ID);
            } else {
                $ID = 0;
            }

            #REPEATUNTIL REPETITION
            require "cal_detail.php";
        }
        break;
    case 'saveDetails':
        # adding bulk dates
        if ($params["calBulk_termStaH"]) {
            $lmb_calendar->lmb_addBulkDate($params, $verkn);
            # add / change single date
        } else {
            $lmb_calendar->lmb_historyUpdate($params);
        }
        break;
    case 'resources':
        $resources = $lmb_calendar->getResources($gtabid, $params);
        $GLOBALS["noencode"] = 1;
        if ($resources) {
            echo json_encode($resources);
        } else {
            echo 1;
        }
        return;
    default:
        break;
}


if($params["reload"]){
	if($tresult = $lmb_calendar->lmb_getEvent($tresult,$params)){
		$GLOBALS["noencode"] = 1;
		echo json_encode($tresult);
	}else{
		echo 1;
	}
}


?>
