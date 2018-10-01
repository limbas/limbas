<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
 */


$tbzm = $result_gtab[$tabgroup]["argresult"][$tabid];
if($gtab["typ"][$tabid] == 5){$isview = 1;}
$col = dbf_5(array($DBA["DBSCHEMA"],$result_gtab[$tabgroup]["tabelle"][$tbzm]));


echo "<form action=\"main_dyns_admin.php\" method=\"post\" name=\"form3\" id=\"form3\">";
echo "<input type=\"hidden\" name=\"val\">";
echo "<input type=\"hidden\" name=\"tabgroup\" value=\"$tabgroup\">";


	
?>
	<table cellspacing="0" cellpadding="0" style="width:450px;">
	
	<tr><td>
	<table cellspacing="0" cellpadding="0" width="100%"><tr class="tabpoolItemTR">
	<td nowrap id="menu1" onclick="LIM_activate(this,'1')" class="tabpoolItemActive"><?=$lang[2795]?></td>
	<td nowrap id="menu2" onclick="LIM_activate(this,'2')" class="tabpoolItemInactive"><?=$lang[2836]?></td>
	<td class="tabpoolItemSpace">&nbsp;</td>
	</tr></table>
	</td></tr>
	
	<tr><td valign="top" class="tabpoolfringe">
	<div id="tab1" style="padding:5px;">

	<table><tr><td valign="top">
	
	<?php
	
	echo "<table>";
	
	
	# tablename
	echo "<tr><td valign=\"top\">".$lang[951]."</td><td><input type=\"text\" STYLE=\"width:100%\" value=\"".$result_gtab[$tabgroup]["tabelle"][$tbzm]."\" $readlonly onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','tabname')\">
	<br><i style=\"color:#AAAAAA\">".$lang[2833]."</i>
	</td></tr>";
	# spelling
	echo "<tr><td valign=\"top\">".$lang[924]."</td><td><input type=\"text\" STYLE=\"width:100%\" value=\"".$result_gtab[$tabgroup]["beschreibung"][$tbzm]."\" $readlonly onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','desc')\">
	<br><i style=\"color:#AAAAAA\">".$lang[2834]."</i>
	</td></tr>";
	# tablegroup
	echo "<tr><td valign=\"top\">".$lang[900]."</td><td><SELECT STYLE=\"width:120px;\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','setmaingroup')\"><OPTION VALUE=\"0\"></OPTION>";
	foreach($tabgroup_["id"] as $bzm1 => $value1){
		if($value1 == $result_gtab[$tabgroup]["maingroup"][$tbzm]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
		echo "<OPTION VALUE=\"$value1\" $SELECTED>".$tabgroup_["name"][$bzm1]."</OPTION>";
	}
	echo "</SELECT></td></tr>";
	# typ
	echo "<tr><td valign=\"top\">".$lang[925]."</td><td>";
	if($result_gtab[$tabgroup]["typ"][$tbzm] == 1){echo $lang[164]."&nbsp;";}
	elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 2){echo $lang[1929]."&nbsp;";}
	elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 6){echo $lang[767]."&nbsp;";}
	elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 5){
		if($result_gtab[$tabgroup]["viewtype"][$tbzm] == 1){
			echo $lang[2656]."&nbsp;";
		}elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 2){
			echo $lang[2657]."&nbsp;";
		}elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 3){
			echo $lang[2658]."&nbsp;";
		}elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 4){
			echo $lang[2659]."&nbsp;";
		}else{
			echo $lang[2656]."&nbsp;";
		}
	}
	# numfields	
	echo "<tr><td>".$lang[953]."</td><td>".$result_gtab[$tabgroup]["num_gtab"][$tbzm]."</td></tr>";
	
	echo "<tr><td><hr></td><td><hr></td></tr>";

	# versioning
	if(!$isview){
	echo "<tr><td valign=\"top\">".$lang[2132]."</td><td>";
	$selected_0 = null;$selected_1 = null;$selected_2 = null;
	if(!$result_gtab[$tabgroup]["versioning"][$tbzm]){$selected_0 = "selected";}
	if($result_gtab[$tabgroup]["versioning"][$tbzm] == 1){$selected_1 = "selected";$det = $lang[2142];}
	if($result_gtab[$tabgroup]["versioning"][$tbzm] == 2){$selected_2 = "selected";$det = $lang[2142];}
	if($result_gtab[$tabgroup]["id"][$tbzm] == $result_gtab[$tabgroup]["verknid"][$tbzm]){
		echo "<select  onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','versioning')\">
        <option value=\"-1\" $selected_0>
        <option value=\"1\" $selected_1>".$lang[2142]."
        <option value=\"2\" $selected_2>".$lang[2143]."
        </select>";
	}else{
		echo $det;
	}
	echo "<br><i style=\"color:#AAAAAA\">".$lang[2822]."</i>";
	echo "</td></tr>";
	}

	# numrowcalc
	if($result_gtab[$tabgroup]["num_gtab"][$tbzm] > 0){
		echo "<tr><td valign=\"top\">".$lang[2688]."</td><td>";
		$selected_0 = null;$selected_1 = null;$selected_2 = null;
		if(!$result_gtab[$tabgroup]["numrowcalc"][$tbzm]){$selected_0 = "selected";}
		elseif($result_gtab[$tabgroup]["numrowcalc"][$tbzm] == 1){$selected_1 = "selected";}
		elseif($result_gtab[$tabgroup]["numrowcalc"][$tbzm] == 2){$selected_2 = "selected";}
		echo "<select  onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','numrowcalc')\">
        <option value=\"-1\" $selected_0>".$lang[2685]."
        <option value=\"1\" $selected_1>".$lang[2686]."
        <option value=\"2\" $selected_2>".$lang[2687]."
        </select>";
		echo "<br><i style=\"color:#AAAAAA\">".$lang[2823]."</i>";
	}

	# indicator
	echo "<tr><td valign=\"top\">".$lang[1255]."</td><td><textarea style=\"width:200px;height:30px\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','indicator')\">".$result_gtab[$tabgroup]["indicator"][$tbzm]."</textarea>
	<br><i style=\"color:#AAAAAA\">".$lang[2824]."</i>
	</td></tr>";
	
	# trigger
	if($gtrigger[$tabid]["id"]){
		echo "<tr><td><hr></td><td><hr></td></tr>";
		echo "<tr><td valign=\"top\">".$lang[2216]."</td><td valign=\"top\"><div id=\"triggerpool\">";
		foreach($gtrigger[$tabid]["id"] as $trid => $trval){
			if(in_array($trid,$result_gtab[$tabgroup]["trigger"][$tbzm])){$CHECKED = "CHECKED";}else{$CHECKED = "";}
			echo "<input type=\"checkbox\" id=\"trigger_$trid\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','trigger')\"> ".$gtrigger[$tabid]["trigger_name"][$trid]." (".$gtrigger[$tabid]["type"][$trid].")<br>";
		}
		echo "<tr><td></td><td><i style=\"color:#AAAAAA\">".$lang[2825]."</i></td></tr>";
	}
	
	
	/* --------------------------------------------------------- */
	/* ----------------- Calendar settings --------------------- */ 
	if($result_gtab[$tabgroup]["typ"][$tbzm] == 2){
		echo "<tr><td><hr></td><td><hr></td></tr>
		<tr><td colspan=\"2\" align=\"center\" class=\"tabHeaderItem\">".$lang[2852]."</td></tr>";
		echo "<tr><td valign=\"top\">".$lang[2850]."</td><td>";
		echo "<select onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params1')\"><option>";
		$sqlquery = "SELECT FIELD_ID,FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE = 11 AND DATA_TYPE = 24";
		$rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
		while(odbc_fetch_row($rs)) {
			if(odbc_result($rs, "FIELD_ID") == $result_gtab[$tabgroup]["params1"][$tbzm]){$selected = 'selected';}else{$selected = '';}
			echo "<option value=\"".odbc_result($rs, "FIELD_ID")."\" $selected>".odbc_result($rs, "FIELD_NAME");
		}
		echo "</select>
		<br><i style=\"color:#AAAAAA\">".$lang[2851]."</i>
		</td></tr>";

		# viewmode
		${'viewmode_'.$result_gtab[$tabgroup]["params2"][$tbzm]['viewmode']} = 'selected';
		echo "<tr><td valign=\"top\">viewmode</td><td><select name=\"param2[viewmode]\" style=\"width:100%\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\">
		<option>
		<option $viewmode_month>month
		<option $viewmode_agendaWeek>agendaWeek
		<option $viewmode_agendaDay>agendaDay
		<option $viewmode_basicWeek>basicWeek
		<option $viewmode_basicDay>basicDay";
		
		if($result_gtab[$tabgroup]["params1"][$tbzm]){	
		echo "<option $viewmode_resourceDay>resourceDay
		<option $viewmode_resourceWeek>resourceWeek
		<option $viewmode_resourceNextWeeks>resourceNextWeeks
		<option $viewmode_resourceMonth>resourceMonth";
		}

		echo "</select>
		</td></tr>";
		
		# weekNumberTitle
		echo "<tr><td style=\"width:90px;\" valign=\"top\">weekNumberTitle</td><td><input type=\"text\" name=\"param2[weekNumberTitle]\" style=\"width:100%\" value=\"".$result_gtab[$tabgroup]["params2"][$tbzm]['weekNumberTitle']."\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"></td></tr>";
		
		
		# search Calendar
		echo "<tr><td valign=\"top\">searchCalendar</td><td><select multiple style=\"width:100%\" name=\"param2[searchCalendar][]\" size=\"2\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		foreach ($gfield[$tabid]['field_name'] as $key => $value){
			echo "<option value=\"$key\" ";
			if(is_array($result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar']) AND in_array($key,$result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar'])){echo 'selected';}
			echo ">$value";
		}
		echo "</select></td></tr>";

		# search Resource
		if($result_gtab[$tabgroup]["params1"][$tbzm]){
			$rtabid = $gfield[$tabid]['verkntabid'][$result_gtab[$tabgroup]["params1"][$tbzm]];
			echo "<tr><td valign=\"top\">searchResource</td><td><select multiple style=\"width:100%\" name=\"param2[searchResource][]\" size=\"2\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
			foreach ($gfield[$rtabid]['field_name'] as $key => $value){
				echo "<option value=\"$key\" ";
				if(is_array($result_gtab[$tabgroup]["params2"][$tbzm]['searchResource']) AND in_array($key,$result_gtab[$tabgroup]["params2"][$tbzm]['searchResource'])){echo 'selected';}
				echo ">$value";
			}
			echo "</select></td></tr>";
		}

		echo "<tr><td colspan=2><table cellpadding=1 cellspacing=0 style=\"width:300px\">";
		# minTime
		echo "<tr><td style=\"width:82px;\" valign=\"top\">minTime</td><td style=\"width:50px;\" align=\"right\"><select name=\"param2[minTime]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=0;$i<=23;$i++){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['minTime'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</td><td style=\"width:40px;\">&nbsp;</td>";
		# maxTime
		echo "<td valign=\"top\">maxTime</td><td style=\"width:50px;\" align=\"right\"><select name=\"param2[maxTime]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=1;$i<=24;$i++){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['maxTime'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</td></tr>";
		

		# firsthour
		echo "<tr><td align=\"top\">firstHour</td><td style=\"width:50px;\" align=\"right\"><select name=\"param2[firstHour]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=1;$i<=24;$i++){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['firstHour'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</select></td><td style=\"width:40px;\">&nbsp;</td>";
		# slotminutes
		echo "<td valign=\"top\">slotMinutes</td><td align=\"right\"><select name=\"param2[slotMinutes]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=5;$i<=240;$i=$i+5){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['slotMinutes'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</select></td></tr>";
		
		# firstday
		echo "<tr><td valign=\"top\">firstDay</td><td align=\"right\"><select name=\"param2[firstDay]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=1;$i<=31;$i++){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['firstDay'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</select></td><td>&nbsp;</td>";
		# snapMinutes
		echo "<td valign=\"top\">snapMinutes</td><td align=\"right\"><select name=\"param2[snapMinutes]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
		for ($i=5;$i<=240;$i=$i+5){
			echo "<option ";
			if($result_gtab[$tabgroup]["params2"][$tbzm]['snapMinutes'] == $i){echo 'selected';}
			echo ">$i";
		}
		echo "</select></td></tr>";
		echo "</table></td></tr>";
		
		
		echo "<tr><td colspan=2><table cellpadding=0 cellspacing=0 style=\"width:300px\">";
		# editable
		echo "<tr><td style=\"width:90px;\"  valign=\"top\">editable</td><td style=\"width:50px;\" align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[editable]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['editable']){echo 'checked';}
		echo "></td><td style=\"width:40px;\">&nbsp;</td>";
		# selectable
		echo "<td valign=\"top\">selectable</td><td align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[selectable]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['selectable']){echo 'checked';}
		echo "></td></tr>";
		
		# weekNumbers
		echo "<tr><td valign=\"top\">weekNumbers</td><td align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[weekNumbers]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['weekNumbers']){echo 'checked';}
		echo "></td><td>&nbsp;</td>";
		# weekends
		echo "<td valign=\"top\">weekends</td><td align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[weekends]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['weekends']){echo 'checked';}
		echo "></td></tr>";
		
		# repetition
		echo "<tr><td valign=\"top\">repetition</td><td align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[repetition]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['repetition']){echo 'checked';}
		echo "></td><td>&nbsp;</td>";
		# allDayDefault
		echo "<td valign=\"top\">allDayDefault</td><td align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[allDayDefault]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
		if($result_gtab[$tabgroup]["params2"][$tbzm]['allDayDefault']){echo 'checked';}
		echo "></td></tr>";
		echo "</table></td></tr>";

	}

    /* --------------------------------------------------------- */
    /* ----------------- Kanban settings --------------------- */
    if($result_gtab[$tabgroup]["typ"][$tbzm] == 7) {
        echo "<tr><td><hr></td><td><hr></td></tr>
		<tr><td colspan=\"2\" align=\"center\" class=\"tabHeaderItem\">".$lang[2852]."</td></tr>";
        # search Kanban
        echo "<tr><td valign=\"top\">Search Kanban</td><td><select multiple style=\"width:100%\" name=\"param2[searchKanban][]\" size=\"" . (count($gfield[$tabid]['field_name']) + 1) . "\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
        foreach ($gfield[$tabid]['field_name'] as $key => $value){
            echo "<option value=\"$key\" ";
            if(is_array($result_gtab[$tabgroup]["params2"][$tbzm]['searchKanban']) AND in_array($key,$result_gtab[$tabgroup]["params2"][$tbzm]['searchKanban'])){echo 'selected';}
            echo ">$value";
        }
        echo "</select></td></tr>";

        # showactive
        echo "<tr><td style=\"width:90px;\"  valign=\"top\">Show active filters</td><td style=\"width:50px;\" align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[showactive]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
        if($result_gtab[$tabgroup]["params2"][$tbzm]['showactive']){echo 'checked';}
        echo "></td><td style=\"width:40px;\">&nbsp;</td>";

        # showdefaultsearch
        echo "<tr><td style=\"width:90px;\"  valign=\"top\">Show default search</td><td style=\"width:50px;\" align=\"right\"><input type=\"checkbox\" value=\"1\" name=\"param2[showdefaultsearch]\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\" ";
        if($result_gtab[$tabgroup]["params2"][$tbzm]['showdefaultsearch']){echo 'checked';}
        echo "></td><td style=\"width:40px;\">&nbsp;</td>";

    }

		
		
	echo "</table></td><td valign=\"top\"><table style=\"width:250px;\">";
	
	
	# logging
	if(!$isview){
		if($result_gtab[$tabgroup]["logging"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[1779]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','logging')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2826]."</i>
		</td></tr>";
		# lockable
		if($result_gtab[$tabgroup]["lockable"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[657]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','lockable')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2827]."</i>
		</td></tr>";
		# linecolor
		if($result_gtab[$tabgroup]["linecolor"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[1601]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','linecolor')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2828]."</i>
		</td></tr>";
		# userrules
		if($result_gtab[$tabgroup]["userrules"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[575]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','userrules')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2829]."</i>
		</td></tr>";
		# ajaxpost
		if($result_gtab[$tabgroup]["ajaxpost"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[2640]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','ajaxpost')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2830]."</i>
		</td></tr>";
		# groupable
		if($result_gtab[$tabgroup]["groupable"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[1465]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','groupable')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2831]."</i>
		</td></tr>";
		# reserveid
		if($result_gtab[$tabgroup]["reserveid"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[2703]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','reserveid')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2832]."</i>
		</td></tr>";
		# datasync
		if($result_gtab[$tabgroup]["datasync"][$tbzm] == 1){$CHECKED = "CHECKED";}else{$CHECKED = "";}
		echo "<tr><td valign=\"top\">".$lang[2703]."</td><td valign=\"top\"><input type=\"checkbox\" value=\"1\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','datasync')\">
		<br><i style=\"color:#AAAAAA\">".$lang[2832]."</i>
		</td></tr>";
	}

	# rebuild rules
	echo "<tr><td valign=\"top\">".$lang[575]."</td><td><i style=\"cursor:pointer\" class=\"lmb-icon lmb-refresh\" onclick=\"open('main_admin.php?action=setup_grusrref&check_table=$tabid&check_all=1' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400')\"></i>
	<br><i style=\"color:#AAAAAA\">".$lang[1054]."</i>
	</td></tr>";
	
	# rebuild temp
	echo "<tr><td valign=\"top\">".$lang[2761]."</td><td><i style=\"cursor:pointer\" class=\"lmb-icon lmb-refresh\" onclick=\"ajaxEditTable(this,'$tabid','$tabgroup','tablesync')\"></i>";
	if($tablesync){echo " <i class=\"lmb-icon lmb-aktiv\"></i>";}
	echo "<br><i style=\"color:#AAAAAA\">".$lang[2030]."</i>
	</td></tr>";
	
	echo "</table>";

	?>

	
	
	</td></tr></table>
	
	</div>
	
	<div id="tab2" style="width:420px;display:none;padding:5px;">
	<table style="width:100%">
	<?php
	if($col){
		
	foreach ($col['columnname'] as $tkey => $tvalue)
	{
		echo "<tr><td style=\"border-bottom:1px solid #CCCCCC\">".$tvalue."</td>
		<td style=\"border-bottom:1px solid #CCCCCC\">".$col["datatype"][$tkey]."&nbsp;</td>
		<td style=\"border-bottom:1px solid #CCCCCC\">".$col["length"][$tkey]."&nbsp;";
		if($col["scale"][$tkey]){
			echo "(".$col["scale"][$tkey].")";
		}

		echo "</td></tr>";


	}
	}
	?>
	</table>
	</div>
	
	
	</td></tr></table>
	
	</form>