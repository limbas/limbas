/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


$(function () {
    $('[data-delete]').on('click', function() {confirmDelete($(this), deleteReport)});
    $('#btn-save-report').on('click', saveReport);
    $('[data-update]').on('change', updateReport);

    $('#modal-report-settings').on('show.bs.modal', loadReportSettings);
    $('#btn-add-report-template').on('click', addTemplate);
});


function updateReport() {

    const $this = $(this);
    const isCheckBox = $this.is(':checkbox');

    sendReportAction({
        action: 'update',
        id: $this.data('id'),
        field: $this.data('update'),
        value: isCheckBox ? ($this.prop('checked') ? 1 : 0) : $this.val()
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

function saveReport() {

    const $newReportTable = $('#new-report-table');
    const tabId = $newReportTable.val();
    sendReportAction({
        action: 'save',
        id: 0,
        name: $('#new-report-name').val(),
        tabId: tabId,
        format: $('#new-report-format').val(),
        copy: $('#new-report-copy').val()
    }).then(function (data) {
        if (data.success) {
            let $html = $(data.html);
            $html.find('[data-delete]').on('click', deleteReport);
            $html.find('[data-update]').on('change', updateReport);

            let $table = $('#table-' + tabId);

            if ($table.length <= 0) {
                const tableName = $newReportTable.find('option:selected').text();
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

function deleteReport($element) {
    let id = $element.data('delete');
    sendReportAction({
        action: 'delete',
        id: id
    }).then(function (data) {
        if (data.success) {
            $('#report-' + id).remove();
            lmbShowSuccessMsg('Delete successful.');
        } else {
            lmbShowErrorMsg('Delete failed.');
        }
    })

}

function sendReportAction(data) {
    data['actid'] = 'manageReports';

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


function loadReportSettings(event) {
    const $btn = $(event.relatedTarget);
    const $settingsHolder = $('#report-settings-holder');
    const $loader = $('#report-settings-loader');
    $loader.removeClass('d-none');
    $settingsHolder.addClass('d-none');

    const reportId = $btn.data('id');

    sendReportAction({
        action: 'getTemplates',
        id: reportId
    }).then(function (data) {
        if (data.success) {
            let $html = $(data.html);
            $html.find('[data-delete]').on('click', deleteReport);
            $html.find('[data-update]').on('change', updateReport);
            $('#table-report-templates').html($html);
            $('#btn-add-report-template').data('id', reportId)
            $settingsHolder.removeClass('d-none');
        }
    }).finally(function () {
        $loader.addClass('d-none');
    });
}

function addTemplate() {

    const $this = $(this);
    const reportId = $this.data('id');

    $this.removeClass('fa-plus').addClass('fa-spinner fa-spin');
    $this.prop('disabled', true);

    sendReportAction({
        action: 'addTemplate',
        id: reportId
    }).then(function (data) {
        if (data.success) {
            let $html = $(data.html);
            $html.find('[data-delete]').on('click', deleteReport);
            $html.find('[data-update]').on('change', updateReport);
            $('#table-report-templates').append($html);
        }
    }).finally(function () {
        $this.addClass('fa-plus').removeClass('fa-spinner fa-spin');
        $this.prop('disabled', false);
    });
}
