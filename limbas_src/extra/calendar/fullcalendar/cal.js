/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID:
 */
var activ_menu = null;
var allFlag = 1;

// init fullcalendar
function lmb_calInit() {

	form_array = new Array('formCalendar','form11');

	// get settings from fomular
	$("#formCalendar input[name^='cal_']").each(function( index ) {
		var sname = $(this).attr('name');
		var val = $(this).val();
		if(val){
			if(!isNaN(val)){val = parseInt(val);}
			jsvar[sname] = val;
		}
	});
	
	// check if already done
	browserType();
	//var viewtype = document.formCalendar.viewtype.value;
        
	$('#calendar').fullCalendar({
		year: jsvar['year'],
		month: jsvar['month'],
		date: jsvar['date'],
		defaultView: jsvar['cal_viewmode'],
		allDayDefault: jsvar['cal_allDayDefault'],
		editable: jsvar['cal_editable'],
		selectable: jsvar['cal_selectable'],
		minTime: jsvar['cal_minTime'],
		maxTime: jsvar['cal_maxTime'],
		weekNumbers:jsvar['cal_weekNumbers'],
		weekNumberTitle: jsvar['cal_weekNumberTitle'],
		weekends: jsvar['cal_weekends'],
		firstDay: jsvar['cal_firstDay'],
		firstHour: jsvar['cal_firstHour'],
		slotMinutes: jsvar['cal_slotMinutes'],
		snapMinutes: jsvar['cal_snapMinutes'],
		header: false,
		unselectAuto: true,
		selectHelper: true,
		select: lmb_calQuickAdd,
		eventDrop: lmb_calDrop,
		eventResize: lmb_calResize,
		eventClick: lmb_calDetail,
		dayClick: lmb_calGotoDay,
		lazyFetching: true,
		columnFormat:{
			week:'ddd - d.MMMM',
			day:'ddd - d.MMMM',
			'':'ddd',
			'resourceWeek':'ddd - d.MMMM',
			'resourceMonth':'dd.MM.'
		},
		timeFormat: 'H:mm{-H:mm}',
		axisFormat: 'H:mm',
		events:lmb_calEvent,
		resources: lmb_calResource,
		refetchResources: false,
		resourceRender: lmb_calResourceRender,
		monthNames: jsvar['jqmonth'],
		monthNamesShort: jsvar['jqmonth'],
		dayNames: jsvar['jqday'],
		dayNamesShort: jsvar['jqday'],
		allDayText: jsvar['lng_2705'],
		eventRender: lmb_calEventRender,
		eventAfterAllRender: function(view) {
			$('#calendar').fullCalendar('renderHolidays', $('#calendar').fullCalendar('getView'));
		},
		dayRender: lmb_calDayRender,
		//holidayRender: lmb_calholidayRender;
		//weekendAsHoliday: true,
		//weekendHolidayColor: '#123456',
		showHolidayName: true,
		
		loading: function(bool) {
			if (bool){
				//$('#loading').show();
				limbasWaitsymbol(null,1,0);
			}else {
				//$('#loading').hide();
				limbasWaitsymbol(null,1,1);
			}
		}
	});
	
	lmb_calSetHeight();
	
	var ratio = (document.getElementById("calendar").offsetWidth/document.getElementById("calendar").offsetHeight);
	$('#calendar').fullCalendar('option', 'aspectRatio', ratio);
	
	lmb_calSetTitle();
}

