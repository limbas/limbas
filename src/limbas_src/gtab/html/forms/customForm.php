<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
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

$readonly = check_DataPermission($gtabid,$ID,$gresult);

printContextMenus($gtabid,$form_id,$ID,$gresult,$readonly);
if($GLOBALS["greportlist_exist"] AND ($LINK[175] OR $LINK[176] OR $LINK[315])){
    LmbReportSelect::printReportSelect($gtabid);
}

if ($gformlist[$gtabid]['theme'][$form_id]) {

} else {
    form_view($gtabid,$ID,$gresult,$form_id,$readonly);
}
