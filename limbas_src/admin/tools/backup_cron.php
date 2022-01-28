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
<table class="tabfringe" border="0" cellspacing="0" cellpadding="1"><tr><td>

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="action" VALUE="setup_backup_cron">
<input type="hidden" name="device" VALUE="1">

<?php if($umgvar['backup_default']){$path1 = $umgvar['backup_default'];}else{$path1 = "localhost:".$umgvar['pfad']."/BACKUP";}?>
<TABLE BORDER="0" cellspacing="2" cellpadding="2">
<TR ID="art_path"><TD STYLE="width:150px;"><B>Type</B></TD><TD>
<SELECT STYLE="width:350px" NAME="art">
<OPTION VALUE="1" SELECTED>Complete Data Backup
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Incremental Data Backup<?php }?>
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="3">Log Backup<?php }?>
</SELECT></TD></TR>
<TR ID="medium_path"><TD WIDTH="100"><B>Media</B></TD><TD>
<SELECT STYLE="width:350px" NAME="medium" OnChange="medium_path(this.value);">
<OPTION VALUE="1" SELECTED>File
<?php if($DB["DBVENDOR"] == "maxdb76"){?><OPTION VALUE="2">Tape<?php }?>
</SELECT></TD></TR>
<TR ID="file_path"><TD WIDTH="100"><B>Target</B> (on db-host)</TD><TD><INPUT TYPE="TEXT" NAME="path1" VALUE="<?=$path1?>" STYLE="width:350px"></TD></TR>
<TR ID="tabe_path" STYLE="display:none"><TD WIDTH="100"><B>Ziel</B> (dev)</TD><TD><INPUT TYPE="TEXT" NAME="path2" VALUE="/dev/rft0" STYLE="width:350px"></TD></TR>
<TR ID="alive"><TD WIDTH="100"><B>Alive</B> (days)</TD><TD><INPUT TYPE="TEXT" NAME="alive" VALUE="31" STYLE="width:30px"></TD></TR>
<TR><TD COLSPAN="2">&nbsp;</TD></TR>

<TR><TD WIDTH="100" VALIGN="TOP"><B>Timeperiod</B> (cron)</TD><TD>
<TABLE BORDER="0" cellspacing="1" cellpadding="2">
<TR class="tabHeader">
    <TD class="tabHeaderItem">minute </TD>
    <TD class="tabHeaderItem">hour </TD>
    <TD class="tabHeaderItem">day of month </TD>
    <TD class="tabHeaderItem">month </TD>
    <TD class="tabHeaderItem">day of week </TD>
</TR>
<TR><TD><INPUT TYPE="TEXT" NAME="cron_1" VALUE="0" STYLE="width:60px"></TD><TD><INPUT TYPE="TEXT" NAME="cron_2" VALUE="1" STYLE="width:60px"></TD><TD><INPUT TYPE="TEXT" VALUE="*" NAME="cron_3" STYLE="width:60px"></TD><TD><INPUT TYPE="TEXT" NAME="cron_4" VALUE="*" STYLE="width:60px"></TD><TD><INPUT TYPE="TEXT" NAME="cron_5" VALUE="*" STYLE="width:60px"></TD></TR>
</TABLE></TD></TR>
<TR><TD WIDTH="100">&nbsp;</TD><TD>&nbsp;</TD></TR>
<TR><TD WIDTH="100">&nbsp;</TD><TD><INPUT TYPE="SUBMIT" NAME="cron_backup" VALUE="add Job!" class="text-warning"></TD></TR>
</TABLE>

<BR><BR>
<?php
if($message){echo implode("<BR>",$message);}
if($device == 2){?>
	<?=$lang[971]?>:<BR>
	<B><?= $result_exp_tabs ?></B> <?=$lang[577]?><BR>
	<B><?= $result_exp_dat ?></B> <?=$lang[972]?><BR>
	<?=$lang[973]?><BR>
<?php }?>





</FORM>




<TABLE BORDER="0" WIDTH="600" cellspacing="1" cellpadding="2" class="tabfringe">
<TR class="tabHeader"><TD  class="tabHeaderItem" COLSPAN="8">&nbsp;</TD></TR>
<TR class="tabHeader">
    <TD class="tabHeaderItem">Nr</TD>
    <TD class="tabHeaderItem">Type</TD>
    <TD class="tabHeaderItem">Time</TD>
    <TD class="tabHeaderItem">Desc</TD>
    <TD class="tabHeaderItem">Activ</TD>
    <TD class="tabHeaderItem">Alive</TD>
    <TD class="tabHeaderItem">delete</TD>
</TR>
<?php

$sqlquery = "SELECT * FROM LMB_CRONTAB WHERE KATEGORY = 'BACKUP' ORDER BY ERSTDATUM";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}
while(lmbdb_fetch_row($rs)){
	if(lmbdb_result($rs,"KATEGORY") == "BACKUP"){$color = "#94AEEF";}
	elseif(lmbdb_result($rs,"KATEGORY") == "INDEX"){$color = "#FF9294";}
	
	echo "<TR BGCOLOR=\"$color\" class=\"tabBody\">
	<TD>&nbsp;".lmbdb_result($rs,"ID")."&nbsp;</TD>
	<TD>&nbsp;".lmbdb_result($rs,"KATEGORY")."&nbsp;</TD>
	<TD>&nbsp;".lmbdb_result($rs,"START")."&nbsp;</TD>
	<TD>&nbsp;".lmbdb_result($rs,"DESCRIPTION")."&nbsp;</TD>
	<TD>&nbsp;".lmbdb_result($rs,"ACTIV")."&nbsp;</TD>
	<TD>&nbsp;".lmbdb_result($rs,"ALIVE")."&nbsp;days</TD>
	<TD ALIGN=\"CENTER\">&nbsp;<A HREF=\"main_admin.php?action=setup_backup_cron&del_job=".lmbdb_result($rs,"ID")."\"><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A>&nbsp;</TD></TR>";
	$cronvalue[] = lmbdb_result($rs,"START")."\twebuser (php \"".$umgvar['pfad']."/cron.php\" < /bin/echo ".lmbdb_result($rs,"ID").")";
}

?>

<tr><td colspan="7" class="tabBody"></td></tr>
</TABLE>
<BR><BR>
<TABLE BORDER="0" cellspacing="0" cellpadding="2" STYLE="collapse:collapse">
<TR class="tabHeader"><TD><U>Crontab Value</U></TD></TR>
<TR ID="crontab" BGCOLOR="<?=$farbschema['WEB3']?>"><TD><TEXTAREA STYLE="font-size:9px;width:600px;height:100px;overflow:hidden;">
<?php
if($cronvalue){
	foreach($cronvalue as $key => $value){
		echo str_replace(";"," ",$value)."\n";
	}
}
?>
</TEXTAREA></TD></TR>
</TABLE>
</div>
</FORM>

</td></tr></table>