<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


/*
 * Socket Flow
 * 
 * On Main -> runSyncWithClientSocket -> Soap Request an Client -> 
 * On Client -> Incoming Soap Request -> startSocket -> Cron -> runSyncWithMainSocket
 * 
 * 
 * Sync Cache Types
 * 1 = delete
 * 2 = created
 * 3 = changed
 * 
 */


abstract class Datasync
{

    /**
     * @var bool defines if the current system is a master or client
     */
    protected $is_main;

    /**
     * @var array synchronisation template
     */
    protected $template;

    /**
     * @var int id of the current client
     */
    protected $current_client;

    /**
     * @var array of exceptions to be loggend in lmb_sync_log table
     */
    protected $sync_exceptions;

    /**
     * @var int timestamp at which the sync started. Used as key of the process.
     */
    protected $cacheTimestamp = 0;


    /**
     * @param $template
     */
    public function __construct($template)
    {
        $this->template = $template;
        $this->sync_exceptions = array();
    }


    /**
     * Delete all handled records from sync cache
     *
     * @param $timestamp
     * @return bool success
     */
    protected function resetCache($timestamp): bool
    {
        global $db;

        LimbasLogger::log('Resetting cache');

        if (empty($timestamp)) {
            return true;
        }

        //don't delete exceptions
        $con = array();
        foreach ($this->sync_exceptions as $type => $exceptions) {
            foreach ($exceptions as $tabid => $records) {
                if ($tabid == 0) {
                    continue;
                }
                foreach ($records as $datid => $record) {
                    if ($datid === 'new') {
                        continue;
                    }

                    if ($datid == 0) {
                        $con[] = "(TABID = $tabid AND (FIELDID <= 0 OR DATID <= 0))";
                        $this->processExceptions($tabid, $datid, reset($record));
                        continue;
                    }
                    foreach ($record as $fieldid => $msg) {
                        if(array_key_exists($fieldid, $msg) && $msg[$fieldid]['code'] === 9) {
                            continue;
                        }
                        $this->processExceptions($tabid, $datid, $msg);
                        if ($fieldid == 0) {
                            $con[] = "(TABID = $tabid AND SLAVE_DATID = $datid)";
                            continue;
                        }
                        $con[] = "(TABID = $tabid AND SLAVE_DATID = $datid AND FIELDID = $fieldid)";

                    }
                }
            }
        }
        $cw = '';
        if (lmb_count($con) > 0) {
            $cw = 'NOT (' . implode(' OR ', $con) . ') AND';
        }

        //TODO: template in lmb_sync_cache
        if (!$this->is_main || !empty($this->current_client)) {
            $sql = "DELETE FROM LMB_SYNC_CACHE WHERE $cw LMB_SYNC_CACHE.PROCESS_KEY = $timestamp  " . (($this->is_main) ? " AND SLAVE_ID = {$this->current_client}" : '');
            $rs = lmbdb_exec($db, $sql);
            if (!$rs) {
                LimbasLogger::error('Reset failed');
                return false;
            }
        }


        // insert client in global log
        if ($this->is_main) {
            $sqlquery = 'SELECT LMB_SYNC_CACHE.ID FROM LMB_SYNC_CACHE LEFT JOIN LMB_SYNC_GLOBAL ON LMB_SYNC_CACHE.ID = LMB_SYNC_GLOBAL.CACHE_ID
    AND LMB_SYNC_GLOBAL.CLIENT_ID = ' . $this->current_client . ' WHERE LMB_SYNC_CACHE.PROCESS_KEY = ' . $timestamp . ' AND SLAVE_ID = 0 AND CACHE_ID IS NULL';

            $rs = lmbdb_exec($db, $sqlquery);
            if (!$rs) {
                LimbasLogger::error('Reading global cache');
                return false;
            }

            $values = [];
            if (lmbdb_num_rows($rs) > 0) {
                while (lmbdb_fetch_row($rs)) {
                    $values[] = '(' . lmbdb_result($rs, 'ID') . ',' . $this->current_client . ')';
                }
            }

            if (!empty($values)) {
                $sql = 'INSERT INTO LMB_SYNC_GLOBAL (CACHE_ID,CLIENT_ID) VALUES ' . implode(',', $values);
                $rs = lmbdb_exec($db, $sql);
                if (!$rs) {
                    LimbasLogger::error('Put into global cache failed');
                    return false;
                }
            }
        }


        return true;
    }

    /**
     * Process specific error codes
     *
     * @param $tabid
     * @param $datid
     * @param $error_msg
     */
    protected function processExceptions($tabid, $datid, $error_msg): void
    {

        if (!is_array($error_msg) || $tabid <= 0) {
            return;
        }

        $error_msg = reset($error_msg);

        //in case of slave sending a change to a record that does not exist on master -> write create entry to cache on slave so next time the missing record will be created
        if ($datid && $error_msg['code'] == 6 && !$this->is_main) {
            $this->putInCache($tabid, 0, $datid, 2);
        }
    }


    /**
     * Mark all records that may be synced by the current process
     * @param $maxRecords
     * @return void
     */
    private function setSyncRecords($maxRecords): void
    {
        $db = Database::get();

        $filter = '';
        if ($this->is_main) {
            $filter = ' LEFT JOIN LMB_SYNC_GLOBAL ON LMB_SYNC_CACHE.ID = LMB_SYNC_GLOBAL.CACHE_ID AND LMB_SYNC_GLOBAL.CLIENT_ID = ' . $this->current_client . '
    WHERE (SLAVE_ID = ' . $this->current_client . ' OR ( SLAVE_ID = 0 AND LMB_SYNC_GLOBAL.CACHE_ID IS NULL ))';
        }

        $limit = '';
        if ($maxRecords !== false) {
            $limit = 'LIMIT ' . $maxRecords;
        }

        if ($this->is_main || $maxRecords !== false) {
            $sqlQuery = 'UPDATE LMB_SYNC_CACHE
        SET PROCESS_KEY = ' . $this->cacheTimestamp . '
        WHERE ID IN (
        SELECT LMB_SYNC_CACHE.ID
          FROM LMB_SYNC_CACHE
          ' . $filter . '
          ORDER BY LMB_SYNC_CACHE.TYPE ' . $limit . '
        )';

        } else {
            // no filter and limit needed on client => update all records
            $sqlQuery = 'UPDATE LMB_SYNC_CACHE SET PROCESS_KEY = ' . $this->cacheTimestamp;
        }


        lmbdb_exec($db, $sqlQuery);
    }


