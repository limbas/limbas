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
 * ID: 104
 */
?>


<FORM ENCTYPE="multipart/form-data" ACTION="main_admin.php" METHOD="post" NAME="form1">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$session[uploadsize]?>">
<input type="hidden" name="action" value="setup_fieldselect">
<input type="hidden" name="atid" value="<?echo $atid;?>">
<input type="hidden" name="fieldid" value="<?echo $fieldid;?>">
<input type="hidden" name="ID" value="<?echo $ID;?>">
<input type="hidden" name="pool" value="<?echo $pool;?>">
<input type="hidden" name="field_pool" value="<?echo $field_pool;?>">
<input type="hidden" name="num_result" value="<?echo $num_result;?>">
<input type="hidden" name="start" value="<?echo $start;?>">
<input type="hidden" name="typ" value="<?=$typ?>">
<input type="hidden" name="del_pool">
<input type="hidden" name="rename_pool">
<input type="hidden" name="select_change">
<input type="hidden" name="select_wert_change">
<input type="hidden" name="select_keyw_change">
<input type="hidden" name="select_sort">
<input type="hidden" name="select_sort_d">
<input type="hidden" name="select_del">
<input type="hidden" name="select_default">
<input type="hidden" name="select_hide">
<input type="hidden" name="set_pool">
<input type="hidden" name="viewtyp">
<input type="hidden" name="level_id" value="<?php echo isset($level_id) ? $level_id : 0;?>">

<DIV class="lmbPositionContainerMainTabPool">
<TABLE class="tabpool" BORDER="0" cellspacing="0" cellpadding="0" style="width:500"><TR><TD>


