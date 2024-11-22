<?php

global $LINK;
global $gfield;
global $gtab;
global $lang;
global $session;
global $action;
global $gformlist;
global $greportlist;
global $gdiaglist;
global $filter;
global $umgvar;
global $farbschema;
global $userdat;

# check if is view
if($gtab["typ"][$gtabid] == 5){$isview = 1;}
$mwidth = 305;

require_once(COREPATH  . 'gtab/html/contextmenus/gtab_filter.php');

?>

<DIV ID="limbasDivMenuDateien" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position:absolute;display:none;z-index:995;"><FORM NAME="filemenu" OnClick="activ_menu = 1;">
        <?php #----------------- Datei-Menü -------------------
        pop_top('limbasDivMenuDateien');
        foreach ($gfield[$gtabid]["id"] as $key => $value){
            if($gfield[$gtabid]["data_type"][$key] == 25 OR $gfield[$gtabid]["data_type"][$key] == 13){
                pop_submenu2($gfield[$gtabid]["spelling"][$key],"newwin12('".$gfield[$gtabid]["file_level"][$key]."','$ID','$gtabid','$key')","","","lmb-icon lmb-folder-open");
                $GLOBALS["filelist_exist"] = 1;
            }
        }
        pop_bottom();
        ?>
    </FORM></DIV>


<DIV ID="limbasDivMenuBericht" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993;" OnClick="activ_menu = 1;">
    <?php #----------------- Berichts-Menü -------------------
    pop_top('limbasDivMenuBericht');

    if($greportlist[$gtabid]["id"]){
        foreach($greportlist[$gtabid]["id"] as $key => $reportid){
            if($greportlist[$gtabid]["hidden"][$key] OR $greportlist[$gtabid]["listmode"][$key]){continue;}

            $preview = ($greportlist[$gtabid]["preview"][$key] == 1 || $greportlist[$gtabid]["preview"][$key] == 3) ? 1 : 0;
            //report is a saved template
            if ($greportlist[$gtabid]["is_template"][$key]) {
                $zl = "limbasReportMenuHandler($preview,event,this,'$gtabid','$reportid','$ID',null,'{$greportlist[$gtabid]["listmode"][$key]}', null, null, null, '".htmlspecialchars($greportlist[$gtabid]["template"][$key], ENT_QUOTES)."');";
            } else {
                $zl = "limbasReportMenuHandler($preview,event,this,'$gtabid','$reportid','$ID',null,'{$greportlist[$gtabid]["listmode"][$key]}');";
            }

            pop_submenu2($greportlist[$gtabid]["name"][$key],$zl,"","","lmb-icon lmb-".$greportlist[$gtabid]["defformat"][$key]);
            $GLOBALS["greportlist_exist"] = 1;
        }
    }
    # extension
    if(function_exists($GLOBALS["gLmbExt"]["menuChangeCReport"][$gtabid])){
        $GLOBALS["gLmbExt"]["menuChangeCReport"][$gtabid]($gtabid,$form_id,$ID,$gresult);
        $GLOBALS["greportlist_exist"] = 1;
    }
    pop_bottom();
    ?>
</DIV>

<DIV ID="limbasDivMenuReportOption" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993;" OnClick="activ_menu = 1;"></DIV>

<DIV ID="limbasDivMenuFormulare" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position: absolute;display:none;z-index:994;" OnClick="activ_menu = 1;">
    <?php #----------------- Formular-Menü -------------------
    pop_top('limbasDivMenuFormulare');
    pop_menu2($lang[2503], null, null, "lmb-icon lmb-icon-cus lmb-form", null, "document.form1.set_form.value='0';send_form('1');");
    pop_line();
    if($gformlist[$gtabid]["id"]){
        foreach($gformlist[$gtabid]["id"] as $key => $value){
            if($gformlist[$gtabid]["hidden"][$key]){continue;}
            if($gformlist[$gtabid]["typ"][$key] == 1){
                pop_menu2($gformlist[$gtabid]["name"][$key], null, null, "lmb-icon lmb-icon-cus lmb-form", null, "document.form1.set_form.value='" . $gformlist[$gtabid]["id"][$key] . "';send_form('1');");
                $GLOBALS["gformlist_exist"] = 1;
            }
        }}
    pop_bottom();
    ?>
</DIV>

