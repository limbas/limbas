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
 * ID: 154
 */

set_time_limit(3600); #60min
ob_implicit_flush();


?>
<div class="lmbPositionContainerMain small">
<?php


if($lmbalert == "update"){
	lmb_alert("source-version is not same as database-version!\\nplease run update to upgrade systemtables to newest version\\nor check lib/version.inf manually!");
}


if($umgvar["lock"]){
	$user_lock = "CHECKED";
}else{
	$user_lock = "";
}




?>

<FORM ACTION="main_admin.php" METHOD="post" name="form1">
<INPUT TYPE="hidden" NAME="action" VALUE="setup_update">


<TABLE class="tabfringe" BORDER="0" width="600" cellspacing="2" cellpadding="2">
<TR><TD class="tabHeader" colspan="2" align="center"><?echo "current Source-Version: <b>".$umgvar["version"]."</b> - current Systemtables-Version: <b>".$umgvar["db-version"]."</b>";?><br><br></TD></TR>
<TR class="tabBody"><TD><?=$lang[1847]?>&nbsp;<SELECT NAME="update_file"><OPTION>
<?php



$rd = explode(".",$umgvar["version"]);
$rd[1] = intval($rd[1]);
$rd[2] = intval($rd[2]);


if($path = read_dir($umgvar["pfad"]."/admin/UPDATE")){
foreach($path["name"] as $key => $value){
	$value_ = explode(".",$value);
	if($path["typ"][$key] == "file" AND $value_[1] == "php"){
		$vd = explode("_",$value);
		$vd[1] = intval($vd[1]);
		$vd[2] = intval(str_replace('.php','',$vd[2]));

		if($vd[1] >= $rd[0] AND $vd[2] >= $rd[1]){
			$upl[] = $value;
		}
	}
}
}

sort($upl);
if($upl){
	foreach ($upl as $key => $value){
		echo "<OPTION VALUE=\"".$value."\">".$value;
	}
}

?>
</SELECT></TD><TD ALIGN="CENTER"><INPUT TYPE="submit" VALUE="ok!"></TD></TR>
<TR><TD class="tabFooter" colspan="2"></TR>
</TABLE>


<br><br>

<?
# --- Systemupdate ----
if($update_file){
	echo "<hr>";
	ob_end_flush();
	ob_start();
	require_once($umgvar["pfad"]."/admin/UPDATE/".$update_file);
	$output = ob_get_contents();
	$output = trim(nl2br($output));
	ob_get_clean();
	echo $output;
	flush();
	if(!$output){
		echo "<b>all updates of this script allready done!</b>";
	}else{
		if($impsystables){
			patch_import($impsystables);
		}
		session_destroy();
		session_unset();
		echo "<br><hr><a href=\"JavaScript:top.document.location.href='index.php'\" style=\"color:blue\">back to mainpage..</a>";
	}
}

/* --- Version auslesen ------------------- */
$sqlquery3 = "SELECT REVISION,VERSION FROM LMB_DBPATCH WHERE ID = (SELECT MAX(ID) FROM LMB_DBPATCH)";
$rs3 = odbc_exec($db,$sqlquery3) or errorhandle(odbc_errormsg($db),$sqlquery3,$action,__FILE__,__LINE__);
if(!$rs3) {$commit = 1;}
$version = odbc_result($rs3,"VERSION").".".odbc_result($rs3,"REVISION");

/* --- revision check --------------------------------- */
if(file_exists("lib/version.inf")){
	$vers = file("lib/version.inf");
	$revisionnr = trim($vers[0]);
	$revision = substr($revisionnr,0,strrpos($revisionnr,"."));
}

if(trim($umgvar["db-version"]) != $revision){
	echo "<br><br><div style=\"border:2px solid red;width:600px\">
	The version of systemtables (<b>".$umgvar["db-version"]."</b>) is different with the revision number of the <i>lib/version.inf</i> file (<b>".$revision."</b>)!
	You can edit <i>version.inf</i> or make shure the update was successfully finished.
	</div>
	";
}else{
	#echo "<br><br><div style=\"border:2px solid green;width:600px\">
	#The version of systemtables and source is the same.
	#</div>";
}
?>

</FORM>
</div>