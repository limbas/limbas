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
 * ID: 5
 */

# EXTENSIONS
if($GLOBALS["gLmbExt"]["ext_gtab_erg.inc"]){
	foreach ($GLOBALS["gLmbExt"]["ext_gtab_erg.inc"] as $key => $extfile){
		require_once($extfile);
	}
}

?>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="limbasAjaxGtabContainer" class="ajax_container" style="padding:1px;position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="limbasDivMenuContext" class="lmbContextMenu" style="display:none;z-index:994;" OnClick="activ_menu = 1;">
<?php

# check if is view
if($gtab["typ"][$gtabid] == 5){$isview = 1;}

#----------------- Kontext-Menü -------------------
pop_top('limbasDivMenuContext');
if(!$isview){
	pop_submenu(9,'','');								# Infomenü
	if($gtab["logging"][$gtabid]){
		pop_submenu(173,'','');							# History
	}
}

if($gtab["tab_view_lform"][$gtabid]){
    pop_menu(6,'view_contextDetail();return;');										# Detail
}

if($gtab["edit"][$gtabid]){pop_menu(3,'','');}			# bearbeiten
if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid] AND !$readonly){pop_menu(201,'','');}			# Datensatz kopieren
if($verknpf AND $gtab["edit"][$gtabid] AND !$readonly){
if($verkn_showonly){
    pop_menu(158,'','');								# Verknüpfung anlegen
}else{
    pop_menu(157,'','');								# Verknüpfung entfernen
}}
if($gtab["ver"][$gtabid] == 1 AND $gtab["add"][$gtabid] AND !$readonly){
	pop_menu(235,'','');								# Datensatz versionieren
}
pop_line();
pop_menu(288,'','');									# neues Fenster
pop_submenu(140,'','');									# Hintergrundmenü
pop_line();
if($gtab["hide"][$gtabid] AND !$readonly){
if($filter["unhide"][$gtabid]){
    pop_menu(166,'','');								# Papierkorb
}else{
    pop_menu(164,'','');								# Papierkorb
}}
if($gtab["delete"][$gtabid] AND !$readonly){pop_menu(11,'','');}		# löschen

