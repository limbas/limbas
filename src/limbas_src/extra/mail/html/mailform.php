<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

?>

<script src="assets/vendor/select2/select2.full.min.js"></script>
<link rel="stylesheet" href="assets/vendor/select2/select2.min.css">
<script src="main.php?action=syntaxcheckjs"></script>
<script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/admin/templates/editor.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/explorer/explorer.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/mail/mail.js?v=<?=$umgvar['version']?>"></script>

<form id="form1">
    <?php /* only necessary because lmbTemplate tinyMCE plugin requires it */ ?>
    <input type="hidden" name="gtabid" value="<?=$gtabid?>">
    <input type="hidden" name="ID" id="formid" value="<?=e($firstId)?>">
    
</form>
<div class="container-fluid h-100">
    <input type="hidden" id="lmbTemplateForceDataTable" value="0">
    
    <div id="mail_sending" class="text-center py-4 d-none">
        <p class="mb-2"><i class="fas fa-spinner fa-spin fa-3x"></i></p>
        <?=$lang[3130]?>...
    </div>
    
    <form id="mailForm" data-id="<?=e($id)?>" data-gtabid="<?=$gtabid?>">
        <input type="hidden" value="<?=e($templateId)?>" id="mail_template">
        <input type="hidden" value="<?=e(json_encode($resolvedTemplateGroups))?>" id="mail_resolvedTemplateGroups">
        <input type="hidden" value="<?=e(json_encode($resolvedDynamicData))?>" id="mail_resolvedDynamicData">
        
        <div class="mb-3">
            <button type="button" class="btn btn-primary btn-send-mail"><i class="fa fa-paper-plane"></i> <?=$lang[3131]?></button>
        </div>
        
        <div class="row">
            <div class="col-12">


                <div class="mb-3 row">
                    <label for="mail_account" class="col-sm-1 col-form-label"><?=$lang[2414]?>:</label>
                    <div class="col-sm-11">
                        <?php if($senderAccountCount > 1): ?>
                            <select name="mail_account" id="mail_account" class="form-select">
                                <?php foreach($senderAccounts as $senderAccount): ?>
                                    <option value="<?=$senderAccount->id?>"><?=$senderAccount->name ? $senderAccount->name . ' - ' : ''?><?=$senderAccount->email?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <div class="form-control-plaintext"><?=$senderAccountCount > 0 ? ($senderAccounts[0]->name ? $senderAccounts[0]->name . ' - ' : '') . $senderAccounts[0]->email : 'No sender configured.'?></div>
                            <input type="hidden" id="mail_account" value="<?=$senderAccountCount > 0 ? $senderAccounts[0]->id : 0?>" name="mail_account">
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="mb-3 row">
                    <label for="mail_receiver" class="col-sm-1 col-form-label"><?=$lang[2050]?>:</label>
                    <div class="col-sm-11">
                        <div class="d-flex gap-1 justify-content-between align-items-start">
                            <?php if($bulkMail): ?>
                                <div>
                                    <div id="mail-receivers-list" class="flex-grow-1 border p-2 pe-3 overflow-hidden position-relative mb-2" style="height: 2rem">
                                        <?= count($receivers) ?>:
                                        <span><?=implode('; </span><span>',$receivers)?></span>
                                        <div class="position-absolute end-0 top-0 p-2">
                                            <i class="fas fa-chevron-down cursor-pointer" id="mail-expand-receivers"></i>
                                        </div>
                                    </div>
                                    <div class="text-primary">Die E-Mail wird einzeln pro Empfänger gesendet. Die Empfänger sehen sich gegenseitig nicht. Untenstehend kann eine Vorschau der finalen E-Mails erzeugt werden.</div>
                                    <input type="hidden" name="mail_receiver" id="mail_receiver" value="">
                                </div>
                            <?php else: ?>
                                <select name="mail_receiver" id="mail_receiver" class="form-select" multiple>
                                    <?php foreach($receivers as $receiver): ?>
                                        <option value="<?=$receiver?>" selected><?=$receiver?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-secondary text-nowrap" type="button" data-add-mail-recipient="cc">Kopie (CC)</button>
                            <button class="btn btn-outline-secondary text-nowrap" type="button" data-add-mail-recipient="bcc">Blindkopie (BCC)</button>
                        </div>
                    </div>
                </div>
                
                
                
                <div class="mb-3 row d-none" id="mail-cc-wrapper">
                    <label for="mail_cc" class="col-sm-1 col-form-label">CC:</label>
                    <div class="col-sm-11">
                        <select name="mail_cc" id="mail_cc" class="form-select" multiple>
                            
                        </select>
                    </div>
                </div>
                <div class="mb-3 row d-none" id="mail-bcc-wrapper">
                    <label for="mail_bcc" class="col-sm-1 col-form-label">BCC:</label>
                    <div class="col-sm-11">
                        <select name="mail_bcc" id="mail_bcc" class="form-select" multiple>

                        </select>
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="mail_subject" class="col-sm-1 col-form-label"><?=$lang[1447]?></label>
                    <div class="col-sm-11">
                        <input type="text" class="form-control" id="mail_subject" name="mail_subject" value="<?=$subject?>">
                    </div>
                </div>

                <div class="mb-3 row">
                    <label for="mail_attachments" class="col-sm-1 col-form-label">Anhänge:</label>
                    <div class="col-sm-11">
                        <div class="d-flex align-items-center gap-3">
                            <button type="button" class="btn btn-outline-primary text-nowrap" id="btn-open-dms">Aus DMS auswählen</button>
                            <input type="file" class="form-control" id="mail_attachments" name="mail_attachments" multiple>
                        </div>                        
                        <div class="d-flex gap-2 mt-3 text-muted" id="attachments">
                            <?php if(!empty($attachments)): ?>
                                <?php foreach($attachments as $attachment): ?>
                                <div class="border px-2 py-1 rounded-2">
                                    <i class="fas fa-file"></i> <?=e($attachment)?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if($bulkMail): ?>
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-outline-primary" id="btn-show-mail-preview">Vorschau für alle laden</button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-12 mb-3" id="mail-editor">
                        <?php if($readonly): ?>
                            <div class="form-control" id="mail_message">
                            <?=$templateHtml?>
                            </div>
                        <?php endif; ?>
                        <div class="<?= $readonly ? 'd-none' : '' ?>">
                            <label for="mail_message" class="form-label d-none"><?=$lang[2660]?></label>
                            <textarea class="form-control" id="mail_message" rows="20" readonly>
                    <?=e($templateHtml)?>
                </textarea>
                            <?= lmbInitTinyMce('mail_message',650, intval($gtabid)); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border p-3 bg-contrast d-none" id="mail-preview">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin fa-4x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                
            </div>
        </div>
        
        
    </form>


    <?php require(COREPATH . 'extra/explorer/mini_explorer_modal.php'); ?>
    
</div>
