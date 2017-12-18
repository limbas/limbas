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
 * ID: 186
 */


if(!$grouping_fields["id"]){
	$grouping_fields["sort"] = array();
	$grouping_fields["id"] = array();
}


if($newupload){

	asort($grouping_fields["sort"]);
	foreach ($grouping_fields["sort"] as $key => $value){
		if($grouping_fields["id"][$key]){
			$gfv[] = $grouping_fields["id"][$key];
		}
	}

	
	if($gfv){$gf = implode(";",$gfv);}
	$sqlquery =  "UPDATE LMB_CONF_FIELDS SET ARGUMENT = '".parse_db_string($gf,500)."' WHERE TAB_ID = $tabid AND FIELD_ID = $fieldid";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	if(!$rs) {$commit = 1;}
}



?>



<BR>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="action" value="setup_grouping_editor">
<input type="hidden" name="fieldid" value="<?=$fieldid;?>">
<input type="hidden" name="tabid" value="<?=$tabid?>">


<TABLE BORDER="0" cellspacing="0" cellpadding="0" WIDTH="100%"><TR><TD width="20">&nbsp;</TD><TD>
<TABLE BORDER="0" CELLPADDING="0" CELLSPACING="0" WIDTH="90%">
<TR><TD><b><?=$lang[1321]?></TD><TD ALIGN="right"><b><?=$lang[2378]?></TD><TD ALIGN="right"><b><?=$lang[2379]?></TD></TR>
<TR><TD colspan="3"><HR></TD></TR>

<?
$sqlquery =  "SELECT ARGUMENT FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_ID = $fieldid";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}	
if(odbc_fetch_row($rs, 1)) {
	$gf = explode(";",odbc_result($rs, "ARGUMENT"));
}

$sqlquery =  "SELECT FIELD_ID,FIELD_NAME,ARGUMENT FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE < 100 ORDER BY SORT";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
if(!$rs) {$commit = 1;}	
$bzm = 1;
while(odbc_fetch_row($rs, $bzm)) {
	$key = array_search(odbc_result($rs, "FIELD_ID"),$gf);
	if($key !== FALSE){$CHECKED = "CHECKED";$sortvalue=($key+1);}else{$CHECKED = "";$sortvalue="";}
	echo "<TR>
	<TD>".odbc_result($rs, "FIELD_NAME")."</TD>
	<TD ALIGN=\"right\"><INPUT TYPE=\"CHECKBOX\" NAME=\"grouping_fields[id][".odbc_result($rs, "FIELD_ID")."]\" VALUE=\"".odbc_result($rs, "FIELD_ID")."\" $CHECKED></TD>
	<TD NOWRAP ALIGN=\"right\"><INPUT TYPE=\"TEXT\" NAME=\"grouping_fields[sort][".odbc_result($rs, "FIELD_ID")."]\" VALUE=\"$sortvalue\" STYLE=\"width:20px;\"></TD>
	</TR>";
$bzm++;
}

?>
<TR><TD colspan="3"><HR></TD></TR>
<TR><TD colspan="3"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[33]?>" NAME="newupload"></TD></TR>

</TABLE>

<BR>
<BR><BR>

</TD></TR></TABLE>
</FORM>
<BR><BR>