# extension
if(function_exists($GLOBALS["gLmbExt"]["menuListCContext"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuListCContext"][$gtabid]($gtabid,$form_id,$gresult);
}
pop_bottom();
?>
</DIV>


<div ID="limbasDivMenuInfo" class="lmbContextMenu" style="display:none;z-index:995" OnClick="activ_menu = 1;">
<FORM NAME="info_form">
<?php #----------------- Info-Menü -------------------2
pop_menu2('','','lmbInfoCreate',"lmb-page-new");
pop_menu2('','','lmbInfoEdit',"lmb-page-edit");
pop_line();
pop_left();
echo "&nbsp;$lang[1527]:<BR>&nbsp;<FONT COLOR=\"green\">[RETURN]</FONT><hr>";
echo "&nbsp;$lang[357]:<BR>&nbsp;<FONT COLOR=\"green\">[Mouse DblClick]</FONT><hr>";
echo "&nbsp;$lang[1529]:<BR>&nbsp;<FONT COLOR=\"green\">[SHIFT][Mouse DblClick]</FONT><hr>";
echo "&nbsp;$lang[2821]:<BR>&nbsp;<FONT COLOR=\"green\">[CTRL][Right Mouse Click]</FONT>";
pop_right();
# extension
if(function_exists($GLOBALS["gLmbExt"]["menuListCInfo"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuListCInfo"][$gtabid]($gtabid,$form_id,$gresult);
}
pop_bottom();
?>
</FORM></div>


<div ID="limbasDivMenuRahmen" class="lmbContextMenu" style="display:none;z-index:993" OnClick="activ_menu = 1;">
<FORM NAME="frame_form">
<?php #----------------- Frameset-Menü -------------------
unset($opt);
pop_top('limbasDivMenuRahmen');
$opt["val"] = array("1","2","3");
$opt["desc"] = array($lang[1246],$lang[1244],$lang[1245]);
$zl = "document.form1.target='gtabframe';document.form1.filter_frame_type.value=this[this.selectedIndex].value;send_form(1);";
pop_select($zl,$opt,$filter["frame_type"][$gtabid],1,'','','');
pop_bottom();
?>
</FORM></div>

<div ID="limbasDivMenuFarb" class="lmbContextMenu" style="display:none;z-index:995;" OnClick="activ_menu = 1;">
<FORM NAME="fcolor_form">
<?php #----------------- Farb-Menü -------------------
unset($opt);
pop_top('limbasDivMenuFarb');
pop_color(null, null, 'limbasDivMenuFarb');
pop_left();
if($gtab["linecolor"][$gtabid]){
	echo "
	<input type=\"radio\" name=\"ctyp\" value=\"1\" style=\"border:none;background-color:transparent;\" checked>&nbsp;$lang[546]&nbsp;&nbsp;
	<input type=\"radio\" name=\"ctyp\" value=\"2\" style=\"border:none;background-color:transparent;\">&nbsp;$lang[851]";
}

pop_right();
pop_bottom();
?>
</FORM></div>


<DIV ID="limbasDivMenuBericht" class="lmbContextMenu" style="display:none;z-index:993;" OnClick="activ_menu = 1;">
<?php #----------------- Berichts-Menü -------------------
pop_top('limbasDivMenuBericht');

if($greportlist[$gtabid]["id"]){
	foreach($greportlist[$gtabid]["id"] as $key => $reportid){
		if($greportlist[$gtabid]["hidden"][$key]){continue;}

        $resolvedTemplateGroups = '';
        if ($greportlist[$gtabid]["is_template"][$key]) {
            $resolvedTemplateGroups = htmlspecialchars($greportlist[$gtabid]["saved_template"][$key], ENT_QUOTES);
            $reportid = $greportlist[$gtabid]["parent_id"][$key];
        }

        $preview = ($greportlist[$gtabid]["preview"][$key] == 1 || $greportlist[$gtabid]["preview"][$key] == 3) ? 1 : 0;
        
        $zl = "limbasReportMenuHandler($preview,event,this,'$gtabid','$reportid','0','','{$greportlist[$gtabid]['listmode'][$key]}', null, null, null, $resolvedTemplateGroups)";
		
		pop_submenu2($greportlist[$gtabid]["name"][$key],$zl,"","","lmb-icon lmb-".$greportlist[$gtabid]["defformat"][$key]);
		$greportlist_exist = 1;
	}
}
# extension
if(function_exists($GLOBALS["gLmbExt"]["menuListCReport"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuListCReport"][$gtabid]($gtabid,$form_id,$gresult);
	$greportlist_exist = 1;
}
pop_bottom();
?>
</DIV>

<DIV ID="limbasDivMenuReportOption" class="lmbContextMenu" style="display:none;z-index:993;" OnClick="activ_menu = 1;"></DIV>


<div ID="limbasDivMenuDiagramme" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- Menü - bearbeiten -------------------
if($gdiaglist[$gtabid]["id"]){
	pop_top('limbasDivMenuDiagramme');
	foreach($gdiaglist[$gtabid]["id"] as $key => $value){
		if($gdiaglist[$gtabid]["hidden"][$key]){continue;}
		pop_submenu2($gdiaglist[$gtabid]["name"][$key],"lmbDiagramm('".$gdiaglist[$gtabid]["id"][$key]."','$gtabid');divclose();","","","lmb-icon lmb-line-chart");
		$gdiaglist_exist = 1;
	}
	pop_bottom();
}
?>
</div>

<div ID="limbasDivMenuExport" class="lmbContextMenu" style="display:none;z-index:994" OnClick="activ_menu = 1;">
<FORM NAME="export_form">
<?php #----------------- Export-Menü -------------------
unset($opt);
pop_top('limbasDivMenuExport');
$opt['val'] = array("1","3","2");
$opt['desc'] = array($lang[2016],'CSV',$lang[2017]);
pop_select('',$opt,'',1,'lmbFormExportTyp',$lang[2502]."&nbsp",'');
pop_line();
$zl = "divclose();this.checked='';gtab_export(1,document.export_form.lmbFormExportTyp[document.export_form.lmbFormExportTyp.selectedIndex].value);";
pop_menu2($lang[1342], null, null, null, null, $zl);
$zl = "divclose();this.checked='';gtab_export(2,document.export_form.lmbFormExportTyp[document.export_form.lmbFormExportTyp.selectedIndex].value);";
pop_menu2($lang[994], null, null, null, null, $zl);
pop_bottom();
?>
</FORM></div>



<div ID="limbasDivMenuDatei" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- Menü - Datei -------------------
$noLine = true;
if($gtab["edit"][$gtabid]){pop_menu(197,'','',0,1);$noLine=false;}	# speichern
if($gtab["add"][$gtabid] AND !$readonly){pop_menu(1,'','',0,0);$noLine=false;}		# neuer Datensatz
if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid] AND !$readonly){pop_menu(201,'','');$noLine=false;}			# Datensatz kopieren
if($verknpf){
if($verkn_showonly){
    pop_menu(158,'','');								# Verknüpfung anlegen
}else{
    pop_menu(157,'','');								# Verknüpfung entfernen
}
$noLine=false;
}
if($gtab["ver"][$gtabid] == 1 AND $gtab["add"][$gtabid] AND !$readonly){
	pop_menu(235,'','',0);								# Datensatz versionieren
        $noLine=false;        
}
if(!$noLine){
    pop_line();
}
pop_menu(23,'','',0,1);									# drucken

if($gtab["lock"][$gtabid] AND !$readonly){
	if($filter["locked"][$gtabid]){
		pop_submenu(271,'','',1);						# entsperren
	}else{
		pop_submenu(270,'','',1); 						# sperren
	}
}

if($gtab["hide"][$gtabid] AND !$readonly){
if($filter["unhide"][$gtabid]){
    pop_menu(166,'','',0,0);							# wieder herstellen
}else{
    pop_menu(164,'','',0,0);							# Papierkorb
}}
if($gtab["delete"][$gtabid] AND !$readonly){pop_menu(11,'','',0,0);}	# löschen

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][3]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][3]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}

pop_bottom();
?>
</div>


<div ID="limbasDivMenuBearbeiten" class="lmbContextMenu" style="display:none;z-index:992;" OnClick="activ_menu=1;">
<?php #----------------- Menü - bearbeiten -------------------
pop_submenu(14,'','',0);											# suchen
pop_menu(28,'','',0,1); 										# zurücksetzen
pop_menu(236,'','',0,1);
if($gtab["edit"][$gtabid]){
if($LINK[161] AND $LINK[3] AND $filter["alter"][$gtabid]){ # Liste bearbeiten
	pop_menu(161,'','',1,0);
}elseif($LINK[161] AND $LINK[10]){
	pop_menu(161,'','',0,0);
}
}

if($gfield[$gtabid]["collreplace"] AND $gtab["edit"][$gtabid]){
	pop_menu(287,'','',0,1);									# Sammeländerungen
}

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][4]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][4]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}

