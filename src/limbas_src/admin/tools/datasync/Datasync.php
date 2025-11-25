<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync;

/*
 * Socket Flow
 * 
 * On Main -> runSyncWithClientSocket -> Soap Request an Client -> 
 * On Client -> Incoming Soap Request -> startSocket -> Cron -> runSyncWithMainSocket
 * 
 */


use DateTime;
use Exception;
use Limbas\admin\tools\datasync\Data\DatasyncNewRecord;
use Limbas\admin\tools\datasync\Data\DatasyncRecordData;
use Limbas\admin\tools\datasync\Data\DatasyncRelation;
use Limbas\admin\tools\datasync\Data\DatasyncTableData;
use Limbas\admin\tools\datasync\Enums\ConflictMode;
use Limbas\admin\tools\datasync\Enums\DatasyncError;
use Limbas\admin\tools\datasync\Enums\DataSyncType;
use Limbas\admin\tools\datasync\Data\DatasyncData;
use Limbas\admin\tools\datasync\Traits\HandleData;
use Limbas\admin\tools\datasync\Traits\HandleIds;
use Limbas\lib\db\Database;
use Limbas\lib\db\functions\Dbf;
use Limbas\lib\general\Log\Log;
use Throwable;

abstract class Datasync
{
    use HandleIds, HandleData;

    /**
     * @var bool defines if the current system is a master or client
     */
    protected bool $isMain;

    /**
     * @var array synchronisation template
     */
    protected array $template;

    /**
     * @var ?int id of the current client
     */
    protected ?int $currentClient = null;

    /**
     * @var int timestamp at which the sync started. Used as key of the process.
     */
    protected int $cacheTimestamp = 0;

    protected array $tableIndex;


    /**
     * @param array $template
     */
    public function __construct(array $template)
    {
        $this->template = $template;
    }


    /**
     * @return int
     */
    public function getCacheTimestamp(): int
    {
        return $this->cacheTimestamp;
    }


