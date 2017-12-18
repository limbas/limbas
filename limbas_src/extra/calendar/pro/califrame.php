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
 * ID:
 */



# Ansicht kurze Termine für Monat
function month_date($wstamp,$gtabid){
	global $tresult;

	$doy = date("z",$wstamp);
	if($tresult["st_st"][$doy]){
	foreach($tresult["st_st"][$doy] as $key => $value){
		if($tresult["marker"][$doy][$key]){$marker = "border-left:4px solid #".$tresult["marker"][$doy][$key].";";}else{$marker = "border-left:4px solid blue";}
		echo "<div style=\"background-color:transparent;width:95%;border-left:margin:1px;overflow:hidden;cursor:pointer;$marker\"
		title=\"".date("H:i:s",$tresult["st_st"][$doy][$key])." - ".date("H:i:s",$tresult["en_st"][$doy][$key])."\"
		OnClick=\"open_detail('".$tresult["id"][$doy][$key]."');\">
		&nbsp;".substr(htmlentities($tresult["subject"][$doy][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"]),0,100)."</div>";
	}}
}

# Ansicht lange Termine für Monat
function longday_view($tresult){
	global $farbschema;
	global $tabwidth;

	if($tresult["l"]["id"]){
	foreach ($tresult["l"]["id"] as $key => $value){
		echo "<tr OnDblClick=\"open_detail('".$tresult["l"]["id"][$key]."');\" OnClick=\"lmbCalShowInfo('".$tresult["l"]["id"][$key]."')\">\n";
		
		if($tresult["l"]["per_st"][$key]>0){
			echo "<td colspan=\"".$tresult["l"]["per_st"][$key]."\"></td>\n";	
		}
		
		echo "<td colspan=\"".$tresult["l"]["per_len"][$key]."\" align=\"left\" title=\"".stampToDate($tresult["l"]["st_st"][$key],3)." - ".stampToDate($tresult["l"]["en_st"][$key],3)."\">
		<table style=\"width:$tabwidth;height:15px;border:1px solid ".$farbschema[WEB12].";background-color:".$farbschema[WEB11].";overflow:hidden;margin:1px;margin-right:4px;\"><tr>
		<td style=\"width:30px;color:green;\" valign=\"center\">[".$tresult["l"]["is_begin"][$key]."]</td>
		<td valign=\"center\"><textarea style=\"width:100%;height:13px;border:none;background-color:transparent;overflow:hidden;\" id=\"lt_".$tresult["l"]["id"][$key]."\" name=\"lt_".$tresult["l"]["id"][$key]."\" OnChange=\"change_value(this);term_modus=1;\">".htmlentities($tresult["l"]["subject"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</textarea></td>
		<td style=\"width:30px;color:green;\" valign=\"center\">[".$tresult["l"]["is_end"][$key]."]</td>
		</tr></table></td></tr>\n";
	}}
}

# Ansicht lange Termine für Tag/Woche
function longmonth_view($dstamp){
	global $farbschema;
	global $tabwidth;
	global $gtabid;
	global $gresult;

	$tresult = list_month_ltermin($gresult,$dstamp);
	if($tresult["l"]["id"]){
	foreach ($tresult["l"]["id"] as $key => $value){
		echo "<tr OnDblClick=\"open_detail('".$tresult["l"]["id"][$key]."');\" OnClick=\"lmbCalShowInfo('".$tresult["l"]["id"][$key]."')\">";
		if($tresult["l"]["per_st"][$key]>0)
			echo "<td colspan=\"".$tresult["l"]["per_st"][$key]."\"></td>";
			
		echo "<td colspan=\"".$tresult["l"]["per_len"][$key]."\" align=\"left\" title=\"".stampToDate($tresult["l"]["st_st"][$key],3)." - ".stampToDate($tresult["l"]["en_st"][$key],3)."\">
		<table style=\"width:$tabwidth;height:15px;border:1px solid ".$farbschema[WEB3].";background-color:".$farbschema[WEB1].";overflow:hidden;margin:1px;margin-right:2px;\"><tr>
		<td style=\"width:30px;color:green;\" valign=\"center\">[".$tresult["l"]["is_begin"][$key]."]</td>
		<td valign=\"center\"><textarea style=\"width:100%;height:13px;border:none;background-color:transparent;overflow:hidden;\" id=\"lt_".$tresult["l"]["id"][$key]."\" name=\"lt_".$tresult["l"]["id"][$key]."\" OnChange=\"change_value(this);term_modus=1;\">".htmlentities($tresult["l"]["subject"][$key],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])."</textarea></td>
		<td style=\"width:30px;color:green;\" valign=\"center\">[".$tresult["l"]["is_end"][$key]."]</td>
		</tr></table></td>";
		
		if(7-$tresult["l"]["per_st"][$key]-$tresult["l"]["per_len"][$key]>0)
			echo "<td colspan=\"" . (7-$tresult["l"]["per_st"][$key]-$tresult["l"]["per_len"][$key]) . "\"></td>";
		
		echo "</tr>\n";
	}}
}


# Sommer/Winterzeit
function lmbGetSWNextDay($stamp){
	$swt = date("I",$stamp);
	$swn = date("I",$stamp+86400);

	if($swt > $swn){
		$stamp_[0] = $stamp+3600;
		$stamp_[1] = "Winterzeit";
	}elseif($swt < $swn){
		$stamp_[0] = $stamp-3600;
		$stamp_[1] = "Sommerzeit";
	}else{
		$stamp_[0] = $stamp;
	}
	return $stamp_;
}

# Tages Ansicht
function day_view($tzone,$stamp){
	global $farbschema;
	global $session;
	global $viewtype;
	global $session;
	
	if($viewtype == 1){
		$day = "<i class=\"lmb-icon lmb-arrow-left-caret u-color-3\" align=\"absbottom\" OnClick=\"document.form1.show_date.value='".date("Y-m-d",($stamp-86400))."';document.form1.viewtype.value=1;document.form1.submit();\"></i>&nbsp;&nbsp;".$day = strftime("%A (%d)",$stamp)."&nbsp;&nbsp;<i class=\"lmb-icon lmb-arrow-right-caret u-color-3\" align=\"absbottom\" OnClick=\"document.form1.show_date.value='".date("Y-m-d",($stamp+86400))."';document.form1.viewtype.value=1;document.form1.submit();\"></i>";
	}else{
		#$day = stampToDate($stamp,"l");
		$day = strftime("%a (%d)",$stamp);
		$to_day="OnClick=\"document.form1.show_date.value='".date("Y-m-d",$stamp)."';document.form1.viewtype.value=1;document.form1.submit();\"";
	}

	if($tzone < $subh){$hg = $subh;}else{$hg = $tzone;}
	echo "\n\n<Script language=\"JavaScript\">\n";
	echo "var cell_height = $hg;\n";
	echo "</Script>\n\n";

	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" style=\"border-collapse:collapse;\">\n";

	echo "<tr bgcolor=\"".$farbschema["WEB7"]."\">
		<td style=\"font-size:12;border:1px solid ".$farbschema["WEB12"].";\"></td>
		<td align=\"center\" style=\"cursor:pointer;font-size:12;border:1px solid ".$farbschema["WEB12"].";\" $to_day>".$day."</td>
		</tr>\n";
	
	# 24 Stunden
	for ($h=0;$h<=23;$h++){
		$split = round((60/$tzone));
		for ($s=1;$s<=$split;$s++){
			$time = $stamp + (($s*$hg-$hg)*60) + ($h*3600);
			if($h == 7 AND $s == 1){$defpos = "defpos";}else{$defpos = "";}
			echo "<tr id=\"tr_".$time."\">";
			if($s==1){
				echo "<td valign=\"top\" class=\"houre\" style=\"height:".$hg."px;\"><a name=\"$defpos\">&nbsp;<b>".sprintf("%02d",$h)."</b></a></td>";
			}else{
				echo "<td valign=\"center\" align=\"right\" class=\"minute\" style=\"height:".$hg."px;\"></td>";
			}
			
			echo "<td valign=\"top\" id=\"td_".$time."\" abbr=\"$farbschema[WEB10]\" style=\"border:1px solid ".$farbschema["WEB12"].";background-color:".$farbschema[WEB10]."\" OnMouseOver=\"select_tmp(event,this,0)\" OnMouseDown=\"select_tmp(event,this,1)\"><div id=\"d_".$time."\" style=\"position:absolute;\">&nbsp;</div></td></tr>\n";
		}
	}
	$time += ($tzone*60-1);
	echo "<tr><td colspan=\"2\" id=\"td_".$time."\" abbr=\"$farbschema[WEB10]\" style=\"border:1px solid ".$farbschema["WEB12"].";\"><div id=\"d_".$time."\" style=\"position:absolute;\">&nbsp;</div>&nbsp;</td></tr>\n";
	echo "</table>";
}


# Monats Ansicht
function month_view($tresult,$gtabid){
	global $farbschema;
	global $session;
	global $viewtype;
	global $lang;
	global $view_days;

	$stamp = $tresult["show_stamp"];

	echo "<table cellpadding=\"0\" cellspacing=\"0\" border=\"1\" width=\"99%\" style=\"border-collapse:collapse\">\n";
	#echo "<tr bgcolor=\"".$farbschema["WEB7"]."\"><td colspan=\"".($view_days)."\" align=\"center\" style=\"font-size:12;border:1px solid ".$farbschema["WEB12"].";\">".strftime("%B %Y",$stamp)."</td></tr>\n";

	# Bezeichnung
	$dstamp = $stamp;
	$t = date("t",$stamp);
	$dw = date("w",$stamp);
	$tw = ceil($t/$view_days);
	#$tw = 5;
	$md = 1;
	echo "<tr bgcolor=\"".$farbschema["WEB9"]."\">\n";
	for($i=0;$i<$view_days;$i++){
		# Sommer/Winterzeit
		if(strftime("%w",$dstamp) == 0 OR strftime("%w",$dstamp) == 6){$bg = $farbschema["WEB10"];}else{$bg = $farbschema["WEB9"];}
		echo "<td align=\"center\" style=\"background-color:$bg\">".strftime("%A",$dstamp)."</td>\n";
		$dstamp_ = lmbGetSWNextDay($dstamp);
		$dstamp = $dstamp_[0];
		$dstamp += 86400;
	}
	echo "</tr>";

	$dstamp = $stamp;
	
	# Tage des Monats
	for($i=1;$i<=$tw;$i++){

		longmonth_view($dstamp,$gtabid,$tresult);

		echo "<tr bgcolor=\"".$farbschema["WEB11"]."\">\n";
		for($w=1;$w<=$view_days;$w++){

			$dom = strftime("%e",$dstamp);
			if($dom == 1){$dom = "1 ".strftime("%B",$dstamp);}
			echo "<td class=\"tabItem2\" style=\"width:".round(100/$view_days)."%;height:100px;padding:5px;\" valign=\"top\"><div style=\"width:100%\" align=\"right\"><A HREF=\"Javascript:document.form1.viewtype.value='1';document.form1.show_date.value='".date("Y-m-d ",$dstamp)."';document.form1.submit();\">".$dom." <span style=\"color:red\">".$dstamp_[1]."</span></A></div>\n";

			# Termin-Einträge
			month_date($dstamp,$gtabid);
			echo "</td>\n";
			$md++;
			
			$dstamp_ = lmbGetSWNextDay($dstamp);
			$dstamp = $dstamp_[0];
			$dstamp += 86400;
		}
		$dstamp += (7-$view_days)*86400;
		echo "</tr>\n";
	}

	echo "</table><br>\n";
}


# Tages Termine eintragen
function day_date($tresult,$g,$gtabid){

	$bzm1 = 0;
	# termin
	foreach ($tresult["bzm"] as $key => $value){
		if($tresult["group"][$key] == $g AND $value >= 0 AND $tresult["st_st"][$key] != $tresult["en_st"][$key]){
			$bzm1++;
			echo "show_termin('".$tresult["st_st"][$key]."','".$tresult["en_st"][$key]."','".$tresult["count"][$g]."','$bzm1','".$tresult["id"][$key]."',Array(".$tresult["symbols"][$key]."));\n";
		}
	}
	# subject
	foreach ($tresult["bzm"] as $key => $value){
		if($tresult["group"][$key] == $g AND $value >= 0){
			echo "fill_termin('lt_".$tresult["id"][$key]."','".htmljs($tresult["subject"][$key])."','".date("H:i:s",$tresult["st_st"][$key])." - ".date("H:i:s",$tresult["en_st"][$key])."','".$tresult["marker"][$key]."',".$tresult["subject_noedit"][$key].",".$tresult["date_noedit"][$key].");\n";
		}
	}
}
?>


<style type="text/css">
div.line {
	width:10px;
	height:1px;
	background-color:black;
	overflow:hidden;
}
td.houre {
	border-left:1px solid <?=$farbschema[WEB12]?>;
	border-right:1px solid <?=$farbschema[WEB12]?>;
	border-top:1px solid <?=$farbschema[WEB12]?>;
	width:30px;
}

td.minute {
	border-left:1px solid <?=$farbschema[WEB12]?>;
	border-right:1px solid <?=$farbschema[WEB12]?>;
	width:30px;
}

</style>


<Script language="JavaScript">

// ----- Js-Script-Variablen --------
jsvar["WEB9"] = "<?=$farbschema[WEB9]?>";
jsvar["subh"] = "<?=$subh*60?>";
jsvar["viewwidth"] = "<?=$viewwidth?>";
jsvar["gtabid"] = "<?=$gtabid?>";
jsvar["fformid"] = "<?=$fformid?>";
jsvar["lng_1424"] = "<?=$lang[1424]?>";


<?

if($viewtype==1){
echo "
parent.document.getElementById('tzoneday').style.opacity='1';
parent.document.getElementById('tzoneweek').style.opacity='0.3';
parent.document.getElementById('tzonemonth').style.opacity='0.3';
parent.document.getElementById('tzoneday').style.filter='Alpha(opacity=100)';
parent.document.getElementById('tzoneweek').style.filter='Alpha(opacity=30)';
parent.document.getElementById('tzonemonth').style.filter='Alpha(opacity=30)';
";
}

if($viewtype==2){
	echo "
parent.document.getElementById('tzoneday').style.opacity='0.3';
parent.document.getElementById('tzoneweek').style.opacity='1';
parent.document.getElementById('tzonemonth').style.opacity='0.3';
parent.document.getElementById('tzoneday').style.filter='Alpha(opacity=30)';
parent.document.getElementById('tzoneweek').style.filter='Alpha(opacity=100)';
parent.document.getElementById('tzonemonth').style.filter='Alpha(opacity=30)';
";
}

if($viewtype==3){
	echo "
parent.document.getElementById('tzoneday').style.opacity='0.3';
parent.document.getElementById('tzoneweek').style.opacity='0.3';
parent.document.getElementById('tzonemonth').style.opacity='1';
parent.document.getElementById('tzoneday').style.filter='Alpha(opacity=30)';
parent.document.getElementById('tzoneweek').style.filter='Alpha(opacity=30)';
parent.document.getElementById('tzonemonth').style.filter='Alpha(opacity=100)';
";
}
?>


</SCRIPT>



<form action="main.php" method="post" name="form1" id="test">
<input type="hidden" name="action" value="kalender_iframe">
<input type="hidden" name="show_date" value="<?=$show_date?>">
<input type="hidden" name="subh" value="<?=$subh?>">
<input type="hidden" name="viewtype" value="<?=$viewtype?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="verkn_tabid" value="<?=$verkn_tabid?>">
<input type="hidden" name="verkn_fieldid" value="<?=$verkn_fieldid?>">
<input type="hidden" name="verkn_ID" value="<?=$verkn_ID?>">
<input type="hidden" name="ctyp" value="pro">
<input type="hidden" name="drag_el">
<input type="hidden" name="resize_el">
<input type="hidden" name="change_el">
<input type="hidden" name="delete_el">
<input type="hidden" name="posy">



<table cellpadding="0" cellspacing="0" border="0" class="lmbfringeGtabBody tabfringe" style="padding-left:10px;padding-top:10px;width:100%;">

<tr><td align="left" colspan=5>&nbsp;<b><?=strftime("%B %Y",$tresult["show_stamp"])?></b></td></tr>

<TR>


<?
# Tages Ansicht
if($viewtype == 1){
	longday_view($tresult);

	#echo "<tr><td><div style=\"width:6px;\"></div></td>";

	echo "<td>";
	day_view($subh,$tresult["show_stamp"]);

	# Tages Termine
	$groupcount = $tresult["group"][count($tresult["group"])-1];
	echo "\n\n<Script language=\"JavaScript\">\n";
	echo "function day_date(){\n";
	#echo "document.getElementById('body_element').innerHTML += \"&nbsp;&nbsp;<span style='color:".$farbschema["WEB12"].";'>".stampToDate($tresult["show_stamp"],2)."</span>\";\n";
	for($i=1;$i<=$groupcount;$i++){
		day_date($tresult,$i,$gtabid);
	}
	echo "}\n";
	echo "</Script>\n\n";

# Wochen Ansicht
}elseif($viewtype == 2){
	longday_view($tresult);
	#echo "<tr><td><div style=\"width:6px;\"></div></td>";

	$week_date_stamp = $tresult["show_stamp"];
	echo "<td width=\"20%\" align=\"left\">";
	for($i=1;$i<=$view_days;$i++){
		# Sommer/Winterzeit
		day_view($subh,$week_date_stamp);
		$week_date_stamp_ = lmbGetSWNextDay($week_date_stamp);
		$week_date_stamp = $week_date_stamp_[0];
		$week_date_stamp += 86400;
		echo "</td><td width=\"20%\" align=\"center\">";
	}

	$week_date_stamp = $tresult["show_stamp"];

	# Tages Termine
	$groupcount = $tresult["group"][count($tresult["group"])-1];
	echo "\n\n<Script language=\"JavaScript\">\n";
	echo "function day_date(){\n";
	#echo "document.getElementById('body_element').innerHTML += \":&nbsp;&nbsp;&nbsp;<span style='color:".$farbschema["WEB12"].";'>".stampToDate($week_date_stamp,2)."</span>\";\n";
	for($e=1;$e<=$groupcount;$e++){
		day_date($tresult,$e,$gtabid);
	}
	echo "}\n";
	echo "</Script>\n\n";

	$week_date_stamp += 86400;

# Monatsansicht
}elseif($viewtype == 3){
	echo "<td>";
	month_view($tresult,$gtabid);
}

?>
</TD></TR>
</TABLE>
<input type="text" id="hiddenfocus" style="width:1px;position:absolute;top:-100;left:-100">
</form>