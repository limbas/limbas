<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Data;

use Limbas\admin\tools\datasync\Enums\DatasyncError;
use Limbas\admin\tools\datasync\Traits\HandleData;
use Limbas\admin\tools\datasync\Traits\HandleIds;
use Limbas\lib\db\Database;

class DatasyncRecordData
{
    use HandleIds, HandleData;

    private const specialFieldIds = [
        27, //Relation: only valid if both, table and linked table, are synchronized
        24, //Relation: only valid if both, table and linked table, are synchronized
        25, // 1:n direct
        30, //Currency
        18, //Auswahl (checkbox)
        31, //Auswahl (multiselect)
        32, //Auswahl (ajax)
        46, //Attribute
        38, // user group list
    ];

    private const ignoreFieldTypes = [
        53, //Validity
        54, //Validity
        52, //Multitenant
        51, //sync slave
        43, //version comment
        34, //erst / edit user / date
        35, //erst / edit user / date
        36, //erst / edit user / date
        37, //erst / edit user / date
        22, //ID field
        13, //Upload
        48, // PHP-Argument
        47, //SQL-Argument
        23, //Backward relation
        100, // Sparte
        101, // Gruppierung Reiter
        102, // Gruppierung Zeile
    ];


    public bool $deleted = false;

    public bool $created = false;

    public ?DatasyncError $failed;
    public ?string $failedMessage;

    public array $deletedCacheIds;
    public array $createdCacheIds;

    public array $updatedFields = [];
    public array $timestamps = [];

    public array $data;
    public array $relations;


    /**
     * @param int $tabId
     * @param int|null $mainRecordId id of the record on main
     * @param int|null $clientRecordId id of the record on client
     * @param int|null $relatedTabId id of one-to-one related id
     */
    public function __construct(
        private readonly int $tabId,
        public ?int          $mainRecordId,
        public ?int          $clientRecordId,
        public ?int          $relatedTabId = null,
    )
    {
        $this->updatedFields = [];
        $this->deletedCacheIds = [];
        $this->createdCacheIds = [];
        $this->failed = null;
        $this->failedMessage = null;
        $this->relations = [];
    }


    public function setDeleted(int $syncCacheId): void
    {
        $this->deletedCacheIds[] = $syncCacheId;
        $this->deleted = true;
        $this->created = false;
    }

    public function setCreated(int $syncCacheId): void
    {
        $this->createdCacheIds[] = $syncCacheId;
        if ($this->deleted) {
            return;
        }
        $this->created = true;
    }

    public function addUpdate(int $fieldId, int $syncCacheId, int $createdAt): void
    {
        $this->updatedFields[$fieldId] ??= [];
        $this->updatedFields[$fieldId][] = $syncCacheId;
        $this->timestamps[$fieldId] = max($this->timestamps[$fieldId] ?? $createdAt, $createdAt);
    }

    public function getAllCacheIds(bool $excludeCreated = false): array
    {
        $cacheIds = array_merge(...array_values($this->updatedFields));

        if (!$excludeCreated) {
            $cacheIds = array_merge($cacheIds, $this->deletedCacheIds, $this->createdCacheIds);
        } else {
            $cacheIds = array_merge($cacheIds, $this->deletedCacheIds);
        }


        return array_unique($cacheIds);
    }

    public function clean(): void
    {
        $this->data = [];
    }

    public function convertClientRecordId(int $clientId): bool
    {
        if (!empty($this->mainRecordId)) {
            return true;
        }
        if (empty($this->clientRecordId)) {
            return false;
        }
        $convertedId = $this->convertID($this->relatedTabId ?? $this->tabId, $this->clientRecordId, $clientId);
        if (!empty($convertedId)) {
            $this->mainRecordId = $convertedId;
            return true;
        }
        return false;
    }

