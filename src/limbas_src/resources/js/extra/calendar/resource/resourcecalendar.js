const CONTAINER_ID = 'container-resourcecalendar';

$(function () {
    $('html').css('overflow', 'hidden');

    let options = {
        minTime: unprocessedOptions.minTime ?? '00:00',
        maxTime: unprocessedOptions.maxTime ?? '24:00',
        editable: unprocessedOptions.editable ?? false,
        selectable: unprocessedOptions.selectable ?? false,
        weekends: unprocessedOptions.weekends ?? false,
        nowIndicator: unprocessedOptions.nowIndicator ?? false,
        subdivision: {
            week: unprocessedOptions.subdivision_week ?? 1,
            month: unprocessedOptions.subdivision_month ?? 1,
            day: unprocessedOptions.subdivision_day ?? 1,
        },
        striping: Striping.from(unprocessedOptions.striping),
        customOptions: {
            customFormId: unprocessedOptions.customOptions?.customFormId ?? undefined,
            customFormDimension: unprocessedOptions.customOptions?.customFormDimension ?? undefined,
            alertText: 'Do you want to create a new event?'
        },

        toolbar: {
            start: 'search,create viewMode',
            center: 'dateDisplay',
            end: 'dateSelect prev next'
        },

        customButtons: {
            search: {
                icon: 'fa-solid fa-magnifying-glass',
                onClick: () => LimbasUtils.showSearchModal(myCalendar.tabId),
            },
            create: {
                icon: 'fa-solid fa-plus-circle',
                onClick: () => LimbasUtils.showEventCreationModal(myCalendar),
            },
        },

        // functions
        fetchResourcesFunction: LimbasUtils.getFetchResources(),
        fetchEventDataFunction: LimbasUtils.fetchEventData,
        saveEventFunction: LimbasUtils.saveEvent,
        deleteEventFunction: LimbasUtils.deleteEvent,
        dateClick: LimbasUtils.showEventUpdateModal,
        eventClick: LimbasUtils.showEventUpdateModal,
    }

    options = {...unprocessedOptions, ...options};

    const myCalendar = new ResourceCalendar(options);
    myCalendar.render();
});

/**
 * Utility functions for interaction with Limbas
 */
