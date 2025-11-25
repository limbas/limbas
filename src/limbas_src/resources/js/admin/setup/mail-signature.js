/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

var jsvar = {
    gtabid: 0,
    ID: 0
}

$(function () {
    $('#btn-save-mail-signature').click(saveMailSignature);
    $('[data-delete]').click(deleteMailSignature);
    $('#modal-mail-signature').on('show.bs.modal', showMailModal);
});

function showMailModal(e) {

    let $button = $(e.relatedTarget);
    let id = parseInt($button.data('id'));
    $('#btn-save-mail-signature').data('id', id);
    
    
    if (id === 0) {
        $('#mail-name').val('');
        //$('#mail-content').val('');
        tinymce.activeEditor.setContent('');
        $('#mail-tenant').val(0).change();
        $('#mail-user').val(0).change();
        $('#mail-status').val(1).change();
        $('#mail-default').prop('checked',false);
        $('#mail-hidden').prop('checked',false);
    } else {
        let $mailSignature = $('#signature-' + id);
        $('#mail-name').val($mailSignature.data('mail-name'));
        //$('#mail-content').val($mailSignature.data('mail-content'));
        tinymce.activeEditor.setContent($mailSignature.data('mail-content'));
        $('#mail-tenant').val($mailSignature.data('mail-tenant-id')).change();
        $('#mail-user').val($mailSignature.data('mail-user-id')).change();
        $('#mail-status').val($mailSignature.data('mail-status')).change();
        $('#mail-hidden').prop('checked',$mailSignature.data('mail-hidden'));
        
        const $mailDefault = $('#mail-default');
        $mailDefault.prop('checked',$mailSignature.data('mail-default'));
        
        const userId = parseInt($mailSignature.data('mail-user-id') ?? 0);
        if(userId > 0) {
            $mailDefault.closest('.row').hide();
        }
        else {
            $mailDefault.closest('.row').show();
        }
    }

    
    
}

function saveMailSignature() {

    const $mailUser = $('#mail-user');
    const $mailTenant = $('#mail-tenant');
    const $mailDefault = $('#mail-default');
    
    let id = $(this).data('id');
    let tenantId = $mailTenant.val();
    let userId = $mailUser.val();
    
    if($mailDefault.prop('checked')) {
        $('[data-default="' + tenantId + '"]').remove();
    }

    sendMailSignatureAction({
        action: 'save',
        id: id,
        name: $('#mail-name').val(),
        content: tinymce.activeEditor.getContent('mail-content'),
        user: userId,
        tenant: tenantId,
        status: $('#mail-status').val(),
        default: $mailDefault.prop('checked') ? 1 : 0,
        hidden: $('#mail-hidden').prop('checked') ? 1 : 0
    }).then(function(data) {
        $('#modal-add-mail-signature').modal('hide');
        if (data.success) {
            $('#modal-mail-signature').modal('hide');
            if(id > 0) {
                $('#signature-' + id).replaceWith(data.html);
            } else {
                let $html = $(data.html);
                $html.find('[data-delete]').click(deleteMailSignature);
                $('#table-mail-signatures').append($html);
            }
        }
    });

}

function deleteMailSignature() {
    let id = $(this).data('delete');    
    sendMailSignatureAction({
        action: 'delete',
        id: id
    }).then(function(data) {
        if (data.success) {
            $('#signature-' + id).remove();
        }
    });

}

function sendMailSignatureAction(data) {
    let isAdmin = !!parseInt($('#is-admin').val());
    let url = isAdmin ? 'main_dyns_admin.php' : 'main_dyns.php';
    
    data['actid'] = isAdmin ? 'manageMailSignatures' : 'handleMailSignatures';
    
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (data) {
                resolve(data)
            },
            error: function (error) {
                reject(error)
            }
        });
    })
}