<div ID="limbasDivMenuDiagramme" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position:absolute;display:none;z-index:9992" OnClick="activ_menu = 1;">
    <?php #----------------- Diagramme - Menü -------------------
    if($gdiaglist[$gtabid]["id"]){
        pop_top('limbasDivMenuDiagramme');
        foreach($gdiaglist[$gtabid]["id"] as $key => $value){
            if($gdiaglist[$gtabid]["hidden"][$key]){continue;}
            pop_submenu2($gdiaglist[$gtabid]["name"][$key],"lmbDiagramm('".$gdiaglist[$gtabid]["id"][$key]."','$gtabid');divclose();","","","lmb-icon lmb-line-chart-alt");
            $GLOBALS["gdiaglist_exist"] = 1;
        }
        pop_bottom();
    }
    ?>
</div>

<DIV ID="limbasDivMenuLink" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position: absolute;display:none;z-index:995;"><FORM NAME="formular_verknpf" OnClick="activ_menu = 1;">
        <?php #----------------- Verknüpfungs-Menü -------------------
        pop_top('limbasDivMenuLink');
        if($gfield[$gtabid]["r_verkntabid"] AND $LINK[133]){
            foreach($gfield[$gtabid]['r_verkntabid'] as $key => $value){
                pop_left();
                echo "<SPAN STYLE=\"cursor:pointer;width:100px;\" OnMouseOver=\"this.style.fontWeight='bold'\" OnMouseOut=\"this.style.fontWeight='normal'\" OnClick=\"show_verknpf('".$gfield[$gtabid]["r_verkntabid"][$key].";".$gfield[$gtabid]["r_verknfieldid"][$key]."','$ID')\">&nbsp;".$gtab['desc'][$gfield[$gtabid]['r_verkntabid'][$key]]."&nbsp;</SPAN>";
                pop_right();
                $GLOBALS["rverknpf_exist"] = 1;
            }
        }
        pop_bottom();
        ?>
    </FORM></DIV>


<DIV ID="limbasDivMenuLock" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position: absolute;display:none;z-index:995;"><FORM NAME="formular_lock" OnClick="activ_menu = 1;">
        <?php #----------------- Verknüpfungs-Menü -------------------
        pop_top('limbasDivMenuLock');
        pop_left();
        echo "&nbsp;&nbsp;<input type=\"text\" maxlength=\"2\" style=\"width:30px;\" value=\"30\" id=\"lmbLockTime\">&nbsp;&nbsp;&nbsp;<select id=\"lmbLockUnit\" class=\"contextmenu\"><option value=\"m\">".$lang[1980]."<option value=\"h\">".$lang[1981]."<option value=\"d\">".$lang[1982]."</select>&nbsp;&nbsp;<input type=\"button\" value=\"".$lang[1218]."\" OnClick=\"document.form1.lockingtime.value=document.getElementById('lmbLockTime').value+'|'+document.getElementById('lmbLockUnit').value;userecord('lock');\">";
        pop_right();
        pop_bottom();
        ?>
    </FORM></DIV>

<DIV ID="limbasDivMenuInfo" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position: absolute;display:none;z-index:992;" OnClick="activ_menu = 1;">
    <FORM NAME="info_form">
        <?php #----------------- Info-Menü -------------------
        pop_menu2('','','lmbInfoCreate',"lmb-icon-cus lmb-page-new");
        pop_menu2('','','lmbInfoEdit',"lmb-icon-cus lmb-page-edit");

        if($gresult[$gtabid]["VDESC"][0]){
            pop_line();
            pop_menu2("<i>".$gresult[$gtabid]["VDESC"][0]."</i>",'','',"lmb-icon lmb-versioning");
        }

        // todo fix/remove shortcuts (descriptions)
        /*
        pop_line();
        pop_left();
        echo "&nbsp;$lang[33]:<br>&nbsp;<span style='color: green;'>[STRG][s]</span><br>";
        echo "&nbsp;$lang[1531]:<br>&nbsp;<span style='color: green;'>[STRG][y]</span><br>";
        echo "&nbsp;$lang[1530]:<br>&nbsp;<span style='color: green;'>[STRG][x]</span><br>";
        echo "&nbsp;$lang[1524]:<br>&nbsp;<span style='color: green;'>[STRG][n]</span><br>";
        echo "&nbsp;$lang[1526]:<br>&nbsp;<span style='color: green;'>[STRG][l]</span><br>";
        pop_right();
        */

        # extension
        if(function_exists($GLOBALS["gLmbExt"]["menuChangeCInfo"][$gtabid])){
            $GLOBALS["gLmbExt"]["menuChangeCInfo"][$gtabid]($gtabid,$form_id,$ID,$gresult);
        }

        pop_bottom();
        ?>
    </FORM></DIV>

