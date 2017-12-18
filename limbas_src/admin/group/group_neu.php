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
 * ID: 119
 */
?>

<DIV class="lmbPositionContainerMain small">
<TABLE BORDER="0" cellspacing="1" cellpadding="0"><tr><td>

<?
if(!$group_id){$group_id = 1;}
$sqlquery = "SELECT GROUP_ID,NAME,LEVEL FROM LMB_GROUPS WHERE GROUP_ID = $group_id";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs){$commit = 1;}

/* --- Gruppen-Neu-Formular 1 Schritt --------------------------------------- */
?>
<FORM ACTION="main_admin.php" METHOD="post">
<input type="hidden" name="action" value="setup_group_add">


<TABLE>
<TR><TD width="180"><?=$lang[897]?>:</TD><TD><SELECT NAME="group_level" STYLE="width:250px;"><OPTION value="0">root
<?
viewgrouptree(0,"",0);
?>

</TD></TR>

<TR><TD><?=$lang[569]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="group_name"></TD></TR>
<TR><TD><?=$lang[570]?>:</TD><TD><INPUT TYPE="TEXT" STYLE="width:250px;" NAME="group_beschr"></TD></TR>

<TR><TD colspan=2><hr></TD></TR>
<TR><TD colspan=2><?=$lang[2585]?></TD></TR>
<TR><TD><?=$lang[2129]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="use_parent_tabsettings" VALUE="1" checked></TD></TR>
<TR><TD><?=$lang[2130]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="use_parent_filesettings" VALUE="1" checked></TD></TR>
<TR><TD><?=$lang[2131]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="use_parent_menusettings" VALUE="1" checked></TD></TR>
<TR><TD><?=$lang[2306]?>:</TD><TD><INPUT TYPE="CHECKBOX" NAME="use_parent_formsettings" VALUE="1" checked></TD></TR>

<TR><TD>&nbsp;</TD></TR>
<TR><TD></TD><TD>
<INPUT TYPE="hidden" NAME="level" value="<?=$group_id;?>">
<INPUT TYPE="submit" value="<?=$lang[571]?>"></TD></TR>

</TABLE>
</FORM>


</td></tr></table></div>