<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

class DatasyncProcess
{

    private array $log;

    /**
     * Initializes synchronisation on master
     *
     * @param int $templateId
     * @param int|null $clientId
     * @return int|null
     */
    public function start(int $templateId, int $clientId = null): ?int
    {

        $this->log = [];

        $fp = fopen(TEMPPATH . 'datasync.lock', 'w+');

        if ($fp === false || !flock($fp, LOCK_EX | LOCK_NB)) {
            // Sync already running
            return null;
        }


        if ($clientId !== null) {
            $client = DatasyncClient::get($clientId);
        } else {
            $client = self::getNextClient();
        }

        if (!$client) {
            return null;
        }

        define('LMB_SYNC_PROC', true);

        $this->runSync($templateId, $client);

        fclose($fp);

        return $client->id;
    }

    /**
     * @param int $templateId
     * @param DatasyncClient $client
     * @return void
     */
    private function runSync(int $templateId, DatasyncClient $client): void
    {
        global $umgvar;
        

        $latestLog = $client->logLatestFile();
        $dailyLog = $client->logDailyFile();

        if (file_exists($latestLog->path)) {
            $log = file_get_contents($latestLog->path);
            file_put_contents($dailyLog->path, $log, FILE_APPEND);
            unlink($latestLog->path);
        }

        LimbasLogger::$logfile = $latestLog->path;

        $this->endAllHistoryEntries();
        $historyId = $this->createHistoryEntry($client->id, $templateId, $dailyLog->name);

        

        LimbasLogger::log('[' . $historyId . '] Start sync');
        

        //run import process
        $status = 2;
        try {

            //load template
            $template = $this->loadTemplate($templateId);

            if (is_array($template)) {
                $datasyncMain = new DatasyncMain($template, $client->id);

                $result = false;

                if ($umgvar['sync_method'] == 'soap') {

                    $result = $datasyncMain->runSyncWithSlaveSoap($client->url, $client->username, $client->pass);

                } elseif ($umgvar['sync_method'] == 'socket') {

                    $result = $datasyncMain->runSyncWithSlaveSocket($client->url, $client->username, $client->pass);

                }


                if ($result === true) {

                    $startTime = $datasyncMain->getCacheTimestamp();
                    $globalCache = $this->resetGlobalCache($startTime);
                    if ($globalCache !== true) {
                        LimbasLogger::error('Global cache could not be cleaned');
                    } else {
                        LimbasLogger::log('Global cache cleaned');
                    }

                    if (!empty($globalTables)) {
                        $globalTables = array_unique($globalTables);
                        //refresh Sequences
                        foreach ($globalTables as $globalTable) {
                            lmb_rebuildSequences($globalTable);
                        }
                    }

                    $status = 1;
                }
            } else {
                LimbasLogger::error('Failed to load template');
            }
            

        } catch (Throwable $t) {
            LimbasLogger::error('Error during sync: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
        }

        $client->setSynced(true);
        $this->updateHistoryEntry($client->id, $status);

        LimbasLogger::log('Sync finished' . ($status != 1 ? ' with errors' : ''));

    }

    /**
     * @param $timestamp
     * @return bool
     */
    private function resetGlobalCache($timestamp): bool
    {
        $db = Database ::get();
        
        $timestamp = intval($timestamp);
        
        if(empty($timestamp)) {
            return true;
        }

        $clientCount = DatasyncClient::count();
        
        $sql = 'SELECT Count(ID) as CC, CACHE_ID FROM LMB_SYNC_CACHE LEFT JOIN LMB_SYNC_GLOBAL ON LMB_SYNC_GLOBAL.CACHE_ID = LMB_SYNC_CACHE.ID WHERE SLAVE_ID = 0 AND LMB_SYNC_CACHE.PROCESS_KEY = ' . $timestamp . ' GROUP BY CACHE_ID';
        $rs = lmbdb_exec($db, $sql);
        if (!$rs) {
            return false;
        }

        $obsoleteCacheIds = [];
        if (lmbdb_num_rows($rs) > 0) {
            while (lmbdb_fetch_row($rs)) {

                $cacheId = lmbdb_result($rs, 'CACHE_ID');
                $cc = (int)lmbdb_result($rs, 'CC');

                if ($clientCount >= $cc) {
                    $obsoleteCacheIds[] = $cacheId;
                }

            }
        }

        if (!empty($obsoleteCacheIds)) {
            $obsoleteCacheIdsString = implode(',', $obsoleteCacheIds);
            $sql = 'DELETE FROM LMB_SYNC_GLOBAL WHERE CACHE_ID IN (' . $obsoleteCacheIdsString . ')';
            $rs = lmbdb_exec($db, $sql);
            if (!$rs) {
                return false;
            }
            $sql = 'DELETE FROM LMB_SYNC_CACHE WHERE ID IN (' . $obsoleteCacheIdsString . ')';
            $rs = lmbdb_exec($db, $sql);
            if (!$rs) {
                return false;
            }
        }


        return true;
    }

    /**
     * initially loads a template
     *
     * @return array|false
     */
    private function loadTemplate($templateId): bool|array
    {
        global $db;
        global $gtab;

        $template = [];

        //load all tables marked for synchronisation
        foreach ($gtab['datasync'] as $tabid => $value) {
            if ($value) {

                $template[$tabid] = [
                    'global' => false,
                    'master' => [],
                    'slave' => []
                ];

                if ($gtab['datasync'][$tabid] == 2) {
                    $template[$tabid]['global'] = true;
                    $globalTables[] = $gtab['table'][$tabid];
                }

            }
        }
        if (empty($template)) {
            //No tables are marked for sync.
            return false;
        }

        //load conflict mode
        $sql = 'SELECT CONFLICT_MODE FROM LMB_SYNC_TEMPLATE WHERE ID = ' . parse_db_int($templateId);
        $rs = lmbdb_exec($db, $sql);
        if (lmbdb_num_rows($rs) > 0 && lmbdb_fetch_row($rs)) {
            $template['conflict_mode'] = lmbdb_result($rs, 'CONFLICT_MODE');
        } else {
            //Template does not exist
            return false;
        }

        //load specific field rules
        $sql = 'SELECT TABID, FIELDID, MASTER, SLAVE FROM LMB_SYNC_CONF WHERE TEMPLATE = ' . parse_db_int($templateId);
        $rs = lmbdb_exec($db, $sql);
        if (lmbdb_num_rows($rs) > 0) {
            while ($row = lmbdb_fetch_array($rs)) {
                $row = array_change_key_case($row, CASE_UPPER);
                $tabid = $row['TABID'];
                if (!array_key_exists($tabid, $template)) {
                    continue;
                }
                if ($row['MASTER']) {
                    $template[$tabid]['master'][] = $row['FIELDID'];
                }
                if ($row['SLAVE']) {
                    $template[$tabid]['slave'][] = $row['FIELDID'];
                }
            }
        } else {
            //Template is empty
            return false;
        }

        return $template;
    }

    /**
     * @param int $clientId
     * @param int $templateId
     * @param string $logfile
     * @return int
     */
    private function createHistoryEntry(int $clientId, int $templateId, string $logfile = ''): int
    {

        $db = Database::get();

        $ID = next_db_id('LMB_SYNC_HISTORY');

        //$processId = getmypid();

        $sql = 'INSERT INTO LMB_SYNC_HISTORY (ID,CLIENT_ID,START_TIME,STATUS,TEMPLATE_ID,LOGFILE) VALUES (' . $ID . ', ' . parse_db_int($clientId) . ',' . LMB_DBDEF_TIMESTAMP . ',0,' . $templateId . ', \'' . $logfile . '\')';

        lmbdb_exec($db, $sql);
        return $ID;
    }

    /**
     * @param int $clientId
     * @param int $status
     * @return void
     */
    private function updateHistoryEntry(int $clientId, int $status): void
    {

        $db = Database::get();

        $sql = 'UPDATE LMB_SYNC_HISTORY SET END_TIME = ' . LMB_DBDEF_TIMESTAMP . ', STATUS = ' . parse_db_int($status) . ' WHERE END_TIME IS NULL AND CLIENT_ID = ' . parse_db_int($clientId);

        lmbdb_exec($db, $sql);

    }

    /**
     * @return void
     */
    private function endAllHistoryEntries(): void
    {

        $db = Database::get();

        $sql = 'UPDATE LMB_SYNC_HISTORY SET END_TIME = ' . LMB_DBDEF_TIMESTAMP . ', STATUS = 2 WHERE END_TIME IS NULL AND STATUS = 0';

        lmbdb_exec($db, $sql);

    }


    /**
     * @return DatasyncClient|null
     */
    public static function getCurrentClient(): ?DatasyncClient
    {

        $db = Database::get();

        $sql = 'SELECT CLIENT_ID FROM LMB_SYNC_HISTORY WHERE STATUS = 0'; //TODO: LIMIT 1

        $rs = lmbdb_exec($db, $sql);

        $clientId = 0;

        while (lmbdb_fetch_row($rs)) {
            $clientId = (int)lmbdb_result($rs, 'CLIENT_ID');
            break;
        }

        return DatasyncClient::get($clientId);
    }

    /**
     * @return mixed|null
     */
    public static function getNextClient(): mixed
    {

        $unsyncedClients = DatasyncClient::all(true, synced: false);

        if (empty($unsyncedClients)) {
            DatasyncClient::resetAllSynced();

            $unsyncedClients = DatasyncClient::all(true, synced: false);
        }

        if (empty($unsyncedClients)) {
            return null;
        }

        return $unsyncedClients[0];
    }

    /**
     * @return object
     */
    public static function status(): object
    {


        $syncedClientCount = count(DatasyncClient::all(true, synced: true));
        $allClientCount = count(DatasyncClient::all(true));

        $currentClient = self::getCurrentClient();
        $nextClient = self::getNextClient();


        return (object)[
            'currentClient' => $currentClient,
            'nextClient' => $nextClient,
            'syncedCount' => $syncedClientCount,
            'allCount' => $allClientCount
        ];
    }

}
