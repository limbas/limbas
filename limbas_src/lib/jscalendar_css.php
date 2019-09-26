<?php
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


/*
$farbschema[WEB1] = "#FFFFFF";
$farbschema[WEB2] = "#000000";
$farbschema[WEB3] = "ActiveCaption";
$farbschema[WEB4] = "Workspace";
$farbschema[WEB5] = "ButtonFace";
$farbschema[WEB6] = "Background";
$farbschema[WEB7] = "#EFEFEF";
$farbschema[WEB8] = "#EFEFEF";
$farbschema[WEB9] = "#E5E6E5";
$farbschema[WEB10] = "ActiveCaption";
*/


$cssfile = fopen($umgvar['pfad']."/USER/".$session['user_id']."/jscalendar.css","w+");
fputs($cssfile,



"
.calendar {
  position: relative;
  display: none;
  border-top: 2px solid #fff;
  border-right: 2px solid #000;
  border-bottom: 2px solid #000;
  border-left: 2px solid #fff;
  font-size: {$umgvar['fontsize']}px;
  color: #000;
  cursor: default;
  background: ".$farbschema['WEB7'].";
  font-family: {$umgvar['font']};
}

.calendar table {
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
  font-size: {$umgvar['fontsize']}px;
  color: #000;
  cursor: default;
  background: ".$farbschema['WEB7'].";
  font-family: {$umgvar['font']};
}

/* Header part -- contains navigation buttons and day names. */

.calendar .button { /* \"<<\", \"<\", \">\", \">>\" buttons have this class */
  text-align: center;
  padding: 1px;
  border-top: 1px solid #fff;
  border-right: 1px solid #000;
  border-bottom: 1px solid #000;
  border-left: 1px solid #fff;
}

.calendar .nav {

}

.calendar thead .title { /* This holds the current \"month, year\" */
  font-weight: bold;
  padding: 1px;
  border: 1px solid #000;
  background: ".$farbschema['WEB4'].";
  color: #fff;
  text-align: center;
}

.calendar thead .headrow { /* Row <TR> containing navigation buttons */
}

.calendar thead .daynames { /* Row <TR> containing the day names */
}

.calendar thead .name { /* Cells <TD> containing the day names */
  border-bottom: 1px solid #000;
  padding: 2px;
  text-align: center;
  background: ".$farbschema['WEB8'].";
}

.calendar thead .weekend { /* How a weekend day name shows in header */
  color: #f00;
}

.calendar thead .hilite { /* How do the buttons in header appear when hover */
  border: 1px solid #000;
  padding: 0px;
  background-color: ".$farbschema['WEB3'].";
}

.calendar thead .active { /* Active (pressed) buttons in header */
  padding: 2px 0px 0px 2px;
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
  background-color: ".$farbschema['WEB3'].";
}

/* The body part -- contains all the days in month. */

.calendar tbody .day { /* Cells <TD> containing month days dates */
  width: 2em;
  text-align: right;
  padding: 2px 4px 2px 2px;
}

.calendar table .wn {
  padding: 2px 3px 2px 2px;
  border-right: 1px solid #000;
  background: ".$farbschema['WEB8'].";
}

.calendar tbody .rowhilite td {
  background: ".$farbschema['WEB3'].";
}

.calendar tbody .rowhilite td.wn {
  background: ".$farbschema['WEB7'].";
}

.calendar tbody td.hilite { /* Hovered cells <TD> */
  padding: 1px 3px 1px 1px;
  border: 1px solid #000;
}

.calendar tbody td.marker { /* cells <TD> */
  padding: 1px 3px 1px 1px;
  border: 1px solid red;
}

.calendar tbody td.active { /* Active (pressed) cells <TD> */
  padding: 2px 2px 0px 2px;
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
}

.calendar tbody td.selected { /* Cell showing selected date */
  font-weight: bold;
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
  padding: 2px 2px 0px 2px;
  background: ".$farbschema['WEB3'].";
}

.calendar tbody td.weekend { /* Cells showing weekend days */
  color: #f00;
}

.calendar tbody td.today { /* Cell showing today date */
  font-weight: bold;
  color: #00f;
}

.calendar tbody .disabled { color: #999; }

.calendar tbody .emptycell { /* Empty cells (the best is to hide them) */
  visibility: hidden;
}

.calendar tbody .emptyrow { /* Empty row (some months need less than 6 rows) */
  display: none;
}

/* The footer part -- status bar and \"Close\" button */

.calendar tfoot .footrow { /* The <TR> in footer (only one right now) */
}

.calendar tfoot .ttip { /* Tooltip (status bar) cell <TD> */
  background: ".$farbschema['WEB8'].";
  padding: 1px;
  border: 1px solid #000;
  background: ".$farbschema['WEB4'].";
  color: #fff;
  text-align: center;
}

.calendar tfoot .hilite { /* Hover style for buttons in footer */
  border: 1px solid #000;
  padding: 1px;
  background: ".$farbschema['WEB3'].";
}

.calendar tfoot .active { /* Active (pressed) style for buttons in footer */
  padding: 2px 0px 0px 2px;
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
}

/* Combo boxes (menus that display months/years for direct selection) */

.combo {
  position: absolute;
  display: none;
  width: 4em;
  top: 0px;
  left: 0px;
  cursor: default;
  border-top: 1px solid #fff;
  border-right: 1px solid #000;
  border-bottom: 1px solid #000;
  border-left: 1px solid #fff;
  background: ".$farbschema['WEB3'].";
  font-size: smaller;
  padding: 1px;
}

.combo .label,
.combo .label-IEfix {
  text-align: center;
  padding: 1px;
}

.combo .label-IEfix {
  width: 4em;
}

.combo .active {
  background: ".$farbschema['WEB8'].";
  padding: 0px;
  border-top: 1px solid #000;
  border-right: 1px solid #fff;
  border-bottom: 1px solid #fff;
  border-left: 1px solid #000;
}

.combo .hilite {
  background: #048;
  color: #fea;
}

.calendar td.time {
  border-top: 1px solid #000;
  padding: 1px 0px;
  text-align: center;
  background-color: ".$farbschema['WEB8'].";
}

.calendar td.time .hour,
.calendar td.time .minute,
.calendar td.time .ampm {
  padding: 0px 3px 0px 4px;
  border: 1px solid #889;
  font-weight: bold;
  background-color: #fff;
}

.calendar td.time .ampm {
  text-align: center;
}

.calendar td.time .colon {
  padding: 0px 2px 0px 3px;
  font-weight: bold;
}

.calendar td.time span.hilite {
  border-color: #000;
  background-color: #766;
  color: #fff;
}

.calendar td.time span.active {
  border-color: #f00;
  background-color: #000;
  color: #0f0;
}

");

fclose($cssfile);

?>