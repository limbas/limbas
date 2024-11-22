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
        attachments
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

function lmbSendMail(mailAccountId, receiverMail, subject, message, gtabid = 0, id = 0, fileInput = '', attachments = []) {
    return new Promise((resolve, reject) => {

        let formData = new FormData();
        formData.append('account', mailAccountId);
        formData.append('receiver', receiverMail);
        formData.append('subject', subject);
        formData.append('message', message);
        formData.append('gtabid', gtabid);
        formData.append('id', id);

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
