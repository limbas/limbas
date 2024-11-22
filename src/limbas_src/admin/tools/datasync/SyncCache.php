<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync;

use DateTime;
use Limbas\extra\template\TemplateTable;
use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class SyncCache extends LimbasModel
{

    protected static string $tableName = 'LMB_SYNC_CACHE';

    public static array $gtabDmsFolders = [];

    /**
     * @param int $id
     * @param int $tabId
     * @param int $clientId
     * @param int $recordId
     * @param DataSyncType $type
     * @param int|null $clientRecordId
     * @param int|null $fieldId
     * @param DateTime|null $createDate
     * @param string|null $processKey
     */
    public function __construct(
        public int          $id,
        public int          $tabId,
        public int          $clientId,
        public int          $recordId,
        public DataSyncType $type,
        public ?int         $clientRecordId = null,
        public ?int         $fieldId = null,
        public ?DateTime    $createDate = null,
        public ?string      $processKey = null,
    )
    {
        //
    }


    /**
     * @param int $id
     * @return SyncCache|null
     */
    public static function get(int $id): SyncCache|null
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
    public static function all(array $where = [], int $limit = null, int $offset = null): array
    {
        $rs = Database::select(self::$tableName, where: $where, limit: $limit, orderBy: ['ID' => 'asc'], offset: $offset);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'TABID'),
                lmbdb_result($rs, 'SLAVE_ID'),
                lmbdb_result($rs, 'DATID'),
                DataSyncType::tryFrom(intval(lmbdb_result($rs, 'TYPE'))) ?? DataSyncType::UNKNOWN,
                lmbdb_result($rs, 'SLAVE_DATID') ?: null,
                lmbdb_result($rs, 'FIELDID') ?: null,
                new DateTime(lmbdb_result($rs, 'ERSTDATUM')) ?: null,
                intval(lmbdb_result($rs, 'PROCESS_KEY')) ?: null
            );

        }

        return $output;
    }

    /**
     * @param array $where
     * @return int
     */
    public static function count(array $where = []): int
    {
        return Database::count(self::$tableName, $where);
    }
    

    public function save(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}
