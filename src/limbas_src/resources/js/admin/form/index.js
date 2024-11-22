/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


$(function () {
    $('[data-delete]').on('click', function() {confirmDelete($(this), deleteForm)});
    $('#btn-save-form').on('click', saveForm);
    $('[data-update]').on('change', updateForm);
});


function updateForm() {

    const $this = $(this);

    sendFormAction({
        action: 'update',
        id: $this.data('id'),
        field: $this.data('update'),
        value: $this.val()
    }).then(function (data) {
        if (data.success) {
            lmbShowSuccessMsg('Update successful.')
        } else {
            lmbShowErrorMsg('Update failed.');
        }
    }).catch(function () {
        lmbShowErrorMsg('Update failed.');
    });

}

function saveForm() {

    const $newFormTable = $('#new-form-table');
    const tabId = $newFormTable.val();
    sendFormAction({
        action: 'save',
        id: 0,
        name: $('#new-form-name').val(),
        tabId: tabId,
        type: $('#new-form-type').val(),
        copy: $('#new-form-copy').val(),
        extension: $('#new-form-extension').val(),
    }).then(function (data) {
        if (data.success) {
            let $html = $(data.html);
            $html.find('[data-delete]').on('click', deleteForm);
            $html.find('[data-update]').on('change', updateForm);

            let $table = $('#table-' + tabId);

            if ($table.length <= 0) {
                const tableName = $newFormTable.find('option:selected').text();
                $table = $('<tbody id="table-' + tabId + '"><tr class="table-section"><td colspan="12">' + tableName + '</td></tr></tbody>');
                $('#table-reports').append($table);
            }

            $('.table-success').removeClass('table-success');

            $table.append($html);

            $('html, body').animate({
                scrollTop: $html.offset().top - 50
            }, 20);
        } else {
            lmbShowErrorMsg('Create failed.');
        }
    }).catch(function () {
        lmbShowErrorMsg('Create failed.');
    })

}

function deleteForm($element) {
    let id = $element.data('delete');
    sendFormAction({
        action: 'delete',
        id: id
    }).then(function (data) {
        if (data.success) {
            $('#form-' + id).remove();
            lmbShowSuccessMsg('Delete successful.');
        } else {
            lmbShowErrorMsg('Delete failed.');
        }
    })

}

function sendFormAction(data) {
    data['actid'] = 'manageForms';

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
