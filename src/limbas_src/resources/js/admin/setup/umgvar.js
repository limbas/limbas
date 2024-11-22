/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('[data-delete]').on('click', function () {
        confirmDelete($(this), deleteUmgVar)
    });
    $('#btn-save-umgvar').on('click', saveUmgVar);
    $('[data-update]').on('change', updateUmgVar);
});


function updateUmgVar() {

    const $this = $(this);

    sendUmgVarAction({
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

function saveUmgVar() {

    let name = $('#new-umgvar-name').val();
    let description = $('#new-umgvar-description').val();
    let value = $('#new-umgvar-value').val();
    let category = $('#new-umgvar-category').val();
    let newCategory = $('#new-umgvar-new-category').val();

    sendUmgVarAction({
        action: 'create',
        name: name,
        description: description,
        value: value,
        category: category,
        newCategory: newCategory
    }).then(function (data) {
        if (data.success) {
            lmbShowSuccessMsg('Create successful.');

            let $html = $(data.html);
            let category = data.category;

            $('[data-delete]').on('click', function () {
                confirmDelete($(this), deleteUmgVar)
            });
            $html.find('[data-update]').on('change', updateUmgVar);

            let $table = $('#table-' + category);

            if ($table.length <= 0) {
                $table = $('<tbody id="table-' + category + '"><tr class="table-section"><td colspan="5">' + data.categoryName + '</td></tr></tbody>');
                $('#table-umgvar').append($table);
            }

            $('.table-success').removeClass('table-success');

            $table.append($html);

            $('html, body').animate({
                scrollTop: $html.offset().top - 50
            }, 20);

        } else {
            lmbShowErrorMsg('Create failed: ' + data.msg);
        }
    }).catch(function () {
        lmbShowErrorMsg('Create failed.');
    });

}

function deleteUmgVar($this) {
    const id = $this.data('delete');
    sendUmgVarAction({
        action: 'delete',
        id: id
    }).then(function (data) {
        if (data.success) {
            $('#umgvar-' + id).remove();
            lmbShowSuccessMsg('Delete successful.');
        } else {
            lmbShowErrorMsg('Delete failed.');
        }
    })

}

function sendUmgVarAction(data) {
    data['actid'] = 'manageUmgVars';

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


