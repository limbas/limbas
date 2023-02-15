<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



//$gtabid = $params["gtabid"];
$tresult = array();
$lmb_kanban = new lmb_kanban($gtabid,$gsr);


# include extensions
if($GLOBALS["gLmbExt"]["ext_kanban.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_kanban.inc"] as $key => $extfile){
		require_once($extfile);
	}
}





if($params["action"] == "loadCards"){
    require_once(COREPATH . 'gtab/gtab_type_erg.lib');
    echo json_encode($lmb_kanban->load_cards());
}elseif($params["action"] == "drop"){
	$lmb_kanban->drop_card($params["ID"],$params["status"],$params["order"]);
}elseif($params["action"] == "add"){
	echo $lmb_kanban->add_card($params["status"]);
}elseif($params["action"] == "delete"){
    $lmb_kanban->delete_card($params["ID"]);
}elseif($params["action"] == "archive"){
    $lmb_kanban->delete_card($params["ID"],true);
}elseif($params["action"] == "details" AND $params["form_id"]){
	require_once(COREPATH . 'gtab/gtab_form.lib');
	require_once(COREPATH . 'gtab/gtab_type.lib');
	require_once(COREPATH . 'gtab/sql/gtab_change.dao');
	
	$ID = floor($params["ID"]);

	if($params["act"] == 'gtab_change'){
		$gresult = get_gresult($gtabid,null,null,null,null,$gform[$params["form_id"]]["used_fields"],$ID);
		form_gresult($ID,$gtabid,$params["form_id"],$gresult); # need for fields of related tables
	}else{
		$ID = 0; # bei Anlage neuer Datensatz
	}
	formListElements($params["act"],$gtabid,$ID,$gresult,$params["form_id"]);
}elseif($params["action"] == "details"){
	require_once(COREPATH . 'gtab/gtab.lib');
	require_once(COREPATH . 'gtab/gtab_type.lib');
	global $LINK;
	global $gtab;
	$gtabid = $params["gtabid"];
	$ID = floor($params["ID"]);
	
	if($ID){
		$gresult = get_gresult($gtabid,1,null,null,null,null,$ID);
	}else{$ID = 0;}
	
		?>


    <table class="kanban-default-form" cellpadding="1" cellspacing="0" width="100%">
        <tr>
            <td><p><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["TITLE"]]?></p>
                <?php display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["TITLE"],$ID);?>
            </td>
            <td rowspan="3">
                <p><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["TAGS"]]?></p>
            <?php display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["TAGS"],$ID);?>
            </td>
        </tr>

        <tr>
            <td><p><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["STATUS"]]?></p>
                <?php display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["STATUS"],$ID,1,null,"width:95%;");?></td>
        </tr>
        <tr>
            <td><p><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["ASSIGNED"]]?></p>
                <?php display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["ASSIGNED"],$ID,1);?></td>
        </tr>
        <tr>
            <td colspan="4"><p><?=$gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["DESCRIPTION"]]?></p>
                <?php display_dftyp($gresult,$gtabid,$gfield[$gtabid]["argresult_name"]["DESCRIPTION"],$ID,1,null,"width:100%;height:100px");?>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <?php

                    echo ($LINK[11] && $gtab['delete'][$gtabid]) ? "<input onclick=\"kanban_card_delete('$gtabid','$ID')\" style=\"margin-right: 0.2rem;\" type=\"button\" value=\"$lang[160]\">" : '';
                    echo ($LINK[164] && $gtab['hide'][$gtabid]) ? "<input onclick=\"kanban_card_archive('$gtabid','$ID')\" type=\"button\" value=\"$lang[1257]\">" : '';
                    echo "<input type=\"button\" value=\"$lang[33]\" onclick=\"kanban_card_edit($ID);\" style=\"float:right\">";
                    echo "<input type=\"button\" value=\"details\"  style=\"float:right;margin-right: 0.2rem;\" onclick=\"lmb_popupDetails($gtabid,$ID)\">";
                ?>

            </td>
        </tr>
    </table>
		<?php
}elseif($params["action"] == "saveDetails"){
	
	$lmb_kanban->lmb_historyUpdate($params);
}elseif($params["action"] == "getCard"){
        require_once(COREPATH . 'gtab/gtab_type_erg.lib');
        echo $lmb_kanban->get_card($params["ID"]);
}


if($params["reload"]){
	/*if($tresult = $lmb_calendar->lmb_getEvent($tresult,$params)){
		$tresult = lmb_arrayDecode($tresult);
		$GLOBALS["noencode"] = 1;
		echo json_encode($tresult);
	}else{
		echo 1;
	}*/
}


?>
