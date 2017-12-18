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

<link rel="stylesheet" href="extern/bootstrap/bootstrap.min.css">
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


<table class="tabfringe" BORDER="0" width="550" cellspacing="2" cellpadding="2">

<TR><TD class="tabHeader" align="left" style="width:200px"><?echo "current Source-Version:</td><td><b>".$umgvar["version"]."</b>";?></TD><td></td></TR>
<TR><TD class="tabHeader" align="left"><?echo "current Systemtables-Version:</td><td><b>".$umgvar["db-version"]."</b>";?></TD><td></td></TR>
<TR><TD class="tabHeader" align="left">&nbsp;</td></TR>

<?php

// database version
$rd = explode(".",$umgvar["db-version"]);
$rd[0] = intval($rd[0]);
$rd[1] = intval($rd[1]);
$rd[2] = intval($rd[2]);
$db_version = floatval($rd[0].'.'.str_pad($rd[1], 2 ,'0', STR_PAD_LEFT) );

// source version
$rs = explode(".",$umgvar["version"]);
$rs[0] = intval($rs[0]);
$rs[1] = intval($rs[1]);
$rs[2] = intval($rs[2]);
$source_version= floatval($rs[0].'.'.str_pad($rs[1], 2 ,'0', STR_PAD_LEFT) );

// available update scripts
if($path = read_dir($umgvar["pfad"]."/admin/UPDATE")){
    
    foreach($path["name"] as $key => $value){
    	$value_ = explode(".",$value);
    	if($path["typ"][$key] == "file" AND $value_[1] == "php"){
    		$vd = explode("_",$value);
    		$vd[1] = intval($vd[1]);
    		$vd[2] = intval(str_replace('.php','',$vd[2]));
    		$vd[3] = str_pad($vd[2], 2 ,'0', STR_PAD_LEFT);
    		$vd[0] = floatval($vd[1].'.'.$vd[3]);
    		$up_scripts[$value] = $vd[0];
    	}
    }
    
    asort($up_scripts);

    foreach($up_scripts as $file => $file_version) {
        if($file_version > $db_version AND $source_version >= $file_version){
            $upl[] = $file;
        }
    }
    
    if(!$upl AND $rs[0].'.'.$rs[1].'.'.$rs[2] != $umgvar["db-version"]){
        end($up_scripts);
    	$upl[] = key($up_scripts);
    }
    
}



if($upl){
    echo '<TR class="tabBody"><TD>'.$lang[1847].'</td><td><SELECT NAME="update_file"><OPTION>';
    $vcount=0;
	foreach ($upl as $key => $value){
		echo "<OPTION VALUE=\"".$value."\">".$vcount.')&nbsp;&nbsp;&nbsp;'.$value;
		$vcount++;
	}
	echo '</SELECT></TD><TD ALIGN="CENTER"><INPUT TYPE="submit" VALUE="ok!"></TD></TR>';
}else{
    echo '<TR class="tabBody"><TD colspan="2"><b style="color:green">nothin to do ... systemtables are synchronous to source!</b></td></tr>';
}


?>





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
		echo "<hr><input type=\"button\" onclick=\"top.document.location.href='index.php'\" class=\"btn btn-info pull-right\" value=\"finished .. back to LIMBAS\"<br><br>";
	}
}else{

/* --- revision check --------------------------------- */
if($vcount){
	echo "<br><div style=\"border:2px solid red;width:600px;padding:3px\">
    <b>".$vcount." updates found ! You have to run all update scripts in their sequence !</b>
	</div>
	";
}

}
?>

</FORM>

<br><br>

<div class="alert alert-warning small" role="alert" style="font-size:12">
<b>Steps to update your system:</b><br><br>

<li>download latest "LIMBAS source"
<li>backup your system!
<li>replace or add the new LIMBAS source directory "limbas_src_x.x"
<li>rebuild the symlink "limbas_src" to the new source directory (<i>ln -s limbas_src_x.x limbas_src</i>)
<li>login to limbas 
<li>limbas will redirect you to the "system update page" (this page)
<li>select the systemupdate script "up_3.x.php" and run the update with "OK". If your release is older than one release you have to run all updates up to your release.
<li>reset your system
<li>replace the "independent" directory with its newest version if necessary. Available as "independent.tar" archive in source.
          
</div>  




</div>