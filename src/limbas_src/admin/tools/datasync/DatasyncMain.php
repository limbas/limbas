<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use admin\tools\datasync\socket\Factory;

class DatasyncMain extends Datasync
{

    private $globalTables = [];

    public function __construct($template, $clientId)
    {
        $this->is_main = true;
        $this->current_client = $clientId;
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
    public function runSyncWithSlaveSoap($url, $username, $password): bool
    {
        global $umgvar;

        LimbasLogger::log('[Method soap' . ($umgvar['sync_transaction'] ? '' : ' (NT)') . ']');

        if ($umgvar['sync_transaction']) {
            lmb_StartTransaction();
        }

        $data = $this->collectChangedData();

        // config soap
        $LIM = array(
            'lim_url' => $url,
            'username' => $username,
            'pass' => $password
        );

        $lmpar[0]['action'] = 'setup_datasync';
        $lmpar[0]['sync_data'] = $data;
        $lmpar[0]['sync_template'] = $this->template;
        LimbasLogger::log('Sending data to client');
        $result = soap_call_client($lmpar, $LIM);

        if ($result === false || $result === null) {
            $this->endTransaction(false);
            LimbasLogger::error('Data could not be transferred to client');
            $this->handleExceptions();
            return false;
        }

        if (is_array($result) && !empty($result)) {

            if (!array_key_exists('success', $result) || $result['success'] === false) {
                $this->endTransaction(false);
                LimbasLogger::error('Data could not be processed on client');
                LimbasLogger::appendLogLines($result['log'], 'From client: ');
                $this->handleExceptions();
                return false;
            }

            //TODO: check if override is ok
            $this->sync_exceptions = $result['sync_exceptions'];
            LimbasLogger::appendLogLines($result['log'], 'From client: ');
            $applyResult = $this->applyChangedData($result);

            if ($applyResult === false) {
                $this->endTransaction(false);
                $this->handleExceptions();
                return false;
            }


            $lmpar = array();
            $lmpar[0]['action'] = 'finish_datasync';
            $lmpar[0]['sync_exceptions'] = $this->sync_exceptions;
            $lmpar[0]['sync_timestamp'] = $result['timestamp'];
            $lmpar[0]['sync_template'] = $this->template;
            LimbasLogger::log('Sending final status to client');
            $result = soap_call_client($lmpar, $LIM);
            if ($result === false || !is_array($result) || (!array_key_exists('success', $result) || $result['success'] === false)) {
                LimbasLogger::error('Final status could not be sent to client');
                if (is_array($result) && array_key_exists('log', $result)) {
                    LimbasLogger::appendLogLines($result['log'], 'From client: ');
                }
                $this->endTransaction(false);
                $this->handleExceptions();
                return false;
            }

            $this->endTransaction(true);

            #$sync_exceptions = array_merge_recursive($sync_exceptions,$result['sync_exceptions']);
            $this->handleExceptions();


            //clean up cache
            $this->resetCache($data['timestamp']);

            if (!empty($this->sync_exceptions)) {
                $exceptionCount = count($this->sync_exceptions);
                LimbasLogger::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
            }

            return true;
        }

        $this->endTransaction(false);
        $this->handleExceptions();
        return false;
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

        LimbasLogger::log('[Method socket' . ($umgvar['sync_transaction'] ? '' : ' (NT)') . ']');

        //tell slave to start socket server
        $LIM = array(
            'lim_url' => $url,
            'username' => $username,
            'pass' => $password
        );
        $lmpar[0]['action'] = 'socket_datasync';
        LimbasLogger::log('Initiating socket');
        $lmb = soap_call_client($lmpar, $LIM);
        if (!$lmb || !is_array($lmb) || !$lmb[0] || !is_array($lmb[0])
            || !$lmb[0]['success'] || $lmb[0]['success'] !== true
            || !$lmb[0]['port'] || !is_numeric($lmb[0]['port'])
            || !$lmb[0]['authKey']) {
            LimbasLogger::error('Socket server could not be opened');
            return false;
        }
        $port = $lmb[0]['port'];
        $authKey = $lmb[0]['authKey'];

        $success = true;
        if ($umgvar['sync_transaction']) {
            lmb_StartTransaction();
        }
        LimbasLogger::log('Transaction started');
        try {
            $url = parse_url($url);
            $url = $url['host'];
            $factory = new Factory();
            $socket = $factory->createClient("$url:$port");
            $data = $this->collectChangedData(true);

            //send data to slave
            LimbasLogger::log('Sending data to client');
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
                LimbasLogger::appendLogLines($result['log'], 'From client: ');
                throw new Exception('Data could not be processed on client');
            }

            $this->sync_exceptions = $result['sync_exceptions'];
            LimbasLogger::appendLogLines($result['log'], 'From client: ');

            $applyResult = $this->applyChangedData($result);

            if ($applyResult === false) {
                throw new Exception('Error applying data');
            }

            # second final message to slave
            LimbasLogger::log('Sending final status to client');
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
            LimbasLogger::error($t->getMessage());
        }
        if ($socket) {
            $socket->close();
        }

        $this->handleExceptions();

        $this->endTransaction($success);
        LimbasLogger::log('Transaction ' . (($success) ? 'successfully finished' : 'aborted'));

        if (!empty($this->sync_exceptions)) {
            $exceptionCount = count($this->sync_exceptions);
            LimbasLogger::warning('Some records may not have been synchronized (' . $exceptionCount . '). See log for details.');
        }

        return $success;
    }

}
