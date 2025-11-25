<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\gtab\export\TableExportTypes;

?>
<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="lmbAjaxContainer2" class="ajax_container" style="position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>
<div id="limbasAjaxGtabContainer" class="ajax_container" style="padding:1px;position:absolute;display:none;z-index:999;" onclick="activ_menu=1;"></div>

<div id="limbasDivMenuContext" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:994;" OnClick="activ_menu = 1;">
    <?php

    # check if is view
    if($gtab["typ"][$gtabid] == 5){$isview = 1;}

    #----------------- Kontext-Menü -------------------
    pop_top('limbasDivMenuContext');
    if(!$isview){
        if(!$gtab["menudisplay"][$gtabid][1][9]) {
            pop_submenu(9, '', '');                                # Infomenü
        }
        if($gtab["logging"][$gtabid] && $gtab["menudisplay"][$gtabid][1][173]){
            pop_submenu(173,'','');						# History
        }
    }

    pop_menu(6);										            # Detail

    if($gtab["edit"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][3]){pop_menu(3,'','');}			# bearbeiten
    if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid] AND !$readonly AND !$gtab["menudisplay"][$gtabid][1][201]){pop_menu(201,'','');}			# Datensatz kopieren
    if($verknpf AND $gtab["edit"][$gtabid] AND !$readonly){
        if($verkn_showonly){
            if(!$gtab["menudisplay"][$gtabid][1][158]) {
                pop_menu(158, '', '');                                # Verknüpfung anlegen
            }
        }else{
            if(!$gtab["menudisplay"][$gtabid][1][157]) {
                pop_menu(157, '', '');                                # Verknüpfung entfernen
            }
        }}
    if($gtab["ver"][$gtabid] == 1 AND $gtab["add"][$gtabid] AND !$readonly AND !$gtab["menudisplay"][$gtabid][1][235]){
        pop_menu(235,'','');								# Datensatz versionieren
    }
    pop_line();
    pop_menu(288,'','');									# neues Fenster
    pop_submenu(140,'','');									# Hintergrundmenü
    pop_line();
    if($gtab["hide"][$gtabid] AND !$readonly){
        if($filter["status"][$gtabid]) {
            if(!$gtab["menudisplay"][$gtabid][1][166]) {
                pop_menu(166, '', '', 0, 0);                            # restore
            }
        }else{
            if(!$gtab["menudisplay"][$gtabid][1][313]) {
                pop_menu(313, '', '', 0, 0);                            # trash
            }
            if(!$gtab["menudisplay"][$gtabid][1][164]) {
                pop_menu(164, '', '', 0, 0);                        # archive
            }
        }
    }
    if($gtab["delete"][$gtabid] AND !$readonly AND !$gtab["menudisplay"][$gtabid][1][11]){
        pop_menu(11,'','');
    }		# löschen

    # extension
    if(function_exists($GLOBALS["gLmbExt"]["menuListCContext"][$gtabid])){
        $GLOBALS["gLmbExt"]["menuListCContext"][$gtabid]($gtabid,$form_id,$gresult);
    }
    pop_bottom();
    ?>
</DIV>


<div ID="limbasDivMenuInfo" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:995" OnClick="activ_menu = 1;">
        <?php #----------------- Info-Menü -------------------2
        pop_menu2('','','lmbInfoCreate',"lmb-page-new");
        pop_menu2('','','lmbInfoEdit',"lmb-page-edit");
        # extension
        if(function_exists($GLOBALS["gLmbExt"]["menuListCInfo"][$gtabid])){
            pop_line();
            $GLOBALS["gLmbExt"]["menuListCInfo"][$gtabid]($gtabid,$form_id,$gresult);
        }
        pop_bottom();
        ?>
    </div>


<div ID="limbasDivMenuRahmen" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993" OnClick="activ_menu = 1;">
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

<div ID="limbasDivMenuFarb" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:995;" OnClick="activ_menu = 1;">
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


<DIV ID="limbasDivMenuBericht" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993;" OnClick="activ_menu = 1;">
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
            $GLOBALS['greportlist_exist'] = 1;
        }
    }
    # extension
    if(function_exists($GLOBALS["gLmbExt"]["menuListCReport"][$gtabid])){
        $GLOBALS["gLmbExt"]["menuListCReport"][$gtabid]($gtabid,$form_id,$gresult);
        $GLOBALS['greportlist_exist'] = 1;
    }
    pop_bottom();
    ?>
</DIV>

<DIV ID="limbasDivMenuReportOption" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993;" OnClick="activ_menu = 1;"></DIV>


