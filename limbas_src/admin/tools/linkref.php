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
 * ID: 152
 */


set_time_limit(10000);
ob_implicit_flush();


if($GLOBALS["action"]){
	
	echo "
	<br><br><div style=\"padding:3px;width:500px;border:1px solid black;background-color:#FFFFFF;position:fixed;left:10px;\"><img id=\"overall\" SRC=\"pic/point_g.gif\" WIDTH=\"0\" HEIGHT=\"15\"></div><br><br>
	<table style=\"width:506px;margin:10px;\" class=\"tabfringe\">
	<tr><td width=\"70\" nowrap><div><b>".$lang[561]." :</b></div></td><td><div id=\"status_group\"> - </div></td></tr>
	</table>
	";

}



$sqlquery = "Select COUNT(*) AS ANZAHL from LMB_GROUPS";
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
$num_group = odbc_result($rs,"ANZAHL");


if($group){
	# -----Einzelgruppe----------
	$sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS WHERE GROUP_ID = $group";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$bzm = 1;
	if(odbc_fetch_row($rs, 1)) {
		check_grouprights(odbc_result($rs,"GROUP_ID"),odbc_result($rs,"NAME"),$num_group);
	}
}else{
	# -----Gruppenliste----------
	$sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS";
	$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	$bzm = 1;
	while(odbc_fetch_row($rs, $bzm)) {
		check_grouprights(odbc_result($rs,"GROUP_ID"),odbc_result($rs,"NAME"),$num_group);
		del_grouprights(odbc_result($rs,"GROUP_ID"),odbc_result($rs,"NAME"),$num_group);
	$bzm++;
	}
}

if($GLOBALS["action"]){
	echo "<Script language=\"JavaScript\">showprocess('overall','500','width');showprocess('status_group','<i class=\"lmb-icon lmb-aktiv\"></i>');</Script>";
	echo "<br><div style=\"margin:12px;color:green\"><b>".$lang[987]."</b></div>";
}


?>
