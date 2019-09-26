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
 * ID: 200
 */

$myExt_useIframe = 0; # set true if using iframe for details


# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_calendar.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_calendar.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

# benutzes Formular
if($gtab["tab_view_tform"][$gtabid]){
	$fformid = $gtab["tab_view_tform"][$gtabid];
}

$fieldid = $gfield[$gtabid]["sort"][key($gfield[$gtabid]["sort"])];

?>

<script type='text/javascript'>

<?php
echo "
jsvar['jqday'] = ['{$lang[873]}','{$lang[874]}','{$lang[875]}','{$lang[876]}','{$lang[877]}','{$lang[878]}','{$lang[879]}'];
jsvar['jqmonth'] = ['{$lang[880]}','{$lang[881]}','{$lang[882]}','{$lang[883]}','{$lang[884]}','{$lang[885]}','{$lang[886]}','{$lang[887]}','{$lang[888]}','{$lang[889]}','{$lang[890]}','{$lang[891]}'];
jsvar['gtabid'] = {$gtabid};
jsvar['searchcount'] = {$umgvar['searchcount']};
jsvar['startstamp'] = '{$startstamp}';
jsvar['dateformat'] = {$session['dateformat']};
jsvar['verkn_ID'] = '{$verkn_ID}';
jsvar['verkn_tabid'] = '{$verkn_tabid}';
jsvar['verkn_fieldid'] = '{$verkn_fieldid}';
jsvar['fformid'] = '{$fformid}';

var lmb_localobj = new lmb_localobj();
";

/* ----- setting parameters ------- */
$calsetting = array('viewmode'=>$umgvar['calendar_viewmode'],'minTime'=>'00:00:00','maxTime'=>'24:00:00','weekNumberTitle'=>'','firstHour'=>$umgvar['calendar_firsthour'],'firstDay'=>$umgvar['calendar_firstday'],'slotMinutes'=>$umgvar['calendar_slotminutes'],'snapMinutes'=>0,'editable'=>'b','selectable'=>'b','weekNumbers'=>0,'weekends'=>'b','allDayDefault'=>'b');

foreach ($calsetting as $option => $type){

	if(${'cal_'.$option}){
		$value = ${'cal_'.$option};
	}elseif ($gtab['params2'][$gtabid][$option]){
		$value = $gtab['params2'][$gtabid][$option];
	}else{
		$value = null;
	}

	${'cal_'.$option} = $value;
	
	if(!$value AND $type == 'b'){
		$value = 'false';
	}elseif (!$value AND is_numeric($type)){
		$value = $type;
	}elseif (!$value AND !is_numeric($type)){
		${'cal_'.$option} = $type;
		$value = "'$type'";
	}else {
		${'cal_'.$option} = $value;
		$value = "'".$value."'";
	}

	
	echo "jsvar['cal_$option'] = $value;\n";

	# hide-weekends in ganttview not supported
	if($gtab['params1'][$gtabid]){
		echo "jsvar['cal_weekends'] = true;\n";
		$cal_weekends = 1;
	}
	
	#error_log('cal_'.$option.' # '.$value);
}

if($startstamp){
	echo "
	jsvar['year'] = ".date("Y",$startstamp).";
	jsvar['month'] = ".(date("m",$startstamp)-1).";
	jsvar['date'] = ".date("d",$startstamp).";";
}

if($form_id OR $form_id = $gformlist[$gtabid]["id"][$gtab["tab_view_tform"][$gtabid]]){
	echo "jsvar['formid'] = '".$form_id."';\n";
	echo "jsvar['form_dimension'] = '".$gformlist[$gtabid]["dimension"][$gtab["tab_view_tform"][$gtabid]]."';\n";
	echo "jsvar['iframe'] = ".$myExt_useIframe.";\n"; 
}
?>

$(document).ready($(function() {
	lmb_datepicker(null,'cal_searchDate','cal_datepicker',null,'yy-mm-dd',10,'lmb_pickerSelected');
	var postform = new Array('formCalendar');
	lmb_calInit();
}));

