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



<Script language="JavaScript">

function medium_path(med) {
	if(med == 1){
		document.getElementById('file_path').style.display = '';
		document.getElementById('tabe_path').style.display = 'none';
	}else{
		document.getElementById('file_path').style.display = 'none';
		document.getElementById('tabe_path').style.display = '';
	}
}

function device_path(device) {
	if(device == 1){
		document.getElementById('art_path').style.display = '';
		document.getElementById('medium_path').style.display = '';
	}else{
		document.getElementById('art_path').style.display = 'none';
		document.getElementById('medium_path').style.display = 'none';
	}
}

</SCRIPT>


<div class="lmbPositionContainerMain small">

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_backup_man">
<input type="hidden" name="device" VALUE="1">

<?php if($umgvar["backup_default"]){$path1 = $umgvar["backup_default"];}else{$path1 = "localhost:".$umgvar["pfad"]."/BACKUP";}?>

<TABLE class="tabfringe" BORDER="0" cellspacing="2" cellpadding="2">
<TR class="tabBody" ID="art_path"><TD STYLE="width:150px;"><B>Type</B></TD><TD>
<SELECT STYLE="width:350px" NAME="art">
<OPTION VALUE="1" SELECTED>Complete Data Backup
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Incremental Data Backup<?php }?>
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="3">Log Backup<?php }?>
<TR class="tabBody" ID="medium_path"><TD WIDTH="100"><B>Media</B></TD><TD>
<SELECT STYLE="width:350px" NAME="medium" OnChange="medium_path(this.value);">
<OPTION VALUE="1" SELECTED>File
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Tape<?php }?>
</SELECT></TD></TR>
<TR class="tabBody" ID="file_path"><TD WIDTH="100"><B>Target (on db-host)</B></TD><TD><INPUT TYPE="TEXT" NAME="path1" VALUE="<?=$path1?>" STYLE="width:350px"></TD></TR>
<TR class="tabBody" ID="tabe_path" STYLE="display:none"><TD WIDTH="100"><B>Target (dev)</B></TD><TD><INPUT TYPE="TEXT" NAME="path2" VALUE="/dev/rft0" STYLE="width:350px"></TD></TR>


<TR class="tabBody"><TD WIDTH="100">&nbsp;</TD><TD>&nbsp;</TD></TR>
<TR class="tabBody"><TD WIDTH="100">&nbsp;</TD><TD><INPUT TYPE="SUBMIT" NAME="int_backup" VALUE="<?=$lang[2751]?>" class="text-warning" onclick="limbasWaitsymbol(event,1);"></TD></TR>
<TR><TD class="tabFooter" colspan="2"></TR>
</TABLE>
<BR><BR>

<?php
echo $message;
if($device == 2){?>
	<?=$lang[971]?>:<BR>
	<B><?= $result_exp_tabs ?></B> <?=$lang[577]?><BR>
	<B><?= $result_exp_dat ?></B> <?=$lang[972]?><BR>
	<?=$lang[973]?><BR>
<?php }?>


</div>
</FORM>
