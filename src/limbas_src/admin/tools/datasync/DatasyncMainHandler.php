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

class DatasyncMainHandler extends Datasync
{

    private $globalTables = [];

    public function __construct(array $template, int $clientId)
    {
        $this->isMain = true;
        $this->currentClient = $clientId;
        parent::__construct($template);
    }

    /**
     * Performs all necessary synchronisation steps for one slave with soap
     *
     * @param string $url
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function runSyncWithSlaveSoap(string $url, string $username, string $password): bool
    {
        global $umgvar;

        DatasyncLog::info('[Method soap' . ($umgvar['sync_transaction'] ? '' : ' (NT)') . ']');


        //TODO: error handling without transactions
        $this->startTransaction();

        $datasyncMainData = $this->collectChangedData();
        if ($datasyncMainData === false) {
            DatasyncLog::error('Data could not be collected');
            return false;
        }
        $datasyncMainDataErrors = $datasyncMainData->errors; // local errors
        $datasyncMainData->errors = []; // don't send local errors of main to client

        $package = new DatasyncPackage($this->template);
        $package->setData($datasyncMainData);

        // config soap
        $LIM = array(
            'lim_url' => $url,
            'username' => $username,
            'pass' => $password
        );

        $lmpar[0]['action'] = 'run_datasync';
        $lmpar[0]['sync_package'] = $package;
        DatasyncLog::info('Sending data to client');

        $datasyncClientPackage = soap_call_client($lmpar, $LIM);

        if ($datasyncClientPackage === false || $datasyncClientPackage === null) {
            DatasyncLog::error('Data could not be transferred to client');
            $this->endDataSync(false, $datasyncMainDataErrors);
            return false;
        }


        if (!($datasyncClientPackage instanceof DatasyncPackage)) {
            DatasyncLog::error('Received data is not a sync package');
            $this->endDataSync(false, $datasyncMainDataErrors);
            return false;
        }

        // receive datasync package from client
        if ($datasyncClientPackage->success === false) {
            DatasyncLog::error('Data could not be processed on client');
            DatasyncLog::getLogger()->appendLog($datasyncClientPackage->log, 'From client: ');
            $this->endDataSync(false, $datasyncMainDataErrors);
            return false;
        }

        /** @var DatasyncData $datasyncClientData */
        $datasyncClientData = $datasyncClientPackage->data;
        // get apply errors from client data
        $datasyncMainDataErrors = $datasyncMainDataErrors + $datasyncClientData->remoteErrors;
        DatasyncLog::getLogger()->appendLog($datasyncClientPackage->log, 'From client: ');

        // apply datasync package from client
        $applyResult = $this->applyChangedData($datasyncClientData);
        if ($applyResult === false) {
            DatasyncLog::error('Data could not be applied');
            $this->endDataSync(false, $datasyncMainDataErrors);
            return false;
        }

        $package = new DatasyncPackage($this->template);
        $datasyncClientData->tableData = [];
        $datasyncClientData->newRecords = [];
        $datasyncClientData->remoteErrors = [];
        $package->setData($datasyncClientData);

        // send package back to client
        $lmpar = array();
        $lmpar[0]['action'] = 'finish_datasync';
        $lmpar[0]['sync_package'] = $package;

        DatasyncLog::info('Sending final status to client');
        $datasyncClientPackage = soap_call_client($lmpar, $LIM);
        if (!($datasyncClientPackage instanceof DatasyncPackage) || $datasyncClientPackage->success === false) {
            DatasyncLog::error('Final status could not be sent to client');
            if ($datasyncClientPackage instanceof DatasyncPackage) {
                DatasyncLog::getLogger()->appendLog($datasyncClientPackage->log, 'From client: ');
            }
            $this->endDataSync(false, $datasyncMainDataErrors);
            return false;
        }

        $this->endDataSync(true, $datasyncMainDataErrors, $datasyncMainData->timestamp);

