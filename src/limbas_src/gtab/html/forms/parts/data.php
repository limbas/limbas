<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
use Limbas\extra\template\select\TemplateSelector;

global $lmmultitenants;

$onlyFields = false;
if(isset($form_id)) {
    $onlyFields = $gform[$form_id]["used_fields"];
}


if(!empty($ID)){
    if($gtab["typ"][$gtabid] == 5){
        # need filter and search-params for using pointer
        $gresult = get_gresult($gtabid,1,$filter,$gsr,0,$onlyFields,$ID);
    }else{
        $gresult = get_gresult($gtabid,null,null,null,0,$onlyFields,$ID);
    }

    if(isset($form_id)) {
        // relation parameters fields | parent relation fields
        if ($gform[$form_id]["parentrel"] or $gform[$form_id]["paramrel"]) {
            $verkn = null;
            if ($verkn_tabid and $verkn_fieldid) {
                $verkn = set_verknpf($verkn_tabid, $verkn_fieldid, $verkn_ID, $verkn_add_ID, $verkn_del_ID, $verkn_showonly, $verknpf);
            }
            form_gresult($ID, $gtabid, $form_id, $gresult, null, $verkn);
        }
    }
    
}
else {
    $gresult = get_default_values($gtabid);
}

$readonly = check_DataPermission($gtabid,$ID,$gresult);

if($umgvar['multitenant'] AND $gtab['multitenant'][$gtabid] AND $lmmultitenants['mid'][$session['mid']] != $gresult[$gtabid]['MID'][0] AND !$session["superadmin"]){
    $action = 'gtab_deterg';
    $readonly = 1;
}

require COREPATH . 'gtab/html/forms/parts/contextmenus.php';

TemplateSelector::printTemplateSelectModal(intval($gtabid));
