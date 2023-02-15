<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



require_once(COREPATH . 'admin/tools/datasync/DatasyncClient.php');

use admin\tools\update\Updater;

global $umgvar;

$manualUpdateCheck = false;
if (isset($_GET['updatecheck'])) {
    $manualUpdateCheck = true;
}

Updater::addMsgColumn();

$systemInformation = Updater::getSystemInfo();
$latestVersion = Updater::checkNewVersionAvailable($manualUpdateCheck);

$clientId = 0;

$clients = DatasyncClient::all(true);

$alert = null;

?>

<script type="text/javascript" src="assets/js/admin/tools/update.js?v=<?=$umgvar["version"]?>"></script>




<div class="container-fluid p-3">

    <?php if(!empty($clients)): ?>

        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="local-tab" data-bs-toggle="tab" data-bs-target="#local" type="button" role="tab" aria-controls="local" aria-selected="true">Local system</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="remote-tab" data-bs-toggle="tab" data-bs-target="#remote" type="button" role="tab" aria-controls="remote" aria-selected="false">Remote systems</button>
            </li>
        </ul>

    <?php endif; ?>
    <div class="tab-content">
        <div class="tab-pane show active" id="local" role="tabpanel" aria-labelledby="local-tab">

            <?php include(__DIR__ . '/update-status.php'); ?>


        </div>
        <div class="tab-pane" id="remote" role="tabpanel" aria-labelledby="remote-tab">

            <!--<div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <p>Overall Status: <span class="fw-bold"><?= $versions['source'] ?></span></p>

                        </div>
                    </div>
                </div>
            </div>-->

            <div class="accordion" id="clientAccordion">

                <?php foreach($clients as $client): ?>
                
                    <?php $systemInformation = false; ?>
                    <?php include(__DIR__ . '/client.php'); ?>

                <?php endforeach; ?>

            </div>

        </div>
    </div>







</div>


<div class="position-fixed bottom-0 end-0 p-3">
    <div id="patch-error-toast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Patch failed</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="patch-error-msg">

        </div>
    </div>
</div>

