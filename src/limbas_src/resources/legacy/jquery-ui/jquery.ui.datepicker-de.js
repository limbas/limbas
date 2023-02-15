/* German initialisation for the jQuery UI date picker plugin. */

jQuery(function($){
	$.datepicker.regional['de'] = {
		closeText: 'Schlie&szlig;en',
		prevText: '&#x3C;zurück',
		nextText: 'Vor&#x3E;',
		currentText: 'heute',
		monthNames: ['Januar','Februar','März','April','Mai','Juni',
		'Juli','August','September','Oktober','November','Dezember'],
		monthNamesShort: ['Jan','Feb','M&auml;r','Apr','Mai','Jun',
		'Jul','Aug','Sep','Okt','Nov','Dez'],
		dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
		dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		weekHeader: 'KW',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['de']);

	$.timepicker.regional['de'] = {
		timeOnlyTitle: '',
		timeText: 'Zeit',
		hourText: 'Stunde',
		minuteText: 'Minute',
		secondText: 'Secunde',
		millisecText: 'Millisekunde',
		timezoneText: 'Zeitzone',
		currentText: 'Heute',
		closeText: 'Schlie&szlig;en',
		isRTL: false
	};
	$.timepicker.setDefaults($.timepicker.regional['de']);

})