    /**
     * Prepares an array based on lmb_sync_cache which can be applied later
     *
     * @return array|false
     */
    protected function collectChangedData(): bool|DatasyncData
    {
        global $umgvar;
        global $gtab;

        $db = Database::get();
        //TODO: Only tables of selected sync template

        $maxRecords = false;
        if (array_key_exists('sync_max_records', $umgvar)) {
            if (!empty($umgvar['sync_max_records'])) {
                $maxRecords = intval($umgvar['sync_max_records']);
            }
        }

        try {
            [$syncCount, $recordCount] = $this->countSyncCache();

            $this->cacheTimestamp = time();
            $this->setSyncRecords($maxRecords);

            //ORDER: delete (1) -> created (2) -> changed (3)
            $sqlquery = 'SELECT ID, TYPE, TABID, FIELDID, DATID, SLAVE_ID, SLAVE_DATID FROM LMB_SYNC_CACHE WHERE PROCESS_KEY = ' . $this->cacheTimestamp . ' ORDER BY TYPE';

            $rs = lmbdb_exec($db, $sqlquery);

            $syncFields = array();
            $rowCount = lmbdb_num_rows($rs);

            if ($this->isMain) {
                DatasyncLog::info('Collecting data on master [' . $rowCount . ' / ' . $syncCount . ' / ' . $recordCount . ']');
            } else {
                DatasyncLog::info('Collecting data on client [' . $rowCount . ' / ' . $syncCount . ' / ' . $recordCount . ']');
            }

            $datasyncData = new DatasyncData($this->cacheTimestamp);

            if ($rowCount <= 0) {
                return $datasyncData;
            }


            while (lmbdb_fetch_row($rs)) {
                $syncCacheId = intval(lmbdb_result($rs, 'ID'));

                $tabId = intval(lmbdb_result($rs, 'TABID'));
                $fieldId = intval(lmbdb_result($rs, 'FIELDID'));
                $mainRecordId = $this->isMain ? intval(lmbdb_result($rs, 'DATID')) : null;
                $clientRecordId = $this->isMain ? (intval(lmbdb_result($rs, 'SLAVE_DATID')) ?: null) : intval(lmbdb_result($rs, 'DATID'));
                $type = DataSyncType::tryFrom(lmbdb_result($rs, 'TYPE')) ?? DataSyncType::UNKNOWN;
                $createdAt = lmbdb_result($rs, 'ERSTDATUM');

                // get highest timestamp
                $dt = new DateTime($createdAt);
                $createdAt = $dt->getTimestamp();

                if (!array_key_exists($tabId, $syncFields)) {
                    //cache sync fields per table
                    $syncFields[$tabId] = $this->getSyncFields($tabId);
                }

                //TODO: clientRecord Id nur einmal pro Record berechnen => Fehler erst am Ende ausgeben, da ggf. durch anderen Eintrag aufgelöst
                // no client record id is assigned and dataset is deleted or updated
                if ($this->isMain && empty($clientRecordId) && $type !== DataSyncType::CREATE && !$this->template[$tabId]['global']) {
                    //try to resolve the client record id
                    $clientRecordId = $this->convertID($tabId, $mainRecordId, $this->currentClient, self::MAIN_TO_CLIENT);

                    if (empty($clientRecordId)) {

                        $datasyncTableData = $datasyncData->getTableData($tabId);
                        if (!empty($datasyncTableData)) {
                            $datasyncRecordData = $datasyncTableData->getRecordData($mainRecordId);
                            if ($type === DataSyncType::UPDATE && $datasyncRecordData->created) {
                                // if this update belongs to a created entry, ignore it
                                $datasyncRecordData->addUpdate($fieldId, $syncCacheId, $createdAt);
                                continue;
                            } elseif ($type === DataSyncType::DELETE && $datasyncRecordData->created) {
                                // if this update belongs to a created entry, ignore it
                                $datasyncRecordData->setDeleted($syncCacheId);
                                continue;
                            }
                        }


                        Database::update('LMB_SYNC_CACHE', ['PROCESS_KEY' => null], ['ID' => $syncCacheId]);
                        $datasyncData->addError($syncCacheId, DatasyncError::NO_CLIENT_DATA_ID);
                        continue;
                    }
                }

                // TODO: test if main / client from template is used
                $datasyncTableData = $datasyncData->getOrCreateTableData($tabId);
                $datasyncRecordData = $datasyncTableData->getOrCreateRecordData($mainRecordId, $clientRecordId);


                //deleted
                if ($type === DataSyncType::DELETE) {
                    $datasyncRecordData->setDeleted($syncCacheId);
                } //created & not deleted
                elseif ($type === DataSyncType::CREATE) {


                    $datasyncRecordData->setCreated($syncCacheId);
                    if ($datasyncRecordData->deleted) {
                        continue;
                    }

                    // in case of 1:1 relations all related tables are send
                    if (array_key_exists('raverkn', $gtab) &&
                        is_array($gtab['raverkn']) &&
                        array_key_exists($tabId, $gtab['raverkn']) &&
                        is_array($gtab['raverkn'][$tabId])) {

                        foreach ($gtab['raverkn'][$tabId] as $relatedTabId) {

                            if (!array_key_exists($relatedTabId, $this->template)) {
                                continue;
                            }

                            if ($relatedTabId !== $tabId) {
                                $relatedDatasyncTableData = $datasyncData->getOrCreateTableData($relatedTabId);
                                $relatedDatasyncTableData->relatedTableId = $tabId;
                                $relatedDatasyncRecordData = $datasyncTableData->getOrCreateRecordData($mainRecordId, $clientRecordId);
                                $relatedDatasyncRecordData->setCreated($syncCacheId);
                            }
                        }

                    }

                } //changed & not deleted
                elseif ($type === DataSyncType::UPDATE) {

                    if (!in_array($fieldId, $syncFields[$tabId]) && $fieldId !== 0) {
                        $datasyncData->addError($syncCacheId, DatasyncError::FIELD_NOT_MARKED_AS_SYNC);
                        continue;
                    }

                    $datasyncRecordData->addUpdate($fieldId, $syncCacheId, $createdAt);
                }
            }

            $datasyncData->fill($this->currentClient);
            return $datasyncData;
        } catch (Throwable $t) {
            DatasyncLog::error('Data collection error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }
    }

    /**
     * Applies changes out of prepared array.
     * On slave: ids of created records are collected and send back
     * On master: all records get their corresponding lmb_sync_slave and lmb_sync_id
     *
     * @param DatasyncData $datasyncData
     * @return bool
     */
    protected function applyChangedData(DatasyncData $datasyncData): bool
    {
        global $db;
        global $gtab;

        DatasyncLog::info('Applying changed data');

        //get deleted records => ignore all other actions 
        $rs = Database::select('LMB_SYNC_CACHE', ['TYPE', 'TABID', 'DATID', 'SLAVE_ID', 'SLAVE_DATID'], ['TYPE' => DataSyncType::DELETE->value]);
        if (!$rs) {
            DatasyncLog::error('Applying data: fetching deleted failed');
            return false;
        }

        $deletedIds = [];
        while (lmbdb_fetch_row($rs)) {
            $tabId = lmbdb_result($rs, 'TABID');

            if (!array_key_exists($tabId, $this->template)) {
                continue;
            }

            if (!array_key_exists($tabId, $deletedIds)) {
                $deletedIds[$tabId] = [];
            }

            $deletedIds[$tabId][] = intval(lmbdb_result($rs, 'DATID'));
        }

        try {
            foreach ($datasyncData->tableData as $tabId => $tableData) {

                //check if table is a parameterized relation table //TODO: other way than name check
                if (strtoupper(substr($gtab['table'][$tabId], 0, 5)) === 'VERK_') {
                    continue;
                }

                if (!array_key_exists($tabId, $this->template)) {
                    continue;
                }

                /** @var DatasyncRecordData $recordData */
                foreach ($tableData->records as $recordKey => $recordData) {


                    if ($recordData->deleted) {

                        if ($recordData->relatedTabId !== null) {
                            // if one-to-one relation => only delete once for main table
                            continue;
                        }

                        try {
                            if ($this->isMain) {
                                if ($recordData->convertClientRecordId($this->currentClient) === false) {
                                    // not found means already deleted
                                    unset($tableData->records[$recordKey]);
                                    continue;
                                }
                            }
                            $id = $this->isMain ? $recordData->mainRecordId : $recordData->clientRecordId;

                            // if already deleted on local system
                            if (!empty($deletedIds[$tabId]) && is_array($deletedIds[$tabId]) && in_array($id, $deletedIds[$tabId])) {
                                unset($tableData->records[$recordKey]);
                                continue;
                            }

                            $recordExists = $id !== null && $this->recordExists($tabId, $id);
                            $delResult = !$recordExists || del_data($tabId, $id);

                            if (!$delResult) {
                                //if not exists returns false 
                                $datasyncData->addRecordError($recordData, DatasyncError::DELETE_DATA_FAILED, Log::getMessagesAsString(true));
                            } elseif ($this->isMain && $this->template[$tabId]['global']) {
                                //global sync does not need SYNC_SLAVE AND SYNC_ID SET but a new entry in the cache table, so it is synced to all other clients
                                $this->putInCache($tabId, 0, $id, DataSyncType::DELETE);
                            }
                        } catch (Throwable $t) {
                            $datasyncData->addRecordError($recordData, DatasyncError::DELETE_DATA_FAILED, $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                        }
                    } elseif ($recordData->created) {
                        //new records
                        try {
                            if ($recordData->relatedTabId !== null) {
                                // if one-to-one relation => only create once for main table
                                continue;
                            }


                            $id = null;
                            $tableName = Dbf::handleCaseSensitive($gtab['table'][$tabId]);


                            if ($this->template[$tabId]['global']) {
                                //same record id on main and client
                                //check if record already exists
                                $id = $recordData->mainRecordId ?? $recordData->clientRecordId;
                                $sql = "SELECT ID FROM $tableName WHERE ID = $id";
                                $rs = lmbdb_exec($db, $sql);
                                if (!lmbdb_result($rs, 'ID')) {
                                    //it does not exist => force id
                                    $id = new_data($tabId, null, null, null, $id);
                                }
                                $recordData->mainRecordId = $id;
                                $recordData->clientRecordId = $id;
                            } else {
                                if ($this->isMain) {
                                    //check if record already exists
                                    $sql = "SELECT ID FROM $tableName WHERE LMB_SYNC_SLAVE = $this->currentClient AND LMB_SYNC_ID = $recordData->clientRecordId";
                                    $rs = lmbdb_exec($db, $sql);
                                    $id = lmbdb_result($rs, 'ID');
                                    if (empty($id)) {
                                        $id = new_data($tabId);
                                    }
                                    $recordData->mainRecordId = $id;
                                } else {
                                    // on client always create new record
                                    $id = new_data($tabId);
                                    $recordData->clientRecordId = $id;
                                }
                            }


                            if (empty($id)) {
                                $datasyncData->addRecordError($recordData, DatasyncError::NEW_DATA_FAILED);
                                continue;
                            }

                            if ($this->isMain && $this->template[$tabId]['global']) {
                                //global sync does not need SYNC_SLAVE AND SYNC_ID SET but a new entry in the cache table so it is synced to all other clients
                                $this->putInCache($tabId, 0, $id, DataSyncType::CREATE);
                            } elseif ($this->isMain) {
                                // assign lmb_sync_slave and lmb_sync_id

                                $tables = [$tableName];


                                if (array_key_exists('raverkn', $gtab) &&
                                    is_array($gtab['raverkn']) && array_key_exists($tabId, $gtab['raverkn']) &&
                                    is_array($gtab['raverkn'][$tabId])) {
                                    foreach ($gtab['raverkn'][$tabId] as $relatedTabId) {
                                        if (!array_key_exists($relatedTabId, $this->template)) {
                                            continue;
                                        }
                                        if ($relatedTabId !== $tabId) {
                                            $tables[] = $gtab['table'][$relatedTabId];
                                        }
                                    }
                                }

                                $keyField = $gtab['keyfield'][$tabId];
                                foreach ($tables as $table) {
                                    $table = Dbf::handleCaseSensitive($table);
                                    Database::update($table,
                                        [
                                            'LMB_SYNC_SLAVE' => $this->currentClient,
                                            'LMB_SYNC_ID' => $recordData->clientRecordId
                                        ], [
                                            $keyField => $recordData->mainRecordId
                                        ]);
                                }

                            }

                        } catch (Throwable $t) {
                            $datasyncData->addRecordError($recordData, DatasyncError::NEW_DATA_FAILED, $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                        }
                    }
                }

                // for faster id resolve => load all send records including new to index
                $this->createTableIndex($datasyncData);


                // after creations and deletions => run updates
                $this->applyUpdates($datasyncData, $tabId, $tableData);

            }
        } catch (Throwable $t) {
            DatasyncLog::error('Apply error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
            return false;
        }

        // only on main: update sync_ids of records created on slave
        $syncIdStatus = $this->updateSyncIds($datasyncData);
        if ($syncIdStatus !== true) {
            DatasyncLog::error('Update sync ids error: ' . $syncIdStatus->getMessage() . ' -- ' . $syncIdStatus->getFile() . ' -- ' . $syncIdStatus->getLine());
            return false;
        }

        //set or delete relations
        $setRelationsStatus = $this->setOrDeleteRelations($datasyncData);
        if ($setRelationsStatus !== true) {
            DatasyncLog::error('Create relations error: ' . $setRelationsStatus->getMessage() . ' -- ' . $setRelationsStatus->getFile() . ' -- ' . $setRelationsStatus->getLine());
            return false;
        }

        //Update relations
        $updateRelationsStatus = $this->updateRelations($datasyncData);
        if ($updateRelationsStatus !== true) {
            DatasyncLog::error('Update relations error:  ' . $updateRelationsStatus->getMessage() . ' -- ' . $updateRelationsStatus->getFile() . ' -- ' . $updateRelationsStatus->getLine());
            return false;
        }

        return true;
    }


    protected function applyUpdates(DatasyncData $datasyncData, int $tabId, DatasyncTableData $tableData): void
    {
        /**
         * @var int $recordKey
         * @var DatasyncRecordData $recordData
         */
        foreach ($tableData->records as $recordKey => $recordData) {
            // dont send back deleted data
            if ($recordData->deleted) {
                unset($tableData->records[$recordKey]);
                continue;
            }
            // if there was an error while creating => skip updates for that record
            if ($recordData->created && (!empty($recordData->failed))) {
                unset($tableData->records[$recordKey]);
                continue;
            }

            // resolve ids
            if ($this->isMain) {
                if ($recordData->convertClientRecordId($this->currentClient) === false) {
                    $datasyncData->addRecordError($recordData, DatasyncError::CLIENT_ID_COULD_NOT_BE_RESOLVED);
                    continue;
                }
            } elseif (empty($recordData->clientRecordId) && !empty($recordData->relatedTabId)) {
                // if one-to-one relation resolve ids if not already done
                if (!array_key_exists($recordData->relatedTabId, $datasyncData->tableData)
                    || !array_key_exists($recordKey, $datasyncData->tableData[$recordData->relatedTabId]->records)) {
                    $datasyncData->addRecordError($recordData, DatasyncError::ONE_TO_ONE_ID_COULD_NOT_BE_RESOLVED, excludeCreated: $recordData->created);
                    continue;
                }
                $recordData->clientRecordId = $datasyncData->tableData[$recordData->relatedTabId]->records[$recordKey]->clientRecordId;
            }

            if (!$this->isMain && $recordData->created) {
                $datasyncData->addNewRecord($tabId, $recordData->mainRecordId, $recordData->clientRecordId);
            }


            $id = $this->isMain ? $recordData->mainRecordId : $recordData->clientRecordId;


            // if deleted on local system
            if (!empty($deletedIds[$tabId]) && is_array($deletedIds[$tabId]) && in_array($id, $deletedIds[$tabId])) {
                unset($tableData->records[$recordKey]);
                continue;
            }

            try {
                $update = [];

                //Update allowed
                if ($this->isMain) {
                    $allowedFieldIds = $this->template[$tabId]['slave'];
                } else {
                    $allowedFieldIds = $this->template[$tabId]['master'];
                }

                $this->handleSystemFieldsUpdate($tabId, $id, $recordData->data, $update, $recordData->created);

                foreach ($recordData->data as $fieldId => $value) {

                    if (!is_numeric($fieldId) || !in_array($fieldId, $allowedFieldIds)) {
                        continue;
                    }

                    $apply = $this->applyFieldType($tabId, $fieldId, $id, $value);
                    if ($apply === true) {
                        if (!$recordData->created) {
                            $timestamp = $recordData->timestamps[$fieldId] ?? $recordData->timestamps[0];
                            $hasConflict = !empty($timestamp) && $this->hasConflict($tabId, $id, $fieldId, $timestamp);
                            if ($hasConflict === true) {
                                continue;
                            } elseif ($hasConflict === ConflictMode::MANUAL) {
                                foreach ($recordData->updatedFields[$fieldId] as $syncCacheId) {
                                    $datasyncData->addError($syncCacheId, DatasyncError::CONFLICT);
                                }
                                continue;
                            }
                        }
                        $update["$tabId,$fieldId,$id"] = $value ?? '';
                    } else {
                        continue;
                    }

                    if ($this->isMain && $this->template[$tabId]['global']) {
                        //global sync table create cache
                        $this->putInCache($tabId, $fieldId, $id, DataSyncType::UPDATE);
                    }
                }

                if (!empty($update) && update_data($update) !== true) {
                    $datasyncData->addRecordError($recordData, DatasyncError::UPDATE_DATA_FAILED, Log::getMessagesAsString(true), excludeCreated: $recordData->created);
                }
            } catch (Throwable $t) {
                $datasyncData->addRecordError($recordData, DatasyncError::UPDATE_DATA_FAILED, $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine(), excludeCreated: $recordData->created);
            }
        }
    }

    /**
     * update sync_ids of records created on slave
     *
     * @param DatasyncData $datasyncData
     * @return true|Throwable
     */
    protected function updateSyncIds(DatasyncData $datasyncData): true|Throwable
    {
        global $gtab;
        global $gfield;

        if (!$this->isMain || empty($datasyncData->newRecords)) {
            return true;
        }

        //update sync_ids of records created on slave
        try {
            /** @var DatasyncNewRecord $datasyncNewRecord */
            foreach ($datasyncData->newRecords as $datasyncNewRecord) {

                $tabId = $datasyncNewRecord->tabId;

                //no need to set sync ids if table is globally synced
                if ($this->template[$tabId]['global']) {
                    continue;
                }

                $table = Dbf::handleCaseSensitive($gtab['table'][$tabId]);
                $keyField = $gtab['keyfield'][$tabId];

                Database::update($table, [
                    'LMB_SYNC_SLAVE' => $this->currentClient,
                    'LMB_SYNC_ID' => $datasyncNewRecord->clientRecordId
                ], [
                    $keyField => $datasyncNewRecord->mainRecordId
                ]);


                // foreach relation field => create update entry
                foreach ($gfield[$tabId]['data_type'] as $fieldId => $dataType) {
                    if (in_array($dataType, [24, 25, 27])) {
                        $null = null;
                        execute_sync($tabId, $fieldId, $datasyncNewRecord->mainRecordId, $null, $datasyncNewRecord->clientRecordId, $this->currentClient, DataSyncType::UPDATE->value, true);
                    }
                }
            }
        } catch (Throwable $t) {
            return $t;
        }
        return true;
    }


    /**
     * Creates a map from main to client id or vise versa to skip database resolve on new records
     *
     * @param DatasyncData $datasyncData
     * @return void
     */
    private function createTableIndex(DatasyncData $datasyncData): void
    {
        $this->tableIndex = [];
        foreach ($datasyncData->tableData as $tabId => $tableData) {
            $this->tableIndex[$tabId] = [];
            /** @var DatasyncRecordData $recordData */
            foreach ($tableData->records as $recordData) {
                if ($this->isMain) {
                    $this->tableIndex[$tabId][$recordData->clientRecordId] = $recordData->mainRecordId;
                } else {
                    $this->tableIndex[$tabId][$recordData->mainRecordId] = $recordData->clientRecordId;
                }
            }
        }
    }

    /**
     * @param DatasyncData $datasyncData
     * @return true|Throwable
     */
    protected function setOrDeleteRelations(DatasyncData $datasyncData): true|Throwable
    {
        global $gtab;
        global $gfield;

        //set or delete relations
        try {

            /**
             * @var int $tabId
             * @var DatasyncTableData $tableData
             */
            foreach ($datasyncData->tableData as $tabId => $tableData) {
                if (empty($tableData->records)) {
                    continue;
                }

                /** @var DatasyncRecordData $recordData */
                foreach ($tableData->records as $recordData) {
                    if (empty($recordData->relations)) {
                        continue;
                    }

                    $recordId = $this->isMain ? $recordData->mainRecordId : $recordData->clientRecordId;

                    foreach ($recordData->relations as $fieldId => $relations) {


                        $relatedTabId = intval($gfield[$tabId]['verkntabid'][$fieldId] ?? 0);
                        $relationTableId = $this->getRelationTableId($tabId, $fieldId);
                        $relationTableSynced = !empty($relationTableId) && $gtab['datasync'][$relationTableId];

                        //get existing relations
                        $filter['relationval'][$tabId] = 1;
                        $filter['status'][$tabId] = -1;
                        $filter['validity'][$tabId] = 'all';
                        $gresult = get_gresult($tabId, 1, $filter, [], null, array($tabId => array($fieldId)), $recordId);
                        $existingRelationIds = [];
                        if ($gresult[$tabId]['res_count'] > 0) {
                            $existingRelationIds = $gresult[$tabId][$fieldId][0] ?? null;
                            if(!is_array($existingRelationIds)) {
                                $datasyncData->addRecordError($recordData, DatasyncError::APPLYING_RELATIONS_FAILED);
                                continue;
                            }
                            $existingRelationIds = array_filter($gresult[$tabId][$fieldId][0]);
                        }


                        $applyRelationIds = [];
                        $levelIds = [];
                        $syncCacheId = null;
                        $synchronizedRelationTable = $relationTableSynced ? [
                            'tabId' => $relationTableId,
                            'keyIds' => [],
                        ] : null;
                        /** @var DatasyncRelation $relation */
                        foreach ($relations as $relation) {
                            $syncCacheId = $relation->syncCacheId;

                            $sourceRecordId = $this->isMain ? $relation->clientRecordId : $relation->mainRecordId;
                            $relatedRecordId = $this->isMain ? $relation->mainRelatedRecordId : $relation->clientRelatedRecordId;
                            if (empty($relatedRecordId)) // the record might has been created, try to resolve
                            {
                                if ($this->isMain) {
                                    // on main simply convert the id, it should already be in the database
                                    $relatedRecordId = $this->tableIndex[$relatedTabId][$relation->clientRelatedRecordId] ?? $this->convertID($relatedTabId, $relation->clientRelatedRecordId, $this->currentClient);
                                } else {
                                    // on client, if the record was created during same sync, the main couldn't have sent the client id because it didn't exist.                                    
                                    $relatedRecordId = $this->tableIndex[$relatedTabId][$relation->mainRelatedRecordId] ?? null;
                                }
                            }

                            if (empty($relatedRecordId)) {
                                // TODO: maybe set an error
                                continue;
                            }

                            if($relationTableSynced) {
                                // $sourceRecordId is only set when the relation table is synced 
                                $applyRelationIds[$sourceRecordId] = $relatedRecordId;
                            }
                            else {
                                $applyRelationIds[] = $relatedRecordId;
                            }
                            
                            if (!empty($relation->levelId)) {
                                $levelIds[$relatedRecordId] = $relation->levelId;
                            }

                            if ($synchronizedRelationTable !== null) {
                                // on client, main id is needed to send new record
                                $synchronizedRelationTable['keyIds'][$relatedRecordId] = $this->isMain ? $relation->clientRecordId : $relation->mainRecordId;
                            }
                        }


                        $applyRelationIds = array_filter($applyRelationIds);

                        //search ids on both client and main
                        $intersect = array_intersect($applyRelationIds, $existingRelationIds);

                        // add ids missing on current system
                        $relationAddIds = array_diff($applyRelationIds, $intersect);
                        if (!empty($relationAddIds)) {
                            if (
                                //table is published and synced too => set lmb_slave_id and keyid from slave
                                $relationTableSynced
                                //table is related to dms
                                || !empty($levelIds)
                            ) {

                                foreach ($relationAddIds as $sourceRecordId => $relatedRecordId) {

                                    $params = [];

                                    //if relation is parameterized and synced
                                    if ($this->isMain && $relationTableSynced) {
                                        $params['LMB_SYNC_SLAVE'] = $this->currentClient;
                                        $params['LMB_SYNC_ID'] = $sourceRecordId;
                                    }

                                    if (!empty($levelIds[$relatedRecordId])) {
                                        $params['LID'] = $levelIds[$relatedRecordId];
                                    }


                                    $this->addRelations($datasyncData, $tabId, $fieldId, $recordId, [$relatedRecordId], $syncCacheId, $synchronizedRelationTable, $params);
                                }
                            } else {

                                $this->addRelations($datasyncData, $tabId, $fieldId, $recordId, $relationAddIds, $syncCacheId, $synchronizedRelationTable);
                            }


                        }

                        // delete missing relations
                        $relationDeleteIds = array_diff($existingRelationIds, $intersect);
                        if (!empty($relationDeleteIds)) {
                            $relation = init_relation($tabId, $fieldId, $recordId, null, $relationDeleteIds);
                            if (!set_relation($relation)) {
                                if ($syncCacheId) {
                                    $datasyncData->addError($syncCacheId, DatasyncError::DELETE_RELATIONS_FAILED);
                                }
                            }
                        }


                    }

                }


            }
        } catch (Throwable $t) {
            return $t;
        }

        return true;
    }

    protected function addRelations(DatasyncData $datasyncData, int $tabId, int $fieldId, int $recordId, array $relationAddIds, ?int $syncCacheId, ?array $synchronizedRelationTable, array $params = []): void
    {
        $relation = init_relation($tabId, $fieldId, $recordId, $relationAddIds, linkParam: $params);

        if ($relation === false) {
            $message = Log::getMessagesAsString(true);
            if ($syncCacheId) {
                $datasyncData->addError($syncCacheId, DatasyncError::DELETE_RELATIONS_FAILED, $message);
            }
        } elseif (!set_relation($relation)) {
            $message = Log::getMessagesAsString(true);
            //workaround for existing relations
            if (!(str_contains($message, 'already joined') || str_contains($message, 'already exists'))) {
                if ($syncCacheId) {
                    $datasyncData->addError($syncCacheId, DatasyncError::DELETE_RELATIONS_FAILED, $message);
                }
            }
        } elseif (!$this->isMain && is_array($synchronizedRelationTable)) {

            $relationTableId = $synchronizedRelationTable['tabId'];
            foreach ($relationAddIds as $relatedRecordId) {
                $masterKeyId = $synchronizedRelationTable['keyIds'][$relatedRecordId] ?? null;
                if (empty($masterKeyId)) {
                    continue;
                }
                $keyId = $this->getRelKeyID($relationTableId, $recordId, $relatedRecordId);
                $datasyncData->addNewRecord($relationTableId, $masterKeyId, $keyId);
            }
        }
    }

    protected function updateRelations(DatasyncData $datasyncData): true|Throwable
    {
        global $gtab;

        try {

            foreach ($datasyncData->tableData as $tabId => $tableData) {

                //check if table is not a parameterized relation table //TODO: other way than name check
                if (strtoupper(substr($gtab['table'][$tabId], 0, 5)) !== 'VERK_') {
                    continue;
                }

                // only do updates for relation tables
                $this->applyUpdates($datasyncData, $tabId, $tableData);
            }
        } catch (Throwable $t) {
            return $t;
        }
        return true;
    }


    /**
     * Writes all cached exceptions into the database
     *
     * @param array $errors
     * @return void
     */
    protected function writeErrorsToSyncLog(array $errors): void
    {
        global $gtab;

        if (empty($errors)) {
            return;
        }

        $isLimbasTable = array_key_exists('LMB_SYNC_LOG', $gtab['argresult_id']);

        if ($isLimbasTable) {
            $id = next_db_id('lmb_sync_log');
        }


        foreach ($errors as $syncCacheId => $error) {
            $datasyncError = DatasyncError::tryFrom($error[0]);
            if (!$datasyncError) {
                continue;
            }
            $message = $datasyncError->getMessage();
            if (array_key_exists(1, $error)) {
                $message = $message . ': ' . $error[1];
            }

            $data = [
                'TYPE' => '',
                'TABID' => 0,
                'DATID' => 0,
                'FIELDID' => 0,
                'ORIGIN' => 0,
                'SLAVEID' => $this->currentClient,
                'ERRORCODE' => $datasyncError->value,
                'MESSAGE' => $message,
                'SYNC_CACHE_ID' => $syncCacheId
            ];

            if ($isLimbasTable) {
                $data['ID'] = $id;
                $id++;
            }


            Database::update('LMB_SYNC_CACHE', ['ERROR' => $datasyncError->value . ($message ? '- ' . $message : ''), 'DONE'=>LMB_DBDEF_FALSE], ['ID' => $syncCacheId]);
            Database::insert('LMB_SYNC_LOG', $data);
            
            if (!$this->isMain && ($datasyncError === DatasyncError::RECORD_NOT_FOUND || $datasyncError === DatasyncError::CLIENT_ID_COULD_NOT_BE_RESOLVED)) {
                // in case of slave sending a change to a record that does not exist on main -> update cache on client from update to create so next time the missing record will be created
                Database::update('LMB_SYNC_CACHE',['TYPE'=>DataSyncType::CREATE->value],['ID'=>$syncCacheId]);
            }

        }

    }


    /**
     * Delete all handled records from sync cache
     *
     * @param int $timestamp
     * @return bool success
     */
    protected function resetCache(int $timestamp): bool
    {
        global $umgvar;
        $db = Database::get();

        DatasyncLog::info('Resetting cache');

        if (empty($timestamp)) {
            return true;
        }
        
        $keepCacheDays = intval($umgvar['sync_keep_cache']);

        //TODO: template in lmb_sync_cache
        if (!$this->isMain || !empty($this->currentClient)) {
            
            if($keepCacheDays > 0) {
                // delete all done cache entries older than specified days
                $date = new DateTime();
                $date->modify('-' . $keepCacheDays . ' days');
                $date->format('Y-m-d H:i:s');
                $sql = 'DELETE FROM LMB_SYNC_CACHE WHERE DONE IS ' . LMB_DBDEF_TRUE . ' AND ERSTDATUM < \'' . $date->format('Y-m-d H:i:s') . '\'';
            }
            else {
                // delete all cache entries of the current process only
                $sql = 'DELETE FROM LMB_SYNC_CACHE WHERE ERROR IS NULL AND LMB_SYNC_CACHE.PROCESS_KEY = ' . $timestamp . (($this->isMain) ? ' AND SLAVE_ID = ' . $this->currentClient : '');
            }
            
            
            $rs = lmbdb_exec($db, $sql);
            if (!$rs) {
                DatasyncLog::error('Reset failed');
                return false;
            }
        }


        // insert client in global log
        if ($this->isMain) {
            $sqlquery = 'SELECT LMB_SYNC_CACHE.ID FROM LMB_SYNC_CACHE LEFT JOIN LMB_SYNC_GLOBAL ON LMB_SYNC_CACHE.ID = LMB_SYNC_GLOBAL.CACHE_ID
    AND LMB_SYNC_GLOBAL.CLIENT_ID = ' . $this->currentClient . ' WHERE LMB_SYNC_CACHE.PROCESS_KEY = ' . $timestamp . ' AND SLAVE_ID = 0 AND CACHE_ID IS NULL';

            $rs = lmbdb_exec($db, $sqlquery);
            if (!$rs) {
                DatasyncLog::error('Reading global cache');
                return false;
            }

            $values = [];
            if (lmbdb_num_rows($rs) > 0) {
                while (lmbdb_fetch_row($rs)) {
                    $values[] = '(' . lmbdb_result($rs, 'ID') . ',' . $this->currentClient . ')';
                }
            }

            if (!empty($values)) {
                $sql = 'INSERT INTO LMB_SYNC_GLOBAL (CACHE_ID,CLIENT_ID) VALUES ' . implode(',', $values);
                $rs = lmbdb_exec($db, $sql);
                if (!$rs) {
                    DatasyncLog::error('Put into global cache failed');
                    return false;
                }
            }
        }


        return true;
    }

    /**
     * @return void
     */
    protected function startTransaction(): void
    {
        global $umgvar;

        if ($umgvar['sync_transaction']) {
            lmb_StartTransaction();
        }
    }

    /**
     * @param bool $status
     * @return void
     */
    protected function endTransaction(bool $status): void
    {
        global $umgvar;

        if ($umgvar['sync_transaction']) {
            $GLOBALS['lmb_transaction'] = 1;
            lmb_EndTransaction($status);
        }
    }


    /**
     * count records of sync cache
     * @return int[]
     */
    private function countSyncCache(): array
    {
        global $umgvar;
        $db = Database::get();

        $doneSql = '(DONE = ' . LMB_DBDEF_FALSE . ' OR DONE IS NULL)';
        $maxAttempts = intval($umgvar['sync_max_attempts']);
        $maxAttemptsSql = ($maxAttempts > 0 ? '(TRY_COUNT <= ' . $maxAttempts . ' OR TRY_COUNT IS NULL)' : '');
        
        //get count of all records
        $sqlQuery = 'SELECT COUNT(ID) AS CACHECOUNT FROM LMB_SYNC_CACHE WHERE ' . $doneSql . ($this->isMain ? ' AND (SLAVE_ID = ' . $this->currentClient . ' OR  SLAVE_ID = 0 )' : '');
        $rs = lmbdb_exec($db, $sqlQuery);
        $recordCount = 0;
        if (lmbdb_fetch_row($rs)) {
            $recordCount = intval(lmbdb_result($rs, 'CACHECOUNT'));
        }

        // get count of records to be synced
        $maxAttempts = intval($umgvar['sync_max_attempts']);
        $syncCount = $recordCount;
        if($maxAttempts > 0) {
            $sqlQuery = 'SELECT COUNT(ID) AS CACHECOUNT FROM LMB_SYNC_CACHE WHERE ' . $doneSql . ' AND ' . $maxAttemptsSql . ($this->isMain ? ' AND (SLAVE_ID = ' . $this->currentClient . ' OR  SLAVE_ID = 0 )' : '');
            $rs = lmbdb_exec($db, $sqlQuery);
            $syncCount = 0;
            if (lmbdb_fetch_row($rs)) {
                $syncCount = intval(lmbdb_result($rs, 'CACHECOUNT'));
            }
        }
        return [$syncCount, $recordCount];
    }


    /**
     * Mark all records that may be synced by the current process
     * @param bool|int $maxRecords
     * @return void
     */
    private function setSyncRecords(bool|int $maxRecords): void
    {
        global $umgvar;
        $db = Database::get();

        $doneSql = '(DONE = ' . LMB_DBDEF_FALSE . ' OR DONE IS NULL)';
        $maxAttempts = intval($umgvar['sync_max_attempts']);
        $maxAttemptsSql = ($maxAttempts > 0 ? ' AND (TRY_COUNT <= ' . $maxAttempts . ' OR TRY_COUNT IS NULL)' : '');
        
        if ($this->isMain) {
            $filter = ' LEFT JOIN LMB_SYNC_GLOBAL ON LMB_SYNC_CACHE.ID = LMB_SYNC_GLOBAL.CACHE_ID AND LMB_SYNC_GLOBAL.CLIENT_ID = ' . $this->currentClient . '
    WHERE ((SLAVE_ID = ' . $this->currentClient . ' AND ' . $doneSql . $maxAttemptsSql . ') OR ( SLAVE_ID = 0 AND LMB_SYNC_GLOBAL.CACHE_ID IS NULL ))';
        }
        else {
            $filter = ' WHERE ' . $doneSql . $maxAttemptsSql;
        }

        $limit = '';
        if ($maxRecords !== false) {
            $limit = 'LIMIT ' . $maxRecords;
        }
        

        if ($this->isMain || $maxRecords !== false) {
            $sqlQuery = 'UPDATE LMB_SYNC_CACHE
        SET PROCESS_KEY = ' . $this->cacheTimestamp . ',
        ERROR = ' . LMB_DBDEF_NULL . ',
        TRY_COUNT = COALESCE(TRY_COUNT,0) + 1,
        DONE = ' . LMB_DBDEF_TRUE . '
        WHERE ID IN (
        SELECT LMB_SYNC_CACHE.ID
          FROM LMB_SYNC_CACHE
          ' . $filter . '
          ORDER BY LMB_SYNC_CACHE.TYPE ' . $limit . '
        )';

        } else {
            // no filter and limit needed on client => update all records
            $sqlQuery = 'UPDATE LMB_SYNC_CACHE SET PROCESS_KEY = ' . $this->cacheTimestamp . ', TRY_COUNT = COALESCE(TRY_COUNT,0) + 1, ERROR = ' . LMB_DBDEF_NULL . ', DONE = ' . LMB_DBDEF_TRUE . ' WHERE ' . $doneSql . $maxAttemptsSql;
        }


        lmbdb_exec($db, $sqlQuery);
    }


    /**
     * Synchronizes special system fields independent of data fields
     *
     * @param int $tabId
     * @param int $id
     * @param array $data
     * @param array $update
     * @param bool $wasCreated
     */
    private function handleSystemFieldsUpdate(int $tabId, int $id, array $data, array &$update, bool $wasCreated = false): void
    {

        if (array_key_exists('LMB_MID', $data) && !empty($data['LMB_MID'])) {
            $update["$tabId,LMB_MID,$id"] = $data['LMB_MID'];
        }
        if (array_key_exists('DEL', $data)) {
            $update["$tabId,DEL,$id"] = ($data['DEL']) ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE;
        }
        if (array_key_exists('LMB_STATUS', $data)) {
            $update["$tabId,LMB_STATUS,$id"] = intval($data['LMB_STATUS']);
        }


        if (array_key_exists('VID', $data) && !empty($data['VID'])) {
            $update["$tabId,VID,$id"] = $data['VID'];
        }
        if (array_key_exists('VDESC', $data) && !empty($data['VDESC'])) {
            $update["$tabId,VDESC,$id"] = "'" . $data['VDESC'] . "'";
        }

        // TODO: versioning VPID not working / same record versioned on both sides
        if (!$wasCreated) {
            $vpid = null;
            if (array_key_exists('VPID', $data) && !empty(trim($data['VPID']))) {
                $vpid = intval($data['VPID']);
                if ($this->isMain) {
                    // TODO: error handling
                    $vpid = $this->convertID($tabId, $vpid, $this->currentClient);
                }
                if (!empty($vpid)) {
                    $update["$tabId,VPID,$id"] = $vpid;
                }
            }
            if (!empty($vpid) && array_key_exists('VACT', $data)) {
                $update["$tabId,VACT,$id"] = $data['VACT'] ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE;

                //set all old versions to if updating active version
                if ($data['VACT'] && $vpid !== null) {
                    $this->setVersionsInactive($tabId, $vpid);
                }
            }
        }


        if (array_key_exists('LMB_VALIDFROM', $data) && !empty($data['LMB_VALIDFROM'])) {
            $update["$tabId,LMB_VALIDFROM,$id"] = "'" . $data['LMB_VALIDFROM'] . "'";
        } elseif (array_key_exists('LMB_VALIDFROM', $data)) {
            $update["$tabId,LMB_VALIDFROM,$id"] = LMB_DBDEF_NULL;
        }
        if (array_key_exists('LMB_VALIDTO', $data) && !empty($data['LMB_VALIDTO'])) {
            $update["$tabId,LMB_VALIDTO,$id"] = "'" . $data['LMB_VALIDTO'] . "'";
        } elseif (array_key_exists('LMB_VALIDTO', $data)) {
            $update["$tabId,LMB_VALIDTO,$id"] = LMB_DBDEF_NULL;
        }

        if (array_key_exists('LID', $data) && !empty($data['LID'])) {
            $update["$tabId,LID,$id"] = $data['LID'];
        }

    }


    /**
     * Set all old versions to inactive
     *
     * @param int $tabId
     * @param int $id
     *
     * @return void converted id or false if not found
     */
    private function setVersionsInactive(int $tabId, int $id): void
    {
        global $db;
        global $gtab;

        $table = Dbf::handleCaseSensitive($gtab['table'][$tabId]);
        $sql = "UPDATE $table SET VACT = " . LMB_DBDEF_FALSE . " WHERE VPID = $id";
        lmbdb_exec($db, $sql);
    }

    /**
     * Collects all relevant fields of a table for synchronization
     *
     * @param int $tabId
     * @return array
     * @throws Exception
     */
    private function getSyncFields(int $tabId): array
    {
        if (!array_key_exists($tabId, $this->template)) {
            // TODO: error handling
            throw new Exception("Tabid $tabId not in sync template $this->template!");
        }

        if (is_array($this->template[$tabId])) {
            if ($this->isMain) {
                return $this->template[$tabId]['master'];
            } else {
                return $this->template[$tabId]['slave'];
            }
        }

        return array();
    }


    /**
     * Checks if same record/field was changed on both, main and client
     *
     * @param int $tabId
     * @param int $recordId
     * @param int $fieldId
     * @param int $timestamp
     * @return bool|ConflictMode
     * @throws Exception
     */
    private function hasConflict(int $tabId, int $recordId, int $fieldId, int $timestamp): bool|ConflictMode
    {
        $conflictMode = ConflictMode::from(intval($this->template['conflict_mode']['global']));


        $conflictMode = $this->template['conflict_mode'][$tabId][$fieldId] ?? $conflictMode;
        if ($conflictMode === ConflictMode::DISABLED) {
            return false;
        }

        $where = [
            'TABID' => $tabId,
            'FIELDID' => $fieldId,
            'DATID' => $recordId,
            'TYPE' => DataSyncType::UPDATE->value
        ];
        if ($this->isMain) {
            $where['SLAVE_ID'] = $this->currentClient;
        }
        $rs = Database::select('LMB_SYNC_CACHE', [
            'ID',
            'ERSTDATUM'
        ], $where);

        
        if (lmbdb_num_rows($rs) <= 0) {
            return false;
        }

        // hasConflict is only called during apply => the data checked comes from the remote system
        switch ($conflictMode) {
            case ConflictMode::MAIN_WINS:
                if ($this->isMain) {
                    return true;
                }
                break;
            case ConflictMode::CLIENT_WINS:
                if (!$this->isMain) {
                    return true;
                }
                break;
            case ConflictMode::LATEST_WINS:
                lmbdb_fetch_row($rs);
                $erstdatum = lmbdb_result($rs, 'ERSTDATUM');
                $dt = new DateTime($erstdatum);
                $erstdatum = $dt->getTimestamp();
                if ($erstdatum > $timestamp) {
                    return true;
                }
                break;
            case ConflictMode::MANUAL:
                return ConflictMode::MANUAL;
            default:
                return false;
        }

        return false;
    }


    /**
     * Writes an entry to the sync cache
     *
     * @param int $tabId
     * @param int $fieldId
     * @param int $datId
     * @param DataSyncType $type
     * @return void
     */
    private function putInCache(int $tabId, int $fieldId, int $datId, DataSyncType $type): void
    {
        //TODO: insert current client in global log
        //TODO: what happens if master deletes record and client has still updates?

        $nextID = next_db_id('LMB_SYNC_CACHE', 'ID', 1);

        Database::insert('LMB_SYNC_CACHE', [
            'ID' => $nextID,
            'TABID' => $tabId,
            'FIELDID' => $fieldId,
            'DATID' => $datId,
            'SLAVE_ID' => 0,
            'SLAVE_DATID' => $datId,
            'TYPE' => $type->value
        ]);
    }


    private function recordExists(int $tabId, int $id): bool
    {
        global $gtab;

        $db = Database::get();

        try {
            $table = Dbf::handleCaseSensitive($gtab['table'][$tabId]);
            $keyfield = $gtab['keyfield'][$tabId];

            $sql = "SELECT $keyfield AS CID FROM $table WHERE $keyfield = $id";
            $rs = lmbdb_exec($db, $sql);
            if (lmbdb_fetch_row($rs)) {
                return true;
            }
        } catch (Throwable) {
        }

        return false;
    }


}