</script>



<div ID="viewmenu" class="lmbContextMenu" style="display:none;z-index:2000;" OnClick="activ_menu = 1;">
<?php #-----------------  -------------------
pop_menu(255,'','');
pop_line();
$opt = array();
$opt['val'] = array('month','agendaWeek','agendaDay','basicWeek','basicDay');
$opt['desc'] = array($lang[1437],$lang[1436],$lang[1435],$lang[1436],$lang[1435]);
$opt['label'] = array($lang[2853],null,null,'basic');
if($gtab['params1'][$gtabid]){
	array_unshift($opt['val'],'resourceMonth','resourceWeek','resourceDay');
	array_unshift($opt['desc'],$lang[1437],$lang[1436],$lang[1435]);
	array_unshift($opt['label'],$lang[2854],null,null);
}
pop_select("lmb_calChangeSettings('viewmode',this.value);",$opt,$cal_viewmode,'','',"Anzeigemodus: ");

//Firstday
$opt = array();
$opt['val'] = array(1,2,3,4,5,6,0);
$opt['desc']= array($lang[874],$lang[875],$lang[876],$lang[877],$lang[878],$lang[879],$lang[873]);
pop_select("lmb_calChangeSettings('firstDay',this.value);",$opt,$cal_firstDay,'','',"Erster Tag der Woche: ");

//Slotminutes
$opt = array();
$opt['val'] = array(5,15,30,60,120,240);
$opt['desc'] = array("5","15","30","60","120","240");
pop_select("lmb_calChangeSettings('slotMinutes',this.value);",$opt,$cal_slotMinutes,'','','Minuten pro Zeile: ');

//Snapminutes
$opt = array();
$opt['val'] = array();
for($i = 1; $i <= 48; $i++){
	$opt['val'][$i] = $i*5;
}
$opt['desc'] = $opt['val'];
$sel=10;
pop_select("lmb_calChangeSettings('snapMinutes',this.value);",$opt,$cal_snapMinutes,'','','Snapminutes: ');

//Weekends
if($cal_weekends){$cal_weekends_ = 'checked';}
pop_checkbox('',"if($(this).is(':checked')){lmb_calChangeSettings('weekends',1)}else{lmb_calChangeSettings('weekends',0)}",'',"",$cal_weekends_,'','Wochenenden: ','');

//Zeitfilter
pop_line();
pop_input2("lmb_calChangeSettings('minTime',this.value);",'',$cal_minTime,"","MinTime");
pop_input2("lmb_calChangeSettings('maxTime',this.value);",'',$cal_maxTime,"","MaxTime");
pop_bottom();
?>
</div>

<?php
# extension
if(function_exists($GLOBALS["gLmbExt"]["menuCalExtras"][$gtabid])){
	echo '<div ID="extrasmenu" class="lmbContextMenu" style="visibility:hidden;z-index:2001;" OnClick="activ_menu = 1;">';
	$GLOBALS["gLmbExt"]["menuCalExtras"][$gtabid]($gtabid,$form_id,$ID,$gresult);
	$extrasmenu = 1;
	echo '</div>';
}
?>

