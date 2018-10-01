/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */
var selectedCard = 0;
var selectedStat = 0;
var filter_visible = true;
var filter_reset = 0;

$(function() {
    kanban_init();
});


function kanban_init()
{
    kanban_load_cards();

    $('.lmbfringegtab').css('height',$(window).height()-120);
    $('.col-add').click(kanban_card_add);

    $('.kanban-filter-head').click(kanban_showhide_filter);

    $('.filter-list .filter-item[data-ftype="list"]').click(kanban_filter_setactive);
    $('.filter-list .filter-item[data-ftype="text"]').keyup(kanban_filter_setactive);
    $('.filter-list .filter-item[data-ftype="text"]').change(kanban_filter_setactive);
    $('.filter-list .filter-item[data-ftype="text"]').find('.lmb-close-alt').click(kanban_filter_reset_text);

    $('.filter-title').click(kanban_showhide_filterlist);

    $(kanban_columns).sortable({
        connectWith: ".kanban-sort",
        placeholder: "kanban-drop",
        cursor: 'move',
        helper : 'clone',
        stop: function (event, ui) {
            kanban_card_drop(event, ui);
        }
    }).disableSelection();
}


function kanban_card_drop(event, ui)
{
    var order = [];
    ui.item.parent().children().each(function () {
        order.push($(this).data('id'));
    });
    $.ajax({
        type: "POST",
        url: "main_dyns.php",
        data: "actid=kanban&action=drop&gtabid=" + jsvar['gtabid'] + "&ID=" + ui.item.data('id') + "&status=" + ui.item.parent().data('status')+"&order="+order.toString()
    }).done(function (data) {
        ajaxEvalScript(data);
        ui.item.find('[data-fstatus]').attr('data-fstatus',ui.item.parent().data('statustitle'));
        kanban_filter_count();
        kanban_filter();
    });
}

function kanban_card_add()
{
    var r = confirm(jsvar["lng_24"]);
    if (r)
    {
        selectedStat = $(this).data('status');
        $.ajax({
          type: "POST",
          url: "main_dyns.php",
          data: "actid=kanban&action=add&gtabid="+jsvar['gtabid']+"&status="+selectedStat,
          success: function(data){
                  kanban_load_card_by_id(data);
                  kanban_card_detail(data);
              }
        });
    }
}

function kanban_card_delete(gtabid, ID){
	divclose();
	var title = confirm(jsvar['lng_84']);
	if(title){
	$.ajax({
	  type: "POST",
	  url: "main_dyns.php",
	  data: "actid=kanban&action=delete&gtabid="+gtabid+"&ID="+ID,
	  success: function(data){
	  	if(data.substr(0,4) == 'true' && ID > 0){
	  		$(".card[data-id='"+ID+"']").remove();
	  		$("#lmbKanAjaxContainer").dialog('destroy');
	  	}else{
	  		ajaxEvalScript(data);
	  	}
	  	// reset history_fields
		document.form1.history_fields.value='';
	  }
	});
	}
}

function kanban_card_archive(gtabid,ID)
{
    var title = confirm(jsvar['lng_49']);
    if(title){
        $.ajax({
            type: "POST",
            url: "main_dyns.php",
            data: "actid=kanban&action=archive&gtabid="+gtabid+"&ID="+ID,
            success: function(data){
                if(data.substr(0,4) == 'true' && ID > 0){
                    $(".card[data-id='"+ID+"']").remove();
                    $("#lmbKanAjaxContainer").dialog('destroy');
                }else{
                    ajaxEvalScript(data);
                }
            }
        });
    }
}


// Post edit event
function kanban_card_edit(id){
	send_form();
    kanban_load_card_by_id(id);
}

function kanban_load_card_by_id(id)
{
    $.ajax({
	  type: "POST",
	  url: "main_dyns.php",
      dataType: 'json',
	  data: "actid=kanban&action=getCard&gtabid="+jsvar['gtabid']+"&ID="+id,
	  success: function(data){
              if (data != 0)
              {
                  if ($('div[data-id="'+id+'"]').length)
                  {
                      //check if status changed
                      var status = $('div[data-id="'+id+'"]').parent().data('status');
                      if (status != data['status'])
                      {
                          $('div[data-id="'+id+'"]').remove();
                          $('.kanban-body div[data-status="'+data['status']+'"]').append(kanban_card_render(data));
                      }
                      else
                      {
                          $('div[data-id="'+id+'"]').replaceWith(kanban_card_render(data));
                      }
                  }
                  else
                  {
                      $('.kanban-body div[data-status="'+data['status']+'"]').append(kanban_card_render(data));
                  }

                  $('div[data-id="'+id+'"]').click(function() {
                      kanban_card_detail($(this).data('id'));
                  });
                  kanban_filter_count();
              }
          }
	});
}


