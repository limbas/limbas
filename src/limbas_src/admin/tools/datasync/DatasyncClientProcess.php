<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync;

use Exception;
use Limbas\admin\tools\datasync\socket\Factory;
use LimbasLogger;
use Throwable;

class DatasyncClientProcess extends Datasync
{


    public function __construct($template)
    {
        $this->is_main = false;
        parent::__construct($template);
    }


    /**
     * Handle all soap requests relevant for synchronisation
     * 
     * @param array $lmpart
     * @return array|bool
     */
    public static function handleSoapRequest(array $lmpart): array|bool
    {

        if (!array_key_exists('sync_template', $lmpart)) {
            return false;
        }

        $datasync = new self($lmpart['sync_template']);

        if ($lmpart['action'] == 'setup_datasync') { //TODO: Rechte
            # check params
            if (!$lmpart['sync_data']) {
                return false;
            }

            return $datasync->handleClient($lmpart['sync_data']);

        } elseif ($lmpart['action'] == 'finish_datasync') {
            # check params
            if (!isset($lmpart['sync_exceptions']) || !isset($lmpart['sync_timestamp'])) {
                return false;
            }

            return $datasync->finish($lmpart['sync_exceptions'], $lmpart['sync_timestamp']);

        } elseif ($lmpart['action'] == 'socket_datasync') {

            return $datasync->startSocket();
        }

        return false;
    }


    /**
     * Run client synchronisation
     * 
     * @param $data
     * @return array|false
     */
    public function handleClient($data): bool|array
    {
        global $umgvar;

        define('LMB_SYNC_PROC', true);

        if ($umgvar['sync_transaction']) {
            lmb_StartTransaction();
        }

        //Apply data received by main
        $newids = $this->applyChangedData($data);
        if ($newids === false) {
            $this->endTransaction(false);
            return ['success' => false, 'log' => LimbasLogger::getLogLines()];
        }

        //Get data changed on this client
        $return = $this->collectChangedData($newids);

        if ($return === false) {
            $this->endTransaction(false);
            return ['success' => false, 'log' => LimbasLogger::getLogLines()];
        }

        $this->endTransaction(true);

        if (!empty($this->sync_exceptions)) {
            $exceptionCount = count($this->sync_exceptions);
            LimbasLogger::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
        }

        //Add sync exceptions to output
        $return['sync_exceptions'] = $this->sync_exceptions;
        $return['log'] = LimbasLogger::getLogLines();
        $return['success'] = true;

        return $return;
    }

    
    /**
     * @param $sync_exceptions
     * @param $sync_timestamp
     * @return array
     */
    public function finish($sync_exceptions, $sync_timestamp): array
    {
        define('LMB_SYNC_PROC', true);

        $this->sync_exceptions = $sync_exceptions;

        $result = false;
        try {
            $result = $this->resetCache($sync_timestamp);
        } catch (Throwable $t) {
            LimbasLogger::error('Reset cache error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
        }

        return ['success' => $result, 'log' => LimbasLogger::getLogLines()];
    }

    /**
     * Start the socket process 
     * @return array[]|false
     * @throws Exception
     */
    public function startSocket(): array|bool
    {
        global $umgvar;

        $port = $umgvar['sync_port'];

        // kill remaining processes
        $cronLocation = COREPATH . 'cron.php';
        $output = shell_exec('ps aux | grep -P "[p]hp ' . preg_quote($cronLocation) . ' .*? .*? .*? limbas_socket_sync_.*" | awk \'{print $2}\'');
        $processIDs = explode("\n", $output);
        $currentPID = getmypid();
        for ($i = 0; $i < count($processIDs); $i++) {
            $pID = $processIDs[$i];
            if ($pID && is_numeric($pID) && intval($pID) !== $currentPID) {
                system('kill ' . $processIDs[$i]);
                sleep(1); // wait for port release
            }
        }

        // generate secret key for authentication via socket
        $authKey = bin2hex(random_bytes(30));
        if (!$authKey) {
            return false;
        }
        $authKey = 'limbas_socket_sync_' . $authKey;

        // open socket server
        $result = shell_exec('php ' . COREPATH . 'cron.php datasync.lib admin limbas123 "' . $authKey . '" >/dev/null 2>&1 &');

        // check status code
        if (!empty($result)) {
            return false;
        }

        // get url
        $urlParts = parse_url($umgvar['url']);
        $url = $urlParts['host'];

        // wait for connection to open
        $factory = new Factory();
        $success = false;
        for ($tries = 0; $tries < $umgvar['sync_timeout']; $tries++) {
            try {
                $client = $factory->createClient("$url:$port");
                $client->close();
                $success = true;
                break;
            } catch (Exception $e) {
                sleep(1);
                continue;
            }
        }

        return [
            [
                'success' => $success,
                'port' => $port,
                'authKey' => $authKey
            ]
        ];
    }


    /**
     * Opens a sync socket and runs sync with master
     *
     * @param string $authKey authentication key generated by master
     * @return void
     */
    public function runSyncWithMasterSocket($authKey)
    {
        define('LMB_SYNC_PROC', true);
        global $umgvar;

        // get url
        $urlParts = parse_url($umgvar['url']);
        $url = $urlParts['host']; # '127.0.0.1';

        $port = $umgvar['sync_port'];

        lmb_StartTransaction();

        $success = false;
        try {
            $factory = new Factory();
            $socket = $factory->createServer("$url:$port", true); // reuse addr
            //first accept -> try connection
            if ($master = $socket->accept()) {
                ///second accept real connection
                if ($master = $socket->accept()) {
                    # get first instruction
                    $data = $master->lmbReadArr();
                    if ($data === null || !is_array($data) || $data['action'] !== 'setup_datasync' || $data['authKey'] !== $authKey) {
                        throw new Exception('Sync failed (s1). Received invalid data');
                    }
                    $this->template = $data['sync_template'];

                    $throwError = false;
                    $newids = $this->applyChangedData($data['sync_data']);
                    if ($newids === false) {
                        $return = ['success' => false, 'log' => LimbasLogger::getLogLines()];
                        $throwError = true;
                    } else {
                        $return = $this->collectChangedData($newids);
                        if ($return === false) {
                            $return = ['success' => false, 'log' => LimbasLogger::getLogLines()];
                            $throwError = true;
                        } else {
                            $return['success'] = true;
                            $return['sync_exceptions'] = $this->sync_exceptions;
                            $return['log'] = LimbasLogger::getLogLines();
                        }
                    }

                    # answer to first sync
                    $master->lmbWriteArr($return);
                    if ($throwError === true) {
                        throw new Exception('Client data handling failed');
                    }

                    # get second instruction
                    $data = $master->lmbReadArr();
                    if (!$data || $data['action'] !== 'finish_datasync' || !array_key_exists('sync_exceptions', $data) || $data['authKey'] !== $authKey) {
                        throw new Exception('Sync failed (s2). Received invalid data');
                    }

                    $this->sync_exceptions = $data['sync_exceptions'] ?: array();

                    //clean up cache
                    $reset = $this->resetCache($return['timestamp']);

                    # answer
                    $master->lmbWrite($reset);
                    $success = true;
                }

            }
        } catch (Throwable $t) {

        }
        if ($socket) {
            $socket->close();
        }
        $this->endTransaction($success);
    }


}
