/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('[data-delete]').click(deleteJob);
    $('[data-run]').click(runJob);
    $('#modal-jobs').on('show.bs.modal', showJobModal);
});

function showJobModal(e) {
    const $button = $(e.relatedTarget);
    const url = $button.data('url');

    sendJobRequest('GET', url).then((response) => {
        $('#modal-jobs .modal-content').html(response.html);
        const $userInput = $('#job-user-input');
        if(response.selected_user) {
            const selectedUserOption = new Option(response.selected_user.name, response.selected_user.id, false, true);
            $userInput.append(selectedUserOption).trigger('change');
        }
        $userInput.select2({
            multiple: false,
            width: '100%',
            dropdownParent: $('.modal-content'),
            ajax: {
                delay: 250,
                dataType: 'json',
                url: 'main_dyns.php?actid=handleUserGroup&action=data',
                processResults: function (data) {
                    return {
                        results: data.data.map(({id, name}) => ({
                            id,
                            text: name
                        }))
                    }
                }
            }
        });
        $('.btn-save-job').click(storeOrUpdateJob);
        $('.file-popup').click(popup);
        popup_checked();
    });
}

function storeOrUpdateJob() {
    const url = $(this).data('url');
    const id = $(this).data('id');
    const method = id > 0 ? 'PUT' : 'POST';

    const jobtype = $('#job-type').val();

    if (!jobtype) {
        lmbShowErrorMsg('You need to select a job type!'); // todo make multilingual
        return;
    }

    const files = [];
    $('input[data-fileid]:checked').each(function () {
        files.push($(this).data('fileid'));
    });

    const fields = {};
    $('input[data-fieldid]:checked').each(function () {
        const $this = $(this);
        const tableId = $this.data('tableid');
        const fieldId = $this.data('fieldid');
        if (!fields[tableId]) {
            fields[tableId] = [];
        }
        fields[tableId].push(fieldId);
    });

    sendJobRequest(method, url, {
        jobtype: jobtype,
        cron_minutes: $('#job-minutes').val(),
        cron_hours: $('#job-hours').val(),
        cron_monthdays: $('#job-month-days').val(),
        cron_months: $('#job-months').val(),
        cron_weekdays: $('#job-week-days').val(),
        jobuser: $('#job-user-input').val(),

        active: $('#job-active').prop('checked') ? 1 : 0,

        include_subdirs: $('#include-subdirs').prop('checked') ? 1 : 0,
        files: files,
        fields: fields,

        template: $('#job-template').val(),
        description: $('#job-description').val(),

        backup_type: $('#job-backuptype').val(),
        backup_medium: $('#job-backupmedium').val(),
        backup_target: $('#job-backuptarget').val(),
        backup_alive: $('#job-backupalive').val(),
    }).then((response) => {
        if (response.success) {
            $('#modal-jobs').modal('hide');
            if (id > 0) {
                $('#job-' + id).replaceWith(response.html);
            } else {
                $('#table-jobs').append($(response.html));
            }
            $('[data-delete]').click(deleteJob);
            $('[data-run]').click(runJob);
            lmbShowSuccessMsg('Job successfully saved!');
        }
        else {
            lmbShowErrorMsg('Job could not be saved!');
        }
    }).catch(() => {
        lmbShowErrorMsg('Job could not be saved!');
    });

}

function deleteJob() {
    const $this = $(this);
    confirmDelete($this, () => {
        const url = $this.data('url');
        let id = $this.data('delete');
        sendJobRequest('DELETE', url).then((response) => {
            if (response.success) {
                $('#job-' + id).remove();
            }
        });
    })
}

function runJob() {
    const url = $(this).data('url');
    let id = $(this).data('run');
    sendJobRequest('GET', url).then((response) => {
        lmbShowSuccessMsg('Job running');
    });
}

function sendJobRequest(method, url, data = {}) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            type: method.toUpperCase(),
            data: data,
            success: (response) => resolve(response),
            error: (error) => reject(error)
        });
    });
}

const IMGSRC = {
    plus: "assets/images/legacy/outliner/plusonly.gif",
    minus: "assets/images/legacy/outliner/minusonly.gif"
};

function popup() {
    const $clickedIcon = $(this);
    const $foldingList = $clickedIcon.closest('.file-row');
    const isExpanded = $clickedIcon.attr('src') === IMGSRC.minus;
    if (isExpanded) {
        $clickedIcon.attr('src', IMGSRC.plus);
        $foldingList.next().hide();
    } else {
        $clickedIcon.attr('src', IMGSRC.minus);
        $foldingList.next().show();
    }
}

function popup_checked() {
    $('input[data-fileid]:checked').each(function () {
        const $fileRow = $(this).closest('.file-row');

        //expand all superfolders that contain checked folder
        const $parents = $fileRow.parents('.folding-list');
        $parents.prev().find('img').attr('src', IMGSRC.minus);
        $parents.show();
    });
}
