<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\tools\datasync\Traits;

use Limbas\lib\db\functions\Dbf;

trait HandleIds
{

    private const CLIENT_TO_MAIN = 0;
    private const MAIN_TO_CLIENT = 1;

    /**
     * Converts master ID from / to slave ID for defined slave
     *
     * @param int $tabId
     * @param int $recordId
     * @param int $clientId
     * @param int $dir 0 = slave ID to master ID; 1 = master ID to slave ID
     * @return int|false converted id or false if not found
     */
    private function convertID(int $tabId, int $recordId, int $clientId, int $dir = self::CLIENT_TO_MAIN): bool|int
    {
        global $db;
        global $gtab;

        //if table is global keep same ids on main and client and don't convert
        if ($this->template[$tabId]['global']) {
            return $recordId;
        }


        $table = Dbf::handleCaseSensitive($gtab['table'][$tabId]);
        $keyField = $gtab['keyfield'][$tabId];
        

        if ($dir === self::MAIN_TO_CLIENT) {
            //master ID to slave ID
            $sql = "SELECT LMB_SYNC_ID AS CID FROM $table WHERE LMB_SYNC_SLAVE = $clientId AND $keyField = $recordId";
        } else {
            //slave ID to master ID
            $sql = "SELECT $keyField AS CID FROM $table WHERE LMB_SYNC_SLAVE = $clientId AND LMB_SYNC_ID = $recordId";
        }
        $rs = lmbdb_exec($db, $sql);

        if (lmbdb_fetch_row($rs)) {
            return intval(lmbdb_result($rs, 'CID'));
        }
        return false;
    }

    /**
     * Get the corresponding folder level id of a relation
     *
     * @param int $tabid
     * @param int $fieldId
     * @param array $relationIds
     * @return array|false
     */
    private function getRelationLevelIds(int $tabid, int $fieldId, array $relationIds): bool|array
    {
        global $db,
               $gtab,
               $gfield;

        if (empty($relationIds)) {
            return false;
        }

        $md5_tab = $gfield[$tabid]['md5tab'][$fieldId];

        $ldmsTabId = (int)$gtab['argresult_id']['LDMS_FILES'];
        $relationTabId = null;
        if (array_key_exists($fieldId, $gfield[$tabid]['verkntabid'])) {
            $relationTabId = (int)$gfield[$tabid]['verkntabid'][$fieldId];
        }

        if ($relationTabId === $ldmsTabId) {

            $sql = 'SELECT LID, VERKN_ID FROM ' . $md5_tab . ' WHERE VERKN_ID IN (' . implode(',', $relationIds) . ')';

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
     * @param int $tabId
     * @param int $fieldId
     * @return int|null
     */
    private function getRelationTableId(int $tabId, int $fieldId): ?int
    {
        global $gfield;

        $relationTabId = null;

        if (array_key_exists('verknparams', $gfield[$tabId]) && array_key_exists($fieldId, $gfield[$tabId]['verknparams'])) {
            $relationTabId = intval($gfield[$tabId]['verknparams'][$fieldId]);
        }

        return $relationTabId;
    }

    /**
     * Returns the real key id of a relation table
     *
     * @param int $tabId
     * @param int $recordId
     * @param int $relatedId
     *
     * @return int|null converted id or false if not found
     */
    private function getRelKeyID(int $tabId, int $recordId, int $relatedId): ?int
    {
        global $db;
        global $gtab;

        $table = Dbf::handleCaseSensitive($gtab['table'][$tabId]);
        $keyField = $gtab['keyfield'][$tabId];
        $sql = "SELECT $keyField AS CID FROM $table WHERE ID = $recordId AND VERKN_ID = $relatedId";
        $rs = lmbdb_exec($db, $sql);

        if (lmbdb_fetch_row($rs)) {
            return lmbdb_result($rs, 'CID');
        }
        return null;
    }
    
}
