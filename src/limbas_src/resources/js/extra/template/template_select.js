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
    $("#lmbTemplateSelect-search").keyup(function() {
        clearTimeout(timer);
        let ms = 200;
        let searchValue = this.value;        
        timer = setTimeout(function() {
            lmbLoadTemplateList(searchValue,1)
        }, ms);
        
    });
    $('#lmbTemplateSelect-search-reset').click(function () {
        $("#lmbTemplateSelect-search").val('').keyup();
    });
    limbasReportInitPagination();
});


/* region template list and initial loading*/

function lmbShowTemplateModal(gtabid, type) {
    limbasBackToReportList();
    
    let $templateSelect = $('#lmbTemplateSelect');
    if(gtabid !== 0) {
        $templateSelect.data('gtabid',gtabid);
    }
    $templateSelect.data('type',type)
    
    lmbLoadTemplateList('',1).then(function (data) {
        
        if(data.skipResolve) {
            lmbTemplateResolvedAction(type,data.params);
            return;
        }
        
        $templateSelect
            .dialog({
                width: 650,
                height: 530,
                resizable: true,
                modal: true,
                buttons: {
                    close: {
                        text: jsvar['lng_844'],
                        click: function() {
                            $( this ).dialog( 'close' );
                        }
                    }
                },
                open: function (event, ui) {
                    $(".ui-widget-overlay").click(function () {
                        $('#lmbTemplateSelect').dialog('close');
                    });
                }
            });
    });
}

function lmbHideTemplateModal() {
    let $templateSelect = $('#lmbTemplateSelect');
    if($templateSelect.hasClass('ui-dialog-content') && $templateSelect.dialog('isOpen')) {
        $templateSelect.dialog('close');
    }
}

function showReportModal() {
    lmbShowTemplateModal(0, 'report');
}

function showMailModal() {

    //let url = $('#mail-send-link').val();

    //showFullPageModal('E-Mail verfassen','<iframe src="'+url+'" class="w-100 h-100"></iframe>','fullscreen');

    lmbShowTemplateModal(0, 'mail');

}


function limbasBackToReportList() {
    $('#lmbTemplateSelect-list').show();
    $('#lmbTemplateSelect-single').hide();
}

function lmbLoadTemplateList(searchValue,page, id = 0) {

    let $templateSelect = $('#lmbTemplateSelect');
    
    let gtabid = $templateSelect.data('gtabid');
    let type = $templateSelect.data('type');
    let $loader = $('#lmbTemplateSelectLoader');
    let $contentTable = $('#lmbTemplateSelect-table');
    
    $loader.show();
    $contentTable.hide();

    if (searchValue === '') {
        $('#lmbTemplateSelect-search-reset').closest('.input-group-text').addClass('d-none');
    } else {
        $('#lmbTemplateSelect-search-reset').closest('.input-group-text').removeClass('d-none');
    }

    if(id === 0) {
        id = parseInt($('#form1').find('input[name="ID"]').val());
    }


    return new Promise((resolve, reject) => {
        $.ajax({
            dataType:'json',
            url: 'main_dyns.php',
            data: {
                actid: 'selectTemplates',
                action: 'loadList',
                gtabid: gtabid,
                id: id,
                search: searchValue,
                page: page,
                perPage: 10,
                type: type
            },
            success: function(data) {
                
                $contentTable.html(data.table);
                $contentTable.find('[data-select-template]').click(lmbOnTemplateClicked);
                $('#lmbTemplateSelect-pagination').html(data.pagination);

                limbasReportInitPagination();

                resolve(data);
            },
            error: function (error) {
                reject(error)
            },
            complete: function () {
                $loader.hide();
                $contentTable.show();
            }
        });
    });  
    
}


/* endregion template list and initial loading*/


/* region template selection and resolving */


function lmbOnTemplateClicked() {
    let $this = $(this);
    let resolved = $this.data('resolved');
    if($this.data('resolve')) {
        let newResolved = keyValueArrToObj($this.closest('form').serializeArray());
        resolved = {...resolved, ...newResolved};
    }
    selectTemplate($this.data('name'),$this.data('element-id'),$this.data('gtabid'),resolved);
}


function selectTemplate(templateName, elementId, gtabid, resolvedTemplateGroups= {}, id= 0) {
    
    if(templateName.length > 0) {
        $('#lmbTemplateSelect-name').text(templateName);
    }

    
    let type = $('#lmbTemplateSelect').data('type');
    let $loader = $('#lmbTemplateSelectLoader');
    let $contentList = $('#lmbTemplateSelect-list');
    let $singleContent = $('#lmbTemplateSelect-single');
    
    if(id === 0) {
        id = parseInt($('#form1').find('input[name="ID"]').val());
    }

    $loader.show();
    $contentList.hide();
    $singleContent.hide();

    $.ajax({
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'selectTemplates',
            action: 'resolve',
            gtabid: gtabid,
            id: id,
            elementId: elementId,
            resolvedTemplateGroups: !$.isEmptyObject(resolvedTemplateGroups) ? JSON.stringify(resolvedTemplateGroups) : null,
            type: type
        },
        success: function(data) {
            
            if(data.resolved === true) {
                $singleContent.show();
                lmbHideTemplateModal();
                lmbTemplateResolvedAction(data.type, data.params);
                return;
            }
            
            if(data.html.length > 0) {
                let $resolvedContent = $('#lmb-report-resolve');
                $resolvedContent.html(data.html);
                $resolvedContent.find('[data-select-template]').click(lmbOnTemplateClicked);
                $singleContent.show();
            } else {
                $contentList.show();
            }
        },
        error: function (error) {
            $contentList.show();
        },
        complete: function () {
            $loader.hide();
        }
    });
}

function lmbTemplateResolvedAction(type, params) {
    
    if(type === 'report') {
        limbasReportShowPreview(params.url, params.name);
    } else if(type === 'mail') {
        lmbOpenMailForm(params.url);
    }
    
}


function limbasReportInitPagination() {
    let $pagination = $('#lmbTemplateSelect-pagination');
    $pagination.find('.page-link').click(function(){
        let page = $(this).data('page');
        let searchValue = $("#lmbTemplateSelect-search").val();
        lmbLoadTemplateList(searchValue,page);
    });

    $pagination.find('input').change(function(){
        let maxpage = $(this).data('max-page');
        let searchValue = $("#lmbTemplateSelect-search").val();
        
        let pages = $(this).val().split('/');
        
        if (pages.length <= 0) {
            return;
        }
        
        let newPage = parseInt(pages[0]);
        if (Number.isInteger(newPage) && newPage <= maxpage && newPage >= 1) {
            lmbLoadTemplateList(searchValue,newPage);
        }
    });
    
}


/* endregion template selection and resolving */
