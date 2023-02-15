<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class DatasyncClient
{
    public int $id;
    public $name;
    public $url;
    public $username;
    public $pass;
    public $rs_path;
    public $rs_user;
    public $rs_params;
    public $active;
    public $synced;
    public $order;

    private $db;

    private $currentStatus;
    private $lastStatus;

    private $logPath;
    
    private array $processLogCache;

    public function __construct(int $id, string $name, string $url, string $username, string $pass, string $rs_path, string $rs_user, string $rs_params, bool $active, bool $synced, int $order)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->username = $username;
        $this->pass = $pass;
        $this->rs_path = $rs_path;
        $this->rs_user = $rs_user;
        $this->rs_params = $rs_params;
        $this->active = $active;
        $this->synced = $synced;
        $this->order = $order;

        $this->db = Database::get();

        $this->processLogCache = [];

        $this->logPath = TEMPPATH . 'log/datasync/';
        if (!file_exists($this->logPath)) {
            mkdir($this->logPath);
        }
    }


    /**
     * @return object
     */
    public function getStatus(): object
    {

        if (!$this->currentStatus) {
            $status = -1;
            $time = '-';

            $history = $this->history(1);

            if (!empty($history)) {
                $historyEntry = $history[0];

                $status = $historyEntry->status;

                $timePast = $historyEntry->startTime->diff(new DateTime());

                if ($timePast->days < 1) {
                    if ($timePast->h < 1) {
                        $time = $timePast->i . 'min';
                    } else {
                        $time = $timePast->h . 'h ' . $timePast->i . 'min';
                    }
                } else {
                    $time = $historyEntry->startTime->format('d.m.Y H:i');
                }


            }

            $this->currentStatus = (object)['status' => $status, 'time' => $time];
        }

        return $this->currentStatus;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function history(int $limit = 15): array
    {

        $sqlquery = 'SELECT LMB_SYNC_HISTORY.ID, CLIENT_ID, START_TIME, END_TIME, STATUS, TEMPLATE_ID, LMB_SYNC_TEMPLATE.NAME as TEMPLATE_NAME FROM LMB_SYNC_HISTORY LEFT JOIN LMB_SYNC_TEMPLATE ON LMB_SYNC_TEMPLATE.ID = LMB_SYNC_HISTORY.TEMPLATE_ID WHERE CLIENT_ID = ' . $this->id . '  ORDER BY START_TIME DESC LIMIT ' . parse_db_int($limit);

        $rs = lmbdb_exec($this->db, $sqlquery);

        $history = [];

        while (lmbdb_fetch_row($rs)) {

            $startTime = new DateTime();
            $endTime = new DateTime();
            try {
                $startTime = new DateTime(lmbdb_result($rs, 'START_TIME'));
                $endTime = new DateTime(lmbdb_result($rs, 'END_TIME'));
            } catch (Throwable $t) {
            }


            $histId = (int)lmbdb_result($rs, 'ID');

            $history[] = (object)[
                'id' => $histId,
                'client_id' => (int)lmbdb_result($rs, 'CLIENT_ID'),
                'startTime' => $startTime,
                'endTime' => $endTime,
                'status' => (int)lmbdb_result($rs, 'STATUS'),
                'templateId' => lmbdb_result($rs, 'TEMPLATE_ID'),
                'templateName' => lmbdb_result($rs, 'TEMPLATE_NAME'),
                'log' => $this->getProcessLog($histId, $startTime->format('Y-m-d'))
            ];

        }

        return $history;
    }

    /**
     * @return object
     */
    public function lastStatus(): object
    {

        if (!$this->lastStatus) {
            $status = -1;
            $time = 'Nie';

            $history = $this->history(2);

            if (!empty($history)) {
                $historyEntry = $history[0];
                $historyEntryCount = count($history);

                if ($historyEntry->status == 0 && $historyEntryCount <= 1) {
                    $historyEntry = null;
                } else if ($historyEntry->status == 0 && $historyEntryCount > 1) {
                    $historyEntry = $history[1];
                }

                if ($historyEntry) {
                    $status = $historyEntry->status;

                    $timePast = $historyEntry->startTime->diff(new DateTime());

                    if ($timePast->days < 1) {
                        if ($timePast->h < 1) {
                            $time = 'vor ' . $timePast->i . ' min';
                        } else {
                            $time = 'vor ' . $timePast->h . 'h ' . $timePast->i . ' min';
                        }
                    } else {
                        $time = $historyEntry->startTime->format('d.m.Y H:i');
                    }
                }


            }

            $this->lastStatus = (object)['status' => $status, 'time' => $time];
        }


        return $this->lastStatus;
    }

    /**
     * @param int $id
     * @return DatasyncClient|null
     */
    public static function get(int $id): DatasyncClient|null
    {
        return self::all(id: $id);
    }

    /**
     * @param bool $onlyActive
     * @param $id
     * @param $synced
     * @return array|mixed|null
     */
    public static function all(bool $onlyActive = false, $id = null, $synced = null): mixed
    {
        global $action;

        $db = Database::get();

        $where = self::getWhere($onlyActive, $id, $synced);

        $sqlquery = 'SELECT ID,NAME,SLAVE_URL,SLAVE_USERNAME,SLAVE_PASS,RS_USER,RS_PARAMS,RS_PATH,ACTIVE,SYNCED,SYNC_ORDER FROM LMB_SYNC_CLIENTS ' . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY SYNC_ORDER';

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);

        $output = [];

        while (lmbdb_fetch_row($rs)) {

            $output[] = new self((int)lmbdb_result($rs, 'ID'), lmbdb_result($rs, 'NAME') ?? '', lmbdb_result($rs, 'SLAVE_URL') ?? '', lmbdb_result($rs, 'SLAVE_USERNAME') ?? '', lmbdb_result($rs, 'SLAVE_PASS') ?? '', lmbdb_result($rs, 'RS_PATH') ?? '', lmbdb_result($rs, 'RS_USER') ?? '', lmbdb_result($rs, 'RS_PARAMS') ?? '', boolval(lmbdb_result($rs, 'ACTIVE')), boolval(lmbdb_result($rs, 'ACTIVE')), (int)lmbdb_result($rs, 'ACTIVE'));

        }

        if ($id !== null) {

            if (empty($output)) {
                return null;
            }

            return $output[0];
        }

        return $output;

    }


    /**
     * @param bool $onlyActive
     * @param $id
     * @param $synced
     * @return int
     */
    public static function count(bool $onlyActive = false, $id = null, $synced = null): int
    {
        $db = Database::get();

        $where = self::getWhere($onlyActive, $id, $synced);

        $sqlquery = 'SELECT Count(ID) as CC FROM LMB_SYNC_CLIENTS ' . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');

        $rs = lmbdb_exec($db, $sqlquery);

        $count = 0;

        while (lmbdb_fetch_row($rs)) {

            $count = (int)lmbdb_result($rs, 'CC');

        }

        return $count;
    }


    /**
     * @param bool $onlyActive
     * @param $id
     * @param $synced
     * @return array
     */
    private static function getWhere(bool $onlyActive = false, $id = null, $synced = null): array
    {
        $where = [];

        if ($onlyActive) {
            $where[] = 'ACTIVE = ' . LMB_DBDEF_TRUE;
        }

        if ($id !== null) {
            $where[] = 'ID = ' . parse_db_int($id);
        }

        if ($synced === true) {
            $where[] = 'SYNCED = ' . LMB_DBDEF_TRUE;
        } elseif ($synced === false) {
            $where[] = 'SYNCED = ' . LMB_DBDEF_FALSE;
        }

        return $where;
    }

    /**
     * @return object
     */
    public function logLatestFile(): object
    {
        $name = $this->id . '-latest.log';
        return (object)['path' => $this->logPath . $name, 'name' => $name];
    }

    /**
     * @return object
     */
    public function logDailyFile(): object
    {
        $name = $this->id . '-' . date('Y-m-d') . '.log';
        return (object)['path' => $this->logPath . $name, 'name' => $name];
    }

    /**
     * @return false|string
     */
    public function getLastestLog(): bool|string
    {

        $log = '';

        $logFile = $this->logLatestFile()->path;
        if (file_exists($logFile)) {
            $log = file_get_contents($logFile);
        }

        return $log;
    }

    /**
     * @param int $historyId
     * @param string $date
     * @return false|string
     */
    public function getProcessLog(int $historyId, string $date): bool|string
    {
        
        if (!array_key_exists($historyId, $this->processLogCache)) {
            $this->processLogCache[$historyId] = '';
            
            $logFile = $this->logPath . $this->id . '-' . $date . '.log';
            
            $currentId = 0;
            $handle = fopen($logFile, 'r');
            if ($handle) {
                while (!feof($handle)) {
                    $line = fgets($handle);
                    /*if (str_contains($line, 'INFO [' . $historyId . ']')) {
                        $logFound = true;
                    }
                    if ($logFound) {
                        $output .= $line . "\n";
                    }
                    if (str_contains($line, 'INFO Sync finished')) {
                        break;
                    }*/

                    if (str_contains($line, 'Start sync')) {
                        $matches = [];
                        preg_match('/\[(\d+)\]/', $line, $matches);
                        if (array_key_exists(1,$matches)) {
                            $currentId = (int) $matches[1];
                        }
                    }

                    if (!array_key_exists($currentId,$this->processLogCache)) {
                        $this->processLogCache[$currentId] = '';
                    }

                    $this->processLogCache[$currentId] .= $line;

                }
                fclose($handle);
            }
        }
        
        return $this->processLogCache[$historyId];
    }


    /**
     * @param bool $synced
     * @return void
     */
    public function setSynced(bool $synced): void
    {
        $this->synced = $synced;
        $db = Database::get();
        $sql = 'UPDATE LMB_SYNC_CLIENTS SET SYNCED = ' . ($synced ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE) . ' WHERE ID = ' . $this->id;
        lmbdb_exec($db, $sql);
    }

    /**
     * @return void
     */
    public static function resetAllSynced(): void
    {
        $db = Database::get();

        $sqlquery = 'UPDATE LMB_SYNC_CLIENTS SET SYNCED = ' . LMB_DBDEF_FALSE;

        lmbdb_exec($db, $sqlquery);
    }


    /**
     * @param $lmpar
     * @return array|bool
     */
    private function runSoapRequest($lmpar): array|bool
    {

        // config soap
        $LIM = array(
            'lim_url' => $this->url,
            'username' => $this->username,
            'pass' => $this->pass
        );

        $result = soap_call_client($lmpar, $LIM);


        if ($result === false) {
            return false;
        }

        return $result;

    }

    /**
     * Get version and update status of client
     *
     * @return false|mixed
     */
    public function fetchUpdateStatus(): mixed
    {
        $lmpar = [
            ['action' => 'get_system_update']
        ];


        $result = $this->runSoapRequest($lmpar);

        $output = false;
        if (is_array($result) && !empty($result)) {

            if (!array_key_exists('success', $result) || $result['success'] === false) {
                return false;
            }

            $output = $result['data'];

        }

        return $output;
    }

    /**
     * @param $soapAction
     * @param $action
     * @param $params
     * @return array|bool
     */
    public function runRemoteAction($soapAction, $action, $params): array|bool
    {
        $lmpar = [
            [
                'action' => $soapAction,
                'clientAction' => $action,
                'params' => $params,
            ]
        ];

        return $this->runSoapRequest($lmpar);
    }

}