function divclose() {
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

//used from gtab_change.dao - "save and close" funktion
function lmb_closeIframeDialog() {
    if($('#lmb_eventDetailFrame').hasClass('ui-dialog-content')){
       $('#lmb_eventDetailFrame').dialog('destroy');
       lmb_calReload();
    }
}

function lmb_localobj() {
  	this.monthNames = jsvar['jqmonth'];
}

// event handler - replace intern ajax call for swiching results
function lmb_calEvent(start, end, callback) {
	
	form_array = new Array('formCalendar','form11');

	////console.log('start: ' + start + ', end: ' + end);

	$.ajax({
		url: 'main_dyns.php?actid=fullcalendar&reload=1',
		dataType: 'json',
		type: 'POST',
		data: $.extend(true,lmb_getFormData(form_array),{recurrent: allFlag},{start: Math.round(start.getTime() / 1000)},{end: Math.round(end.getTime() / 1000)},{gtabid:jsvar['gtabid']},{action:'kalender'}),
		success: function(data) {
			var events = [];
			$(data).each(function() {
				if (undefined != this.repeat) {
					$('#calendar').fullCalendar('addRecurrent', this);
				}
				else if (undefined != this.holiday) {
					$('#calendar').fullCalendar('addHoliday', this);
				}
				else {
					events.push(this);
				}
			});
			$.merge(events, $('#calendar').fullCalendar('getRecurrents', start, end));
			callback(events);

			allFlag = 0;
		}
	});
}

function lmb_calResource(){

	var dat = null;
	$.ajax({
		url: 'main_dyns.php?actid=fullcalendar',
		data: ajaxFormToURL('formCalendar')+'&action=resources',
		dataType: 'json',
		type: 'POST',
		async: false,
		success: function(data) {
			dat = data;
			var opt='<option></option>';
			$(data).each(function() {
				opt+='<option value=\"'+this.id+'\">'+this.name+'</option>';
			});

			var mselect = $('#cal_resourceSearch');
			mselect.empty().append(opt);
		}
	});
	
	if(dat && Array.isArray(dat)){
		return dat;
	} else {
		return [];
	}
	
	//"main_dyns.php?actid=fullcalendar&"+ajaxFormToURL('formCalendar')+"&action=resources";
}

// example for holidayRender
function lmb_calholidayRender(date, holiday, element, isWeekend) {
	if (isWeekend) {
		element.find('div.fc-day-number').css({
			width: '95%',
			'text-align': 'right',
			'background-color': 'darkgrey',
			'font-style': 'italic',
			'font-weight': 'bold'
		});
	}
 }
			

// render events with icons and more
function lmb_calEventRender(event, eventElement, view) {
	
	// show event icons
	if (event.imageurl) {
		if (eventElement.find('span.fc-event-time').length) {
			eventElement.find('span.fc-event-time').before($(event.imageurl));
		}else if (eventElement.find('div.fc-event-time').length) {
			eventElement.find('div.fc-event-time').before($(event.imageurl));
			eventElement.find('div.fc-event-time').css("float", "left");
		} else {
			eventElement.find('span.fc-event-title').before($(event.imageurl));
		}
	}
	
	// title
	if(event.titletag){
		eventElement.prop('title', event.titletag);
	}
	
	// contextmenu
    eventElement.bind('contextmenu', function() {
		lmb_calShowContext(event,eventElement.get(0));
		return false;
    });
    
    // mouseover
    eventElement.bind('mouseover', function() {
    	$('.event_' + event.id).css('box-shadow','2px 2px red'); // #909090 #97C5AB
    });
    
    // mouseout
    eventElement.bind('mouseout', function() {
    	$('.event_' + event.id).css('box-shadow','');
    });
}


function lmb_calDayRender(date, cell, resource){
	
	// contextmenu
	cell.bind('contextmenu', function(event) {
		lmb_calShowContext(event,cell,date,resource);
		return false;
    });
	
}

// render resource
function lmb_calResourceRender(resource, element, view) {
	//$(resource).css({
	//		'background-color': 'darkgrey',
	//		'font-style': 'italic',
	//		'font-weight': 'bold'
	//	});
}


// get and show context window
function lmb_calShowContext(event,eventElement,date,resource){
	divclose();
	divclose();

    if(date){
		if(!sessionStorage.lmb_calendar){return;}
		limbasDivShow('',event,'lmbAjaxContextPaste');
		activeDate = date;
		activeResource = resource;
	}else{
		limbasDivShow(eventElement,'','lmbAjaxContextmenu','50x-5',1);
	}
	activeEvent = event;
	activeDiv = eventElement;

    // no divclose trigger on right click
    activ_menu = 0;

	//$.ajax({
	//  type: "POST",
	//  url: "main_dyns.php",
	// data: "actid=fullcalendar&action=context&gtabid="+jsvar['gtabid']+"&ID="+evid,
	// success: function(data){
	//  	 document.getElementById('lmbAjaxContextmenu').innerHTML=data;
	// }
	//});
}


// paste
function lmb_calPaste(date, resource){
	
	divclose();
	divclose();
	lmb_calendar = sessionStorage.lmb_calendar;
	
	if(lmb_calendar){
		session_event = JSON.parse(lmb_calendar);

		// get Event with ID
		var cevent = $('#calendar').fullCalendar('clientEvents',session_event.id);
		
		if(confirm(jsvar['lng_1441']+' '+jsvar['lng_1615'] +' ? \n'+ session_event.title)){
			
			if(session_event.paste == 'move'){
				// try to move event
				if(cevent[0] && cevent[0].end){
					cevent = cevent[0];
			
					// length of event
					var len = cevent.end.getTime()/1000 - cevent.start.getTime()/1000;
			
					cevent.start = date;
					cevent.end = date.getTime()/1000 + len;
					cevent.resource = resource;
					
					$('#calendar').fullCalendar('updateEvent', cevent);
				}
			}else if(session_event.paste == 'copy'){
				
			
			}else{sessionStorage.removeItem("lmb_calendar");return;}

			$.ajax({
				  type: "GET",
				  url: "main_dyns.php",
				  data: "actid=fullcalendar&action="+session_event.paste+"&gtabid="+jsvar['gtabid']+"&ID="+session_event.id+"&resource="+resource+"&stamp="+(date.getTime()/1000),
				  success: function(){
					  if(!len){
						  lmb_calReload();
					  }
				  }
			});
		}
		
		sessionStorage.removeItem("lmb_calendar");
	}
}


// calendar - dayClick
function lmb_calGotoDay(date, allDay, jsEvent, resource){
	divclose();


	// paste
	if(jsEvent.ctrlKey && sessionStorage.lmb_calendar){
		lmb_calPaste(date, resource.id);

	// go to date
	}else if(jsEvent.shiftKey || jsEvent.ctrlKey){
		lmb_calView('agendaDay');
		$('#calendar').fullCalendar( 'gotoDate', date );
		lmb_calSetTitle();
	}
}

// calendar - add event
function lmb_calQuickAdd(start, end, allDay, jsEvent, view, resource){
	
	if(!jsEvent.shiftKey && !jsEvent.ctrlKey && start && end){
		var title = confirm(jsvar['lng_24']);
	}

	if (!title || !start || !end){
		$('#calendar').fullCalendar( 'render' );
		return false;
	}
	
	var title = '';
	
	var resourceid = null;
	if(resource && resource.id){
		resourceid = resource.id;
	}

	$('#calendar').fullCalendar( 'gotoDate', start );
	id = lmb_calAdd(title, Math.round(start.getTime() / 1000), Math.round(end.getTime() / 1000), allDay, resourceid);
	
	//if(id){
	//	$('#calendar').fullCalendar('renderEvent',
	//	{
	//		id: id,
	//		title: title,
	//		start: start,
	//		end: end,
	//		allDay: allDay,
	//		resource: resourceid
	//	},
	//	true
	//	);
	//	$('#calendar').fullCalendar( 'render' );
	//}

	$('#calendar').fullCalendar('unselect');
	
	// open Formular
	lmb_calDetail(null, jsEvent, id);

}

// post add event
function lmb_calAdd(title, start, end, allDay, resourceid){
	$.ajax({
	  type: "POST",
	  url: "main_dyns.php",
	  async: false,
	  data: "actid=fullcalendar&action=add&gtabid="+jsvar['gtabid']+"&start="+start+"&end="+end+"&allDay="+allDay+"&title="+title+"&resource="+resourceid+"&verkn_ID="+jsvar['verkn_ID']+"&verkn_tabid="+jsvar['verkn_tabid']+"&verkn_fieldid="+jsvar['verkn_fieldid'],
	  success: function(data){result = data;}
	});
	return result;
}

// calendar - resize event
function lmb_calResize(event, dayDelta, minuteDelta){
	$.ajax({
		type: "POST",
		url: "main_dyns.php",
		data: "actid=fullcalendar&action=resize&gtabid="+jsvar['gtabid']+"&ID="+event.id+"&dayDelta="+dayDelta+"&minuteDelta="+minuteDelta
	}).done(function(data) {
		ajaxEvalScript(data);
	});
}

// calendar - drop event
function lmb_calDrop(event, dayDelta, minuteDelta, allDay, fx, ev ,ui, resource,origin_resource){

	//origin_resource = '';
	//if(event.resource){
	//	origin_resource = event.resource;
	//}else{
	//	resource = '';
	//}

	$.ajax({
		type: "POST",
		url: "main_dyns.php",
		data: "actid=fullcalendar&action=drop&gtabid="+jsvar['gtabid']+"&ID="+event.id+"&dayDelta="+dayDelta+"&minuteDelta="+minuteDelta+"&resource="+resource+"&origin_resource="+origin_resource
	}).done(function(data) {
		ajaxEvalScript(data);
		// workaround - only for multible resources
		if(resource != origin_resource){
			event.resource = resource;
			$('#calendar').fullCalendar( 'render' );
		}
	});
}


// calendar - delete event
function lmb_calDelete(evt,gtabid,ID){
	divclose();divclose();
	var title = confirm(jsvar['lng_84']);
	if(title){
	$.ajax({
	  type: "POST",
	  url: "main_dyns.php",
	  data: "actid=fullcalendar&action=delete&gtabid="+gtabid+"&ID="+ID+"&verkn_ID="+jsvar['verkn_ID']+"&verkn_tabid="+jsvar['verkn_tabid']+"&verkn_fieldid="+jsvar['verkn_fieldid'],
	  }).done(function(data){
	  	if(data.trim().substr(0,4) == 'true' && ID > 0){
	  		$('#calendar').fullCalendar( 'removeEvents', ID);
	  		if($("#lmbCalAjaxContainer").dialog()){
	  			$("#lmbCalAjaxContainer").dialog('destroy');
	  		}
	  	}else{
	  		ajaxEvalScript(data);
	  	}
	  	// reset history_fields
		document.form1.history_fields.value='';
	});
	}
}

// calendar - cut event & store in sessionStorage
function lmb_calCut(el,calEvent){
	
	divclose();divclose();
	$( ".fc-event" ).css({'opacity':'1','boxShadow':'none'});
	el.style.opacity = '0.5';
	sessionStorage.removeItem("lmb_calendar");
	calEvent.paste = 'move';
	sessionStorage.setItem("lmb_calendar", JSON.stringify(calEvent));
	
}

// calendar - copy event & store in sessionStorage
function lmb_calCopy(el,calEvent){
	
	divclose();divclose();
	$( ".fc-event" ).css({'opacity':'1','boxShadow':'none'});
	el.style.boxShadow = '4px 4px #BBBBBB';
	sessionStorage.removeItem("lmb_calendar");
	calEvent.paste = 'copy';
	sessionStorage.setItem("lmb_calendar", JSON.stringify(calEvent));
	
}



// Post edit event
function lmb_calEdit(event){
	
	var start = document.getElementById('g_'+jsvar['gtabid']+'_7').value;
	var end = document.getElementById('g_'+jsvar['gtabid']+'_8').value;
	if(document.getElementById('g_'+jsvar['gtabid']+'_12')){var allDay = document.getElementById('g_'+jsvar['gtabid']+'_12').checked;}
	if(document.getElementById('g_'+jsvar['gtabid']+'_9')){var title = document.getElementById('g_'+jsvar['gtabid']+'_9').value;}
	if(document.getElementById('g_'+jsvar['gtabid']+'_11')){var color = document.getElementById('g_'+jsvar['gtabid']+'_11').value;}
	if(document.form1.calBulk_periodStart){var calBulk_periodStart = document.form1.calBulk_periodStart.value;}
	if(document.form1.calBulk_periodEnd){var calBulk_periodEnd = document.form1.calBulk_periodEnd.value;}
	if(allDay == 'true'){allDay = 1;}else{allDay = 0;}
	
	// edit selected item
	if(activeEvent){
		if(title){activeEvent.title = title;}
		if(color){
			activeEvent.backgroundColor = color;
			activeEvent.borderColor = color;
		}
	    if(activeEvent){$('#calendar').fullCalendar('updateEvent', activeEvent);}
	    send_form();
	    //$("#lmbCalAjaxContainer").dialog('destroy');
	// create new item
	}else{
		send_form();
		$('#calendar').fullCalendar( 'render' );
	}
	
	//}else{
	//	start = limbasParseDate(start,jsvar['dateformat']);
	//	end = limbasParseDate(end,jsvar['dateformat']);
	//	if((start && end)){
	//		lmb_calQuickAdd(start, end, allDay, event, null, title, color, 1);
	//	}else{
	//		return false;
	//	}
	//	$("#lmbCalAjaxContainer").dialog('destroy');
	//}
}

// calendar - SetTitle
function lmb_calSetTitle(){
	var d = $('#calendar').fullCalendar('getDate');
	
	if(jsvar['cal_viewmode'] == 'resourceDay'){
		var title = $.fullCalendar.formatDate( d, 'dd MMMM yyyy', lmb_localobj);
	}else{
		var title = $.fullCalendar.formatDate( d, 'MMMM yyyy', lmb_localobj);
	}
	
    document.getElementById("current_date").innerHTML = title;
    document.formCalendar.startstamp.value = (d.getTime()/1000);
}
   
//  destroy calender
function lmb_calDestroy(){
	$('#calendar').fullCalendar( 'destroy' );
	allFlag = 1;
}
	
// calendar - change view
function lmb_calView(view){

	lmb_closeCalDialog();
	
	$('#calendar').fullCalendar('changeView',view);
	jsvar['cal_viewmode'] = view;
	document.formCalendar.cal_viewmode.value=view;
	divclose();
	
	$("#iconmenu td[id^='tzone']").removeClass('tabpoolItemInactive tabpoolItemActive');
	$("#iconmenu td[id^='tzone']").addClass('tabpoolItemInactive');
	
	$('#tzone_'+view).removeClass('tabpoolItemInactive');
	$('#tzone_'+view).addClass('tabpoolItemActive');
	
	lmb_calSetTitle();
	$('#calendar').css('height','');
	$('#calendar').fullCalendar('option', 'height', lmb_calSetHeight());

}


// calendar - open details
var activeEvent;
var activeDiv;
var activeDate;
var activeResource;
var action;
function lmb_calDetail(calEvent, jsEvent, id, act) {
	
	var bulk = '';
	action = act;
	divclose();
	
	if(!act){act = 'gtab_change';}
	activeEvent = null;
	if(calEvent){
		id = calEvent.id;
		activeEvent = calEvent;
		document.getElementById("eventID").value = id;
		var title = calEvent.title;
	}
	
	// cut&paste
	if(jsEvent.ctrlKey){
		lmb_calCut($(this).get(0),calEvent);
		return;
	}
	
	if(document.getElementById("bulkmenu")){
		var bulk = "<div onclick=\"lmb_openBulkMenu()\" id=\"bulkmenuLink\" style=\"position:absolute;top:5px;right:20px;cursor:pointer;z-index:9999;\">&nbsp;Terminserie&nbsp;<i class=\"lmb-icon-cus lmb-cal-span\" style=\"vertical-align:text-bottom\"></i></div><br>";
	}
	var w_ = 430;
	var h_ = 550;
	
	// reset history_fields
	document.form1.history_fields.value='';
		
	// specific formular
	if(jsvar['formid']){
		
		if(jsvar['form_dimension']){
			var size = jsvar['form_dimension'].split('x');
			var w_ = (parseFloat(size[0])+40);
			var h_ = (parseFloat(size[1])+80);
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
					$('#lmb_detailFrame').attr("src","main.php?&action="+act+"&ID="+id+"&gtabid="+jsvar['gtabid']+"&form_id="+jsvar['fformid']);
				}
			});
			return;
		}
	}
	
	$.ajax({
	  type: "GET",
	  url: "main_dyns.php",
	  async: false,
	  dataType: "html",
	  data: "actid=fullcalendar&action=details&gtabid="+jsvar['gtabid']+"&form_id="+jsvar['fformid']+"&ID="+id+"&act="+act,
	  success: function(data){
	  	if(id){bulk = '';}
	  	document.getElementById("lmbCalAjaxContainer").innerHTML = bulk+'<div class="calenderBulk">'+data+'</div>';
	  	ajaxEvalScript(data);
	  }
	});
	
	if(!jsvar['formid']){h_ = ($("#lmbCalAjaxContainer").height()+80);}
	
	if($("#lmbCalAjaxContainer").hasClass('ui-dialog-content')){
		$("#lmbCalAjaxContainer").dialog('open');
	}else{
		$("#lmbCalAjaxContainer").css({'position':'relative','left':'0','top':'0'}).dialog({
			title: title,
			width: w_,
			height: h_,
			resizable: true,
			modal: true,
			zIndex: 10,
			close:lmb_closeCalDialog,
			appendTo: "#form1"
			//open: function( event, ui ) {
			//	$(this).parent().appendTo("form[name='form1']")
			//}
		});
	}

}

