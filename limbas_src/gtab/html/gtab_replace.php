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
 * ID: 230
 */

if(!$confirm){$confirm = 1;}
?>

<form action="main.php" method="post" id="lmbForm11" name="form11">
<input type="hidden" name="use_records">

<?php
pop_top('lmbAjaxContainer');
pop_left();
?>

<table border="0" cellspacing="0" cellpadding="1" style="min-width:300px;padding:3px;">
    <tr style="height:20px;"><td colspan="2"><i class="<?=$LINK["icon_url"][287]?>"></i>&nbsp;&nbsp;<b><?=$lang[$LINK["name"][287]]?></b></td></tr>
<tr><td colspan="2"><hr></td></tr>
<tr><td valign="top" style="width:100px;"><?=$lang[103]?>:</td>
<td><select style="width:200px;" onchange="lmb_showReplaceField(this.value);" name="grplfield"><option>
<?php
# ----------- Feldliste ------------
foreach ($gfield[$gtabid]["sort"] as $key => $value){
    if($gfield[$gtabid]["collreplace"][$key] AND $gfield[$gtabid]["perm_edit"][$key]){
		if($key == $fieldid){$SELECTED = "selected";}else{$SELECTED = "";$display = 'none';}
		echo "<option value=\"$key\" $SELECTED>".$gfield[$gtabid]["spelling"][$key];
    }
}
echo "</select></td></tr>";

echo "<tr><td colspan=\"2\"><hr></td></tr>";

if($fieldid){$display = '';}else{$display = 'none';}
$key1 = 1;
echo "<tr id=\"gsea_$key1\" style=\"display:$display;\"><td valign=\"top\">$lang[2673]</td><td valign=\"top\">";

# --------- INPUT --------------------
foreach ($gfield[$gtabid]["sort"] as $key => $value){
	if($gfield[$gtabid]["collreplace"][$key]){
	    
	    // show preselected field
	    if($key == $fieldid){$display = '';}else{$display = 'none';}
	    
		#------- BOOLEAN -------
		if($gfield[$gtabid]["field_type"][$key] == 10){
			echo "<div id=\"gse_".$key."_".$key1."\" style=\"display:$display;\">
			<input type=\"hidden\" name=\"rules_$key1\" value=\"num_rules\">
			<select style=\"width:200px;\" NAME=\"grplval[".$gtabid."][".$key."]\"><OPTION>
			<option value=\"true\">".$lang[1506]."
			<option value=\"false\">".$lang[1507]."
			</select></div>";
			# ------- Selectfelder -------
		}elseif($gfield[$gtabid]["data_type"][$key] == 12 OR $gfield[$gtabid]["data_type"][$key] == 14){
			echo "<div id=\"gse_".$key."_".$key1."\" style=\"display:$display;\">
			<select style=\"width:200px;\" name=\"grplval[".$gtabid."][".$key."]\"><option>";
			$sqlquery = "SELECT DISTINCT WERT,".$gfield[$gtabid]["select_sort"][$key]." FROM LMB_SELECT_W WHERE POOL = ".$gfield[$gtabid]["select_pool"][$key]." ORDER BY ".$gfield[$gtabid]["select_sort"][$key];
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			while(lmbdb_fetch_row($rs)) {
				echo "<option value=\"".str_replace("\"","",lmbdb_result($rs,"WERT"))."\">".lmbdb_result($rs,"WERT");
			}
			echo "</select>
			</div>";
		}elseif($gfield[$gtabid]["field_type"][$key] == 4){
			echo "<div id=\"gse_".$key."_".$key1."\" style=\"display:$display;\">
			<select multiple style=\"width:200px;\" name=\"grplval[".$gtabid."][".$key."][]\"><option>";
			$sqlquery = "SELECT DISTINCT WERT,ID,".$gfield[$gtabid]["select_sort"][$key]." FROM LMB_SELECT_W WHERE POOL = ".$gfield[$gtabid]["select_pool"][$key]." ORDER BY ".$gfield[$gtabid]["select_sort"][$key];
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			while(lmbdb_fetch_row($rs)) {
				echo "<option value=\"".str_replace("\"","",lmbdb_result($rs,"ID"))."\">".lmbdb_result($rs,"WERT");
			}
			echo "</select>
			</div>";
		}elseif($gfield[$gtabid]["parse_type"][$key] == 4){
			echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:$display;\"><input type=\"text\" style=\"width:180px;\" id=\"grplval[".$gtabid."][".$key."]\" name=\"grplval[".$gtabid."][".$key."]\" value=\"".$gsrres."\">";
			$dateformat = $gfield[$gtabid]["datetime"][$key];
			if($gfield[$gtabid]["data_type"][$key] == 40){$dateformat = 1;}
			$dateformat = dateStringToDatepicker(setDateFormat($dateformat,1));
			echo "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" OnClick=\"lmb_datepicker(event,this,'',this.value,'".$dateformat."',20)\"></i>";

			echo "<table>
			    <tr><td><input type=\"text\" name=\"agregate[".$gtabid."][".$key."][minute]\" style=\"width:30px\" onchange=\"document.getElementById('grplval\[".$gtabid."\]\[".$key."\]').value=''\"></td><td>".$lang[1980]."</td></tr>
			    <tr><td><input type=\"text\" name=\"agregate[".$gtabid."][".$key."][hour]\" style=\"width:30px\" onchange=\"document.getElementById('grplval\[".$gtabid."\]\[".$key."\]').value=''\"></td><td>$lang[1981]</td></tr>
			    <tr><td><input type=\"text\" name=\"agregate[".$gtabid."][".$key."][day]\" style=\"width:30px\" onchange=\"document.getElementById('grplval\[".$gtabid."\]\[".$key."\]').value=''\"></td><td>".$lang[1982]."</td></tr>
			    <tr><td><input type=\"text\" name=\"agregate[".$gtabid."][".$key."][month]\" style=\"width:30px\" onchange=\"document.getElementById('grplval\[".$gtabid."\]\[".$key."\]').value=''\"></td><td>".$lang[296]."</td></tr>
			    <tr><td><input type=\"text\" name=\"agregate[".$gtabid."][".$key."][year]\" style=\"width:30px\" onchange=\"document.getElementById('grplval\[".$gtabid."\]\[".$key."\]').value=''\"></td><td>".$lang[1985]."</td></tr>
			    </table>
			    ";
			
			echo "</div>";
			
		}elseif($gfield[$gtabid]["field_type"][$key] != 8 AND $gfield[$gtabid]["data_type"][$key] != 39 OR $gfield[$gtabid]["data_type"][$key] != 13){
			echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:$display;\">
			<input type=\"text\" style=\"width:200px;\" name=\"grplval[".$gtabid."][".$key."]\" value=\"".$gsrres."\">
			</div>";
		}
	}
}

echo "
</td></tr>
<tr><td></td>
<td height=\"50\">
<input type=\"button\" value=\"$lang[2460]\" name=\"search\" onclick=\"limbasCollectiveReplace(null,this,1,'$gtabid');\">&nbsp;
</td></tr>
</table>";

pop_right();
pop_bottom();

echo "</form>";
?>