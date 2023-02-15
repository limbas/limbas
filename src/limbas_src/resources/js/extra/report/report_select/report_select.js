
/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function(){
   $('#btn-back-to-report-list').click(limbasBackToReportList);
    let timer;
    $("#lmb-report-select-search").keyup(function() {
        clearTimeout(timer);
        let ms = 200;
        let searchValue = this.value;        
        timer = setTimeout(function() {
            limbasReportLoadList(searchValue,1)
        }, ms);
        
    });
    $('#lmb-report-select-search-reset').click(function () {
        $("#lmb-report-select-search").val('').keyup();
    });
    limbasReportInitPagination();
});


function showReportModal() {

    limbasBackToReportList();
    $('#lmb-report-select').dialog({
        width: 650,
        height: 530,
        resizable: true,
        modal: true,
        buttons: {
            close: {
                text: jsvar['lng_844'],
                click: function() {
                    $( this ).dialog( "close" );
                }
            }
        },
        open: function (event, ui) {
            $(".ui-widget-overlay").click(function () {
                $('#lmb-report-select').dialog('close');
            });
        }
    });
}


function reportModalSelectItem(evt,el,gtabid,reportid,ID,output,listmode,report_medium,report_rename,report_printer, resolvedTemplateGroups={}, resolvedDynamicData={},preview=null) {
    
    $('#lmb-report-select-name').text($(el).data('name'));
    
    if (preview == null) {
        preview = limbasReportSetPreviewCkb($(el).data('preview'));
    }
    
    limbasReportMenuHandler(preview,evt,null,gtabid,reportid,ID,output,listmode,report_medium,report_rename,report_printer, resolvedTemplateGroups, resolvedDynamicData,'',limbasReportMenuOptionsPost2,'bs4');
}

function limbasReportSetPreviewCkb(prevParam) {
    let $ckb = $('#lmb-ckb-report-preview');

    if (prevParam == null){
        return $ckb.prop('checked') ? 1 : 0;
    }

    let preview = 1;

    /*
     * 0 = no preview, visible
     * 1 = preview, visible
     * 2 = no preview, not visible
     * 3 = preview, not visible
     * 
     */

    if (prevParam == 0 || prevParam == 2) {
        $ckb.prop('checked',false);
        preview = 0;
    } else {
        $ckb.prop('checked',true);
    }
    if (prevParam == 2 || prevParam == 3) {
        $ckb.closest('div').addClass('d-none');
    } else {
        $ckb.closest('div').removeClass('d-none');
    }
    return preview;
}

function limbasBackToReportList() {
    $('#lmb-report-select-list').removeClass('d-none');
    $('#lmb-report-select-single').addClass('d-none');
}

function limbasReportLoadList(searchValue,page) {

    let gtabid = $('#lmb-report-select').data('gtabid');

    if (searchValue === '') {
        $('#lmb-report-select-search-reset').closest('.input-group-append').addClass('d-none');
    } else {
        $('#lmb-report-select-search-reset').closest('.input-group-append').removeClass('d-none');
    }
    
    $.ajax({
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'lmbGetReportList',
            gtabid: gtabid,
            search: searchValue,
            page: page,
            perPage: 10
        },
        success: function(data) {

            $('#lmb-report-select-table').html(data.table);
            $('#lmb-report-select-pagination').html(data.pagination);

            limbasReportInitPagination();
        }
    });
    
    
}

function limbasReportMenuOptionsPost2(result,el,output){
    
    if (!ajaxEvalScript(result)) {
        $('#lmb-report-select-list').addClass('d-none');
        $('#lmb-report-resolve').html(result);
        $('#lmb-report-select-single').removeClass('d-none');
    }
    
    if(output==2){
        $('#btn-report-archive').html('<i class="lmb-icon lmb-aktiv"></i>');
        
    }else if(output==1 || output==4){
        activ_menu = 0;
        limbasDivClose();
    }

}


function limbasReportInitPagination() {
    let $pagination = $('#lmb-report-select-pagination');
    $pagination.find('.page-link').click(function(){
        let page = $(this).data('page');
        let searchValue = $("#lmb-report-select-search").val();
        limbasReportLoadList(searchValue,page);
    });

    $pagination.find('input').change(function(){
        let maxpage = $(this).data('max-page');
        let searchValue = $("#lmb-report-select-search").val();
        
        let pages = $(this).val().split('/');
        
        if (pages.length <= 0) {
            return;
        }
        
        let newPage = parseInt(pages[0]);
        if (Number.isInteger(newPage) && newPage <= maxpage && newPage >= 1) {
            limbasReportLoadList(searchValue,newPage);
        }
    });
    
}
