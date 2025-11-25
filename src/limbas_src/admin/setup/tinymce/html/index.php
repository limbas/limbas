<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\setup\tinymce\TinyMceConfig;

?>

<div class="container-fluid p-3">

    <table class="table table-sm table-striped table-hover border bg-contrast align-middle">
        <thead>
        <tr>
            <th></th>
            <th><?=$lang[949]?></th>
            <th><?=$lang[924]?></th>
            <th><?=$lang[2685]?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="table-configurations">
        <?php
        /** @var TinyMceConfig $config */
        foreach($configs as $config) :
            include(__DIR__ . '/tinymce-row.php');
        endforeach; ?>
        </tbody>

    </table>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-config" data-id="0"><i class="fas fa-plus"></i></button>

</div>


<div class="modal fade" id="modal-config" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">TinyMce-Konfiguration hinzufügen</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="config-name" class="col-sm-3 col-form-label"><?=$lang[924]?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="config-name">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="config-default" class="col-sm-3 col-form-label"><?=$lang[2685]?></label>
                    <div class="col-sm-9 pt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="config-default">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="config-desc" class="col-sm-3 col-form-label">Konfiguration</label>
                    <div class="col-sm-9">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Json</th>
                                </tr>
                            </thead>
                            <tbody id="table-configuration">
                            
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary" id="btn-add-config-row"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-primary" id="btn-save-config"><?=$lang[842]?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="assets/js/admin/setup/tinymce.js?v=<?=$umgvar['version']?>"></script>
