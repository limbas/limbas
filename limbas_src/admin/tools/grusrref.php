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
 * ID: 147
 */


set_time_limit(10000);
ob_implicit_flush();


echo "
<br><br><div style=\"padding:3px;width:500px;border:1px solid black;background-color:#FFFFFF;position:fixed;left:10px;\"><img id=\"overall\" SRC=\"pic/point_g.gif\" WIDTH=\"0\" HEIGHT=\"15\"></div><br><br>

<table style=\"width:506px;margin:10px;\" class=\"tabfringe\">
<tr><td width=\"70\" nowrap><div><b>".$lang[561]." :</b></div></td><td><div id=\"status_group\"> - </div></td></tr>
<tr><td nowrap><div><b>".$lang[164]." :</b></div></td><td><div id=\"status_table\"> - </div></td></tr>
</table>
";


if($check_all){
	# ------------------------------ Gruppenrechte ergänzen ------------------------------------
	check_grouprights1All(1,$check_table);
	
}elseif($group_id){
	# ------------------------------ Gruppenrechte zurücksetzten ------------------------------------	
	function group_list($group_id,$group_name){
		global $db;
		global $commit;
		
		$sqlquery = "SELECT GROUP_ID,NAME FROM LMB_GROUPS WHERE LEVEL = $group_id";
		$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		if(!$rs) {$commit = 1;}
		while(lmbdb_fetch_row($rs)) {
			group_list(lmbdb_result($rs, "GROUP_ID"),lmbdb_result($rs,"NAME"));
		}
		check_grouprights2($group_id,$group_name,1,1);
	}
	group_list($group_id,$groupdat['name'][$group_id]);
}

if($GLOBALS["action"]){
	echo "<Script language=\"JavaScript\">showprocess('overall','500','width');showprocess('status_group','<i class=\"lmb-icon lmb-aktiv\"></i>');showprocess('status_table','<i class=\"lmb-icon lmb-aktiv\"></i>')</SCRIPT>";
	echo "<br><div style=\"margin:12px;color:green\"><b>".$lang[987]."</b></div>";
}

?>