pop_bottom();
?>
</div>

<div ID="limbasDivMenuAnsicht" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- Menü - Ansicht -------------------
pop_menu(240,'','',$session["symbolbar"],1);	# Symbolleiste
if(!$filter["alter"][$gtabid]){
	pop_menu(160,'',''); 	# Tabellengröße
	pop_line();
}

if($LINK[286]){
	pop_submenu(286,'','',0);									# zeige Felder
	pop_line();
}

#pop_submenu(159,'','',1);									# Ramenbreiten
if($verknpf == 1){
	pop_menu(243,'','',$verkn["showonly"],0);				# zeige verknüpfte
}
if($gtab["viewver"][$gtabid]){
	pop_menu(237,'','',$filter["viewversion"][$gtabid],0);	# zeige versionierte
}

if($gtab["validity"][$gtabid]){
	pop_submenu(312,'','',0);	# zeige valide
}

if($gtab["sverkn"][$gtabid] and $verkn){
	pop_menu(261,'','',$filter["nosverkn"][$gtabid],0);		# zeige selbstverknüpfte
}

pop_menu(165,'','',$filter["unhide"][$gtabid],0);			# zeige gelöschte
if($gtab["lockable"][$gtabid]){
	pop_menu(273,'','',$filter["locked"][$gtabid],0);			# zeige gesperrte
	pop_menu(233,'','',$filter["hidelocked"][$gtabid],0);		# verstecke gesperrte
}

if($gtab["multitenant"][$gtabid] AND count($lmmultitenants['mid']) > 1){
    pop_menu(309,'','',$filter["multitenant"][$gtabid],0);			# zeige Mandanten
}

if($gtab["edit_userrules"][$gtabid] OR $gtab["edit_ownuserrules"][$gtabid]){ # Benutzerrechte	
	pop_menu(267,'','',$filter["userrules"][$gtabid],0);
}

if($LINK[275] AND $gfield[$gtabid]["aggregate"]){
	pop_line();
	pop_menu(275,'','',$filter["show_sum"][$gtabid],0);			# zeige summe
}

if($gfrist AND !$isview AND $LINK[109]){                                    # Wiedervorlage filtern
	pop_menu2($lang[2899],null,null,"lmb-icon lmb-filter",null,"limbasDivShowReminderFilter(event,this);");
}

# extension
if(function_exists($GLOBALS["gLmbExt"]["menuListCDisplay"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuListCDisplay"][$gtabid]($gtabid,$form_id,$gresult);
}

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][5]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][5]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}
	
pop_bottom();
?>
</div>

