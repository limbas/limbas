<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\mail\MailAccount;
use Limbas\extra\mail\MailTable;

$multiTenantEnabled = false;

if($umgvar['multitenant'] && !empty($lmmultitenants['mid'])) {
    $multiTenantEnabled = true;
}

?>
<input type="hidden" value="<?=$adminMails?'1':'0'?>" id="is-admin">
<div class="container-fluid p-3">

    <table class="table table-sm table-striped table-hover border bg-contrast align-middle">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th><?=$lang[924]?></th>
            <th><?=$lang[521]?></th>
            <?php if($adminMails): ?>
                <th><?=$lang[1242]?></th>
                <?php if($multiTenantEnabled): ?>
                <th><?=$lang[2962]?></th>
                <?php endif; ?>
            <?php endif; ?>
            <th><?=$lang[3127]?></th>
            <th>IMAP</th>
            <th>SMTP</th>
            <th><?=$lang[1205]?></th>
        </tr>
        </thead>

        <tbody id="table-mail-accounts">
        <?php
        /** @var MailAccount $mailAccount */
        foreach($mailAccounts as $mailAccount) :
            include(__DIR__ . '/mail-row.php');
        endforeach; ?>
        </tbody>

    </table>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-mail-account" data-id="0"><i class="fas fa-plus"></i></button>

</div>




<div class="modal fade" id="modal-mail-account" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5"><?=$lang[3128]?></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="mail-name" class="col-sm-3 col-form-label"><?=$lang[924]?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mail-name">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="mail-email" class="col-sm-3 col-form-label"><?=$lang[521]?></label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" id="mail-email">
                    </div>
                </div>
                <?php if($adminMails): ?>
                    <?php if($multiTenantEnabled): ?>
                        <div class="row mb-3">
                            <label for="mail-tenant" class="col-sm-3 col-form-label"><?=$lang[2962]?></label>
                            <div class="col-sm-9">
                                <select class="form-select" id="mail-tenant">
                                    <option value="0"></option>
                                    <?php foreach($lmmultitenants['name'] as $tenantId => $tenantName): ?>
                                        <option value="<?=$tenantId?>"><?=$tenantName?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row mb-3">
                        <label for="mail-user" class="col-sm-3 col-form-label"><?=$lang[1242]?></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="mail-user">
                                <option value="0"></option>
                                <?php foreach($userdat['id'] as $uKey => $id): ?>
                                    <option value="<?=$id?>"><?=$userdat['username'][$id]?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" value="<?=$session['user_id']?>" id="mail-user">
                <?php endif; ?>
                <hr>
                <div class="row mb-3">
                    <label for="mail-status" class="col-sm-3 col-form-label"><?=$lang[1205]?></label>
                    <div class="col-sm-9">
                        <select class="form-select" id="mail-status">
                            <option value="0"><?=$lang[633]?></option>
                            <option value="1"><?=$lang[632]?></option>
                        </select>
                    </div>
                </div>
                <?php if($adminMails):?>
                <div class="row mb-3">
                    <label for="mail-default" class="col-sm-3 col-form-label"><?=$lang[2685]?></label>
                    <div class="col-sm-9 pt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="mail-default">
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row mb-3">
                    <label for="mail-hidden" class="col-sm-3 col-form-label"><?=$lang[2088]?></label>
                    <div class="col-sm-9 pt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="mail-hidden">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <label for="mail-imap-host" class="col-sm-3 col-form-label">IMAP-Host</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mail-imap-host">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="mail-imap-port" class="col-sm-3 col-form-label">IMAP-Port</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" id="mail-imap-port">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="mail-imap-user" class="col-sm-3 col-form-label">IMAP-<?=$lang[1242]?></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mail-imap-user">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="mail-imap-password" class="col-sm-3 col-form-label">IMAP-<?=$lang[141]?></label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" id="mail-imap-password">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="mail-imap-path" class="col-sm-3 col-form-label">IMAP-Path</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mail-imap-path">
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <label for="mail-type" class="col-sm-3 col-form-label"><?=$lang[3127]?></label>
                    <div class="col-sm-9">
                        <select class="form-select" id="mail-type">
                            <option value="<?=MailAccount::TRANSPORT_SMTP?>">SMTP</option>
                            <option value="<?=MailAccount::TRANSPORT_SENDMAIL?>">Sendmail</option>
                            <option value="<?=MailAccount::TRANSPORT_NATIVE?>"><?=$lang[3129]?></option>
                        </select>
                    </div>
                </div>
                <div id="mail-mail-transport-smtp">
                    <div class="row mb-3">
                        <label for="mail-smtp-host" class="col-sm-3 col-form-label">SMTP-Host</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="mail-smtp-host">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="mail-smtp-port" class="col-sm-3 col-form-label">SMTP-Port</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="mail-smtp-port">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="mail-smtp-user" class="col-sm-3 col-form-label">SMTP-<?=$lang[1242]?></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="mail-smtp-user">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="mail-smtp-password" class="col-sm-3 col-form-label">SMTP-<?=$lang[141]?></label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="mail-smtp-password">
                        </div>
                    </div>
                </div>

                <?php if($adminMails && !empty($mailTables)):?>
                    <hr>
                    <div class="row mb-3">
                        <label for="mail-table" class="col-sm-3 col-form-label">Speichertabelle</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="mail-table">
                                <option value="0"></option>
                                <?php
                                /** @var MailTable $mailTable */
                                foreach ($mailTables as $mailTable): ?>
                                    <option value="<?=$mailTable->id?>"><?=$mailTable->name?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                Sofern eine Speichertabelle ausgewählt ist, werden alle Mails ggf. zusätzlich zum IMAP-Upload gespeichert.<br>
                                Je nach Einstellung agiert diese Tabelle als Warteliste für den Mailversand.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-primary" id="btn-save-mail-account"><?=$lang[842]?></button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="assets/js/admin/setup/mail.js?v=<?=$umgvar['version']?>"></script>
