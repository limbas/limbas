<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID: 191
 */
?>

<br>

<script type="text/javascript" src="extra/calendar/dom/cal.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript" src="extern/jscalendar/calendar.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript" src="extern/jscalendar/lang/calendar-de.js?v=<?=$umgvar["version"]?>"></script>
<style type="text/css">@import url(extern/jscalendar/jscalendar.css?v=<?=$umgvar["version"]?>);</style>

<Script language="JavaScript">


// ------------------- fester Menükalender ----------------


function flatSelected(calendar) {
	if (calendar.dateClicked) {
		//var d = date.split(".");		
		var y = calendar.date.getFullYear();
		var m = calendar.date.getMonth();
		var d = calendar.date.getDate();
		parent.cal_main.document.form1.y.value = y;
		parent.cal_main.document.form1.m.value = m;
		parent.cal_main.document.form1.d.value = d;
		parent.cal_main.view('day','kontainer','tag');
		parent.cal_main.call(y,m,d,null);
	}
}

function showFlatCalendar() {
  var par = document.getElementById("clnd");
  var cal = new Calendar(true, null, flatSelected);
  cal.weekNumbers = true;
  cal.setDateFormat("%d.%m.%Y");
  cal.create(par);
  cal.show();
}


</Script>