<div ID="limbasDivMenuExtras" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- Menü - Extras -------------------
#pop_submenu(132,'','');			# Formulare
if($LINK[131] AND $GLOBALS["greportlist_exist"] AND ($LINK[175] OR $LINK[176] OR $LINK[315])){
	pop_submenu(131,'','',0);			# Berichte
    if ($LINK[315]) {
        pop_submenu(315,'','',0);
    }
}
if($gdiaglist[$gtabid]["id"]){
	pop_submenu(232,'','',0);			# Diagramme
	pop_line();
}

pop_submenu(188,'','',0); 			# Schnapschuß


pop_submenu(7,'','',1); 				# export
if($gtab["ver"][$gtabid] AND $LINK[242]){
	pop_submenu2($lang[$LINK['name'][242]],"lmb_datepicker(event,this,'view_version_status',document.form1.view_version_status.value,'".dateStringToDatepicker(setDateFormat(1,1))."',null,'lmb_version_status')",$lang[$LINK['desc'][242]],null,$LINK['icon_url'][242]);
	#pop_submenu(242,'','',1); 			# Versionsstand
}
if(!$isview){
	pop_submenu(109,'','',1);		    # Wiedervorlage
}
if($gtab["edit_userrules"][$gtabid] OR $gtab["edit_ownuserrules"][$gtabid]){ # Benutzerrechte	
	pop_submenu(266,'','',0);
}
if($LINK[277]){
pop_line();
pop_submenu(277,'','');					# Einstellungen
}

# extension
if(function_exists($GLOBALS["gLmbExt"]["menuListCExtras"][$gtabid])){
	$GLOBALS["gLmbExt"]["menuListCExtras"][$gtabid]($gtabid,$form_id,$gresult);
}

// custmenu
if($GLOBALS['gcustmenu'][$gtabid][6]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][6]['id'] as $cmkey => $cmid){
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
    }
}
	
pop_bottom();
?>
</div>


<?php
// extended custmenu
if($GLOBALS['gcustmenu'][$gtabid][2]['id'][0]){
    foreach($GLOBALS['gcustmenu'][$gtabid][2]['id'] as $cmkey => $cmid){
        echo "<DIV ID=\"limbasDivCustMenu_$cmid\" class=\"lmbContextMenu\" style=\"display:none;z-index:993\" OnClick=\"activ_menu = 1;\">";
        lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
        echo "</div>";
    }
}
?>

<DIV ID="limbasDivMenuValidity" class="lmbContextMenu" style="display:none;z-index:993" OnClick="activ_menu = 1;">
<FORM NAME="formular_Validity" onsubmit="document.form1.filter_validity.value=this.lmbValidity.value;send_form(1);return false;">
<?php
pop_top('limbasDivMenuValidity');
pop_left(null,'lmb-icon lmb-calendar');
$dateformat = setDateFormat(1,2);
$transl = array('all'=>$lang[3052],'allto'=>$lang[3054],'allfrom'=>$lang[3055]);
$validityval = $filter["validity"][$gtabid];
if($transl[$filter["validity"][$gtabid]]){$validityval = $transl[$filter["validity"][$gtabid]];}
echo "<input type=\"text\" maxlength=\"20\" style=\"width:170px;\" value=\"".$validityval."\" name=\"lmbValidity\" id=\"lmbValidity\" onclick=\"activ_menu=1;\" onchange=\"document.form1.filter_validity.value=this.value;send_form(1);return;\" ondblclick=\"lmb_datepicker(event,this,'lmbValidity',document.getElementById('lmbValidity').value,'$dateformat',10);\">
<i class=\"lmbContextRight lmb-icon lmb-caret-right-alt\" border=\"0\" onclick=\"lmb_datepicker(event,this,'lmbValidity',document.getElementById('lmbValidity').value,'$dateformat',10);\"></i>";
pop_right();

${'fvalidity_'.$filter["validity"][$gtabid]} = 1;
pop_menu(0, "document.form1.filter_validity.value='all';send_form(1);return;", $lang[3052], $fvalidity_all, 0);
pop_menu(0, "document.form1.filter_validity.value='allto';send_form(1);return;", $lang[3054], $fvalidity_allto, 0);
pop_menu(0, "document.form1.filter_validity.value='allfrom';send_form(1);return;", $lang[3055], $fvalidity_allfrom, 0);

pop_bottom();
?>
</FORM>
</DIV>

<DIV ID="limbasDivMenuSettings" class="lmbContextMenu" style="display:none;z-index:993" OnClick="activ_menu = 1;">
<?php
pop_top('limbasDivMenuSettings');
pop_menu(212,'','',$filter["nolimit"][$gtabid],0);			# Limit aufheben
if(!$isview AND $gtab["delete"][$gtabid]){
	if($filter["force_delete"][$gtabid]){$checked = "checked";}else{$checked = null;}
	pop_checkbox(276,"document.form1.filter_force_delete.value='1';","filter_force_delete",1,$checked,0); # rekursives Löschen forcieren
}
pop_bottom();
?>
</DIV>