// set calendar height
function lmb_calSetHeight(){    
    if($('#cal_searchFrame').css('display') != 'none') {
        // only count height of calendar
        $('#cal_searchFrame').hide();
        lmb_setAutoHeightCalendar(document.getElementById("calendar"), 3 /* somehow there are 3px overflow */);
        $('#cal_searchFrame').show();
    }else{
        lmb_setAutoHeightCalendar(document.getElementById("calendar"), 3 /* somehow there are 3px overflow */);
    }    
}

function lmb_closeCalDialog(){
	if($('#lmbCalAjaxContainer').prev().hasClass('bulkContainer')){$('#lmbCalAjaxContainer').prev().remove();}
	if($("#lmbCalAjaxContainer").hasClass('ui-dialog-content')){$("#lmbCalAjaxContainer").dialog('close');}
	document.form1.history_fields.value='';
}

// use datepicker
function lmb_pickerSelected(date) {
	
	datepart = date.split('-');
	
	var y = datepart[0];
	var m = (datepart[1]-1);
	var d = datepart[2];
        
	$('#calendar').fullCalendar( 'render' );
	$('#calendar').fullCalendar('gotoDate',y,m,d);
	lmb_calSetTitle();
	
}

// open formular  
function lmb_popupDetails(event,gtabid,ID) {
	divclose();
	details = open("main.php?&action=gtab_change&ID=" + ID + "&gtabid=" + gtabid + "" ,"details","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=650,height=600");
}