const LimbasUtils = {
    /**
     * Post Action to server, function dyns_manageResourceCalendar
     * @param data
     * @return {Promise<unknown>}
     */
    postAction(data) {
        data['actid'] = 'manageResourceCalendar';

        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'main_dyns.php',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: (data) => {
                    resolve(data)
                },
                error: (error) => {
                    reject(error)
                }
            });
        })
    },

    /**
     * show Event modal for creation of event
     * @param {ResourceCalendar} calendar
     */
    showEventCreationModal(calendar) {
        LimbasUtils.showEventModal(null, calendar)
    },

    /**
     * show Event modal for modifiying of event
     * @param {ResourceCalendarEvent} event
     */
    showEventUpdateModal(event) {
        LimbasUtils.showEventModal(event)
    },

    /**
     * Show Event modal for creation (if event = null) or modification (if event is object of class ResourceCalendarEvent)
     * @param {ResourceCalendarEvent} event
     * @param {ResourceCalendar} calendar
     */
    showEventModal(event = null, calendar = null) {
        if (event instanceof ResourceCalendarEvent) {
            calendar = event.calendar;
        }
        if (calendar === null) {
            return;
        }

        LimbasUtils.postAction({
            action: 'details'
        }).then((data) => {
            if (data.success) {
                const $modalDetail = $(data.html);
                const $resourceSelect = $modalDetail.find('#input-resource');
                calendar.resources.forEach((resource, resourceId) => {
                    $resourceSelect.append($(`
                        <option value="${resourceId}">${resource}</option>
                    `));
                });

                if (calendar.options.customOptions.customFormId) {
                    if (event && !event.eventId) {
                        const userAccepted = confirm(calendar.options.customOptions.alertText);
                        if (!userAccepted) {
                            return;
                        }
                    }

                    let heightIFrame = 600;
                    let widthIFrame = 600;
                    if (calendar.options.customOptions.customFormDimension) {
                        let formDimension = calendar.options.customOptions.customFormDimension.split('x');
                        widthIFrame = (parseFloat(formDimension[0]) + 40);
                        heightIFrame = (parseFloat(formDimension[1]) + 80);
                    }

                    let eventId;
                    let action;
                    if (event instanceof ResourceCalendarEvent) {
                        eventId = event.eventId;
                        action = 'gtab_change';
                    } else {
                        eventId = 0;
                        action = 'gtab_neu';
                    }

                    const openIFrame = () => {
                        if (eventId === null) {
                            eventId = event.eventId;
                        }

                        $modalDetail.find('.modal-dialog').css('--bs-modal-width', `${widthIFrame}px`);

                        let $iframe = $('<iframe>', {
                            id: 'cal-detail-iframe',
                            src: `main.php?&action=${action}&ID=${eventId}&gtabid=${calendar.tabId}&form_id=${calendar.options.customOptions.customFormId}`,
                            width: '100%',
                            height: heightIFrame,
                            frameborder: '0'
                        });

                        // remove everything but close button
                        $modalDetail.find('#resource-detail-modal-footer-default > *:not(#rc-btn-close)').remove();

                        $modalDetail.find('#resource-detail-modal-body').html($iframe);

                        // update on modal close, as iframe cannot communicate
                        $modalDetail.on('hidden.bs.modal', () => {
                            calendar.updateDates();
                            calendar.render();
                        });
                    }

                    if (eventId === null) {
                        LimbasUtils.saveEvent(event, () => {
                            event.render();
                            openIFrame();
                        });
                    } else {
                        openIFrame();
                    }
                } else {
                    if (event instanceof ResourceCalendarEvent) {
                        $modalDetail.find('#input-startdate').val(LimbasUtils.dateToLocalISOString(event.startDate));
                        $modalDetail.find('#input-enddate').val(LimbasUtils.dateToLocalISOString(event.endDate));
                        $modalDetail.find('#input-title').val(event.title);
                        $modalDetail.find('#input-color').val(event.color);
                        $resourceSelect.val(event.resourceId);
                        $modalDetail.find('#input-allday').prop('checked', event.allDay);

                        const btnDelete = $modalDetail.find('#rc-btn-delete');
                        if (event.eventId) {
                            btnDelete.on('click', (e) => {
                                e.preventDefault();
                                $modalDetail.modal('hide');
                                LimbasUtils.deleteEvent(event, () => {
                                    // delete the event and its jquery data
                                    event.remove();
                                });
                            });
                        } else {
                            btnDelete.remove();
                        }

                        $modalDetail.find('form').on('submit', (e) => {
                            e.preventDefault();

                            const formData = new FormData(e.currentTarget);
                            event.resourceId = formData.get('resource').toString();
                            event.setStartDate(new Date(formData.get('startdate').toString()));
                            event.setEndDate(new Date(formData.get('enddate').toString()));
                            event.setTitle(formData.get('title'));
                            event.setColor(formData.get('color'));
                            event.setAllDay($modalDetail.find('#input-allday').is(':checked'));
                            LimbasUtils.saveEvent(event, () => {
                                event.render();
                            }, true);

                            $modalDetail.modal('hide');
                        });
                    } else {
                        $modalDetail.find('#input-startdate').val(LimbasUtils.dateToLocalISOString(calendar.selectedDate));
                        $modalDetail.find('#input-enddate').val(LimbasUtils.dateToLocalISOString(calendar.selectedDate));
                        $modalDetail.find('#input-color').val(calendar.options.defaultEventColor);
                        $modalDetail.find('#rc-btn-delete').remove();

                        $modalDetail.find('form').on('submit', (e) => {
                            e.preventDefault();

                            const formData = new FormData(e.currentTarget);

                            event = ResourceCalendarEvent.new(
                                calendar,
                                new Date(formData.get('startdate').toString()),
                                new Date(formData.get('enddate').toString()),
                                formData.get('resource').toString(),
                                null,
                                formData.get('title'),
                                formData.get('color'),
                                undefined,
                                $modalDetail.find('#input-allday').is(':checked'),
                            );

                            if (event !== null) {
                                LimbasUtils.saveEvent(event, () => {
                                    event.render();
                                }, true);
                            }

                            $modalDetail.modal('hide');
                        });
                    }
                }

                $modalDetail.modal('show');
            } else {
                lmbShowErrorMsg('Opening of detail modal failed.');
            }
        }).catch(() => {
            lmbShowErrorMsg('Opening of detail modal failed.');
        });
    },

    /**
     * Show Search Modal for given tabId (in this case calendar/event table)
     * @param tabId
     */
    showSearchModal(tabId) {
        limbasDetailSearch(null, null, tabId, '', 'lmbCalAjaxContainer');
    },

    /**
     * Creates ISO conformant string from Date object without converting to UTC
     * @param date
     * @return {string}
     */
    dateToLocalISOString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    },

    deleteEvent(event, onSuccess = null) {
        LimbasUtils.postAction({
            action: 'delete',
            event_id: event.eventId,
            tab_id: event.calendar.tabId,
        }).then((data) => {
            if (data.success) {
                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
            } else {
                lmbShowErrorMsg('Deleting of event failed.');
            }
        }).catch(() => {
            lmbShowErrorMsg('Deleting of event failed.');
        });
    },

    saveEvent(event, onSuccess = null, saveDetails = false) {
        let inputData = {
            action: 'save',
            start_date: LimbasUtils.dateToLocalISOString(event.startDate),
            end_date: LimbasUtils.dateToLocalISOString(event.endDate),
            resource_id: event.resourceId,
            event_id: event.eventId,
            tab_id: event.calendar.tabId,
            title: event.title,
            color: event.color,
            all_day: event.allDay
        }

        if (saveDetails) {
            inputData = {
                ...inputData, ...{
                    action: 'saveDetails',
                }
            };
        }

        LimbasUtils.postAction(inputData).then((data) => {
            if (data.success) {
                if (data.event_id) {
                    event.eventId = data.event_id;
                }
                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
            } else {
                lmbShowErrorMsg('Saving of event failed.');
            }
        }).catch(() => {
            lmbShowErrorMsg('Saving of event failed.');
        });
    },

    /**
     * Sends synchronous ajax request to initialize table; Synchronous because everything else relies on it, so it needs to be blocking
     * If passed, set gs variable to use search functionality/pass search parameters to server
     * @return {any}
     */
    getFetchResources(gsObject = null) {
        return (calendar) => {
            let resources = {};

            let inputData = {
                action: 'get_resources',
                actid: 'manageResourceCalendar',
                tab_id: calendar.tabId
            };

            if (gsObject !== null) {
                inputData = {...inputData, ...gsObject, gssearch: true, gtabid: calendar.tabId};
            }

            $.ajax({
                url: 'main_dyns.php',
                type: 'POST',
                data: inputData,
                dataType: 'json',
                async: false, // Make the request synchronous
                success: (data) => {
                    if (data.success) {
                        resources = data.resources ?? [];
                    } else {
                        console.error('Failed to fetch initial data');
                    }
                },
                error: (error) => {
                    console.error('Error fetching initial data:', error);
                }
            });

            return resources;
        }
    },

    fetchEventData(calendar) {
        let eventData;

        let inputData = {
            action: 'get_events',
            actid: 'manageResourceCalendar',
            tab_id: calendar.tabId,
            start_date: LimbasUtils.dateToLocalISOString(calendar.startDate),
            end_date: LimbasUtils.dateToLocalISOString(calendar.endDate),
        };

        $.ajax({
            url: 'main_dyns.php',
            type: 'POST',
            data: inputData,
            dataType: 'json',
            async: false, // Make the request synchronous
            success: (data) => {
                if (data.success) {
                    eventData = data.events;
                } else {
                    console.error('Failed to fetch event data:', data.message);
                }
            },
            error: (error) => {
                console.error('Error fetching event data:', error);
            }
        });

        return eventData;
    },
}

/**
 * General Utility functions for calendar
 * note: put this into a module in the future
 */
const ResourceCalendarUtils = {
    /**
     * Calculates the absolute time difference between two dates in milliseconds.
     * @returns {number}
     * @param {Date} date1
     * @param {Date} date2
     */
    calculateIntervalMilliseconds(date1, date2) {
        return Math.abs(date1.getTime() - date2.getTime());
    },

    /**
     * Calculates week number for a given date.
     * @param d
     * @return {number}
     */
    getWeekNumber(d) {
        d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
        const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
        return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
    },

    /**
     * hex color string to rgb number array
     * @param color
     * @return {number[]}
     */
    getRGB(color) {
        color = color.startsWith('#') ? color.substring(1) : color;
        const r = parseInt(color.substring(0, 2), 16);
        const g = parseInt(color.substring(2, 4), 16);
        const b = parseInt(color.substring(4, 6), 16);
        return [r, g, b];
    },

    /**
     * get white or black font color for background color
     * @param color
     * @return {string}
     */
    getContrastYIQ(color) {
        const [r, g, b] = this.getRGB(color);
        const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
        return (yiq >= 128) ? 'black' : 'white';
    },

    /**
     * darken/lighten color by specified amount -> <1.0 lighten; >1.0 darken
     * @param color
     * @param decimal
     * @return {string}
     */
    shadeColor(color, decimal) {
        return `#${(this.getRGB(color)).map((n) => Math.min(Math.round(n / decimal), 255).toString(16).padStart(2, '0')).join('')}`
    },

    /**
     * find the highest multiple of interval that is smaller than target
     * @param target
     * @param interval
     * @return {number}
     */
    findSmallestEquidistantValue(target, interval) {
        if (interval === 0) {
            return 0;
        }

        const roundedQuotient = Math.floor(target / interval);
        return roundedQuotient * interval;
    },

    /**
     * find the smallest multiple of interval that is bigger than target
     * @param target
     * @param interval
     * @return {number}
     */
    findBiggestEquidistantValue(target, interval) {
        if (interval === 0) {
            return 0;
        }

        const roundedQuotient = Math.ceil(target / interval);
        return roundedQuotient * interval;
    },

    /**
     * convert Date object to string for type date input
     * @param {Date} date
     * @return {string}
     */
    formatDateForInput(date) {
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');

        return `${year}-${month}-${day}`;
    },
};

