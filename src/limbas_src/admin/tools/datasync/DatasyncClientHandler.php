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
use Limbas\admin\tools\datasync\Data\DatasyncData;
use Limbas\admin\tools\datasync\Data\DatasyncPackage;
use Limbas\admin\tools\datasync\socket\Factory;
use Throwable;

class DatasyncClientHandler extends Datasync
{


    public function __construct(array $template)
    {
        $this->isMain = false;
        parent::__construct($template);
    }


    /**
     * Handle all soap requests relevant for synchronisation
     *
     * @param array $request
     * @return array|bool
     * @throws Exception
     */
    public static function handleSoapRequest(array $request): bool|DatasyncPackage
    {

        if (!array_key_exists('sync_package', $request)) {
            return false;
        }

        $datasyncPackage = $request['sync_package'];
        $datasyncClientHandler = new self($datasyncPackage->template);

        $action = $request['action'];

        if ($action === 'run_datasync') {
            if (empty($datasyncPackage->data)) {
                return false;
            }
            try {
                return $datasyncClientHandler->handleClient($datasyncPackage->data);
            } catch (Throwable $t) {
                DatasyncLog::error('Error during client handling: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
                return (new DatasyncPackage())->setLog(DatasyncLog::getLogger()->getLog())->setSuccess(false);
            }
        } elseif ($action === 'finish_datasync') {
            if (empty($datasyncPackage->data)) {
                return false;
            }

            return $datasyncClientHandler->finish($datasyncPackage->data);

        } elseif ($action === 'socket_datasync') {

            return $datasyncClientHandler->startSocket();
        }

        return false;
    }


    /**
     * Run client synchronisation
     *
     * @param DatasyncData $datasyncData
     * @return array|false
     */
    public function handleClient(DatasyncData $datasyncData): bool|DatasyncPackage
    {
        global $umgvar;

        // check if the current system is marked as master
        if (empty($umgvar['sync_mode'])) {
            DatasyncLog::error('Configuration mismatch: Attempt to synchronise with a master.');
            return (new DatasyncPackage())->setLog(DatasyncLog::getLogger()->getLog())->setSuccess(false);
        }

        define('LMB_SYNC_PROC', true);

        $this->startTransaction();

        //TODO: error handling without transactions

        //Apply data received by main
        $result = $this->applyChangedData($datasyncData);
        if ($result === false) {
            DatasyncLog::error('Data could not be applied');
            $this->endTransaction(false);
            return (new DatasyncPackage())->setLog(DatasyncLog::getLogger()->getLog())->setSuccess(false);
        }

        //Get data changed on this client
        $datasyncClientData = $this->collectChangedData();
        if ($datasyncClientData === false) {
            DatasyncLog::error('Data could not be collected');
            $this->endTransaction(false);
            return (new DatasyncPackage())->setLog(DatasyncLog::getLogger()->getLog())->setSuccess(false);
        }

        $this->endTransaction(true);

        if (!empty($datasyncClientData->errors)) {
            $exceptionCount = count($datasyncClientData->errors);
            DatasyncLog::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
        }

        $this->writeErrorsToSyncLog($datasyncClientData->errors);

        //Add sync exceptions to output
        $datasyncClientData->errors = []; // don't send local errors of client to main
        $datasyncClientData->remoteErrors = $datasyncData->errors;
        $datasyncClientData->newRecords = $datasyncData->newRecords;

        $datasyncPackage = new DatasyncPackage();
        $datasyncPackage->setData($datasyncClientData);
        $datasyncPackage->setLog(DatasyncLog::getLogger()->getLog());

        return $datasyncPackage;
    }


    /**
     * @param DatasyncData $datasyncData
     * @return DatasyncPackage
     */
    public function finish(DatasyncData $datasyncData): DatasyncPackage
    {
        $result = false;
        try {
            $this->writeErrorsToSyncLog($datasyncData->errors);
            $result = $this->resetCache($datasyncData->timestamp);
        } catch (Throwable $t) {
            DatasyncLog::error('Reset cache error: ' . $t->getMessage() . ' -- ' . $t->getFile() . ' -- ' . $t->getLine());
        }

        $datasyncPackage = new DatasyncPackage();
        $datasyncPackage->success = $result;
        $datasyncPackage->setLog(DatasyncLog::getLogger()->getLog());

        return $datasyncPackage;
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
    public function runSyncWithMasterSocket($authKey): void
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
                        $return = ['success' => false, 'log' => DatasyncLog::getLogger()->getLog()];
                        $throwError = true;
                    } else {
                        $return = $this->collectChangedData();
                        if ($return === false) {
                            $return = ['success' => false, 'log' => DatasyncLog::getLogger()->getLog()];
                            $throwError = true;
                        } else {
                            $return['success'] = true;
                            $return['sync_exceptions'] = $this->sync_exceptions;
                            $return['log'] = DatasyncLog::getLogger()->getLog();
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
