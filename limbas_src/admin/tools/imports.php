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


<script language="JavaScript">

function LIM_activate(el,elid){

	document.form1.aktivid.value=elid;
	document.form1.submit();

}

function LIM_setDefault(aktivid){
     el = document.getElementById('menu'+aktivid);
     limbasSetLayoutClassTabs(el,'tabpoolItemInactive','tabpoolItemActive');
}

<?php
if(!$aktivid){$aktivid = 1;}

if($imp_msg){
?>
alert('<?=$lang[988]?>:\n\n<?= $imp_msg ?>');
<?php }?>

</script>

<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_import">

<INPUT TYPE="hidden" NAME="aktivid"  VALUE="<?=$aktivid?>">
<INPUT TYPE="hidden" NAME="import_action">
<INPUT TYPE="hidden" NAME="kompimport">
<INPUT TYPE="hidden" NAME="remoteimport">
<INPUT TYPE="hidden" NAME="syncimport">
<INPUT TYPE="hidden" NAME="odbcimport" VALUE="<?=$odbcimport?>">
<INPUT TYPE="hidden" NAME="setup">
<INPUT TYPE="hidden" NAME="del_all">
<INPUT TYPE="hidden" NAME="install">
<INPUT TYPE="hidden" NAME="convertimport">
<INPUT TYPE="hidden" NAME="precheck">
<INPUT TYPE="hidden" NAME="hold_id" VALUE="1">
<INPUT TYPE="hidden" NAME="confirm_fileimport">
<INPUT TYPE="hidden" NAME="confirm_syncimport">
<INPUT TYPE="hidden" NAME="template">
<INPUT TYPE="hidden" NAME="odbc[odbc_table]" VALUE="<?=$odbc['odbc_table']?>">


<DIV class="lmbPositionContainerMainTabPool">

<TABLE class="tabpool" BORDER="0" width="700" cellspacing="0" cellpadding="0"><TR><TD>

<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR class="tabpoolItemTR">
<TD nowrap ID="menu1" OnClick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[990]?></TD>
<TD nowrap ID="menu2" OnClick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[1006]?></TD>
<TD nowrap ID="menu5" OnClick="LIM_activate(this,'5')" class="tabpoolItemInactive"><?=$lang[2240]?></TD>
<TD nowrap ID="menu3" OnClick="LIM_activate(this,'3')" class="tabpoolItemInactive"><?=$lang[2208]?></TD>
<TD nowrap ID="menu6" OnClick="LIM_activate(this,'6')" class="tabpoolItemInactive"><?=$lang[2860]?></TD>
<TD nowrap ID="menu7" OnClick="LIM_activate(this,'7')" class="tabpoolItemInactive">ODBC Import</TD>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>

</TD></TR>

<TR><TD class="tabpoolfringe">

<?php

ob_flush();

if($aktivid == 1){
    require_once('admin/tools/import_part.php');
}elseif($aktivid == 2){
    require_once('admin/tools/import_complete.php');
}elseif($aktivid == 5){
    require_once('admin/tools/import_convert.php');
}elseif($aktivid == 3){
    require_once('admin/tools/import_project.php');
}elseif($aktivid == 6){
    require_once('admin/tools/import_syncs.php');
}elseif($aktivid == 7){
    require_once('admin/tools/import_odbc.php');
}




?>

</TD></TR>
<TR class="tabBody"><TD colspan="2" class="tabFooter"></TD></TR>
</TABLE>
</FORM>


<script language="JavaScript">
LIM_setDefault('<?=$aktivid?>');
</script>