    /**
     * Prepares an array based on lmb_sync_cache which can be applied later
     *
     * @param array<int> $newids
     * @return array|false
     */
    protected function collectChangedData($newids = array()): bool|array
    {
        global $umgvar;

        $db = Database::get();

        //TODO: group alle entries of the same datid into one record with array of types and array of update fields
        //TODO: Only tables of selected sync template


        

        $maxRecords = false;
        if (array_key_exists('sync_max_records', $umgvar)) {
            if (!empty($umgvar['sync_max_records'])) {
                $maxRecords = intval($umgvar['sync_max_records']);
            }
        }

        try {
            
            
            //get count of all records
            $sqlQuery = 'SELECT COUNT(ID) AS CACHECOUNT FROM LMB_SYNC_CACHE' . ($this->is_main ? ' WHERE (SLAVE_ID = ' . $this->current_client . ' OR  SLAVE_ID = 0 )' : '');
            $rs = lmbdb_exec($db, $sqlQuery);
            $recordCount = 0;
            if (lmbdb_fetch_row($rs)) {
                $recordCount = intval(lmbdb_result($rs,'CACHECOUNT'));
            }
            

            $this->cacheTimestamp = time();

            $this->setSyncRecords($maxRecords);

            //ORDER: delete (1) -> created (2) -> changed (3)
            $sqlquery = 'SELECT TYPE, TABID, FIELDID, DATID, SLAVE_ID, SLAVE_DATID FROM LMB_SYNC_CACHE WHERE PROCESS_KEY = ' . $this->cacheTimestamp . ' GROUP BY TYPE, TABID, FIELDID, DATID, SLAVE_ID, SLAVE_DATID ORDER BY TYPE';

            $rs = lmbdb_exec($db, $sqlquery);

            $data = array();
            $syncFields = array();
            $rowCount = lmbdb_num_rows($rs);
            
            if ($this->is_main) {
                LimbasLogger::log('Collecting data on master [' . $rowCount . ' / ' . $recordCount . ']');
            } else {
                LimbasLogger::log('Collecting data on client [' . $rowCount . ' / ' . $recordCount . ']');
            }
            
            if ($rowCount > 0) {
                while (lmbdb_fetch_row($rs)) {
                    $tabid = lmbdb_result($rs, 'TABID');
                    $fieldid = lmbdb_result($rs, 'FIELDID');
                    $datid = lmbdb_result($rs, 'DATID');
                    $slave_datid = lmbdb_result($rs, 'SLAVE_DATID');
                    $type = lmbdb_result($rs, 'TYPE');
                    $erstdatum = lmbdb_result($rs, 'ERSTDATUM');

                    if ($this->template[$tabid]['global']) {
                        $slave_datid = $datid;
                    }


                    // get highest timestamp
                    $dt = new DateTime($erstdatum);
                    $erstdatum = $dt->getTimestamp();

                    // no slave assigned and dataset deleted/created
                    if ($this->is_main && $slave_datid == 0 && $type != 2 && !$this->template[$tabid]['global']) {
                        $this->setException('error', 8, 'No slavedat', $tabid);
                        continue;
                    }

                    if (!array_key_exists($tabid, $data)) {
                        $data[$tabid] = array();
                        //cache syncfields per table
                        $syncFields[$tabid] = $this->getSyncFields($tabid);
                    }

                    //deleted
                    if ($type == 1) {
                        $data[$tabid][$slave_datid] = false;
                        if (is_array($data[$tabid]['new']) && array_key_exists($slave_datid, $data[$tabid]['new'])) {
                            unset($datid, $data[$tabid]['new'][$slave_datid]);
                        }
                    } //created & not deleted
                    elseif ($type == 2 && $data[$tabid][$slave_datid] !== false) {

                        //only take all data if fieldid = 1
                        if ($fieldid == 1) {
                            $newdata = $this->getData($tabid, $datid, $syncFields[$tabid], skipRelations: true);
                        } else {
                            //otherwise, take only system fields
                            $newdata = $this->getData($tabid, $datid, [], $erstdatum, skipRelations: true);
                        }
                        if ($newdata === false) {
                            continue;
                        }

                        //always remove specific values systemdata
                        if (array_key_exists('sys', $newdata)) {
                            $ignore = ['VPID', 'VACT'];
                            foreach ($ignore as $i) {
                                if (array_key_exists($i, $newdata['sys'])) {
                                    unset($newdata['sys'][$i]);
                                }
                            }
                        }


                        // add data to default update array
                        $newdata['new'] = true;
                        $data[$tabid][$datid] = $newdata;

                        $newid = [
                            'ID' => $datid,
                            'slave_datid' => 0
                        ];

                        if (!$this->is_main) {
                            $data[$tabid]['new'][$datid] = $newid;
                        } else {
                            $data[$tabid]['new'][] = $newid;
                        }

                    } //changed & not deleted
                    elseif ($type == 3 && $data[$tabid][$slave_datid] !== false) {
                        if (in_array($fieldid, $syncFields[$tabid]) || $fieldid == 0) {
                            if ($fieldid == 0) {
                                $changedata = $this->getData($tabid, $datid, array(), $erstdatum);
                            } else {
                                $changedata = $this->getData($tabid, $datid, array($fieldid), $erstdatum);
                            }

                            if ($changedata === false) {
                                continue;
                            }
                            if (is_array($data[$tabid][$slave_datid])) {
                                $data[$tabid][$slave_datid] += $changedata; // array merge
                            } else {
                                $data[$tabid][$slave_datid] = $changedata;
                            }
                        }
                    }
                }
            }

            return array('data' => $data, 'newids' => $newids, 'timestamp' => $this->cacheTimestamp);
        } catch (Throwable $t) {
            LimbasLogger::error('Data collection error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }
    }


    /**
     * Applies changes out of prepared array.
     * On slave: ids of created records are collected and send back
     * On master: all records get their corresponding lmb_sync_slave and lmb_sync_id
     *
     * @param array $syncdata
     * @return array|false
     */
    protected function applyChangedData($syncdata): bool|array
    {
        global $db;
        global $gfield;
        global $gtab;

        LimbasLogger::log('Applying changed data');

        //get deleted records
        $sqlquery = "SELECT TYPE, TABID, DATID, SLAVE_ID, SLAVE_DATID FROM LMB_SYNC_CACHE WHERE TYPE = 0";
        $rs = lmbdb_exec($db, $sqlquery);
        if (!$rs) {
            $this->setException('error', 10, 'Fetching deleted data failed');
        }

        $deleted = array();
        while (lmbdb_fetch_row($rs)) {
            $tabid = lmbdb_result($rs, 'TABID');

            if (!is_array($this->template) || !array_key_exists($tabid, $this->template)) {
                continue;
            }

            //if master use slave_datid instead of datid
            if ($this->is_main && !$this->template[$tabid]['global']) {
                $deleted[$tabid][] = lmbdb_result($rs, 'SLAVE_DATID');
            } else {
                $deleted[$tabid][] = lmbdb_result($rs, 'DATID');
            }
        }

        $newids = array();
        $relation = array();
        try {
            foreach ($syncdata['data'] as $tabid => $data) {

                //check if table is a parameterized relation table //TODO: other way than name check
                if (strtoupper(substr($gtab['table'][$tabid], 0, 5)) === 'VERK_') {
                    continue;
                }

                if (!is_array($this->template) || !array_key_exists($tabid, $this->template)) {
                    continue;
                }

                $new = null;
                if (array_key_exists('new', $data)) {
                    $new = $data['new'];
                    unset($data['new']);
                }

                //new records
                try {
                    if (is_array($new)) {
                        foreach ($new as $record) {

                            $table = dbf_4($gtab['table'][$tabid]);
                            $orgId = $record['ID'];

                            if ($this->template[$tabid]['global']) {
                                //check if record already exists
                                $id = $orgId;
                                $sql = "SELECT ID FROM $table WHERE ID = $id";
                                $rs = lmbdb_exec($db, $sql);
                                if (!lmbdb_result($rs, 'ID')) {
                                    //it does not exist => force id
                                    $id = new_data($tabid, null, null, null, $id);
                                }
                            } else {
                                if ($this->is_main) {
                                    //check if record already exists
                                    $sql = "SELECT ID FROM $table WHERE LMB_SYNC_SLAVE = {$this->current_client} AND LMB_SYNC_ID = {$orgId}";
                                    $rs = lmbdb_exec($db, $sql);
                                    $id = lmbdb_result($rs, 'ID');
                                    if (empty($id)) {
                                        $id = new_data($tabid);
                                    }
                                } else {
                                    $id = new_data($tabid);
                                }

                            }


                            if (empty($id)) {
                                $this->setException('error', 4, 'New data failed: ' . lmb_log::getLogMessage(true), $tabid, $orgId);
                                continue;
                            }

                            if ($this->is_main && !$this->template[$tabid]['global']) {
                                //assign lmb_sync_slave and lmb_sync_id
                                $sql = "UPDATE $table SET LMB_SYNC_SLAVE = {$this->current_client}, LMB_SYNC_ID = {$record['ID']} WHERE ID = $id";
                                $rs = lmbdb_exec($db, $sql);
                            } elseif ($this->is_main && $this->template[$tabid]['global']) {
                                //global sync does not need SYNC_SLAVE AND SYNC_ID SET but a new entry in the cache table so it is synced to all other clients
                                $this->putInCache($tabid, 0, $id, 2);
                            } else {
                                //fill match array for master system
                                $newids[$tabid][$record['ID']] = array('ID' => $record['ID'], 'slave_datid' => $id);
                            }

                        }
                    }
                } catch (Throwable $t) {
                    LimbasLogger::error('Create new records error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                    return false;
                }

                //update / delete records
                foreach ($data as $id => $values) {
                    if ($id == 0) {
                        $this->setException('error', 6, 'Record not found', $tabid, $id);
                        continue;
                    }


                    // on slave check if data belongs to new record and translate id
                    if(!$this->is_main && is_array($values) && array_key_exists('new', $values) && array_key_exists($tabid, $newids) && array_key_exists($id,$newids[$tabid])) {
                        $id = $newids[$tabid][$id]['slave_datid'];
                        unset($values['new']);
                    } elseif(!$this->is_main && is_array($values) && array_key_exists('new', $values)) {
                        $this->setException('error', 6, 'ID resolve failed', $tabid, $id);
                        continue;
                    }
                    

                    $sid = $id;
                    if ($this->is_main) {
                        $id = $this->convertID($tabid, $id);
                        if ($id === false) {
                            //if not deleted anyway
                            if ($values !== false) {
                                $this->setException('error', 6, 'ID resolve failed', $tabid, $sid);
                            }
                            continue;
                        }
                    }

                    //deleted
                    if ($values === false) {
                        //TODO: setting delete or hide
                        //TODO: relation check

                        try {
                            $recordExists = $this->recordExists(intval($tabid), intval($id));
                            $delResult = !$recordExists || del_data($tabid, $id);
                            
                            if (!$delResult) {
                                //if not exists returns false 
                                $this->setException('error', 2, 'Delete failed: ' . lmb_log::getLogMessage(true), $tabid, $sid);
                            } elseif ($this->is_main && $this->template[$tabid]['global']) {
                                //global sync does not need SYNC_SLAVE AND SYNC_ID SET but a new entry in the cache table, so it is synced to all other clients
                                $this->putInCache($tabid, 0, $id, 1);
                            }
                        } catch (Throwable $t) {
                            LimbasLogger::error('Delete records error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                            return false;
                        }


                    } //changed and not deleted
                    elseif (!array_key_exists($tabid, $deleted) || !in_array($id, $deleted[$tabid])) {

                        try {
                            $update = array();

                            //Update allowed
                            if ($this->is_main) {
                                $fids = $this->template[$tabid]['slave'];
                            } else {
                                $fids = $this->template[$tabid]['master'];
                            }


                            foreach ($values as $fieldid => $value) {
                                if ($fieldid == 'new') {
                                    continue;
                                }
                                if ($fieldid == 'sys') {
                                    $this->handleSystemFields($tabid, $id, $value, $update);
                                    continue;
                                }

                                if (!in_array($fieldid, $fids) || $fieldid === 'ID' || $fieldid === 'slave_datid') {
                                    continue;
                                }

                                $special = $this->applyFieldType($tabid, $fieldid, $id, $value['value']);
                                if ($special === true) {
                                    if ($this->hasConflict($tabid, $id, $fieldid, $value)) {
                                        continue;
                                    }
                                    $update["$tabid,$fieldid,$id"] = $value['value'] . '';
                                } else if ($special === 'rel') {
                                    $relation[$tabid][$id][$fieldid] = $value['value'];
                                } else {
                                    continue;
                                }

                                if ($this->is_main && $this->template[$tabid]['global']) {
                                    //global sync table create cache
                                    $this->putInCache($tabid, $fieldid, $id, 3);
                                }
                            }
                            if (!empty($update) && update_data($update) !== true) {
                                $this->setException('error', 3, 'Update failed: ' . lmb_log::getLogMessage(true), $tabid, $sid);
                            }
                        } catch (Throwable $t) {
                            LimbasLogger::error('Update records error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                            return false;
                        }


                    }
                }

                unset($syncdata['data'][$tabid]);
            }
        } catch (Throwable $t) {
            LimbasLogger::error('Apply error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }

        //update sync_ids of records created on slave
        try {
            if ($this->is_main) {
                foreach ($syncdata['newids'] as $tabid => $tab) {

                    //no need to set sync ids if table is globally synced
                    if ($this->template[$tabid]['global']) {
                        continue;
                    }

                    foreach ($tab as $record) {
                        $id = $record['ID'];
                        $sync_datid = $record['slave_datid'];

                        $table = dbf_4($gtab['table'][$tabid]);
                        //set lmb_sync_slave and lmb_sync_id
                        $sql = "UPDATE $table SET LMB_SYNC_SLAVE = {$this->current_client}, LMB_SYNC_ID = $sync_datid WHERE ID = $id";
                        $rs = lmbdb_exec($db, $sql);
                        
                        
                        // foreach relation field => create update entry
                        foreach($gfield[$tabid]['data_type'] as $fieldId => $dataType) {
                            if(in_array($dataType,[24,25,27])) {
                                $null = null;
                                execute_sync($tabid,$fieldId,$id,$null,$sync_datid,$this->current_client,3, true);
                            }
                        }
                        
                    }
                }
            }
        } catch (Throwable $t) {
            LimbasLogger::error('Update sync ids error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }

        //set or delete relations
        //verbose
        try {
            foreach ($relation as $tabid => $records) {
                foreach ($records as $datid => $fields) {
                    $sdatid = $datid;
                    if ($this->is_main) {
                        $sdatid = $this->convertID($tabid, $datid, 1);
                    }

                    foreach ($fields as $fieldid => $verknids) {
                        if (!is_array($verknids)) {
                            continue;
                        }
                        $verkntab = $gfield[$tabid]['verkntabid'][$fieldid];

                        //check if relation is hierarchical and ignore it
                        if (array_key_exists('verkntree', $gfield[$tabid]) && array_key_exists($fieldid, $gfield[$tabid]['verkntree'])) {
                            continue;
                        }

                        $firstValueKey = array_key_first($verknids);
                        $hasLevelIds = false;
                        $levelIds = [];
                        if (is_array($verknids[$firstValueKey]) && array_key_exists('LID',$verknids[$firstValueKey])) {
                            $hasLevelIds = true;
                            foreach ($verknids as $key => &$verknid) {
                                $levelIds[$verknid['id']] = $verknid['LID'];
                                $verknid = $verknid['id'];
                            }
                        } elseif(!$this->is_main) {
                            foreach ($verknids as $key => &$verknid) {                                
                                if(is_array($verknid) && array_key_exists('new',$verknid)) {
                                    
                                    $relTabId = $gfield[$tabid]['verkntabid'][$fieldid];
                                    
                                    if(array_key_exists($relTabId,$newids) && array_key_exists($verknid['id'],$newids[$relTabId])) {
                                        $verknid = $newids[$relTabId][$verknid['id']]['slave_datid'];
                                    } else {
                                        unset($verknids[$key]);
                                    }
                                }
                            }
                        }


                        //get exsting relations
                        $gsr[$tabid]['ID'] = $datid;
                        $filter["relationval"][$tabid] = 1;
                        $filter['status'][$tabid] = -1;
                        $filter["validity"][$tabid] = 'all';
                        $gresult = get_gresult($tabid, 1, $filter, $gsr, null, array($tabid => array($fieldid)));
                        $existing_rel = [];
                        if ($gresult[$tabid]['res_count'] > 0) {
                            $existing_rel = array_filter($gresult[$tabid][$fieldid][0]);
                        }
                        if (empty($existing_rel)) {
                            $existing_rel = [];
                        }
                        if ($this->is_main) {
                            $levelIdsNew = [];
                            foreach ($verknids as $key => &$verknid) {
                                $orgverknid = $verknid;

                                $verknid = $this->convertID($verkntab, $verknid);
                                $verknids[$key] = $verknid;

                                if ($hasLevelIds && array_key_exists($orgverknid, $levelIds)) {
                                    $levelIdsNew[$verknid] = $levelIds[$orgverknid];
                                }
                            }

                            $levelIds = $levelIdsNew;
                        }
                        $verknids = array_filter($verknids);

                        //search ids on both slave and master
                        $intersect = array_intersect($verknids, $existing_rel);
                        if (!$intersect) {
                            $intersect = array();
                        }

                        // add ids missing on current system
                        $verkn_add_ids = array_diff($verknids, $intersect);
                        if (!empty($verkn_add_ids)) {


                            $vtabid = $this->getVerknTabId($tabid, $fieldid);
                            $vtablesynced = array_key_exists($vtabid, $gtab['datasync']) && !empty($gtab['datasync'][$vtabid]);
                            if (
                                //table is published and synced too => set lmb_slave_id and keyid from slave
                                $vtablesynced
                                //table is related to dms
                                || $hasLevelIds
                            ) {

                                foreach ($verkn_add_ids as $keyid => $verknAddId) {

                                    $params = [];

                                    //if relation is parameterized and synced
                                    if ($vtablesynced) {
                                        $params['LMB_SYNC_SLAVE'] = $this->current_client;
                                        $params['LMB_SYNC_ID'] = $keyid;
                                    }

                                    if ($hasLevelIds && array_key_exists($verknAddId, $levelIds)) {
                                        $params['LID'] = $levelIds[$verknAddId];
                                    }

                                    $relation = init_relation($tabid, $fieldid, $datid, [$verknAddId], null, null, $params);


                                    if (!set_relation($relation)) {
                                        $errormsg = lmb_log::getLogMessage(true);
                                        //workaround for existing relations
                                        if (!(strpos($errormsg, 'already joined') !== false)) {
                                            $this->setException('error', 11, 'Add relations failed: ' . lmb_log::getLogMessage(true), $tabid, $sdatid, $fieldid);
                                        }
                                    }
                                }
                            } else {
                                $relation = init_relation($tabid, $fieldid, $datid, $verkn_add_ids);

                                if ($verkn_add_ids && !set_relation($relation)) {
                                    $errormsg = lmb_log::getLogMessage(true);
                                    //workaround for existing relations
                                    if (!(strpos($errormsg, 'already joined') !== false)) {
                                        $this->setException('error', 11, 'Add relations failed: ' . lmb_log::getLogMessage(true), $tabid, $sdatid, $fieldid);
                                    }
                                }
                            }


                        }

                        // delete ids missing on partner system
                        if ($verkn_del_ids = array_diff($existing_rel, $intersect)) {
                            $relation = init_relation($tabid, $fieldid, $datid, null, $verkn_del_ids);
                            if ($verkn_del_ids && !set_relation($relation)) {
                                //TODO: $this->setException('error', 11, 'Remove relations failed: ' . lmb_log::getLogMessage(true), $tabid, $sdatid, $fieldid);
                            }
                        }
                    }
                }
            }
        } catch (Throwable $t) {
            LimbasLogger::error('Create relations error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }


        //Update relations
        try {
            foreach ($syncdata['data'] as $tabid => $data) {

                //TODO: no delete entry for relation tables is set!!!

                //update / delete records
                foreach ($data as $id => $values) {
                    if ($id == 0) {
                        $this->setException('error', 6, 'Record not found', $tabid, $id);
                        continue;
                    }

                    $sid = $id;
                    if ($this->is_main) {
                        $id = $this->convertID($tabid, $id);
                        if ($id === false) {
                            //if not deleted anyway
                            if ($values !== false) {
                                $this->setException('error', 6, 'ID resolve failed', $tabid, $sid);
                            }
                            continue;
                        }
                    }

                    //deleted
                    if ($values === false) {
                        continue;
                    } //changed and not deleted
                    else if (!array_key_exists($tabid, $deleted) || !in_array($id, $deleted[$tabid])) {
                        $update = array();

                        //Update allowed
                        if ($this->is_main) {
                            $fids = $this->template[$tabid]['master'];
                        } else {
                            $fids = $this->template[$tabid]['slave'];
                        }


                        foreach ($values as $fieldid => $value) {
                            if ($fieldid == 'sys') {
                                $this->handleSystemFields($tabid, $id, $value, $update);
                                continue;
                            }

                            if (!in_array($fieldid, $fids)) {
                                continue;
                            }

                            $special = $this->applyFieldType($tabid, $fieldid, $id, $value['value']);
                            if ($special === true) {
                                if ($this->hasConflict($tabid, $id, $fieldid, $value)) {
                                    continue;
                                }
                                $update["$tabid,$fieldid,$id"] = $value['value'] . '';
                            } else {
                                continue;
                            }
                        }
                        if (!empty($update) && update_data($update) !== true) {
                            $this->setException('error', 3, 'Update failed: ' . lmb_log::getLogMessage(true), $tabid, $sid);
                        }
                    }
                }

            }
        } catch (Throwable $t) {
            LimbasLogger::error('Update relations error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }

        return $newids;
    }


    /**
     * Synchronizes special system fields independent of data fields
     *
     * @param int $tabid
     * @param int $id
     * @param array $systemdata
     */
    protected function handleSystemFields($tabid, $id, $systemdata, &$update, $new = false)
    {

        if (array_key_exists('MID', $systemdata) && !empty($systemdata['MID'])) {
            $update["$tabid,LMB_MID,$id"] = $systemdata['MID'];
        }
        if (array_key_exists('DEL', $systemdata)) {
            $update["$tabid,DEL,$id"] = ($systemdata['DEL']) ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE;
        }
        if (array_key_exists('LMB_STATUS', $systemdata)) {
            $update["$tabid,LMB_STATUS,$id"] = intval($systemdata['LMB_STATUS']);
        }


        if (array_key_exists('VID', $systemdata) && !empty($systemdata['VID'])) {
            $update["$tabid,VID,$id"] = $systemdata['VID'];
        }
        if (array_key_exists('VDESC', $systemdata) && !empty($systemdata['VDESC'])) {
            $update["$tabid,VDESC,$id"] = "'" . $systemdata['VDESC'] . "'";
        }


        if (!$new) {
            $vpid = null;
            if (array_key_exists('VPID', $systemdata) && !empty(trim($systemdata['VPID']))) {
                $vpid = $systemdata['VPID'];
                if($this->is_main) {
                    $vpid = $this->convertID($tabid, $systemdata['VPID']);
                }
                if (!empty($vpid)) {
                    $update["$tabid,VPID,$id"] = $vpid;
                }
            }
            if (!empty($vpid) && array_key_exists('VACT', $systemdata)) {
                $update["$tabid,VACT,$id"] = $systemdata['VACT'] ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE;

                //set all old versions to if updating active version
                if ($systemdata['VACT'] && $vpid !== null) {
                    $this->setVersionsInactive($tabid, $vpid);
                }
            }
        }


        if (array_key_exists('LMB_VALIDFROM', $systemdata) && !empty($systemdata['LMB_VALIDFROM'])) {
            $update["$tabid,LMB_VALIDFROM,$id"] = "'" . $systemdata['LMB_VALIDFROM'] . "'";
        }
        if (array_key_exists('LMB_VALIDTO', $systemdata) && !empty($systemdata['LMB_VALIDTO'])) {
            $update["$tabid,LMB_VALIDTO,$id"] = "'" . $systemdata['LMB_VALIDTO'] . "'";
        }

        if (array_key_exists('LID', $systemdata) && !empty($systemdata['LID'])) {
            $update["$tabid,LID,$id"] = $systemdata['LID'];
        }

    }


    /**
     * Converts master ID from / to slave ID for defined slave
     *
     * @param int $tabid
     * @param int $cid
     * @param int $dir 0 = slave ID to master ID; 1 = master ID to slave ID
     * @return int|false converted id or false if not found
     */
    protected function convertID($tabid, $cid, $dir = 0): bool|int
    {
        global $db;
        global $gtab;

        //if table is global keep same ids on main and client and don't convert
        if ($this->template[$tabid]['global']) {
            return intval($cid);
        }


        $table = dbf_4($gtab['table'][$tabid]);
        $keyfield = $gtab['keyfield'][$tabid];


        if ($cid === 'new') {
            //LimbasLogger::warning('Unexpected "new" as ID. Table: ' . $table);
            return false;
        }

        if ($dir == 1) {
            //master ID to slave ID
            $sql = "SELECT LMB_SYNC_ID AS CID FROM $table WHERE LMB_SYNC_SLAVE = {$this->current_client} AND $keyfield = $cid";
        } else {
            //slave ID to master ID
            $sql = "SELECT $keyfield AS CID FROM $table WHERE LMB_SYNC_SLAVE = {$this->current_client} AND LMB_SYNC_ID = $cid";
        }
        $rs = lmbdb_exec($db, $sql);

        if (lmbdb_fetch_row($rs)) {
            return intval(lmbdb_result($rs, 'CID'));
        }
        return false;
    }


    /**
     * Returns the real key id of a relation table
     *
     * @param int $tabid
     * @param     $id
     * @param     $verkn_id
     *
     * @return int|false converted id or false if not found
     */
    protected function getRelKeyID($tabid, $id, $verkn_id): bool|int
    {
        global $db;
        global $gtab;

        $table = dbf_4($gtab['table'][$tabid]);
        $keyfield = $gtab['keyfield'][$tabid];
        $sql = "SELECT $keyfield AS CID FROM $table WHERE ID = $id AND VERKN_ID = $verkn_id";
        $rs = lmbdb_exec($db, $sql);

        if (lmbdb_fetch_row($rs)) {
            return lmbdb_result($rs, 'CID');
        }
        return false;
    }

    /**
     * Set all old versions to inactive
     *
     * @param int $tabid
     * @param     $id
     *
     * @return void converted id or false if not found
     */
    protected function setVersionsInactive($tabid, $id)
    {
        global $db;
        global $gtab;

        $table = dbf_4($gtab['table'][$tabid]);
        $sql = "UPDATE $table SET VACT = " . LMB_DBDEF_FALSE . " WHERE VPID = $id";
        $rs = lmbdb_exec($db, $sql);
    }

    /**
     * Collects all relevant fields of a table for synchronization
     *
     * @param int $tabid
     * @return array
     * @throws Exception
     */
    protected function getSyncFields($tabid): array
    {
        if (!array_key_exists($tabid, $this->template)) {
            throw new Exception("Tabid $tabid not in sync template {$this->template}!");
        }

        if (is_array($this->template[$tabid])) {
            if ($this->is_main) {
                return $this->template[$tabid]['master'];
            } else {
                return $this->template[$tabid]['slave'];
            }
        }

        return array();
    }


    /**
     * Retrieves all needed data of one record
     *
     * @param int $tabid
     * @param int $id
     * @param array $field_ids
     * @param int $timestamp
     * @return array | false
     */
    protected function getData($tabid, $id, $field_ids, $timestamp = null, bool $skipRelations = false): bool|array
    {
        global $gfield;

        //$gsr[$tabid]['ID'] = $id;
        $filter['relationval'][$tabid] = 1;
        $filter['getlongval'][$tabid] = 1;
        $filter['status'][$tabid] = -1;
        $filter["validity"][$tabid] = 'all';

        //TODO: filter for lmb_sync_slave?
        if ($field_ids === 'all') {
            $gresult = get_gresult($tabid, 1, $filter, null, null, null, $id);
        } else {
            $gresult = get_gresult($tabid, 1, $filter, null, null, array($tabid => $field_ids), $id);

            // ensure that all field ids are present
            foreach ($field_ids as $field_id) {
                //TODO: Log field not in template
                if (in_array($field_id, $gfield[$tabid]['field_id']) && !array_key_exists($field_id, $gresult[$tabid])) {
                    $gresult[$tabid][$field_id] = array();
                }
            }
        }

        if ($gresult[$tabid]['res_count'] <= 0) {
            $this->setException('error', 9, 'No record data', $tabid, $id);
            return false;
        }

        $data = array();
        foreach ($gresult[$tabid] as $fieldid => $value) {
            if (is_numeric($fieldid)) {
                if ($this->prepareFieldType($tabid, $fieldid, $value[0], $gresult, $id, $skipRelations)) {
                    $data[$fieldid] = array('value' => $value[0], 'time' => $timestamp);
                }
            } else {
                //get all limbas system fields
                if (!isset($data['sys'])) {
                    $data['sys'] = array();
                }
                
                // convert id on master to slave id before send
                if ($this->is_main && $fieldid === 'VPID' && !empty(trim($value[0]))) {
                    $value[0] = $this->convertID($tabid, $value[0], 1);
                }
                
                $data['sys'][$fieldid] = $value[0];
            }
        }
        return $data;
    }


    /**
     * Checks if a special field type is allowed for synchronization and prepare its data
     *
     * @param int $tabid
     * @param int $fieldid
     * @param mixed $value
     * @param array $gresult
     * @param $id
     * @param bool $skipRelations
     * @return bool
     */
    protected function prepareFieldType($tabid, $fieldid, &$value, $gresult, $id, bool $skipRelations = false): bool
    {
        global $gtab;
        global $gfield;

        if (!is_array($gfield[$tabid])) {
            return false;
        }

        if (array_key_exists('sys', $gfield[$tabid]) && is_array($gfield[$tabid]['sys']) && array_key_exists($fieldid, $gfield[$tabid]['sys']) && $gfield[$tabid]['sys'][$fieldid]) {
            return false;
        }

        switch ($gfield[$tabid]['data_type'][$fieldid]) {
            //Validity
            case 53:
            case 54:
                //Multitenant
            case 52:
                //sync slave
            case 51:
                //version comment
            case 43:
                //erst / edit user / date
            case 34:
            case 35:
            case 36:
            case 37:
                //ID field
            case 22:
                //Upload
            case 13:
            case 48:
                //TODO: PHP-Argument
            case 31:
                //SQL-Argument
            case 47:
                return false; // ignore
            //Relation: only valid if both, table and linked table, are synchronized
            case 27:
            case 24:
                //special case: LEVEL ID of DMS

                //if relation table is parameterized
                $vtabid = $this->getVerknTabId($tabid, $fieldid);

                $levelIds = false;
                if ($vtabid === null) {
                    $levelIds = $this->getVerknLevelIds($tabid, $fieldid, $value);
                }
            // break is intentionally missing
            // 1:n direct
            case 25:
                //Backward relation
            case 23:
                if ($skipRelations) {
                    return false;
                }
                $verkntab = $gfield[$tabid]['verkntabid'][$fieldid];

                //if relation table is synchronized
                if (is_array($this->template) && !array_key_exists($verkntab, $this->template)) {
                    return false;
                }

                //if parameterized
                if (!empty($vtabid) && $gtab['datasync'][$vtabid]) {
                    $matched_values = [];
                    foreach ($value as $verknid) {
                        $realID = $this->getRelKeyID($vtabid, $id, $verknid);
                        if ($realID) {
                            $matched_values[$realID] = $verknid;
                        }
                    }
                    $value = $matched_values;
                }

                if ($this->is_main) {
                    foreach ($value as &$verknid) {
                        $orgVerknId = $verknid;
                        $verknid = $this->convertID($verkntab, $verknid, 1);
                        // related entry is new and doesn't have a client id yet
                        if(empty($verknid)) {
                            $verknid = ['id'=>$orgVerknId,'new'=>true];
                        }
                    }
                }

                if ($levelIds !== false && is_array($levelIds)) {
                    foreach ($value as &$verknid) {
                        $levelId = null;
                        $orgVerknId = $verknid;
                        if(is_array($verknid)) {
                            $orgVerknId = $verknid['id'];
                        }
                        if (array_key_exists($orgVerknId, $levelIds)) {
                            $levelId = $levelIds[$orgVerknId];
                        }
                        $verknid = ['LID' => $levelId, 'id' => $verknid];
                    }
                }


                break;
            //Currency
            case 30:
                if (is_array($value)) {
                    $value = $value['V'] . ' ' . $value['C'];
                }
                break;
            //Auswahl (checkbox), Auswahl (multiselect), Auswahl (ajax), Attribute
            case 18:
            case 31:
            case 32:
            case 46:
                if ($value > 0) {
                    $func = 'cftyp_' . $gfield[$tabid]['funcid'][$fieldid];
                    $values = $func(0, $fieldid, $tabid, 5, $gresult);
                    if (!is_array($values)) {
                        $values = array();
                    }
                    $value = array();

                    //Attribut Werte
                    if ($gfield[$tabid]['data_type'][$fieldid] == 46) {
                        $value['values'] = [];
                    }

                    foreach ($values as $wid => $text) {
                        if (is_numeric($wid)) {
                            $value[] = $wid;
                            if ($gfield[$tabid]['data_type'][$fieldid] == 46) {
                                $value['values'][$wid] = $this->getAttributeValue($tabid, $fieldid, $id, $wid);
                            }
                        }
                    }


                } else {
                    $value = null;
                }
                break;
            // user group list
            case 38:
                $func = 'cftyp_' . $gfield[$tabid]['funcid'][$fieldid];
                $values = $func(0, $fieldid, $tabid, 6, $gresult);
                if (is_array($values)) {
                    $value = [];
                    foreach ($values as $ugValue) {
                        $value[] = $ugValue['id'] . '_' . lmb_substr($ugValue['typ'], 0, 1);
                    }
                }
                break;
        }

        return true;
    }


    /**
     * Applies data of special fields
     *
     * @param int $tabid
     * @param int $fieldid
     * @param int $ID
     * @param mixed $value
     * @return bool|string
     * @throws Exception
     */
    protected function applyFieldType($tabid, $fieldid, $ID, &$value): bool|string
    {
        global $gfield;

        if (!is_array($gfield[$tabid])) {
            return false;
        }

        if (array_key_exists('sys', $gfield[$tabid]) && is_array($gfield[$tabid]['sys']) && array_key_exists($fieldid, $gfield[$tabid]['sys']) && $gfield[$tabid]['sys'][$fieldid]) {
            return false;
        }

        if (array_key_exists('argument', $gfield[$tabid]) && array_key_exists($fieldid, $gfield[$tabid]['argument'])) {
            return false;
        }

        $filter = [];
        $filter['relationval'][$tabid] = 1;
        $filter['status'][$tabid] = -1;
        $filter['validity'][$tabid] = 'all';


        switch ($gfield[$tabid]['data_type'][$fieldid]) {
            //Validity
            case 53:
            case 54:
                //Multitenant
            case 52:
                //sync slave
            case 51:
                //version comment
            case 43:
                //erst / edit user / date
            case 34:
            case 35:
            case 36:
            case 37:
                //ID field
            case 22:
                //Upload
            case 13:
            case 48:
                //TODO: PHP-Argument
            case 31:
                //SQL-Argument
            case 47:
                return false; // ignore
            //Relation: only valid if both, table and linked table, are synchronized
            case 27:
            case 24:
            case 25:
                //Backward relation
            case 23:
                $verkntab = $gfield[$tabid]['verkntabid'][$fieldid];
                if (!array_key_exists($verkntab, $this->template)) {
                    return false;
                }
                return 'rel';
            //Currency
            case 30:
                if (is_array($value)) {
                    $value = $value['V'] . ' ' . $value['C'];
                }
                break;
            //Auswahl (checkbox), Auswahl (multiselect), Auswahl (ajax)
            case 18:
            case 31:
            case 32:
            case 46:
                if (is_array($value)) {
                    if ($this->hasConflict($tabid, $ID, $fieldid, $value)) {
                        return true;
                    }

                    $wvalues = [];
                    if (array_key_exists('values', $value)) {
                        $wvalues = $value['values'];
                        unset($value['values']);
                    }


                    //compare exsting values only if attribute or ajax; others are already handled by uftyp
                    if ($gfield[$tabid]['data_type'][$fieldid] == 32 || $gfield[$tabid]['data_type'][$fieldid] == 46) {

                        $gsr[$tabid]['ID'] = $ID;
                        $gresult = get_gresult($tabid, 1, $filter, $gsr, null, array($tabid => array($fieldid)));

                        if ($gresult[$tabid]['res_count'] > 0) {
                            $existing = array();
                            $this->prepareFieldType($tabid, $fieldid, $existing, $gresult, $ID);
                            if (array_key_exists('values', $existing)) {
                                unset($existing['values']);
                            }

                            $fvalue = [];
                            $removes = array_diff($existing, $value);
                            foreach ($removes as $rv) {
                                $fvalue[] = 'd' . $rv;
                            }

                            $adds = array_diff($value, $existing);
                            foreach ($adds as $av) {
                                $fvalue[] = 'a' . $av;
                            }

                        }

                        $value = $fvalue;

                    }

                    if ($gfield[$tabid]['data_type'][$fieldid] == 32) {
                        //$value = ';' . implode(';', $value);
                    }


                    uftyp_23($tabid, $fieldid, $ID, $value);


                    if (!empty($wvalues)) {
                        foreach ($wvalues as $wid => $val) {
                            $this->setAttributeValue($tabid, $fieldid, $ID, $wid, $val);
                        }
                    }


                }
                return false;
            // user group list
            case 38:

                // get existing values
                $func = 'cftyp_' . $gfield[$tabid]['funcid'][$fieldid];
                $gsr = [];
                $gsr[$tabid]['ID'] = $ID;
                $gresult = get_gresult($tabid, 1, $filter, $gsr, null, array($tabid => array($fieldid)));
                $existing = $func(0, $fieldid, $tabid, 6, $gresult);

                $existingValues = [];
                if (is_array($existing)) {
                    foreach ($existing as $ugValue) {
                        $existingValues[] = $ugValue['id'] . '_' . lmb_substr($ugValue['typ'], 0, 1);
                    }
                }

                if (!is_array($value)) {
                    $value = [];
                }


                $diff = array_merge(array_diff($existingValues, $value), array_diff($value, $existingValues));
                if (!empty($diff)) {
                    $updateFunc = 'uftyp_' . $gfield[$tabid]['funcid'][$fieldid];
                    foreach ($diff as $ug) {
                        $updateFunc($tabid, $fieldid, $ID, $ug);
                    }
                }

                return false;
        }

        return true;
    }


    /**
     * Collects values of attribute field type
     *
     * @param $tabid
     * @param $fieldid
     * @param $datid
     * @param $wid
     * @return string
     */
    protected function getAttributeValue($tabid, $fieldid, $datid, $wid): string
    {
        global $db;

        $sqlquery1 = "SELECT VALUE_STRING,VALUE_NUM,VALUE_DATE FROM LMB_ATTRIBUTE_D WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabid AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldid AND LMB_ATTRIBUTE_D.DAT_ID = $datid";


        $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);

        while (lmbdb_fetch_row($rs1)) {

            $string_value = lmbdb_result($rs1, 'VALUE_STRING');
            $num_value = lmbdb_result($rs1, 'VALUE_NUM');
            $date_value = lmbdb_result($rs1, 'VALUE_DATE');

            if (!empty($string_value)) {
                return 's' . $string_value;
            } elseif (!empty($date_value)) {
                return 'd' . $date_value;
            } elseif (!empty($num_value) || $num_value === 0 || $num_value === '0') {
                return 'n' . $num_value;
            }
        }

        return '';
    }


    /**
     * Sets the values of attribute field type
     *
     * @param $tabid
     * @param $fieldid
     * @param $datid
     * @param $wid
     * @param $value
     * @return void
     */
    protected function setAttributeValue($tabid, $fieldid, $datid, $wid, $value)
    {
        global $db;

        if (empty($value)) {
            $sqlquery1 = "UPDATE LMB_ATTRIBUTE_D SET VALUE_STRING = '', VALUE_DATE = " . LMB_DBDEF_NULL . ", VALUE_NUM = " . LMB_DBDEF_NULL . "  WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabid AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldid AND LMB_ATTRIBUTE_D.DAT_ID = $datid";
            $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
            return;
        }

        $type = $value[0];
        $value = substr($value, 1);

        $update_field = '';
        switch ($type) {
            case 's':
                $update_field = 'VALUE_STRING';
                $value = "'$value'";
                break;
            case 'd':
                $update_field = 'VALUE_DATE';
                $value = "'$value'";
                break;
            case 'n':
                $update_field = 'VALUE_NUM';
                break;
        }

        if (empty($update_field)) {
            return;
        }


        $sqlquery1 = "UPDATE LMB_ATTRIBUTE_D SET $update_field = $value WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabid AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldid AND LMB_ATTRIBUTE_D.DAT_ID = $datid";
        $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
    }


    /**
     * Get the corresponding folder level id of a relation
     *
     * @param $tabid
     * @param $fieldid
     * @param $value
     * @return array|false
     */
    protected function getVerknLevelIds($tabid, $fieldid, &$value): bool|array
    {
        global $db,
               $gtab,
               $gfield;

        if (empty($value)) {
            return false;
        }

        $md5_tab = $gfield[$tabid]['md5tab'][$fieldid];

        $ldmsTabId = (int)$gtab['argresult_id']['LDMS_FILES'];
        $verknTabId = null;
        if (array_key_exists($fieldid, $gfield[$tabid]['verkntabid'])) {
            $verknTabId = (int)$gfield[$tabid]['verkntabid'][$fieldid];
        }

        if ($verknTabId === $ldmsTabId) {

            $sql = 'SELECT LID, VERKN_ID FROM ' . $md5_tab . ' WHERE VERKN_ID IN (' . implode(',', $value) . ')';

            $rs = lmbdb_exec($db, $sql);

            $levelIds = [];
            while (lmbdb_fetch_row($rs)) {
                $levelIds[lmbdb_result($rs, 'VERKN_ID')] = lmbdb_result($rs, 'LID');
            }
            if (empty($levelIds)) {
                return false;
            }
            return $levelIds;
        }

        return false;
    }

    /**
     * Gets the id of the related table of a relation field if it is parameterized
     *
     * @param $tabid
     * @param $fieldid
     * @return mixed|null
     */
    protected function getVerknTabId($tabid, $fieldid): mixed
    {
        global $gfield;

        $vtabid = null;

        if (array_key_exists('verknparams', $gfield[$tabid]) && array_key_exists($fieldid, $gfield[$tabid]['verknparams'])) {
            $vtabid = $gfield[$tabid]['verknparams'][$fieldid];
        }

        return $vtabid;
    }


    /**
     * Checks if same record/field was changed on both, slave and master
     * mode 0 = master wins
     * mode 1 = slave wins
     * mode 2 = latest wins
     * mode 3 = manual intervention
     *
     * @param int $tabid
     * @param int $datid
     * @param int $fieldid
     * @param array $value ['time' => ..., 'value' => ...]
     * @return bool
     * @throws Exception
     */
    protected function hasConflict($tabid, $datid, $fieldid = 0, $value = array()): bool
    {
        global $db;

        $sqlquery = "SELECT TYPE, TABID, FIELDID, DATID, SLAVE_ID, SLAVE_DATID, ERSTDATUM FROM LMB_SYNC_CACHE WHERE TABID = $tabid AND FIELDID = $fieldid AND DATID = $datid AND TYPE = 3" . (($this->is_main) ? "AND SLAVE_ID = {$this->current_client}" : '');
        $rs = lmbdb_exec($db, $sqlquery);
        if (lmbdb_num_rows($rs) > 0) {
            $curval = $this->getData($tabid, $datid, array($fieldid));
            $curval = $curval[$fieldid];
            if ($curval['value'] == $value['value']) {
                return true;
            }
            switch ($this->template['conflict_mode']) {
                case 0:
                    if ($this->is_main) {
                        return true;
                    }
                    break;
                case 1:
                    if (!$this->is_main) {
                        return true;
                    }
                    break;
                case 2:
                    lmbdb_fetch_row($rs);
                    $erstdatum = lmbdb_result($rs, 'ERSTDATUM');
                    $dt = new DateTime($erstdatum);
                    $erstdatum = $dt->getTimestamp();
                    if ($erstdatum > $value['time']) {
                        return true;
                    }
                    break;
                case 3:
                    $this->setException('conflict', 1, 'Ungelöster Konflikt', $tabid, $datid, $fieldid);
                    return true;
            }
        }
        return false;
    }


    /**
     * Writes all cached exceptions into the database
     *
     * @return void
     */
    protected function handleExceptions()
    {
        global $db;
        global $gtab;

        $isLimbasTable = array_key_exists('LMB_SYNC_LOG', $gtab['argresult_id']);

        if (lmb_count($this->sync_exceptions) > 0) {
            foreach ($this->sync_exceptions as $type => $exceptions) {
                foreach ($exceptions as $tabid => $records) {
                    foreach ($records as $datid => $record) {
                        foreach ($record as $fieldid => $field) {
                            foreach ($field as $msg) {

                                if ($datid === 'new') {
                                    continue;
                                }

                                if ($msg['origin'] == 1) {
                                    $datid = $this->convertID($tabid, $datid);
                                }
                                $datid = parse_db_int($datid); //todo 'new' passed

                                if ($isLimbasTable) {
                                    $ID = next_db_id('lmb_sync_log');
                                    // TODO ID, slaveid 2x
                                    $sqlquery = "INSERT INTO LMB_SYNC_LOG (ID,TYPE,TABID,DATID,FIELDID,ORIGIN,SLAVEID,ERRORCODE,MESSAGE) VALUES ($ID,'$type',$tabid,$datid,$fieldid,{$msg['origin']},$this->current_client,{$msg['code']},'{$msg['msg']}')";
                                } else {
                                    $sqlquery = "INSERT INTO LMB_SYNC_LOG (TYPE,TABID,DATID,FIELDID,ORIGIN,SLAVEID,ERRORCODE,MESSAGE) VALUES ('$type',$tabid,$datid,$fieldid,{$msg['origin']},$this->current_client,{$msg['code']},'{$msg['msg']}')";
                                }
                                $rs = lmbdb_exec($db, $sqlquery);
                            }
                        }
                    }
                }
            }
        }
    }


    /**
     * @param string $type
     * @param int $code
     * @param string $msg
     * @param int $tabid
     * @param int $datid
     * @param int $fieldid
     */
    protected function setException($type, $code, $msg, $tabid = 0, $datid = 0, $fieldid = 0)
    {
        $this->sync_exceptions[$type][$tabid][$datid][$fieldid][] = array('code' => $code, 'msg' => $msg, 'origin' => ($this->is_main) ? 0 : 1);
    }


    /**
     * Writes an entry to the sync cache
     *
     * @param $tabid
     * @param $fieldid
     * @param $datid
     * @param $type
     * @return void
     */
    protected function putInCache($tabid, $fieldid, $datid, $type)
    {
        global $db;

        //TODO: insert current client in global log
        //TODO: what happens if master deletes record and client has still updates?

        $nextID = next_db_id('LMB_SYNC_CACHE');

        if ($datid === 'new') {
            //$this->setException('warning',12,'Unexpected "new" as id',$tabid,0,$fieldid);
            return;
        }

        $sqlquery = "INSERT INTO LMB_SYNC_CACHE (ID,TABID,FIELDID,DATID,SLAVE_ID,SLAVE_DATID,TYPE) VALUES($nextID,$tabid,$fieldid,$datid,0,$datid,$type)";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $GLOBALS['action'], __FILE__, __LINE__);
    }

    /**
     * @return int
     */
    public function getCacheTimestamp(): int
    {
        return $this->cacheTimestamp;
    }


    /**
     * @param bool $status
     * @return void
     */
    protected function endTransaction(bool $status)
    {
        global $umgvar;

        if ($umgvar['sync_transaction']) {
            $GLOBALS["lmb_transaction"] = 1;
            lmb_EndTransaction($status);
        }
    }
    
    
    private function recordExists(int $tabId, int $id): bool {
        global $gtab;
        
        $db = Database::get();
        
        try {
            $table = dbf_4($gtab['table'][$tabId]);
            $keyfield = $gtab['keyfield'][$tabId];

            $sql = "SELECT $keyfield AS CID FROM $table WHERE $keyfield = $id";
            $rs = lmbdb_exec($db, $sql);
            if (lmbdb_fetch_row($rs)) {
                return true;
            }
        } catch (Throwable){}
        
        return false;
    }
    
}
