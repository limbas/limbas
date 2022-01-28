let tinymceChangeLoaded = false;

$(function() {

    $('.list-group-item').on('click', function() {
        $(this).find('.lmb-icon')
            .toggleClass('lmb-caret-right')
            .toggleClass('lmb-caret-down');
    });


    $('[data-load]').on('click', function () {
        if(!$(this).data('loaded')) {
            $(this).data('loaded',true);
            loadStructure(this,$(this).data('load'),$(this).data('reportid'),$(this).data('gtabid'),0,$(this).data('template'),$(this).data('settings'));
        }
    });
    
    $('[data-add]').click(addTemplateToGroupModal);
    $('#btn-show-copy-template').click(copyTemplateToGroupModal);
    $('#btn-save-new-template').click(addTemplateToGroup);
    $('#btn-save').click(saveTemplate);

    $('#btn-show-delete-template').click(deleteTemplateConfirm);
    $('#btn-delete-template').click(deleteTemplate);
    
    $('#form1').change(function () {
        activateSaveButton();
    });
    
    $('[data-delete]').click(deleteTemplateConfirm);
});

function initElement(element) {
    element.filter('.list-group-item').on('click', function() {
        $(this).find('.lmb-icon')
            .toggleClass('lmb-caret-right')
            .toggleClass('lmb-caret-down');
    });

    element.find('[data-load]').addBack('[data-load]').on('click', function () {
        if(!$(this).data('loaded')) {
            $(this).data('loaded',true);
            loadStructure(this, $(this).data('load'),$(this).data('reportid'),$(this).data('gtabid'),$(this).data('tgtabid'),$(this).data('template'),'');
        }
    });

    element.find('[data-edit]').addBack('[data-edit]').click(editTemplate);

    element.find('[data-add]').addBack('[data-add]').click(addTemplateToGroupModal);

    element.find('[data-delete]').addBack('[data-delete]').click(deleteTemplateConfirm);
}

function loadStructure(sender, element,reportid,gtabid,tgtabid,template,settings) {

    let lid = getRandomInt(100000);
    $(sender).after('<div class="text-center" id="loader-' + lid + '"><img src="pic/wait1.gif"></div>');
    
    $.ajax({
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'reportManagerLoadStructure',
            reportid:reportid,
            gtabid:gtabid,
            tgtabid: tgtabid,
            template:template,
            settings:settings
        },
        success: function(data) {
            let output = '';

            $('#loader-' + lid).remove();
            
            $.each(data, function( index, element ) {

                try {
                    if (element['children'].length <= 0) {
                        output += getSingleListElement(reportid,gtabid,element['tgtabid'],element);
                    } else {
                        output += getGroupList(reportid,gtabid,element['tgtabid'],element)
                    }
                }
                catch (e) {
                    
                }
                
            });


            let $output = $(output);

            initElement($output);

            $('#'+element).html($output);

        }
    });
}

function getSingleListElement(reportid,gtabid,tgtabid,element) {
    
    if (element['haschildren']) {
        
        let gid = getRandomInt(100000);
        
        return '<a href="#report-'+reportid+'-'+element['ident']+gid+'" class="list-group-item list-group-item-action" data-toggle="collapse" data-toggle="collapse" data-load="report-'+reportid+'-'+element['ident']+gid+'" data-reportid="'+reportid+'" data-template="'+element['name']+'" data-gtabid="'+gtabid+'" data-tgtabid="'+tgtabid+'">' +
            getSingleListElementInner(element) +
            '</a>' +
            '<div class="list-group collapse" id="report-'+reportid+'-'+element['ident']+gid+'"></div>';
    } 
    
    
    return '<div class="list-group-item list-group-item-action">' + getSingleListElementInner(element) + '</div>';       
}


