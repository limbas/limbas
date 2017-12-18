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
 * ID:
 */


require("lib/include.lib");


function call_soap($res_next,$id,$name,$desc,$typ,$price,$order){


	# search
	if(is_numeric($id)){
		$gsr[23][1][0] = $id;
	}
	if($name){
		$gsr[23][2][0] = trim(substr($name,0,128));
	}
	if($desc){
		$gsr[23][3][0] = trim(substr($desc,0,128));
	}
	if($typ){
		$gsr[23][4][0] = trim(substr($typ,0,128));
	}
	if(is_numeric($price)){
		$gsr[23][5][0] = trim(substr($price,0,128));
	}

	# order
	if(is_numeric($order)){
		$lmpar[0]["order"] = "$order,ASC";
	}


	# start on page
	if(!$res_next){
		$res_next = 1;
	}

	$lmpar[0]["gsr"] = $gsr;								# array of search requests
	$lmpar[0]["getvars"] = array('fresult');				# return result arrays, you can use (fresult, gtab, gfield, umgvar). fresult is needed for resultsets
	$lmpar[0]["action"] = "gtab_erg";						# you can use tables [gtab_erg] or filemanager [explorer_main]
	$lmpar[0]["gtabid"] = "23";								# ID of requested table
	$lmpar[0][23]["showfields"] = "1,2,3,4,5,6";			# IDs of requested fileds in table
	$lmpar[0][23]["res_next"] = $res_next;					# current page
	$lmpar[0][23]["count"] = 15;							# count of datasets seen in one page
	$lmpar[0][23]["fieldlimit"][3] = 150;					# limit of characters in field of type long
	$lmpar[0][23]["thumbsize"][6] = null; 					# size of thumbnail in fileds of type upload - like 50, 0x50, 10x, 50x
	$lmpar[0][23]["reminder"] = null;						# is set use reminder
	$lmpar[0][23]["reminder_group"] = null;					# filter for specific reminder group
	$lmpar[0][23]["reminder_user"] = null;					# filter for specific reminder user

	$GLOBALS["lmpar"] = $lmpar;
	return call_client($lmpar);

}

if($reset){
	$name = "";
	$desc = "";
	$typ = "";
	$price = "";
	$order = "";
}else{
	$lmb = call_soap($res_next,$id,$name,$desc,$typ,$price,$order);
}



?>


<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>LIMBAS Soap Demo</title>

<Script language="JavaScript">
function sendkeydown(evt) {
	if(evt.keyCode == 13){
		document.form1.submit();
	}
}
</Script>

</head>
<body OnKeydown="sendkeydown(event)">

<form name="form1" method="post" action="data_access.php">
<INPUT type="hidden" name="jumpto" VALUE="<?=$jumpto?>">
<INPUT type="hidden" name="res_next">

<table width="100%"  border="0" cellspacing="1" cellpadding="1">
<tr><td></td><td><input type="text" style="border:1px solid #CCCCCC;width:100%;height:20px;" name="id" value="<?=$id?>"></td>
<td><input type="text" style="border:1px solid #CCCCCC;width:100%;height:20px;" name="name" value="<?=$name?>"></td>
<td><input type="text" style="border:1px solid #CCCCCC;width:100%;height:20px;" name="desc" value="<?=$desc?>"></td>
<td><input type="text" style="border:1px solid #CCCCCC;width:100%;height:20px;" name="type" value="<?=$type?>"></td>
<td><input type="text" style="border:1px solid #CCCCCC;width:100%;height:20px;" name="price" value="<?=$price?>"></td></tr>
<tr bgcolor="#FFFFCC"><td colspan="6" STYLE="height:1px;overflow:hidden;background-color:#CCCCCC;"></td></tr>

<tr><td></td><td><b>id</td><td><b>name</td><td><b>desc</td><td><b>type</td><td><b>price</td></tr>
<?
if($lmb[0]["fresult"][23]){
$result = $lmb[0]["fresult"][23];
foreach($result as $key => $value){
	echo "<TR>";
	if($value[6]["url"][0]){
		echo "<TD VALIGN=\"TOP\"><IMG SRC=\"".$LIM["lim_url"].$value[6]["url"][0]."\"></TD>";
	}else{
		echo "<TD></TD>";
	}
	echo "<TD VALIGN=\"TOP\">".$value[1]."</TD>";
	echo "<TD VALIGN=\"TOP\">".$value[2]."</TD>";
	echo "<TD VALIGN=\"TOP\">".$value[3]."</TD>";
	echo "<TD VALIGN=\"TOP\">".$value[4]."</TD>";
	echo "<TD VALIGN=\"TOP\">".$value[5]."</TD>";
	}
	echo "</TR>";
}
echo "<TR><TD>&nbsp;</TD></TR>";
echo "<tr bgcolor=\"#FFFFCC\"><td colspan=\"6\" STYLE=\"height:1px;overflow:hidden;background-color:#CCCCCC;\"></td></tr>";
echo "<TR><td></td><TD COLSPAN=\"5\"><FONT STYLE=\"font-size:11px;\">Total <B>".$lmb[0]["result"]["res_count"][23]."</B> Fundstellen, Seite:&nbsp; <span style=\"cursor:pointer;color:blue;font-weight:bold;\" OnClick=\"document.form1.res_next.value='1';document.form1.submit();\">&lt;&lt;</span> &nbsp;<span style=\"cursor:pointer;color:blue;font-weight:bold;\" OnClick=\"document.form1.res_next.value='".($lmpar[0][23]["res_next"] - 1)."';document.form1.submit();\">&lt;</span>&nbsp; <B>".$lmb[0]["result"]["page"][23]."</B>&nbsp;&nbsp;<span style=\"cursor:pointer;color:blue;font-weight:bold;\" OnClick=\"document.form1.res_next.value='".($lmpar[0][23]["res_next"] + 1)."';document.form1.submit();\">&gt;</span> &nbsp;<span style=\"cursor:pointer;color:blue;font-weight:bold;\" OnClick=\"document.form1.res_next.value='".(ceil($lmb[0]["result"]["max_count"][23]/$lmpar[0][23]["count"]))."';document.form1.submit();\">&gt;&gt;</span> von <B>".(ceil($lmb[0]["result"]["max_count"][23]/$lmpar[0][23]["count"]))."</B> Seiten</FONT></TD></TR>";
?>


</table>
</form>

</body>
</html>