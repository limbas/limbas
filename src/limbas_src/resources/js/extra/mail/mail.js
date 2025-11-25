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
    loadDataFromSessionStorage();
    $('#btn-show-mail-preview').on('click', loadMailPreview);
    $('#mail-signature-select').on('change', onSignatureChange);
    $('#mail_account').on('change', onMailAccountChanged);
    setTimeout(()=>{
        onMailAccountChanged();
    },1000);
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
    
    resetSessionStorage();
    
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
        $('#mail_signature').val(),
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

function lmbSendMail(mailAccountId, receivers, subject, message, gtabid = 0, id = 0, fileInput = '', attachments = [], cc = null, bcc = null, templateId = null,resolvedTemplateGroups='',resolvedDynamicData='', signature = '') {
    return new Promise((resolve, reject) => {

        let formData = new FormData();
        formData.append('account', mailAccountId);
        formData.append('subject', subject);
        formData.append('message', message);
        formData.append('gtabid', gtabid);
        formData.append('templateId', templateId);
        formData.append('resolvedTemplateGroups', resolvedTemplateGroups);
        formData.append('resolvedDynamicData', resolvedDynamicData);
        formData.append('signature', signature);

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
            '<a href="main.php?action=download&amp;ID=' + file.id + '" target="_blank">' + file.name + '</a>' +
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
    
    initRecipientSelect($('select#mail_receiver'));
    initRecipientDataReset();
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
    }).on('change', saveDataToSessionStorage);
}

function initRecipientDataReset() {
    const generalMainModal = window.parent.document.getElementById('general-main-modal');
    $(generalMainModal).find('[data-bs-dismiss]').on('click', resetSessionStorage)
}

function getFormKey() {
    const $mailForm = $('#mailForm');
    const data = $mailForm.attr('data-id') + '-' + $mailForm.data('gtabid');
    let hash = 2166136261n;
    for (let i = 0; i < data.length; i++) {
        hash ^= BigInt(data.charCodeAt(i));
        hash *= 16777619n;
    }
    return hash.toString(16);
}

function saveDataToSessionStorage() {
    const formKey = getFormKey();
    const isBulk = parseInt($('#mailForm').data('bulk')) === 1;
    const fields = ['bcc', 'cc'];
    if(!isBulk) {
        fields.push('receiver');
    }

    fields.forEach(field => {
        const value = $('#mail_' + field).val();
        sessionStorage.setItem('mail-' + formKey + field[0], JSON.stringify(value));
    });
}

function loadDataFromSessionStorage() {
    const formKey = getFormKey();
    const isBulk = parseInt($('#mailForm').data('bulk')) === 1;
    const fields = ['bcc', 'cc'];
    if(!isBulk) {
        fields.push('receiver');
    }

    fields.forEach(field => {
        const data = sessionStorage.getItem('mail-' + formKey + field[0]);
        const receivers = data ? JSON.parse(data) : [];
        if (data !== null && receivers.length > 0) {
            let $field = $('#mail_' + field);
            setSelectValue($field, receivers)
            if(field !== 'receiver') {
                $('[data-add-mail-recipient="' + field + '"]').trigger('click');
            }
        }
    });
}

function resetSessionStorage() {
    const formKey = getFormKey();
    const fields = ['receiver', 'bcc', 'cc'];

    fields.forEach(field => {
        sessionStorage.removeItem('mail-' + formKey + field[0]);
    });
}

function setSelectValue($select, values) {
    $select.val(null);
    values.forEach(value => {
        if ($select.find("option[value='" + value + "']").length) {
            $select.val(value).trigger('change.select2');
        } else {
            const newOption = new Option(value, value, true, true);
            $select.append(newOption).trigger('change.select2');
        }
    });
}

/* endregion recipient handling */

/* region signature handling */

function onMailAccountChanged() {
    $('#mail-signature-select').val(0).trigger('change');
}

function onSignatureChange() {
    const $signatureSelect = $('#mail-signature-select');
    let signatureId = parseInt($signatureSelect.val());
    const useDefault = isNaN(signatureId) || signatureId === 0;
    
    let signatureContent;
    if(useDefault) {
        const $mailAccounts = $('#mail_account');
        if($mailAccounts.is('input')) {
            signatureId = parseInt($mailAccounts.data('signature'));
            signatureContent = $mailAccounts.data('signature-content');
        }
        else {
            const $mailAccount = $mailAccounts.find(':selected');
            signatureId = parseInt($mailAccount.data('signature'));
            signatureContent = $mailAccount.data('signature-content');
        }
    }
    else {
        signatureId = parseInt($signatureSelect.val());
        signatureContent = $signatureSelect.find(':selected').data('signature-content');
    }

    if(useDefault && (isNaN(signatureId) || signatureId === 0)) {
        signatureId = 0;
        signatureContent = $signatureSelect.data('default-content');
    }
    loadSignature(signatureId, signatureContent);
}

function loadSignature(id, content) {
    $('#mail_signature').val(id);
    tinymce.get('mail_signature_content').setContent(content ?? '');
}


/* endregion signature handling */

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
