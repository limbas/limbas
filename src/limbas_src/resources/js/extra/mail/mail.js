/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('#mailForm').find('.btn-send-mail').click(lmbSendMailForm);
    initAttachmentModal();
    initRecipients();
    $('#btn-show-mail-preview').on('click', loadMailPreview);
});

/* region send mail */
function lmbSendMailForm() {
    let $mailSending = $('#mail_sending');
    let $mailForm = $('#mailForm');
    $mailSending.removeClass('d-none');
    $mailForm.addClass('d-none');

    let attachments = [];
    $('.attachment').each(function() {
        attachments.push($(this).data('id'));
    });
    
    lmbSendMail(
        $('#mail_account').val(),
        $('#mail_receiver').val(),
        $('#mail_subject').val(),
        tinymce.get('mail_message').getContent(),
        $mailForm.data('gtabid'),
        $mailForm.data('id'),
        'mail_attachments',
        attachments,
        $('#mail_cc').val(),
        $('#mail_bcc').val(),
        $('#mail_template').val(),
        $('#mail_resolvedTemplateGroups').val(),
        $('#mail_resolvedDynamicData').val(),
    ).then(function (data) {
        if(data.success) {
            $('#mail_receiver').val('');
            $('#mail_subject').val('');
            $('#mail_message').val('');
            hideFullPageModal();
            lmbShowSuccessMsg('Mail successfully sent.');
        } else {
            lmbShowErrorMsg('Mail could not be sent.');
        }
    }).catch(function (){
        lmbShowErrorMsg('Mail could not be sent.');
    }).finally(function () {
        $mailSending.addClass('d-none');
        $mailForm.removeClass('d-none');
    });
}

function lmbSendMail(mailAccountId, receivers, subject, message, gtabid = 0, id = 0, fileInput = '', attachments = [], cc = null, bcc = null, templateId = null,resolvedTemplateGroups='',resolvedDynamicData='') {
    return new Promise((resolve, reject) => {

        let formData = new FormData();
        formData.append('account', mailAccountId);
        formData.append('subject', subject);
        formData.append('message', message);
        formData.append('gtabid', gtabid);
        formData.append('templateId', templateId);
        formData.append('resolvedTemplateGroups', resolvedTemplateGroups);
        formData.append('resolvedDynamicData', resolvedDynamicData);

        if(Array.isArray(id)) {
            for (let i = 0; i < id.length; i++) {
                formData.append('id[]', id[i]);
            }
        }
        else {
            formData.append('id', id);
        }
        if(Array.isArray(receivers)) {
            for (let i = 0; i < receivers.length; i++) {
                formData.append('receivers[]', receivers[i]);
            }
        }
        else {
            formData.append('receivers[]', receivers);
        }
        if(Array.isArray(cc)) {
            for (let i = 0; i < cc.length; i++) {
                formData.append('cc[]', cc[i]);
            }
        }
        else {
            formData.append('cc[]', cc);
        }
        if(Array.isArray(bcc)) {
            for (let i = 0; i < bcc.length; i++) {
                formData.append('bcc[]', bcc[i]);
            }
        }
        else {
            formData.append('bcc[]', bcc);
        }
        

        $.each(attachments, function(i, dmsId) {
            formData.append('attachments[]', dmsId);
        });

        if(fileInput !== '') {
            let $fileInput = $('#' + fileInput);
            if($fileInput.length >= 1) {
                $.each($fileInput[0].files, function(i, file) {
                    formData.append('attachments[]', file);
                });
            }
        }

        $.ajax({
            type: 'POST',
            contentType: false,
            processData: false,
            url: 'main_dyns.php?actid=handleMail&action=send',
            data: formData,
            dataType: 'json',
            success: function (data) {
                resolve(data)
            },
            error: function (error) {
                reject(error)
            },
        })
    })
}

/* endregion send mail */


/* region dms attachments */

function initAttachmentModal() {
    $('#btn-open-dms').on('click', openDmsAttachments);
}

function openDmsAttachments() {
    LmEx_open_miniexplorer('',[], selectAttachments);
}

function selectAttachments(files,params) {
    const $attachments = $('#attachments');

    $.each(files, function(i, file) {
        let $html = $('<div class="border px-2 py-1 rounded-2 attachment" data-id="' + file.id + '">' +
            '<i class="fas fa-file"></i> ' +
            file.name +
            ' <i class="fas fa-times ms-2 link-danger cursor-pointer"></i>' +
            '</div>');

        $html.find('.fa-times').on('click', function () {
            $(this).closest('.attachment').remove();
        });

        $attachments.append($html);
    });

    $('#miniexplorer-modal').modal('hide');
}

/* endregion dms attachments */

/* region recipient handling */

function initRecipients() {
    
    $('[data-add-mail-recipient]').on('click', function () {
        const context = $(this).data('add-mail-recipient');
        initRecipientSelect($('#mail-' + context + '-wrapper').removeClass('d-none').find('select'));
        $(this).remove();
    });
    
    $('#mail-expand-receivers').on('click', expandReceivers)
    
    initRecipientSelect($('select#mail_receiver'));
}

function initRecipientSelect($element) {
    $element.select2({
        selectOnClose: true,
        tags: true,
        tokenSeparators: [';', ' '],
        createTag: function (params) {
            if (!(/^[^@]+@[^@]+\.[^@]+$/.test(params.term))) {
                return null;
            }

            return {
                id: params.term,
                text: params.term
            }
        }
    });
}

function expandReceivers() {
    const $this = $(this);
    const $list = $('#mail-receivers-list');
    let expanded = $this.data('expanded');
    if(expanded === true) {
        expanded = false;
        $list.css('height', '2rem');
        $this.removeClass('fa-chevron-up').addClass('fa-chevron-down');
    }
    else {
        expanded = true;
        $list.css('height', 'auto');
        $this.removeClass('fa-chevron-down').addClass('fa-chevron-up');
    }
    $this.data('expanded', expanded);
}

/* endregion recipient handling */



function loadMailPreview() {
    let $mailEditor = $('#mail-editor');
    let $mailForm = $('#mailForm');
    let $mailPreview = $('#mail-preview');

    $mailEditor.addClass('col-md-6').removeClass('col-md-12');
    $mailPreview.removeClass('d-none');

    
    let id = $mailForm.data('id');
    const tabId = $mailForm.data('gtabid');


    let formData = new FormData();
    formData.append('message', tinymce.get('mail_message').getContent());
    formData.append('tabId', tabId);
    formData.append('templateId', $('#mail_template').val());
    formData.append('resolvedTemplateGroups', $('#mail_resolvedTemplateGroups').val());
    formData.append('resolvedDynamicData', $('#mail_resolvedDynamicData').val());

    if(Array.isArray(id)) {
        for (let i = 0; i < id.length; i++) {
            formData.append('ids[]', id[i]);
        }
    }
    else {
        formData.append('ids[]', id);
    }

    $.ajax({
        type: 'POST',
        contentType: false,
        processData: false,
        url: 'main_dyns.php?actid=handleMail&action=preview',
        data: formData,
        dataType: 'json',
        success: function (data) {
            
            console.log(data);
            
            if(data.success) {
                $mailPreview.html(data.html);
            }
            else {
                lmbShowErrorMsg('Preview could not be loaded.');
            }
            
        },
        error: function (error) {
            lmbShowErrorMsg('Preview could not be loaded.');
        },
    })
}
