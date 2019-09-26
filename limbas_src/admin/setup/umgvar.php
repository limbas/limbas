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
 * ID: 164
 */

?>
<FORM ACTION="main_admin.php" METHOD=post name="form1">
<input type="hidden" name="action" value="setup_umgvar">
<input type="hidden" name="changecat">

<div class="lmbPositionContainerMain">

	<?php
	$syscat = array(1893,1894,1895,1896,1898,2700,1899,1900,1995,2818,2819,2820,2935);
	echo "<table class=\"tabfringe\" border=\"0\" cellspacing=\"0\" cellpadding=\"1\" style=\"width:100%\">";

	/* --- Ergebnisliste --------------------------------------- */
	foreach($result_category as $value0){
		echo "<TR class=\"tabSubHeader\"><TD colspan=\"5\" class=\"tabSubHeaderItem\">$lang[$value0]</TD></TR>";
		foreach($result_umgvar["id"] as $key1 => $value1){
			if($result_umgvar["category"][$key1] == $value0){
				#$rowcol = lmb_getRowColor();
				?>
				<TR class="tabBody" style="background-color:<?=$rowcol?>" <?= ($value1 == 59 ? 'id="admin-mode"' : '') ?>>
				<TD nowrap class="tabitem" VALIGN="TOP"><?= $result_umgvar["form_name"][$key1] ?>&nbsp;</TD>
				<TD nowrap class="tabitem" VALIGN="TOP"><?= $result_umgvar["beschreibung"][$key1] ?>&nbsp;</TD>
				<TD nowrap class="tabitem" VALIGN="TOP"><INPUT TYPE="TEXT" SIZE="50" OnChange="document.form1.changecat.value=document.form1.changecat.value+',<?=$result_umgvar["id"][$key1]?>'" VALUE="<?= str_replace("\"","&quot;", str_replace("<","&lt;",$result_umgvar['norm'][$key1])) ?>" NAME="umg_<?=$result_umgvar['id'][$key1]?>"></TD>
				<?php if($umgvar["admin_mode"]){?>
				<TD  class="tabitem" ALIGN="CENTER" VALIGN="TOP"><select name="cat[<?=$result_umgvar["id"][$key1]?>]" OnChange="document.form1.changecat.value=document.form1.changecat.value+',<?=$result_umgvar["id"][$key1]?>'">
				<?php
				foreach($result_category as $value){
					if(!$umgvar["admin_mode"]){continue;}
					if($result_umgvar["category"][$key1] == $value){$SELECTED = "SELECTED";}else{$SELECTED = "";}
					echo "<option value=\"$value\" $SELECTED>".$lang[$value];
				}
				?>
				</SELECT></TD>
                <TD nowrap class="tabitem" ALIGN="CENTER" VALIGN="TOP"><A HREF="main_admin.php?action=setup_umgvar&del=1&id=<?= urlencode($result_umgvar["id"][$key1]) ?>"><i class="lmb-icon lmb-trash" BORDER="0"></i></A></TD>
				<?php
				}
			}
		}
	}
	




	?>
	
	
<TR class="tabBody"><TD COLSPAN="5"><HR></TD></TR>
<TR class="tabBody"><TD COLSPAN="5"><INPUT TYPE="submit" VALUE="<?=$lang[522]?>" NAME="change"></TD></TR>
<TR class="tabBody"><TD COLSPAN="5"><HR></TD></TR>


<?if($umgvar["admin_mode"]){?>

<TR class="tabBody"><TD><INPUT TYPE="TEXT" SIZE="20" NAME="name"></TD><TD><INPUT TYPE="TEXT" SIZE="20" NAME="beschreibung"></TD><TD><INPUT TYPE="TEXT" SIZE="50" NAME="norm"></TD>
<TD>
<?php
echo "<SELECT name=\"category\"><option value=\"\">";
foreach($syscat as $value){
	echo "<option value=\"$value\">".$lang[$value];
}
?>
</SELECT></TD>
<TD><INPUT TYPE="submit" VALUE="<?=$lang[540]?>" NAME="add"></TD>
</TR>

<TR class="tabBody"><TD></TD><TD></TD><TD></TD><TD><INPUT TYPE="TEXT" NAME="newcategory"></TD></TR>

<?}?>


<TR class="tabFooter"><TD COLSPAN="5"></TD></TR>
</TABLE><br><br>


</div>
</FORM>