// search frame
var visible = 0;
function lmb_calSearchFrame() {
	divclose();

	visible = 0;
	if($('#cal_searchFrame').css('display') == 'none'){
		$('#cal_searchFrameTD').show();
		visible = 1;
	}
	
	$('#cal_searchFrame').animate({width:'toggle'},350, function() {
		if(!visible){
			$('#cal_searchFrameTD').hide();
		}
		$('#calendar').fullCalendar( 'render' );
	});
	
	ajaxGet(null,'main_dyns.php','layoutSettings&frame=cal&size='+visible,null);
}

// open bulk menu
function lmb_openBulkMenu(){
	var cont = $('#bulkmenu').html().replace('bulk_ts','bulk_terminserie');
	cont = cont.replace(/calBulk_periodStartT/g, "calBulk_periodStart");
	cont = cont.replace(/calBulk_periodEndT/g, "calBulk_periodEnd");
	$('#lmbCalAjaxContainer').before(cont);
	$('#bulkmenuLink').remove();
	if(document.getElementById('g_'+jsvar['gtabid']+'_7')){document.getElementById('g_'+jsvar['gtabid']+'_7').value='';document.getElementById('g_'+jsvar['gtabid']+'_7').disabled = true;}
	if(document.getElementById('g_'+jsvar['gtabid']+'_8')){document.getElementById('g_'+jsvar['gtabid']+'_8').value='';document.getElementById('g_'+jsvar['gtabid']+'_8').disabled = true;}
}