<DIV ID="limbasDivMenuLock" class="lmbContextMenu" style="position: absolute;display:none;z-index:992;" OnClick="activ_menu = 1;">
<FORM NAME="formular_lock">
<?php #----------------- Verknüpfungs-Menü -------------------
pop_top('limbasDivMenuLock');
pop_left();
echo "&nbsp;&nbsp;<input type=\"text\" maxlength=\"2\" style=\"width:30px;\" value=\"30\" id=\"lmbLockTime\">&nbsp;&nbsp;&nbsp;<select id=\"lmbLockUnit\" class=\"contextmenu\"><option value=\"m\">".$lang[1980]."<option value=\"h\">".$lang[1981]."<option value=\"d\">".$lang[1982]."</select>&nbsp;&nbsp;<input type=\"button\" value=\"".$lang[1218]."\" ID=\"LmbIconDataLock\" OnClick=\"document.form1.lockingtime.value=document.getElementById('lmbLockTime').value+'|'+document.getElementById('lmbLockUnit').value;userecord('lock');\">";
pop_right();
pop_bottom();
?>
</FORM></DIV>



<div ID="limbasDivMenuSnapshot" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- SubMenü - Schnapschuß -------------------
pop_top("limbasDivMenuSnapshot");

# save
if ($snap_id AND ($gsnap[$gtabid]["owner"][$snap_id] OR $gsnap[$gtabid]["edit"][$snap_id])){
	pop_menu(0,"limbasSnapshotSave()",$lang[842], null, null, 'lmb-save');
}
# save as
if ($LINK[188]){
	//pop_submenu(0,"limbasDivShow(this,'limbasDivMenuSnapshot','limbasDivSnapshotSaveas')",$lang[1998]);
        pop_submenu2($lang[1998],"limbasDivShow(this,'limbasDivMenuSnapshot','limbasDivSnapshotSaveas')", null, null, 'lmb-icon lmb-save');

}
# share
if ($snap_id AND $gsnap[$gtabid]["owner"][$snap_id] AND $LINK[225]){
	pop_submenu(0,"limbasSnapshotShare(this,$snap_id,'')",$lang[1996]);
}
# administrate
pop_menu(189,"",$lang[2000]);
# delete
if ($snap_id AND ($gsnap[$gtabid]["owner"][$snap_id] OR $gsnap[$gtabid]["del"][$snap_id])){
	pop_menu(0,"limbasSnapshotDelete();",$lang[160]);
}
# list of snapshots
if($gsnap[$gtabid]){
	pop_line();
	foreach($gsnap[$gtabid]["id"] as $key => $value){
		$zl = "document.form1.snap_id.value=$key;send_form(1);";
		pop_menu2($gsnap[$gtabid]["name"][$key], null, null, null, null, $zl);
	}
}
pop_bottom();

?>
</div>


<div ID="limbasDivSnapshotSaveas" class="lmbContextMenu" style="display:none;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- SubMenü - Schnapschuß -------------------

pop_top("limbasDivSnapshotSaveas");
pop_input2('limbasSnapshotSaveas();', 'limbasSnapshotName', '', '', $lang[4] . ':');
?>
</div>



<div ID="limbasDivMenuFields" class="lmbContextMenu" style="display:none;top:0px;z-index:992" OnClick="activ_menu = 1;">
<?php #----------------- SubMenü - Schnapschuß -------------------

pop_top("limbasDivMenuFields");

$header = false;
foreach ($gfield[$gtabid]['sort'] as $key => $value){
    
    if($gfield[$gtabid]['field_type'][$key] == 100 /* sparte */){
        pop_header(null, $gfield[$gtabid]['spelling'][$key], 'lmbShowSubColumns(this);');
        $header = true;
    } else if ($gfield[$gtabid]['field_type'][$key] != 101 /* gruppierung */){
        if (!$header) {
            # pop default header "Allgemein"
            pop_header(null, $lang[1634], 'lmbShowSubColumns(this);');
            $header = true;
        }
        if ($filter["hidecols"][$gtabid][$key]) {
            $color = "black";
            $icdis = "hidden";
        } else {
            $color = "green";
            $icdis = "";
        }
        pop_menu2(
            $gfield[$gtabid]["spelling"][$key],
            $gfield[$gtabid]["spelling"][$key],
            "lmbViewFieldItem_" . $gtabid . "_" . $key . "\" style=\"color:$color;cursor:pointer;",
            "lmb-icon lmb-check\" style=\"visibility: $icdis",
            null,
            "lmbShowColumn($gtabid,$key);"
        );
    }
}
pop_bottom();
?>
</div>



