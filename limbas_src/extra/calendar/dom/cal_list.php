<?php
/*
 * Copyright notice
 * (c) 1998-2016 Limbas GmbH - Axel westhagen (support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.0
 */

/*
 * ID: 193
 */
?>

<script type="text/javascript" src="extra/calendar/dom/cal.js"></script>
	
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


<TABLE BORDER="0" cellpadding="0" cellspacing="0" width="100%"><TD WIDTH="15">&nbsp;</TD><TD>
<DIV style="width:100%" ID="list_kontainer"></DIV>
</TD></TR></TABLE>