// duplicate dates for bulk
function lmb_bulkAddTermin(){
	
	var i = 0;
	var e = 1;
	$('#bulk_terminserie').append('<table width="400px">' + $('#bulk_terminserie table:first').html() + '</table>');
	$('#bulk_terminserie input').each(function( index ) {
		$(this).attr('name',$(this).attr('name').substring(0,16) + '['+i+']');
		if(e == 5){
			i++;
			e = 0;
		}
		e++;
	});
}


function lmb_validateTime(el,type){
	if(type == 'h'){
		if(isNaN(el.value) || el.value > 23){
			el.value = '';
		}
	}else if(type == 'm'){
		if(isNaN(el.value) || el.value > 59){
			el.value = '00';
		}
	}else if(type == 'd'){
		if(isNaN(el.value)){
			el.value = '';
		}	
	}
}




function lmb_calChangeSettings(setting,val){
        $('[name=cal_' + setting + ']').val(val);
	
	lmb_calReload(true);
	
	//$('#calendar').fullCalendar('option','slotMinutes', 120);
	//$('#calendar').fullCalendar( setting, val);	
	//$('#calendar').fullCalendar( 'render' );
}


//calender reload
function lmb_calReload(fullreload,detail_search){
	
	divclose();
	if($("#lmbCalAjaxContainer").hasClass('ui-dialog-content')){$("#lmbCalAjaxContainer").dialog('close');}
	if($("#lmb_eventDetailFrame").hasClass('ui-dialog-content')){$("#lmb_eventDetailFrame").dialog('close');}
	
	if(!detail_search){
		if($("#lmbCalAjaxContainer").hasClass('ui-dialog-content')){$("#lmbCalAjaxContainer").dialog('destroy');}
		$("#lmbCalAjaxContainer").html('');
	}
	
	if(fullreload){
		var d = $('#calendar').fullCalendar('getDate');
		lmb_calDestroy();
		lmb_calInit();
		$('#calendar').fullCalendar( 'gotoDate', d );
		lmb_calSetTitle();
	}else{
		$('#calendar').fullCalendar( 'refetchEvents' );
	}
	
}


