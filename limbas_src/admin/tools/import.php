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
 * ID: 148
 */
?>

<script language="JavaScript">
function exportconf(){ /* --- Export.conf Ansicht ----------------------------------- */
	if(document.form1.backupdir.value){
		newpage = "BACKUP/" + document.form1.backupdir.value + "/export.conf";
		confview = open(newpage ,"exportconf","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=500,height=700");
	}
}

function LIM_deactivate(elid){
	document.getElementById("tab"+elid).style.display = 'none';
}


function LIM_activate(el,elid){

	LIM_deactivate('1');
	LIM_deactivate('2');
	LIM_deactivate('3');
	LIM_deactivate('4');
	LIM_deactivate('5');
	LIM_deactivate('6');
	
	if(!el){el = document.getElementById('menu'+elid);}
	
	limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
	document.getElementById("tab"+elid).style.display = '';

}


function LIM_setDefault(){
	<?php
	if($kompimport){
		echo "LIM_activate(null,2)";
	}elseif($remoteimport){
		echo "LIM_activate(null,3)";
	}elseif($convertimport){
		echo "LIM_activate(null,5)";
	}elseif($syncimport || $confirm_syncimport){
		echo "LIM_activate(null,6)";
	}
	?>

}

</SCRIPT>


<?
if($imp_msg){
?>
<script language="JavaScript">
alert('<?=$lang[988]?>:\n\n<?echo $imp_msg;?>');
</SCRIPT>
<?}?>

<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_import">
<INPUT TYPE="hidden" NAME="aktivid">
<INPUT TYPE="hidden" NAME="kompimport">
<INPUT TYPE="hidden" NAME="remoteimport">
<INPUT TYPE="hidden" NAME="syncimport">
<INPUT TYPE="hidden" NAME="setup">
<INPUT TYPE="hidden" NAME="hold_id">
<INPUT TYPE="hidden" NAME="del_all">
<INPUT TYPE="hidden" NAME="install">
<INPUT TYPE="hidden" NAME="convertimport">
<INPUT TYPE="hidden" NAME="precheck">
<INPUT TYPE="hidden" NAME="confirm_fileimport">
<INPUT TYPE="hidden" NAME="confirm_remoteimport">
<INPUT TYPE="hidden" NAME="confirm_syncimport">

<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[990]?></TD>
<TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[1006]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[2208]?></TD>
<TD nowrap ID="menu5" OnClick="LIM_activate(this,'5')" class="tabpoolItemInactive"><?=$lang[2240]?></TD>
<TD nowrap ID="menu6" OnClick="LIM_activate(this,'6')" class="tabpoolItemInactive"><?=$lang[2860]?></TD>
<?#<TD nowrap ID="menu4" OnClick="LIM_activate(this,'4')" class="tabpoolItemInactive">lang[2209]</TD>?>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">

<?
/* --- Teilimport ------------------------------- */
if($partimport){
	$display = "display:none;";
}
if(!$txt_terminate){$txt_terminate = $umgvar['csv_delimiter'];}
if(!$txt_enclosure){$txt_enclosure = $umgvar['csv_enclosure'];}
?>

<TABLE ID="tab1" width="100%" cellspacing="2" cellpadding="1" class="tabBody">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="99"><?=$lang[990]?>:</TD></TR>

<TR class="tabBody">
<TD valign="top"><IMG SRC="pic/limbasicon.gif" ALT="<?=$lang[995]?>" Border="0"><input type="radio" NAME="import_typ" VALUE="atm" STYLE="BACKGROUND-COLOR:<?echo $farbschema[WEB8]?>;BORDER:none" checked></TD>
<TD valign="top">&nbsp;<?=$lang[998]?>:&nbsp;</TD>
<TD valign="top"><input type="file" NAME="fileatm"></TD>

<TD align="right" colspan="5">
<?if($import_overwrite == "over" OR !$import_overwrite){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1002]?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="over" <?=$checked?>>&nbsp;&nbsp;<br>
<?if($import_overwrite == "add"){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1003]?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="add" <?=$checked?>>&nbsp;&nbsp;<br>
<?if($import_overwrite == "add_with_ID"){$checked = "checked";}else{$checked = "";}?>
<?=$lang[1003].' ('.$lang[1004].')'?><INPUT TYPE="RADIO" NAME="import_overwrite" VALUE="add_with_ID" <?=$checked?>>&nbsp;&nbsp;
</TD>

</TR>

<TR class="tabBody"><TD colspan="5"><HR></TD></TR>

<TR class="tabBody">
<TD valign="top"><i class="lmb-icon lmb-file-text" ALT="<?=$lang[991]?>" Border="0"></i><input type="radio" ID="import_typ" NAME="import_typ" VALUE="txt" STYLE="BACKGROUND-COLOR:<?echo $farbschema[WEB8]?>;BORDER:none"></TD>
<TD valign="top">&nbsp;<?=$lang[992]?>&nbsp;(csv,gz,zip):&nbsp;</TD>
<TD valign="top"><input type="file" NAME="filetxt"></TD>
<TD valign="top" align="right">
<td>
<table cellpadding="0" cellspacing="0">
    <style>
        .table-spacer-2px { padding: 2px 0; }
    </style>
<tr><td><?=$lang[997]?>&nbsp;</td><td class="table-spacer-2px" align="right"><SELECT NAME="txt_calculate" style="width:50px"><OPTION VALUE="0"><?=$lang[993]?><OPTION VALUE="50" selected>50<OPTION VALUE="100">100<OPTION VALUE="1000">1000<OPTION VALUE="99999999"><?=$lang[994]?></SELECT></td></tr>
<tr><td>field terminated&nbsp;</td><td class="table-spacer-2px"  align="right"><input type="text" NAME="txt_terminate" style="width:50px" value="<?=htmlentities($txt_terminate,ENT_QUOTES,$umgvar["charset"])?>"></td></tr>
<tr><td>field enclosure&nbsp;</td><td class="table-spacer-2px" align="right"><input type="text" NAME="txt_enclosure" style="width:50px" value="<?=htmlentities($txt_enclosure,ENT_QUOTES,$umgvar["charset"])?>"></td></tr>

