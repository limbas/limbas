/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('#btn-save-mail-account').click(saveMailAccount);
    $('#mail-type').change(switchNewTransport);
    $('[data-delete]').click(deleteMailAccount);
    $('#modal-mail-account').on('show.bs.modal', showMailModal);
});

function showMailModal(e) {

    let $button = $(e.relatedTarget);
    let id = parseInt($button.data('id'));
    $('#btn-save-mail-account').data('id', id);
    
    
    if (id === 0) {
        $('#mail-name').val('');
        $('#mail-email').val('');
        $('#mail-tenant').val(0).change();
        $('#mail-user').val(0).change();
        $('#mail-type').val(1).change();
        $('#mail-imap-host').val('');
        $('#mail-imap-port').val('');
        $('#mail-imap-user').val('');
        $('#mail-imap-password').val('');
        $('#mail-imap-path').val('');
        $('#mail-smtp-host').val('');
        $('#mail-smtp-port').val('');
        $('#mail-smtp-user').val('');
        $('#mail-smtp-password').val('');
        $('#mail-status').val(1).change();
        $('#mail-default').prop('checked',false);
        $('#mail-hidden').prop('checked',false);
        $('#mail-table').prop('selectedIndex',0);
    } else {
        let $mailAccount = $('#account-' + id);
        $('#mail-name').val($mailAccount.data('mail-name'));
        $('#mail-email').val($mailAccount.data('mail-email'));
        $('#mail-tenant').val($mailAccount.data('mail-tenant-id')).change();
        $('#mail-user').val($mailAccount.data('mail-user-id')).change();
        $('#mail-type').val($mailAccount.data('mail-type')).change();
        $('#mail-imap-host').val($mailAccount.data('mail-imap-host'));
        $('#mail-imap-port').val($mailAccount.data('mail-imap-port'));
        $('#mail-imap-user').val($mailAccount.data('mail-imap-user'));
        $('#mail-imap-password').val($mailAccount.data('mail-imap-password'));
        $('#mail-imap-path').val($mailAccount.data('mail-imap-path'));
        $('#mail-smtp-host').val($mailAccount.data('mail-smtp-host'));
        $('#mail-smtp-port').val($mailAccount.data('mail-smtp-port'));
        $('#mail-smtp-user').val($mailAccount.data('mail-smtp-user'));
        $('#mail-smtp-password').val($mailAccount.data('mail-smtp-password'));
        $('#mail-status').val($mailAccount.data('mail-status')).change();
        $('#mail-default').prop('checked',$mailAccount.data('mail-default'));
        $('#mail-hidden').prop('checked',$mailAccount.data('mail-hidden'));
        $('#mail-table').val($mailAccount.data('mail-table')).change();
    }
    
}

function saveMailAccount() {

    let id = $(this).data('id');

    sendMailAction({
        action: 'save',
        id: id,
        name: $('#mail-name').val(),
        email: $('#mail-email').val(),
        user: $('#mail-user').val(),
        tenant: $('#mail-tenant').val(),
        type: $('#mail-type').val(),
        imap_host: $('#mail-imap-host').val(),
        imap_port: $('#mail-imap-port').val(),
        imap_user: $('#mail-imap-user').val(),
        imap_password: $('#mail-imap-password').val(),
        imap_path: $('#mail-imap-path').val(),
        smtp_host: $('#mail-smtp-host').val(),
        smtp_port: $('#mail-smtp-port').val(),
        smtp_user: $('#mail-smtp-user').val(),
        smtp_password: $('#mail-smtp-password').val(),
        status: $('#mail-status').val(),
        default: $('#mail-default').prop('checked') ? 1 : 0,
        hidden: $('#mail-hidden').prop('checked') ? 1 : 0,
        mail_table: $('#mail-table').val()
    }).then(function(data) {
        $('#modal-add-mail-account').modal('hide');
        if (data.success) {
            $('#modal-mail-account').modal('hide');
            if(id > 0) {
                $('#account-' + id).replaceWith(data.html);
            } else {
                let $html = $(data.html);
                $html.find('[data-delete]').click(deleteMailAccount);
                $('#table-mail-accounts').append($html);
            }
        }
    });

}

function deleteMailAccount() {
    let id = $(this).data('delete');    
    sendMailAction({
        action: 'delete',
        id: id
    }).then(function(data) {
        if (data.success) {
            $('#account-' + id).remove();
        }
    });

}

function sendMailAction(data) {
    let isAdmin = !!parseInt($('#is-admin').val());
    let url = isAdmin ? 'main_dyns_admin.php' : 'main_dyns.php';
    
    data['actid'] = isAdmin ? 'manageMailAccounts' : 'handleMail';
    
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

function switchNewTransport() {
    let value = parseInt($(this).val());
    if (value === 1) {
        $('#mail-mail-transport-smtp').show();
    } else {
        $('#mail-mail-transport-smtp').hide();
    }
}
