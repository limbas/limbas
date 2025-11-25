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

class DatasyncData
{

    /** @var array holds all data records changed on the current system */
    public array $tableData;

    /** @var array contains all errors of current system */
    public array $errors;

    /** @var array contains all errors of the remote system */
    public array $remoteErrors;

    /** @var array collect new records on client to be processed on main */
    public array $newRecords;

    public function __construct(public int $timestamp)
    {
        $this->tableData = [];
        $this->errors = [];

        $this->newRecords = [];
        $this->remoteErrors = [];
    }

    public function getTableData(int $tabId): ?DatasyncTableData
    {
        if (!array_key_exists($tabId, $this->tableData)) {
            return null;
        }
        return $this->tableData[$tabId];
    }

    /**
     * Get the DatasyncTableData object for a specific table
     * @param int $tabId
     * @return DatasyncTableData
     */
    public function getOrCreateTableData(int $tabId): DatasyncTableData
    {
        if (!array_key_exists($tabId, $this->tableData)) {
            $this->tableData[$tabId] = new DatasyncTableData($tabId);
        }
        return $this->tableData[$tabId];
    }

    /**
     * Add error to data package linked to a specific cache entry
     *
     * @param int $syncCacheId
     * @param DatasyncError $datasyncError
     * @param string|null $message
     * @return void
     */
    public function addError(int $syncCacheId, DatasyncError $datasyncError, ?string $message = null): void
    {
        $this->errors[$syncCacheId] = [$datasyncError->value];
        if ($message) {
            $this->errors[$syncCacheId][] = $message;
        }
    }

    /**
     * Add error to data package linked to a specific cache entry
     *
     * @param DatasyncRecordData $datasyncRecordData
     * @param DatasyncError $datasyncError
     * @param string|null $message
     * @param bool $excludeCreated
     * @return void
     */
    public function addRecordError(DatasyncRecordData &$datasyncRecordData, DatasyncError $datasyncError, string $message = null, bool $excludeCreated = false): void
    {
        $syncCacheIds = $datasyncRecordData->getAllCacheIds($excludeCreated);
        foreach ($syncCacheIds as $syncCacheId) {
            $this->addError($syncCacheId, $datasyncError, $message);
        }
        $datasyncRecordData->failed = $datasyncError;
        $datasyncRecordData->failedMessage = $message;
    }


    public function addNewRecord(int $tabId, int $mainRecordId, int $clientRecordId): void
    {
        $this->newRecords[] = new DatasyncNewRecord($tabId, $mainRecordId, $clientRecordId);
    }


    /**
     * Fill all collected records with data
     * @param int|null $currentClient
     * @return void
     */
    public function fill(?int $currentClient): void
    {
        /** @var DatasyncTableData $tableData */
        foreach ($this->tableData as $tableId => $tableData) {
            $errors = $tableData->fill($currentClient);
            foreach ($errors as $error) {
                foreach ($error['ids'] as $syncCacheId) {
                    $datasyncError = $error['error'] ?? DatasyncError::FILL_RECORD;
                    $this->addError($syncCacheId, $datasyncError, $error['message']);
                }
            }
            if (empty($tableData->records)) {
                unset($this->tableData[$tableId]);
            }
        }

    }

    /**
     * Remove all data to get only ids and status
     * @return void
     */
    public function clean(): void
    {
        /** @var DatasyncTableData $tableData */
        foreach ($this->tableData as $tableData) {
            $tableData->clean();
        }
    }


}