        if (!empty($datasyncMainDataErrors)) {
            $exceptionCount = count($datasyncMainDataErrors);
            DatasyncLog::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
        }

        return true;
    }

    private function endDataSync(bool $status, array $errors, int $timestamp = null): void
    {
        // TODO: handle already successfully created records on client
        $this->endTransaction($status);
        $this->writeErrorsToSyncLog($errors);

        if ($timestamp === null) {
            return;
        }
        //clean up cache => all successfull removed
        $this->resetCache($timestamp);
    }


    /**
     * Performs all necessary synchronisation steps for one slave through socket
     *
     * @param string $url
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function runSyncWithSlaveSocket($url, $username, $password): bool
    {
        global $umgvar;

        DatasyncLog::info('[Method socket' . ($umgvar['sync_transaction'] ? '' : ' (NT)') . ']');

        //tell slave to start socket server
        $LIM = array(
            'lim_url' => $url,
            'username' => $username,
            'pass' => $password
        );
        $lmpar[0]['action'] = 'socket_datasync';
        DatasyncLog::info('Initiating socket');
        $lmb = soap_call_client($lmpar, $LIM);
        if (!$lmb || !is_array($lmb) || !$lmb[0] || !is_array($lmb[0])
            || !$lmb[0]['success'] || $lmb[0]['success'] !== true
            || !$lmb[0]['port'] || !is_numeric($lmb[0]['port'])
            || !$lmb[0]['authKey']) {
            DatasyncLog::error('Socket server could not be opened');
            return false;
        }
        $port = $lmb[0]['port'];
        $authKey = $lmb[0]['authKey'];

        $success = true;
        if ($umgvar['sync_transaction']) {
            lmb_StartTransaction();
        }
        DatasyncLog::info('Transaction started');
        try {
            $url = parse_url($url);
            $url = $url['host'];
            $factory = new Factory();
            $socket = $factory->createClient("$url:$port");
            $data = $this->collectChangedData();

            //send data to slave
            DatasyncLog::info('Sending data to client');
            $message = array(
                'action' => 'setup_datasync',
                'sync_data' => $data,
                'sync_template' => $this->template,
                'authKey' => $authKey
            );
            $socket->lmbWriteArr($message);


            # wait for answer
            $result = $socket->lmbReadArr();
            if (!is_array($result) || empty($result)) {
                throw new Exception('Data could not be transferred to client');
            }
            if (!array_key_exists('success', $result) || $result['success'] === false) {
                DatasyncLog::getLogger()->appendLog($result['log'], 'From client: ');
                throw new Exception('Data could not be processed on client');
            }

            $this->sync_exceptions = $result['sync_exceptions'];
            DatasyncLog::getLogger()->appendLog($result['log'], 'From client: ');

            $applyResult = $this->applyChangedData($result);

            if ($applyResult === false) {
                throw new Exception('Error applying data');
            }

            # second final message to slave
            DatasyncLog::info('Sending final status to client');
            $message = array(
                'action' => 'finish_datasync',
                'sync_exceptions' => $this->sync_exceptions,
                'authKey' => $authKey
            );
            $socket->lmbWriteArr($message);
            # wait for answer
            $result = $socket->lmbRead();
            if ($result === false) {
                throw new Exception('Final status could not be sent to client');
            }


            #$sync_exceptions = array_merge($sync_exceptions,$result['sync_exceptions']);
            #print_r($sync_exceptions);

            //clean up cache
            $this->resetCache($data['timestamp']);
        } catch (Throwable $t) {
            $success = false;
            DatasyncLog::error($t->getMessage());
        }
        $socket?->close();

        $this->writeErrorsToSyncLog();

        $this->endTransaction($success);
        DatasyncLog::info('Transaction ' . (($success) ? 'successfully finished' : 'aborted'));

        if (!empty($this->sync_exceptions)) {
            $exceptionCount = count($this->sync_exceptions);
            DatasyncLog::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
        }

        return $success;
    }

}