    public function fill(?int $currentClient): bool
    {
        global $gtab;
        global $gfield;
        // TODO: load all data here

        $isMain = !empty($currentClient);
        $this->data = [];

        if ($this->deleted) {
            return true;
        }

        $tableName = $gtab['table'][$this->tabId];
        $keyField = $gtab['keyfield'][$this->tabId];

        $systemFields = ['ID', 'INUSE_TIME', 'INUSE_USER', 'DEL', 'ERSTUSER', 'EDITUSER', 'ERSTDATUM', 'EDITDATUM', 'LMB_SYNC_ID', 'LMB_SYNC_SLAVE'];
        if (!empty($gtab['validity'][$this->tabId])) {
            $systemFields = array_merge($systemFields, ['LMB_VALIDFROM', 'LMB_VALIDTO']);
        }
        if (!empty($gtab['versioning'][$this->tabId])) {
            $systemFields = array_merge($systemFields, ['VID', 'VPID', 'VACT', 'VDESC']);
        }
        if (!empty($gtab['multitenant'][$this->tabId])) {
            $systemFields = array_merge($systemFields, ['LMB_MID']);
        }


        $localFields = [];
        $databaseFields = $systemFields;
        $specialFields = [];

        // get all fields if created or zero is passed as field id
        if ($this->created || array_key_exists(0, $this->updatedFields)) {
            $selectedFields = $gfield[$this->tabId]['field_name'];
        } else {
            $selectedFields = $this->updatedFields;
        }

        foreach ($selectedFields as $fieldId => $_) {

            $type = $gfield[$this->tabId]['data_type'][$fieldId];

            if (in_array($type, self::ignoreFieldTypes)) {
                continue;
            }

            if (in_array($type, self::specialFieldIds)) {
                $specialFields[] = $fieldId;
                continue;
            }

            $localFields[$fieldId] = $gfield[$this->tabId]['field_name'][$fieldId];
            $databaseFields[] = $gfield[$this->tabId]['field_name'][$fieldId];
        }


        $recordId = $isMain ? $this->mainRecordId : $this->clientRecordId;
        if (empty($recordId)) {
            $this->failed = DatasyncError::FILL_RECORD;
            $this->failedMessage = 'Empty ID (' . $this->tabId . ':' . $this->mainRecordId . '-' . $this->clientRecordId . ')';
            return false;
        }

        $rs = Database::select($tableName, $databaseFields, [$keyField => $recordId]);
        if (!$rs) {
            $this->failed = DatasyncError::FILL_RECORD;
            $this->failedMessage = implode('|', Database::get()->errorInfo());
            return false;
        }
        $record = lmbdb_fetch_array($rs);
        if (empty($record)) {
            $this->failed = DatasyncError::RECORD_NOT_FOUND;
            $this->failedMessage = '(' . $this->tabId . ':' . $recordId . ')';
            return false;
        }

        $record = array_change_key_case($record, CASE_UPPER);

        foreach ($systemFields as $systemField) {
            // convert id on main to client id before send
            /*if ($this->is_main && $systemField === 'VPID' && !empty(trim($value[0]))) {
                $value[0] = $this->convertID($tabid, $value[0], 1);
            }*/
            $this->data[$systemField] = $record[$systemField];
            if ($systemField === 'LMB_SYNC_ID') {
                $clientId = intval($record[$systemField]) ?: null;
                if (empty($this->clientRecordId)) {
                    $this->clientRecordId = $clientId;
                } elseif (!empty($clientId) && $this->clientRecordId !== $clientId) {
                    $this->failed = DatasyncError::FILL_RECORD;
                    $this->failedMessage = 'Client id mismatch (' . $this->tabId . ':' . $this->mainRecordId . '-' . $this->clientRecordId . '-' . $clientId . ')';
                    return false;
                }
            }
        }


        foreach ($localFields as $fieldId => $fieldName) {
            $this->data[$fieldId] = $record[$fieldName];
        }

        if (!empty($specialFields)) {
            $this->fillSpecialFields($currentClient, $recordId, $specialFields, $isMain && $this->created);
        }

        return true;
    }


    public function apply(): bool
    {
        //TODO: move record handling here
        if ($this->deleted) {
            return $this->delete();
        } elseif ($this->created) {
            return $this->create();
        }

        return $this->update();
    }

    private function create(): bool
    {
        return true;
    }

    private function update(): bool
    {
        return true;
    }

    private function delete(): bool
    {
        return true;
    }


    /**
     * Retrieves all needed data of one record
     *
     * @param int|null $currentClient
     * @param int $recordId
     * @param array $fieldIds
     * @param bool $skipRelations
     * @return void
     */
    protected function fillSpecialFields(?int $currentClient, int $recordId, array $fieldIds, bool $skipRelations = false): void
    {
        $filter['relationval'][$this->tabId] = 1;
        $filter['getlongval'][$this->tabId] = 1;
        $filter['status'][$this->tabId] = -1;
        $filter['validity'][$this->tabId] = 'all';

        //TODO: filter for lmb_sync_slave?
        $gresult = get_gresult($this->tabId, 1, $filter, null, null, [$this->tabId => $fieldIds], $recordId);

        if ($gresult[$this->tabId]['res_count'] <= 0) {
            return;
        }

        foreach ($gresult[$this->tabId] as $fieldId => $value) {
            $fieldId = intval($fieldId);
            if (empty($fieldId)) {
                continue;
            }
            $value = $this->prepareFieldType($currentClient, $this->tabId, $fieldId, $recordId, $value[0], $gresult, $skipRelations);
            if ($value === false) {
                //TODO: error
                continue;
            }
            $this->data[$fieldId] = $value;
        }
        
        $this->relations = array_filter($this->relations);
    }


    /**
     * Collects values of attribute field type
     *
     * @param int $tabId
     * @param int $fieldId
     * @param int $recordId
     * @param $wid
     * @return string
     */
    private function getAttributeValue(int $tabId, int $fieldId, int $recordId, $wid): string
    {
        global $db;

        $sql = "SELECT VALUE_STRING,VALUE_NUM,VALUE_DATE FROM LMB_ATTRIBUTE_D WHERE LMB_ATTRIBUTE_D.W_ID = $wid AND LMB_ATTRIBUTE_D.TAB_ID = $tabId AND LMB_ATTRIBUTE_D.FIELD_ID = $fieldId AND LMB_ATTRIBUTE_D.DAT_ID = $recordId";


        $rs1 = lmbdb_exec($db, $sql);
        if (!$rs1) {
            return '';
        }

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


}