<DIV ID="limbasDivMenuContext" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position:absolute;display:none;z-index:991;" OnClick="activ_menu = 1;">
    <?php #----------------- Datei -------------------
    if($form_id){pop_top('limbasDivMenuContext');}
    if(!$isview){pop_submenu(9,'','');}					# Infomenü
    if($gtab["logging"][$gtabid]){
        pop_submenu(173,'','');							# History
    }
    pop_line();
    if($gtab["edit"][$gtabid] AND $action == "gtab_change" AND !$readonly){pop_menu(197,'','');}	# speichern


    if($gtab["add"][$gtabid]){
        pop_menu(1,'','');								# neuer Datensatz
    }

    # id ID ~ new dataset
    if($ID){

        if($gtab["copy"][$gtabid] AND !$readonly){
            pop_menu(201,'','');							# Datensatz kopieren
        }

        if($gtab["ver"][$gtabid] == 1 AND $gtab["add"][$gtabid] AND $gresult[$gtabid]["VACT"][0] AND !$readonly){
            pop_menu(235,'','');							# Datensatz versionieren
        }

        pop_line();
        pop_menu(23,'','');									# drucken
        pop_line();
        if(($gtab["hide"][$gtabid] OR $gtab["trash"][$gtabid]) AND !$readonly){
            if($gresult[$gtabid]["LMB_STATUS"][0]) {
                pop_menu(166,'','',0,0);                            # restore
            }else{
                if($gtab["trash"][$gtabid]) {
                    pop_menu(313, '', '', 0, 0);                    # trash
                }
                if($gtab["hide"][$gtabid]) {
                    pop_menu(164, '', '', 0, 0);                    # archive
                }
            }
        }


        if($gtab["lock"][$gtabid] AND !$gresult[$gtabid]["LOCK"]["USER"][$ID] AND !$readonly){
            if($gresult[$gtabid]["LOCK"]["STATIC"][$ID]){
                pop_menu(271,'','');							# freigeben
            }else{
                #pop_menu(270,'','');							# sperren
                pop_submenu(270,'','',1);
            }}

        pop_line();
        if($gtab["delete"][$gtabid] AND !$readonly){pop_menu(11,'','');}	# löschen

        # extension
        if(function_exists($GLOBALS["gLmbExt"]["menuChangeCContext"][$gtabid])){
            $GLOBALS["gLmbExt"]["menuChangeCContext"][$gtabid]($gtabid,$form_id,$ID,$gresult);
        }

        // custmenu
        if($GLOBALS['gcustmenu'][$gtabid][13]['id'][0]){
            foreach($GLOBALS['gcustmenu'][$gtabid][13]['id'] as $cmkey => $cmid){
                lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
            }
        }

        pop_bottom();

    }
    ?>
</DIV>

<DIV ID="limbasDivMenuAnsicht" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position:absolute;display:none;z-index:991" OnClick="activ_menu = 1;">
    <?php #----------------- Ansicht -------------------
    if($form_id){pop_top('limbasDivMenuAnsicht');}

    pop_menu(240,'','',$session["symbolbar"]);	# Symbolleiste
    if(!$form_id){pop_menu(281,'','',$filter["groupheader"][$gtabid]);}	# grouping fieldheaders
    if(!$isview){
        pop_line();
        pop_menu(10,'','');						# Liste
        if($action == "gtab_change"){
            pop_menu(6,'','');					# Detail
        }elseif($gtab["edit"][$gtabid] AND !$readonly){
            pop_menu(3,'','');					# bearbeiten
        }
        pop_line();
    }
    pop_menu(282,'','');					# Ansicht speichern

    # extension
    if(function_exists($GLOBALS["gLmbExt"]["menuChangeCDisplay"][$gtabid])){
        $GLOBALS["gLmbExt"]["menuChangeCDisplay"][$gtabid]($gtabid,$form_id,$ID,$gresult);
    }

    // custmenu
    if($GLOBALS['gcustmenu'][$gtabid][15]['id'][0]){
        foreach($GLOBALS['gcustmenu'][$gtabid][15]['id'] as $cmkey => $cmid){
            lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
        }
    }

    pop_bottom();
    ?>
</DIV>

