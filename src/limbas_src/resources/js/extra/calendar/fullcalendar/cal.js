/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


var activeEvent;
var activeDiv;
var activeDate;
var activeResource;
var action;
let $calendar;
let calendar;
let $lmbCalAjaxContainer;

function lmb_calInit() {
    sessionStorage.removeItem("lmb_calendar");
    var formdata = lmb_getFormData(Array('formCalendar', 'form11'));
    $calendar = document.getElementById("calendar");
    $calendar.style.visibility = "hidden";

    $("#formCalendar input[name^='cal_']").each(function (index) {
        var sname = $(this).attr('name');
        var val = $(this).val();
        if (val) {
            if (!isNaN(val)) {
                val = parseInt(val);
            }
            jsvar[sname] = val;
        }
    });

    let customButtons = {
        listDesc: {
            text: "Listenansicht",
            icon: " fa-solid fa-list"
        },
        gridDesc: {
            text: "Rasteransicht",
            icon: " fa-solid fa-calendar-days"
        },
        jumpToDate: {},
        nextIc: {
            icon: " fa-solid fa-chevron-right",
            click: function () {
                calendar.next();
            }
        },
        prevIc: {
            icon: " fa-solid fa-chevron-left",
            click: function () {
                calendar.prev();
            }
        },
        nextYearIc: {
            icon: " fa-solid fa-forward",
            click: function () {
                calendar.nextYear();
            }
        },
        prevYearIc: {
            icon: " fa-solid fa-backward",
            click: function () {
                calendar.prevYear();
            }
        },
        create: {
            icon: " lmb-icon lmb-plus-square",
            text: jsvar['buttons']['create']['title'],
            click: function () {
                lmb_calNewEvent(event);
            }
        },
        search: {
            icon: " lmb-icon lmb-page-find",
            text: jsvar['buttons']['search']['title'],
            click: function () {
                limbasDetailSearch(event, this, jsvar['gtabid'], '', 'lmbCalAjaxContainer');
            }
        },
        refresh: {
            icon: " lmb-icon lmb-refresh",
            text: jsvar['buttons']['refresh']['title'],
            click: () => {
                calendar.refetchEvents()
            }
        },
        dayGridMonth: {
            icon: " fa-solid fa-calendar",
            click: function () {
                lmb_calView('dayGridMonth');
            }
        },
        timeGridWeek: {
            icon: " fa-solid fa-calendar-week",
            click: function () {
                lmb_calView('timeGridWeek');
            }
        },
        timeGridDay: {
            icon: " fa-solid fa-calendar-day",
            click: function () {
                lmb_calView('timeGridDay');
            }
        },
        listMonth: {
            icon: " fa-solid fa-calendar",
            click: function () {
                lmb_calView('listMonth');
            }
        },
        listWeek: {
            icon: " fa-solid fa-calendar-week",
            click: function () {
                lmb_calView('listWeek');
            }
        },
        listDay: {
            icon: " fa-solid fa-calendar-day",
            click: function () {
                lmb_calView('listDay');
            }
        },
    }
    let displayedButtons = '';
    if (jsvar['buttons']['settings']) {
        customButtons.settings = {
            icon: " lmb-icon lmb-cog",
            text: jsvar['buttons']['settings']['title'],
            click: function () {
                limbasDivShow(this, '', 'viewmenu');
            }
        }
        displayedButtons += 'settings,';
    }
    displayedButtons += 'create,search,refresh'
    if (jsvar['buttons']['extras']) {
        customButtons.extras = {
            icon: " lmb-icon lmb-puzzle-piece",
            text: jsvar['buttons']['extras']['title'],
            click: function () {
                limbasDivShow(this, '', 'extrasmenu');
            }
        }
        displayedButtons += ',extras';
    }

    let events = {
        url: 'main_dyns.php?actid=fullcalendar&action=reload&reload=1',
        method: 'POST',
        extraParams: formdata
    };


    calendar = new FullCalendar.Calendar($calendar, {
        locale: 'de',
        initialView: jsvar['cal_viewmode'],
        editable: jsvar['cal_editable'],
        droppable: true,
        selectable: jsvar['cal_selectable'],
        eventResizableFromStart: true,
        eventClick: lmb_calDetail,
        eventDrop: lmb_calDrop,
        eventResize: lmb_calResize,
        weekends: jsvar['cal_weekends'],
        firstDay: jsvar['cal_firstDay'],
        slotMinTime: jsvar['cal_minTime'],
        slotMaxTime: jsvar['cal_maxTime'],
        slotDuration: jsvar['cal_slotMinutes'],
        weekNumbers: jsvar['cal_weekNumbers'],
        weekText: jsvar['cal_weekNumberTitle'],
        defaultAllDay: jsvar['cal_defaultAllDay'],
        nowIndicator: jsvar['cal_nowIndicator'],
        eventDidMount: lmb_calEventRender,
        dayCellDidMount: lmb_calDayRender,
        select: lmb_calQuickAdd,
        contentHeight: '80vh',
        themeSystem: 'bootstrap5',
        views: {},
        headerToolbar: {
            left: displayedButtons + ' gridDesc,dayGridMonth,timeGridWeek,timeGridDay listDesc,listMonth,listWeek,listDay',
            center: 'title',
            right: 'jumpToDate today prevIc,nextIc prevYearIc,nextYearIc'
        },
        buttonText: {
            today: 'today',
            prev: 'prev',
            next: 'next',
            prevYear: 'prevYear',
            nextYear: 'nextYear'
        },
        customButtons: customButtons,
        footerToolbar: {
            left: '',
            center: '',
            right: ''
        },
        events: events
    });

    $lmbCalAjaxContainer = $("#lmbCalAjaxContainer");

    calendar.render();
    $("button[class^='fc-'][class*='Desc-button']").prop('disabled', true);
    $("span.fc-icon").removeClass('fc-icon'); // remove fullcalendar icons messing with fa icons
    $calendar.style.visibility = "visible";

    const $fcJumpToDateButton = $('.fc-jumpToDate-button');

    // workaround bootstrap icons
    $($calendar).find("span[class*='bi']").removeClass('bi-').removeClass('bi');

    $fcJumpToDateButton.addClass('p-1');
    $fcJumpToDateButton.html(`
                            <div>
                                <input type="date" id="input-jumptodate" />
                            </div>
                        `);
    const $inputJumpToDate = $('#input-jumptodate');

    const today = new Date();
    const dd = String(today.getDate()).padStart(2, '0');
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const yyyy = today.getFullYear();
    const formattedDate = yyyy + '-' + mm + '-' + dd;
    $inputJumpToDate.val(formattedDate);

    $inputJumpToDate.on('change', function () {
        const date = $inputJumpToDate.val();
        if (date) {
            calendar.gotoDate(date);
        }
    });
}

