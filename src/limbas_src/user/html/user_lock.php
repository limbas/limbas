<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if($droplocktab AND $droplockid){
	if($gtab["lock"][$droplocktab] AND $LINK[271]){
		$lock = lock_data_check($droplocktab,$droplockid,$session["user_id"]);
		if($lock["isselflocked"] OR !$lock){
			lock_data_set($droplocktab,$droplockid,$session["user_id"],"unlock");
		}
	}
}
?>


<SCRIPT LANGUAGE="JavaScript">

function newwin(gtabid,ID){
	newwindata=open("main.php?action=gtab_change&ID="+ID+"&gtabid="+gtabid,"datadetail","toolbar=0,location=0,status=0,menubar=0,scrollbars=1,resizable=1,width=700,height=700");
}

</SCRIPT>

<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="action" value="user_lock">



<div class="lmbPositionContainerMain">
<table class="tabfringe" border="0" cellspacing="1" cellpadding="2"><tr><td valign="top">

<?php
	$stamp = mktime(date("H"),date("i") + $umgvar["inusetime"],date("s"),date("m"),date("d"),date("Y"));
	$iuse = "'".convert_stamp($stamp)."'";

	$extension["where"][] = "INUSE_USER = ".$session["user_id"]." AND INUSE_TIME > $iuse";
	
	foreach ($gtab["table"] as $gtabid => $table){
		if(!$gtab["lockable"][$gtabid]){continue;}
		$onlyfield = null;
		
		if($gfield[$gtabid]["mainfield"]){
			$onlyfield[$gtabid] = array($gfield[$gtabid]["mainfield"]);
		}elseif($gfield[$gtabid]["fieldkey_id"]){
			$onlyfield[$gtabid] = array($gfield[$gtabid]["fieldkey_id"]);
		}
		
		$extension["order"][] = "INUSE_TIME";
		######### gresult Abfrage ##########
		$gresult = get_gresult($gtabid,1,null,null,null,$onlyfield,null,$extension);
		if($maxCount = $gresult[$gtabid]["res_count"]){
			echo "<tr class=\"tabHeader\"><td colspan=\"4\"><b>".$gtab["desc"][$gtabid]."</b></td></tr>";
			for ($i=0;$i<$maxCount;$i++) {
				echo "<tr bgcolor=\"".$farbschema["WEB8"]."\" style=\"cursor:pointer;\" OnMouseOver=\"this.style.backgroundColor='".$farbschema["WEB10"]."'\" OnMouseOut=\"this.style.backgroundColor=''\">
				<td OnClick=\"newwin($gtabid,".$gresult[$gtabid]["id"][$i].")\">".$gresult[$gtabid]["id"][$i]."</td>";
				if($onlyfield){echo "<td OnClick=\"newwin($gtabid,".$gresult[$gtabid]["id"][$i].")\">".$gresult[$gtabid][$onlyfield[$gtabid][0]][$i]."</td>";}
				echo "<td OnClick=\"newwin($gtabid,".$gresult[$gtabid]["id"][$i].")\">".get_date($gresult[$gtabid]["INUSE_TIME"][$i],2)."</td>";
				echo "<td align=\"center\"><i class=\"lmb-icon lmb-trash\" OnClick=\"document.location.href='main.php?action=user_lock&droplocktab=$gtabid&droplockid=".$gresult[$gtabid]["id"][$i]."'\"></i></td>
				</tr>";
			}
			$cnt = 1;
		}
	}
	
if(!$cnt){
	echo "<tr><td><b>".$lang[98]."</b></td></tr>";
}
	
?>


<tr><td></td></tr>
</table></div>

</form>
