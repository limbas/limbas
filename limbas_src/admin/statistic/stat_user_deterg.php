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
 * ID:
 */
?>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" cellspacing="1" cellpadding="1">
<TR><TD COLSPAN="99">&nbsp;</TD></TR>

<TR><TD COLSPAN="99" HEIGHT="20" STYLE="color:blue;"><B><?= $user ?></B></TD></TR>

<TR BGCOLOR="<?= $farbschema['WEB3'] ?>">
<TD><B>Nr&nbsp;&nbsp;</B></TD>
<TD><B>Login&nbsp;&nbsp;</B></TD>
<TD><B>Logout&nbsp;&nbsp;</B></TD>
<TD><B>Dauer&nbsp;&nbsp;</B></TD>
<TD><B>IP_Adresse&nbsp;&nbsp;</B></TD>
<TD><B>Host&nbsp;&nbsp;</B></TD>
</TR>


<?php
if(convert_date($diag_von)){$where = "AND LOGIN_DATE >= '".convert_date($diag_von)."'";}
if(convert_date($diag_bis)){$where .= " AND LOGIN_DATE <= '".convert_date($diag_bis)."'";}

$sqlquery =  "SELECT DISTINCT ID,LOGIN_DATE, UPDATE_DATE, IP, HOST, TIMEDIFF(LOGIN_DATE,UPDATE_DATE) DAUER FROM LMB_HISTORY_USER WHERE USERID = $ID $where ORDER BY LOGIN_DATE";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
        if($BGCOLOR == $farbschema['WEB7']){$BGCOLOR = $farbschema['WEB8'];} else {$BGCOLOR = $farbschema['WEB7'];}
        echo "<TR BGCOLOR=\"$BGCOLOR\">";
        echo"<TD>$bzm</TD>";
        echo"<TD>".get_date(lmbdb_result($rs,"LOGIN_DATE"),2)."&nbsp;&nbsp;</TD>";
        echo"<TD>".get_date(lmbdb_result($rs,"UPDATE_DATE"),2)."&nbsp;&nbsp;</TD>";
        echo"<TD>".lmb_substr(lmbdb_result($rs,"DAUER"),0,19)."&nbsp;&nbsp;</TD>";
        echo"<TD>".lmbdb_result($rs,"IP")."&nbsp;&nbsp;</TD>";
        echo"<TD>".lmbdb_result($rs,"HOST")."&nbsp;&nbsp;</TD>";
        echo"</TR>";
}


?>





</TABLE>
</TD></TR></TABLE>