<?
if($viewtyp == "filter"){
	?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0"><TR>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.viewtyp.value='pool';document.form1.pool.value='';document.form1.submit();"><?=$lang[1830]?></TD>
	<?if($pool){?><TD nowrap class="tabpoolItemInactive" OnClick="document.form1.pool.value='';document.form1.pool.value='<?=$field_pool?>';document.form1.submit();"><?=$lang[914]?></TD>
	<TD nowrap class="tabpoolItemActive">Tools</TD><?}?>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>
	</TD></TR>

	<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR><TD>&nbsp;</TD></TR>
	
	<tr><td><?=$lang[1836]?><i> (<?=$lang[1842]?>, tab delimited)</i></SPAN></td></tr>
	<tr><td><INPUT TYPE="file" NAME="select_import"> <?=$lang[1003]?> <INPUT TYPE="CHECKBOX" NAME="select_import_add" STYLE="border:none; background-color:transparent;" CHECKED></td></tr>
	<tr><td><INPUT TYPE="submit" NAME="select_import" value="importieren"></td></tr>

	</TABLE>
	
<?

}elseif($pool){
	if($gtabid){$atid == $gtabid;}
	?>

	<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%" class="tabBody"><TR>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.viewtyp.value='pool';document.form1.pool.value='';document.form1.submit();"><?=$lang[1830]?></TD>
	<TD nowrap class="tabpoolItemActive"><?=$lang[914]?></TD>
	<TD nowrap class="tabpoolItemInactive" OnClick="document.form1.viewtyp.value='filter';document.form1.submit();">Tools</TD>
	
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>

	</TD></TR>

	<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	
	<TR><td></td><TD><INPUT TYPE="text" STYLE="width:130px;" NAME="select_pool[<?=$pool?>]" value="<?=$result_pool["name"][$pool]?>" OnChange="document.form1.rename_pool.value=<?=$pool?>;"></TD>
	<TD COLSPAN="4"><B><?=$result_fieldselect[num_ges];?></B>&nbsp;<?=$lang[1843]?>,&nbsp;<?=$lang[1844]?>&nbsp;<B><?=$result_fieldselect[num_rows];?></B>&nbsp;<?=$lang[1846]?>
	</TD></TR>
	
	<?if($fieldid){echo "<tr><td></td><td colspan='5'>".$lang[168].': <b>'.$gfield[$atid]['spelling'][$fieldid].'</b> | '.$lang[164].': <b>'.$gtab['desc'][$atid].'</b></td></tr>';}?>
	
	<tr><td colspan="6"><hr></td></tr>
	
	<?if($fieldid){?><TR><td></td><TD><?=$lang[1837]?></TD><TD COLSPAN="4"><SELECT STYLE="width:130px" NAME="fssort" STYLE="width:160px;" OnChange="document.form1.select_sort.value='1';document.form1.submit();">
	<OPTION VALUE="SORT" <?if($result_fieldselect[sort] == "SORT"){echo "SELECTED";}?>><?=$lang[1838]?><OPTION VALUE="WERT ASC" <?if($result_fieldselect[sort] == "WERT ASC"){echo "SELECTED";}?>><?=$lang[1840]?><OPTION VALUE="WERT DESC" <?if($result_fieldselect[sort] == "WERT DESC"){echo "SELECTED";}?>><?=$lang[1839]?></TD></TR><?}?>

	<TR>
		<td></td>
		<TD><INPUT TYPE="text" STYLE="width:130px;" NAME="new_wert"></TD>
		<TD><INPUT TYPE="text" STYLE="width:130px;" NAME="new_keyword"></TD>
		<?if($typ=="LMB_ATTRIBUTE"){echo "<TD><SELECT NAME=\"new_fieldtype\"><OPTION VALUE=\"8\">text<OPTION VALUE=\"17\">int<OPTION VALUE=\"19\">float<OPTION VALUE=\"40\">date<OPTION VALUE=\"20\">JaNein</SELECT></TD>";}?>
		<td></td><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[915]?>" NAME="add_select"></TD>
	</TR>
	<TR class="tabHeader"><td></td><TD><INPUT TYPE="text" STYLE="width:130px;" NAME="find_wert" VALUE="<?=htmlentities($find_wert,ENT_QUOTES,$umgvar["charset"])?>"></TD><TD><INPUT TYPE="text" STYLE="width:130px;" NAME="find_keyw" VALUE="<?=htmlentities($find_keyw,ENT_QUOTES,$umgvar["charset"])?>"></TD><TD><INPUT TYPE="TEXT" STYLE="width:40px;" NAME="num_result" VALUE="<?=$num_result?>"></TD><TD COLSPAN="2"><INPUT TYPE="submit" VALUE="<?=$lang[110]?>" NAME="search_select"></TD></TR>
<?php	
	$multi_language = $umgvar['multi_language'];
	echo "<TR class=\"tabHeader\"><td class=\"tabHeaderItem\">ID</td><TD class=\"tabHeaderItem\">".$lang[1833]."</TD>";
	if($gfield[$atid]['multilang'][$fieldid] AND $multi_language){
	    
	    require_once('admin/setup/language.lib');
	    $langdef = get_language_list();
		foreach($multi_language as $lkey => $langid){
			echo "<TD class=\"tabHeaderItem\">".$langdef['language'][$langid]."</TD>";
		}
	}
	echo "<TD class=\"tabHeaderItem\">".$lang[1834]."</TD><TD class=\"tabHeaderItem\">".$lang[1835]."</TD><TD class=\"tabHeaderItem\">".$lang[1952]."</TD><TD class=\"tabHeaderItem\"></TD></TR>";
	

$parent = array();
if(isset($result_fieldselect["parent"])
	&& (!empty($result_fieldselect["parent"]))
	&& is_array($result_fieldselect["parent"])){

	$result_fieldselect["parent"] = array_reverse($result_fieldselect["parent"],true);
	foreach($result_fieldselect["parent"] as $k => $v){
		$parent[] = "<a href=\"#\" onclick=\"document.form1.level_id.value=$k;document.form1.submit();return false;\" style=\"font-weight:bold;color:blue;\" title=\"$v\">$v</a>";
	}
}
$parent = implode("=>",$parent);
if(!empty($parent)){
	echo <<<EOD
	<TR BGCOLOR="{$farbschema["WEB9"]}"><TD colspan="5">$parent</TD></TR>
EOD;
}
?>
	<?
	/* --- Ergebnisliste --------------------------------------- */
	if($result_fieldselect[id]){
		foreach($result_fieldselect[id] as $key => $value){
			if($result_fieldselect[def][$key]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
			echo "<TR BGCOLOR=\"$farbschema[WEB9]\">
	        <td>".$result_fieldselect['id'][$key]."</td>
			<TD><INPUT TYPE=\"text\" STYLE=\"width:130px;\" NAME=\"select_wert[".$result_fieldselect[id][$key]."]\" VALUE=\"".$result_fieldselect[wert][$key]."\" OnChange=\"document.form1.select_change.value=document.form1.select_change.value+';".$result_fieldselect[id][$key]."';\"></TD>";
	        
			
			// multilang
			if($gfield[$atid]['multilang'][$fieldid] AND $multi_language){
			    foreach($multi_language as $lkey => $langid){
			         echo "<TD><INPUT TYPE=\"text\" STYLE=\"width:130px;\" NAME=\"select_wert_".$langid."[".$result_fieldselect[id][$key]."]\" VALUE=\"".$result_fieldselect['wert_'.$langid][$key]."\"  OnChange=\"document.form1.select_change.value=document.form1.select_change.value+';".$result_fieldselect[id][$key]."';\"></TD>";;
			    }
			}
			
			echo "<TD><INPUT TYPE=\"text\" STYLE=\"width:130px;\" NAME=\"select_keyw[".$result_fieldselect[id][$key]."]\" VALUE=\"".$result_fieldselect[keywords][$key]."\"  OnChange=\"document.form1.select_change.value=document.form1.select_change.value+';".$result_fieldselect[id][$key]."';\"></TD>";
			echo "<TD style=\"white-space: nowrap;\"><INPUT TYPE=\"checkbox\" $CHECKED STYLE=\"border:none;background-color:transparent;\" OnClick=\"document.form1.select_default.value='".$result_fieldselect[id][$key]."';document.form1.submit();\">&nbsp;";

			if($result_fieldselect[sort] == "SORT" OR !$result_fieldselect[sort]){
				echo "<i class=\"lmb-icon lmb-long-arrow-up\" style=\"cursor:pointer\" BORDER=\"0\" OnClick=\"document.form1.select_sort_d.value=1;document.form1.select_sort.value='".$result_fieldselect[id][$key]."';document.form1.submit();\"></i>
	        	<i class=\"lmb-icon lmb-long-arrow-down\" style=\"cursor:pointer\" BORDER=\"0\" OnClick=\"document.form1.select_sort_d.value=2;document.form1.select_sort.value='".$result_fieldselect[id][$key]."';document.form1.submit();\"></i>";
			}
			echo "</TD>";
			if($result_fieldselect[hide][$key]){$CHECKED = "CHECKED";}else{$CHECKED = "";}
			echo "<TD ALIGN=\"CENTER\"><INPUT TYPE=\"checkbox\" $CHECKED STYLE=\"border:none;background-color:transparent;\" OnClick=\"document.form1.select_hide.value='".$result_fieldselect[id][$key]."';document.form1.submit();\">&nbsp;</TD><TD>";

			global $gfield;
	    	if($gfield[$atid]["data_type"][$fieldid]==32){
    			$imgst = "";
	    		if(!$result_fieldselect["haslevel"][$key]) $imgst = "style=\"opacity:0.3;filter:Alpha(opacity=30)\"";
				echo "<a href=\"javascript:document.form1.level_id.value='".$result_fieldselect["id"][$key]."';document.form1.submit();\">"
					."<i class=\"lmb-icon lmb-connection\" $imgst border=\"0\"></i>"
					."</a><img src=\"pic/outliner/blank.gif\" border=\"0\">";
			}
			echo "<i class=\"lmb-icon lmb-trash\" style=\"cursor:pointer\" BORDER=\"0\" OnClick=\"document.form1.select_del.value='".$result_fieldselect[id][$key]."';document.form1.submit();\"></i></TD>
			</TR>";
		}}

	?>

	<TR class="tabFooter"><td colspan="4">
        <i class="lmb-icon lmb-first" STYLE="cursor:pointer" OnClick="document.form1.start.value='1';document.form1.submit();"></i>
	<i class="lmb-icon lmb-previous" STYLE="cursor:pointer"  OnClick="document.form1.start.value='<?=($start - $result_fieldselect["num_rows"])?>';document.form1.submit();"></i>&nbsp;
	<i class="lmb-icon lmb-next" STYLE="cursor:pointer"  OnClick="document.form1.start.value='<?=($start + $result_fieldselect["num_rows"])?>';document.form1.submit();"></i>
	<i class="lmb-icon lmb-last" STYLE="cursor:pointer"  OnClick="document.form1.start.value='<?=($result_fieldselect["num_ges"] - $num_result)?>';document.form1.submit();"></i>
	</TD>
	<TD COLSPAN="2"><INPUT TYPE="SUBMIT" VALUE="<?=$lang[1997]?>">
	</TR>

	</TABLE>


<?
}else{

	?>
	<TABLE BORDER="0" cellspacing="0" cellpadding="0" width="100%"><TR>
	<TD nowrap class="tabpoolItemActive"><?=$lang[1830]?></TD>
	<TD class="tabpoolItemSpace">&nbsp;</TD>
	</TR></TABLE>
	</TD></TR>

	<TR><TD class="tabpoolfringe">

	<TABLE BORDER="0" cellspacing="1" cellpadding="2" width="100%" class="tabBody">
	<TR><TD>&nbsp;</TD></TR>
	<?

	echo "<TABLE BORDER=\"0\" cellspacing=\"1\" cellpadding=\"1\" STYLE=\"width:100%;\">";
	echo "<TR class=\"tabHeader\"><TD class=\"tabHeaderItem\">ID</TD><TD class=\"tabHeaderItem\">$lang[1831]</TD><TD class=\"tabHeaderItem\">$lang[914]</TD><TD COLSPAN=\"2\">&nbsp;</TD></TR>";
	echo "<TR ><TD HEIGHT=\"25\"></TD><TD><INPUT TYPE=\"TEXT\" STYLE=\"width:200px;\" NAME=\"new_wert\"></TD><TD COLSPAN=\"3\"><INPUT TYPE=\"submit\" NAME=\"add_pool\" VALUE=\"$lang[1832]\"></TD></TR>";
	if($result_pool["id"]){
	foreach($result_pool["id"] as $key => $value){
		if($field_pool == $key){$bgcolor = $farbschema[WEB10];$CHECKED = "CHECKED";}else{$bgcolor = $farbschema[WEB7];$CHECKED = "";}
		echo "<TR class=\"tabBody\"><TD OnClick=\"document.form1.pool.value='$key';document.form1.submit();\" STYLE=\"cursor:pointer;color:blue;\">&nbsp;".$result_pool["id"][$key]."</TD>
		<TD nowrap><INPUT TYPE=\"TEXT\" NAME=\"select_pool[$key]\" VALUE=\"".htmlentities($result_pool["name"][$key],ENT_QUOTES,$umgvar["charset"])."\" OnChange=\"document.form1.rename_pool.value=$key;document.form1.submit();\" STYLE=\"border:none;background-color:transparent;width:200px;\"></TD>
		<TD nowrap>&nbsp;".$result_pool["num"][$key]."</TD>
		<TD nowrap><i class=\"lmb-icon lmb-trash\" BORDER=\"0\" OnClick=\"document.form1.del_pool.value='$key';document.form1.submit();\" STYLE=\"cursor:pointer;\"></i></TD>";
		if($atid){echo "<TD nowrap><INPUT TYPE=\"RADIO\" NAME=\"aktive_pool\" STYLE=\"border:none;background-color:transparent;\" VALUE=\"$key\" OnClick=\"document.form1.set_pool.value='$key';document.form1.submit();\" $CHECKED></TD>";}
		echo "</TD></TR>";
	}}
	echo "</TABLE>";

}




?>

</TD></TR></table>
</div>
</FORM>