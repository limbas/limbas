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
 * ID: 211
 */
?>
<div class="lmbPositionContainerMain small">

<TABLE class="tabfringe" BORDER="0" WIDTH="750" cellspacing="1" cellpadding="2">

<TR class="tabHeader">
    <TD class="tabHeaderItem">Nr</TD>
    <TD class="tabHeaderItem">Job-Nr</TD>
    <TD class="tabHeaderItem" WIDTH="80">Aktion</TD>
    <TD class="tabHeaderItem" WIDTH="130">Datum</TD>
    <TD class="tabHeaderItem">Status</TD>
    <TD class="tabHeaderItem">Zeit (sek.)</TD>
    <TD class="tabHeaderItem">Indizes</TD>
    <TD></TD>
</TR>

<?php
$sqlquery = "SELECT * FROM LMB_INDIZE_HISTORY ORDER BY ERSTDATUM DESC";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(!$maxresult){$maxresult = 30;}
$status = "OK";
$bzm = 1;
while(odbc_fetch_row($rs, $bzm) AND $bzm <= $maxresult) {

	if(!odbc_result($rs,"RESULT")){$color = "#FF9294";$status = "FALSE";}
	elseif(!odbc_result($rs,"INUM")){$color1 = "";}
	elseif(odbc_result($rs,"INUM") > 0 AND odbc_result($rs,"INUM") <= 1000){$color1 = "#CEE8D8";}
	elseif(odbc_result($rs,"INUM") > 1000 AND odbc_result($rs,"INUM") <= 10000){$color1 = "#97DAB1";}
	elseif(odbc_result($rs,"INUM") > 10000 AND odbc_result($rs,"INUM") <= 100000){$color1 = "#78BE93";}
	elseif(odbc_result($rs,"INUM") > 100000 AND odbc_result($rs,"INUM") <= 500000){$color1 = "#F5F67C";}
	elseif(odbc_result($rs,"INUM") > 500000 AND odbc_result($rs,"INUM") <= 1000000){$color1 = "#FCC457";}
	elseif(odbc_result($rs,"INUM") > 1000000){$color1 = "#FC6A57";}
	
	echo "<TR BGCOLOR=\"$color1\">
	<TD>&nbsp;".odbc_result($rs,"ID")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"JOB")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"ACTION")."&nbsp;</TD>
	<TD>&nbsp;".get_date(odbc_result($rs,"ERSTDATUM"),2)."&nbsp;</TD>
	<TD>&nbsp;$status ".odbc_result($rs,"MESSAGE")."&nbsp;</TD>
	<TD>&nbsp;".number_format((odbc_result($rs,"USED_TIME")/60),1,".",".")." &nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"JNUM")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"INUM")."&nbsp;</TD></TR>";

	$bzm++;
}
?>


<TR><TD class="tabFooter" colspan="9"></TR>
</TABLE>
</div>