/**
 * ViewMode determines table date layout, formatting etc.
 */
class ViewMode {
    static DEFAULT_LOCALE = 'en-US';

    /**
     * Returns all available view modes (ViewMode Subclasses)
     * note: this is a function because subclasses cannot be statically accessed before the whole class is instantiated
     */
    static modes() {
        return {
            [WeekView.name]: WeekView,
            [MonthView.name]: MonthView,
            [DayView.name]: DayView
        }
    }

    static fromString(str, options = {}) {
        return new (ViewMode.modes()[str] || WeekView)(options);
    }

    constructor(name, options) {
        if (this.constructor === ViewMode) {
            throw new Error("Abstract class ViewMode cannot be instantiated.");
        }
        this.name = name;
        this.subdivision = options.subdivision[name] ?? 1;
        this.locale = options.locale ?? ViewMode.DEFAULT_LOCALE;
        this.weekends = options.weekends ?? true;
    }

    formatDate(date) {
    }

    advanceBy(date, by) {
    }

    getDates(date = new Date()) {
    }

    getStartDate(date) {
    }

    getEndDate(date) {
        return this.advanceBy(this.getStartDate(date), 1);
    }

    equalsUnit(date1, date2) {
    }

    header(date) {
    }
}

class WeekView extends ViewMode {
    static name = 'week';
    static buttonIconClass = 'fa-solid fa-calendar-week';

    constructor(options) {
        super(
            WeekView.name,
            options
        );
    }

    formatDate(date) {
        const options = {
            weekday: 'short',
            month: 'long',
            day: 'numeric'
        };

        return new Intl.DateTimeFormat(this.locale, options).format(date);
    }

    /**
     * Advances the given date by a number of weeks.
     * @param {Date} date
     * @param {number} by
     * @returns {Date}
     */
    advanceBy(date, by) {
        const newDate = new Date(date);
        newDate.setDate(newDate.getDate() + (by * 7));
        return newDate;
    }

    /**
     * Gets an array of Date objects for the week containing the given date,
     * starting from Monday and ending on Sunday.
     * @param {Date} date
     * @returns {Array<Date>}
     */
    getDates(date = new Date()) {
        const monday = this.getStartDate(date);

        const weekDates = [];
        const dayCount = this.weekends ? 7 : 5;

        for (let i = 0; i < dayCount; i++) {
            const newDate = new Date(monday);
            newDate.setDate(monday.getDate() + i);
            weekDates.push(newDate);
        }

        return weekDates;
    }

    getStartDate(date) {
        const currentDayOfWeek = date.getDay();

        const daysToMonday = currentDayOfWeek === 0 ? -6 : -(currentDayOfWeek - 1);

        const monday = new Date(date);
        monday.setDate(date.getDate() + daysToMonday);
        monday.setHours(0, 0, 0, 0);

        return monday;
    }

    getEndDate(date) {
        const endDate = super.getEndDate(date);
        if (!this.weekends) {
            endDate.setDate(endDate.getDate() - 2);
        }
        return endDate;
    }

    equalsUnit(date1, date2) {
        if (!(date1 instanceof Date) || !(date2 instanceof Date)) {
            return false;
        }

        return date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    header(date) {
        const startDate = this.getStartDate(date);
        const endDate = this.getEndDate(date);

        const formatterStart = new Intl.DateTimeFormat(this.locale, {
            day: '2-digit',
            month: '2-digit',
        });

        const formatterEnd = new Intl.DateTimeFormat(this.locale, {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });

        const startFormatted = formatterStart.format(startDate);
        const endFormatted = formatterEnd.format(endDate);
        const calendarWeek = ResourceCalendarUtils.getWeekNumber(date);


        return `KW ${calendarWeek}: ${startFormatted} - ${endFormatted}`;
    }
}

class MonthView extends ViewMode {
    static name = 'month';
    static buttonIconClass = 'fa-solid fa-calendar';

    constructor(options) {
        super(
            MonthView.name,
            options
        );
    }

    formatDate(date) {
        const formatter = new Intl.DateTimeFormat(this.locale, {
            weekday: 'short',
            day: '2-digit',
        });

        let formattedDate = formatter.format(date);

        formattedDate = formattedDate.replace(/,\s*/, ' ');
        formattedDate = formattedDate.replace(/\.$/, '');

        return formattedDate;
    }


    /**
     * Advances the given date by a number of months.
     * @param {Date} date
     * @param {number} by
     * @returns {Date}
     */
    advanceBy(date, by) {
        const newDate = new Date(date);
        newDate.setMonth(newDate.getMonth() + by);
        return newDate;
    }

    /**
     * Gets an array of Date objects for the month containing the given date
     * @param {Date} date
     * @returns {Array<Date>}
     */
    getDates(date = new Date()) {
        const year = date.getFullYear();
        const month = date.getMonth();

        const dates = [];
        let day = 1;

        let newDate;
        while ((newDate = new Date(year, month, day)).getMonth() === month) {
            dates.push(newDate);
            day++;
        }

        return dates;
    }

    getStartDate(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const day = 1;

        return new Date(year, month, day);
    }

    equalsUnit(date1, date2) {
        if (!(date1 instanceof Date) || !(date2 instanceof Date)) {
            return false;
        }

        return date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate();
    }

    header(date) {
        return new Intl.DateTimeFormat(this.locale, {month: 'long', year: 'numeric'}).format(date);
    }
}

class DayView extends ViewMode {
    static name = 'day';
    static buttonIconClass = 'fa-solid fa-calendar-day';

    constructor(options) {
        super(
            DayView.name,
            options
        );
    }