<tr><td><?=$lang[1003]?>&nbsp;</td><td align="right" class="table-spacer-2px">
	<select name="attach_gtabid"><option value="">
	<?
	$gtab_ = $gtab;
	asort($gtab_['table']);
	foreach ($gtab_["table"] as $key => $value){
		echo "<option value=\"".$gtab_["tab_id"][$key]."\">".$gtab_["table"][$key]."</option>";
	}
	?>
	</select>
</td></tr>

</table>
</td></tr>

<? if(!$import_typ){?>
<TR class="tabBody"><TD colspan="5"><HR></TD></TR>
<TR class="tabBody">
<TD colspan="5"><INPUT TYPE="SUBMIT" NAME="partimport" VALUE="<?=$lang[1005]?>">&nbsp;&nbsp;utf8 en/decode&nbsp;<input name="txt_encode" type="checkbox" <?if($txt_encode){echo "checked";}?>></TD>
</TR>
<?}?>

<TR><TD colspan="5" class="tabFooter"></TD></TR>

</TABLE>




<?/* --- Komplettimport ------------------------------- */?>
<TABLE ID="tab2" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">

<TR class="tabHeader"><TD class="tabHeaderItem"><?=$lang[1006]?>:</TD><TD class="tabHeader"><?=$lang[1007]?></TD><TD class="tabHeader"><?=$lang[1008]?></TD><TD class="tabHeader"></TD></TR>
<TR class="tabBody"><TD VALIGN="TOP">&nbsp;</TD><TD><A HREF="Javascript:exportconf();">export.conf</A></TD><TD><SELECT NAME="backupdir"><OPTION>
<?
if($path = read_dir($umgvar["pfad"]."/BACKUP")){
foreach($path["name"] as $key => $value){
	if($path["typ"][$key] == "file"){
		echo "<OPTION VALUE=\"".$value."\">".$value;
	}
}
}
?>
</TD>
<TD VALIGN="TOP"><INPUT TYPE="button" VALUE="<?=$lang[1009]?>" OnCLick="this.form.kompimport.value=1;this.form.setup.value=1;this.form.hold_id.value=1;this.form.del_all.value=1;this.form.install.value='reinstall';this.form.submit();"></TD></TR>
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



<?/* --- Remote-import ------------------------------- */?>
<TABLE ID="tab3" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="5"><?=$lang[2208]?></TD></TR>
<TR class="tabBody"><TD>File</TD><TD><input type="file" NAME="fileproject" style="width:250px;"></TD></TR>
<TR class="tabBody"><TD>Host</TD><TD><input type="text" name="remote_host" value="<?=stripslashes($remote_host)?>" style="width:250px;"></TD></TR>
<TR class="tabBody"><TD>User</TD><TD><input type="text" name="remote_user" value="<?=stripslashes($remote_user)?>"></TD></TR>
<TR class="tabBody"><TD>Pass</TD><TD><input type="password" name="remote_pass"></TD></TR>
<TR class="tabBody"><TD></TD><TD><input type="button" value="<?=$lang[2243]?>" onclick="document.form1.precheck.value=1;document.form1.remoteimport.value=1;document.form1.submit();"></TD></TR>
<TR class="tabBody"><TD colspan="2" class="tabFooter"></TD></TR>
</TABLE>


<?/* --- Syncronize-import ------------------------------- */?>
<TABLE ID="tab4" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">
<TR><TD colspan="2" class="tabFooter"></TD></TR>
</TABLE>

<?/* --- convert-import ------------------------------- */?>
<TABLE ID="tab5" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="5"><?=$lang[2240]?></TD></TR>
<TR  class="tabBody"><TD><SELECT NAME="tabname"><OPTION></OPTION>
<?php
$sqlquery = "SELECT TABELLE FROM LMB_CONF_TABLES";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(odbc_fetch_row($rs)) {
	$existing_tables[] = lmb_strtoupper(odbc_result($rs, "TABELLE"));
}

$odbc_table = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbc_table["table_name"] as $tkey => $tvalue) {
	if(!in_array(lmb_strtoupper($tvalue),$existing_tables)){
		echo "<OPTION VALUE=\"".$tvalue."\">".$tvalue."</OPTION>\n";
	}
}
?>
</SELECT>&nbsp;<input type="button" value="<?=$lang[2240]?>" onclick="document.form1.convertimport.value=1;document.form1.submit();">
</TD></TR>

<TR><TD colspan="2" class="tabFooter"></TD></TR>

</TABLE>



<?/* --- Sync-import ------------------------------- */?>
<TABLE ID="tab6" width="100%" cellspacing="2" cellpadding="1" class="tabBody" style="display:none;">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="5"><?=$lang[2860]?></TD></TR>
<TR class="tabBody"><TD>File</TD><TD><input type="file" NAME="filesync" style="width:250px;"></TD></TR>
<TR class="tabBody"><TD></TD><TD><input type="button" value="<?=$lang[2243]?>" onclick="document.form1.precheck.value=1;document.form1.syncimport.value=1;document.form1.submit();"></TD></TR>
<TR class="tabBody"><TD colspan="2" class="tabFooter"></TD></TR>
</TABLE>


</div>
</FORM>

<script language="JavaScript">
LIM_setDefault();
</script>

<?
ob_flush();
flush();
?>