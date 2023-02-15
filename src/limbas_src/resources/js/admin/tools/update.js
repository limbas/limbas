/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function(){

    updateInitActions($('#local'));

    updateInitClient($('#clientAccordion'));
});


function updateInitActions($element) {

    $element.find('[data-bs-toggle="popover"]').popover({
        container: 'body'
    })

    $element.find('[data-done]').click(updateMarkPatchAsDone);
    $element.find('[data-run]').click(updateRunPatch);
    $element.find('.btn-run-all').click(updateRunAllUpdates);

}


function updateMarkPatchAsDone() {
    const patch = $(this).data('done');
    const update = $(this).data('update');
    const clientId = $(this).data('client');

    updateRunPatchAction(update, patch, clientId, 'updateMarkPatchAsDone', function(data) {
        if (data.success) {
            updateSetPatchDone(update, patch, clientId);
        }
    });
}

function updateRunPatch() {
    const patch = $(this).data('run');
    const update = $(this).data('update');
    const clientId = $(this).data('client');
    const ident = update + '' + patch + '-' + clientId;

    updateRunPatchAction(update, patch, clientId, 'updateRunPatch', function(data) {
        if (data.success) {
            updateSetPatchDone(update, patch, clientId);
        } else {
            $('#patch' + ident + 'actions').show();

            $('#patch-error-msg').text(data.msg);
            $('#patch-error-toast').toast('show');

            $('#patch' + ident + 'error-content').attr('data-bs-content',data.msg);
        }
    });

}

function updateRunPatchAction(update, patch, clientId, action, successCallback, completeCallback = null) {

    const ident = update + '' + patch + '-' + clientId;
    const $patchLoad = $('#patch' + ident + 'loading');
    const $patchActions = $('#patch' + ident + 'actions');

    $patchActions.hide();
    $patchLoad.removeClass('d-none');

    $('#patch' + ident + 'new').hide();
    $('#patch' + ident + 'error-content').removeClass('d-none');


    $.ajax({
        method: 'post',
        url: 'main_dyns_admin.php',
        dataType: 'json',
        data: {
            action: 'setup_update',
            actid: action,
            update: update,
            patch: patch,
            client: clientId
        },
        success: successCallback,
        complete: function () {
            $patchLoad.addClass('d-none');
            if (completeCallback !== null && typeof completeCallback === 'function') {
                completeCallback();
            }
        }
    });
}

function updateSetPatchDone(update, patch, clientId) {
    const ident = update + '' + patch + '-' + clientId;
    $('#patch' + ident + 'loading').hide();
    $('#patch' + ident + 'actions').hide();
    $('#patch' + ident + 'success').removeClass('d-none');
}

function updateRunAllUpdates() {

    let $this = $(this);
    const update = $this.data('version');
    const clientId = $this.data('client');

    updateSetButtonLoading($this)


    let patches = [];

    $('[data-pending][data-version="' + update + '"][data-client="' + clientId + '"]').each(function() {
        let patch = $(this).data('pending');
        patches.push(patch);

        const ident = update + '' + patch + '-' + clientId;
        let $patchLoad = $('#patch' + ident + 'loading');
        let $patchActions = $('#patch' + ident + 'actions');

        $patchActions.hide();
        $patchLoad.removeClass('d-none');
    });

    if(patches.length > 0) {
        updateRunUpdatesRecursive(patches,0,update,clientId,$this);
    }

}


function updateRunUpdatesRecursive(patches,key,update,clientId,$button) {

    if(!(key in patches)) {
        updateSetButtonLoading($button);
        return;
    }

    let patch = patches[key];

    updateRunPatchAction(update, patch, clientId, 'updateRunPatch', function(data) {
        const ident = update + '' + patch + '-' + clientId;

        if (data.success) {
            updateSetPatchDone(update, patch, clientId);
        } else {
            $('#patch' + ident + 'actions').show();
            $('#patch' + ident + 'error-content').attr('data-bs-content',data.msg);
        }
    }, function() {
        let nextKey = key + 1;
        updateRunUpdatesRecursive(patches,nextKey,update,clientId,$button);
    });

}

function updateSetButtonLoading($button) {

    let disabled = $button.prop('disabled');
    let buttonText = $button.text();

    if (!disabled) {
        $button.data('orgtext',buttonText);
        $button.html('<i class="fas fa-spinner fa-spin"></i>');
        $button.prop('disabled', true)
    } else {
        $button.text($button.data('orgtext'));
        $button.prop('disabled', false)
    }


}



function updateInitClient($element) {

    $element.find('[data-fetch]').click(updateFetchRemoteStatus);

    updateInitActions($element);
}


function updateFetchRemoteStatus(event) {
    event.preventDefault();
    event.stopPropagation();

    let $this = $(this);
    const clientId = $this.data('fetch');
    let $parent = $('#client-' + clientId);

    updateSetButtonLoading($this);

    $.ajax({
        url: 'main_dyns_admin.php',
        dataType: 'json',
        data: {
            action: 'setup_update',
            actid: 'updateFetchRemoteStatus',
            client: clientId
        },
        success: function (data) {
            if(data.success) {
                let $client = $(data.html);
                updateInitClient($client);
                $parent.replaceWith($client);
            }
        },
        complete: function () {
            updateSetButtonLoading($this);
        }
    });

}