<div ID="limbasDivMenuDiagramme" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
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

<div id="limbasDivMenuExport" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:994" onclick="activ_menu = 1;">
    <form name="export_form">
        <?php #----------------- Export-Menü -------------------
        unset($opt);
        pop_top('limbasDivMenuExport');
        $tableExportTypesCases = TableExportTypes::cases();
        $opt['val'] = array_column($tableExportTypesCases, 'value');
        $opt['desc'] = array_column($tableExportTypesCases, 'name');
        pop_select('',$opt,'',1,'lmbFormExportTyp',$lang[2502]."&nbsp",'');
        pop_line();
        $zl = "divclose();this.checked='';gtab_export(1,document.export_form.lmbFormExportTyp[document.export_form.lmbFormExportTyp.selectedIndex].value);";
        pop_menu2($lang[441], null, null, null, null, $zl);
        pop_bottom();
        ?>
    </form>
</div>



<div ID="limbasDivMenuDatei" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- Menü - Datei -------------------
    $noLine = true;
    if($gtab["edit"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][197]){pop_menu(197,'','',0,1);$noLine=false;}	# speichern
    if($gtab["add"][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][1]){pop_menu(1,'','',0,0);$noLine=false;}		# neuer Datensatz
    if($gtab["add"][$gtabid] && $gtab["copy"][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][201]){pop_menu(201,'','');$noLine=false;}			# Datensatz kopieren
    if($verknpf){
        if($verkn_showonly){
            if(!$gtab["menudisplay"][$gtabid][1][158]) {
                pop_menu(158, '', '');                                # Verknüpfung anlegen
            }
        }else{
            if(!$gtab["menudisplay"][$gtabid][1][157]) {
                pop_menu(157, '', '');                                # Verknüpfung entfernen
            }
        }
        $noLine=false;
    }
    if($gtab["ver"][$gtabid] == 1 && $gtab["add"][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][235]){
        pop_menu(235,'','',0);								# Datensatz versionieren
        $noLine=false;
    }
    if(!$noLine){
        pop_line();
    }

    if(!$gtab["menudisplay"][$gtabid][1][23]) {
        pop_menu(23, '', '', 0, 1);                                    # drucken
    }

	if(($gtab["hide"][$gtabid] OR $gtab["trash"][$gtabid]) AND !$readonly){
        if($filter["status"][$gtabid]) {
            if(!$gtab["menudisplay"][$gtabid][1][166]) {
                pop_menu(166, '', '', 0, 0);                            # restore
            }
        }else{
            if(!$gtab["menudisplay"][$gtabid][1][313]) {
                pop_menu(313, '', '', 0, 0);                            # trash
            }
            if(!$gtab["menudisplay"][$gtabid][1][164]) {
                pop_menu(164, '', '', 0, 0);                        # archive
            }
        }
    }

    if($gtab["lock"][$gtabid] AND !$readonly){
        if($filter["locked"][$gtabid]){
            if(!$gtab["menudisplay"][$gtabid][1][271]) {
                pop_menu(271, '', '');                                    # entsperren
            }
        }else{
            if(!$gtab["menudisplay"][$gtabid][1][270]) {
                pop_submenu(270, '', '', 1);                        # sperren
            }
        }
    }

    if($gtab["delete"][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][11]){
        pop_menu(11,'','',0,0);                                 # löschen
    }

    // custmenu
    if($GLOBALS['gcustmenu'][$gtabid][3]['id'][0]){
        foreach($GLOBALS['gcustmenu'][$gtabid][3]['id'] as $cmkey => $cmid){
            lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
        }
    }

    pop_bottom();
    ?>
</div>