    formatDate(date) {
        const formatter = new Intl.DateTimeFormat(this.locale, {
            hour: '2-digit',
            minute: '2-digit'
        });

        return formatter.format(date);
    }

    /**
     * Advances the given date by a number of days.
     * @param {Date} date
     * @param {number} by
     * @returns {Date}
     */
    advanceBy(date, by) {
        const newDate = new Date(date);
        newDate.setDate(newDate.getDate() + by);
        return newDate;
    }

    /**
     * Gets an array of 24 Date objects representing each hour of the day for the given date.
     * Each Date object will be set to the start of that hour (e.g., 00:00, 01:00, ..., 23:00).
     * @param {Date} date
     * @returns {Array<Date>}
     */
    getDates(date = new Date()) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const day = date.getDate();

        const hours = [];
        for (let i = 0; i < 24; i++) {
            const hourDate = new Date(year, month, day, i, 0, 0, 0);
            hours.push(hourDate);
        }
        return hours;
    }

    getStartDate(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const day = date.getDate();

        return new Date(year, month, day, 0, 0, 0, 0);
    }

    equalsUnit(date1, date2) {
        if (!(date1 instanceof Date) || !(date2 instanceof Date)) {
            return false;
        }

        return date1.getFullYear() === date2.getFullYear() &&
            date1.getMonth() === date2.getMonth() &&
            date1.getDate() === date2.getDate() &&
            date1.getHours() === date2.getHours();
    }

    header(date) {
        return new Intl.DateTimeFormat(this.locale).format(date);
    }
}


const Striping = Object.freeze({
    NONE: Symbol("none"),
    COLUMNS: Symbol("columns"),
    ROWS: Symbol("rows"),

    from(str) {
        return Object.values(this).find(symbol =>
            typeof symbol === 'symbol' && symbol.description === str
        ) ?? this.DEFAULT;
    },

    get DEFAULT() {
        return this.NONE;
    },
});

class ResourceCalendar {
    static DEFAULT_OPTIONS = {
        // calendar settings
        containerId: 'container-resourcecalendar',
        defaultEventColor: '#5e5e5e',
        initialDate: new Date(),
        nowIndicator: true,
        selectable: true, // todo
        editable: true, // todo
        striping: Striping.DEFAULT,

        eventDrop: null,
        eventClick: null,
        dateClick: null,

        // viewmode settings
        defaultViewMode: 'week',
        locale: navigator.language,
        weekends: true,
        minTime: '00:00', // todo
        maxTime: '24:00', // todo
        subdivision: {
            'week': 1,
            'month': 1,
            'day': 1,
        },

        toolbar: {
            start: 'viewMode',
            center: 'dateDisplay',
            end: 'dateSelect prev next'
        },

        customButtons: {

        },

        // customOptions are options that are not used by the resource calendar and just are stored for calendar extensions
        customOptions: {}
    }

    standardButtons = {
        viewMode: {
            creationFunction: this.createViewModeButtons.bind(this)
        },
        dateDisplay: {
            creationFunction: this.createDateDisplay.bind(this)
        },
        dateSelect: {
            creationFunction: this.createDateSelect.bind(this)
        },
        prev: {
            icon: 'lmb-icon lmb-caret-left',
            onClick: this.getPrev.bind(this),
        },
        next: {
            icon: 'lmb-icon lmb-caret-right',
            onClick: this.getNext.bind(this),
        }
    }