<?php if($LINK[296]){
$DPdateFormat = setDateFormat(1,2);
echo <<<EOD

<div ID="bulkmenu" class="lmbContextMenu" style="display:none;z-index:2001;" OnClick="activ_menu = 1;">
<div style="padding:10px;margin-top:3px" class="bulkContainer ui-widget-header">

<table width="98%">

<tr><td colspan="3"><b>{$lang[1441]}</b></td></tr>

<tr><td class="ui-widget-header" colspan="3" id="bulk_ts">
<table width="400px"><tr>
<td>{$lang[2799]}:</td><td><input name="calBulk_termStaH[0]" type="text" style="width:20px" onchange="lmb_validateTime(this,'h')">&nbsp;<b>:</b>&nbsp;<input name="calBulk_termStaM[0]" type="text" style="width:20px" value="00" onchange="lmb_validateTime(this,'m')"></td>
<td>{$lang[2385]}:</td><td><input name="calBulk_termEndH[0]" type="text" style="width:20px" onchange="lmb_validateTime(this,'h')">&nbsp;<b>:</b>&nbsp;<input name="calBulk_termEndM[0]" type="text" style="width:20px" value="00" onchange="lmb_validateTime(this,'m')"></td>
<td>{$lang[2801]}:</td><td><input name="calBulk_termLenD[0]" type="text" style="width:40px" onchange="lmb_validateTime(this,'d')"> min.</td>
<td><i class="lmb-icon lmb-plus" onclick="lmb_bulkAddTermin()"></i></td></tr></table>
</td></tr>

<tr><td colspan="3"><b>{$lang[2803]}</b></td></tr>

<tr>
<td class="ui-widget-header">
<table style="height:100%">
<tr>
<td>{$lang[874]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[1]" value="1" checked style="margin-right:18px"></td>
<td>{$lang[875]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[2]" value="1" checked style="margin-right:18px"></td>
<td>{$lang[876]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[3]" value="1" checked style="margin-right:18px"></td>
<td>{$lang[877]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[4]" value="1" checked style="margin-right:18px"></td>

</tr>

<tr>
<td>{$lang[878]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[5]" value="1" checked style="margin-right:18px"></td>
<td>{$lang[879]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[6]" value="1" checked style="margin-right:18px"></td>
<td>{$lang[873]}</td><td><input type="checkbox" class="checkbox" name="calBulk_schmeaDay[7]" value="1" checked style="margin-right:18px"></td>
<td></td></tr>
</table>
</td></tr>

<tr><td colspan="3"><b>{$lang[2802]}</b></td></tr>

<tr><td colspan="3" class="ui-widget-header">
<table width="400px"><tr>
<td>{$lang[2799]}:</td>
<td><input type="text" id="calBulk_periodStartT" name="calBulk_periodStart" style="width:100px"> <i id="sel_7" border="0" align="middle" onclick="lmb_datepicker(event,this,'','','{$DPdateFormat}')" style="cursor:pointer;vertical-align:top;" title="öffne Quick-Kalender" class="lmb-icon lmb-caret-right"></i></td>
<td>&nbsp;&nbsp;</td>
<td>{$lang[2385]}:</td>
<td><input type="text" id="calBulk_periodEndT" name="calBulk_periodEnd" style="width:100px"> <i id="sel_7" border="0" align="middle" onclick="lmb_datepicker(event,this,'','','{$DPdateFormat}')" style="cursor:pointer;vertical-align:top;" title="öffne Quick-Kalender" class="lmb-icon lmb-caret-right"></i></td>
</tr></table>
</td></tr>
</table>
</div>
</div>
EOD;
}
?>




<form action="main.php" method="post" name="formCalendar" id="formCalendar">
<input type="hidden" name="action" value="kalender">

<input type="hidden" name="startstamp" value="<?=$startstamp?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="verkn_tabid" value="<?=$verkn_tabid?>">
<input type="hidden" name="verkn_fieldid" value="<?=$verkn_fieldid?>">
<input type="hidden" name="verkn_ID" value="<?=$verkn_ID?>">

<input type="hidden" name="cal_viewmode" value="<?=$cal_viewmode?>">
<input type="hidden" name="cal_firstDay" value="<?=$cal_firstDay?>">
<input type="hidden" name="cal_minTime" value="<?=$cal_minTime?>">
<input type="hidden" name="cal_maxTime" value="<?=$cal_maxTime?>">
<input type="hidden" name="cal_firstHour" value="<?=$cal_firstHour?>">
<input type="hidden" name="cal_slotMinutes" value="<?=$cal_slotMinutes?>">
<input type="hidden" name="cal_snapMinutes" value="<?=$cal_snapMinutes?>">
<input type="hidden" name="cal_weekends" value="<?=$cal_weekends?>">
<input type="hidden" name="filter_reset">

<div class="lmbfringegtab" style="overflow:auto">


<div style="float:left;padding-left:10px;margin-right:100px;width:calc(100% - 30px);border-bottom:1px solid <?=$farbschema['WEB3']?>;">
    <span style="float:left;font-size:1.4em;" onclick="lmb_calReload(true,false)"><?= $gtab['desc'][$gtabid] ?></span>
    <span style="float:right;"><i class="lmb-icon lmb-top-y u-color-4" style="cursor:pointer;font-size: 2em;marging-bottom:5px;" onclick="$('#calendar').fullCalendar('prev');lmb_calSetTitle();"></i><i class="lmb-icon lmb-top-x u-color-4" style="cursor:pointer;font-size: 2em;" onclick="$('#calendar').fullCalendar('next');lmb_calSetTitle();"></i></span>
    <span style="transform: translateY(+10%);float:right;vertical-align: text-bottom;padding-right:10px;font-size:1.3em;font-weight:bold;" id="current_date"><span>
</div>


<table width="99%" style="padding-top:15px;"><tr><td valign="top" align="left" id="cal_searchFrameTD" style="width:230px;padding-right:20px;<?php $menu_setting=lmbGetMenuSetting();if(!$menu_setting["frame"]["cal"]){echo 'display:none';}?>">
<div id="cal_searchFrame" style="<?php if(!$menu_setting["frame"]["cal"]){echo 'display:none';}?>">
<div id="cal_datepicker"></div>
<br>
<table id="extsearchtab" cellpadding="2" cellspacing="0">


<?php

# extension
if(function_exists($GLOBALS["gLmbExt"]["calSearchMenu"][$gtabid])){
	$GLOBALS["gLmbExt"]["calSearchMenu"][$gtabid]($gtabid);
}

if($gtab['params2'][$gtabid]['searchCalendar']){
	
	echo "<tr class=\"tabSubHeader\"><td colspan=\"2\" align=\"center\" class=\"tabSubHeaderItem\">".$gtab["desc"][$gtabid]."</td></tr>";
	
	foreach ($gtab['params2'][$gtabid]['searchCalendar'] as $key => $value){
		if(!$gfield[$gtabid]['sort'][$value]){continue;}
		echo "<tr class=\"tabBody\"><td nowrap>".$gfield[$gtabid]['spelling'][$value].":</td><td ondblclick=\"limbasDetailSearch(event,this,'$gtabid','$value','lmbCalAjaxContainer')\">";
		lmbGlistSearchDetail($gtabid,$value);
		echo "</td></tr>";
	}
}



# search Resource
if($gtab["params1"][$gtabid] AND $gtab["params2"][$gtabid]['searchResource']){
	
	$rtabid = $gfield[$gtabid]['verkntabid'][$gtab["params1"][$gtabid]];
	
	echo "<tr><td>&nbsp;</td></tr><tr class=\"tabSubHeader\"><td colspan=\"2\" align=\"center\" class=\"tabSubHeaderItem\">".$gtab["desc"][$rtabid]."</td></tr>";

	echo "<tr class=\"tabBody\"><td colspan=\"2\"><select id=\"cal_resourceSearch\" name=\"cal_resourceSearch\" multiple size=\"4\" class=\"gtabHeaderInputINP\" style=\"width:100%;\"><option>";
	if(!$lmb_calendar){$lmb_calendar = new lmb_calendar;}
	if($resources = $lmb_calendar->getResources($gtabid,$null)){
		foreach ($resources as $key => $value){
			echo "<option value=\"".$resources[$key]['id']."\">".$resources[$key]['name'];
		}
	}
	echo "</select></td></tr>";

	#$event["change"] = "lmb_calResource()";
	foreach ($gtab['params2'][$gtabid]['searchResource'] as $key => $value){
		if(!$gfield[$rtabid]['sort'][$value]){continue;}
		echo "<tr class=\"tabBody\"><td nowrap>".$gfield[$rtabid]['spelling'][$value].":</td><td ondblclick=\"limbasDetailSearch(event,this,'$rtabid','$value','lmbCalAjaxContainer')\">";
		lmbGlistSearchDetail($rtabid,$value,null,$event);
		echo "</td></tr>";
	}
}
?>

<tr><td colspan="2" align="center"><hr><input type="button" value="suchen" onclick="lmb_calReload(1)">&nbsp;<input type="button" value="erweitert" onclick="limbasDetailSearch(event,this,'<?=$gtabid?>','','lmbCalAjaxContainer')">&nbsp;<input type="button" value="reset" onclick="lmb_extsearchclear()"></td></tr>
</table>

</div>

</td><td valign="top">

<div style="display: block;float: none;overflow: hidden;padding-right:10px;">

<table id="CalTable" width="100%" cellspacing="0" cellpadding="0" border="0" style="border-collapse:collapse;margin-right:20px;">
<tr><td>
<table border="0" cellspacing="0" cellpadding="0"><tr>
<td nowrap id="menu1" class="lmbGtabTabmenuActive" onclick="document.formCalendar.submit();"><?=$gtab["desc"][$gtabid]?></TD>
<td class="lmbGtabTabmenuSpace">&nbsp;</td>
</tr></table>
</td></tr>

<tr><td >
<div id="iconmenu" class="gtabHeaderSymbolTR" style="height:35px;">
<table cellpadding="2" cellspacing="0" style="border-collapse: collapse;">

<tr>
<TD>
</TD>
<?php if($LINK[224] AND $cal_viewmode != 3){?><td><i class="lmb-icon lmb-cog" onclick="limbasDivShow(this,'','viewmenu');" title="<?=$lang[$LINK['name'][224]]?>" style="cursor:pointer;"></i></td><?php }?>
<TD><i class="lmb-icon lmb-plus-square" onclick="lmb_calDetail(0, event, 0, 'gtab_neu');" title="<?=$lang[1435]?>" style="cursor:pointer;"></i></TD>
<?php /* lmb_datepicker(event,this,null,null,'yy-mm-dd',10,'lmb_pickerSelected') */ ?>
<TD><input type="hidden"><i class="lmb-icon lmb-page-find" OnClick="lmb_calSearchFrame()" TITLE="<?=$lang[2255]?>" style="cursor:pointer;"></i></TD>
<?php if($extrasmenu){?><td><i class="lmb-icon lmb-puzzle-piece" onclick="limbasDivShow(this,'','extrasmenu');" title="<?=$lang[1939]?>" style="cursor:pointer;"></i></td><?php }?>
<TD>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</TD>

<?php
# resource
if($gfield[$gtabid]["md5tab"][$gtab['params1'][$gtabid]]){?>
<TD class="tabpoolItemInactive lmb-tabpoolItemnohover" title="<?=$lang[2854]?>"><i class="lmb-icon lmb-calendar-alt2"></i></TD>
<TD id="tzone_resourceMonth" style="cursor:pointer;" onclick="lmb_calView('resourceMonth');" <?php if($cal_viewmode=='resourceMonth'){echo "class=\"tabpoolItemActive\" ";}?>  class="tabpoolItemInactive"><?=$lang[1437];?></TD>
<TD id="tzone_resourceWeek" style="cursor:pointer;" onclick="lmb_calView('resourceWeek');" <?php if($cal_viewmode=='resourceWeek'){echo "class=\"tabpoolItemActive\" ";}?>  class="tabpoolItemInactive"><?=$lang[1436];?></TD>
<TD id="tzone_resourceDay" style="cursor:pointer;" onclick="lmb_calView('resourceDay');" <?php if($cal_viewmode=='resourceDay'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1435];?></TD>
<TD>&nbsp;&nbsp;&nbsp;</TD>
<?php }else{?>

<TD title="<?=$lang[2853]?>" class="tabpoolItemInactive lmb-tabpoolItemnohover"><i class="lmb-icon-cus lmb-cal-day"></i></TD>
<TD id="tzone_month" style="cursor:pointer; margin-right:-5px" value="<?=$lang[1437];?>" onclick="lmb_calView('month');" <?php if($cal_viewmode=='month'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1437];?></TD>
<TD id="tzone_agendaWeek" style="cursor:pointer; margin-right:-5px" value="<?=$lang[1436];?>" onclick="lmb_calView('agendaWeek');" <?php if($cal_viewmode=='agendaWeek'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1436];?></TD>
<TD id="tzone_agendaDay" style="cursor:pointer; margin-right:-5px" value="<?=$lang[1435];?>" onclick="lmb_calView('agendaDay');" <?php if($cal_viewmode=='agendaDay'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1435];?></TD>
<TD>&nbsp;&nbsp;&nbsp;</TD>
<TD title="Basic" class="tabpoolItemInactive lmb-tabpoolItemnohover"><i class="lmb-icon-cus lmb-cal-week"></TD>
<TD id="tzone_basicWeek" style="cursor:pointer; margin-right:-5px" value="<?=$lang[1436];?>" onclick="lmb_calView('basicWeek');" <?php if($cal_viewmode=='basicWeek'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1436];?></TD>
<TD id="tzone_basicDay" style="cursor:pointer; margin-right:-5px" value="<?=$lang[1435];?>" onclick="lmb_calView('basicDay');" <?php if($cal_viewmode=='basicDay'){echo "class=\"tabpoolItemActive\" ";}?> class="tabpoolItemInactive"><?=$lang[1435];?></TD>
<!--<TD width="100%" align="right"><div style="padding-right:10px;font-size:1.3em;font-weight:bold;" id="current_date"></div></TD>-->
<?php }?>

</tr></table>
</div>
</td></tr>


<tr><td valign="top">
<div id='calendar' style="overflow:auto;margin:0px;"></div>
</td></tr>

</table>
</div>

</td></tr></table>

</div>

</form>



<div id="lmb_eventDetailFrame" style="position:absolute;display:none;z-index:9999;overflow:hidden;width:300px;height:300px;padding:0px;">
<iframe id="lmb_detailFrame" style="width:100%;height:100%;overflow:auto;"></iframe>
</div>

</tr></td>
</table>
</div>

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;" OnClick="activ_menu=1;"></div>
<div id="limbasAjaxGtabContainer" class="ajax_container" style="padding:1px;position:absolute;display:none;" onclick="activ_menu=1;"></div>
<div id="lmbCalAjaxContainer" class="ajax_container" style="position:absolute;display:none;z-index:2003"></div>

<div id="lmbAjaxContextmenu" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;" OnClick="activ_menu=1;">
<?php
pop_top();
if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid]){pop_menu(0,'lmb_calCut(activeDiv,activeEvent)',$lang[2666],'','','lmb-icon-cus lmb-page-cut');}   # auschneiden
if($gtab["edit"][$gtabid]){pop_menu(0,'lmb_calCopy(activeDiv,activeEvent)',$lang[817],'','','lmb-page-copy');}        # kopieren
if($gtab["delete"][$gtabid]){pop_menu(0,"lmb_calDelete(event,$gtabid,activeEvent.id);",$lang[160],'','','lmb-icon-cus lmb-page-delete-fancy');}		# löschen
pop_bottom();
?>
</div>

<div id="lmbAjaxContextPaste" class="lmbContextMenu" style="position:absolute;display:none;z-index:2004;" OnClick="activ_menu=1;">
<?php
pop_top();
if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid]){
    echo '<div id="lmb_eventPaste">';
    pop_menu(0,"lmb_calPaste(activeDate, activeResource)",$lang[2667],'','','lmb-paste');
    echo '</div>';
}   # einfügen 
pop_bottom();
?>
</div>









<form action="main.php" method="post" name="form1" id ="form1" autocomplete="off">
<input type="hidden" id="eventID" name="ID">
<input type="hidden" name="history_fields">
<input type="hidden" name="action" value="<?=$action?>">
<input type="hidden" name="gtabid" value="<?=$gtabid?>">
<input type="hidden" name="history_search">
<input type="hidden" name="change_ok">

</form>
