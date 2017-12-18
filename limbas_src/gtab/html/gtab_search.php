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
 * ID: 19
 */
?>

<FORM ACTION="main.php" METHOD="post" name="form11" id="form11">
<input type="hidden" name="action" value="gtab_erg">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="gfrist">
<input type="hidden" name="LID">
<input type="hidden" name="next" value="1">
<input type="hidden" name="supersearch" value="1">
<input type="hidden" name="filter_reset">
<input type="hidden" name="fieldid">
<input type="hidden" name="snap_id">

<TABLE class="lmbContextMenu" BORDER="0" cellspacing="0" cellpadding="1" style="padding:3px;">
<TR><TD VALIGN="TOP"><B><?=$lang[103]?>: </B></TD>
<TD><SELECT STYLE="width:200px;" OnChange="LmGs_divchange(this.value);"><OPTION>
<?php

if(!$fieldid){
	$fieldid = $gfield[$gtabid]["sort"][key($gfield[$gtabid]["sort"])];
}

# ----------- Feldliste ------------
foreach ($gfield[$gtabid]["sort"] as $key => $value){
	if($gfield[$gtabid][field_type][$key] != 13){
                /* removed, so the user has to select something. without selections, nothing was shown */
		//if($key == $fieldid){$SELECTED = "SELECTED";}else{$SELECTED = "";}
		echo "<OPTION VALUE=\"$key\" $SELECTED>".$gfield[$gtabid][spelling][$key];
	}
}
echo "</SELECT>";
echo "</TD><TD></TD></TR>";

echo "<TR><TD COLSPAN=\"4\"><HR></TD></TR>";

