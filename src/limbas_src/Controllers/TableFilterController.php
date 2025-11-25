<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\Controllers;

use Limbas\gtab\lib\tables\TableFilter;
use Symfony\Component\HttpFoundation\Request;

class TableFilterController extends LimbasController
{

    public function handleRequest(Request|array $request): array
    {
        return match ($request->get('action')) {
            'save' => $this->save($request),
            'delete' => $this->delete($request),
            'shareSelect' => $this->shareSelect($request),
            default => ['success' => false],
        };
    }


    /**
     * Save new snapshot
     *
     * @param Request $request
     * @return array
     */
    private function save(Request $request): array
    {
        
        $orgFilterId = intval($request->get('snap_id',0));
        $tabId = intval($request->get('gtabid',0));
        $name = $request->get('limbasSnapshotName');

        $filterId = TableFilter::create($tabId,$name,$orgFilterId);
        if(empty($orgFilterId)) {
            echo $filterId;
        }
        return [];
    }


    /**
     * Delete snapshot
     *
     * @param Request $request
     * @return array
     */
    private function delete(Request $request): array
    {
        $filterId = intval($request->get('snap_id',0));
        
        $tableFilter = TableFilter::get($filterId);
        if(!$tableFilter->delete()) {
            echo 'Error on delete snapshot : '. $filterId;
        }
        return [];
    }

    /**
     * share snapshot
     *
     * @param Request $request
     * @return array
     */
    private function shareSelect(Request $request): array
    {

        $params = $request->query->all();
        
        if($params['destUser'] AND $params['gtabid']){
            $tableFilter = TableFilter::get($params['gtabid']);
            $tableFilter->share($params['destUser'],$params['del'],$params['edit'],$params['drop']);
        }
        
        $content = $this->shareDisplay($params);
        
        $params['usefunction'] = 'lmbSnapShareSelect';
        dyns_showUserGroups($params, $content);
        return [];
    }

    private function shareDisplay(array $params): string
    {
        global $session;
        global $db;
        global $lang;
        global $userdat;
        global $groupdat;

        $snapid = $params["gtabid"];

        $sqlquery = "SELECT LMB_SNAP_SHARED.EDIT,LMB_SNAP_SHARED.DEL,LMB_SNAP_SHARED.ENTITY_TYPE,LMB_SNAP_SHARED.ENTITY_ID FROM LMB_SNAP_SHARED WHERE SNAPSHOT_ID = $snapid ORDER BY ENTITY_TYPE DESC,ENTITY_ID";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,'',__FILE__,__LINE__);

        $output = "<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">
	<tr><td colspan=\"6\"><hr></td></tr>
	<tr><td></td><td></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-eye\"></i></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-pencil\"></i></td>
	<td style=\"width:25px\" align=\"center\"><i class=\"lmb-icon lmb-trash\"></td>
	</tr>
	";

        while(lmbdb_fetch_row($rs)){
            $uid = lmbdb_result($rs,"ENTITY_ID")."_".lmbdb_result($rs,"ENTITY_TYPE");
            if(lmbdb_result($rs,"EDIT")){$edit = "CHECKED";}else{$edit = "";}
            if(lmbdb_result($rs,"DEL")){$del = "CHECKED";}else{$del = "";}
            if(lmbdb_result($rs,"ENTITY_TYPE")=="U"){
                $pic = " lmb-user ";
                $name = $userdat["bezeichnung"][lmbdb_result($rs,"ENTITY_ID")];
            }elseif(lmbdb_result($rs,"ENTITY_TYPE")=="G"){
                $pic = " lmb-group ";
                $name = $groupdat["name"][lmbdb_result($rs,"ENTITY_ID")];
            }else{
                continue;
            }

            $output .= "<tr>
		<td><i class=\"lmb-icon $pic\"></i></td>
		<td>".$name."</td>
		<td align=\"center\"><i class=\"lmb-icon lmb-check-alt\"></i></td>
		<td align=\"center\"><input type=\"checkbox\" class=\"checkb\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',0,1)\" $edit></td>
		<td align=\"center\"><input type=\"checkbox\" class=\"checkb\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',1)\" $del></td>
		<td align=\"center\" style=\"width:20px;\"><i class=\"lmb-icon lmb-erase\" style=\"cursor:pointer;\" onclick=\"limbasSnapshotShare(null,$snapid,'$uid',0,0,1)\"></td>
		</tr>
		";
        }
        $output .= "</table>";

        return $output;
    }

    
    
    
}
