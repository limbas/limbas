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
 * ID: 106
 */
?>
<Script language="JavaScript">
function change_color(id,color){
        document.getElementById(id).style.background=color;
}
</Script>

<FORM ACTION="main_admin.php" METHOD=post name="form1">
<input type="hidden" name="action" value="setup_color_schema">
<input type="hidden" name="def">


<div class="lmbPositionContainerMain">

<TABLE class="tabfringe" BORDER="0" cellspacing="1" cellpadding="0">

<TR class="tabHeader"><TD class="tabHeader"></TD><TD class="tabHeaderItem">WEB3</TD><TD class="tabHeaderItem">WEB5</TD><TD class="tabHeaderItem">WEB6</TD><TD class="tabHeaderItem">WEB1</TD><TD class="tabHeaderItem">WEB7</TD><TD class="tabHeaderItem">WEB9</TD><TD class="tabHeaderItem">WEB8</TD><TD class="tabHeaderItem">WEB12</TD><TD class="tabHeaderItem">WEB4</TD><TD class="tabHeaderItem">WEB2</TD><TD class="tabHeaderItem">WEB10</TD><TD class="tabHeaderItem">WEB11</TD><TD class="tabHeaderItem">WEB13(bg)</TD><TD class="tabHeaderItem">WEB14(bg)</TD><TD class="tabHeaderItem"></TD></TR>

	<?php
	/* --- Ergebnisliste --------------------------------------- */
	foreach ($result_colors["id"] as $bzm => $value){
       echo "<TR class=\"tabBody\"><TD colspan=\"16\"><B>(".$value.") ".$result_colors["NAME"][$bzm]."</B></TD></TR>";
		?>
			<TR class="tabBody">
			<TD><?=$lang[529]?></TD>

			<TD style="background-color:<?= $result_colors['WEB3'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB3'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB3'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB3'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB3'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB5'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB5'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB5'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB5'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB5'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB6'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB6'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB6'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB6'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB6'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB1'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB1'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB1'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB1'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB1'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB7'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB7'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB7'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB7'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB7'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB9'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB9'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB9'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB9'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB9'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB8'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB8'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB8'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB8'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB8'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB12'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB12'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB12'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB12'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB12'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB4'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB4'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB4'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB4'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB4'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB2'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB2'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB2'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB2'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB2'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB10'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB10'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB10'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB10'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB10'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB11'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB11'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB11'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB11'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB11'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB13'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB13'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB13'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB13'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB13'][$bzm], 5, 2)) ?></TD>
			<TD style="background-color:<?= $result_colors['WEB14'][$bzm] ?>; color:<?= lmbSuggestColor($result_colors['WEB14'][$bzm]) ?>;"><?= hexdec(lmb_substr($result_colors['WEB14'][$bzm], 1, 2)).",".hexdec(lmb_substr($result_colors['WEB14'][$bzm], 3, 2)).",".hexdec(lmb_substr($result_colors['WEB14'][$bzm], 5, 2)) ?></TD>
            </TR>

			<TR class="tabBody">
			<TD><?=$lang[530]?></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_3" VALUE="<?=$result_colors['WEB3'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_5" VALUE="<?=$result_colors['WEB5'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_6" VALUE="<?=$result_colors['WEB6'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_1" VALUE="<?=$result_colors['WEB1'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_7" VALUE="<?=$result_colors['WEB7'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_9" VALUE="<?=$result_colors['WEB9'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_8" VALUE="<?=$result_colors['WEB8'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_12" VALUE="<?=$result_colors['WEB12'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_4" VALUE="<?=$result_colors['WEB4'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_2" VALUE="<?=$result_colors['WEB2'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_10" VALUE="<?=$result_colors['WEB10'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_11" VALUE="<?=$result_colors['WEB11'][$bzm]?>"></TD>
			<TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_13" VALUE="<?=$result_colors['WEB13'][$bzm]?>"></TD>
            <TD><INPUT STYLE="width:70px;" TYPE="TEXT" SIZE="10" NAME="c_<?=$result_colors["id"][$bzm]?>_14" VALUE="<?=$result_colors['WEB14'][$bzm]?>"></TD>
            
            <td align="center"><A HREF="main_admin.php?action=setup_color_schema&del=1&id=<?=$value?>"><i class="lmb-icon lmb-trash" BORDER="0"></i></A></td>
            
			</TR>
		<?php
	}

	?>


<TR class="tabBody"><TD colspan="16"><HR></TD></TR>
<TR class="tabBody"><TD COLSPAN="16"><INPUT TYPE="submit" VALUE="<?=$lang[522]?>" NAME="change"></TD></TR>
<TR class="tabBody"><TD colspan="16"><HR></TD></TR>

<TR class="tabBody">
<TD colspan=16>
<INPUT TYPE="TEXT" SIZE="20" NAME="name">
<select name="layout">
<?php
if($path = read_dir($umgvar["pfad"]."/layout")){
if(!$result_user["layout"]){$result_user["layout"] = $umgvar["default_layout"];}
foreach($path["name"] as $key => $value){
	if($path["typ"][$key] == "dir"){
		echo "<OPTION VALUE=\"".$value."\">".$value;
	}
}
}
?>
</select>
<INPUT TYPE="submit" VALUE="<?=$lang[540]?>" NAME="add">
</TD>
</TR>
<TR><TD colspan="16" class="tabFooter"></TD></TR>
</TABLE><br><br></div>
</FORM>
