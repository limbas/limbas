<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

?>

<br><br>

<input type="date" onchange="flatSelected(this.value)">


<Script language="JavaScript">

// ------------------- fester Menükalender ----------------


function flatSelected(calendar) {

		var d = calendar.split("-");

		var y = d[0];
        var m = d[1];
        var d = d[2];

		parent.cal_main.document.form1.y.value = y;
		parent.cal_main.document.form1.m.value = (m-1);
		parent.cal_main.document.form1.d.value = d;
		parent.cal_main.view('day','kontainer','tag');
		parent.cal_main.call(y,m,d,null);
}


</Script>
