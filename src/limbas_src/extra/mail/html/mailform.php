<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

?>

<script src="main.php?action=syntaxcheckjs"></script>
<script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/admin/templates/editor.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/mail/mail.js?v=<?=$umgvar['version']?>"></script>

<div id="mailForm" class="container-fluid h-100">
    
    <div id="mail_sending" class="text-center py-4 d-none">
        <p class="mb-2"><i class="fas fa-spinner fa-spin fa-3x"></i></p>
        <?=$lang[3130]?>...
    </div>
    
    <form id="form1">
        
            <input type="hidden" name="action" value="saveTemplate">
            <input type="hidden" name="actid" value="manageTemplates">
            <input type="hidden" name="gtabid" value="<?=$gtabid?>">
            <input type="hidden" name="ID" id="formid" value="<?=$id?>">

            <input type="hidden" id="mail_gtabid" value="<?=$gtabid?>">
            <input type="hidden" id="mail_id" value="<?=$id?>">

            <input type="hidden" name="type" value="">
            <input type="hidden" name="element_id" value="">


            <input type="hidden" name="content" id="content">

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
                        <?php if($receiverCount > 1): ?>
                            <select name="mail_receiver" id="mail_receiver" class="form-select">
                                <?php foreach($receivers as $receiver): ?>
                                    <option value="<?=$receiver?>"><?=$receiver?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control" value="<?=!empty($receivers) ? $receivers[0] : ''?>" name="mail_receiver" id="mail_receiver">
                        <?php endif; ?>
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
                        <input type="file" class="form-control" id="mail_attachments" name="mail_attachments" value="<?=$subject?>" multiple>
                        <?php if(!empty($attachments)): ?>
                        <div class="d-flex gap-2 mt-3 text-muted">
                            <?php foreach($attachments as $attachment): ?>
                            <div class="text-center border p-2 rounded-2">
                                <i class="fas fa-file"></i><br>
                                <?=e($attachment)?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="mail_message" class="form-label d-none"><?=$lang[2660]?></label>
                    <textarea class="form-control" id="mail_message" rows="20">
                    <?=e($templateHtml)?>
                </textarea>
                    <?= lmb_ini_wysiwyg('mail_message',null,null,1,650, intval($gtabid)); ?>
                </div>
            </div>
        </div>
        
        
    </form>

</div>