// open details
function kanban_card_detail(id) {
	selectedCard = id;
	// reset history_fields
	document.form1.history_fields.value='';


        var w_ = 550;
	var h_ = 380;

	// specific formular
	if(jsvar['formid']){

		if(jsvar['form_dimension']){
			var size = jsvar['form_dimension'].split('x');
			w_ = (parseFloat(size[0])+40);
			h_ = (parseFloat(size[1])+80);
		}

		// display in iframe
		if(jsvar['iframe']){
			$('#lmb_detailFrame').contents().find('body').html('');
			$("#lmb_eventDetailFrame").css({'position':'relative','left':'0','top':'0'}).dialog({
				width: w_,
				height: h_,
				resizable: true,
				modal: true,
				zIndex: 10,
				open: function(ev, ui){
					$('#lmb_detailFrame').attr("src","main.php?&action=details&act=gtab_change&ID="+id+"&gtabid="+jsvar['gtabid']+"&form_id="+jsvar['fformid']);
				}
			});
			return;
		}
	}

	$.ajax({
	  type: "GET",
	  url: "main_dyns.php",
	  dataType: "html",
	  data: "actid=kanban&action=details&act=gtab_change&gtabid="+jsvar['gtabid']+"&form_id="+jsvar['fformid']+"&ID="+id,
	  success: function(data){
	  	document.getElementById("lmbKanAjaxContainer").innerHTML = data;
	  	ajaxEvalScript(data);


                if(!jsvar['formid']){h_ = ($("#lmbKanAjaxContainer").height()+50);}

                if($("#lmbKanAjaxContainer").hasClass('ui-dialog-content')){
                        $("#lmbKanAjaxContainer").dialog('open');
                }else{
                        $("#lmbKanAjaxContainer").css({'position':'relative','left':'0','top':'0'}).dialog({
                                width: w_,
                                height: h_,
                                resizable: true,
                                modal: true,
                                zIndex: 10,
                                close:lmb_closeKanDialog,
                                appendTo: "#form1"
                        });
                }

	  }
	});



}

function lmb_closeKanDialog(){
	if($("#lmbKanAjaxContainer").hasClass('ui-dialog-content')){$("#lmbKanAjaxContainer").dialog('close');}
	document.form1.history_fields.value='';
}






function send_form(ajax,need) {
	var needok = needfield(need);
	if(needok){
		alert(jsvar["lng_1509"]+"\n\n"+needok);
	}else{
		dynfunc = function(result){send_formPost(result,ajax);};
		ajaxGet(null,'main_dyns.php','kanban&action=saveDetails',null,'dynfunc','form1',null,1);

		return true;
	}
}

// Ajax post formelements output
function send_formPost(result,ajax){
	if(!ajax && $("#lmbKanAjaxContainer").hasClass('ui-dialog-content')){
		$("#lmbKanAjaxContainer").dialog('destroy');
	}
	ajaxEvalScript(result);
}

function kanban_load_cards() {
    form_array = new Array('formKanban','form11');
    $.ajax({
        type: "GET",
        url: "main_dyns.php",
        dataType: "json",
        data: $.extend(true,lmb_getFormData(form_array),{actid: 'kanban'},{action: 'loadCards'},{gtabid:jsvar['gtabid']},{filter_reset:filter_reset}),
        //data: "actid=kanban&action=loadCards&gtabid=" + jsvar['gtabid'] + "&filter_reset="+filter_reset,
        success: function (data) {
            $.each(data, function (key, card) {
                $('.kanban-body div[data-status="' + card['status'] + '"]').append(kanban_card_render(card));
            });
            $('.card').click(function () {
                kanban_card_detail($(this).data('id'));
            });
            kanban_filter_count();
            kanban_filter();
        }
    });
}

function kanban_destroy() {
    $('.card').remove();
}

function kanban_card_render(card)
{
    var output = '';
    output += '<div class="card" data-id="'+card.id+'">';
    if (card.tags.length > 0)
    {
        output += '<div class="card-tags">';
        $.each(card['tags'], function(key, tag) {
            output += '<span style="background-color: '+tag['color']+'" title="'+tag['tag']+'" class="card-tag"></span>';
        });
        output += '</div>';
    }
    output += '<div class="card-title">'+card['title']+'</div>';
    if (card.assigned.length > 0)
    {
        output += '<div class="card-assigned">';
        $.each(card['assigned'], function(key, assigned) {
            output += '<div class="card-assigned-value" title="'+assigned.long+'">'+assigned.short+'</div>';
        });
        output += '</div>';
    }
    output += '<div class="card-meta">';
    $.each(card['filters'], function(fkey, filter) {

        if ($.isArray(filter))
        {
            $.each(filter, function(key, value) {
                output += '<span data-f'+fkey+'="'+value+'"></span>';
            });
        }
        else
        {
            output += '<span data-f'+fkey+'="'+filter+'"></span>';
        }
    });
    output += '</div></div>';
    return output;
}