function getSingleListElementInner(element) {
    let icon = (element['haschildren']) ? 'lmb-caret-right' : 'lmb-icon-cus lmb-form';
    
    
    let editicon = '';
    if (element['edittype'] !== 2) {
        
        if (element['edittype']) {
            editicon = (element['add']) ? 'lmb-icon lmb-plus' : '';
        } else {
            editicon = (element['edit']) ? 'lmb-icon lmb-pencil' : ''; //'lmb-icon lmb-eye'
        }
        
    }
    
    
    let editparams = 'data-edit-allowed="' + element['edit'] + '" ';
    let addparams = 'data-edit-allowed="' + element['edit'] + '" data-add-allowed="' + element['add'] + '" ';
    if (element['edittype'] !== 2) {
        if (element['edittype'] && element['add']) {
            addparams += 'data-add="'+element['id']+'"';
        } else if(element['haschildren']) {
            addparams += 'data-edit="'+element['id']+'"';
        } else {
            editparams += 'data-edit="'+element['id']+'"';
        }
    }
    
    return '<div class="row cursor-pointer" ' + editparams + ' data-tgtabid="'+element['tgtabid']+'" data-name="'+element['name']+'">' +
        '<div class="col-10"><i class="lmb-icon align-middle mr-2 '+icon+'"></i><span class="align-middle">'+element['name']+'</span></div>' +
        '<div class="col-2 text-right"><i class="'+editicon+'" '+addparams+' data-tgtabid="'+element['tgtabid']+'" data-name="'+element['name']+'"></i></div>' +
        '</div>';
}


function getGroupList(reportid,gtabid,tgtabid,rootelement) {

    let bg = (rootelement['edittype'] === 1 || rootelement['edittype'] === 2) ? 'bg-lightgrey' : '';

    let gid = getRandomInt(100000);
    
    let output = '<a href="#report-'+reportid+'-'+rootelement['ident']+gid+'" class="list-group-item list-group-item-action '+bg+'" data-toggle="collapse">' +
        getSingleListElementInner(rootelement)+'</a>' +
        '<div class="list-group collapse" id="report-'+reportid+'-'+rootelement['ident']+gid+'" data-group="'+rootelement['id']+'">';

    $.each(rootelement['children'], function( index, element ) {

        if (element['children'].length <= 0) {
            output += getSingleListElement(reportid,gtabid,element['tgtabid'],element);
        } else {
            output += getGroupList(reportid,gtabid,element['tgtabid'],element)
        }

    });

    output += '</div>';
    
    return output;
}

function getRandomInt(max) {
    return Math.floor(Math.random() * Math.floor(max));
}

function editTemplate(e) {
    e.stopPropagation();

    loadEditTemplate($(this));
}

function loadEditTemplate($element) {
    
    let id = $element.data('edit');
    let name = $element.data('name');
    let gtabid = $element.data('tgtabid');
    let editAllowed = $element.data('edit-allowed');

    $('.list-group-item').removeClass('text-info m-0');
    $element.closest('.list-group-item').addClass('text-info m-0');
    $('#edit-template-name').text(name);
    $('#editor-div').removeClass('d-none');

    if (!tinymceChangeLoaded) {
        tinymce.activeEditor.on('keyup', activateSaveButton);
        tinymceChangeLoaded = true;
    }

    if (editAllowed) {
        tinymce.activeEditor.mode.set('design');
        $('#btn-save').show();
    } else {
        tinymce.activeEditor.mode.set('readonly');
        $('#btn-save').hide();
    }



    $('#btn-show-delete-template').data('delete','t-' + id).data('delete-name',name).data('gtabid',gtabid);

    $('#btn-show-copy-template').data('tgtabid',gtabid).data('group',$element.data('group')).data('copy',id).data('name',name);

    loadForm($element.data('tgtabid'),id);
    
}


function loadForm(gtabid,id) {
    
    $('#form_gtabid').val(gtabid);
    $('#form_id').val(id);
    
    let ed = tinymce.activeEditor;

    ed.plugins.lmbTemplate.setLmbIDs(gtabid, id);

    ed.setProgressState(true);
    $.ajax({
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'reportManagerGetForm',
            gtabid:gtabid,
            id: id,
        }
    }).success(function(data) {
        ed.setProgressState(false);
        ed.setContent(data.content);
        deactivateSaveButton();
    });
}

