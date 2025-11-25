<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib\tables;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class TableFilter extends LimbasModel
{

    protected static string $tableName = 'LMB_SNAP';

    /**
     * @param int $id
     * @param int $userId
     * @param int $tabId
     * @param string $name
     * @param bool $global
     * @param TableFilterContent|null $content
     * @param string|null $ext
     * @param int|null $type
     * @param TableFilterGroup|null $group
     */
    public function __construct(

        public int    $id,
        public int    $userId,
        public int    $tabId,
        public string $name,
        public bool   $global,
        public ?TableFilterContent $content,
        public ?string $ext = null,
        public ?int    $type = null,
        public ?TableFilterGroup   $group = null
    )
    {
        //
    }


    /**
     * @param int $id
     * @return TableFilter|null
     */
    public static function get(int $id): TableFilter|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {
        $rs = Database::select(self::$tableName, where: $where);
        
        $output = [];
        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                intval(lmbdb_result($rs, 'USER_ID')),
                    intval(lmbdb_result($rs, 'TABID')),
                 lmbdb_result($rs, 'NAME'),
                boolval(lmbdb_result($rs, 'GLOBAL')),
                TableFilterContent::fromJson(lmbdb_result($rs, 'FILTER')),
                lmbdb_result($rs, 'EXT'),
                intval(lmbdb_result($rs, 'TYPE')),
                TableFilterGroup::get(intval(lmbdb_result($rs, 'SNAPGROUP'))),
            );
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $data = [
            'ID' => $this->id,
            'USER_ID' => $this->userId,
            'TABID' => $this->tabId,
            'NAME' => $this->name,
            'GLOBAL' => $this->global ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'FILTER' => $this->content?->toJson() ?? '[]',
            'EXT' => $this->ext,
            'TYPE' => $this->type,
            'SNAPGROUP' => $this->group?->id,
        ];

        lmb_StartTransaction();

        $nextId = $this->id;
        if (empty($this->id)) {
            $nextId = next_db_id(self::$tableName);
            $data['ID'] = $nextId;
            $result = Database::insert(self::$tableName, $data);
        } else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }


        if ($result) {
            lmb_EndTransaction(1);
            $this->id = $nextId;
        } else {
            lmb_EndTransaction(0);
        }

        return $result;
    }


    /**
     * @return bool
     */
    public function delete(): bool
    {
        global $session;
        global $gsnap;
        global $LINK;

        if (!$LINK[225] OR !is_numeric($this->id) OR (!$gsnap[$this->tabId]['owner'][$this->id] && !$gsnap[$this->tabId]['edit'][$this->id] && $session['group_id'] != 1)) {
            lmb_alert("you have no permission to edit this snapshot!");
            return false;
        }
        
        
        lmb_StartTransaction();

        $deleted = Database::delete(self::$tableName, ['ID' => $this->id]);

        if (!$deleted) {
            lmb_EndTransaction(0);
        } else {

            Database::delete('LMB_SNAP_SHARED', ['SNAPSHOT_ID' => $this->id]);
            
            lmb_EndTransaction(1);
        }

        unset($gsnap[$this->tabId]["id"][$this->id]);
        unset($gsnap[$this->tabId]["name"][$this->id]);

        return $deleted;
    }
    
    
    public static function create(int $tabId, ?string $name, int $id, TableFilterContent $useContent = null, int $type=1, int $filterGroupId = 0): bool|int
    {
        global $session;
        global $filter;
        global $gsr;
        global $gfield;
        global $gsnap;
        global $LINK;

        if(!empty($name)) {
            $name = lmb_substr(str_replace(';',',',trim($name)),0,50);   
        }
        $userId = intval($session['user_id']);


        if (!$LINK[225] OR ($id && (!$gsnap[$tabId]['owner'][$id] && !$gsnap[$tabId]['edit'][$id] && $session['group_id'] != 1))) {
            lmb_alert("you have no permission to edit this snapshot!");
            return false;
        }

        $content = $useContent;
        if($content === null){
            $content = new TableFilterContent(
                $filter['order'][$tabId] ?? null,
                $filter['popups'][$tabId] ?? null,
                $filter['status'][$tabId] ?? null,
                $filter['hidecols'][$tabId] ?? null,
                $filter['form'][$tabId] ?? null,
                $gsr[$tabId] ?? null,
                $gfield[$tabId]['sort'] ?? null,
                $gfield[$tabId]['rowsize'] ?? null
            );
        }


        $tableFilter = TableFilter::get($id);

        // update the snapshot
        if($tableFilter !== null) {
            $tableFilter->content = $content;
            $tableFilter->save();
            SNAP_invalidate();
        }
        // save a new snapshot for a user
        elseif (!empty($name)) {
            #$_SESSION["gsnap"] = null;

            // check if filter for table with same name exists for user
            $tableFilters = TableFilter::all([
                'TABID' => $tabId,
                'USER_ID' => $session['user_id'],
                'NAME' => $name
            ]);

            if(!empty($tableFilters)) {
                $tableFilter = $tableFilters[0];
                $tableFilter->content = $content;
            }
            else {

                $group = TableFilterGroup::get($filterGroupId) ?? new TableFilterGroup(0,'',false);

                $tableFilter = new TableFilter(
                    0,
                    $userId,
                    $tabId,
                    $name,
                    false,
                    $content,
                    type: $type,
                    group: $group
                );
            }

            $tableFilter->save();

            # save snapshot in session
            $gsnap[$tabId]['id'][$tableFilter->id] = $tableFilter->id;
            $gsnap[$tabId]['user_id'][$tableFilter->id] = $session['user_id'];
            $gsnap[$tabId]['name'][$tableFilter->id] = $name;
            $gsnap[$tabId]['filter'][$tableFilter->id] = $content->toArray();
            $gsnap[$tabId]['glob'][$tableFilter->id] = 0;
            $gsnap[$tabId]['owner'][$tableFilter->id] = 1;
            $gsnap[$tabId]['shared'][$tableFilter->id] = 0;
            # invalidate Session for other users
            SNAP_invalidate();
        }
        // if no ID and no name then this is a new system snapshot
        else {

            $group = TableFilterGroup::get($filterGroupId) ?? new TableFilterGroup(0,'',false);
            $tableFilter = new TableFilter(
                0,
                0,
                $tabId,
                '',
                false,
                $content,
                type: $type,
                group: $group
            );
            $tableFilter->save();
        }

        return $tableFilter->id;
    }
    
    
    public function share(string $destUser, bool $toggleDelete, bool $toggleEdit, bool $drop): bool
    {
        global $LINK;
        global $session;
        global $gsnap;

        $uid = explode('_', $destUser);
        if (!$uid[0] || !$uid[1]) {
            return false;
        }

        $entityId = $uid[0];
        $entityType = lmb_strtoupper($uid[1]);
        
        $rs = Database::select('LMB_SNAP_SHARED', ['ID','ENTITY_ID', 'ENTITY_TYPE','EDIT','DEL'],[
            'ENTITY_ID' => $entityId,
            'ENTITY_TYPE' => $entityType,
            'SNAPSHOT_ID' => $this->id,
        ]);
        
        $shareFilterId = null;
        if ($rs && lmbdb_fetch_row($rs)) {
            $entityId = lmbdb_result($rs, 'ENTITY_ID');
            $entityType = lmbdb_result($rs, 'ENTITY_TYPE');
            $canDelete = lmbdb_result($rs, 'DEL');
            $canEdit = lmbdb_result($rs, 'EDIT');
            $shareFilterId = lmbdb_result($rs, 'ID');
        }
        

        if (!$LINK[225] and !$gsnap[$this->tabId]["owner"][$this->id]) {
            lmb_alert("you have no permission to share this snapshot!");
            return false;
        }
        
        $where = [
            'ID' => $shareFilterId
        ];
        

        $result = false;
        if ($drop && $entityId && $shareFilterId !== null) {
            $result = Database::delete('LMB_SNAP_SHARED', $where);
        } elseif ($toggleEdit && $entityId && $shareFilterId !== null) {
            $result = Database::update('LMB_SNAP_SHARED', [
                'EDIT' => $canEdit ? LMB_DBDEF_FALSE : LMB_DBDEF_TRUE,
            ], $where);
        } elseif ($toggleDelete && $entityId && $shareFilterId !== null) {
            $result = Database::update('LMB_SNAP_SHARED', [
                'DEL' => $canDelete ? LMB_DBDEF_FALSE : LMB_DBDEF_TRUE,
            ], $where);
        } elseif (!$shareFilterId && ($entityType === 'G' || $session['user_id'] != $entityId)) {
            $nextId = next_db_id('LMB_SNAP_SHARED');
            $result = Database::insert('LMB_SNAP_SHARED', [
                'ID' => $nextId,
                'ENTITY_ID' => $entityId,
                'ENTITY_TYPE' => $entityType,
                'SNAPSHOT_ID' => $this->id,
            ]);
        }

        if ($result) {
            SNAP_invalidate();
            return true;
        } else {
            return false;
        }
    }
    
    
    public static function loadInSession($snap_changed=null,$admin=null): array
    {
        global $session;
        global $db;
        global $action;
        global $gfield;
        global $gsnapgroup;
        global $gsnap;


        $gsnap = [];
        
        $sqlquery = "SELECT 
					LMB_SNAP.ID,
					LMB_SNAP.USER_ID,
					LMB_SNAP.TABID,
					LMB_SNAP.NAME,
					LMB_SNAP.SNAPGROUP,
					LMB_SNAP.GLOBAL,
					LMB_SNAP.FILTER,
					LMB_SNAP.EXT,
					LMB_SNAP.TYPE,
					LMB_SNAP_SHARED.ENTITY_TYPE,
					LMB_SNAP_SHARED.ENTITY_ID,
					LMB_SNAP_SHARED.EDIT,
					LMB_SNAP_SHARED.DEL
					";

        if($admin == 1){
            $sqlquery .= "FROM LMB_SNAP, LMB_SNAP_SHARED
				WHERE LMB_SNAP.ID = LMB_SNAP_SHARED.SNAPSHOT_ID ORDER BY LMB_SNAP.USER_ID,LMB_SNAP.NAME";
        }elseif($admin == 2){
            $sqlquery .= "FROM LMB_SNAP LEFT OUTER JOIN LMB_SNAP_SHARED ON(LMB_SNAP.ID = LMB_SNAP_SHARED.SNAPSHOT_ID)
				ORDER BY LMB_SNAP.USER_ID,LMB_SNAP.NAME";
        }else{
            $sqlquery .= "FROM LMB_SNAP left outer join LMB_SNAP_SHARED ON LMB_SNAP.ID = LMB_SNAP_SHARED.SNAPSHOT_ID
				WHERE LMB_SNAP.USER_ID = " . $session["user_id"] ."
				OR (LMB_SNAP_SHARED.entity_type = 'U' AND LMB_SNAP_SHARED.entity_id = ".$session["user_id"].")
				OR GLOBAL = ".LMB_DBDEF_TRUE;
            foreach ($session["subgroup"] as $key => $groupid) {
                $sqlquery = $sqlquery . " OR (LMB_SNAP_SHARED.entity_type = 'G' AND LMB_SNAP_SHARED.entity_id = ".$groupid.")";
            }
            $sqlquery .= "ORDER BY LMB_SNAP.SNAPGROUP,LMB_SNAP.NAME";
        }

        $rs1 = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

        while(lmbdb_fetch_row($rs1)){
            $resort = 0;
            $snaptabid = lmbdb_result($rs1,"TABID");
            if(!$snaptabid){$snaptabid = 0;}
            $snapid = lmbdb_result($rs1,"ID");

            $gsnap[$snaptabid]['filter'][$snapid] = TableFilterContent::fromJson(lmbdb_result($rs1,'FILTER') ?? '[]')?->toArray() ?? [];
            if(empty($gsnap[$snaptabid]['filter'][$snapid])){
                // fallback for old serialized filters, should be removed in future
                $gsnap[$snaptabid]["filter"][$snapid] = unserialize(lmbdb_result($rs1,'FILTER'));
                if($gsnap[$snaptabid]["filter"][$snapid] === false) {
                    $gsnap[$snaptabid]["filter"][$snapid] = [];
                }
            }
            #SNAP_validate($snaptabid,$gsnap[$snaptabid]["filter"][$snapid]);


            //$gsnap[$snaptabid]["gsr_md5"][$snapid] = md5(serialize($gsnap[$snaptabid]["filter"][$snapid]['gsr']));


            $gsnap[$snaptabid]["id"][$snapid] = $snapid;
            $gsnap[$snaptabid]["user_id"][$snapid] = lmbdb_result($rs1,"USER_ID");
            $gsnap[$snaptabid]["name"][$snapid] = lmbdb_result($rs1,"NAME");
            $gsnap[$snaptabid]["group"][$snapid] = lmbdb_result($rs1,"SNAPGROUP");
            $gsnap[$snaptabid]["glob"][$snapid] = lmbdb_result($rs1,"GLOBAL");
            $gsnap[$snaptabid]["del"][$snapid] = lmbdb_result($rs1,"DEL");
            $gsnap[$snaptabid]["edit"][$snapid] = lmbdb_result($rs1,"EDIT");
            $gsnap[$snaptabid]["type"][$snapid] = lmbdb_result($rs1,"TYPE");
            $gsnap["argresult_id"][$snapid] = $snaptabid;

            $vtabid = $gtab['argresult_id'][$md5_tab];

            if($gsnap[$snaptabid]["group"][$snapid]) {
                $gsnap[-1][ $gsnap[$snaptabid]["group"][$snapid] ][$snapid] = $snapid;
            }

            ###### check for changes/permissions #######

            # drop missing fields
            if($gsnap[$snaptabid]["filter"][$snapid]['sort']){
                foreach ($gsnap[$snaptabid]["filter"][$snapid]['sort'] as $key => $value){
                    if(!$gfield[$snaptabid]['sort'][$key]){
                        unset($gsnap[$snaptabid]["filter"][$snapid]['sort'][$key]);
                        unset($gsnap[$snaptabid]["filter"][$snapid]['hidecols'][$key]);
                        unset($gsnap[$snaptabid]["filter"][$snapid]["gsr"][$key]);
                    }
                }}

            # add new fields as hidden
            if($gfield[$snaptabid]['sort']){
                foreach ($gfield[$snaptabid]['sort'] as $key => $value){
                    if(!$gsnap[$snaptabid]["filter"][$snapid]['sort'][$key]){
                        $gsnap[$snaptabid]["filter"][$snapid]['hidecols'][$key] = 1;
                        $gsnap[$snaptabid]["filter"][$snapid]['sort'][$key] = $value;
                        $resort = 1;
                    }
                }}

            # resort if needed
            if($resort){
                asort($gsnap[$snaptabid]["filter"][$snapid]['sort']);
            }


            # Extension
            #$e2xt = lmbdb_result($rs1,"EXT");
            #if($ext){
            #	eval($ext);
            #	$gsnap[$snaptabid]["ext"][$snapid] = $extension;
            #
            $gsnap[$snaptabid]["ext"][$snapid] = lmbdb_result($rs1,"EXT");
            #}

            #$gsnap["tab"][$snapid] = $snaptabid;
            if(lmbdb_result($rs1,"USER_ID") == $session["user_id"]){
                $gsnap[$snaptabid]["owner"][$snapid] = 1;
            }
            if(lmbdb_result($rs1,"ENTITY_TYPE")){
                $gsnap[$snaptabid]["shared"][$snapid] = 1;
            }
        }

        if($snap_changed){
            $sqlquery = "UPDATE LMB_SESSION SET SNAP_CHANGED = ".LMB_DBDEF_FALSE." WHERE USER_ID = " . $session["user_id"];
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
        }

        return $gsnap;
    }
    
}
