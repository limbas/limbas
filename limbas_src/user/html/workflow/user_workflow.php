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
 * ID: 208
 */



?>



<SCRIPT LANGUAGE="JavaScript">

function limbasCancelWorkflowinstance(wfid,wfinstid) {
	document.form1.wfid.value = wfid;
	document.form1.wfinstid.value = wfinstid;
	
	document.form1.wf_cancel.value = 1;
	document.form1.wf_pause.value = 0;
	
	document.form1.submit();
}

function limbasPauseWorkflowinstance(wfid,wfinstid) {
	document.form1.wfid.value = wfid;
	document.form1.wfinstid.value = wfinstid;
	
	document.form1.wf_cancel.value = 0;
	document.form1.wf_pause.value = 1;
	
	document.form1.submit();
}

function nav_refresh(gtabid,snapid,val) {
	if(parent.nav){
		parent.nav.document.location.href = 'main.php?<?=SID?>&action=nav&sparte=gtab&refresh=no';
	}
	if(parent.parent.nav){
		parent.parent.nav.document.location.href = 'main.php?<?=SID?>&action=nav&sparte=gtab&refresh=no';
	}
}
</SCRIPT>


<BR>
<TABLE width='100%'><TR><TD WIDTH="20">&nbsp;</TD><TD>

<FORM ACTION="main.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="my_workflow">
<input type="hidden" name="wfid">
<input type="hidden" name="wfinstid">
<input type="hidden" name="wf_cancel">
<input type="hidden" name="wf_pause">

<script type="text/javascript" src="user/html/user_workflow.js"></script>

<?php
$mywf = WF_getMyWorkflowInstances();
$mytasks = WF_getMyTasks();


if(!$mywf && !$mytasks){
	echo "<br><br><center>".$lang[2051]."</center>";
}

if($mywf AND $LINK[$action]){//$LINK[228]){
	echo "<TABLE cellspacing=0>";
	echo "<TR>";
	echo "<TD colspan=87 style=\"background-color:" . $farbschema["WEB8"] . "\"><B>$lang[2057]</B></TD>";
	echo "</TR>";
	
	echo "<TR>";
	echo "<TD colspan=7>&nbsp;</TD>";
	echo "</TR>";
	
	
	
	
	foreach($mywf as $wfid => $value){
		
		
		echo "<TR>";
		echo "<TD colspan=7 style=\"background-color:" . $farbschema["WEB6"] . "\"><B>".$mywf[$wfid]["name"]."</TD>";
		echo "</TR>\n";
	
		echo "<TR  style=\"background-color:" . $farbschema["WEB7"] . "\">";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\">".$lang[2040]."</TD>";
		echo "<TD width=\"200\" style=\"font-weight:bold;color:grey\">".$lang[2041]."</TD>";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\" TITLE=\"".$lang[2042]."\">".$lang[2042]."</TD>";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\" TITLE=\"".$lang[2043]."\">".$lang[2043]."</TD>";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\" TITLE=\"".$lang[2045]."\">".$lang[2044]."</TD>";
		echo "<TD>&nbsp;</TD>";
		echo "<TD>&nbsp;</TD>";
		echo "</TR>";
		
		
		
		foreach($mywf[$wfid]["instance"] as $wfinstid => $wfinstance){
				
			echo "<TR>\n";

			echo "<TD TITLE=\"" . $wfinstance["todo"] . "\">";
			if($wfinstance["url"]){
				echo "<A href=\"" . $wfinstance["url"] . "\">" . $wfinstance["disp"] . "</A>";
			}else{
				echo $wfinstance["disp"];
			}
			echo "</TD>\n";
			
			echo "<TD>" . $wfinstance["todo"] . "</TD>\n";

			echo "<TD>";
			echo ($wfinstance["user"]["previous"]?USER_display($wfinstance["user"]["previous"]):"&nbsp;");
			echo "</TD>\n";
			
			echo "<TD>";
			echo ($wfinstance["user"]["current"]?USER_display($wfinstance["user"]["current"]):"&nbsp;");
			echo "</TD>\n";
			
			echo "<TD>";
			echo ($wfinstance["user"]["next"]?USER_display($wfinstance["user"]["next"]):"&nbsp;");
			echo "</TD>\n";
			
			echo "<TD TITLE=\"Halten\">";
			echo "<i class=\"lmb-icon lmb-lock-file\" onClick=\"javascript:if(confirm('".$lang[2046]."')){limbasPauseWorkflowinstance($wfid,$wfinstid);}\" style=\"cursor:pointer\"></i>";
			echo "</TD>\n";
			
			echo "<TD TITLE=\"Abrechen\">";
			echo "<i class=\"lmb-icon lmb-trash\" onClick=\"javascript:if(confirm('".$lang[2047]."')){limbasCancelWorkflowinstance($wfid,$wfinstid);}\" style=\"cursor:pointer\"></i>";
			echo "</TD>\n";

			echo "</TR>\n";
				#echo "<LI><INPUT TYPE=\"TEXT\" VALUE=\"".$gsnap[$key1][name][$key2]."\" OnChange=\"edit_name('$key1','$key2',this.value)\" STYLE=\"border:none;width:150px;\"> <IMG SRC=\"pic/delete2.gif\" OnCLick=\"document.location.href='main.php?".SID."&action=user_snapshot&gtabid=$key1&del=$key2'\" STYLE=\"cursor:pointer;\"></LI>";
		}
		
		
		
		echo "<TR><TD COLSPAN=7>&nbsp;</TD></TR>\n";
		if($mywf && $mytasks){
			echo "<TR><TD COLSPAN=7><HR></TD></TR>\n";
			echo "<TR><TD COLSPAN=7>&nbsp;</TD></TR>\n";
		}
		
		
		
	}
	echo "</TABLE>";
}



