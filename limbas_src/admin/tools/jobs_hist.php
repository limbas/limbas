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
 * ID:
 */
?>

<div class="lmbPositionContainerMain">

<TABLE BORDER="0" WIDTH="750" cellspacing="1" cellpadding="2">

<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="8">&nbsp;</TD></TR>
<TR class="tabHeader">
    <TD class="tabHeaderItem">Nr</TD>
    <TD class="tabHeaderItem">Job-Nr</TD>
    <TD class="tabHeaderItem" WIDTH="80">Aktion</TD>
    <TD class="tabHeaderItem" WIDTH="130">Datum</TD>
    <TD class="tabHeaderItem">Status</TD>
    <TD class="tabHeaderItem">Zeit (sek.)</TD>
    <TD class="tabHeaderItem">Indizes</TD>
</TR>


<?php
/*
$sqlquery = "SELECT * FROM JOBS_HIST ORDER BY ERSTDATUM DESC";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(!$maxresult){$maxresult = 30;}
$status = "OK";
$bzm = 1;
while(odbc_fetch_row($rs, $bzm) AND $bzm <= $maxresult) {
	echo "<TR>
	<TD STYLE=\"border:1px solid white\">&nbsp;".odbc_result($rs,"ID")."&nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;".odbc_result($rs,"JOB")."&nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;".odbc_result($rs,"ACTION")."&nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;".get_date(odbc_result($rs,"ERSTDATUM"),2)."&nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;$status ".odbc_result($rs,"MESSAGE")."&nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;".number_format(odbc_result($rs,"USED_TIME"),2,".",".")." &nbsp;</TD>
	<TD STYLE=\"border:1px solid white\">&nbsp;".odbc_result($rs,"INUM")."&nbsp;</TD></TR>";

	$bzm++;
}
*/
?>






</TABLE>
</div>