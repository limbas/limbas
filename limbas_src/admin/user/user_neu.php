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
 * ID: 136
 */


/* --- Kopf --------------------------------------- */
?>
<BR><BR>
<TABLE><TR><TD width="20">&nbsp;</TD><TD>

<FORM ACTION="main_admin.php" METHOD=get>
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="user_add">


<TABLE>
<TR><TD width="180"><?=$lang[668]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="username" MAXLENGTH="10"></TD></TR>
<TR><TD><?=$lang[669]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="passwort" MAXLENGTH="10"></TD></TR>
<TR><TD><?=$lang[670]?>:</TD><TD><SELECT name="group_id" STYLE="width:250px;">
<?
$sqlquery = "SELECT GROUP_ID,NAME,LOWER(NAME),BESCHREIBUNG,ERSTDATUM,EDITDATUM FROM LMB_GROUPS WHERE DEL = ".LMB_DBDEF_FALSE." ORDER BY LOWER(NAME)";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$bzm = 1;
while(odbc_fetch_row($rs, $bzm)){
if(odbc_result($rs, "GROUP_ID") == $group_id){$SELECTED="SELECTED";}else{$SELECTED="";}
echo "<OPTION VALUE=\"".odbc_result($rs, "GROUP_ID")."\" $SELECTED>".odbc_result($rs, "NAME");
$bzm++;
}
?>
</SELECT></TD></TR>
<TR><TD><?=$lang[671]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="vorname"></TD></TR>
<TR><TD><?=$lang[672]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="name"></TD></TR>
<TR><TD><?=$lang[673]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="email"></TD></TR>
<TR><TD><?=$lang[674]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="beschreibung"></TD></TR>

<TR><TD>&nbsp;</TD></TR>
<TR><TD></TD><TD><input type="submit" value="<?=$lang[675]?>"></TD></TR>
</TABLE>
</FORM>

</TD></TR></TABLE>