function sendFullCalendarAction(data) {
    data['actid'] = 'fullcalendar';

    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'main_dyns.php',
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


var activ_menu = null;

//  destroy calender
function lmb_calDestroy() {
    calendar.destroy();
}

function divclose() {
    if (!activ_menu) {
        limbasDivClose('');
    }
    activ_menu = 0;
}

//used from gtab_change.dao - "save and close" funktion
function lmb_closeIframeDialog() {
    if ($('#lmb_eventDetailFrame').hasClass('ui-dialog-content')) {
        $('#lmb_eventDetailFrame').dialog('destroy');
        lmb_calReload();
    }
}

// render events with icons and more
function lmb_calEventRender(arg) {
    let extendedProps = arg.event.extendedProps;

    let evtBeforeEvent = extendedProps.evtBeforeEvent;
    let evtAfterEvent = extendedProps.evtAfterEvent;
    let evtTextColor = extendedProps.evtTextColor;
    let evtColor = extendedProps.evtColor;
    let evtborderColor = extendedProps.evtborderColor;
    let evtBackgroundColor = extendedProps.evtBackgroundColor;
    let evtTitle = extendedProps.evtTitle;

    // show event icons before
    if (evtBeforeEvent) {
        if (calendar.view.type.startsWith('list')) {
            $(arg.el.firstChild).prepend(evtBeforeEvent);
        } else {
            $(arg.el).prepend(evtBeforeEvent);
            $(arg.el).children(":first").css('float', 'left');
        }
    }

    // title / tooltip
    if (evtTitle) {
        //$(arg.el).prop('title', evtTitle);
        arg.event.title = evtTitle;
    }


    // background color
    if (evtBackgroundColor) {
        //$(arg.el).css("background-color", evtBackgroundColor);
        arg.event.backgroundColor = evtBackgroundColor
    }


    // text color
    if (evtTextColor) {
        //$(arg.el).css("color", evtTextColor);
        arg.event.textColor = evtTextColor
    }

    // border color
    if (evtborderColor) {
        //$(arg.el).css("border", '1px solid ' + evtborderColor);
        arg.event.borderColor = evtborderColor;
    }

    const $el = $(arg.el);
    $el.find('.fc-event-title').html(arg.event.title.replace(/\n/g, '<br>'));

    // contextmenu
    $el.on('contextmenu', function (event) {
        lmb_calShowContext(event, arg, null);
        return false;
    });
}

function lmb_localobj() {
    this.monthNames = jsvar['jqmonth'];
}

function lmb_calDayRender(arg) {

    arg.el.addEventListener("contextmenu", (jsEvent) => {
        jsEvent.preventDefault();
        lmb_calShowContext(event, arg, arg.date);
    })

}

// get and show context window
function lmb_calShowContext(event, eventInfo, date) {
    divclose();
    divclose();

    if (date) {
        if (!sessionStorage.lmb_calendar) {
            return;
        }
        limbasDivShow('', event, 'lmbAjaxContextPaste');
        activeDate = date;
    } else {
        limbasDivShow(eventInfo.el, '', 'lmbAjaxContextmenu', '50x-5', 1);
        activeEvent = eventInfo;
    }

    // no divclose trigger on right click
    activ_menu = 0;
}


// paste
function lmb_calPaste(date) {

    divclose();

    // get object from session
    let event_from_session = sessionStorage.lmb_calendar; // get active event from storage
    if (!event_from_session) {
        return;
    }

    event_from_session = JSON.parse(event_from_session);
    if (!event_from_session) {
        return;
    }
    let id = event_from_session.id;
    let el = event_from_session.el;
    let paste = event_from_session.paste;
    let cevent = calendar.getEventById(id); // get Event with ID
    let offsetDate = date;
    offsetDate.setHours(cevent.start.getHours());

    if (confirm(jsvar['lng_1441'] + ' ' + jsvar['lng_1615'] + ' ? \n' + cevent.title)) {

        if (paste == 'move') {
            // try to move event
            if (cevent[0] && cevent[0].end) {
                cevent = cevent[0];

                // length of event
                var len = cevent.end.getTime() / 1000 - cevent.start.getTime() / 1000;

                cevent.start = offsetDate;
                cevent.end = offsetDate.getTime() / 1000 + len;

                sessionStorage.removeItem("lmb_calendar");
            }
        } else if (paste == 'copy') {


        } else {
            sessionStorage.removeItem("lmb_calendar");
            return;
        }

        $.ajax({
            type: "GET",
            url: "main_dyns.php",
            data: "actid=fullcalendar&action=" + paste + "&gtabid=" + jsvar['gtabid'] + "&ID=" + id + "&stamp=" + (offsetDate.getTime() / 1000),
            success: function () {
                if (!len) {
                    lmb_calReload();
                }
            }
        });

        sendFullCalendarAction({
            action: paste,
            gtabid: jsvar['gtabid'],
            ID: id,
            stamp: offsetDate.getTime() / 1000,
        }).then(function (data) {
            if (data.success) {
                if (!len) {
                    lmb_calReload();
                }
            } else {
                lmbShowErrorMsg('Action failed.');
            }
        }).catch(function () {
            lmbShowErrorMsg('Action failed.');
        });


    }


}


// calendar - dayClick
function lmb_calGotoDay(dateInfo) {
    divclose();

    calendar.gotoDate(dateInfo.date);
    lmb_calView('timeGridDay');
}

// calendar - add event
// function lmb_calQuickAdd(start, end, allDay, jsEvent, view, resource){
function lmb_calQuickAdd(selectionInfo) {
    let title;
    if (!selectionInfo.jsEvent.shiftKey && !selectionInfo.jsEvent.ctrlKey && selectionInfo.start && selectionInfo.end) {
        title = confirm(jsvar['lng_24']);
    }

    if (!title || !selectionInfo.start || !selectionInfo.end) {
        calendar.render();
        return false;
    }

    title = '';

    let resourceid = null;
    // disable resources - todo
    //if (selectionInfo.resource && selectionInfo.resource.id) {
    //resourceid = selectionInfo.resource.id;
    //}

    //calendar.gotoDate(selectionInfo.start);

    // fullcalendar has exclusive end for allday events, we want inclusive, so we subtract one day
    let endStamp = Math.round(selectionInfo.end.getTime() / 1000);
    if(selectionInfo.allDay) {
        endStamp -= 86400;
    }
    lmb_calAdd(title, Math.round(selectionInfo.start.getTime() / 1000), endStamp, selectionInfo.allDay, resourceid);

    calendar.unselect();

    // open Formular
    lmb_calReload();
}

// post add event
function lmb_calAdd(title, start, end, allDay) {
    sendFullCalendarAction({
        action: 'add',
        gtabid: jsvar['gtabid'],
        start: start,
        end: end,
        allDay: allDay,
        title: title,
        verkn_ID: jsvar['verkn_ID'],
        verkn_tabid: jsvar['verkn_tabid'],
        verkn_fieldid: jsvar['verkn_fieldid']
    }).then(function (data) {
        if (data.success) {
            lmbShowSuccessMsg('Update successful.')
        } else {
            lmbShowErrorMsg('Update failed.');
        }
    }).catch(function () {
        lmbShowErrorMsg('Update failed.');
    });
}

// calendar - resize event
function lmb_calResize(eventInfo) {
    lmb_calDrop(eventInfo);
}

// calendar - drop event
function lmb_calDrop(eventInfo) {
    let formatFunction = (date) => FullCalendar.formatDate(date, {
        month: '2-digit',
        year: 'numeric',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        locale: 'de'
    }).replace(",", "");
    let newStartDate = formatFunction(eventInfo.event.start);
    let newEndDate;
    if (!eventInfo.event.end) {
        let end = new Date();
        end.setTime(eventInfo.event.start.getTime() + (60 * 60 * 1000));
        newEndDate = formatFunction(end);
    } else {
        if (eventInfo.event.allDay) {
            // fullcalendar has exclusive end for allday events, we want inclusive, so we subtract one day
            newEndDate = formatFunction(eventInfo.event.end - 86400 * 1000);
        } else {
            newEndDate = formatFunction(eventInfo.event.end);
        }
    }

    sendFullCalendarAction({
        action: 'drop',
        gtabid: jsvar['gtabid'],
        ID: eventInfo.event.id,
        newStartDate: newStartDate,
        newEndDate: newEndDate,
        allDay: eventInfo.event.allDay
    }).then(function (data) {
        if (!data.success) {
            lmbShowErrorMsg('Update failed.');
        }
    }).catch(function () {
        lmbShowErrorMsg('Update failed.');
    });
}


// calendar - delete event
function lmb_calDelete(evt, gtabid, ID) {
    divclose();
    divclose();
    var title = confirm(jsvar['lng_84']);
    if (title) {
        sendFullCalendarAction({
            action: 'delete',
            gtabid: gtabid,
            ID: ID,
            verkn_ID: jsvar['verkn_ID'],
            verkn_tabid: jsvar['verkn_tabid'],
            verkn_fieldid: jsvar['verkn_fieldid']
        }).then(function (data) {
            if (data.success && ID > 0) {
                calendar.getEventById(ID).remove();

                if ($lmbCalAjaxContainer.dialog()) {
                    $lmbCalAjaxContainer.dialog('destroy');
                }
                lmbShowSuccessMsg('Delete successful.')
            } else {
                lmbShowErrorMsg('Delete failed.');
            }
        }).catch(function () {
            lmbShowErrorMsg('Delete failed.');
        }).finally(function () {
            document.form1.history_fields.value = '';
        });
    }
}

// calendar - cut event & store in sessionStorage
function lmb_calMoveCopy(action) {

    divclose();
    el = activeEvent.el;
    $(".fc-event").css({'opacity': '1', 'boxShadow': 'none'});
    el.style.opacity = '0.5';

    // create new object so save in session
    sessionStorage.removeItem("lmb_calendar");
    activeEvent_ = new Object();
    activeEvent_.id = activeEvent.event.id;
    activeEvent_.el = activeEvent.el;
    activeEvent_.paste = action;

    sessionStorage.setItem("lmb_calendar", JSON.stringify(activeEvent_));

}


// Post edit event
function lmb_calEdit(event) {
    var start = document.getElementById('g_' + jsvar['gtabid'] + '_7').value;
    var end = document.getElementById('g_' + jsvar['gtabid'] + '_8').value;
    if (document.getElementById('g_' + jsvar['gtabid'] + '_12')) {
        var allDay = document.getElementById('g_' + jsvar['gtabid'] + '_12').checked;
    }
    if (document.getElementById('g_' + jsvar['gtabid'] + '_9')) {
        var title = document.getElementById('g_' + jsvar['gtabid'] + '_9').value;
    }
    if (document.getElementById('g_' + jsvar['gtabid'] + '_11')) {
        var color = document.getElementById('g_' + jsvar['gtabid'] + '_11').value;
    }
    if (document.form1.calBulk_periodStart) {
        var calBulk_periodStart = document.form1.calBulk_periodStart.value;
    }
    if (document.form1.calBulk_periodEnd) {
        var calBulk_periodEnd = document.form1.calBulk_periodEnd.value;
    }


    // edit selected item
    if (activeEvent) {
        activeEvent.setAllDay(allDay);
        if (title) {
            activeEvent.setProp('title', title);
        }
        if (color) {
            activeEvent.setProp('backgroundColor', color)
        }
        calendar.title = title;


        send_form();
        // create new item
    } else {
        send_form();
        calendar.render();
    }
}

// calendar - change view
function lmb_calView(view) {
    calendar.changeView(view);

    jsvar['cal_viewmode'] = view;
    document.formCalendar.cal_viewmode.value = view;
    divclose();
}


function lmb_calNewEvent(event) {

    let eventInfo = {jsEvent: event, action: 'gtab_neu'};
    lmb_calDetail(eventInfo);

}


// calendar - open details

//function lmb_calDetail(calEvent, jsEvent, id, act) {
function lmb_calDetail(eventInfo) {
    var bulk = '';
    divclose();

    let calEvent = eventInfo.event;
    let jsEvent = eventInfo.jsEvent;
    action = eventInfo.action;
    let id;
    if (!eventInfo.event) {
        id = 0;
    } else {
        id = eventInfo.event.id;
    }


    if (!action) {
        action = 'gtab_change';
    }
    activeEvent = null;
    if (calEvent) {
        activeEvent = calEvent;
        document.getElementById("eventID").value = calEvent.id;
        var title = calEvent.title;
    }

    // cut&paste
    if (jsEvent.ctrlKey) {
        lmb_calCut($(this).get(0), calEvent);
        return;
    }

    if (document.getElementById("bulkmenu")) {
        var bulk = "<div onclick=\"lmb_openBulkMenu()\" id=\"bulkmenuLink\" style=\"position:absolute;top:5px;right:20px;cursor:pointer;z-index:9999;\">&nbsp;Terminserie&nbsp;<i class=\"lmb-icon-cus lmb-cal-span\" style=\"vertical-align:text-bottom\"></i></div><br>";
    }

    // reset history_fields
    document.form1.history_fields.value = '';

    sendFullCalendarAction({
        action: 'details',
        gtabid: jsvar['gtabid'],
        form_id: jsvar['fformid'],
        ID: id,
        act: action
    }).then(function (data) {
        if (data.success) {
            if (id) {
                bulk = '';
            }

            let $modalDetail = $(data.html_modal);

            let $form1 = $('#form1');

            $form1.append($modalDetail);

            if(jsvar['fformid']) {
                //let widthIFrame = 500;
                let heightIFrame = 600;
                if (jsvar['form_dimension']) {
                    let formDimension = jsvar['form_dimension'].split('x');
                    //widthIFrame = (parseFloat(formDimension[0]) + 40);
                    heightIFrame = (parseFloat(formDimension[1]) + 80);
                }

                let $iframe = $('<iframe>', {
                    id: 'cal-detail-iframe',
                    src: "main.php?&action=" + action + "&ID=" + id + "&gtabid=" + jsvar['gtabid'] + "&form_id=" + jsvar['fformid'],
                    width: '100%',
                    height: heightIFrame,
                    frameborder: '0'
                });

                $modalDetail.find('#cal-detail-modal-body').html($iframe);

                $modalDetail.on('hidden.bs.modal', function () {
                    document.form1.history_fields.value = '';
                    lmb_calReload();
                });
            } else {
                let htmlDetailForm = bulk + '<div class="calenderBulk">' + data.html_form + '</div>';

                $modalDetail.find('#cal-detail-modal-body').html(htmlDetailForm);

                $modalDetail.on('hidden.bs.modal', function () {
                    lmb_calReload();
                });
            }

            $modalDetail.modal('show');
        } else {
            lmbShowErrorMsg('Opening of detail modal failed.');
        }
    }).catch(function () {
        lmbShowErrorMsg('Opening of detail modal failed.');
    });
}

// set calendar height
function lmb_calSetHeight() {
    if ($('#cal_searchFrame').css('display') != 'none') {
        // only count height of calendar
        $('#cal_searchFrame').hide();
        lmb_setAutoHeightCalendar(document.getElementById("calendar"), 10 /* somehow there are 3px overflow */);
        $('#cal_searchFrame').show();
    } else {
        lmb_setAutoHeightCalendar(document.getElementById("calendar"), 10 /* somehow there are 3px overflow */);
    }
}

function lmb_setAutoHeightCalendar(el, offset, reset) {
    offset = (typeof offset !== 'undefined') ? offset : 0;

    if (reset && el) {
        $(el).height('');
    } else {
        window.focus();
        if ($(el) && $(el).css('overflow-y') == 'auto') {
            // set white background to fit window
            var background = $(el).parents('.lmbfringegtab').first();
            var bgMarginY = background.outerHeight(true) - background.outerHeight();
            background.outerHeight(getWindowHeight() - bgMarginY);

            // set element to fit background
            var bgPaddingTop = (background.innerHeight() - background.height()) / 2;
            var elOffsetTop = $(el).position().top - background.position().top; // space between background and el
            var elNewHeight = background.height() - elOffsetTop + bgPaddingTop - offset;
            $(el).height(elNewHeight);
            return elNewHeight;
        }
    }
}

function lmb_closeCalDialog() {
    if ($lmbCalAjaxContainer.prev().hasClass('bulkContainer')) {
        $lmbCalAjaxContainer.prev().remove();
    }
    if ($lmbCalAjaxContainer.hasClass('ui-dialog-content')) {
        $lmbCalAjaxContainer.dialog('close');
    }
    document.form1.history_fields.value = '';
    lmb_calReload();
}

// use datepicker
function lmb_pickerSelected(date) {
    calendar.gotoDate(date);
}

// open formular
function lmb_popupDetails(event, gtabid, ID) {
    divclose();
    details = open("main.php?&action=gtab_change&ID=" + ID + "&gtabid=" + gtabid + "", "details", "toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=650,height=600");
}


// search frame
var visible = 0;

function lmb_calSearchFrame() {
    divclose();

    visible = 0;
    if ($('#cal_searchFrame').css('display') == 'none') {
        $('#cal_searchFrameTD').show();
        visible = 1;
    }

    $('#cal_searchFrame').animate({width: 'toggle'}, 500, function () {
        if (!visible) {
            $('#cal_searchFrameTD').hide();
        }
        calendar.render();
    });

    ajaxGet(null, 'main_dyns.php', 'layoutSettings&frame=cal&size=' + visible, null);
}

// open bulk menu
function lmb_openBulkMenu() {
    var cont = $('#bulkmenu').html().replace('bulk_ts', 'bulk_terminserie');
    cont = cont.replace(/calBulk_periodStartT/g, "calBulk_periodStart");
    cont = cont.replace(/calBulk_periodEndT/g, "calBulk_periodEnd");
    $lmbCalAjaxContainer.before(cont);
    $('#bulkmenuLink').remove();
    if (document.getElementById('g_' + jsvar['gtabid'] + '_7')) {
        document.getElementById('g_' + jsvar['gtabid'] + '_7').value = '';
        document.getElementById('g_' + jsvar['gtabid'] + '_7').disabled = true;
    }
    if (document.getElementById('g_' + jsvar['gtabid'] + '_8')) {
        document.getElementById('g_' + jsvar['gtabid'] + '_8').value = '';
        document.getElementById('g_' + jsvar['gtabid'] + '_8').disabled = true;
    }
}

// duplicate dates for bulk
function lmb_bulkAddTermin() {

    var i = 0;
    var e = 1;
    $('#bulk_terminserie').append('<table width="400px">' + $('#bulk_terminserie table:first').html() + '</table>');
    $('#bulk_terminserie input').each(function (index) {
        $(this).attr('name', $(this).attr('name').substring(0, 16) + '[' + i + ']');
        if (e == 5) {
            i++;
            e = 0;
        }
        e++;
    });
}


function lmb_validateTime(el, type) {
    if (type == 'h') {
        if (isNaN(el.value) || el.value > 23) {
            el.value = '';
        }
    } else if (type == 'm') {
        if (isNaN(el.value) || el.value > 59) {
            el.value = '00';
        }
    } else if (type == 'd') {
        if (isNaN(el.value)) {
            el.value = '';
        }
    }
}


function lmb_calChangeSettings(setting, val) {
    $('[name=cal_' + setting + ']').val(val);
    lmb_calReload(true);
}


//calender reload
function lmb_calReload(fullreload, detail_search) {

    divclose();
    if ($lmbCalAjaxContainer.hasClass('ui-dialog-content')) {
        $lmbCalAjaxContainer.dialog('close');
    }
    if ($("#lmb_eventDetailFrame").hasClass('ui-dialog-content')) {
        $("#lmb_eventDetailFrame").dialog('close');
    }

    if (!detail_search) {
        if ($lmbCalAjaxContainer.hasClass('ui-dialog-content')) {
            $lmbCalAjaxContainer.dialog('destroy');
        }
        $lmbCalAjaxContainer.html('');
    }

    if (fullreload) {
        var d = calendar.getDate();
        lmb_calDestroy();
        lmb_calInit();
        calendar.gotoDate(d);
        //lmb_calSetTitle();
    } else {
        calendar.refetchEvents()
    }

}

// override send_form from gtab_change.js
function send_form(formid, ajax, need, wclose, calBulk_precalc) {

    var needok = needfield(need);
    if (typeof calBulk_precalc == 'undefined') {
        calBulk_precalc = '';
    }
    set_focus();

    if (document.getElementById('g_' + jsvar['gtabid'] + '_7')) {
        var check = 1;
        var start = limbasParseDate($('#g_' + jsvar['gtabid'] + '_7').val(), jsvar['dateformat']);
    }
    if (document.getElementById('g_' + jsvar['gtabid'] + '_8')) {
        var check = 1;
        var end = limbasParseDate($('#g_' + jsvar['gtabid'] + '_8').val(), jsvar['dateformat']);
    }
    if (document.getElementById('calBulk_periodStart')) {
        var pcheck = 1;
        var pstart = limbasParseDate($('#calBulk_periodStart').val(), jsvar['dateformat']);
    }
    if (document.getElementById('calBulk_periodEnd')) {
        var pcheck = 1;
        var pend = limbasParseDate($('#calBulk_periodEnd').val(), jsvar['dateformat']);
    }

    if (pcheck) {
        if (pstart > pend) {
            alert('end-date before start-date!');
            return;
        }
        if (!pstart || !pend) {
            alert('start-date and end-date needed!');
            return;
        }
    } else if (check) {
        if (start > end) {
            alert('end-date before start-date!');
            return;
        }
        if (!start || !end) {
            alert('start-date and end-date needed!');
            return;
        }
    }

    if (needok) {
        alert(jsvar["lng_1509"] + "\n\n" + needok);
    } else {
        dynfunc = function (result) {
            send_formPost(result, ajax);
        };
        ajaxGet(null, 'main_dyns.php', 'fullcalendar&action=saveDetails&calBulk_precalc=' + calBulk_precalc, null, 'dynfunc', 'form1', null, 1);
        if (calBulk_precalc == 'create' || !document.getElementById("bulk_terminserie")) {
            if (!ajax) {
                lmb_calReload();
            }
        }
        return true;
    }
}

function lmb_extsearchclear() {
    $("[name='filter_reset']").val('1');
    lmb_calReload(1);
    $("[name='filter_reset']").val('');
}


// Ajax post formelements output
function send_formPost(result, ajax) {
    if (!ajax && activeEvent && $lmbCalAjaxContainer.hasClass('ui-dialog-content')) {
        $lmbCalAjaxContainer.dialog('destroy');
    }
    ajaxEvalScript(result);
}

function LmGs_sendForm(reset) {
    if (reset) {
        lmb_extsearchclear();
    } else {
        lmb_calReload(1, 1);
    }
}
