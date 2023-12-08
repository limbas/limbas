/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('#btn-save-mail-template').click(saveMailTemplate);
    $('[data-delete]').click(deleteMailTemplate);
    $('#modal-mail-template').on('show.bs.modal', showMailTemplateModal);
});

function showMailTemplateModal(e) {
    $('#mail-name').val('');
    $('#template-desc').val('');
}

function saveMailTemplate() {

    sendMailAction({
        action: 'save',
        id: 0,
        name: $('#template-name').val(),
        description: $('#template-desc').val(),
        tabId: $('#template-gtabid').val(),
        templateTabId: $('#template-table').val()
    }).then(function(data) {
        if (data.success) {
            $('#modal-mail-template').modal('hide');
            let $html = $(data.html);
            $html.find('[data-delete]').click(deleteMailTemplate);
            $('#table-mail-templates').append($html);
        }
    });

}

function deleteMailTemplate() {
    let id = $(this).data('delete');
    sendMailAction({
        action: 'delete',
        id: id
    }).then(function(data) {
        if (data.success) {
            $('#template-' + id).remove();
        }
    });

}

function sendMailAction(data) {    
    data['actid'] = 'manageMailTemplates';

    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'main_dyns_admin.php',
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
