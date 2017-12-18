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
 * ID: 207
 */
?>

<Script language="JavaScript">

// Ajax edit field
function ajaxEditTree(evt,treeid,treetab,itemtab,relationid,changetyp,changeval){
	if(changetyp==null)changetyp = '';
	if(changeval==null)changeval = '';
	mainfunc = function(result){ajaxEditTreePost(result,evt);}
	ajaxGet(null,"main_dyns_admin.php","editTableTree&treeid="+treeid+"&treetab="+treetab+"&itemtab="+itemtab+"&relationid="+relationid+"&changetyp="+changetyp+"&changeval="+changeval,null,"mainfunc");
}

function ajaxEditTreePost(result,evt){
	document.getElementById("lmbAjaxContainer").innerHTML = result;

	if(document.getElementById("lmbAjaxContainer").style.display == 'none'){
		if(evt){limbasDivShow('',evt,"lmbAjaxContainer");}
		document.getElementById("lmbAjaxContainer").style.left = (parseInt(document.getElementById("lmbAjaxContainer").style.left)+30);
	}
	
	
}

function divclose(){
	if(!activ_menu){
		limbasDivClose('');
	}
	activ_menu = 0;
}

var activ_menu = null;

</Script>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>

<?php

if($new_tabletree AND $new_treename AND $new_treetable){
	add_tabletree($new_tabletree,$new_treename,$new_treetable);
}

if(is_numeric($delid)){
	delete_tabletree($delid);
}

$tabletree = get_tabletree();


$sqlquery = "SELECT * FROM LMB_TABLETREE WHERE TREEID = ".parse_db_int($treeid);
$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
while(odbc_fetch_row($rs)){
	if($md5tab = odbc_result($rs,"RELATIONID")){
		$tree['tform'][$md5tab] = odbc_result($rs,"TARGET_FORMID");
		#$tree['tsnap'][$md5tab] = odbc_result($rs,"TARGET_SNAP");
		$tree['display'][$md5tab] = odbc_result($rs,"DISPLAY");
		$tree['tfield'][$md5tab] = odbc_result($rs,"DISPLAY_FIELD");
		$tree['ttitle'][$md5tab] = odbc_result($rs,"DISPLAY_TITLE");
		$tree['tsort'][$md5tab] = odbc_result($rs,"DISPLAY_SORT");
		$tree['ticon'][$md5tab] = odbc_result($rs,"DISPLAY_ICON");
		$tree['trule'][$md5tab] = odbc_result($rs,"DISPLAY_RULE");
	}
}


function lmb_subtree($treetab,$gtabid,$treeid,$sub=0){
	global $gverkn;
	global $gfield;
	global $gtab;
	global $tree;
	static $alldone;

	if($gverkn[$gtabid]["id"]){
		
		$count = 0;
		foreach($gverkn[$gtabid]["id"] as $fieldid => $tabid){
			if($gfield[$gtabid]["verkntabletype"][$fieldid] == 2){continue;}
			$count++;
		}
		
		if($alldone[$gtabid] >= 1){return;}
		$alldone[$gtabid]++;

		if($count){
			echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n";

			$bzm = 1;
			foreach($gverkn[$gtabid]["id"] as $fieldid => $tabid){

				if($gfield[$gtabid]["verkntabletype"][$fieldid] == 2){continue;}

				if($count == $bzm){
					$imgpref = "joinbottom";
				}else{
					$imgpref = "join";
				}

				$md5tab = $gfield[$gtabid]["md5tab"][$fieldid];
				$desc = "";
				if($tree['display'][$md5tab]){$color = "red";}else{$color = "";}
				if($tree['tfield'][$md5tab]){$desc .= "<b style=\"color:blue\" title=\"field\"> fi </b>";}
				if($tree['ttitle'][$md5tab]){$desc .= "<b style=\"color:purple\" title=\"title\"> ti </b>";}
				if($tree['tform'][$md5tab]){$desc .= "<b style=\"color:green\" title=\"form\"> fo </b>";}
				if($tree['tsort'][$md5tab]){$desc .= "<b style=\"color:red\" title=\"sort\"> so </b>";}
				if($tree['ticon'][$md5tab]){$desc .= "<b style=\"color:orange\" title=\"icon\"> ic </b>";}
				if($tree['trule'][$md5tab]){$desc .= "<b style=\"color:grey\" title=\"rule\"> ru </b>";}
				if($desc){$desc = "($desc)";}

				echo "
			<tr><td style=\"width:18px\" valign=\"top\"><img src=\"pic/outliner/".$imgpref.".gif\" align=\"top\" border=\"0\"></td>
			<td align=\"left\" nowrap>&nbsp;<a style=\"color:$color;\" onclick=\"activ_menu=1;ajaxEditTree(event,$treeid,$treetab,".$gfield[$gtabid]["verkntabid"][$fieldid].",'".$gfield[$gtabid]["md5tab"][$fieldid]."')\">".$gtab["desc"][$tabid]."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$desc</td></tr>
			";

				if($gverkn[$tabid]["id"]){
					$stb = "";

					if($count != $bzm){
						$stb = "style=\"width:18px;background-image:url(pic/outliner/line.gif);background-repeat:repeat-y;\"";
					}

					echo "
				<tr>
				<td valign=\"top\" $stb></td>
				<td align=\"left\" nowrap>
				";
					lmb_subtree($treetab,$tabid,$treeid,1);
					echo "
				</td>
				</tr>
				";
				}
				$bzm++;
			}
			echo "</table>\n";
		}
	}
}