<div ID="limbasDivMenuBearbeiten" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992;" OnClick="activ_menu=1;">
    <?php #----------------- Menü - bearbeiten -------------------
    if(!$gtab["menudisplay"][$gtabid][1][14]) {                                        # suchen
        pop_submenu(14, '', '', 0);
    }
    if(!$gtab["menudisplay"][$gtabid][1][28]) {
        pop_menu(28,'','',0,1); 									# zurücksetzen
    }
    if(!$gtab["menudisplay"][$gtabid][1][270]) {                                        # Datensatz sperren
        pop_menu(270, '', '', 0, 1);
    }
    if(!$gtab["menudisplay"][$gtabid][1][236]) {                                        # Datensatz sperren
        pop_menu(236, '', '', 0, 1);
    }

    if($gtab["edit"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][161]){
        if($LINK[161] AND $LINK[3] AND $filter["alter"][$gtabid]){                      # Liste bearbeiten
            pop_menu(161,'','',1,0);
        }elseif($LINK[161] AND $LINK[10]){
            pop_menu(161,'','',0,0);
        }
    }

    if($gfield[$gtabid]["collreplace"] AND $gtab["edit"][$gtabid] AND !$gtab["menudisplay"][$gtabid][1][287]){ # Stapeländerung
        pop_menu(287,'','',0,1);
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

<div ID="limbasDivMenuAnsicht" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- Menü - Ansicht -------------------
    if(!$gtab["menudisplay"][$gtabid][1][240]) {
        pop_menu(240, '', '', $session["symbolbar"], 1);    # Symbolleiste
    }
    if($LINK[160] && !$filter["alter"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][160]){
        pop_menu(160,'',''); 	                                        # Tabellengröße
        pop_line();
    }

    if($LINK[286] && !$gtab["menudisplay"][$gtabid][1][286]){
        pop_submenu(286,'','',0);									# zeige Felder
        pop_line();
    }

    if($verknpf == 1 && !$gtab["menudisplay"][$gtabid][1][243]){
        pop_menu(243,'','',$verkn["showonly"],0);				# zeige verknüpfte
    }
    if($gtab["viewver"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][237]){
        pop_menu(237,'','',$filter["viewversion"][$gtabid],0);	# zeige versionierte
    }

    if($gtab["validity"][$gtabid] && !$gtab["menudisplay"][$gtabid][1][312]){
        pop_submenu(312,'','',0);	                                    # zeige valide
    }

    if($gtab["sverkn"][$gtabid] && $verkn && !$gtab["menudisplay"][$gtabid][1][261]){
        pop_menu(261,'','',$filter["nosverkn"][$gtabid],0);		# zeige selbstverknüpfte
    }

    if(!$gtab["menudisplay"][$gtabid][1][165]) {
        pop_menu(165, '', '', ($filter["status"][$gtabid] == 1 ? true : false), 0);            # zeige archivierte
    }
    if(!$gtab["menudisplay"][$gtabid][1][314]) {
        pop_menu(314, '', '', ($filter["status"][$gtabid] == 2 ? true : false), 0);            # zeige Papierkorb
    }

    if($gtab["lockable"][$gtabid]){
        if(!$gtab["menudisplay"][$gtabid][1][273]) {
            pop_menu(273, '', '', $filter["locked"][$gtabid], 0);            # zeige gesperrte
        }
        if(!$gtab["menudisplay"][$gtabid][1][233]) {
            pop_menu(233, '', '', $filter["hidelocked"][$gtabid], 0);        # verstecke gesperrte
        }
    }

    if($gtab["multitenant"][$gtabid] AND !$gtab["menudisplay"][$gtabid][1][309] AND lmb_count($lmmultitenants['mid']) > 1){
        pop_menu(309,'','',$filter["multitenant"][$gtabid],0);			# zeige Mandanten
    }

    if(!$gtab["menudisplay"][$gtabid][1][267] && ($gtab["edit_userrules"][$gtabid] OR $gtab["edit_ownuserrules"][$gtabid])){ # Benutzerrechte
        pop_menu(267,'','',$filter["userrules"][$gtabid],0);
    }

    if($LINK[275] AND $gfield[$gtabid]["aggregate"] AND !$gtab["menudisplay"][$gtabid][1][275]){
        pop_line();
        pop_menu(275,'','',$filter["show_sum"][$gtabid],0);			# zeige summe
    }

    if($gfrist AND !$isview AND !$gtab["menudisplay"][$gtabid][1][328]){                                    # Wiedervorlage filtern
        pop_menu(328,'','','',0);
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

<div ID="limbasDivMenuExtras" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- Menü - Extras -------------------

    if($GLOBALS["greportlist_exist"] AND !$gtab["menudisplay"][$gtabid][1][315] AND ($LINK[175] OR $LINK[176] OR $LINK[315])){
        #pop_submenu(131,'','',0);			# Berichte
        pop_submenu(315,'','',0);
    }
    if($gdiaglist[$gtabid]["id"] && !$gtab["menudisplay"][$gtabid][1][232]){
        pop_submenu(232,'','',0);			# Diagramme
        pop_line();
    }

    if(!$gtab["menudisplay"][$gtabid][1][188]) {
        pop_submenu(188, '', '', 0);            # Schnapschuß
    }

    if(!$gtab["menudisplay"][$gtabid][1][7]) {
        pop_submenu(7, '', '', 1);                # export
    }
    if($gtab["ver"][$gtabid] AND $LINK[242] AND !$gtab["menudisplay"][$gtabid][1][242]){
        pop_submenu2($lang[$LINK['name'][242]],"lmb_datepicker(event,this,'view_version_status',document.form1.view_version_status.value,'".dateStringToDatepicker(setDateFormat(1,1))."',null,'lmb_version_status')",$lang[$LINK['desc'][242]],null,$LINK['icon_url'][242]);
        #pop_submenu(242,'','',1); 			# Versionsstand
    }
    if(!$isview && !$gtab["menudisplay"][$gtabid][1][109]){
        pop_submenu(109,'','',1);		    # Wiedervorlage
    }
    if(($gtab["edit_userrules"][$gtabid] OR $gtab["edit_ownuserrules"][$gtabid]) AND !$gtab["menudisplay"][$gtabid][1][266]){ # Benutzerrechte
        pop_submenu(266,'','',0);
    }
    if($LINK[277] && !$gtab["menudisplay"][$gtabid][1][277]){
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

<DIV ID="limbasDivMenuValidity" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993" OnClick="activ_menu = 1;">
    <FORM NAME="formular_Validity" onsubmit="document.form1.filter_validity.value=this.lmbValidity.value;send_form(1);return false;">
        <?php
        pop_top('limbasDivMenuValidity');
        pop_left(null,'lmb-icon lmb-calendar');
        $dateformat = setDateFormat(1,2);
        $transl = array('all'=>$lang[3052],'allto'=>$lang[3055],'allfrom'=>$lang[3054]);
        $validityval = $filter["validity"][$gtabid];
        if($transl[$filter["validity"][$gtabid]]){$validityval = $transl[$filter["validity"][$gtabid]];}
        echo "<input type=\"text\" maxlength=\"20\" style=\"width:170px;\" value=\"".$validityval."\" name=\"lmbValidity\" id=\"lmbValidity\" onclick=\"activ_menu=1;\" onchange=\"document.form1.filter_validity.value=this.value;send_form(1);return;\" ondblclick=\"lmb_datepicker(event,this,'lmbValidity',document.getElementById('lmbValidity').value,'$dateformat',10);\">
<i class=\"lmbContextRight lmb-icon lmb-caret-right-alt\" border=\"0\" onclick=\"lmb_datepicker(event,this,'lmbValidity',document.getElementById('lmbValidity').value,'$dateformat',10);\"></i>";
        pop_right();

        ${'fvalidity_'.$filter["validity"][$gtabid]} = 1;
        pop_menu(0, "document.form1.filter_validity.value='all';send_form(1);return;", $lang[3052], $fvalidity_all, 0);
        pop_menu(0, "document.form1.filter_validity.value='allto';send_form(1);return;", $lang[3055], $fvalidity_allto, 0);
        pop_menu(0, "document.form1.filter_validity.value='allfrom';send_form(1);return;", $lang[3054], $fvalidity_allfrom, 0);

        pop_bottom();
        ?>
    </FORM>
</DIV>

<DIV ID="limbasDivMenuSettings" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993" OnClick="activ_menu = 1;">
    <?php
    pop_top('limbasDivMenuSettings');
    pop_menu(212,'','',$filter["nolimit"][$gtabid],0);			# Limit aufheben
    if(!$isview AND $gtab["delete"][$gtabid]){
        if($filter["force_delete"][$gtabid]){$checked = "checked";}else{$checked = null;}
        if($gtab["versioning"][$gtabid]){pop_checkbox(276,"document.form1.filter_force_delete.value='1';","filter_force_delete",1,$checked,0);} # rekursives Löschen von versionen
        pop_menu(320,'','',0,0);			# Spalten Breite übernehmen
    }
    pop_bottom();
    ?>
</DIV>

<DIV ID="limbasDivMenuLock" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="position: absolute;display:none;z-index:992;" OnClick="activ_menu = 1;">
    <FORM NAME="formular_lock">
        <?php #----------------- Verknüpfungs-Menü -------------------
        pop_top('limbasDivMenuLock');
        pop_left();
        echo "&nbsp;&nbsp;<input type=\"text\" maxlength=\"2\" style=\"width:30px;\" value=\"30\" id=\"lmbLockTime\">&nbsp;&nbsp;&nbsp;<select id=\"lmbLockUnit\" class=\"contextmenu\"><option value=\"m\">".$lang[1980]."<option value=\"h\">".$lang[1981]."<option value=\"d\">".$lang[1982]."</select>&nbsp;&nbsp;<input type=\"button\" value=\"".$lang[1218]."\" ID=\"LmbIconDataLock\" OnClick=\"document.form1.lockingtime.value=document.getElementById('lmbLockTime').value+'|'+document.getElementById('lmbLockUnit').value;userecord('lock');\">";
        pop_right();
        pop_bottom();
        ?>
    </FORM></DIV>



<div ID="limbasDivMenuSnapshot" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- SubMenü - Schnapschuß -------------------
    pop_top("limbasDivMenuSnapshot");

    if ($LINK[225]) {
        # save
        if ($snap_id and ($gsnap[$gtabid]["owner"][$snap_id] or $gsnap[$gtabid]["edit"][$snap_id])) {
            pop_menu(0, "limbasSnapshotSave()", $lang[842], null, null, 'lmb-save');
        }
        # save as
        pop_submenu2($lang[1998], "limbasDivShow(this,'limbasDivMenuSnapshot','limbasDivSnapshotSaveas')", null, null, 'lmb-icon lmb-save');

        # share
        if ($snap_id and $gsnap[$gtabid]["owner"][$snap_id]) {
            pop_submenu(0, "limbasSnapshotShare(this,$snap_id,'')", $lang[1996]);
        }
        # delete
        if ($snap_id AND ($gsnap[$gtabid]["owner"][$snap_id] OR $gsnap[$gtabid]["del"][$snap_id])){
            pop_menu(0,"limbasSnapshotDelete();",$lang[160]);
        }
    }

    # administrate
    pop_menu(189, "", $lang[2000]);

    # list of snapshots
    if($gsnap[$gtabid] AND $LINK[188]){
        pop_line();
        $filterlist = SNAP_get_filtergroup($gtabid);
        foreach($filterlist as $key => $value){
            $sg = $gsnap[$gtabid]['group'][$key];
            $sgname = $gsnapgroup['name'][$sg];
            if($sgp != $sg){
                pop_group($GLOBALS['gsnapgroup']['name'][ $gsnap[$gtabid]['group'][$key]]);
            }
            $sgp = $sg;
            $zl = "document.form1.snap_id.value=$key;send_form(1);";
            pop_menu2($gsnap[$gtabid]["name"][$key], null, null, null, null, $zl);
        }
    }
    pop_bottom();

    ?>
</div>


<div ID="limbasDivSnapshotSaveas" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- SubMenü - Schnapschuß -------------------

    pop_top("limbasDivSnapshotSaveas");
    pop_input2("", "limbasSnapshotName", '', '', '');
    pop_menu(0,"limbasSnapshotSaveas(jsvar['gtabid'],document.getElementById('limbasSnapshotName').value);",$lang[842], null, null, 'lmb-save');
    ?>
</div>



<div ID="limbasDivMenuFields" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;top:0px;z-index:992" OnClick="activ_menu = 1;">
    <?php #----------------- SubMenü - Schnapschuß -------------------

    pop_top("limbasDivMenuFields");

    $header = false;


    pop_left();
    ?>
    <i class="lmbContextLeft lmb-icon lmb-icon lmb-eye" style="float:right" border="0" onclick="lmbShowColumn(<?=$gtabid?>,'all')"></i>
    <i class="lmbContextLeft lmb-icon lmb-icon lmb-rep-hidden" style="float:right" border="0" onclick="lmbShowColumn(<?=$gtabid?>,'none')"></i>
    <?php
    pop_right();

    foreach ($gfield[$gtabid]['key'] as $key => $value){

        if($gfield[$gtabid]["col_hide"][$key]){continue;}

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
                "lmbViewFieldItem_" . $gtabid . "_" . $key . "\" data-gtabid=\"$gtabid\" data-fieldid=\"$key\" style=\"color:$color;cursor:pointer;",
                "lmb-icon lmb-check\" style=\"visibility: $icdis",
                null,
                "lmbShowColumn($gtabid,$key);"
            );
        }
    }
    pop_bottom();
    ?>
</div>



<DIV ID="limbasDivRowSetting" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993" OnClick="activ_menu = 1;">
    <?php #----------------- SubMenü - Rowsetting -------------------
    pop_top('limbasDivRowSetting');
    pop_menu2($lang[861],null,null,"lmb-textsort-up",null,"lmbFieldSort(event,lmbGlobVar['sortid'],'&ASC')");
    pop_menu2($lang[862],null,null,"lmb-textsort-down",null,"lmbFieldSort(event,lmbGlobVar['sortid'],'&DESC')");

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
