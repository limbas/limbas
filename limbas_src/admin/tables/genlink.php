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
 * ID: 107
 */

/* --- Kopf --------------------------------------- */
echo "<CENTER><B><U>$lang[916]</U></B><BR><BR></CENTER>";

?>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_genlink">
<input type="hidden" name="fieldid" value="<?= $fieldid ?>">
<input type="hidden" name="atid" value="<?= $atid ?>">
<input type="hidden" name="tab" value="<?= $tab ?>">
<input type="hidden" name="typ" value="<?= $typ ?>">

<CENTER>
<TABLE BORDER="0" cellspacing="0" cellpadding="2">
<?php
/* --- Ergebnisliste --------------------------------------- */
        echo "<TR><TD>$lang[917]<BR>zB: http://www/nixgwieswies.de/<B><U>?ID</U></B>...<BR>$lang[918].</TD></TR>";
        echo "<TR><TD><INPUT TYPE=\"text\" SIZE=\"45\" NAME=\"argument\" VALUE=\"".$result_genlink["genlink"]."\"></TD></TR>";
        echo "<TR><TD><INPUT TYPE=\"submit\" VALUE=\"$lang[522]\" name=\"genlink_change\"></TD></TR>";

?>

</TABLE>
</CENTER>
</FORM>