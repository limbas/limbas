<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\mail\MailSignature;

$multiTenantEnabled = false;

if($umgvar['multitenant'] && !empty($lmmultitenants['mid'])) {
    $multiTenantEnabled = true;
}

?>

<script src="assets/js/admin/setup/mail-signature.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar["version"]?>"></script>

<input type="hidden" value="<?=$adminSignatures?'1':'0'?>" id="is-admin">
<div class="container-fluid p-3">

    <ul class="nav nav-tabs">
        <?php if ($LINK['user_mails'] || $LINK['setup_mails']): ?>
            <li class="nav-item">
                <?php if($adminSignatures): ?>
                    <a class="nav-link" href="main_admin.php?action=setup_mails"><?=$lang[$LINK["desc"][$LINK_ID['setup_mails']]]?></a>
                <?php else: ?>
                    <a class="nav-link" href="main.php?action=user_mails"><?=$lang[$LINK["desc"][$LINK_ID['user_mails']]]?></a>
                <?php endif; ?>
            </li>
        <?php endif; ?>
        <?php if ($LINK['user_mail_signatures'] || $LINK['setup_mail_signatures']): ?>
            <li class="nav-item">
                <a class="nav-link active bg-contrast" href="#"><?=$lang[$LINK["desc"][$LINK_ID['setup_mail_signatures']]]?></a>
            </li>
        <?php endif; ?>
    </ul>

    <table class="table table-sm table-striped table-hover border border-top-0 bg-contrast align-middle">
        <thead>
        <tr>
            <th></th>
            <th>ID</th>
            <th><?=$lang[924]?></th>
            <?php if($adminSignatures): ?>
                <th><?=$lang[1242]?></th>
                <?php if($multiTenantEnabled): ?>
                <th><?=$lang[2962]?></th>
                <?php endif; ?>
            <?php endif; ?>
            <th><?=$lang[1205]?></th>
            <th></th>
        </tr>
        </thead>

        <tbody id="table-mail-signatures">
        <?php
        /** @var MailSignature $mailSignature */
        foreach($mailSignatures as $mailSignature) :
            include(__DIR__ . '/signature-row.php');
        endforeach; ?>
        </tbody>

    </table>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-mail-signature" data-id="0"><i class="fas fa-plus"></i></button>

</div>


<div class="modal fade" id="modal-mail-signature" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                    <label for="mail-content" class="col-sm-3 col-form-label"><?=$lang[521]?></label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control" id="mail-content"></textarea>
                        <?= lmbInitTinyMce('mail-content',400); ?>
                    </div>
                </div>
                
                <?php if($adminSignatures): ?>
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
                <?php if($adminSignatures):?>
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

            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-primary" id="btn-save-mail-signature"><?=$lang[842]?></button>
            </div>
        </div>
    </div>
</div>



