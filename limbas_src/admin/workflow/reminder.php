<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 187
 */



?>

<script language="JavaScript">
    function deleteReminder(id, name) {
        var desc = confirm('Delete reminder ' + id + ' (' + name + ')?');
        if(desc){
            document.location.href='main_admin.php?action=setup_reminder&delid=' + id;
        }
    }
</script>

<form action="main_admin.php" method="post" name="form1">
<input type="hidden" name="action" value="setup_reminder">
<input type="hidden" name="editid">

<div class="lmbPositionContainerMain small">

<table class="tabfringe">
<?php

if($reminder){
	foreach ($reminder as $tabid => $value){
                if($tabid == "name_id") { continue; }
            
		echo "<tr class=\"tabHeader\"><td colspan=\"8\" class=\"tabHeaderItem\">".$gtab["desc"][$tabid]."</td></tr>";
		echo "<tr class=\"tabHeader\">
                    <td class=\"tabHeaderItem\">ID</td>
                    <td class=\"tabHeaderItem\"></td>
                    <td class=\"tabHeaderItem\"></td>
                    <td class=\"tabHeaderItem\">".$lang[4]."</td>
                    <td class=\"tabHeaderItem\">".$lang[2756]."</td>
                    <td class=\"tabHeaderItem\">".$lang[1169]."</td>
                    <td class=\"tabHeaderItem\">".$lang[2742]."</td>
		</tr>";
		foreach ($value['name'] as $id => $name){
			echo "<tr clas=\"tabBody\">
				<td>".$id."</td>
				<td align=\"center\" style=\"width:20px;\"><i class=\"lmb-icon lmb-trash\" onclick=\"deleteReminder(".$id.", '".$name."')\" style=\"cursor:pointer\" border=\"0\"></i></td>
				
				
				<TD align=\"center\" style=\"width:30px;white-space: nowrap;\">
				<a href=\"main_admin.php?action=setup_reminder&sort=up&sortid=$id&tabid=$tabid\"><i class=\"lmb-icon lmb-long-arrow-up\" BORDER=\"0\"></i></a>&nbsp;<a href=\"main_admin.php?action=setup_reminder&sort=down&sortid=$id&tabid=$tabid\"><i class=\"lmb-icon lmb-long-arrow-down\" BORDER=\"0\"></i></a></TD>
				<td><input type=\"text\" name=\"remindername[$id]\" value=\"".$name."\" onchange=\"document.form1.editid.value=$id;document.form1.submit();\"></td>
				<td>";
			if($gformlist[$tabid]["name"]){
				$forms = "";
				foreach($gformlist[$tabid]["name"] as $key => $val){
					if($gformlist[$tabid]["typ"][$key] == 2){
						if($key == $value['forml_id'][$id]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
						$forms .= "<option value=\"".$key."\" $SELECTED>".$val;
					}
				}
				if($forms){
					echo "<select name=\"reminderforml[$id]\" onchange=\"document.form1.editid.value=$id;document.form1.submit();\"><option>$forms</select>";
				}
			}
			echo "</td><td>";
			if($gformlist[$tabid]["name"]){
				$forms = "";
				foreach($gformlist[$tabid]["name"] as $key => $val){
					if($gformlist[$tabid]["typ"][$key] == 1){
						if($key == $value['formd_id'][$id]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
						$forms .= "<option value=\"".$key."\" $SELECTED>".$val;
					}
				}
				if($forms){
					echo "<select name=\"reminderformd[$id]\" onchange=\"document.form1.editid.value=$id;document.form1.submit();\"><option>$forms</select>";
				}
			}
			echo "</td>";
			if($value['groupbased'][$id]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
			echo "<td align=\"center\"><input type=\"checkbox\" name=\"remindergrouping[$id]\" $CHECKED value=\"1\" onchange=\"document.form1.editid.value=$id;document.form1.submit();\"></td>";
			
			echo "</tr>";
		}
	}
}


echo "<tr><td colspan=\"8\"><hr></td></tr>";
echo "<tr class=\"tabHeader\">
        <td colspan=\"3\"></td>
	<td class=\"tabHeaderItem\">".$lang[4]."</td>
	<td class=\"tabHeaderItem\">".$lang[164]."</td>
	<td></td>
	</tr>";

echo "<tr>";
echo "<td colspan=\"3\"></td>";
echo "<td><input type=\"text\" name=\"new_remindername\"></td>";
echo "<td><select name=\"new_remindertable\"><option>";
foreach($gtab["table"] as $tabkey => $tabval){
	echo "<option value=\"".$tabkey."\">".$tabval;
}
echo "</select></td>";
echo "<td><input type=\"submit\" value=\"".$lang[2740]."\" name=\"new_reminder\"></td>";
echo "</tr>";


?>

</table>

</div>
</form>