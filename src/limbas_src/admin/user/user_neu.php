<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




/* --- Kopf --------------------------------------- */
?>
<BR><BR>
<TABLE><TR><TD width="20">&nbsp;</TD><TD>

<FORM ACTION="main_admin.php" METHOD=get>
<input type="hidden" name="action" value="user_add">


<TABLE>
<TR><TD width="180"><?=$lang[519]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="username" MAXLENGTH="10"></TD></TR>
<TR><TD><?=$lang[141]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="passwort" MAXLENGTH="10"></TD></TR>
<TR><TD><?=$lang[561]?>:</TD><TD><SELECT name="group_id" STYLE="width:250px;">
<?php
$sqlquery = "SELECT GROUP_ID,NAME,LOWER(NAME),BESCHREIBUNG,ERSTDATUM,EDITDATUM FROM LMB_GROUPS WHERE DEL = ".LMB_DBDEF_FALSE." ORDER BY LOWER(NAME)";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)){
if(lmbdb_result($rs, "GROUP_ID") == $group_id){$SELECTED="SELECTED";}else{$SELECTED="";}
echo "<OPTION VALUE=\"".lmbdb_result($rs, "GROUP_ID")."\" $SELECTED>".lmbdb_result($rs, "NAME");
}
?>
</SELECT></TD></TR>
<TR><TD><?=$lang[142]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="vorname"></TD></TR>
<TR><TD><?=$lang[672]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="name"></TD></TR>
<TR><TD><?=$lang[612]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="email"></TD></TR>
<TR><TD><?=$lang[126]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="beschreibung"></TD></TR>

<TR><TD>&nbsp;</TD></TR>
<TR><TD></TD><TD><input type="submit" value="<?=$lang[571]?>"></TD></TR>
</TABLE>
</FORM>

</TD></TR></TABLE>
