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
 * ID: 180
 */
?>



<SCRIPT LANGUAGE="JavaScript">
function change_file(val) {
	if(val) {
		document.form1.EDIT.value = "a";
		document.form1.submit();
	}else{
		document.form1.EDIT.value = "FALSE";
		document.form1.submit();
	}
}
</SCRIPT>

<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" name="form1">
<input type="hidden" name="<?echo $_SID;?>" value="<?echo session_id();?>">
<input type="hidden" name="action" value="setup_language">
<input type="hidden" name="language_typ" value="<?=$language_typ?>">
<input type="hidden" name="language_id" value="<?=$language_id?>">
<input type="hidden" name="list" value="<?=$list?>">
<input type="hidden" name="order" value="<?=$order?>">
<input type="hidden" name="is_value">
<input type="hidden" name="is_js">
<input type="hidden" name="del">

<DIV class="lmbPositionContainerMainTabPool">



<TABLE class="tabpool language-table" BORDER="0" cellspacing="0" cellpadding="0"><TR><TD>
<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR class="tabpoolItemTR">
<?
if($language_typ == 1){
	if($LINK[108]){echo "<TD class=\"tabpoolItemActive\" NOWRAP>".$lang[$LINK["desc"][108]]."</TD>";}
	if($LINK[258]){echo "<TD class=\"tabpoolItemInactive\" NOWRAP OnClick=\"document.location.href='main_admin.php?action=setup_language&language_typ=2'\">".$lang[$LINK["desc"][258]]."</TD>";}
}elseif($language_typ == 2){
	if($LINK[108]){echo "<TD class=\"tabpoolItemInactive\" NOWRAP OnClick=\"document.location.href='main_admin.php?action=setup_language&language_typ=1'\">".$lang[$LINK["desc"][108]]."</TD>";}
	if($LINK[258]){echo "<TD class=\"tabpoolItemActive\" NOWRAP>".$lang[$LINK["desc"][258]]."</TD>";}
}
?>
<TD class="tabpoolItemSpace">&nbsp;</TD>
</TR></TABLE>
</TD></TR>
<TR><TD class="tabpoolfringe">


