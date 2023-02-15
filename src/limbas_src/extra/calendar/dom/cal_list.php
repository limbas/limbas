<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<script type="text/javascript" src="assets/js/extra/calendar/dom/cal.js?v=<?=$umgvar["version"]?>"></script>
	
<Script language="JavaScript">

function getElement(ev) {
	if(window.event && window.event.srcElement){
		el = window.event.srcElement;
	} else {
		el = ev.target;
	}
	return el;
}

function make_bold(ev){
	var el = getElement(ev);
	el.style.fontWeight = "bold";
}
function make_unbold(ev){
	var el = getElement(ev);
	el.style.fontWeight = "normal";
}
function open_details(ev){
	var el = getElement(ev);
	open("main_admin.php?&action=setup_user_tracking&typ=1&userid=<?=$userstat?>&periodid="+el.id,"Tracking","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=400,height=600");
}


var par1;
var par2;
function addEvent(el, evname, func, par_1) {
	par1 = par_1;
	par2 = par_2;
	
	if (el.attachEvent) { // IE
		el.attachEvent("on" + evname, func);
	} else if (el.addEventListener) { // Gecko / W3C
		el.addEventListener(evname, func, true);
	} else {
		el["on" + evname] = func;
	}
}

</Script>



<DIV style="width:100%" ID="list_kontainer"></DIV>