<DIV ID="limbasDivRowSetting" class="lmbContextMenu" style="display:none;z-index:993" OnClick="activ_menu = 1;">
<?php #----------------- SubMenü - Rowsetting -------------------
pop_top('limbasDivRowSetting');
pop_menu2($lang[861],null,null,"lmb-textsort-up",null,"lmbFieldSort(event,lmbGlobVar['sortid'],'&ASC',lmbGlobVar['res_next'])");
pop_menu2($lang[862],null,null,"lmb-textsort-down",null,"lmbFieldSort(event,lmbGlobVar['sortid'],'&DESC',lmbGlobVar['res_next'])");

// replace
if($gfield[$gtabid]["collreplace"] AND $gtab["edit"][$gtabid]){
    pop_line();
	pop_menu(287,'','',0,1);
}
 // hide row
if(!$form_id){
    pop_line();
    pop_menu2($lang[2733],null,null,"lmb-hide-col",null,"lmbShowColumn(lmbGlobVar['ActiveTab'],lmbGlobVar['ActiveRow']);divclose();");
}

pop_bottom();
?>
</DIV>

<?php if($gfrist){?>
<div ID="limbasDivMenuReminder" class="lmbContextMenu" style="display:none;z-index:995" OnClick="activ_menu = 1;">
<FORM NAME="reminder_form">
<?php #----------------- Reminder-Menü -------------------
pop_header(null,$lang[425],$elementWidth);
pop_left();
echo "<table>
<tr><td>".$lang[1445]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_from.value=this.value+' ';checktyp(11,'lmb_ReminderFilterFrom','','','',this.value)\" name=\"lmb_ReminderFilterFrom\" value=\"".$filter['reminder_from'][$gfrist]."\" MAX=16>
<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" onclick=\"lmb_datepicker(event,this,'lmb_ReminderFilterFrom',this.previousSibling.value,'".dateStringToDatepicker(setDateFormat(0,1))."',10);\"></i></td>
</tr>
<tr><td>".$lang[1446]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_to.value=this.value+' ';checktyp(11,'lmb_ReminderFilterTo','','','',this.value)\" name=\"lmb_ReminderFilterTo\" value=\"".$filter['reminder_to'][$gfrist]."\" MAX=16>
<i class=\"lmb-icon lmb-edit-caret\" style=\"cursor:pointer\" onclick=\"lmb_datepicker(event,this,'lmb_ReminderFilterTo',this.previousSibling.value,'".dateStringToDatepicker(setDateFormat(0,1))."',10);\"></i></td>
</tr>
<tr><td>".$lang[3]."</td><td><input type=\"text\" onchange=\"document.form1.filter_reminder_create.value=this.value+' ';checktyp(1,'lmb_ReminderFilterCreate','','','',this.value)\" name=\"lmb_ReminderFilterCreate\" value=\"".$filter['reminder_create'][$gfrist]."\" MAX=30></td></tr>
</table>
";
pop_right();
pop_submit($lang[30]);
pop_bottom();
?>
</FORM></div>
<?php }?>

<?php /*----------------- Javascript Functionen -------------------*/?>
<Script language="JavaScript">

// ----- Js-Script-Variablen --------
jsvar["gtabid"] = "<?=$gtabid?>";
jsvar["snap_id"] = "<?=$snap_id?>";
jsvar["user_id"] = "<?=$session["user_id"]?>";
jsvar["verkn_ID"] = "<?=$verkn_ID?>";
jsvar["form_id"] = "<?=$form_id?>";
jsvar["tablename"] = "<?php if($snapid){echo $gsnap[$gtabid]["name"][$snapid];}else{echo $gtab["table"][$gtabid];}?>";
jsvar["wfl_id"] = "<?=$wfl_id?>";
jsvar["wfl_inst"] = "<?=$wfl_inst?>";
jsvar["resultspace"] = "<?=$umgvar["resultspace"]?>";
jsvar["usetablebody"] = "<?=$umgvar["usetablebody"]?>";
jsvar["action"] = "<?=$action?>";
jsvar["detail_viewmode"] = "<?=$umgvar["detail_viewmode"]?>";
jsvar["ajaxpost"] = "<?=$gtab["ajaxpost"][$gtabid]?>";
jsvar["tablename"] = "<?=$gtab["desc"][$gtabid]?>";

// --------------------- Verknüpfungszusatz Datensatz markieren ------------------------
</SCRIPT>

<?php