// override send_form from gtab_change.js - delay 0,5 sec
//function send_form(formid,ajax,need,calBulk_precalc) {
//	setTimeout(function (){send_form_delay(formid,ajax,need,calBulk_precalc)}, 500 );
//	//eval("setTimeout(\"send_form_delay('"+formid+"','"+ajax+"','"+need+"','"+calBulk_precalc+"')\",500);");
//}

// override send_form from gtab_change.js
function send_form(formid,ajax,need,wclose,calBulk_precalc) {

	var needok = needfield(need);
	if(typeof calBulk_precalc == 'undefined'){calBulk_precalc = '';}
	set_focus();
	
	if(document.getElementById('g_'+jsvar['gtabid']+'_7')){var check = 1;var start = limbasParseDate($('#g_'+jsvar['gtabid']+'_7').val(),jsvar['dateformat']);}
	if(document.getElementById('g_'+jsvar['gtabid']+'_8')){var check = 1;var end = limbasParseDate($('#g_'+jsvar['gtabid']+'_8').val(),jsvar['dateformat']);}
	if(document.getElementById('calBulk_periodStart')){var pcheck = 1;var pstart = limbasParseDate($('#calBulk_periodStart').val(),jsvar['dateformat']);}
	if(document.getElementById('calBulk_periodEnd')){var pcheck = 1;var pend = limbasParseDate($('#calBulk_periodEnd').val(),jsvar['dateformat']);}

	if(pcheck){
		if(pstart > pend){alert('end-date before start-date!');return;}
		if(!pstart || !pend){alert('start-date and end-date needed!');return;}
	}else if(check){
		if(start > end){alert('end-date before start-date!');return;}
		if(!start || !end){alert('start-date and end-date needed!');return;}
	}

	if(needok){
		alert(jsvar["lng_1509"]+"\n\n"+needok);
	}else{
		dynfunc = function(result){send_formPost(result,ajax);};
		ajaxGet(null,'main_dyns.php','fullcalendar&action=saveDetails&calBulk_precalc='+calBulk_precalc,null,'dynfunc','form1',null,1);
		if(calBulk_precalc == 'create' || !document.getElementById("bulk_terminserie")){
			if(!ajax){
				lmb_calReload();
			}
		}
		return true;
	}
}

function lmb_extsearchclear(){
	$('#extsearchtab input, #extsearchtab select').filter('.gtabHeaderInputINP').val('');
	$("[name='filter_reset']").val('1');
	lmb_calReload(1);
	$("[name='filter_reset']").val('');
}


// Ajax post formelements output
function send_formPost(result,ajax){
	if(!ajax && activeEvent && $("#lmbCalAjaxContainer").hasClass('ui-dialog-content')){
		$("#lmbCalAjaxContainer").dialog('destroy');
	}
	ajaxEvalScript(result);
}

function LmGs_sendForm(reset){
	if(reset){
		lmb_extsearchclear();
	}else{
		// sync searchtab with detail searchform
		$('#extsearchtab input, #extsearchtab select').filter('.gtabHeaderInputINP').each(function() {
			var n = $(this).attr('name');
			if(document.form11.elements[n]){
				$(this).val(document.form11.elements[n].value);
			}
		});

		lmb_calReload(1,1);
	}
}




