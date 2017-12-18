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
 * ID: 198
 */
?>
<Script language="JavaScript">

function flatSelected(calendar) {
	if (calendar.dateClicked) {
		//var d = date.split(".");		
		var y = calendar.date.getFullYear();
		var m = (calendar.date.getMonth()+1);
		var d = calendar.date.getDate();
		
		if(parent.cal_main.califrame){
			parent.cal_main.califrame.document.form1.show_date.value=y+'-'+m+'-'+d;
			parent.cal_main.califrame.send_form();
		}else{
			parent.cal_main.document.form1.show_date.value=y+'-'+m+'-'+d;
			parent.cal_main.send_form();
		}
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

// for Extension use a function lmbCalExtInfo(el)
function lmbCalSetShowInfo(el){
	if(typeof lmbCalExtInfo == 'function'){
		lmbCalExtInfo(el);
	}else{
		//alert(el);
	}
}

</Script>

<TABLE BORDER="0" cellpadding="0" cellspacing="0" WIDTH="100%">
<FORM ACTION="main.php" METHOD="post" name="form1">
<TR><TD style="height:20px;">&nbsp;</TD></TR>
<TR><TD WIDTH="10">&nbsp;</TD><TD VALIGN="TOP" ALIGN="LEFT"><DIV ID="clnd" STYLE="width:210px"></DIV></TD></TR>
<TR><TD WIDTH="10">&nbsp;</TD><TD VALIGN="TOP" ALIGN="LEFT">&nbsp;</TD></TR>
</FORM>
</TABLE>

<DIV ID="CalInfo"></DIV>