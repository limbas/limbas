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

<TABLE ID="tab5" width="100%" cellspacing="2" cellpadding="1" class="tabBody importcontainer">
<TR class="tabHeader"><TD class="tabHeaderItem" COLSPAN="5"><?=$lang[2240]?></TD></TR>
<TR  class="tabBody"><TD><SELECT NAME="covertfromtable"><OPTION></OPTION>
<?php
$sqlquery = "SELECT TABELLE FROM LMB_CONF_TABLES";
$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(lmbdb_fetch_row($rs)) {
	$existing_tables[] = lmb_strtoupper(lmbdb_result($rs, "TABELLE"));
}

$odbctable = dbf_20(array($DBA["DBSCHEMA"],null,"'TABLE','VIEW'"));
foreach($odbctable["table_name"] as $tkey => $tvalue) {
	if(!in_array(lmb_strtoupper($tvalue),$existing_tables)){
		echo "<OPTION VALUE=\"".$tvalue."\">".$tvalue."</OPTION>\n";
	}
}
?>
</SELECT>&nbsp;
<INPUT TYPE="button" onclick="document.form1.import_action.value=1;document.form1.submit();" VALUE="<?=$lang[2240]?>">
</TD></TR>

<TR><TD colspan="2" class="tabFooter"></TD></TR>

</TABLE>


<?php

if($import_action == 1 AND $covertfromtable){
	/* --- Tabellenfelder auslesen --------------------------------------------- */
	$columns = dbf_5(array($DBA["DBSCHEMA"],$tabname));
	foreach ($columns["columnname"] as $key => $value){
		$header[] = $value;
		$e["field_type"][] = $columns["datatype"][$key];
		$e["length"][] = $columns["length"][$key];
	}
	
    import_create_fieldmapping($ifield,'convert',$covertfromtable);

}elseif($import_action == 2){
    $result = import_create_addtable('convert', $ifield, $add_permission = null, 1);
}

?>