/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function () {
    $('#btn-save-config').on('click',saveConfig);
    $('[data-delete]').on('click', function() {confirmDelete($(this), deleteConfig)});
    $('#modal-config').on('show.bs.modal', showConfigModal);
    $('#btn-add-config-row').on('click', () => addConfigRow('', '', false));
});

function showConfigModal(e) {
    let $button = $(e.relatedTarget);
    let id = parseInt($button.data('id'));
    $('#btn-save-config').data('id', id);


    if (id === 0) {
        $('#config-name').val('');
        $('#config-default').prop('checked',false);
        loadConfig({});
    } else {
        let $config = $('#config-' + id);
        $('#config-name').val($config.data('config-name'));
        $('#config-default').prop('checked',$config.data('config-default'));
        loadConfig($config.data('config-config'));
    }
    
    
}

function saveConfig() {


    const $default = $('#config-default');

    let id = $(this).data('id');

    if($default.prop('checked')) {
        $('[data-default="0"]').remove();
    }
    
    let config = [];
    $('#table-configuration').find('.config-row').each(function () {
        let $this = $(this);
        config.push({
            key: $this.find('[data-key]').val(),
            value: $this.find('[data-value]').val(),
            json: $this.find('[data-json]').prop('checked') ? 1 : 0,
        });
    });
    
    sendConfigAction({
        action: 'save',
        id: id,
        name: $('#config-name').val(),
        config: config,
        default: $default.prop('checked') ? 1 : 0,
    }).then(function(data) {
        if (data.success) {
            $('#modal-config').modal('hide');
            let $html = $(data.html);
            $html.find('[data-delete]').on('click', function() {confirmDelete($(this), deleteConfig)});

            if(id > 0) {
                $('#config-' + id).replaceWith($html);
            } else {
                $('#table-configurations').append($html);
            }
            
        }
    });

}

function deleteConfig($element) {
    const id = $element.data('delete');
    sendConfigAction({
        action: 'delete',
        id: id
    }).then(function(data) {
        if (data.success) {
            $('#config-' + id).remove();
        }
    });

}

function sendConfigAction(data) {    
    data['actid'] = 'manageTinyMceConfigs';

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

function addConfigRow(key, value, isJson) {    
    const $html = $('<tr class="config-row">' +
        '<td><input type="text" class="form-control form-control-sm" data-key="1" value="' + key + '"></td>' +
        '<td><input type="text" class="form-control form-control-sm" data-value="1"></td>' +
        '<td><input type="checkbox" data-json="1" ' + (isJson ? 'checked' : '') + '></td>' +
        '<td><i class="fas fa-times link-danger cursor-pointer" data-remove-row="1"></i></td>' +
        +'</tr>');

    $html.find('[data-value]').val(value);
    
    initConfigRow($html);

    $('#table-configuration').append($html);
}


function initConfigRow($element) {
    $element.find('[data-remove-row]').on('click', deleteConfigRow);
}

function deleteConfigRow() {
    $(this).closest('tr').remove();
}

function loadConfig(config) {
    const $table = $('#table-configuration');
    $table.html('');

    for (let [key, value] of Object.entries(config)) {
        let isJson = false;
        if(Array.isArray(value)) {
            isJson = true;
            value = JSON.stringify(value);
        }
        addConfigRow(key, value, isJson);
    }
}