    /**
     * Creates an instance of ResourceCalendar
     * @param options This should be set when initializing a ResourceCalendar
     */
    constructor(options) {
        this.options = {...ResourceCalendar.DEFAULT_OPTIONS, ...options};

        this.container = $('#' + this.options.containerId);
        this.container.empty();

        if (this.container.length === 0) {
            console.error(`ResourceCalendar Error: Container element with ID "${this.options.containerId}" not found.`);
            return;
        }

        this.container.data('calendar', this);

        this.setViewMode(this.options.defaultViewMode, this.options.locale)
        this.selectedDate = new Date(this.options.initialDate);
        this.tabId = this.options.tabId;

        this.fetchResourcesFunction = this.options.fetchResourcesFunction;
        this.fetchEventDataFunction = this.options.fetchEventDataFunction;
        this.saveEventFunction = this.options.saveEventFunction;
        this.deleteEventFunction = this.options.deleteEventFunction;
        this.dateClick = this.options.dateClick;
        this.eventClick = this.options.eventClick;
        this.buttons = {...this.standardButtons, ...this.options.customButtons};

        this.updateCalendarData();

        // re-render calendar on window resize
        const calendar = this;
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (calendar.isRendered) {
                    calendar.render();
                }
            }, 250);
        });

        this.rows = new Map();
    }

    rerender() {
        this.updateCalendarData();
        this.render();
    }

    updateCalendarData() {
        this.updateResources();
        this.updateDates();
    }

    updateResources() {
        this.resources = new Map(Object.entries(this.fetchResources()));
    }

    verifyResource(resourceId) {
        return resourceId && this.resources.has(resourceId);
    }

    setViewMode(viewmode) {
        this.viewmode = ViewMode.fromString(viewmode, this.options);
    }

    selectDate(newDate) {
        this.selectedDate = newDate;
        this.updateDates();
        this.render();
    }

    /**
     * setDates according to viewmode, e.g. monday to sunday for weekview
     */
    updateDates() {
        this.subdivision = this.viewmode.subdivision ?? 1;

        this.dates = this.viewmode.getDates(this.selectedDate);
        this.startDate = this.viewmode.getStartDate(this.selectedDate);
        this.endDate = this.viewmode.getEndDate(this.selectedDate);

        // todo maybe move this into viewmode
        // calculate the ms of one subdivided date (column)
        this.subdivisionInterval = ResourceCalendarUtils.calculateIntervalMilliseconds(
            new Date(this.dates[0]),
            new Date(this.dates[1])
        ) / this.subdivision;

        this.updateEventData();
    }

    /**
     * Sends synchronous ajax request to initialize table; Synchronous because everything else relies on it, so it needs to be blocking
     * If passed, set gs variable to use search functionality/pass search parameters to server
     * @return {any}
     */
    fetchResources() {
        return this.fetchResourcesFunction(this);
    }

    fetchEventData() {
        return this.fetchEventDataFunction(this);
    }

    /**
     * update eventData variable before render()
     */
    updateEventData() {
        this.eventData = this.fetchEventData();
    }

    renderEvents() {
        this.tdWidth = this.container.find('td').outerWidth();

        // check if calendar is rendered
        if (!this.tdWidth) {
            return;
        }

        for (const i in this.eventData) {
            const event = ResourceCalendarEvent.fromJSON(this, this.eventData[i]);

            if (event instanceof ResourceCalendarEvent) {
                event.renderPartial();
            }
        }

        this.$calendar.find('tr').each((index, tr) => {
            ResourceCalendarEvent.updateRow($(tr));
        })
    }

    /**
     * Clears the container and renders the resource calendar table based on the provided data
     */
    render() {
        if (!this.container || this.container.length === 0) {
            console.error("ResourceCalendar Error: Cannot render. Instance is not valid or container not found.");
            return;
        }

        const calendar = this;

        this.$calendar = $(`<div>`);

        const $toolbar = this.createToolbar();
        this.$calendar.append($toolbar);

        const $table = this.createTable();
        this.$calendar.append($table);

        this.container.empty();
        this.container.append(this.$calendar);

        let $tds = this.container.find('td');

        const $dropzones = this.container.find('.dropzone');
        this.$dropzones = $dropzones;

        this.renderEvents();

        $tds.on('click', function (e) {
            const event = ResourceCalendarEvent.fromClick(
                calendar,
                this,
                e
            );

            if (typeof calendar.dateClick === "function") {
                calendar.dateClick(event);
            }
        })

        $dropzones.on('dragenter', function (e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        });

        $dropzones.on('dragover', function (e) {
            e.preventDefault();
        });

        $dropzones.on('dragleave', function () {
            $(this).removeClass('drag-over');
        });

        $dropzones.on('drop', function (e) {
            e.preventDefault();

            $(this).removeClass('drag-over');

            const id = e.originalEvent.dataTransfer.getData('text/plain');
            const $event = $('#' + id);
            $event.data("drop_target", e.target);
        });

        this.isRendered = true;
    }

    /**
     *
     * @return {jQuery}
     */
    createTable() {
        const $table = $('<table>')
            .addClass('table table-responsive table-hover')
            .css({
                tableLayout: 'fixed',
            })
            .append(this.createHeader())
            .append(this.createBody());

        if (this.options.striping === Striping.ROWS) {
            $table.addClass('table-striped');
        }

        const $tableContainer = $('<div>').addClass('overflow-y-scroll overflow-x-hidden').css('height', '85vh').append($table);

        const $col = $('<div>').addClass('col').append($tableContainer);

        return $('<div>').addClass('row').append($col);
    }

    /**
     * Creates the toolbar element
     * @returns {jQuery} The generated thead jQuery object.
     * @private
     */
    createToolbar() {
        const $toolbarRow = $(`
            <div class="row mb-2">
                <div class="col d-flex justify-content-between">
                    
                </div>
            </div>
        `);

        const $toolbar = $toolbarRow.children().first();

        const toolbarConfig = this.options.toolbar;

        for (const [key, value] of Object.entries(toolbarConfig)) {
            const $toolbarPart = $(`<div class="d-flex">`);
            let groups = value.split(' ');
            groups = groups.map(word => word.split(','));
            for (const group of groups) {
                let $group;
                if (group.length > 1) {
                    const $buttonGroup = $(`<div>`);
                    for (const button of group) {
                        $buttonGroup.append(this.createButton(this.buttons[button]));
                    }
                    $group = $buttonGroup;
                } else if (group.length === 1) {
                    $group = this.createButton(this.buttons[group[0]]);
                } else {
                    continue;
                }
                $toolbarPart.append($group);
            }
            if (key === 'start') {
                $toolbarPart.children().addClass('me-1');
            } else if (key === 'end') {
                $toolbarPart.children().addClass('ms-1');
            }
            $toolbar.append($toolbarPart);
        }

        return $toolbarRow;
    }

    createViewModeButtons() {
        const $viewmodeBtnDiv = $(`<div>`);
        const viewModes = ViewMode.modes();
        for (const viewmode in viewModes) {

            // todo: make this language independent, for now only on english
            const viewmodeTitle =  String(viewmode).charAt(0).toUpperCase() + String(viewmode).slice(1);

            const $viewmodeBtn = $(`
                <button title="${viewmodeTitle}" class="btn btn-dark"><i class="${viewModes[viewmode].buttonIconClass}"></i></button>
            `).on('click', () => {
                this.setViewMode(viewmode);
                this.updateDates();
                this.render();
            });
            if (viewModes[viewmode].name === this.viewmode.name) {
                $viewmodeBtn.addClass('active');
            }
            $viewmodeBtnDiv.append($viewmodeBtn);
        }
        return $viewmodeBtnDiv;
    }

    createDateDisplay() {
        const headerText = this.viewmode.header(this.selectedDate);
        return $(`<span class="fw-bold fs-5">${headerText}</span>`);
    }

    createButton(button) {
        if (typeof button.creationFunction === "function") {
            return button.creationFunction();
        }

        const $button = $(`
            <button class="btn btn-dark">
        `).on('click', (event) => button.onClick(event));

        if (button.icon) {
            if (button.text) {
                $button.prop('title', button.text);
            }
            $button.append($(`<i class="${button.icon}"></i>`));
        } else if (button.text) {
            $button.text(button.text);
        }

        return $button;
    }

    createDateSelect() {
        return $(`
            <input 
                    class="form-control me-1"
                    type="date"
                    value="${ResourceCalendarUtils.formatDateForInput(this.selectedDate)}">
        `).on('change', (event) => {
            const selectedDateString = event.target.value;
            const selectedDate = new Date(selectedDateString);
            this.selectDate(selectedDate);
        });
    }

    /**
     * Creates the table header (<thead>) element
     * @returns {jQuery} The generated thead jQuery object.
     * @private
     */
    createHeader() {
        const $thead = $('<thead>').css({
            position: 'sticky',
            top: '0px',
            zIndex: 1000,
        });
        const $headerRow = $('<tr>');

        // First empty header cell (top-left corner)
        $headerRow.append($('<th>')
            .attr('scope', 'col')
            .attr('colspan', this.subdivision)
            .css({
                width: '15vw',
                position: 'sticky',
                top: '0px',
            }));

        const today = new Date();
        this.todayIndex = null;

        // Date header cells
        $.each(this.dates, (index, date) => {
            const formattedDate = this.viewmode.formatDate(date);

            const $dateTh = $('<th>')
                .attr('scope', 'col')
                .attr('colspan', this.subdivision)
                .css({
                    position: 'sticky',
                    top: '0px',
                })
                .text(formattedDate);

            if (this.options.striping === Striping.COLUMNS && index % 2 === 0) {
                $dateTh.css('box-shadow', 'inset 0 0 0 9999px rgba(var(--bs-emphasis-color-rgb), 0.05)');
            }

            if (this.options.nowIndicator) {
                if (this.viewmode.equalsUnit(today, date)) {
                    $dateTh.addClass('bg-warning');
                    this.todayIndex = index;
                }
            }

            $headerRow.append($dateTh);
        });
        $thead.append($headerRow);
        return $thead;
    }

    /**
     * Creates the table body (<tbody>) element
     * @returns {jQuery} The generated tbody jQuery object.
     * @private
     */
    createBody() {
        const $tbody = $('<tbody>');

        this.resources.forEach((resource, resourceId) => {
            const $resourceRow = $('<tr>');

            const $resourceTh = $('<th>')
                .addClass('p-1')
                .attr('scope', 'row')
                .attr('colspan', this.subdivision)
                .css({
                    width: '15vw',
                })
                .html(`${resource} (${resourceId})`);
            $resourceRow.append($resourceTh);
            for (let i = 0; i < this.dates.length; i++) {
                for (let j = 0; j < this.subdivision; j++) {
                    const $resourceTd = $('<td>');
                    if (this.todayIndex && i === this.todayIndex) {
                        $resourceTd.addClass('bg-warning-subtle');
                    }
                    if (this.options.striping === Striping.COLUMNS && i % 2 === 0) {
                        $resourceTd.css('box-shadow', 'inset 0 0 0 9999px rgba(var(--bs-emphasis-color-rgb), 0.05)');
                    }
                    let borderStyle = j + 1 < this.subdivision ? 'dashed' : 'solid'
                    $resourceTd.css({
                        borderRight: `1px rgba(0, 0, 0, 0.3) ${borderStyle}`
                    });
                    const $dropzoneDiv = $('<div>')
                        .addClass('dropzone w-100 h-100');
                    $resourceTd.append($dropzoneDiv);
                    $resourceRow.append($resourceTd);
                }
            }
            $resourceRow.data({
                resource_id: resourceId,
                height_index: 0
            });

            $tbody.append($resourceRow);
            this.rows.set(resourceId, $resourceRow);
        });

        return $tbody;
    }

    getNext() {
        this.selectDate(this.viewmode.advanceBy(this.selectedDate, 1));
    }

    getPrev() {
        this.selectDate(this.viewmode.advanceBy(this.selectedDate, -1));
    }

    verifyDateValidity(startDate, endDate) {
        if (!startDate || !endDate || !this.startDate || !this.endDate || startDate > endDate || this.startDate > this.endDate) {
            return false;
        }
        const startDateBetweenBounds = (startDate < this.endDate && startDate >= this.startDate);
        const endDateBetweenBounds = (endDate > this.startDate && endDate < this.endDate);
        const bothOverbounds = (this.startDate >= startDate && this.endDate <= endDate);
        return startDateBetweenBounds || endDateBetweenBounds || bothOverbounds;
    }

    subdivisionsToTime(subdivisions) {
        return subdivisions * this.subdivisionInterval;
    }

    getNextDate(date) {
        return new Date(date.getTime() + this.subdivisionInterval);
    }

    getDateFromDiffX(diffX) {
        return new Date(this.startDate.getTime() + this.subdivisionInterval * diffX / this.tdWidth);
    }

    getDiffXFromDate(date) {
        const diffTime = date.getTime() - this.startDate.getTime();
        return diffTime / this.subdivisionInterval * this.tdWidth;
    }

    getBoundedDiffXFromDate(date) {
        return Math.min(Math.max(this.getDiffXFromDate(date), 0), this.getDiffXFromDate(this.endDate));
    }
}