// fullcalendar extension functions for repeating events
(function($) {

    var fullCalendarOrg = $.fn.fullCalendar;
	var rEvents = [];
	var subIds = {};
	var holidays = [];

	$.fn.fullCalendar = function(options) {
		var args;
        if(typeof options === "object") {
            options = $.extend(true, $.fn.fullCalendar.recurringDefaults, options );
        }
		else if (typeof options === "string") {
			args = Array.prototype.slice.call(arguments, 1);
			if ('destroy' == options) {
				rEvents = [];
				subIds = {};
				holidays = [];
			}
			else if ('addRecurrent' == options) {
				addRecurrent(args[0]);
				return;
			}
			else if ('getRecurrents' == options) {
				return getRecurrents(args[0], args[1]);
			}
			else if ('addHoliday' == options) {
				addHoliday(args[0]);
				return;
			}
/* 			else if ('getHoliday' == options) {
				return isHoliday(args[0]);
			}
 */			else if ('renderHolidays' == options) {
				return renderHolidays(args[0]);
			}
			else if ('getHolidays' == options) {
				return getHolidays(args[0], args[1]);
			}
		}

        args = Array.prototype.slice.call(arguments, 0);
        return fullCalendarOrg.apply(this, args);
		
		function addRecurrent(event) {
			if (typeof event.repeat != 'number') {
				event.repeat = parseInt(event.repeat);
			}
			rEvents.push(event);
		}
		
		function getRecurrents(start, end) {
			var events = [];
			$(rEvents).each(function() {
				var event = $.extend(true, {startDate: parseDate(this.start), endDate: (undefined == this.end) ? this.end : parseDate(this.end), repeatEndDate: (undefined == this.repeatEnd) ? this.repeatEnd : parseDate(this.repeatEnd)}, this);
				if (undefined == subIds[this.id]) {
					subIds[this.id] = 1;
				}
				while (event.startDate < start) {
					event = nextRecurrent(event, start, end);
				}
				while (event.startDate < end && (undefined == event.repeatEndDate || (event.startDate < event.repeatEndDate))) {
					event.id = this.id + '.' + subIds[this.id];
					events.push(event);
					event = nextRecurrent(event, start, end);
					subIds[this.id]++;
				}
			});

			return events;
		}
		
		function addHoliday(event) {
			event = $.extend(true, {startDate: parseDate(event.start), endDate: (undefined == event.end) ? parseDate(event.start) : parseDate(event.end)}, event);
			holidays.push(event);
		}
		
		function getHoliday(date) {
			var retval = null;
			$(holidays).each(function() {
				if (this.startDate <= date && date <= this.endDate) {
					retval = this;
					return false;
				}
			});
			return retval;
		}

		function renderHolidays(view) {
			var aDay = 24*60*60*1000;
			var options = view.calendar.options;
			var date = view.visStart;
			var end = view.visEnd;
			for (; date<end;) {
				var holiday = getHoliday(date);
				var isWeekend = 0 == date.getDay() || 6 == date.getDay();
				var element = null;
				if (null != holiday || (undefined != options.weekendAsHoliday && options.weekendAsHoliday && isWeekend)) {
					if ('month' == view.name) {
						element = $("td[data-date=" + $.fullCalendar.formatDate(date, 'yyyy-MM-dd') + "]");
						if (null != holiday) {
							if (!element.hasClass('fc-today')) {
								if (holiday.background) {
									element.css('background', holiday.background);
								}
								else if ( holiday.color) {
									element.css('background-color', holiday.color);
								}
							}
							if (undefined != options.showHolidayName && options.showHolidayName) {
								element.find("div:first").css('position', 'relative')
								.append($('<div class="fcr-holiday-title-M">').html(holiday.title));
							}
						}
						else if (!element.hasClass('fc-today')) {
							element.css('background-color', options.weekendHolidayColor);
						}
						if (options.holidayRender) {
							options.holidayRender.apply(
								view.calendar, [
									date,
									holiday,
									element,
									isWeekend
								]
							);
						}
					}
					else if ('agendaWeek' == view.name || 'basicWeek' == view.name) {
						if ('agendaWeek' == view.name) {
							var selector = "table.fc-agenda-days td.fc-" + $.fullCalendar.formatDate(date, 'ddd');
							element = $(selector.toLowerCase());
						}
						else {
							element = $("td[data-date=" + $.fullCalendar.formatDate(date, 'yyyy-MM-dd') + "]");
						}
						if (null != holiday) {
							if (holiday.background) {
								element.css('background', holiday.background);
							}
							else if ( holiday.color) {
								element.css('background-color', holiday.color);
							}
							if (undefined != options.showHolidayName && options.showHolidayName) {
								if ('agendaWeek' == view.name) {
									element.find("div:first").css('position', 'relative')
									.append($('<div class="fcr-holiday-title-aW">').html(holiday.title));
								}
								else {
									element.find("div:first").css('position', 'relative')
									.append($('<div class="fcr-holiday-title-bW">').html(holiday.title));
								}
							}
						}
						else {
							element.css('background-color', options.weekendHolidayColor);
						}
					}
					else if ('agendaDay' == view.name || 'basicDay' == view.name) {
						if ('agendaDay' == view.name) {
							element = $("table.fc-agenda-days td:first");
						}
						else {
							element = $("td[data-date=" + $.fullCalendar.formatDate(date, 'yyyy-MM-dd') + "]");
						}
						if (null != holiday) {
							if (holiday.background) {
								element.css('background', holiday.background);
							}
							else if ( holiday.color) {
								element.css('background-color', holiday.color);
							}
							if (undefined != options.showHolidayName && options.showHolidayName) {
								if ('agendaDay' == view.name) {
									element = $("table.fc-agenda-days thead th:eq(1)");
									element.append(' <span class="fcr-holiday-title-aD">(' + holiday.title + ')</span>');
								}
								else {
									element = $("th.fc-day-header");
									element.append(' <span class="fcr-holiday-title-bD">(' + holiday.title + ')</span>');
								}
							}
						}
						else {
							element.css('background-color', options.weekendHolidayColor);
						}
//						if (options['holidayRender']) {
//							options['holidayRender'].apply(
//								view.calendar, [
//									date,
//									holiday,
//									element,
//									isWeekend
//								]
//							);
//						}
					}
				}
				date = new Date(date.getTime() + aDay);
			}
		}
		
		function getHolidays(start, end) {
			var events = [];
			$(holidays).each(function() {
				//var event = $.extend(true, {startDate: parseDate(this.start), endDate: (undefined == this.end) ? parseDate(this.start) : parseDate(this.end), holiday: 1}, this);
				//console.log(this.startDate + "; " + end);
				if (this.startDate >= start && this.endDate <= end) {
					events.push(event);
				}
			});

			return events;
		}
		
		function parseDate(aDate) {
			var retval = $.fullCalendar.parseDate(aDate);
			if (null === retval) {
				//console.log("fullCalendar.parseDate not working: " + aDate);
				retval = new Date(aDate.substr(0, 4), parseInt(aDate.substr(5, 2))-1, aDate.substr(8, 2), aDate.substr(11, 2), aDate.substr(14, 2), aDate.substr(17, 2), 0);
			}
			return retval;
		}
		
		function nextRecurrent(prevEvent, start, end) {
			var event = $.extend(true, {}, prevEvent);
			////console.log("nextRecurrent " + event.id + "; " + event.startDate + "; " + start + ", " + end);
			var isset = false;
			var aDate;
			if (event.startDate < start) {
				switch (event.repeat) {
					case 1: // daily
						aDate = new Date(start.getTime());
						aDate.setHours(event.startDate.getHours());
						aDate.setMinutes(event.startDate.getMinutes());
						aDate.setSeconds(event.startDate.getSeconds());
						event.end = event.endDate = new Date(aDate.getTime() + (event.endDate.getTime() - event.startDate.getTime()));
						event.start = event.startDate = new Date(aDate.getTime());
						//console.log("daily start: " + event.startDate + ", " + event.endDate);
						isset = true;
						break;
					case 44: // monthly
						aDate = new Date(event.startDate.getTime());
						aDate.setFullYear(start.getFullYear());
						aDate.setDate(start.getDate());
						event.end = event.endDate = new Date(aDate.getTime() + (event.endDate.getTime() - event.startDate.getTime()));
						event.start = event.startDate = new Date(aDate.getTime());
						isset = true;
						break;
				}
			}

			if (!isset) {
				switch (event.repeat) {
					case 1: // daily
						event.start = event.startDate = new Date(event.startDate.getTime() + 86400000);
						event.end = event.endDate = new Date(event.endDate.getTime() + 86400000);
						break;
					case 2: // weekly
						event.start = event.startDate = new Date(event.startDate.getTime() + (7 * 86400000));
						event.end = event.endDate = new Date(event.endDate.getTime() + (7 * 86400000));
						break;
					case 3: // every 2 weeks
						event.start = event.startDate = new Date(event.startDate.getTime() + (14 * 86400000));
						event.end = event.endDate = new Date(event.endDate.getTime() + (14 * 86400000));
						break;
					case 4: // monthly
						aDate = new Date(event.startDate.getTime());
						aDate.setMonth(event.startDate.getMonth()+1);
						if (aDate.getMonth() -1 > event.startDate.getMonth()) {
							//console.log("!!! " + aDate + ", " + event.startDate);
							aDate = new Date(event.startDate.getTime());
							aDate.setMonth(event.startDate.getMonth()+2);
							//console.log("!!! " + aDate + ", " + event.startDate);
						}
						event.start = event.startDate = aDate;
						//new Date(event.startDate.getFullYear(), event.startDate.getMonth()+1, event.startDate.getDate(), event.startDate.getHours(), event.startDate.getMinutes(), event.startDate.getSeconds());
						aDate = new Date(event.endDate.getTime());
						aDate.setMonth(event.endDate.getMonth()+1);
						if (aDate.getMonth() -1 > event.endDate.getMonth()) {
							//console.log("!!! " + aDate + ", " + event.endDate);
							aDate = new Date(event.endDate.getTime());
							aDate.setMonth(event.endDate.getMonth()+2);
							//console.log("!!! " + aDate + ", " + event.endDate);
						}
						event.end = event.endDate = aDate;
						break;
					case 5: // yearly
						aDate = new Date(event.startDate.getTime());
						aDate.setYear(event.startDate.getFullYear()+1);
						event.start = event.startDate = aDate;
						aDate = new Date(event.endDate.getTime());
						aDate.setYear(event.endDate.getFullYear()+1);
						event.end = event.endDate = aDate;
						break;
				}
			}
			return event;
		}
    };
	$.fn.fullCalendar.recurringDefaults = {
	    	weekendAsHoliday: false,
	    	weekendHolidayColor: 'lightgrey',
	    	showHolidayName: false
	};



})(jQuery);
