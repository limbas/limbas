<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
$clients = DatasyncClient::all(true);
$status = DatasyncProcess::status();

?>

<div class="container-fluid p-3">

    <?php include 'tabs.php' ?>

    
    <div class="row mt-4 mb-3">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body h-100 text-center d-flex flex-column align-items-center">
                    <?php if(!empty($status->currentClient)): ?>
                    <p><i class="fas fa-spinner fa-spin-pulse fa-4x text-warning"></i></p>
                    <p>Aktuell ausgeführt wird</p>
                    <p class="fw-bold fa-2x"><?=$status->currentClient->name?></p>
                    <?php else: ?>
                        <p class="pt-4"><i class="fas fa-check-circle fa-4x text-muted"></i></p>
                        <p>Keine laufende Synchronisation</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <p class="mb-2">Abgeschlossen:</p>
                    <p class="fa-3x mb-2"> <?=$status->syncedCount?> / <?=$status->allCount?></p>
                    <p>
                        <?php if($status->nextClient): ?>
                            Nächster: <?=$status->nextClient->name?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <!--<button class="btn btn-primary">Alle resetten</button>-->
                </div>
            </div>
        </div>
    </div>


    <div class="accordion" id="clientAccordion">
        
        <?php
        /** @var DatasyncClient $client */
        foreach($clients as $client): ?>

            <div class="accordion-item mb-3 border bg-white">
                <div class="accordion-header" id="heading<?=$client->id?>">
                    <div class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$client->id?>" aria-expanded="true" aria-controls="collapse<?=$client->id?>">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-1">
                                <?php if($client->getStatus()->status == 0): ?>
                                    <i class="fas fa-spinner fa-spin-pulse fa-3x text-warning"></i>
                                <?php elseif($client->getStatus()->status == 1): ?>
                                    <i class="fa-solid fa-square-check fa-3x text-success"></i>
                                <?php elseif($client->getStatus()->status == 2): ?>
                                    <i class="fa-solid fa-square-xmark fa-3x text-danger"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-circle-question fa-3x text-muted"></i>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-5">
                                <strong class="h3"><?=$client->name?></strong>
                            </div>
                            <div class="col-md-3">
                                Zuletzt ausgeführt: <?=$client->lastStatus()->time?><br>
                                Status: 
                                <?php if($client->getStatus()->status == 0): ?>
                                    <span class="text-warning"><i class="fas fa-spinner fa-spin-pulse fa-fw"></i> Wird ausgeführt</span>
                                <?php elseif($client->getStatus()->status == 1): ?>
                                    <span class="text-success"><i class="fa-solid fa-square-check fa-fw"></i> Erfolgreich</span>
                                <?php elseif($client->getStatus()->status == 2): ?>
                                    <span class="text-danger"><i class="fa-solid fa-square-xmark fa-fw"></i> Mit Fehler</span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                            <div class="col-md-3 text-end pe-5">
                                <?php if($client->getStatus()->status != 0): ?>
                                <!--<button class="btn btn-outline-primary"><i class="fa-solid fa-play"></i> Run</button>-->
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="collapse<?=$client->id?>" class="accordion-collapse collapse" aria-labelledby="heading<?=$client->id?>">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col">
                                <h5>Letztes Protokoll</h5>
                                <div class="code mb-5 border p-3">
                                    <?=nl2br($client->getLastestLog())?>
                                </div>
                                <!--<button class="btn btn-outline-dark">Detailprotokoll laden</button>-->
                            </div>
                            <div class="col">
                                <h5>Historie</h5>
                                
                                <?php $clientHistory = $client->history(5); ?>

                                <div class="accordion">

                                    <?php foreach($clientHistory as $histEntry): ?>

                                        <div class="accordion-item mb-2 border-0">
                                            <div class="card cursor-pointer" data-bs-toggle="collapse" data-bs-target="#collapseHist<?=$histEntry->id?>">
                                                <?php if($histEntry->status === 2): ?>
                                                    <div class="card-body border-start border-danger border-5 py-2">
                                                        <div class="row">
                                                            <div class="col">
                                                                Mit Fehlern
                                                            </div>
                                                            <div class="col text-muted">
                                                                Template: <?=$histEntry->templateName?>
                                                            </div>
                                                            <div class="col text-end">
                                                                <?=$histEntry->startTime->format('d.m.Y H:i:s')?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php elseif($histEntry->status === 1): ?>
                                                <div class="card-body border-start border-success border-5 py-2">
                                                    <div class="row">
                                                        <div class="col">
                                                            Erfolgreich
                                                        </div>
                                                        <div class="col text-muted">
                                                            Template: <?=$histEntry->templateName?>
                                                        </div>
                                                        <div class="col text-end">
                                                            <?=$histEntry->startTime->format('d.m.Y H:i:s')?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php else: ?>
                                                    <div class="card-body border-start border-warning border-5 py-2">
                                                        <div class="row">
                                                            <div class="col">
                                                                Wird ausgeführt
                                                            </div>
                                                            <div class="col text-muted">
                                                                Template: <?=$histEntry->templateName?>
                                                            </div>
                                                            <div class="col text-end">
                                                                <?=$histEntry->startTime->format('d.m.Y H:i:s')?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div id="collapseHist<?=$histEntry->id?>" class="accordion-collapse collapse">
                                                <div class="accordion-body bg-white border border-top-0">
                                                    <?=nl2br($histEntry->log)?>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        
    </div>
    
    
</div>
