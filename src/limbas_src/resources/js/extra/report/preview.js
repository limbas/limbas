/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



$(function() {
   $('[data-report-action]').on('click', reportActionClick);

   
   if(!$('#report_form').data('has-data')) {
       reportAction('preview');
   }

    // update left/right inputs if right/left changed
    $('input[name],select[name],textarea[name]').on('change', reportHandleDataChange);
    

    //move all footer elements to the bottom
    $('htmlpagefooter').appendTo($('#report_preview_form'))
   
});

/**
 * Handle report button click
 */
function reportActionClick() {
    reportAction($(this).data('report-action'));
}

/**
 * Handle report actions
 */
function reportAction(action, params={}) {
    return new Promise((resolve, reject) => {
        $('#report_source').addClass('d-none');
        $('.btn-show-preview').addClass('d-none');
        $('.load-preview').removeClass('d-none');

        if(action === 'preview') {
            reportSetArchiveBtn(null);
        }
        
        let data = {
            actid: 'reportAction',
            action: action,
            gtabid: $('input[name="gtabid"]').val(),
            report_id: $('input[name="report_id"]').val(),
            ID: $('input[name="ID"]').val(),
            use_record: $('input[name="use_record"]').val(),
            report_rename: $('input[name="report_rename"]').val(),
            report_printer: $('select[name="report_printer"]').val(),
            resolvedTemplateGroups: $('input[name="resolvedTemplateGroups"]').val(),
            resolvedDynamicData: $('input[name="resolvedDynamicData"]').val(),
            dmsIds: $('#btn-report-archive').data('ids')
        }
        
        
        if(action === 'print' || action === 'archivePrint') {
            // add printer options
            data = {...data, ...getPrinterOptions()};
        }

        data = {...data, ...params};        

        $.get({
            dataType:'json',
            url: 'main_dyns.php',
            data: data,
            success: function (data) {
                if(action === 'preview') {
                    reportPreviewCallback(data);
                }
                else if(action === 'print' || action === 'archivePrint') {
                    reportArchiveCallback(data);
                }
                else if(action === 'archive') {
                    reportArchiveCallback(data);
                }
                resolve(data);
            },
            error: function () {
                if(action === 'preview') {
                    reportPreviewCallback({success:false});
                }
                else if(action === 'print' || action === 'archivePrint') {
                    reportArchiveCallback({success:false});
                }
                else if(action === 'archive') {
                    reportArchiveCallback({success:false});
                }
                reject();
            }
        });
    });
}

/**
 * Loads the iframe preview on the specified side
 */
function reportPreviewCallback(data) {

    let $btnShowPreview = $('.btn-show-preview');
    $('.load-preview').addClass('d-none');
    
    if(!data.success) {
        $btnShowPreview.removeClass('d-none');
        lmbShowErrorMsg('Preview could not be loaded.');
    } else {
        const $iframe = $('#report_source');
        $iframe.removeClass('d-none');
        $iframe.attr('src', data.url);
        $('.link-open-report').attr('target','_blank').attr('href', data.url);
        $btnShowPreview.addClass('d-none');
    }

}

/**
 * Archives the previewed file
 * @param data
 */
function reportArchiveCallback(data) {

    let $btnArchive = $('#btn-report-archive');
    
    if(data.success) {

        $('input[name="report_rename"]').prop('disabled', true); // disable name field
        
        reportSetArchiveBtn(1);

        $('.load-preview').addClass('d-none');

        const $iframe = $('#report_source');
        $iframe.removeClass('d-none');
        
        if(data.hasOwnProperty('url')) {
            $iframe.attr('src', data.url);
            $('.link-open-report').attr('target','_blank').attr('href', data.url);
        }

        if(data.hasOwnProperty('ids')) {
            $btnArchive.data('ids',data.ids);
        }
        
        
    } else {
        reportSetArchiveBtn(0);
    }
}


function reportSetArchiveBtn(status) {
    let $btnArchive = $('#btn-report-archive');
    $btnArchive.removeClass('btn-outline-dark btn-outline-success btn-outline-danger');

    $btnArchive.text($btnArchive.data('text'));
    $btnArchive.prop('disabled', false);
    
    if(status === 1) {
        // successfully archived
        $btnArchive.prop('disabled', true);
        $btnArchive.addClass('btn-outline-success');
        $btnArchive.html($btnArchive.html() + ' <i class="lmb-icon lmb-check"></i>');
    }
    else if(status === 0) {
        // archive failed
        $btnArchive.addClass('btn-outline-danger');
        $btnArchive.html($btnArchive.html() + ' <i class="lmb-icon lmb-exclamation"></i>');
    } else {
        // reset to default
        $btnArchive.addClass('btn-outline-dark');
        $btnArchive.removeData('ids');
    }
}


/**
 * Replaces entries in object base with entries of object overwrites, if set
 * @param base
 * @param overwrites
 * @param dontIgnoreEmpty
 * @returns object
 */
function reportObjectReplace(base, overwrites, dontIgnoreEmpty) {
    for (const propName in overwrites) {
        if (overwrites[propName] || dontIgnoreEmpty) {
            base[propName] = overwrites[propName];
        }
    }
    return base;
}


/**
 * Gets all set values of inputs, selects and textareas in the given html container
 * @param $container jQuery
 */
function reportGetFormValues($container) {
    const elements = $container.find('input[name],select[name],textarea[name]');
    const keyVal = {};
    elements.each(function() {
        const type = $(this).attr('type');
        if(type === 'checkbox') {
            if($(this).prop('checked')) {
                keyVal[$(this).attr('name')] = $(this).val();
            } else {
                keyVal[$(this).attr('name')] = '';
            }
        } else {
            keyVal[$(this).attr('name')] = $(this).val();
        }
    });
    return keyVal;
}


/**
 * update left/right inputs if right/left changed
 * save data to hidden dynamic data input
 */
function reportHandleDataChange() {
    const changedEl = $(this);
    const type = $(this).attr('type');   
    
    const otherEl = $('[name="' + changedEl.attr('name') + '"]').not(changedEl);
    if (otherEl.length === 1) {
        if(type === 'checkbox') {
            otherEl.prop('checked', changedEl.prop('checked'))
        } else {
            otherEl.val(changedEl.val());
        }
    }

    // store dynamic data
    const contentPreviewForm = $('#report_preview_form');
    const leftElements = reportGetFormValues(contentPreviewForm);

    const contentForm = $('#content_form');
    const rightElements = reportGetFormValues(contentForm);

    const hiddenInput = $('input[name=resolvedDynamicData]');
    let dynamicData = hiddenInput.val() ? JSON.parse(hiddenInput.val()) : {};
    dynamicData = reportObjectReplace(dynamicData, leftElements, type === 'checkbox');
    dynamicData = reportObjectReplace(dynamicData, rightElements, type === 'checkbox');
    hiddenInput.val(JSON.stringify(dynamicData));
    
}
