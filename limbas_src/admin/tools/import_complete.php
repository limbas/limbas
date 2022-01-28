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
 * ID: 148
 */
?>



<TABLE ID="tab2" width="100%" cellspacing="2" cellpadding="1" class="tabBody importcontainer">

<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[1006]?>:</TD><TD class="tabHeader"><?=$lang[1007]?></TD><TD class="tabHeader"><?=$lang[197]?></TD><TD class="tabHeader"></TD></TR>
<TR class="tabBody"><TD VALIGN="TOP">&nbsp;</TD><TD><SELECT NAME="backupdir"><OPTION>
<?php
if($path = read_dir($umgvar["pfad"]."/BACKUP")){
foreach($path["name"] as $key => $value){
	if($path["typ"][$key] == "file"){
		echo "<OPTION VALUE=\"".$value."\">".$value;
	}
}
}
?>
</TD>
<TD VALIGN="TOP">
<input type="button" VALUE="<?=$lang[1009]?>" onclick="document.form1.import_action.value=1;document.form1.submit();">
</TD></TR>
</TR>

<?php
if($report){
	echo "<TR><TD COLSPAN=\"4\"><HR></TR></TD>";
	echo "<TR><TD COLSPAN=\"4\"><B>REPORT</B></TR></TD>";
	echo "<TR><TD COLSPAN=\"4\">";
	echo $report;
	echo "</TR></TD>";
}
?>

<TR class="tabBody"><TD colspan="5" class="tabFooter"></TD></TR>

</TABLE>


<?php

if($import_action){
    import_complete(1,$txt_encode);
}

?>