class ResourceCalendarEvent {
    /**
     * checked factory function, returns null if invalid date was entered
     * @param {ResourceCalendar} calendar
     * @param {Date} startDate
     * @param {Date} endDate
     * @param {string} resourceId
     * @param {string|null} eventId
     * @param {string} title
     * @param {string} color
     * @param {string} additionalEventHTML
     * @param {boolean} allDay
     * @return {ResourceCalendarEvent|null}
     */
    static new(calendar, startDate, endDate, resourceId, eventId = null, title = '', color = '', additionalEventHTML = '', allDay = false) {
        if (
            !calendar.verifyDateValidity(startDate, endDate)
            || !calendar.verifyResource(resourceId)
        ) {
            return null;
        }
        return new ResourceCalendarEvent(calendar, startDate, endDate, resourceId, eventId, title, color, additionalEventHTML, allDay);
    }

    /**
     *
     * @param {ResourceCalendar} calendar
     * @param {Date} startDate
     * @param {Date} endDate
     * @param {string} resourceId
     * @param {string|null} eventId
     * @param {string} title
     * @param {string} color
     * @param {string} additionalEventHTML
     * @param {boolean} allDay
     */
    constructor(calendar, startDate, endDate, resourceId, eventId = null, title = '', color = '', additionalEventHTML = '', allDay = false) {
        this.calendar = calendar;
        this.startDate = startDate;
        this.endDate = endDate;
        this.resourceId = resourceId;
        this.eventId = eventId;
        this.title = title ?? '';
        this.color = color ?? '';
        this.additionalEventHTML = additionalEventHTML ?? '';
        this.allDay = allDay ?? false;

        this.heightIndex = 0; // heightIndex controls overlapping events
    }

    static fromClick(calendar, startTd, e) {
        const $startTd = $(startTd).closest('tr').find('td').first();
        const clickX = e.originalEvent.clientX;
        const tdOffset = $startTd.offset().left;
        const diffX = clickX - tdOffset;
        const snappedWidth = ResourceCalendarUtils.findSmallestEquidistantValue(diffX, calendar.tdWidth);

        const startDate = calendar.getDateFromDiffX(snappedWidth);
        const endDate = new Date(calendar.getNextDate(startDate));
        const resourceId = $startTd.closest('tr').data('resource_id');
        const eventId = null;
        const title = '';
        const color = calendar.options.defaultEventColor;
        const allDay = false;

        return ResourceCalendarEvent.new(calendar, startDate, endDate, resourceId, eventId, title, color, undefined, allDay);
    }

    static fromJSON(calendar, data) {
        const startDate = new Date(data.start);
        const endDate = new Date(data.end);
        const resourceId = data.resourceId?.toString() ?? null;
        const eventId = data.id ?? null;
        const title = data.title ?? '';
        const color = data.backgroundColor ?? calendar.options.defaultEventColor;
        const additionalEventHTML = data.extendedProps?.evtBeforeEvent ?? '';
        const allDay = data.allDay ?? false;

        return ResourceCalendarEvent.new(calendar, startDate, endDate, resourceId, eventId, title, color, additionalEventHTML, allDay);
    }

