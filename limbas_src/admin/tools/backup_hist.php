<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */
?>
<Script language="JavaScript">
function f_3(PARAMETER) {
	document.form1.action.value = PARAMETER;
	document.form1.submit();
}
</SCRIPT>

<div class="lmbPositionContainerMain">

<TABLE class="tabfringe" BORDER="0" WIDTH="1100" cellspacing="1" cellpadding="2">

<TR class="tabHeader">
    <TD class="tabHeaderItem">Nr</TD>
    <TD class="tabHeaderItem" WIDTH="80">Action</TD>
    <TD class="tabHeaderItem" WIDTH="130">Date</TD>
    <TD class="tabHeaderItem">Status</TD>
    <TD class="tabHeaderItem">Media</TD>
    <TD class="tabHeaderItem" WIDTH="70">Size</TD>
    <TD class="tabHeaderItem">Server</TD>
    <TD class="tabHeaderItem">Target</TD>
</TR>
<?php

$sqlquery = "SELECT * FROM LMB_HISTORY_BACKUP ORDER BY ERSTDATUM DESC";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
if(!$maxresult){$maxresult = 100;}
$status = "OK";
$bzm = 1;
while(odbc_fetch_row($rs, $bzm) AND $bzm <= $maxresult) {
	
	if(!odbc_result($rs,"RESULT")){$bgColor = "#FBE3BF";$status = "FALSE";}
	elseif(odbc_result($rs,"ACTION") == "SAVE DATA"){$bgColor = "#D0DEEF";}
	elseif(odbc_result($rs,"ACTION") == "SAVE LOG"){$bgColor = "#CEDBF7";}
	elseif(odbc_result($rs,"ACTION") == "SAVE PAGES"){$bgColor = "#E3ECF5";}
	else{$bgColor = "#FFFFFF";}

	$color = lmbSuggestColor($bgColor);

	echo "<TR style=\"background-color:$bgColor; color:$color;\">
	<TD>&nbsp;".odbc_result($rs,"ID")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"ACTION")."&nbsp;</TD>
	<TD>&nbsp;".get_date(odbc_result($rs,"ERSTDATUM"),2)."&nbsp;</TD>
	<TD>&nbsp;$status ".odbc_result($rs,"MESSAGE")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"MEDIUM")."&nbsp;</TD>
	<TD>&nbsp;".file_size(odbc_result($rs,"SIZE")*1024)."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"SERVER")."&nbsp;</TD>
	<TD>&nbsp;".odbc_result($rs,"LOCATION")."&nbsp;</TD>";
	$bzm++;
}
?>
<TR><TD class="tabFooter" colspan="10"></TR>
</TABLE>

</div>