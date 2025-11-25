<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\tools\datasync\DatasyncClient;

?>



<div class="container-fluid p-3">

    <?php include 'tabs.php' ?>


    <div class="accordion mt-3" id="clientAccordion">
        
        <?php
        /** @var DatasyncClient $client */
        foreach($clients as $client): ?>

            <div class="accordion-item mb-3 border bg-contrast">
                <div class="accordion-header" id="heading<?=$client->id?>">
                    <div class="accordion-button collapsed bg-contrast" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$client->id?>" aria-expanded="true" aria-controls="collapse<?=$client->id?>">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-5">
                                <strong class="h3"><?=$client->name?></strong>
                            </div>
                            <div class="col-md-3">
                                Anzahl Einträge: <?=$client->getSyncCacheCount()?>
                            </div>
                            <div class="col-md-3 text-end pe-5">
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div id="collapse<?=$client->id?>" class="accordion-collapse collapse" aria-labelledby="heading<?=$client->id?>" data-id="<?=$client->id?>">
                    <div class="accordion-body">
                        <table id="table<?=$client->id?>" class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>TabId</td>
                                    <td>FieldId</td>
                                    <td>RecordId</td>
                                    <td>ClientRecordId</td>
                                    <td>Type</td>
                                    <td>Date</td>
                                    <td>ProcessKey</td>
                                    <td>Error</td>
                                    <td>Try Count</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        
    </div>
    
    
</div>

<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.min.js"></script>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
<script src="assets/js/admin/tools/datasync/sync_cache.js?v=<?=$umgvar['version']?>"></script>