for($key1=0;$key1<=($umgvar['searchcount']-1);$key1++){
	echo "<TR ID=\"gsea_$key1\" style=\"display:none;\" class=\"tabHeader\">";
	echo "<TD VALIGN=\"TOP\" WIDTH=\"115\">";

	# --------- AND / OR --------------------
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		echo "<div ID=\"gseo_".$key."_".$key1."\" style=\"display:none;\">";
		if(!$gsr[$gtabid][$key][string][0]){
			if($key1 > 0){
				echo "<SELECT ID=\"gdsandor_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][andor][".$key1."]\">";
				echo "<OPTION VALUE=\"1\"";
				if($gsr[$gtabid][$key]["andor"][$key1] == 1){echo " SELECTED";}
				echo ">".$lang[854];
				echo "<OPTION VALUE=\"2\"";
				if($gsr[$gtabid][$key]["andor"][$key1] == 2){echo " SELECTED";}
				echo ">".$lang[855];
				echo "</SELECT>";
			}else{
				echo "<B>".$lang[102].": </B>";
			}
		}
		echo "</div>";
	}
	echo "</TD><TD WIDTH=\"220\" VALIGN=\"TOP\">";
	# --------- INPUT --------------------
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		$gsrres = $gsr[$gtabid][$key][$key1];
		if($gfield[$gtabid][field_type][$key] != 13){
			#------- BOOLEAN -------
	        if($gfield[$gtabid][field_type][$key] == 10){
	                echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:none;\">";
	                echo "<INPUT TYPE=\"HIDDEN\" NAME=\"rules_$key1\" VALUE=\"num_rules\">";
	                echo "<SELECT STYLE=\"width:200px;\" ID=\"gds_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][".$key1."]\"><OPTION>
	                <OPTION VALUE=\"TRUE\"";
	                if($gsrres == LMB_DBDEF_TRUE){echo " SELECTED";}
	                echo ">$lang[1506]<OPTION VALUE=\"FALSE\"";
	                if($gsrres == LMB_DBDEF_FALSE){echo " SELECTED";}
	                echo ">$lang[1507]</SELECT>";
	                echo "</div>";
	        # ------- Selectfelder -------
	        }elseif($gfield[$gtabid][field_type][$key] == 4 AND $gfield[$gtabid][artleiste][$key]){
	                echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:none;\">";
	                echo "<SELECT STYLE=\"width:200px;\" ID=\"gds_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][".$key1."]\" onClick=\"setTimeout('this.focus()',100);\"><OPTION>";
	                if(!$gfield[$gtabid][select_sort][$key]){$gfield[$gtabid][select_sort][$key] = "SORT";}
	                $sqlquery = "SELECT DISTINCT WERT,".$gfield[$gtabid][select_sort][$key]." FROM LMB_SELECT_W WHERE POOL = ".$gfield[$gtabid][select_pool][$key]." ORDER BY ".$gfield[$gtabid][select_sort][$key];
	                $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
	                $key2 = 1;
	                while(odbc_fetch_row($rs, $key2)) {
	                        if(lmb_strtolower($gsrres) == lmb_strtolower(odbc_result($rs,"WERT"))){$SELECTED = "SELECTED";}else{$SELECTED = "";}
	                        echo "<OPTION VALUE=\"".str_replace("\"","",odbc_result($rs,"WERT"))."\" $SELECTED>".odbc_result($rs,"WERT");
	                $key2++;
	                }
	                echo "</SELECT>";
	                echo "</div>";
	        # ------- Upload/Memo (Index) ------
	        }elseif($gfield[$gtabid]["data_type"][$key] == 39 OR $gfield[$gtabid]["data_type"][$key] == 13){
	                echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:none;\">";
	                echo "<Input TYPE=\"TEXT\" STYLE=\"width:200px;\" ID=\"gds_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][".$key1."]\" VALUE=\"".$gsrres."\"";
	                if($key1 == 0){
	                	echo " OnChange=\"limbasCheckforindex(this.value,'$key','$gtabid')\">";
	                }else{echo ">";}

					if($gsr[$gtabid][$key][string][$key1] AND $gsr[$gtabid][$key][string][$key1+1]){$dspl = '';}else{$dspl = 'display:none;';}
					echo "<i class=\"lmb-icon-cus lmb-pfeildown2\" ID=\"indpic_".$key."_".$key1."\" STYLE=\"$dspl\"></i>";
					echo "<INPUT TYPE=\"HIDDEN\" ID=\"gds_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][string][".$key1."]\" VALUE=\"".$gsr[$gtabid][$key][string][$key1]."\">";
					echo "</div>";
	        }elseif($gfield[$gtabid]["field_type"][$key] != 8){
	                echo "<div ID=\"gse_".$key."_".$key1."\" style=\"display:none;\">";
	                echo "<input TYPE=\"TEXT\" STYLE=\"width:200px;\" ID=\"gds_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][".$key1."]\" VALUE=\"".$gsrres."\">";
	                if($gfield[$gtabid]["parse_type"][$key] == 4){
	                	$dateformat = $gfield[$gtabid]["datetime"][$key];
	                	if($gfield[$gtabid]["data_type"][$key] == 40){$dateformat = 1;}
	                	$dateformat = dateStringToDatepicker(setDateFormat($dateformat,1));
	                	echo "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" OnClick=\"lmb_datepicker(event,this,'',this.value,'".$dateformat."',20)\"></i>";
	                }
	                echo "</div>";
	        }
		}
	}
	echo "</TD>";




	# --------- parameter --------------------
	echo "<TD VALIGN=\"TOP\">\n";
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		if($gfield[$gtabid][field_type][$key] == 2 OR $gfield[$gtabid][field_type][$key] == 7 OR $gfield[$gtabid][field_type][$key] == 5 OR $gfield[$gtabid][field_type][$key] == 12 OR $gfield[$gtabid][field_type][$key] == 15){
			echo "<div ID=\"gser_".$key."_".$key1."\" style=\"display:none;\">";
			echo "<SELECT ID=\"gdsnum_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][num][".$key1."]\" STYLE=\"width:150px;\">";
			echo "<OPTION VALUE=\"1\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 1){echo " SELECTED";}
			echo ">$lang[713]";
			echo "<OPTION VALUE=\"2\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 2){echo " SELECTED";}
			echo ">$lang[711]";
			echo "<OPTION VALUE=\"3\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 3){echo " SELECTED";}
			echo ">$lang[712]";
			echo "<OPTION VALUE=\"5\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 5){echo " SELECTED";}
			echo ">$lang[711] $lang[713]";
			echo "<OPTION VALUE=\"4\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 4){echo " SELECTED";}
			echo ">$lang[712] $lang[713]";
			echo "<OPTION VALUE=\"6\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 6){echo " SELECTED";}
			echo ">$lang[2683]";
			
			echo "<OPTION VALUE=\"7\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 7){echo " SELECTED";}
			echo ">$lang[2681]";
			echo "<OPTION VALUE=\"8\"";
			if($gsr[$gtabid][$key]["num"][$key1] == 8){echo " SELECTED";}
			echo ">$lang[2682]";

			echo "</SELECT>";
			echo "</div>";
		}elseif($gfield[$gtabid]["field_type"][$key] == 10){
			echo "<div ID=\"gser_".$key."_".$key1."\" style=\"display:none;\">";
			echo "</div>";
		}else{
			echo "<div ID=\"gser_".$key."_".$key1."\" style=\"display:none;\">";
			echo "<SELECT ID=\"gdstxt_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$key."][txt][".$key1."]\" STYLE=\"width:150px;\" OnChange=\"if(this.selectedIndex == 5){};limbasCheckforindex(this.value)\">";
			echo "<OPTION VALUE=\"2\"";
			if($gsr[$gtabid][$key]["txt"][$key1] == 2){echo " SELECTED";}
			echo ">$lang[107]";
			echo "<OPTION VALUE=\"1\"";
			if($gsr[$gtabid][$key]["txt"][$key1] == 1){echo " SELECTED";}
			echo ">$lang[106]";
			echo "<OPTION VALUE=\"3\"";
			if($gsr[$gtabid][$key]["txt"][$key1] == 3){echo " SELECTED";}
			echo ">$lang[108]";
			if($gfield[$gtabid]["data_type"][$key] == 39 OR $gfield[$gtabid]["data_type"][$key] == 13){
				echo "<OPTION VALUE=\"4\"";
				if($gsr[$gtabid][$key]["txt"][$key1] == 4){echo " SELECTED";}
				echo ">$lang[1597]";
			}
			
			echo "<OPTION VALUE=\"7\"";
			if($gsr[$gtabid][$key]["txt"][$key1] == 7){echo " SELECTED";}
			echo ">$lang[2681]";
			echo "<OPTION VALUE=\"8\"";
			if($gsr[$gtabid][$key]["txt"][$key1] == 8){echo " SELECTED";}
			echo ">$lang[2682]";
			
			echo "</SELECT>";
			echo "</div>";
		}
	}
	# --------- CS --------------------
	echo "<span>";
	foreach ($gfield[$gtabid]["sort"] as $key => $value){
		if($gfield[$gtabid][field_type][$key] == 2 OR $gfield[$gtabid][field_type][$key] == 7 OR $gfield[$gtabid][field_type][$key] == 5 OR $gfield[$gtabid][field_type][$key] == 15){
			echo "<div ID=\"gsec_".$key."_".$key1."\" style=\"display:none;\">";
			echo "</div>";
		}elseif($gfield[$gtabid][field_type][$key] == 10){
			echo "<div ID=\"gsec_".$key."_".$key1."\" style=\"display:none;\">";
			echo "</div>";
		}else{
			echo "<div ID=\"gsec_".$key."_".$key1."\" style=\"display:none\">";
			if((($gfield[$gtabid]["data_type"][$key] == 39 OR $gfield[$gtabid]["data_type"][$key] == 13) AND $umgvar[indize_cs]) OR ($gfield[$gtabid]["data_type"][$key] != 39 AND $gfield[$gtabid]["data_type"][$key] != 13)){
			echo "$lang[109]&nbsp;<INPUT TYPE=\"CHECKBOX\" VALUE=\"1\" ID=\"gdscs_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$gfield[$gtabid]["field_id"][$key]."][cs][".$key1."]\" STYLE=\"border:none;\"";
			if($gsr[$gtabid][$gfield[$gtabid]["field_id"][$key]]["cs"][$key1] == 1){echo " CHECKED";}
			echo ">";

			echo "negation&nbsp;<INPUT TYPE=\"CHECKBOX\" VALUE=\"1\" ID=\"gdsneg_".$gtabid."_".$key."_".$key1."\" NAME=\"gs[".$gtabid."][".$gfield[$gtabid]["field_id"][$key]."][neg][".$key1."]\" STYLE=\"border:none;\"";
			if($gsr[$gtabid][$gfield[$gtabid]["field_id"][$key]]["neg"][$key1] == 1){echo " CHECKED";}
			echo ">";
			}
			echo "</div>";
		}
	}
	echo "</span></TD></TR>\n";
}