<DIV ID="limbasDivMenuExtras" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="position:absolute;display:none;z-index:991" OnClick="activ_menu = 1;">
    <?php
    if($ID){
        #----------------- Extras -------------------
        if($form_id){pop_top('limbasDivMenuExtras');}

        if($GLOBALS["gformlist_exist"]){
            pop_submenu(132,'','');			# Formulare
            $l = 1;
        }
        if($GLOBALS["greportlist_exist"]){
            #pop_submenu(131,'','');			# Berichte
            pop_submenu(315,'','');
            $l = 1;
        }
        if($GLOBALS["gdiaglist_exist"]){
            pop_submenu(232,'','');			# Diagramme
            $l = 1;
        }
        if($GLOBALS["filelist_exist"]){
            pop_submenu(193,'','');			# Dateien
            $l = 1;
        }
        if($gfield[$gtabid]["r_verkntabid2"] AND $GLOBALS["rverknpf_exist"]){
            pop_submenu(133,'','');			# Link Herkunft
            $l = 1;
        }
        #if($GLOBALS["workflow_exist"]){
        #	if($l){pop_line();}
        #	pop_submenu(238,'','');			# Workflow
        #	$l = 1;
        #}

        if($l){pop_line();}

        if(!$isview){
            pop_submenu(109,'','',1);		# Wiedervorlage
        }

        if($gtab["edit_userrules"][$gtabid] OR ($gtab["edit_ownuserrules"][$gtabid] AND $gresult[$gtabid]["IS_OWN_USER"][0])){pop_submenu(266,'','',0);}

        if($LINK[239] AND $gtab["viewver"][$gtabid] AND $gresult[$gtabid]["V_ID"]){	# Version Diff
            foreach ($gresult[$gtabid]["V_ID"] as $key => $value){
                if($value != $ID){
                    $versionlist .= "<OPTION VALUE=\"$value\">Vers. ".$key;
                }
            }
            if($versionlist){
                pop_line();
                pop_left();
                echo "&nbsp;".$lang[$LINK["name"][239]]."&nbsp;&nbsp;<SELECT class=\"contextmenu\" ID=\"versionDiff\" OnChange=\"limbasVersionDiff('$gtabid','$form_id','$ID',this.value);this.selectedIndex=0;document.getElementById('limbasDivMenuExtras').style.visibility='hidden';\" STYLE=\"width:55px;\"><OPTION>";
                echo $versionlist;
                echo "</SELECT>&nbsp;";
                pop_right();
            }
        }

        if($LINK[277]){
            pop_line();
            pop_submenu(277,'','');					# Einstellungen
        }

    }

    # extension
    if(function_exists($GLOBALS["gLmbExt"]["menuChangeCExtras"][$gtabid])){
        $GLOBALS["gLmbExt"]["menuChangeCExtras"][$gtabid]($gtabid,$form_id,$ID,$gresult);
    }

    // custmenu
    if($GLOBALS['gcustmenu'][$gtabid][16]['id'][0]){
        foreach($GLOBALS['gcustmenu'][$gtabid][16]['id'] as $cmkey => $cmid){
            lmb_pop_custmenu($cmid,$gtabid,$ID,$gresult);
        }
    }

    pop_bottom();
    ?>
</DIV>


<DIV ID="limbasDivMenuSettings" class="lmbContextMenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>" style="visibility:hidden;z-index:993" OnClick="activ_menu = 1;">
    <?php
    pop_top('limbasDivMenuSettings');
    if(!$isview AND $gtab["delete"][$gtabid]){
        if($filter["force_delete"][$gtabid]){$checked = "checked";}else{$checked = null;}
        if($gtab["versioning"][$gtabid]){pop_checkbox(276,"document.form1.filter_force_delete.value='1';","filter_force_delete",1,$checked,0);} # rekursives Löschen forcieren
    }
    pop_bottom();
    ?>
</DIV>

<DIV ID="limbasDivRowSetting" class="lmbContextMenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>" style="display:none;z-index:993" OnClick="activ_menu = 1;">
    <?php #----------------- SubMenü - Rowsetting -------------------
    pop_top('limbasDivRowSetting');
    pop_menu2($lang[861],null,null,"lmb-textsort-up",null,"LmExt_RelationFields(this,lmbGlobVar['ActiveTab'],lmbGlobVar['ActiveRow'],'','1','$ID',lmbGlobVar['sortid'],'','ASC','',lmbGlobVar['gformid'],lmbGlobVar['formid'])");
    pop_menu2($lang[862],null,null,"lmb-textsort-down",null,"LmExt_RelationFields(this,lmbGlobVar['ActiveTab'],lmbGlobVar['ActiveRow'],'','1','$ID',lmbGlobVar['sortid'],'','DESC','',lmbGlobVar['gformid'],lmbGlobVar['formid'])");
    pop_bottom();
    ?>
</DIV>

<?php
# extension
if(function_exists($GLOBALS["gLmbExt"]["menuCChange"][$gtabid])){
    $GLOBALS["gLmbExt"]["menuCChange"][$gtabid]($gtabid,$form_id,$ID,$gresult);
}