    /**
     * initiate the event in the dom. note: this rerenders the event if it is already rendered
     */
    initiate() {
        if (this.$event) {
            this.remove();
        }

        const $event = $(`
            <div 
                id="event-${this.eventId}"
                draggable="true"
                class="rc-event d-flex justify-content-center align-items-center">
                ${this.additionalEventHTML}
                <span style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;"></span>
                <div class="resize-handle handle-start d-flex justify-content-center align-items-center"></div>
                <div class="resize-handle handle-end d-flex justify-content-center align-items-center"></div>
            </div>
        `);

        $event.data('event_id', this.eventId);
        $event.data('event', this);

        const event = this;

        let isResizing;
        let startX;
        let startTime;
        let endTime;

        const $resizeHandleStart = $event.find('.handle-start');
        const $resizeHandleEnd = $event.find('.handle-end');

        $resizeHandleStart.on('mousedown', (e) => handleMouseDown(e, true, $resizeHandleStart))
        $resizeHandleEnd.on('mousedown', (e) => handleMouseDown(e, false, $resizeHandleEnd))

        $event.on('click', (e) => {
            e.stopPropagation();
            if (isResizing) {
                isResizing = false;
                e.stopPropagation();
                e.preventDefault();
                return;
            }

            if (typeof this.calendar.eventClick === "function") {
                this.calendar.eventClick(this);
            }
        })

        let dragStartX;

        $event.on('dragstart', (e) => {
            dragStartX = e.clientX;

            $event.data("drop_target", null);

            e.originalEvent.dataTransfer.setData('text/plain', e.currentTarget.id);

            // timeout used for chromium fix
            setTimeout(() => {
                $(e.currentTarget).css('opacity', '0.5');
                $(e.currentTarget).css('cursor', 'grabbing');
                this.calendar.$dropzones.css('z-index', 1000);
            }, 0);
        });

        $event.on('dragend', (e) => {
            $(e.currentTarget).css({
                'opacity': '1',
                'cursor': 'grab'
            });
            this.calendar.$dropzones.removeClass('drag-over');

            const dropTarget = $event.data("drop_target");

            if (dropTarget !== null) {
                const $dropTarget = $(dropTarget);
                let endX = e.clientX;
                const draggedDistanceX = endX - dragStartX;
                const $newTr = $dropTarget.closest('tr');
                let movedSubdivisions = Math.round(draggedDistanceX / this.calendar.tdWidth);
                this.moveBySubdivisions(movedSubdivisions, $newTr);
                event.save();
            } else {
                // note: potential functionality: maybe drop it as far as possible
            }

            event.calendar.$dropzones.css('z-index', -1);
            $event.removeData("drop_target");
        });

        function handleMouseDown(e, resizeStart, $handle) {
            if ($handle.hasClass('overflow-handle')) {
                return;
            }
            isResizing = true;
            startX = e.clientX;
            startTime = event.startDate.getTime();
            endTime = event.endDate.getTime();
            $event.addClass('resizing');

            e.preventDefault();
            e.stopPropagation();

            $('body').css('cursor', 'ew-resize');

            $(document).on('mousemove.resizing', (event) => handleMouseMove(event, resizeStart));
            $(document).on('mouseup.resizing', (event) => handleMouseUp(event));
        }

        function handleMouseMove(e, resizeStart) {
            e.preventDefault();
            e.stopPropagation();
            if (!isResizing) return;

            const currentX = e.clientX;
            const diffX = (currentX - startX);
            let movedSubdivisions = Math.round(diffX / event.calendar.tdWidth);

            event.resizeBySubdivisions(movedSubdivisions, startTime, endTime, resizeStart)
        }

        function handleMouseUp(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isResizing) {
                $event.removeClass('resizing');
                $('body').css('cursor', 'default');

                $(document).off('.resizing');

                event.save();
            }
        }