function addTemplateToGroupModal(e) {
    e.stopPropagation();
    $('#add-to-group').text($(this).data('name'));
    $('#new-template-name').val('');
    $('#btn-save-new-template').data('source',0).data('sourcegtabid',0);
    
    $('#new-template-group').html('<option value="' + $(this).data('add') + '"  data-tgtabid="' + $(this).data('tgtabid') + '" selected>' + $(this).data('name') + '</option>').hide();
    
    $('#modal-new-template-title').show();
    $('#modal-copy-template-title').hide();
    
    $('#modal-new-template').modal('show');
}

function copyTemplateToGroupModal(e) {
    e.stopPropagation();
    $('#copy-name').text($(this).data('name'));
    $('#new-template-name').val('');
    $('#btn-save-new-template').data('source',$(this).data('copy')).data('sourcegtabid',$(this).data('tgtabid'));

    loadGroupsToCopySelect();
    $('#new-template-group').show();

    $('#modal-new-template-title').hide();
    $('#modal-copy-template-title').show();
    
    $('#modal-new-template').modal('show');
}

function addTemplateToGroup() {

    let $newTempGroup = $('#new-template-group');
    let tgtabid = $newTempGroup.find(':selected').data('tgtabid');
    let group = $newTempGroup.val();
    let name = $('#new-template-name').val();
    let source = $(this).data('source');
    let sourcegtabid = $(this).data('sourcegtabid');

    tinymce.activeEditor.setContent('');

    $('#modal-new-template').modal('hide');
    $.ajax({
        method: 'post',
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'reportManagerAddTemplate',
            tgtabid:tgtabid,
            group: group,
            name: name,
            source: source,
            sourcegtabid: sourcegtabid
        }
    }).success(function(data) {
        
        if (data.success) {
            let $ele = $(getSingleListElement(0,0,tgtabid,data.element));
            initElement($ele);
            $("[data-group='"+group+"']").append($ele);
            loadEditTemplate($ele.find('.row').first());
            $(window).scrollTop($ele.offset().top);
        }
        
    });
}

function saveTemplate() {

    let ed = tinymce.activeEditor;
    
    $.ajax({
        method: 'post',
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'reportManagerSaveTemplate',
            gtabid:$('#form_gtabid').val(),
            id: $('#form_id').val(),
            content: ed.getContent()
        }
    }).success(function(data) {
        deactivateSaveButton();
    });
}


function deleteTemplateConfirm(e) {
    e.stopPropagation();
    
    let id = $(this).data('delete');
    let name = $(this).data('delete-name');
    let gtabid = $(this).data('gtabid');
    
    $('#delete-name').text(name);
    $('#btn-delete-template').data('id',id).data('gtabid',gtabid);
    $('#modal-delete-template').modal('show');
    
}


function deleteTemplate() {
    let id = $(this).data('id');
    let gtabid = $(this).data('gtabid');

    $.ajax({
        dataType:'json',
        url: 'main_dyns.php',
        data: {
            actid: 'reportManagerDeleteTemplate',
            gtabid: gtabid,
            id: id
        }
    }).success(function(data) {

    });

}

function activateSaveButton() {
    $('#btn-save').prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
}

function deactivateSaveButton() {
    $('#btn-save').prop('disabled',true).addClass('btn-secondary').removeClass('btn-primary');
}

function loadGroupsToCopySelect() {
    
    let options = '';
    
    let dups = [];
    
    $('[data-add]').each(function() {
        let $this = $(this);
        let gid = $this.data('add');
        let dup = dups.includes(gid);
        if (!dup && $this.data('add-allowed')){
            options += '<option value="' + gid + '" data-tgtabid="' + $this.data('tgtabid') + '" >' + $this.data('name') + '</option>';
            dups.push(gid);
        }        
    });    
    
    $('#new-template-group').html(options);
    
}