echo "<TR><TD COLSPAN=\"4\">&nbsp;</TD></TR>";

if($gsr[$gtabid][andor] == 2){$or = "CHECKED";}else{$and = "CHECKED";}
echo "<TR><TD style=\"vertical-align:bottom;\"><B>$lang[1827]:</B></TD><TD>$lang[854]<INPUT TYPE=\"radio\" NAME=\"gs[".$gtabid."][andor]\" VALUE=\"1\" style=\"vertical-align:bottom;\" $and>&nbsp;$lang[855]<INPUT TYPE=\"radio\" NAME=\"gs[".$gtabid."][andor]\" VALUE=\"2\" style=\"vertical-align:bottom;\" $or></TD></TR>";

?>

<TR><TD></TD><TD HEIGHT="50"><INPUT TYPE="button" VALUE="<?=$lang[110]?>" NAME="search" OnClick="LmGs_sendForm();">&nbsp;<INPUT TYPE="button" VALUE="reset" OnClick="LmGs_sendForm(1);"></TD></TR>


<tr><td colspan="2"><i style="cursor:pointer" onclick="document.getElementById('lmbsearchhelp').style.display='';" class="lmb-icon lmb-help"></i></td></tr>

<tr><td colspan="4">

<table id="lmbsearchhelp" style="display:none">
<tr><td colspan="2"><?=$lang[2693]?>:</td></tr>
<tr><td><?=$lang[711]?></td><td><b>> / >=</b></td></tr>
<tr><td><?=$lang[712]?></td><td><b>< / <=</b></td></tr>
<tr><td><?=$lang[2683]?></td><td><b>!=</b></td></tr>
<tr><td><?=$lang[2681]?></td><td><b>#NULL#</b></td></tr>
<tr><td><?=$lang[2682]?></td><td><b>#NOTNULL#</b></td></tr>
<tr><td colspan="2"><br>Date-Examples:</td></tr>
<tr><td>Date</td><td><b>01.05.2011 10:25</b></td></tr>
<tr><td>Date</td><td><b>01 may 2011</b></td></tr>
<tr><td>Year</td><td><b>2011</b></td></tr>
<tr><td>Month of Year</td><td><b>05 2011</b></td></tr>
<tr><td>Week of Year</td><td><b>CW24 2011 | KW 24</b></td></tr>

</table>
</td></tr>

</TABLE>
</FORM>