<?if($language_id AND $list){?>

	<TABLE BORDER="0" cellspacing="1" cellpadding="0">
	<TR class="tabHeader">
	<TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='ELEMENT_ID';document.form1.submit();"><?=$lang[1208]?></A></TD>
    <TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='EDIT';document.form1.submit();"><?=$lang[1209]?></A> </TD>
    <TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='LANGUAGE';document.form1.submit();"><?=$lang[1210]?></A> </TD>
	<TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='TYP';document.form1.submit();"><?=$lang[1211]?></A> </TD>
	<TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='FILE';document.form1.submit();"><?=$lang[1212]?></A> </TD>
    <TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='REF';document.form1.submit();"><?=$lang[1213]?></A></TD>
    <TD class="tabHeaderItem"><A HREF="#" OnClick="document.form1.order.value='WERT';document.form1.submit();"><?=$lang[1213]?></A> </TD>
    <TD class="tabHeaderItem">JS</TD>
	<TD class="tabHeaderItem"><?=$lang[1214]?></TD>
	</TR>

	<TR>
	<TD VALIGN="TOP" WIDTH="5"><INPUT TYPE="TEXT" SIZE="5" NAME="ID" VALUE="<?=$ID?>" OnChange="document.form1.submit();"></TD>
	<TD VALIGN="TOP" WIDTH="10"><SELECT NAME="EDIT" OnChange="document.form1.submit();">
	<OPTION VALUE="a" <?if($EDIT == 'a'){echo "SELECTED";}?>><?=$lang[1216]?>
	<OPTION VALUE="FALSE" <?if($EDIT == 'FALSE'){echo "SELECTED";}?>><?=$lang[1217]?>
	<OPTION VALUE="TRUE" <?if($EDIT == 'TRUE'){echo "SELECTED";}?>><?=$lang[1218]?>
	</SELECT></TD>
	<TD VALIGN="TOP" WIDTH="10"><SELECT NAME="language_id" OnChange="document.form1.submit();">
    <?$bzm = 0;
    while($result_language["language_id"][$bzm]){
    	if($result_language["language_id"][$bzm] == $language_id){$SELECTED = "SELECTED";}else{$SELECTED = "";}
        echo "<OPTION VALUE=\"".$result_language["language_id"][$bzm]."\" $SELECTED>".$result_language["language"][$bzm];
    $bzm++;
    }?>
	</SELECT></TD>
	<TD VALIGN="TOP" WIDTH="10"><SELECT NAME="TYP" OnChange="document.form1.submit();">
	<OPTION VALUE="0" <?if($TYP == 0){echo "SELECTED";}?>><?=$lang[1216]?>
	<OPTION VALUE="1" <?if($TYP == 1){echo "SELECTED";}?>><?=$lang[1219]?>
	<OPTION VALUE="3" <?if($TYP == 3){echo "SELECTED";}?>><?=$lang[1220]?>
	<OPTION VALUE="2" <?if($TYP == 2){echo "SELECTED";}?>><?=$lang[1221]?>
	<OPTION VALUE="4" <?if($TYP == 4){echo "SELECTED";}?>><?=$lang[2276]?>
	</SELECT></TD>
	<TD VALIGN="TOP" WIDTH="5" STYLE="width:5"><INPUT TYPE="TEXT" SIZE="5" NAME="FILE" VALUE="<?=$FILE?>" OnChange="change_file(this.value)"></TD>
	<TD VALIGN="TOP" nowrap><SELECT NAME="ref_id" OnChange="document.form1.submit();">
    <?$bzm = 0;
    while($result_language["language_id"][$bzm]){
    	if($result_language["language_id"][$bzm] == $ref_id){$SELECTED = "SELECTED";}else{$SELECTED = "";}
        echo "<OPTION VALUE=\"".$result_language["language_id"][$bzm]."\" $SELECTED>".$result_language["language"][$bzm];
    $bzm++;
    }?>
	</SELECT>
	<INPUT TYPE="TEXT" SIZE="40" NAME="REFWERT" VALUE="<?=$REFWERT?>" OnChange="document.form1.submit();">&nbsp;
	</TD>
	<TD VALIGN="TOP" WIDTH="50"><INPUT TYPE="TEXT" SIZE="50" NAME="WERT" STYLE="width:250px" VALUE="<?=$WERT?>" OnChange="document.form1.submit();"></TD>
	<TD VALIGN="TOP" ALIGN="center" WIDTH="5" STYLE="width:5"><INPUT TYPE="checkbox" name="JS" OnChange="document.form1.submit();" <?if($JS){echo "checked";}?>></TD>
	<TD VALIGN="TOP" WIDTH="5" STYLE="width:5"></TD>
	</TR>
	
	<TR><TD colspan="10">&nbsp;</TD></TR>

	<?
	/* --- Ergebnisliste bei Refrenzsuche--------------------------------------- */
    $bzm = 0;
    if($result_ref["element_id"] AND $revvalue){
    foreach($result_ref["element_id"] as $val){
		if($result_el["id"][$val]){
    	if($result_el["js"][$val]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		?>
			<TR class="tabBody">
			<TD VALIGN="TOP">&nbsp;<?echo $result_el["element_id"][$val];?>&nbsp;</TD>
            <TD VALIGN="TOP" ALIGN="CENTER">&nbsp;<?if($result_el["edit"][$val] == 1){echo "<i class=\"lmb-icon lmb-aktiv\"></i>";}else{echo "<i class=\"lmb-icon lmb-erase\"></i>";}?>&nbsp;</TD>
            <TD VALIGN="TOP">&nbsp;<?echo $result_el["language"][$val];?>&nbsp;</TD>
            <TD VALIGN="TOP"><?echo $result_el["typ"][$val];?></TD>
            <TD VALIGN="TOP"><?echo $result_el["file"][$val];?></TD>
            <TD VALIGN="TOP"><?echo $result_ref["wert"][$val];?></TD>
            <TD VALIGN="TOP"><TEXTAREA style="heigt:20px;width:250px;" NAME="new_value[<?=$result_el["id"][$val]?>]" OnChange="document.form1.is_value.value=document.form1.is_value.value+';<?=$result_el["id"][$val]?>'"><?echo htmlentities($result_el["wert"][$val],ENT_QUOTES,$umgvar["charset"]);?></TEXTAREA></TD>
            <TD VALIGN="TOP" ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="new_js[<?=$result_el["id"][$val]?>]" <?=$CHECKED?> OnChange="document.form1.is_js.value=document.form1.is_js.value+';<?=$result_el["id"][$val]?>'"></TD>
            <TD VALIGN="TOP" ALIGN="CENTER"><i class="lmb-icon lmb-trash"></i></TD>
            </TR>
		<?
		}
	if($bzm > $showlimit){break;}
    $bzm++;
    }
    /* --- Ergebnisliste bei Normalsuche --------------------------------------- */
    }elseif($result_el["element_id"] AND !$REFWERT){
    foreach($result_el["element_id"] as $val){
		if($result_el["id"][$val]){
    	if($result_el["js"][$val]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		?>
			<TR class="tabBody">
			<TD VALIGN="TOP">&nbsp;<?echo $result_el["element_id"][$val];?>&nbsp;</TD>
            <TD VALIGN="TOP" ALIGN="CENTER">&nbsp;<?if($result_el["edit"][$val] == 1){echo "<i class=\"lmb-icon lmb-aktiv\"></i>";}else{echo "<i class=\"lmb-icon lmb-erase\"></i>";}?>&nbsp;</TD>
            <TD VALIGN="TOP">&nbsp;<?echo $result_el["language"][$val];?>&nbsp;</TD>
            <TD VALIGN="TOP"><?echo $result_el["typ"][$val];?></TD>
            <TD VALIGN="TOP"><?echo $result_el["file"][$val];?></TD>
            <TD VALIGN="TOP"><?echo $result_ref["wert"][$val];?></TD>
            <TD VALIGN="TOP"><TEXTAREA style="heigt:20px;width:250px;" NAME="new_value[<?=$result_el["id"][$val]?>]" OnChange="document.form1.is_value.value=document.form1.is_value.value+';<?=$result_el["id"][$val]?>'"><?echo htmlentities($result_el["wert"][$val],ENT_QUOTES,$umgvar["charset"]);?></TEXTAREA></TD>
            <TD VALIGN="TOP" ALIGN="CENTER"><INPUT TYPE="CHECKBOX" NAME="new_js[<?=$result_el["id"][$val]?>]" <?=$CHECKED?> OnChange="document.form1.is_js.value=document.form1.is_js.value+';<?=$result_el["id"][$val]?>'"></TD>
            <TD VALIGN="TOP" ALIGN="CENTER"><i class="lmb-icon lmb-trash" OnClick="document.form1.del.value='<?=$val?>';document.form1.submit();" STYLE="cursor:pointer"></i></TD>
            </TR>
		<?
		}
	if($bzm > $showlimit){break;}
    $bzm++;
    }
    }

	?>
	<TR><TD COLSPAN="9"><HR></TD></TR>
    <TR><TD COLSPAN="6"></TD><TD><?=$lang[96]?>: <select name="showlimit" onchange="this.form.submit();">
    <option value="100" <?if($showlimit == 100){echo "selected";}?>>100
    <option value="500" <?if($showlimit == 500){echo "selected";}?>>500
    <option value="9000" <?if($showlimit == 9000){echo "selected";}?>><?=$lang[994]?>
    </select></TD><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1215]?>" NAME="change" style="width:100px;"></TD></TR>
    <TR><TD COLSPAN="9"><HR></TD></TR>
    <TR>
    <TD></TD>
    <TD></TD>
    <TD></TD>
    <TD><SELECT NAME="typ">
    <OPTION VALUE="1" <?if($typ == 1){echo "SELECTED";}?>><?=$lang[1219]?>
    <OPTION VALUE="3" <?if($typ == 3){echo "SELECTED";}?>><?=$lang[1220]?>
    <OPTION VALUE="2" <?if($typ == 2){echo "SELECTED";}?>><?=$lang[1221]?>
    <OPTION VALUE="4" <?if($typ == 4){echo "SELECTED";}?>><?=$lang[2276]?>
    </SELECT></TD>

    <TD><INPUT TYPE="TEXT" SIZE="10" NAME="file" VALUE="<?=$FILE?>"></TD>
    <TD></TD>
    <TD><INPUT TYPE="TEXT" SIZE="50" NAME="wert"></TD>
    <TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[1206]?>" NAME="add" style="width:100px;"></TD>
    </TR>
    <TR><TD colspan="10" class="tabFooter"></TD></TR>
    </TABLE>

	<br><br>

<?}else{

    $multi_language = $umgvar['multi_language'];
    
    if($language_typ == 2){$mheader = '<TD>'.$lang[2897].'</TD>';}

    echo "<TABLE WIDTH=\"350\" class=\"tabBody\">";
    #echo "<TR><TD colspan=\"4\" class=\"tabHeader\">".$lang[1222]."</TD></TR>";
    echo "<TR class=\"tabSubHeader\"><TD width=\"5\" class=\"tabHeaderItem\">default</TD><TD class=\"tabHeaderItem\">$lang[1204]</TD><TD class=\"tabHeaderItem\">$lang[1205]</TD>$mheader<TD>&nbsp;</TD></TR>";

    foreach ($result_language["language_id"] as $bzm => $lval){
    	echo "<TR>";
        if($result_language["edit"][$bzm]){$edit_color = "red";}else{$edit_color = "green";}
        if($umgvar['default_language'] == $result_language["language_id"][$bzm]){echo '<TD width=\"5\"><center><b>*</b></center></TD>';}else{echo '<TD width=\"5\"></TD>';}
        echo "<TD><A HREF=main_admin.php?action=setup_language&list=1&language_id=".$result_language["language_id"][$bzm]."&language_typ=$language_typ>".$result_language["language"][$bzm]."</A></TD>";
        echo "<TD ALIGN=\"LEFT\"><FONT COLOR=\"$edit_color\">".$result_language[edit][$bzm]."</TD>";
        
        // skip default language
        if($language_typ == 2){
        if ($result_language["language_id"][$bzm] != $umgvar['default_language']) {
            if(in_array($result_language["language_id"][$bzm],$multi_language)){$CHECKED = 'CHECKED';}else{$CHECKED = '';}
            echo "<TD ALIGN=\"CENTER\"><input type=\"checkbox\" $CHECKED onclick=\"if(confirm('".$lang[2898]."')){document.location.href='main_admin.php?action=setup_language&language_typ=$language_typ&language_id=".$result_language["language_id"][$bzm]."&multilang='+this.checked;}\"></TD>";
        }else{
            echo "<td></td>";
        }
        }
        echo "<TD ALIGN=\"CENTER\"><A HREF=main_admin.php?".SID.".&action=setup_language&del_lang=1&language_id=".$result_language["language_id"][$bzm]."&language_typ=$language_typ><i class=\"lmb-icon lmb-trash\" BORDER=\"0\"></i></A></TD>";
        echo "</TR>";
    }
    echo "<TR><TD></TD><TD>&nbsp;</TD></TR>";
    echo "<TR><TD></TD><TD><INPUT TYPE=\"TEXT\" SIZE=\"15\" NAME=\"add_lang\"></TD><TD><INPUT TYPE=\"submit\" VALUE=\"$lang[1206]\"></TD></TR>";
    echo "<TR><TD></TD><TD colspan=\"3\" class=\"tabFooter\"></TD></TR>";
    echo "</TABLE>";
    
    
	/*
	echo "</TD><TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD><TD VALIGN=\"TOP\"><br>";

    echo "<TABLE class=\"tabBody\">";
    echo "<TR><TD colspan=\"3\" class=\"tabHeader\">".$lang[1203]."</TD></TR>";
    echo "<TR class=\"tabSubHeader\"><TD>&nbsp;</TD><TD>&nbsp;</TD></TR>";
    echo "<TR><TD><SELECT NAME=\"import_lang\">";
    $bzm = 0;
    while($result_language["language_id"][$bzm]){
    	if($result_language["edit"][$bzm]){$edit_color = "red";}else{$edit_color = "green";}
        echo "<OPTION VALUE=\"".$result_language["language_id"][$bzm]."\">".$result_language[language][$bzm];
    $bzm++;
    }
    echo "</SELECT>&nbsp;</TD>";
    echo "<TD><INPUT TYPE=\"FILE\" NAME=\"import\"></TD></TR>";
    echo "<TR><TD COLSPAN=\"2\" ALIGN=\"RIGHT\"><INPUT TYPE=\"SUBMIT\" VALUE=\"$lang[1207]\"></TD></TR></TABLE>";
    echo "<TR><TD colspan=\"3\" class=\"tabFooter\"></TD></TR>";
	echo "</TD></TR></TABLE>";
	*/
	

}?>

<DIV>

</FORM>