if($mytasks AND $LINK[$action]){//$LINK[230]){
	echo "<TABLE cellspacing=0>";
	echo "<TR>";
	echo "<TD colspan=4 style=\"background-color:" . $farbschema["WEB8"] . "\"><B>$lang[2038]</B></TD>";
	echo "</TR>";
	
	echo "<TR>";
	echo "<TD colspan=4>&nbsp;</TD>";
	echo "</TR>";
	
	
	foreach($mytasks as $wfid => $value){
		
		
		echo "<TR>";
		echo "<TD colspan=4 style=\"background-color:" . $farbschema["WEB6"] . "\"><B>".$mytasks[$wfid]["description"]."</B></TD>";
		echo "</TR>\n";
	
		echo "<TR  style=\"background-color:" . $farbschema["WEB7"] . "\">";
		echo "<TD width=\"150\" style=\"font-weight:bold;color:grey\">".$lang[2040]."</TD>";
		echo "<TD width=\"250\" style=\"font-weight:bold;color:grey\">".$lang[2048]."</TD>";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\">".$lang[2049]."</TD>";
		echo "<TD width=\"100\" style=\"font-weight:bold;color:grey\">".$lang[2050]."</TD>";
		echo "</TR>";
		
		
		
		foreach($mytasks[$wfid]["step"] as $key => $val){
				
			echo "<TR>\n";

			echo "<TD TITLE=\"" . $mytasks[$wfid]["step"][$key]["desc"] . "\">";
			if($mytasks[$wfid]["step"][$key]["url"]){
				echo "<A HREF=\"" . $mytasks[$wfid]["step"][$key]["url"] . "\">" . $mytasks[$wfid]["step"][$key]["desc"] . "</A>";
			}else{
				echo $mytasks[$wfid]["step"][$key]["desc"];
			}
			echo "</TD>\n";

			echo "<TD TITLE=\"" . $mytasks[$wfid]["step"][$key]["todo"] . "\">" .  $mytasks[$wfid]["step"][$key]["todo"] . "</TD>\n";

			echo "<TD>";
			echo ($mytasks[$wfid]["step"][$key]["user"]["previous"]?USER_display($mytasks[$wfid]["step"][$key]["user"]["previous"]):"&nbsp;");
			echo "</TD>\n";
			
			echo "<TD>";
			echo ($mytasks[$wfid]["step"][$key]["user"]["next"]?USER_display($mytasks[$wfid]["step"][$key]["user"]["next"]):"&nbsp;");
			echo "</TD>\n";
			
			echo "</TR>\n";
		}
		
		echo "<TR><TD COLSPAN=4>&nbsp;</TD></TR>\n";
	}
	echo "</TABLE>";
}

?>

</FORM>

</TD></TR></TABLE>