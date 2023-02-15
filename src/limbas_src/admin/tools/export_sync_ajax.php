<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



?>


<?php

require_once(COREPATH . 'admin/tools/export.lib');
require_once(COREPATH . 'admin/tools/import_sync.php');

function lmb_syncToClient($params){

    global $umgvar;
    global $session;

    $response['success'] = 0;
    $syncID = $params['syncid'];                            # sync slave ID
    $syncModules = $params['syncmodule'];                   # modules to sync
    $sync_to_slaves = $params['sync_to_slaves'];            # sync to host or slaves
    $sync_export_remote = $params['sync_export_remote'];    # precheck / export
    $stamp = $params['stamp'];                              # identifier


    // logfile
    $logpath = $umgvar['path'] . "/TEMP/log/structuresync/".date('y_m_d_G_i_s',$stamp).".log";
    if(!file_exists($umgvar['path'] . "/TEMP/log/structuresync")){
        mkdir($umgvar['path'] . "/TEMP/log/structuresync");
    }

    if(!is_array($syncModules)){
        echo json_encode($response);
        return;
    }

    $synccallextensionfunction = $syncModules['synccallextensionfunction'];
    unset($syncModules['syncToSlavesActive']);

    $syncModules = array_keys($syncModules);

    if($synccallextensionfunction) {
        $syncModules[] = 'callextensionfunction ' . $synccallextensionfunction;
    }

    #error_log(print_r($syncModules,1));

    $precheck = 1;
    $confirm = null;

    if($params['precheck_level']){
        $precheck = 2;
    }

    if($sync_export_remote == 'confirm') {
        $confirm = 1;
    }

    if($sync_to_slaves == 'sync_to_host') {
        $slaves['name'][0] = 'single host';
        $slaves['slave_url'][0] = trim($params['remoteHost']);
        $slaves['slave_username'][0] = trim($params['remoteUser']);
        $slaves['slave_pass'][0] = trim($params['remotePass']);
        $syncID = 0;
    }else{
        $slaves = lmb_GetSyncSlaves();
    }

    $logentry[] = local_date(0)." : starting job \t (".$slaves['slave_url'][$syncID].")";

    $response = lmbExport_remoteSync($syncModules, $slaves['slave_url'][$syncID], $slaves['slave_username'][$syncID], $slaves['slave_pass'][$syncID], $precheck, $confirm, $_SESSION['structure_sync_run'][$stamp]);
    $_SESSION['structure_sync_run'][$stamp] = 1;

    # if answer valid
    if($response && is_array($response)) {

        // $alex = bin2hex(random_bytes(8));
        $lmblogger = LimbasLogger::getLogLines();
        if(is_array($lmblogger)){
            $lmblogger = implode("<br>",$lmblogger);
        }

        # log errors
        if (!$response['success']) {

            $response['success'] = 0;
            unset($response['output']);
            $notice = ' - error occurred! see log for details';
            $logentry[] = local_date(0).' : failure';
            $logentry[] = print_r($response['log'],1);

        }elseif((!array_key_exists('output', $response) OR !$response['output']) AND !$response['log']){

            $notice = 'no differencesdetected';
            $logentry[] = $notice;

        }else {

            $notice = 'view details';

            // checksum output
            if ($response['output']) {
                $checksum = md5(implode(' ', $response['output']));
                if (!$session['structure_sync_checksum'][$stamp]) {
                    $session['structure_sync_checksum'][$stamp] = $checksum;
                }
                if ($checksum == $session['structure_sync_checksum'][$stamp]) {
                    $response['checksum'] = 'true';
                } else {
                    $response['checksum'] = 'false';
                }
            }

            // logging
            $logentry[] = local_date(0).' : success';
            $logentry[] = print_r($response,1);

        }

        file_put_contents($umgvar['path']."/USER/".$session["user_id"]."/temp/syncexp_$syncID.html",lmb_renderDiffToBootstrap($response));

        $response['notice'] = $notice;
        unset($response['output']);
        echo json_encode($response);



    }else{
        $response = array();
        $response['log'][] = 'Invalid answer of remote system or not available';
        $logentry[] = print_r($response['log'],1);

        // save output log
        file_put_contents($umgvar['path'] . "/USER/" . $session["user_id"] . "/temp/syncexp_$syncID.html", implode("<br>",$logentry).'<br><br>'.$lmblogger);
        echo json_encode($response);
    }



    if($confirm) {
        if ($lmblogger) {
            $logentry[] = $lmblogger;
        }
        $logentry[] = local_date(0) . " : ending job \t (" . $slaves['slave_url'][$syncID] . ")";
        $logf = fopen($logpath, 'w+');
        fwrite($logf, implode("\n", $logentry));
        fclose($logf);
    }
}