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
?>

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="setup_quest_select">


<BR><BR>
<TABLE BORDER="0" width="600" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" width="500" cellspacing="0" cellpadding="0">

<TR BGCOLOR="<?= $farbschema['WEB3'] ?>">
<TD><B>Abfrage&nbsp;</B></TD>
<TD><B>&nbsp;erstellt von&nbsp;</B></TD>
<TD ALIGN="CENTER"><B>&nbsp;löschen&nbsp;</B></TD>
</TR>

<TR><TD>&nbsp;</TD></TR>
<?php



$sqlquery = "SELECT * FROM LMB_CONF_TABLES WHERE TYP = 4";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);

while(odbc_fetch_row($rs)) {
        echo "<TR><TD><A HREF=\"main_admin.php?action=setup_quest_sql&quest_id=".odbc_result($rs, "ID")."\">".odbc_result($rs, "QUEST_NAME")."</A></TD>";
        echo "<TD>".$userdat['vorname'][odbc_result($rs, "ERSTUSER")]." ".$userdat['name'][odbc_result($rs, "ERSTUSER")]."</TD>";
        echo "<TD <TD ALIGN=\"CENTER\"><A HREF=\"main_admin.php?action=setup_quest_select&del=1&quest_id=".odbc_result($rs, "ID")."\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A></TD></TR>";
}

?>

<TR><TD>&nbsp;</TD></TR>
<TR><TD COLSPAN="3"><HR></TD></TR>
</TABLE>

<TABLE>
<TR><TD><B>Name</B></TD>
<TD><B>Vorwahl</B></TD>
<TR><TD><INPUT TYPE="TEXT" NAME ="quest_name" SIZE="20"></TD>
<TD><SELECT NAME="quest_typ">
<OPTION VALUE="1">SQL-Entwurfsanquest
</SELECT></TD>
<TD><INPUT TYPE="SUBMIT" VALUE="neue Abfrage" NAME="new_quest"></TD></TR>
</TABLE>

</TD></TR></TABLE>
</FORM>




