/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

$(function(){
    $('#btn-save').click(saveTemplate);
    $('#form1').change(formChanged);
    $('#template_table').change(templateChange);
    
    let $paperSize = $('#paper_size');
    if($paperSize.length > 0) {
        $paperSize.change(papersizeChange);
        papersizeChange();
    }

    tinymce.activeEditor.on('keyup', contentChanged);
    tinymce.activeEditor.on('change', contentChanged);

});

var templatechanged = false;
function templateChange(){
    templatechanged = true;
}

function papersizeChange() {

    let val = $('#paper_size').val();

    if (val === 'custom') {
        $('#custom_size').removeClass('d-none');
    } else {
        $('#custom_size').addClass('d-none');
    }
}

var contentchanged = false;
function contentChanged() {
    $('#btn-save')
        .prop('disabled',false)
        .removeClass('btn-secondary')
        .addClass('btn-primary');
    contentchanged = true;
}

var formchanged = false;
function formChanged() {
    $('#btn-save')
        .prop('disabled',false)
        .removeClass('btn-secondary')
        .addClass('btn-primary');
    formchanged = true;
}

function saveTemplate() {

    let $this = $(this);
    let btnText = $(this).text();
    
    $this.html('<i class="lmb-icon lmb-spinner fa-spin"></i>')
        .prop('disabled',true)
        .addClass('btn-secondary')
        .removeClass('btn-primary');

    let ed = tinymce.activeEditor;

    if(contentchanged) {
        $('#content').val(ed.getContent());
    }
    content_ = $("#form1").serialize();

    if(contentchanged){
        content_ += "&contentchanged=1";
    }
    if(formchanged){
        content_ += "&formchanged=1";
    }
    if(templatechanged){
        content_ += "&templatechanged=1";
    }

    $.ajax({
        method: 'post',
        dataType:'json',
        url: 'main_dyns_admin.php',
        data: content_,
        success: function(data) {
            contentchanged = false;
            formchanged = false;
            templatechanged = false;
        },
        complete: function(data) {
            $this.html($this.attr("data-title"))
                .prop('disabled',true)
                .removeClass('btn-primary');
        },
    });
}
