/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Replaces entries in object base with entries of object overwrites, if set
 * @param base
 * @param overwrites
 * @returns object
 */
function objectReplace(base, overwrites) {
    for (const propName in overwrites) {
        if (overwrites[propName]) {
            base[propName] = overwrites[propName];
        }
    }
    return base;
}

/**
 * Loads the iframe preview on the specified side
 */
function loadPreview() {
    report_form.preview.value = 1;

    // store dynamic data
    const contentPreviewForm = $('#report_preview_form');
    const leftElements = getFormValues(contentPreviewForm);

    const contentForm = $('#content_form');
    const rightElements = getFormValues(contentForm);

    const hiddenInput = $('input[name=resolvedDynamicData]');
    let dynamicData = hiddenInput.val() ? JSON.parse(hiddenInput.val()) : {};
    dynamicData = objectReplace(dynamicData, leftElements);
    dynamicData = objectReplace(dynamicData, rightElements);
    hiddenInput.val(JSON.stringify(dynamicData));

    const iframe = $('#preview_frame');
    if (iframe.length) {
        // reload using ajax
        fetchReportPreview(function(path) {
            preview_path = path;
            iframe.attr('src', path);
        });
    } else {
        // submit
        const mainForm = $('#report_form');
        mainForm.submit();
    }
}

/**
 * Downloads the previewed file with the given name
 * @param name
 */
function downloadUrl(name) {
    const link = document.createElement("a");
    link.download = name;
    link.href = preview_path;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Opens the previewed file in a new tab
 */
function openInNewTab() {
    open(preview_path, '_blank');
}

/**
 * Archives the previewed file
 * @param button
 */
function reportArchive(button) {
    limbasWaitsymbol(null, true);

    $('#reportOutput').val(2);
    const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
    const callback = function(result) {

        limbasWaitsymbol(null, true, true);
        if (ajaxEvalScript(result)) {
            if(!$(button).hasClass("btn-success") || !result) {
                $('#report_Archive').addClass("btn btn-danger");
                $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-exclamation'></i>");
            }
            return; // error
        }
        if(!$(button).hasClass("btn-success")) {
            $(button).prop('disabled', true); // disable button
            $(report_form.report_rename).prop('disabled', true); // disable name field
            $(button).addClass("btn btn-success");
            $(button).html($(button).html() + " <i class='lmb-icon lmb-check'></i>");
            document.getElementById("report_source").src = result;
        }
    };
    ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
}

/**
 * Regenerates and prints the previewed pdf
 */
function reportPrint(button) {
    limbasWaitsymbol(null, true);

    $('#reportOutput').val(4);
    const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
    const callback = function(result) {

        limbasWaitsymbol(null, true, true);
        if (ajaxEvalScript(result) || !result) {
            if(!$('#report_Archive').hasClass("btn-danger")) {
                $('#report_Archive').addClass("btn btn-danger");
                $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-exclamation'></i>");
            }
            return; // error
        }
        $(button).prop('disabled', true); // disable button
        $(report_form.report_rename).prop('disabled', true); // disable name field
        $(button).addClass("btn btn-success");
        $(button).html($(button).html() + " <i class='lmb-icon lmb-check'></i>");

        /*<?php if($umgvar['printer_cache']){?>
            if(!$('#report_Archive').hasClass("btn-success")) {
                $('#report_Archive').prop('disabled', true); // disable button
                $('#report_Archive').addClass("btn btn-success");
                $('#report_Archive').html($('#report_Archive').html() + " <i class='lmb-icon lmb-check'></i>");
                document.getElementById("report_source").src = result;
            }
            <?php }?>*/
    };
    ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
}

/**
 * Generates a pdf preview with ajax
 * @param callbackOuter function(filePath)
 */
function fetchReportPreview(callbackOuter) {
    limbasWaitsymbol(null, true);

    $('#reportOutput').val(5);
    const fields = ['gtabid', 'report_id', 'ID', 'use_record', 'report_medium', 'report_rename', 'report_printer', 'resolvedTemplateGroups', 'resolvedDynamicData', 'report_output'];
    const callback = function(result) {
        limbasWaitsymbol(null, true, true);

        if (ajaxEvalScript(result)) {
            return; // error
        }
        const path = result.trim();
        if (!path) {
            showAlert(jsvar['lng_56']);
            return;
        }
        callbackOuter(path);
    };
    ajaxGet(null,'main_dyns.php','reportAction',fields,callback,'report_form');
}

/**
 * Gets all set values of inputs, selects and textareas in the given html container
 * @param $container jQuery
 */
function getFormValues($container) {
    const elements = $container.find('input[name],select[name],textarea[name]');
    const keyVal = {};
    elements.each(function() {
        keyVal[$(this).attr('name')] = $(this).val();
    });
    return keyVal;
}

// on load
$(function() {
    // set correct medium
    //report_form.report_medium.value= '<?= $report_medium ?>';

    // update left/right inputs if right/left changed
    $('input[name],select[name],textarea[name]').change(function() {
        const changedEl = $(this);
        const otherEl = $('[name="' + changedEl.attr('name') + '"]').not(changedEl);
        if (otherEl.length === 1) {
            otherEl.val(changedEl.val());
        }
    })

    //move all footer elements to the bottom
    $('htmlpagefooter').appendTo($('#report_preview_form'))
});