if($GLOBALS["greportlist_exist"] AND $LINK[315]){
    LmbReportSelect::printReportSelect($gtabid);
}

 ?>

	<?php /*-------------------------------------------- Formularkopf ----------------------------------------*/?>
	<form action="main.php" method="post" id="lmbForm2" name="form2" autocomplete="off">
	<input type="hidden" name="action">
	<input type="hidden" name="old_action" value="<?=$action?>">
	<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
	<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
	<input type="hidden" name="wfl_id" value="<?=$wfl_id;?>">
	<input type="hidden" name="wfl_inst" value="<?=$wfl_inst;?>">
	<input type="hidden" name="snap_id" value="<?=$snap_id;?>">
	<input type="hidden" name="request_gsr" value="<?=$request_gsr;?>">
	<input type="hidden" name="request_flt" value="<?=$request_flt;?>">
	<input type="hidden" name="gfrist" VALUE="<?=$gfrist;?>">
	<input type="hidden" name="formlist_id" value="<?=$form_id;?>">
	<input type="hidden" name="form_id">
	<input type="hidden" name="ID">
	<input type="hidden" name="use_record">
	<input type="hidden" name="posx">
	<input type="hidden" name="posy">
	<input type="hidden" name="funcid">
	<input type="hidden" name="verknpf" VALUE="<?= $verknpf ?>">
	<?php /* --------------------- Verknüpfungszusatz ------------------------ */?>
	<input type="hidden" name="verkn_ID" VALUE="<?= $verkn_ID ?>">
	<input type="hidden" name="verkn_add_ID" VALUE="<?= $verkn_add_ID ?>">
	<input type="hidden" name="verkn_del_ID" VALUE="<?= $verkn_del_ID ?>">
	<input type="hidden" name="verkn_tabid" VALUE="<?= $verkn_tabid ?>">
	<input type="hidden" name="verkn_fieldid" VALUE="<?= $verkn_fieldid ?>">
	<input type="hidden" name="verkn_showonly" VALUE="<?= $verkn_showonly ?>">
	<?php /*<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom?>">  todo  */?>
	<input type="hidden" name="verkn_poolid" VALUE="<?=$verkn_poolid;?>">
	<span id="myExtForms2"></span>
	</form>


	<form action="main.php" method="post" id="form1" name="form1" autocomplete="off">
	<input type="hidden" name="action" value="gtab_erg">
	<input type="hidden" name="old_action" value="<?=$action?>">
	<input type="hidden" name="gtabid" value="<?= $gtabid ?>">
	<input type="hidden" name="tab_group" value="<?= $tab_group ?>">
	<input type="hidden" name="order">
	<input type="hidden" name="select">
	<input type="hidden" name="exp_typ">
	<input type="hidden" name="exp_medium">
	<input type="hidden" name="ID">
	<input type="hidden" name="LID">
	<input type="hidden" name="posx">
	<input type="hidden" name="posy">
	<input type="hidden" name="rowsize">
	<input type="hidden" name="change">
	<input type="hidden" name="funcid">
	<input type="hidden" name="snapshot">
	<input type="hidden" name="gfrist" VALUE="<?=$gfrist;?>">
	<input type="hidden" name="form_id" value="<?=$form_id;?>">
	<input type="hidden" name="snap_id" value="<?=$snap_id;?>">
	<input type="hidden" name="wfl_id" value="<?=$wfl_id;?>">
	<input type="hidden" name="wfl_inst" value="<?=$wfl_inst;?>">
	<input type="hidden" name="limbaswfid">
	<input type="hidden" name="request_gsr" value="<?=$request_gsr;?>">
	<input type="hidden" name="request_flt" value="<?=$request_flt;?>">
	<input type="hidden" name="lockingtime">
	<input type="hidden" name="popup_links">
	<input type="hidden" name="view_symbolbar">
	<input type="hidden" name="view_version_status" value="<?=$view_version_status;?>">
	<input type="hidden" name="td_sort">
	<input type="hidden" name="use_record">
	<input type="hidden" name="use_typ">
	<input type="hidden" name="readonly" value="<?=$readonly;?>">
	
	<input type="hidden" name="filter_frame_type">
	<input type="hidden" name="filter_reset">
	<input type="hidden" name="filter_indicator">
	<input type="hidden" name="filter_popups">
	<input type="hidden" name="filter_alter">
	<input type="hidden" name="filter_unhide">
	<input type="hidden" name="filter_hidecols">
	<input type="hidden" name="filter_hidelocked">
	<input type="hidden" name="filter_locked">
	<input type="hidden" name="filter_nolimit">
	<input type="hidden" name="filter_version">
	<input type="hidden" name="filter_nosverkn">
	<input type="hidden" name="filter_userrules">
	<input type="hidden" name="filter_sum">
	<input type="hidden" name="filter_reminder_from">
	<input type="hidden" name="filter_reminder_to">
	<input type="hidden" name="filter_reminder_create">
	<input type="hidden" name="filter_force_delete">
    <input type="hidden" name="filter_tabulatorKey">
    <input type="hidden" name="filter_multitenant">
    <input type="hidden" name="filter_validity">
    <input type="hidden" name="filter_validity_all">
	<input type="hidden" name="pop_choice">
	<input type="hidden" name="grp_choice">
	
	<input type="hidden" name="history_fields">
	<input type="hidden" name="history_search">
	<input type="hidden" name="verknpf" VALUE="<?= $verknpf ?>">
	<input type="hidden" name="verkn_poolid" VALUE="<?=$verkn_poolid;?>">
	<input type="hidden" name="verkn_addfrom" VALUE="<?=$verkn_addfrom;?>">
	<?php #--------------------- Verknüpfungszusatz ------------------------
	if($verknpf){?>
	<input type="hidden" name="verkn_ID" VALUE="<?= $verkn_ID ?>">
	<input type="hidden" name="verkn_add_ID" VALUE="<?= $verkn_add_ID ?>">
	<input type="hidden" name="verkn_del_ID" VALUE="<?= $verkn_del_ID ?>">
	<input type="hidden" name="verkn_tabid" VALUE="<?= $verkn_tabid ?>">
	<input type="hidden" name="verkn_fieldid" VALUE="<?= $verkn_fieldid ?>">
	<input type="hidden" name="verkn_showonly" VALUE="<?= $verkn_showonly ?>">
	<?php }



