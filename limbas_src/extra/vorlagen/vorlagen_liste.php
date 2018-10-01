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
 * ID: 55
 */
?>

<BR><BR>
<TABLE BORDER="0" width="600" cellspacing="0" cellpadding="0"><TR><TD width="20">&nbsp;</TD><TD>

<?php
/* --- Kopf --------------------------------------- */ ?>
<TABLE BORDER="0" width=500" cellspacing="1" cellpadding="0">

<FORM ACTION="main.php" METHOD=post NAME=form1>
<input type="hidden" name="action" value="user_vorlage">

<TR BGCOLOR="<?=$farbschema['WEB3'];?>"><TD><?=$lang[4]?></TD><TD><?=$lang[126]?></TD><TD><?=$lang[197]?></TD><TD><?=$lang[843]?></TD><TD><?=$lang[160]?></TD></TR>
<?php
$bzm = 0;
while($result_vorlagen['id'][$bzm]){
if($BGCOLOR == $farbschema['WEB7']){$BGCOLOR = $farbschema['WEB8'];} else {$BGCOLOR = $farbschema['WEB7'];}
        echo "<TR BGCOLOR=\"$BGCOLOR\"><TD><A HREF=\"main.php?action=user_vorlage_view&beschreibung=".urlencode($result_vorlagen['beschreibung'][$bzm])."&ID=".$result_vorlagen['id'][$bzm]."\" TARGET=\"richtexteditor\">".$result_vorlagen['name'][$bzm]."</A></TD>";
        echo "<TD>".$result_vorlagen['beschreibung'][$bzm]."</TD>";
        echo "<TD>".$result_vorlagen['erstdatum'][$bzm]."</TD>";
        echo "<TD>&nbsp;<A HREF=\"main.php?action=user_vorlage_change&beschreibung=".urlencode($result_vorlagen['beschreibung'][$bzm])."&ID=".$result_vorlagen['id'][$bzm]."\" TARGET=\"richtexteditor\"><i class=\"lmb-icon lmb-edit\" BORDER=\"0\" TITLE=\"$lang[843]\"></i></A>&nbsp;</TD>";
        echo "<TD>&nbsp;<A HREF=\"main.php?action=user_vorlage&del=1&ID=".$result_vorlagen['id'][$bzm]."\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A>&nbsp;</TD></TR>";
$bzm++;
}
?>
<TR><TD COLSPAN="6"><HR></TD></TR>
<TR><TD><INPUT TYPE="TEXT" SIZE="15" NAME="name"></TD><TD><INPUT TYPE="TEXT" SIZE="20" NAME="beschreibung"></TD><TD><INPUT TYPE="SUBMIT" NAME="new" VALUE="<?=$lang[200]?>"></TD></TR>
</FORM>

</TABLE>
</TD></TR></TABLE>