?>

<form action="main_admin.php" method="post" name="form1">
<input type="hidden" name="action" value="setup_tabletree">

<div class="lmbPositionContainerMain small">


<table class="tabfringe">
<?php

if($treeid AND $tabid){
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
<tr><td style=\"width:18px\" valign=\"top\"></td>
<td align=\"left\" nowrap>&nbsp;<a onclick=\"activ_menu=1;ajaxEditTree(event,$treeid,$tabid,$tabid,'top')\">".$gtab["desc"][$tabid]."</a></td></tr>
<tr>
<td valign=\"top\">&nbsp;</td>
<td align=\"left\" nowrap>
";
	lmb_subtree($tabid,$tabid,$treeid);
	echo "</td></tr></table>\n";

}else{

	foreach ($gtab["tab_id"] as $key => $tabid){
		if($tabletree[$tabid]["treeid"]){
			echo "<tr class=\"tabHeader\"><td colspan=\"7\" class=\"tabHeaderItem\">".$gtab["desc"][$tabid]."</td></tr>";
			echo "<tr class=\"tabSubHeader\"><td class=\"tabSubHeaderItem\" colspan=\"7\">".$lang[2532]."</td></tr>";
			foreach ($tabletree[$tabid]["treeid"] as $key1 => $value1){
				if($tabletree[$tabid]["display_icon"][$key1]){$icon = "<img src=\"".$tabletree[$tabid]["display_icon"][$key1]."\">";}else{$icon = "";}
				echo "<tr clas=\"tabBody\">
				<td align=\"center\"><a href=\"main_admin.php?action=setup_tabletree&deltabid=$tabid&delid=".$tabletree[$tabid]["treeid"][$key1]."&poolid=".$tabletree[$tabid]["poolid"][$key1]."\"><i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" border=\"0\"></i></a></td>
				<td><a href=\"main_admin.php?action=setup_tabletree&treeid=".$tabletree[$tabid]["treeid"][$key1]."&tabid=$tabid\">".$tabletree[$tabid]["poolname"][$key1]."</td>
				</tr>";
			}
		}
	}

	echo "<tr><td colspan=\"7\"><hr></td></tr>";
	echo "<tr class=\"tabSubHeader\">
	<td class=\"tabSubHeaderItem\" colspan=\"2\">".$lang[2532]."</td>
	<td class=\"tabSubHeaderItem\" colspan=\"2\">".$lang[2540]."</td>
	<td></td>
	</tr>";

	echo "<tr>";
	echo "<td colspan=\"2\"><input type=\"text\" name=\"new_treename\"></td>";
	echo "<td colspan=\"2\"><select name=\"new_treetable\"><option>";
	foreach($gtab["table"] as $tabkey => $tabval){
		echo "<option value=\"".$tabkey."\">".$tabval;
	}
	echo "</select></td>";
	echo "<td><input type=\"submit\" value=\"".$lang[2543]."\" name=\"new_tabletree\"></td>";
	echo "</tr>";

}
?>

</table>

</div>
</form>