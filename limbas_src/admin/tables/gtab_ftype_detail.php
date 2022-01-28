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
 * ID:
 */


$fieldid = $par["fieldid"];
$gtabid = $par["gtabid"];
$tab_group = $par["tab_group"];
$act = $par["act"];
$val = $par["val"];
$solve_dependency = $par["solve_dependency"];

if($act){
	${$act} = $val;
}
$atid = $gtabid;
$ftid = $fieldid;
if($gtab["typ"][$gtabid] == 5){$isview = 1;}

require_once("admin/tables/gtab_ftype.dao");

echo "<form action=\"main_dyns_admin.php\" method=\"post\" name=\"form2\">";
echo "<input type=\"hidden\" name=\"val\">";
echo "<input type=\"hidden\" name=\"solve_dependency\">";
echo "</form>";

	
?>
	<table border="0" cellspacing="0" cellpadding="0" style="width:550px;">
	
	<tr><td>
	<table border="0" cellspacing="0" cellpadding="0" width="100%"><tr class="tabpoolItemTR">
	<td nowrap id="menu1" onclick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[2795]?></td>
	<td nowrap id="menu2" onclick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[2836]?></td>
	<td class="tabpoolItemSpace">&nbsp;</td>
	</tr></table>
	</td></tr>
	
	<tr><td valign="top" class="tabpoolfringe">
	<table ID="tab1"><tr><td valign="top">
	<table>
	<?php
	
	#echo "<table style=\"background-color:".$farbschema["WEB11"].";width:420px;\"><tr><td valign=\"top\">";
	
	# fieldname
	#echo "<tr><td>".$lang[922]."<hr></td><td><b>".$result_fieldtype[$table_gtab[$bzm]]["field"][1]."</b><hr></td></tr>";
	if($isview){$readlonly = "readonly disabled";}else{$readlonly = "";}
	echo "<tr><td>".$lang[922]."</td><td><input type=\"text\" STYLE=\"width:100%\" value=\"".$result_fieldtype[$table_gtab[$bzm]]["field"][1]."\" $readlonly onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','fieldname')\"></td></tr>";

	
	# spelling
	echo "<tr><td>".$lang[924]."</td><td><input type=\"text\" STYLE=\"width:100%\" value=\"".$lang[$result_fieldtype[$table_gtab[$bzm]]["spelling"][1]]."\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','spelling')\"></td></tr>";
	
	# title
	echo "<tr><td>".$lang[923]."</td><td><input type=\"text\" STYLE=\"width:100%\" value=\"".$lang[$result_fieldtype[$table_gtab[$bzm]]["beschreibung_feld"][1]]."\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','desc')\"></td></tr>";
	
	# fieldtype
	echo "<tr><td valign=\"top\">".$lang[925]."</td><td>";
	if($result_fieldtype[$table_gtab[$bzm]]["scale"][1]){
		$fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][1].",".$result_fieldtype[$table_gtab[$bzm]]["scale"][1];
	}else{
		$fsize = $result_fieldtype[$table_gtab[$bzm]]["precision"][1];
	}
	if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]){
		echo $result_type["beschreibung"][$result_type["arg_result_datatype"][$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]]]."&nbsp;-&nbsp;";
	}
	echo $result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1];
	if($result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]){echo "($lang[2086])";}

	echo "<br><i style=\"color:#AAAAAA\">".$result_fieldtype[$table_gtab[$bzm]]["format_typ"][1]."</i>";
	echo "</td></tr>";
	
	
	if($result_fieldtype[$table_gtab[$bzm]]["type_name"][1]){
		echo "<tr><td>DB-".$lang[925]."</td><td><i>(".$result_fieldtype[$table_gtab[$bzm]]["type_name"][1]." ".$fsize.")</i></td></tr>";
	}
	
	
	# fieldsize
	if($result_type["hassize"][$result_fieldtype[$table_gtab[$bzm]]["datatype_id"][1]]){
	echo "<tr><td valign=\"top\">".$lang[210]."</td><td><input type=\"text\" style=\"width:100%\" value=\"".$result_fieldtype[$table_gtab[$bzm]]["field_size"][1]."\" OnChange=\"convert_field('".$result_fieldtype[$table_gtab[$bzm]]["datatype_id"][1]."','".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$result_fieldtype[$table_gtab[$bzm]]["field"][1]."',this.value);\">
	<br><i style=\"color:#AAAAAA\">".$lang[2655]."</i>
	</td></tr>";
	}
	
	# default
	if(!$isview){
	if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 4 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 44 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]){
		echo "<tr><td valign=\"top\">".$lang[928]."</td><td>";
		echo "<input type=\"text\" STYLE=\"width:100%\" value=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["domain_default"][1],ENT_QUOTES,$umgvar["charset"])."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','def')\">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2588]."</i>";
		echo "</td></tr>";
	}elseif($result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1]){
		echo "<tr><td>".$lang[928]."</td><td>";
		echo $result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1];
		echo "</td></tr>";
	}}
	

	####### Sonderfelder #######

	/* --- BOOLEAN Format --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 10){
		echo "<tr><td valign=\"top\">".$lang[2998]."</td><td>";
		echo "<SELECT STYLE=\"width:100%;\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','boolformat')\">";
		$SELECTED[$result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1]] = 'selected';
		echo "<OPTION VALUE=\" \" $SELECTED[0]>Checkbox";
		echo "<OPTION VALUE=\"2\" $SELECTED[2]>Select";
		echo "<OPTION VALUE=\"1\" $SELECTED[1]>Radio";
		echo "<OPTION VALUE=\"3\" $SELECTED[3]>Radio reversed";
		echo "</SELECT>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2999]."</i>";
		echo "</td></tr>";
    }
	
	/* --- NFORMAT --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 44){
		echo "<tr><td valign=\"top\">".$lang[1880]."</td><td>";
		echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:100%;\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]["format"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','nformat')\">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2596]." </i>";
		echo "</td></tr>";
		echo "<tr><td valign=\"top\">".$lang[2587]."</td><td>";
		echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:100%;\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]["potency"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','potency')\">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2597]."</i>";
		echo "</td></tr>";
	}
	
	/* --- Timestamp Format --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 2 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 24){
		echo "<tr><td valign=\"top\">".$lang[2576]."</td><td>";
		echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:50%;\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]["format"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','nformat')\">";
		if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 11){
			${'seldt'.$result_fieldtype[$table_gtab[$bzm]]["datetime"][1]} = 'selected';
			echo "
			<br><select style=\"width:100%\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','datetime')\">
			<option value=\"1\" $seldt1>".$lang[197]."</option>
			<option value=\"4\" $seldt4>".$lang[1723]."</option>
			<option value=\"0\" $seldt0>".$lang[2702]."</option></select>";
			#<option value=\"2\" $sel2>Datum gesprochen kurz</option>
			#<option value=\"3\" $sel3>Datum gesprochen lang</option>
		}

		${'seldp'.$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]} = 'selected';
		echo "
			<br><select style=\"width:100%\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','datepicker')\">
			<option value=\"1\" $seldp1>UI datepicker</option>
			<option value=\"2\" $seldp2 >browwser datepicker</option></select>";

		echo "<br><i style=\"color:#AAAAAA\">".$lang[2602]."</i>";
		echo "</td></tr>";
		/* --- Time Format --------------------------------------- */
	}elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 7){
		echo "<tr><td valign=\"top\">".$lang[2600]."</td><td>";
		echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:100%;\" VALUE=\"".$result_fieldtype[$table_gtab[$bzm]]["format"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','nformat')\">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2602]."</i>";
		echo "</td></tr>";
	}
	
	/* --- relations / multiselect --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32){
		$SELECTED = array();
		$SELECTED[$result_fieldtype[$table_gtab[$bzm]]["listing_viewmode"][1]] = 'selected';
		echo "
		<tr><td><hr></td><td><hr></td></tr>
		<tr><td valign=\"top\">".$lang[2812]."</td><td><select style=\"width:100%\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','relviewmode')\">
		<option value=\"1\" $SELECTED[1]>".$lang[2814]."</option>
		<option value=\"2\" $SELECTED[2]>".$lang[2815]."</option>";
		if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 25) {
            echo "<option value=\"3\" $SELECTED[3]>" . $lang[2709] . "</option>
		    <option value=\"4\" $SELECTED[4]>" . $lang[2913] . "</option>";
        }
		echo "</select>
		<br><i style=\"color:#AAAAAA\">".$lang[2848]."</i>
		</td></tr>";
        // --- relations
		if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11) {
            $SELECTED = array();
            $SELECTED[$result_fieldtype[$table_gtab[$bzm]]["triggercount"][1]] = 'selected';
            echo "<tr><td valign=\"top\">" . $lang[2991] . "</td><td><select style=\"width:100%\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','triggercount')\"><option value=\"1\" $SELECTED[1]>" . $lang[2994] . "</option><option value=\"2\" $SELECTED[2]>" . $lang[2993] . "</option></select>
		<br><i style=\"color:#AAAAAA\">" . $lang[2992] . "</i>
		</td></tr>";
        }
		
		echo "<tr><td valign=\"top\">".$lang[2813]."</td><td><input type=\"text\" style=\"width:100%;\" value=\"".$result_fieldtype[$table_gtab[$bzm]]["listing_cut"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','relviewcut')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2849]."</i>
		</td></tr>
		
		<tr><td><hr></td><td><hr></td></tr>
		";
	}
	
	# convert
	if(!$isview){
	if(($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11) AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]){
		# multiselect convert
		if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32){
			$result_type_allow_convert = array(18,31,32);
		}elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 24) {
			$result_type_allow_convert = array(27);
	    }elseif($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 27) {
	       	$result_type_allow_convert = array(24);
	    }else{
			$result_type_allow_convert = array(16,17,33,19,21,1,2,3,4,5,6,7,8,9,10,29,28,12,14,31,18,30,32,39,42,44,45,50);
		}
		echo "<tr><td valign=\"top\">".$lang[930]."</td><td>";
		#echo "<SELECT STYLE=\"width:100%\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','convert_value')\"><OPTION>";
		echo "<SELECT STYLE=\"width:100%\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][1]."');\"><OPTION>";
		foreach($result_type["id"] as $type_key => $type_value){
			if(in_array($result_type["data_type"][$type_key],$result_type_allow_convert)){
				echo "<OPTION VALUE=\"".$type_key."\">".$result_type["beschreibung"][$type_key];
			}
		}
		echo "</SELECT>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2589]."</i>";
		echo "</td></tr>";
		echo "<tr><td><hr></td><td><hr></td></tr>";
	}elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 101){
		echo "<tr><td valign=\"top\">".$lang[930]."</td><td>";
		echo "<SELECT STYLE=\"width:100%\" OnChange=\"convert_field(this[this.selectedIndex].value,'".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$result_fieldtype[$table_gtab[$bzm]]['field'][1]."');\"><OPTION>";
		$result_type_allow_convert = array(101,102);
		foreach($result_type["id"] as $type_key => $type_value){
			if(in_array($result_type["data_type"][$type_key],$result_type_allow_convert)){
				echo "<OPTION VALUE=\"".$type_key."\">".$result_type["beschreibung"][$type_key];
			}
		}
		echo "</SELECT>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2589]."</i>";
		echo "</td></tr>";
		echo "<tr><td><hr></td><td><hr></td></tr>";
	}
	
	
	}
	
	# extension
	if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $ext_fk AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[1986]."</td><td>";
		echo "<SELECT STYLE=\"width:100%\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','extend_value')\"><OPTION VALUE=\" \">";
		foreach ($ext_fk as $key => $value){
			echo "<OPTION VALUE=\"$value\" ";
			if($result_fieldtype[$table_gtab[$bzm]]["ext_type"][1] == $value){echo "SELECTED";}
			echo ">".$value."\n";
		}
		echo "</SELECT>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2590]."</i>";
		echo "</td></tr>";
	}
	
	/* --- Währung --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 30){
		echo "<tr><td valign=\"top\">".$lang[1883]."</td><td>";
		echo "<SELECT STYLE=\"width:100%;\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','ncurrency')\"><OPTION VALUE=\" \">";
		asort($lmcurrency["currency"]);
		foreach($lmcurrency["currency"] as $ckey => $cval){
			if($lmcurrency["code"][$ckey] == $result_fieldtype[$table_gtab[$bzm]]["currency"][1]){$sel = "SELECTED";}
			#elseif($lmcurrency['code'][$ckey] == "EUR" AND !$result_fieldtype[$table_gtab[$bzm]]['currency'][1]){$sel = "SELECTED";}
			else{$sel = "";}
			echo "<OPTION VALUE=\"".$lmcurrency['code'][$ckey]."\" $sel>".$lmcurrency["currency"][$ckey];
		}
		echo "</SELECT>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2599]."</i>";
		echo "</td></tr>";
	}
	
	# Agregatfunktion
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5){
		echo "<tr><td valign=\"top\">".$lang[2731]."</td><td valign=\"top\">";
		#echo "<SELECT MULTIBLE STYLE=\"width:100%;height:\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','agregat_value')\"><OPTION VALUE=\" \">";
		$agregat_fk = array(1=>'AVG',2=>'COUNT',3=>'MAX',4=>'MIN',5=>'SUM');
		foreach ($agregat_fk as $key => $value){
			if(in_array($key,$result_fieldtype[$table_gtab[$bzm]]["aggregate"][1])){$CHECKED = "checked";}else{$CHECKED = "";}
			echo "<div style=\"float:left;width:36px;text-align:center\">$value<br><input type=\"checkbox\" onchange=\"document.form2.val.value=this.checked+'_$key';ajaxEditField('$fieldid','aggregate')\" style=\"vertical-align:text-top;margin:0;margin-bottom:3px\" $CHECKED>&nbsp;&nbsp;</div>";
		}
		#echo "</SELECT>";
		echo "<br style=\"clear:both\"><i style=\"color:#AAAAAA\">".$lang[2732]."</i>";
		echo "</td></tr>";
	}
	

	
	
	echo "<tr><td><hr></td><td><hr></td></tr>";
	
	# --- View-Rule ------
	echo "<tr><td valign=\"top\">".$lang[2505]."</td><td>";
	echo "<TEXTAREA onfocus=\"this.style.height='60px';\" onblur=\"this.style.height='18px';\" STYLE=\"width:100%;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','view_rule')\">".$result_fieldtype[$table_gtab[$bzm]]["view_rule"][1]."</TEXTAREA>";
	echo "<br><i style=\"color:#AAAAAA\">".$lang[2591]."</i>";
	echo "</td></tr>";

	# --- Edit-Rule ------
	if(!$isview){
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1] != 47 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 47){
		echo "<tr><td valign=\"top\">".$lang[2570]."</td><td>";
		echo "<TEXTAREA onfocus=\"this.style.height='60px';\" onblur=\"this.style.height='18px';\" STYLE=\"width:100%;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','edit_rule')\">".$result_fieldtype[$table_gtab[$bzm]]["edit_rule"][1]."</TEXTAREA>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2592]."</i>";
		echo "</td></tr>";
	}}
	
	# --- Sparten-Event ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 100){
		echo "<tr><td valign=\"top\">Click-Event</td><td>";
		echo "<TEXTAREA onfocus=\"this.style.height='60px';\" onblur=\"this.style.height='18px';\" STYLE=\"width:100%;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','options')\">".$result_fieldtype[$table_gtab[$bzm]]["options"][1]."</TEXTAREA>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2712]."</i>";
		echo "</td></tr>";
	}

	echo "</table>";
	echo "</td><td valign=\"top\">";
	echo "&nbsp;";
	echo "</td><td valign=\"top\">";
	echo "<table style=\"width:250px\">";
	


	# --- Bezeichner ------
	if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][$bzm1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[2235]."</td><td align=\"left\">";
		if($result_fieldtype[$table_gtab[$bzm]]["mainfield"][1] == 1){$mainfieldvalue = "CHECKED";} else{$mainfieldvalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','mainfield')\"".$mainfieldvalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2837]."</i>";
		echo "</td></tr>";
	}

	# --- Index ------
	if(!$isview){
	if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 39 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[1720]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["indexed"][1] == 1){$indexvalue = "CHECKED";} else{$indexvalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','fieldindex')\"".$indexvalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2838]."</i>";
		echo "</td></tr>";
	}
	
	# --- unique ------
	if(!$result_fieldtype[$table_gtab[$bzm]]["domain_admin_default"][1] AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[1]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 12 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 18 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 3 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19
    AND !($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 25 AND $result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2)
    ){
		echo "<tr><td valign=\"top\">".$lang[927]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["unique"][1] == 1){$unique = "CHECKED";} else{$unique = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','uniquefield')\"".$unique.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2839]."</i>";
		echo "</td></tr>";
	}
	
	
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
	}
	
	# --- select ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6){
		echo "<tr><td valign=\"top\">".$lang[932]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["artleiste"][1] == 1){$artleistevalue = "CHECKED";} else{$artleistevalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','artleiste')\"".$artleistevalue.">";
		if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11){
			echo "<br><i style=\"color:#AAAAAA\">".$lang[2846]."</i>";
		}else{
			echo "<br><i style=\"color:#AAAAAA\">".$lang[2841]."</i>";
		}
		echo "</td></tr>";
	}
	
	# --- ajax search ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11  OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 12 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[2639]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["dynsearch"][1] == 1){$dynsearch = "CHECKED";} else{$dynsearch = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','dynsearch')\"".$dynsearch.">";
		if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4){
			echo "<br><i style=\"color:#AAAAAA\">".$lang[2845]."</i>";
		}elseif($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11){
			echo "<br><i style=\"color:#AAAAAA\">".$lang[2847]."</i>";
		}else{
			echo "<br><i style=\"color:#AAAAAA\">".$lang[2842]."</i>";
		}
		echo "</td></tr>";
	}
	
	# --- ajax save ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] <= 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 14 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 15 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 9 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 8 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 19 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[2640]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["ajaxsave"][1] == 1){$ajaxsave = "CHECKED";} else{$ajaxsave = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','ajaxsave')\"".$ajaxsave.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2840]."</i>";
		echo "</td></tr>";
	}
	}

    # --- quick search ------
    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6){
        echo "<tr><td valign=\"top\">".$lang[2507]."</td><td>";
        if($result_fieldtype[$table_gtab[$bzm]]["quicksearch"][1] == 1){$quicksearchvalue = "CHECKED";} else{$quicksearchvalue = "";}
        echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','quicksearch')\"".$quicksearchvalue.">";
        echo "<br><i style=\"color:#AAAAAA\">".$lang[2842]."</i>";
        echo "</td></tr>";
    }

    # --- full table search ------
    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 10 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 33 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 6 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 20){
        echo "<tr><td valign=\"top\">".$lang[2922]."</td><td>";
        if($result_fieldtype[$table_gtab[$bzm]]["fullsearch"][1] == 1){$fullsearchvalue = "CHECKED";} else{$fullsearchvalue = "";}
        echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','fullsearch')\"".$fullsearchvalue.">";
        echo "<br><i style=\"color:#AAAAAA\">".$lang[2923]."</i>";
        echo "</td></tr>";
    }
	
	# --- Upload - show preview ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 6){
		echo "<tr><td valign=\"top\">".$lang[1739]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["quicksearch"][1] == 1){$quicksearchvalue = "CHECKED";} else{$quicksearchvalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','quicksearch')\"".$quicksearchvalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[997]."</i>";
		echo "</td></tr>";
	}
	
	# --- Gruppierbar ------
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 11 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 3 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 31 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 32 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 18  AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 13 AND $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 16){
		echo "<tr><td valign=\"top\">".$lang[1459]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["groupable"][1] == 1){$groupablevalue = "CHECKED";} else{$groupablevalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','groupable')\"".$groupablevalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2843]."</i>";
		echo "</td></tr>";
	}
	
	# --- coll_replace ------
	if(!$isview){
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] != 100 AND $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] != 22 AND !$result_fieldtype[$table_gtab[$bzm]]["argument"][1] AND ($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 2 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 10 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 21 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 18)){
		echo "<tr><td valign=\"top\">".$lang[2672]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["collreplace"][1] == 1){$collreplacevalue = "CHECKED";} else{$collreplacevalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','collreplace')\"".$collreplacevalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2844]."</i>";
		echo "</td></tr>";
	}}


	# --- long list edit ------
	if(!$isview){
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3){
		echo "<tr><td valign=\"top\">".$lang[1879]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["argument_edit"][1] == 1){$argument_edit = "CHECKED";}else{$argument_edit = " ";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value='$argument_edit';ajaxEditField('$fieldid','argument_edit')\" $argument_edit>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[3050]."</i>";
		echo "</td></tr>";
	}}
	
	
	# --- multi language ------
    if(!$isview){
	if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4){
		echo "<tr><td valign=\"top\">".$lang[2895]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["multilang"][1] == 1){$collreplacevalue = "CHECKED";} else{$collreplacevalue = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','multilang')\"".$collreplacevalue.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2896]."</i>";
		echo "</td></tr>";
	}}

	# --- searchversion ------
    if(!$isview){
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] < 100 AND ($tab_versioning[$bzm] OR $table_validity[$bzm]) ){
		echo "<tr><td valign=\"top\">".$lang[3045]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["searchversion"][1] == 1){$checked = "CHECKED";} else{$checked = "";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','searchversion')\"".$checked.">";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[3046]."</i>";
		echo "</td></tr>";
	}}
	
	# relation/grouping popup default
    if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11 OR $result_fieldtype[$table_gtab[$bzm]]["groupable"][1] == 1){
        echo "<tr><td valign=\"top\">".$lang[2918]."</td><td>";
        if($result_fieldtype[$table_gtab[$bzm]]["popupdefault"]){$checked = "CHECKED";} else{$checked = "";}
        echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','popupdefault')\" ".$checked.">";
        echo "<br><i style=\"color:#AAAAAA\">".$lang[2919]."</i>";
        echo "</td></tr>";
    }
    
	/* --- Argument --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		echo "<tr><td align=\"left\" colspan=\"2\"><b>".$result_type["beschreibung"][$result_type["arg_result_datatype"][$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]]]."</td><td>";
		echo "<tr><td>".$lang[2593]."</td><td><i border=\"0\" onclick=\"newwin3('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$KEYID_gtab[$bzm]."','$bzm','".$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]."');\" class=\"lmb-icon lmb-pencil\" style=\"cursor:pointer\"></i>";
		
		echo "</td></tr>";
		if($result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1] == 15){
			echo "<tr><td valign=\"top\">".$lang[1879]."</td><td>";
			if($result_fieldtype[$table_gtab[$bzm]]["argument_edit"][1] == 1){$argument_edit = "CHECKED";}else{$argument_edit = " ";}
			echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value='$argument_edit';ajaxEditField('$fieldid','argument_edit')\" $argument_edit>";
		}
		echo "</td></tr>";
	}
	
	/* --- Attribut --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 19){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		echo "<tr><td align=\"left\" colspan=\"2\"><b>".$result_type["beschreibung"][$result_type["arg_result_datatype"][$result_fieldtype[$table_gtab[$bzm]]["argument_typ"][1]]]."</td><td>";
		echo "<tr><td>".$lang[2594]."</td><td>";
		$pool = 0;
		if($pool = $result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]){
			$sqlquery = "SELECT NAME FROM LMB_ATTRIBUTE_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1];
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			echo "<A HREF=\"JAVASCRIPT: newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','$bzm','$pool','LMB_ATTRIBUTE');\">".htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"])."</A>";
			
		}
		echo "&nbsp;&nbsp;<A HREF=\"JAVASCRIPT: newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','$bzm','$pool','LMB_ATTRIBUTE');\"><i border=\"0\" class=\"lmb-icon lmb-pencil\"></i>";
		
		echo "</td></tr>";
	}
	
	/* --- Selectauswahl --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 4){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		echo "<tr><td align=\"left\" colspan=\"2\"><b>".$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]."</td><td>";
		echo "<tr><td>".$lang[2594]."</td><td>";
		$pool = 0;
		if($pool = $result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]){
			$sqlquery = "SELECT NAME FROM LMB_SELECT_P WHERE ID = ".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1];
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
			echo "<A HREF=\"JAVASCRIPT: newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','$bzm','$pool','LMB_SELECT');\">".htmlentities(lmbdb_result($rs, "NAME"),ENT_QUOTES,$umgvar["charset"])."<a>";
		}
		echo "&nbsp;&nbsp;<i border=\"0\" class=\"lmb-icon lmb-pencil\" onclick=\"newwin('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','$bzm','$pool','LMB_SELECT');\" style=\"cursor:pointer;\"></i>";
		echo "</td></tr>";
		
		if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32){
			echo "<tr><td>".$lang[2595]."</td><td align=\"center\">";
			echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:100%;\" VALUE=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["select_cut"][1],ENT_QUOTES,$umgvar["charset"])."\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','select_cut')\">";
			echo "</td></tr>";
		}

		echo "<tr><td valign=\"top\">".$lang[2795]."</td><td>";
		echo "<TEXTAREA onfocus=\"this.style.height='60px';\" onblur=\"this.style.height='18px';\" STYLE=\"width:100%;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','options')\">".$result_fieldtype[$table_gtab[$bzm]]["options"][1]."</TEXTAREA>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2857]."</i>";
		
		if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 18 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 31 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 32){
        	# rebuild temp
        	echo "<tr><td valign=\"top\">".$lang[2761]."</td><td><i style=\"cursor:pointer\" class=\"lmb-icon lmb-refresh\" onclick=\"document.form2.val.value=1;ajaxEditField('$fieldid','tablesync')\"></i>";
		    if($tablesync){echo " <i class=\"lmb-icon lmb-aktiv\"></i>";}
        	echo "<br><i style=\"color:#AAAAAA\">".$lang[2030]."</i>
        	</td></tr>";
		}

		echo "</td></tr>";
	}
	
	/* --- Long --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 39){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["memoindex"][1] == 1){$memoindexvalue = "CHECKED";} else{$memoindexvalue = "";}
		if($result_fieldtype[$table_gtab[$bzm]]["wysiwyg"][1] == 1){$wysiwygvalue = "CHECKED";} else{$wysiwygvalue = "";}
		echo "<tr><td>".$lang[1581]."</td><td align=\"center\">";
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','memoindex')\" ".$memoindexvalue.">";
		echo "</td></tr>";
		echo "<tr><td>".$lang[1885]."</td><td align=\"center\">";
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','wysiwyg')\" ".$wysiwygvalue.">";
		echo "</td></tr>";
		echo "<tr><td valign=\"top\">".$lang[2795]."</td><td align=\"center\">";
		echo "<TEXTAREA onfocus=\"this.style.height='60px';\" onblur=\"this.style.height='18px';\" STYLE=\"width:100%;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','options')\">".$result_fieldtype[$table_gtab[$bzm]]["options"][1]."</TEXTAREA>";
		echo "</td></tr>";
	}
	
	/* --- Long / Textblock --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 39 OR $result_fieldtype[$table_gtab[$bzm]]["datatype"][1] == 10){
		echo "<tr><td>".$lang[2817]."</td><td><input type=\"text\" style=\"width:100%;\" value=\"".$result_fieldtype[$table_gtab[$bzm]]["select_pool"][1]."\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','textblocksize')\"></td></tr>";
	}
	
	/* --- Grouping --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 101){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		echo "<tr><td align=\"left\" colspan=\"2\"><b>".$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]."</td><td>";
		echo "<tr><td><A HREF=\"JAVASCRIPT: newwin7('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$bzm."');\">".$lang[2593]."</A></td><td align=\"center\">";
		if($result_fieldtype[$table_gtab[$bzm]]["genlink"][1]){echo "Link";}
		echo "</td></tr>";
	}
	
	/* --- User / Group List --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 16){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
		
		echo "<tr><td>".$lang[2595]."</td><td>";
		echo "<INPUT TYPE=\"TEXT\" STYLE=\"width:50px;\" VALUE=\"".htmlentities($result_fieldtype[$table_gtab[$bzm]]["select_cut"][1],ENT_QUOTES,$umgvar["charset"])."\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','select_cut')\">";
		echo "</td></tr>";
	}
	
	/* --- relations --------------------------------------- */
    
	/* --- relations --------------------------------------- */
	if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 11){
		echo "<tr><td colspan=\"2\"><hr></td><td>";
        echo "<tr><td align=\"left\" colspan=\"2\"><b>".$result_fieldtype[$table_gtab[$bzm]]["beschreibung_typ"][1]."</td><td>";

        # target of relation
        $verknTabid = $result_fieldtype[$table_gtab[$bzm]]["verkntabid"][1];
        $verknFieldid = $result_fieldtype[$table_gtab[$bzm]]["verknfieldid"][1];
        $tdStyle = '';
        if (!$verknTabid or !$verknFieldid) {
            $tdStyle = 'style="color: red"';
        }
        echo "<tr><td>".$lang[1460]."</td><td>";
		if($verknTabid){
			$sqlquery = "SELECT BESCHREIBUNG FROM LMB_CONF_TABLES WHERE TAB_ID = ".$verknTabid;
			$rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
            echo "<a onclick=\"document.location.href='main_admin.php?&action=setup_gtab_ftype&tab_group=$tab_group&atid=$verknTabid'\">".$lang[lmbdb_result($rs, "BESCHREIBUNG")]."</a> | ";

            if($verknFieldid){
                $sqlquery = "SELECT SPELLING FROM LMB_CONF_FIELDS WHERE TAB_ID = ".$verknTabid." AND FIELD_ID = ".$verknFieldid;
                $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                echo $lang[lmbdb_result($rs, "SPELLING")];
            } else {
                echo '?';
            }
		} else {
		    echo '?';
        }

		if($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2){echo "&nbsp;(<span style=\"color:green;\">recursive</span>)";}
		echo "</td></tr>";
        
		# relation table
		echo "<tr><td>".$lang[2604]."</td><td>";
		echo $result_fieldtype[$table_gtab[$bzm]]["verkntab"][1]."&nbsp;";
		if($result_fieldtype[$table_gtab[$bzm]]["verkntabletype"][1] == 2){echo "(<span style=\"color:green;\">view</span>)";}
		echo "</td></tr>";
        
        # configure
		echo "<tr><td valign=\"top\">".$lang[2593]."</td><td $tdStyle><i style=\"cursor:pointer\" class=\"lmb-icon-cus lmb-rel-edit\" title=\"$lang[1301]\" OnClick=\"newwin5('".$result_fieldtype[$table_gtab[$bzm]]["field_id"][1]."','".$KEYID_gtab[$bzm]."','".$result_fieldtype[$table_gtab[$bzm]]["verkntabid"][1]."')\"></i></td>";

		# rebuild temp
        echo "<tr><td valign=\"top\">".$lang[2761]."</td><td><i style=\"cursor:pointer\" class=\"lmb-icon lmb-refresh\" onclick=\"document.form2.val.value=1;ajaxEditField('$fieldid','tablesync')\"></i>";
		if($tablesync){echo " <i class=\"lmb-icon lmb-aktiv\"></i>";}
        echo "<br><i style=\"color:#AAAAAA\">".$lang[2030]."</i>
        </td></tr>";

		echo "</td></tr>";
	}
	
	# Verärbung
	if($result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]){
		
		echo "<tr><td colspan=\"2\" align=\"center\"><hr></td>";
		echo "<tr><td colspan=\"2\" align=\"center\">".$lang[2611]."</td></tr>";
		echo "<tr><td>".$lang[164]."</td><td>".$gtab["desc"][$result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]]."</td></tr>";
		echo "<tr><td>".$lang[168]."</td><td>".$gfield[$result_fieldtype[$table_gtab[$bzm]]["inherit_tab"][1]]["spelling"][$result_fieldtype[$table_gtab[$bzm]]["inherit_field"][1]]."</td></tr>";
		echo "<tr><td>".$lang[561]."</td><td align=\"left\">";
		echo "<SELECT style=\"width:60px;\" onchange=\"document.form2.val.value=this.value;ajaxEditField('$fieldid','inherit_group')\"><option value='null'></option>";
		for($i=1; $i<=10; $i++){
			if($result_fieldtype[$table_gtab[$bzm]]["inherit_group"][1] == $i){$selected = "selected";}else{$selected = "";}
			echo "<option value=\"$i\" $selected>$i</option>";
		}
		echo "</td></tr>";
		
		if($result_fieldtype[$table_gtab[$bzm]]["inherit_search"][1] == 1){
			echo "<tr><td valign=\"top\">".$lang[2608]."</td><td>";
			echo "<TEXTAREA onfocus=\"this.style.height='80px';this.style.width='250px';\" onblur=\"this.style.height='18px';this.style.width='120px';\" STYLE=\"width:120px;height:18px;overflow:visible\" onchange=\"document.form2.val.value=this.value+' ';ajaxEditField('$fieldid','inherit_filter')\">".$result_fieldtype[$table_gtab[$bzm]]["inherit_filter"][1]."</TEXTAREA>";
			echo "</td></tr>";
		}

		if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 3){
			echo "<tr><td>".$lang[2610]."</td><td>";
			if($result_fieldtype[$table_gtab[$bzm]]["inherit_eval"][1] == 1){$checked = "CHECKED";} else{$checked = "";}
			echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','inherit_eval')\" ".$checked.">";
			echo "</td></tr>";
		}
		
		if($result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 1 OR $result_fieldtype[$table_gtab[$bzm]]["fieldtype"][1] == 5){
			echo "<tr><td>".$lang[2609]."</td><td>";
			if($result_fieldtype[$table_gtab[$bzm]]["inherit_search"][1] == 1){$checked = "CHECKED";} else{$checked = "";}
			echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value=this.checked;ajaxEditField('$fieldid','inherit_search')\" ".$checked.">";
			echo "</td></tr>";
		}
		
		echo "<tr><td valign=\"top\">".$lang[1879]."</td><td>";
		if($result_fieldtype[$table_gtab[$bzm]]["argument_edit"][1] == 1){$argument_edit = "CHECKED";}else{$argument_edit = " ";}
		echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"document.form2.val.value='$argument_edit';ajaxEditField('$fieldid','argument_edit')\" $argument_edit>";

	}
	
    if($depviews = lmb_checkViewDependency($gtab["table"][$gtabid],$result_fieldtype[$table_gtab[$bzm]]["field"][1])){
       echo "<tr><td colspan=\"2\" align=\"center\"><hr></td></tr>";
      
       echo "<tr><td valign=\"top\">".$lang[2912]."</td><td>";
       echo "<INPUT TYPE=\"CHECKBOX\" onclick=\"if(this.checked){document.form1.solve_dependency.value=1;document.form2.solve_dependency.value=1;}else{document.form1.solve_dependency.value='';document.form2.solve_dependency.value='';};\"><br>";
	   echo "<i style=\"color:#AAAAAA\">".$lang[2911]."</i><br><hr>";
       echo '- '.implode('<br>- ',$depviews);
	   echo "</td></tr>";
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	?>
	</table>
	</td></tr></table>
	
	<table ID="tab2" border="0" style="width:420px;display:none;padding:5px;">
	<?php
	if($rs = dbf_5(array($DBA["DBSCHEMA"],$table_gtab[$bzm],dbf_4($result_fieldtype[$table_gtab[$bzm]]["field"][1]),1))){
	while (lmbdb_fetch_row($rs))
	{
		$jml = lmbdb_num_fields($rs);
		for($i=1;$i<=$jml;$i++)
		{
			echo "<tr><td style=\"border-bottom:1px solid #CCCCCC\">".lmbdb_field_name($rs,$i) . "</td><td style=\"border-bottom:1px solid #CCCCCC\">" . lmbdb_result($rs,$i)."</td></tr>";
		}
	}
	}
	
	?>
	</table>
	
	</td></tr></table>