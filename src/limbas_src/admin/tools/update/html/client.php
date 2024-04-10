<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$statusLoaded = false;
$latestVersion = false;
$clientId = $client->id;
$versions = [];
if ($systemInformation) {
    $statusLoaded = true;
    $versions = $systemInformation['versions'];
    $updateNecessary = $systemInformation['updateNecessary'];
    $completed = $systemInformation['completed'];
}


?>

<div class="accordion-item mb-3 border bg-contrast" id="client-<?=$client->id?>">
    <div class="accordion-header" id="heading<?=$client->id?>">
        <div class="accordion-button collapsed bg-contrast" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$client->id?>" aria-expanded="true" aria-controls="collapse<?=$client->id?>">
            <div class="row w-100 align-items-center">
                <div class="col-md-1">
                    <?php if($statusLoaded): ?>

                        <?php if($updateNecessary): ?>
                            <i class="fa-solid fa-times-circle fa-3x text-danger"></i>
                        <?php elseif($completed['current']): ?>
                            <i class="fa-solid fa-check-circle fa-3x text-success"></i>
                        <?php else: ?>
                            <i class="fas fa-info-circle fa-3x text-warning"></i>
                        <?php endif; ?>
                    
                    <?php else: ?>
                        <i class="fa-solid fa-circle-question fa-3x text-muted"></i>
                    <?php endif; ?>
                </div>
                <div class="col-md-5">
                    <strong class="h3"><?=$client->name?></strong>
                </div>
                <div class="col-md-3">
                    <?php if($statusLoaded): ?>
                    Source-Version: <span class="fw-bold"><?= $versions['source'] ?></span><br>
                    Database-Version: <span class="fw-bold"><?= $versions['db'] ?></span><br>
                    Status:
                        <?php if($updateNecessary): ?>
                            <span class="text-danger"><i class="fa-solid fa-square-xmark fa-fw"></i> Update necessary</span>
                        <?php elseif($completed['current']): ?>
                            <span class="text-success"><i class="fa-solid fa-square-check fa-fw"></i> Latest version</span>
                        <?php else: ?>
                            <span class="text-warning"><i class="fas fa-info-circle fa-fw"></i> Some patches failed</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 text-end pe-5">                    
                    <?php if($statusLoaded && ($updateNecessary || !$completed['current'])): ?>
                        <button class="btn btn-outline-<?= $updateNecessary && !$completed['current'] ? 'danger' : 'warning' ?> btn-run-all" data-version="0" data-client="<?= $clientId ?>"><i class="fa-solid fa-play"></i> Run updates</button>
                    <?php elseif(!$statusLoaded): ?>
                        <button class="btn btn-outline-secondary" data-fetch="<?=$client->id?>"><i class="fa-solid fa-download"></i> Fetch status</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="collapse<?=$client->id?>" class="accordion-collapse collapse" aria-labelledby="heading<?=$client->id?>">
        <div class="accordion-body">            
            
            
            <?php if($statusLoaded): ?>
            
            <?php include(__DIR__ . '/update-status.php') ?>
            
            <?php else: ?>

                <div class="d-flex justify-content-center align-items-center">
                    Status not fetched. <button class="btn btn-sm btn-outline-secondary ms-2" data-fetch="<?=$client->id?>">Fetch now</button>
                </div>
            
            <?php endif; ?>
            
        </div>
    </div>
</div>
