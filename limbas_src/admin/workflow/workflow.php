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
 * ID: 208
 */
?>

<form action="main_admin.php" method="post" name="form1">
<input type="hidden" name="action" value="setup_workflow">
<input type="hidden" name="editid">
<input type="hidden" name="wflid" value="<?=$wflid?>">

<div class="lmbPositionContainerMain small">
<table class="tabfringe">
<?php

if($wflid){
	echo "<tr class=\"tabHeader\"><td class=\"tabHeaderItem\" height=\"20\" colspan=\"5\"><b>".$wfltask["wfl_name"]."</b></td>";
	echo "<tr class=\"tabHeader\">
		<td class=\"tabHeaderItem\">ID</td>
		<td class=\"tabHeaderItem\"></td>
		<td class=\"tabHeaderItem\">".$lang[4]."</td>
		<td class=\"tabHeaderItem\">".$lang[2331]."</td>
		<td class=\"tabHeaderItem\">".$lang[164]."</td>
              </tr>";

	if($wfltask['name']){
		foreach ($wfltask['name'] as $key => $value){
		echo "<tr clas=\"tabBody\">
			<td>".$key."</td>
			<td align=\"center\" valign=\"top\"><a href=\"main_admin.php?action=setup_workflow&wflid=$wflid&delid=$key\"><i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" border=\"0\"></i></a></td>
			<td valign=\"top\"><input type=\"text\" id=\"taskname[$key]\"  name=\"taskname[$key]\" value=\"".$wfltask["name"][$key]."\" onchange=\"document.form1.editid.value=$key;document.form1.submit();\"></td>
			<td valign=\"top\"><textarea name=\"taskparams[$key]\" onchange=\"document.form1.editid.value=$key;document.form1.submit();\" style=\"width: 200px; height: 16px;\" onblur=\"this.style.height=$(document.getElementById('taskname[$key]')).outerHeight();\" onfocus=\"this.style.height='80px';\">".htmlentities($wfltask["uparams"][$key],ENT_QUOTES,$umgvar["charset"])."</textarea></td>
			<td valign=\"top\"><select name=\"tasktabid[$key]\" onchange=\"document.form1.editid.value=$key;document.form1.submit();\"><option>";
			foreach($gtab["table"] as $tabkey => $tabval){
				if($wfltask["tab_id"][$key] == $tabkey){$SELECTED = "SELECTED";}else{$SELECTED = "";}
				echo "<option value=\"".$tabkey."\" $SELECTED>".$tabval;
			}
			echo "</select></td>
			<td></td>
			</tr>";
	}}
	
	echo "<tr><td colspan=\"5\"><hr></td></tr>";
	echo "<tr class=\"tabHeader\">
		<td class=\"tabHeaderItem\" colspan=\"2\"></td>
		<td class=\"tabHeaderItem\">".$lang[4]."</td>
		<td></td>
		<td></td>
		</tr>";
	
	echo "<tr>";
	echo "<td align=\"right\" colspan=\"2\"></td>";
	echo "<td align=\"right\"><input type=\"text\" name=\"new_taskname\"></td>";
	echo "<td><input type=\"submit\" value=\"".$lang[2754]."\" name=\"new_task\"></td>";
	echo "</tr>";
	

}else{

	if($workflow){
		echo "<tr class=\"tabHeader\">
			<td class=\"tabHeaderItem\">ID</td>
			<td class=\"tabHeaderItem\" colspan=\"2\"></td>
			<td class=\"tabHeaderItem\">".$lang[4]."</td>
			<td class=\"tabHeaderItem\">".$lang[2331]."</td>
                      </tr>";
	
		foreach ($workflow['name'] as $key => $value){
			echo "<tr clas=\"tabBody\">
				<td>".$key."</td>
				<td align=\"center\" valign=\"top\"><a href=\"main_admin.php?action=setup_workflow&wflid=$key\"><i class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer\" border=\"0\"></i></a></td>
				<td align=\"center\" valign=\"top\"><a href=\"main_admin.php?action=setup_workflow&delid=$key\"><i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" border=\"0\"></i></a></td>
				<td valign=\"top\"><input type=\"text\" id=\"workflowname[$key]\" name=\"workflowname[$key]\" value=\"".$workflow["name"][$key]."\" onchange=\"document.form1.editid.value=$key;document.form1.submit();\"></td>
				<td valign=\"top\"><textarea name=\"workflowparams[$key]\" onchange=\"document.form1.editid.value=$key;document.form1.submit();\" style=\"width: 200px; height: 16px;\" onblur=\"this.style.height=$(document.getElementById('workflowname[$key]')).outerHeight();\" onfocus=\"this.style.height='80px';\">".htmlentities($workflow["params"][$key],ENT_QUOTES,$umgvar["charset"])."</textarea></td>
				</tr>";
		}
	}
	
	
	echo "<tr><td colspan=\"5\"><hr></td></tr>";
	echo "<tr class=\"tabHeader\">
		<td class=\"tabHeaderItem\" colspan=\"3\"></td>
		<td class=\"tabHeaderItem\">".$lang[4]."</td>
		<td></td>
		</tr>";
	
	echo "<tr>";
	echo "<td colspan=\"3\" align=\"right\"></td>";
	echo "<td align=\"right\"><input type=\"text\" name=\"new_workflowname\"></td>";
	echo "<td><input type=\"submit\" value=\"".$lang[2752]."\" name=\"new_workflow\"></td>";
	echo "</tr>";

}
?>
</table>

</div>
</form>