function kanban_reload(){
    kanban_destroy();
    kanban_load_cards();
}


function LmGs_sendForm(reset){
    if(reset){
        kanban_filter_reset();
    }else{
        // sync searchtab with detail searchform
        $('#extsearchtab input, #extsearchtab select').filter('.gtabHeaderInputINP').each(function() {
            var n = $(this).attr('name');
            if(document.form11.elements[n]){
                $(this).val(document.form11.elements[n].value);
            }
        });

        kanban_reload();
    }
    $("#lmbKanAjaxContainer").dialog('destroy');
}


function kanban_filter_count()
{
    var fname = '';
    var fval;
    $('div.filter-list').each(function () {

        fname = $(this).data('filter');
        $(this).children('div').each(function() {
            fval = $(this).data('fvalue');
            $(this).children('.counter').html($('[data-f'+fname+'="'+fval+'"]').length);
        });
    });
}


function kanban_filter_setactive()
{
    if ($(this).data('factive') == 1)
    {
        if ($(this).data('ftype') == 'list' || ($(this).data('ftype') == 'text' && $(this).children('input').val() == '' ))
        {
            $(this).data('factive',0);
            $(this).attr('data-factive',0).ready(kanban_filter); // async function
        }
    }
    else
    {
        $(this).data('factive',1);
        $(this).attr('data-factive',1);
    }
    kanban_filter();
}

function kanban_filter_reset_text()
{
    $(this).siblings('input').val('').change();
}


function kanban_filter_remove()
{
    var rel = $(this).data('rel');
    $(rel).data('factive',0);
    $(rel).attr('data-factive',0);
    $(this).remove();
    kanban_filter();
}


function kanban_filter()
{
    if ($('[data-factive="1"]').length <= 0)
    {
        $('.card').show();
    }
    else
    {
        $('.card').hide();
        $('#active-filters').html('');
        $('[data-factive="1"]').each(function( index ) {
            var fname = $(this).parent().data('filter');
            var fval = '';

            if ($(this).data('ftype') == 'list')
            {
                fval = $(this).data('fvalue').toLowerCase();
                $('[data-f'+fname+'="'+fval+'"]').closest('.card').show();
            }
            else if ($(this).data('ftype') == 'text')
            {
                fval = $(this).children('input').val().toLowerCase();
                $('[data-f'+fname+'*="'+fval+'"]').closest('.card').show();
            }
            if (jsvar['kan_showactive']){
                $(this).clone().appendTo( "#active-filters" ).data('rel',this);
            }
        });
        $('#active-filters .filter-item').click(kanban_filter_remove);
    }
}



function kanban_showhide_filter() {
    if(!filter_visible){
        $('.kanban-filter').css('flex','');
        $('.kanban-filter-head').css('flex','');
        $('.kanban-filter-head').find('.col-title').show();
        $('.kanban-filter').children().show();
        filter_visible = true;
    }
    else
    {
        $('.kanban-filter').css('flex','0 0 40px');
        $('.kanban-filter-head').css('flex','0 0 40px');
        $('.kanban-filter-head').find('.col-title').hide();
        $('.kanban-filter').children().hide();
        filter_visible = false;
    }
}

function kanban_showhide_filterlist()
{
    if ($(this).siblings('.filter-list').is(":visible"))
    {
        $(this).children('i').removeClass('lmb-angle-up').addClass('lmb-angle-down');
        $(this).siblings('.filter-list').hide();
    }
    else
    {
        $('.filter-title').children('i').removeClass('lmb-angle-up').addClass('lmb-angle-down');
        $(this).children('i').removeClass('lmb-angle-down').addClass('lmb-angle-up');
        $('.filter-list').hide();
        $(this).siblings('.filter-list').show();
    }
}

function kanban_filter_reset(){
    $('.card').show();
    $('[data-factive="1"]').each(function( index ) {
        $(this).data('factive',0);
        $(this).attr('data-factive',0);
    });
    $('#extsearchtab input, #extsearchtab select').filter('.gtabHeaderInputINP').each(function() {
        $(this).val('');
    });
    filter_reset = 1;
    kanban_reload();
    filter_reset = 0;
}

// open formular
function lmb_popupDetails(gtabid,ID) {
    divclose();
    details = open("main.php?&action=gtab_change&ID=" + ID + "&gtabid=" + gtabid + "" ,"details","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=650,height=600");
}