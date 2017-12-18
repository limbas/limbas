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
 * ID: 134
 */

?>
<BR>
<FORM ACTION="main_admin.php" METHOD="post" name="form1" TARGET="user_main">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" VALUE="setup_user_erg">

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" cellspacing="1" cellpadding="0">
<TR><TD>
<INPUT TYPE="TEXT" style="width:160px;" NAME="search_value" VALUE="<?echo $value;?>">
</TD></TR>

<TR><TD HEIGHT="20">&nbsp;</TD></TR>
<TR><TD><INPUT TYPE="SUBMIT" VALUE="<?=$lang[243]?>"></TD></TR>
</TABLE>


</TD></TR></TABLE></FORM>