$still_done = array();
if (!$filter["anzahl"][$gtabid]){$filter["anzahl"][$gtabid] = $session["maxresult"];}

# Layout lib
require_once("gtab/html/gtab_erg.lib");

#---- Tabellen-funktionen aufrufen -------

# get tabsize
if(!$filter["hidecols"][$gtabid][0]){
	lmbGetGtabWidth($gtabid,$filter["hidecols"][$gtabid]);
}

# ----- Formular -------
if($form_id AND $gformlist[$gtabid]["id"][$form_id] AND $gformlist[$gtabid]["typ"][$form_id] == 2){
	if($gformlist[$gtabid]["css"][$form_id]){
		echo"<style type=\"text/css\">@import url(".$gformlist[$gtabid]["css"][$form_id]."?v={$umgvar['version']});</style>\n";
	}
	form_ergview($gtabid,$gresult,$form_id);
}else{
	# ----- Gruppiert -------
	if($popg[$gtabid][0]['null']){
		foreach($popg[$gtabid][0]['null'] as $key => $value){
			if($value AND $gfield[$gtabid]["groupable"][$key]){$group_fields[] = $key;}
		}
		# --- Prüfe ob Feld ausgewählt oder auf "ohne" gesetzt
		if($group_fields){
			$gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn);
			lmbGlistOpen();
			lmbGlistTabs($gtabid,$verkn_poolid,$snap_id);
			lmbGlistMenu($gtabid);
			lmbGlistSearch($gtabid,0,0);
			lmbGlistHeader($gtabid,$gresult,0);
			table_group($gtabid,$group_fields,$filter,0,$gsr,$verkn);
			lmbGlistFooter($gtabid,$gresult,$filter);
			lmbGlistClose();
		}else{
			$usedef = 1;
			unset($popg[$gtabid]);
		}
	}else{
		$usedef = 1;
	}
	
	if($usedef){
		lmbGlistOpen();
		lmbGlistTabs($gtabid,$verkn_poolid,$snap_id);
		lmbGlistMenu($gtabid);
		
		# scrolling Header X-position
		echo "<tr><td><div id=\"GtabTableFull\" style=\"overflow-x:auto;overflow-y:hidden;\"><table cellpadding=\"0\" cellspacing=\"0\">";
		lmbGlistSearch($gtabid,$verknpf,$verkn);
		lmbGlistHeader($gtabid,$gresult,0);
		lmbGlistBody($gtabid,$gresult,0,$verkn,$verknpf,$filter,0,0,0,0,1);
		echo "</table></div></td></tr>";
		
		lmbGlistFooter($gtabid,$gresult,$filter);
		lmbGlistClose();
	}
}


# Kopierten Datensatz darstellen
if($use_typ == 'copy' AND $use_record){
$rec = explode(";",$use_record);
$rec = explode("_",$rec[0]);
echo "
<Script language=\"JavaScript\">
view_copychange(".$rec[0].",".$rec[1].");
</Script>
";
}