        this.$event = $event;
    }

    /**
     * render the event in the dom. if render() was already called, just update dom attributes
     * This does not update the rendering in the row, call event.updateRow() after
     */
    renderPartial() {
        if (!this.$event) {
            this.initiate();
        }

        this.renderUpdate();
        this.renderDetailUpdates();
    }

    /**
     * render the event in the dom. if render() was already called, just update dom attributes
     */
    render() {
        this.renderPartial();
        this.updateRow();
    }

    toggleOverflowHandles() {
        const manageHandle = (handleClass, isStartHandle, iconClass, overflowCondition) => {
            const $handle = this.$event.find(`.${handleClass}`);

            if (overflowCondition && $handle.hasClass('resize-handle')) {
                const $overflowIcon = $(`<i class="${iconClass}"></i>`);
                $handle.removeClass('resize-handle').addClass('overflow-handle');
                $handle.append($overflowIcon);
            } else if (!overflowCondition && $handle.hasClass('overflow-handle')) {
                $handle.removeClass('overflow-handle').addClass('resize-handle');
                $handle.find('i').remove();
            }
        };

        manageHandle(
            'handle-start',
            true,
            'fa-solid fa-caret-left',
            this.startDate < this.calendar.startDate
        );

        manageHandle(
            'handle-end',
            false,
            'fa-solid fa-caret-right',
            this.endDate > this.calendar.endDate
        );
    }

    moveBySubdivisions(movedSubdivisions, $newTr = null) {
        const timeOffset = this.calendar.subdivisionsToTime(movedSubdivisions);
        const $previousRow = this.getRow();

        let sameRow = false;
        if ($newTr !== null) {
            const newId = $newTr.data('resource_id');
            if (this.resourceId === newId) {
                sameRow = true;
            }
            this.resourceId = newId;
        }
        this.setStartTime(this.startDate.getTime() + timeOffset);
        this.setEndTime(this.endDate.getTime() + timeOffset);
        this.renderUpdate();
        if (!sameRow) {
            ResourceCalendarEvent.updateRow($previousRow);
        }
        this.updateRow();
    }

    /**
     *
     * @param movedSubdivisions
     * @param startTime
     * @param endTime
     * @param resizeStart - if true, resize start date, if false, resize end date
     */
    resizeBySubdivisions(movedSubdivisions, startTime, endTime, resizeStart = true) {
        const timeOffset = this.calendar.subdivisionsToTime(movedSubdivisions);
        if (resizeStart) {
            // resize at Start
            let newTime;
            if (this.calendar.viewmode.name === 'day') {
                newTime = startTime;
            } else {
                // as we use timestamps to modify the time, we need to adjust the resulting time using TimezoneOffset
                newTime = ResourceCalendarUtils.findBiggestEquidistantValue(startTime, this.calendar.subdivisionInterval) + this.startDate.getTimezoneOffset() * 60000;
            }
            newTime = newTime + timeOffset;
            if (newTime >= this.endDate.getTime()) {
                return;
            }
            this.setStartTime(newTime);
        } else {
            // resize at End
            let newTime;
            if (this.calendar.viewmode.name === 'day') {
                newTime = endTime;
            } else {
                // as we use timestamps to modify the time, we need to adjust the resulting time using TimezoneOffset
                newTime = ResourceCalendarUtils.findBiggestEquidistantValue(endTime, this.calendar.subdivisionInterval) + this.endDate.getTimezoneOffset() * 60000;
            }
            newTime = newTime + timeOffset;
            if (newTime <= this.startDate.getTime()) {
                return;
            }
            this.setEndTime(newTime);
        }
        this.renderUpdate();
        this.updateRow();
    }

    /**
     * safely detach the event element from dom to move it
     */
    detach() {
        if (!this.$event) {
            // dom event doesnt exist
            return;
        }
        this.$event.detach();
    }

    /**
     * removes event element and its jquery data
     */
    remove() {
        if (!this.$event) {
            // dom event doesnt exist
            return;
        }

        let $prevTr = this.$event.closest('tr');

        this.$event.remove();

        ResourceCalendarEvent.updateRow($prevTr)
    }

    updatePositionAndWidth() {
        let endDate = this.endDate;
        let startDate = this.startDate;
        if (this.allDay) {
            startDate = new Date(startDate);
            startDate.setHours(0, 0, 0, 0);
            endDate = new Date(endDate);
            endDate.setDate(endDate.getDate() + 1);
            endDate.setHours(0, 0, 0, 0);
        }

        let diffXStart = this.calendar.getBoundedDiffXFromDate(startDate);
        let diffXEnd = this.calendar.getBoundedDiffXFromDate(endDate);

        this.$event.css({
            left: diffXStart,
            right: diffXEnd
        });

        this.$event.width(diffXEnd - diffXStart)
    }

    moveToCorrectResource() {
        const $tr = this.getRow();
        const $td = $tr.find('td').first();

        this.$event.css({
            top: 0
        });

        $td.append(this.$event);
    }

    getRow() {
        return this.calendar.rows.get(this.resourceId);
    }

    updateRow() {
        ResourceCalendarEvent.updateRow(this.getRow());
    }

    static updateRow($tr) {
        if (!$tr) {
            return;
        }

        const $td = $tr.find('td').first();

        const $events = $td.find('.rc-event');

        const allEvents = $events.map((index, element) => {
            return $(element).data('event');
        }).get().sort((a, b) => {
            if (a.startDate < b.startDate) return -1;
            if (a.startDate > b.startDate) return 1;
            if (a.endDate < b.endDate) return -1;
            if (a.endDate > b.endDate) return 1;
            return 0;
        });

        const processedEvents = [];
        let maxHeightIndex = 0;

        const eventsOverlap = (eventA, eventB) => {
            return eventA.startDate < eventB.endDate && eventA.endDate > eventB.startDate;
        };

        allEvents.forEach(currentEvent => {
            const occupiedIndices = new Set();

            processedEvents.forEach(placedEvent => {
                if (eventsOverlap(currentEvent, placedEvent)) {
                    occupiedIndices.add(placedEvent.heightIndex);
                }
            });

            let newHeightIndex = 0;
            while (occupiedIndices.has(newHeightIndex)) {
                newHeightIndex++;
            }

            currentEvent.heightIndex = newHeightIndex;
            processedEvents.push(currentEvent);

            if (newHeightIndex > maxHeightIndex) {
                maxHeightIndex = newHeightIndex;
            }
        });

        // trIndex is used to avoid expensive reflow
        const trIndex = $tr.data('height_index');
        if ($events.length > 0) {
            if (trIndex !== maxHeightIndex) {
                const singleEventHeight = $events.first().height();
                $tr.height((maxHeightIndex + 2) * singleEventHeight);
                $tr.data('height_index', maxHeightIndex);
            }
        } else {
            if (trIndex > 0) {
                $tr.height(0);
                $tr.data('height_index', 0);
            }
        }

        allEvents.forEach(event => {
            event.shiftToIndex();
        });
    }

    /**
     * renders the event according to the object members
     */
    renderUpdate() {
        this.toggleOverflowHandles();
        this.detach();
        this.updatePositionAndWidth();
        this.moveToCorrectResource();
    }

    /**
     * this only gets called when the event is changed using the detail modal, not on resize/move
     */
    renderDetailUpdates() {
        this.updateTitle();
        this.updateColor();
    }

    /**
     * shift element to the heightIndex to make place for overlapping events
     */
    shiftToIndex() {
        this.heightIndex = Math.max(0, this.heightIndex);
        this.$event.css('top', this.heightIndex * this.$event.outerHeight());
    }

    setStartTime(newTime) {
        this.startDate.setTime(newTime);
    }

    setEndTime(newTime) {
        this.endDate.setTime(newTime);
    }

    setStartDate(newDate) {
        this.setStartTime(newDate.getTime());
    }

    setEndDate(newDate) {
        this.setEndTime(newDate.getTime());
    }

    setTitle(newTitle) {
        this.title = newTitle;
    }

    setColor(newColor) {
        this.color = newColor ?? this.calendar.options.defaultEventColor;
    }

    setAllDay(allDay) {
        this.allDay = allDay ?? false;
    }

    updateTitle() {
        this.$event.attr('title', this.title);
        this.$event.find('span').text(this.title);
    }

    updateColor() {
        this.$event.css('background-color', this.color);
        this.$event.css('border-color', ResourceCalendarUtils.shadeColor(this.color, 1.1));
        this.$event.css('color', ResourceCalendarUtils.getContrastYIQ(this.color));
    }

    save() {
        this.calendar.saveEventFunction(this);
    }

    delete() {
        this.calendar.deleteEventFunction(this);
    }
}

/*
 * Override functions for gtab search
 */

function lmb_extsearchclear() {
    const $filter_reset = $("[name='filter_reset']");
    $filter_reset.val('1');
    const calendar = $(`#${CONTAINER_ID}`).data('calendar');
    calendar.fetchResourcesFunction = LimbasUtils.getFetchResources({});
    calendar.rerender();
    calendar.fetchResourcesFunction = LimbasUtils.getFetchResources();
    $filter_reset.val('');
    $('#searchFilterModal').modal('hide');
}

function LmGs_sendForm(reset) {
    if (reset) {
        lmb_extsearchclear();
    } else {
        let formData = new FormData(document.getElementById('form11'));
        let searchData = {};
        formData.forEach((value, key) => {
            if (key.startsWith('gs[')) {
                searchData[key] = value;
            }
        });
        const calendar = $(`#${CONTAINER_ID}`).data('calendar');
        calendar.fetchResourcesFunction = LimbasUtils.getFetchResources(searchData);
        calendar.rerender();
        calendar.fetchResourcesFunction = LimbasUtils.getFetchResources();
        $('#searchFilterModal').modal('hide');
    }
}