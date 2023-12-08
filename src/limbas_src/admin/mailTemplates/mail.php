<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\mailTemplates\MailTemplate;
use Limbas\extra\template\TemplateTable;

?>

<div class="container-fluid p-3">

    <table class="table table-sm table-striped table-hover border bg-white align-middle">
        <thead>
        <tr>
            <th></th>
            <th><?=$lang[949]?></th>
            <th><?=$lang[924]?></th>
            <th><?=$lang[1162]?></th>
            <th><?=$lang[126]?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="table-mail-templates">
        <?php
        $gtabId = null;
        /** @var MailTemplate $mailTemplate */
        foreach($mailTemplates as $mailTemplate) :
            if($gtabId !== $mailTemplate->tabId && $mailTemplate->tabId > 0):
                $gtabId = $mailTemplate->tabId;
                ?>
                <tr class="table-section"><td colspan="12"><?=$gtab['desc'][$mailTemplate->tabId]?></td></tr>
            <?php endif;
            include(__DIR__ . '/mail-row.php');
        endforeach; ?>
        </tbody>

    </table>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-mail-template" data-id="0"><i class="fas fa-plus"></i></button>

</div>


<div class="modal fade" id="modal-mail-template" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Mail-Vorlage hinzufügen</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="template-name" class="col-sm-3 col-form-label"><?=$lang[924]?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="template-name">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="template-desc" class="col-sm-3 col-form-label"><?=$lang[126]?></label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="template-desc">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="template-gtabid" class="col-sm-3 col-form-label"><?=$lang[1162]?></label>
                    <div class="col-sm-9">
                        <select class="form-select" id="template-gtabid">
                            <option value="0">Global</option>
                            <?php foreach ($tabgroup['id'] as $key0 => $value0):?>
                                <optgroup label="<?=$tabgroup['name'][$key0]?>">
                                <?php foreach ($gtab['tab_id'] as $key => $value):
                                    if($gtab['tab_group'][$key] == $value0):
                                    ?>
                                        <OPTION VALUE="<?=$value?>"><?=$gtab['desc'][$key]?></OPTION>
                                <?php
                                endif;
                                endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="template-table" class="col-sm-3 col-form-label">Template-Table</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="template-table">
                            <?php
                            /** @var TemplateTable $templateTable */
                            foreach($templateTables as $templateTable): ?>
                                <option value="<?=$templateTable->id?>" <?=($templateTableId === $templateTable->id) ? 'selected' : ''?>><?=$templateTable->name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-primary" id="btn-save-mail-template"><?=$lang[842]?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="assets/js/admin/mailTemplates/list.js?v=<?=$umgvar['version']?>"></script>
