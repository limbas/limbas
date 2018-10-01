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
 * ID: 186
 */
?>

<BR>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form1" OnSubmit="document.cursor = 'wait';">
<input type="hidden" name="action" value="setup_upload_editor">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="tabid" value="<?=$tabid?>">
<TABLE BORDER="0" cellspacing="0" cellpadding="0" WIDTH="100%"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="100%">
<TR><TD COLSPAN="3"><?=$message?></TD></TR>
<TR><TD><B><?=$lang[168]?></B><BR><SELECT NAME="uploadfield" SIZE="25"><OPTION VALUE="ID" SELECTED>ID
<?php
$sqlquery =  "SELECT * FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}	
$bzm = 1;
while(odbc_fetch_row($rs, $bzm)) {
	echo "<OPTION VALUE=\"".odbc_result($rs, "FIELD_NAME")."\">".odbc_result($rs, "FIELD_NAME");
$bzm++;
}
if($sourcepath){$path = $sourcepath;}else{$path = "/TEMP";}
?>
</SELECT></TD><TD>&nbsp;</TD>
<TD VALIGN="TOP"><B><?=$lang[1322]?></B><BR>
<INPUT TYPE="TEXT" SIZE="40" NAME="sourcepath" VALUE="<?=$path?>"><BR><BR>
<INPUT TYPE="SUBMIT" VALUE="<?=$lang[1324]?>" NAME="newupload"><BR><BR>
<?php if($sum_ok){echo "<FONT COLOR=\"green\"><B>$sum_ok</B> files imported!</FONT><BR>";}?>
<?php if($sum_false){echo "<FONT COLOR=\"red\"><B>$sum_false</B> files failed!</FONT><BR>";}?>
</TD>
</TR>
</TABLE>
</TD></TR></TABLE>
</FORM>
<BR><BR>