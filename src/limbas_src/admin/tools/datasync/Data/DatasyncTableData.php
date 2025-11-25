<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Data;

class DatasyncTableData
{

    public array $records = [];
    
    public ?int $relatedTableId = null; // only used if one-to-one table relation exists


    public function __construct(public int $tabId)
    {
        //
    }

    public function getRecordData(int $recordId): ?DatasyncRecordData
    {
        if (!array_key_exists($recordId, $this->records)) {
            return null;
        }
        return $this->records[$recordId];
    }

    public function getOrCreateRecordData(?int $mainRecordId, ?int $clientRecordId): DatasyncRecordData
    {
        $recordId = $mainRecordId ?? $clientRecordId;
        if (!array_key_exists($recordId, $this->records)) {
            $this->records[$recordId] = new DatasyncRecordData($this->tabId, $mainRecordId, $clientRecordId, $this->relatedTableId);
        }
        return $this->records[$recordId];
    }


    /**
     * Fill all records with data; only called locally
     * @param int|null $currentClient
     * @return array
     */
    public function fill(?int $currentClient): array
    {
        $errors = [];
        /** @var DatasyncRecordData $record */
        foreach ($this->records as $recordId => $record) {
            $status = $record->fill($currentClient);
            if ($status === false) {
                $errors[] = [
                    'ids' => $record->getAllCacheIds(),
                    'error' => $record->failed,
                    'message' => $record->failedMessage
                ];
                unset($this->records[$recordId]);
            }
        }
        return $errors;
    }

    public function clean(): void
    {
        /** @var DatasyncRecordData $record */
        foreach ($this->records as $record) {
            $record->clean();
        }
    }

}
