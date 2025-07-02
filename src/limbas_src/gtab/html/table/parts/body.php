<?php

/* ---------------------- rows --------------------------------------- */
$bzm = 0;
while($bzm < $gresult[$gtabid]['res_viewcount']) {

    # Data ID
    $ID = $gresult[$gtabid]['id'][$bzm];

    # ----------- table edit permission -----------
    $tablePerm_edit = $gtab['edit'][$gtabid];
    # ----------- table edit permission over editrule -----------
    if($gtab['editrule'][$gtabid] AND $tablePerm_edit AND ($filter['alter'][$gtabid] OR ($gfield[$gtabid]['listedit'] AND in_array(1,$gfield[$gtabid]['listedit'])))){
        $tablePerm_edit = !check_GtabRules($ID,$gtabid,null,$gtab['editrule'][$gtabid],$bzm,$gresult);
    }

    # --- check dataset-lock ---
    if($gtab["lockable"][$gtabid] AND $filter["locked"][$gtabid]){
        $lck = lock_data_check($gtabid,$ID,$session["user_id"]);
        if($lck["isselflocked"]){
            $lock = "<i class=\"lmb-icon lmb-selflock\" TITLE=\"".$lang[1721]." ".$userdat["bezeichnung"][$gresult[$gtabid]["INUSE_USER"][$bzm]]." - ".$lang[1670].": ".$lck["time_left"]."\" STYLE=\"vertical-align:bottom;\"></i>";
        }else{
            $lock = "<i class=\"lmb-icon lmb-lock\" title=\"".$lang[1721]." ".$userdat["bezeichnung"][$gresult[$gtabid]["INUSE_USER"][$bzm]]." - ".$lang[1670].": ".$lck["time_left"]."\" style=\"vertical-align:bottom;margin-left:2px;margin-right:2px;\"></i>";
        }
    }

    # --- check version-locking ---
    if(!$gresult[$gtabid]["VACT"][$bzm] AND $gtab["viewver"][$gtabid]){$subversion = 1;}else{$subversion = 0;}

    # ---- check of already done ------
    $pophist["done"][$gtabid] = $gtabid;
    # ---- self-relation ------
    if($gverkn[$gtabid]["id"] AND $popc[$gtabid] AND $gtab["sverkn"][$gtabid]){
        if(!$pophist["sdone"][$gtabid]){$pophist["sdone"][$gtabid] = array();}
        if(in_array($ID,$pophist["sdone"][$gtabid])){$bzm++; continue;}
        $pophist["sdone"][$gtabid][] = $ID;
        # no popups
        if($verknpf){unset($popc[$gtabid]);}
    }

    /* --- row-color --------------------------------------- */

    # swich bg color
    if($BGCLASS == 'gtabBodyTRColorB'){$BGCLASS = 'gtabBodyTRColorA';}else{$BGCLASS = 'gtabBodyTRColorB';}

    $BGCOLOR = '';
    # row-color from indicator
    if(is_array($gresult[$gtabid]["indicator"]["color"][$bzm])){
        $BGCOLOR = average_color($gresult[$gtabid]["indicator"]["color"][$bzm]);
        # row-color from user
    }elseif($gresult[$gtabid]["color"][$bzm]){
        $BGCOLOR = $gresult[$gtabid]["color"][$bzm];
    }
    # row-title from indicator
    $title = "";
    if($gresult[$gtabid]["indicator"]["title"][$bzm]){
        $title = implode(", ",$gresult[$gtabid]["indicator"]["title"][$bzm]);
    }
    # color for grouping & left empty space by grouping
    if($filter["popups"][$gtabid][$ID][$prev_id]){
        $grpimage = "lmb-small-arrow-down u-color-4";
        $BGCOLOR = $farbschema["WEB14"];
    }else{
        $grpimage = "lmb-small-arrow-right u-color-4";
    }

    # class from indicator
    $indicatorClass = '';
    if ($gresult[$gtabid]['indicator']['class'][$bzm]) {
        $indicatorClass = $gresult[$gtabid]['indicator']['class'][$bzm];
    }

    # attribute from indicator
    $indicatorAttribute = '';
    if ($gresult[$gtabid]['indicator']['attribute'][$bzm]) {
        $indicatorAttribute = $gresult[$gtabid]['indicator']['attribute'][$bzm];
    }

    # use formular from indicator
    $form_id = null;
    if ($gresult[$gtabid]['indicator']['form_id'][$bzm]) {
        $form_id = $gresult[$gtabid]['indicator']['form_id'][$bzm];
    # use formular from reminder
    }elseif($GLOBALS['greminder'][$gtabid]["formd_id"][$filter['reminder'][$gtabid]]){
        $form_id = $GLOBALS['greminder'][$gtabid]["formd_id"][$filter['reminder'][$gtabid]];
    # use formular from group setting
    }elseif($gtab["tab_view_form"][$gtabid]){
        $form_id = $gtab["tab_view_form"][$gtabid];
    # use formular from table setting
    }elseif($gtab["detailform"][$gtabid]){
        $form_id = $gtab["detailform"][$gtabid];
    }

    # klick event in views
    if($gtab["typ"][$gtabid] == 5 AND $gtab["event"][$gtabid]) {
        $klickevent = eval("return \"" . $gtab["event"][$gtabid] . "\";");
    }else {
        $klickevent = "lmbTableDblClickEvent(event,this,'$gtabid','$ID');return false;";
    }

    // use custom kontextmenu
    if($GLOBALS['gcustmenu'][$gtabid][18]['directlink'][0]) {
        $kontextevent = lmb_pop_custmenu($GLOBALS['gcustmenu'][$gtabid][18]['id'][0],$gtabid, $ID, linkonly:1);
    }else{
        $kontextevent = "lmbTableContextMenu(event,this,'$ID','$gtabid');";
    }
    /* ---------------- begin row -------------------- */
    /*----------------- <TR> -------------------*/

    if($session["data_display"] == 2 AND $verknpf != 1){$is_frame = "1";}else{$is_frame = "0";}

    ?>

    <tr
        id="elrow_<?=e($ID)?>_<?=e($gtabid)?>"
        title="<?=$title?>"
        data-id="<?=e($gresult[$gtabid]['id'][$bzm])?>"
        data-reltype="<?=$prev_typ?>"
        data-relid="<?=$prev_id?>"
        data-reltab="<?=$prev_gtabid?>"
        data-relfield="<?=$prev_fieldid?>"
        data-formid="<?=$form_id?>"
        data-formdimension="<?=$GLOBALS['gformlist'][$gtabid]["dimension"][$form_id]?>"
        data-formopenas="<?=$gtab["detailform_opener"][$gtabid]?>"
        data-createinfo="<?=e($gresult[$gtabid]["ERSTDATUM"][$bzm].' '.$userdat["bezeichnung"][$gresult[$gtabid]["ERSTUSER"][$bzm]])?>"
        data-editinfo="<?=e($gresult[$gtabid]["EDITDATUM"][$bzm].' '.$userdat["bezeichnung"][$gresult[$gtabid]["EDITUSER"][$bzm]])?>"
        <?=$indicatorAttribute?>
        class="element-row gtabBodyTR <?=$BGCLASS?> <?=$indicatorClass?>"
        style="background-color:<?=$BGCOLOR?>;cursor:context-menu"
        lmbbgcolor="<?=$BGCOLOR?>"
        oncontextmenu="event.preventDefault();<?=$kontextevent?>"
        ondblclick="lmbTableClickEvent(event,this,<?=e($gtabid)?>,<?=e($ID)?>);<?=$klickevent?>"
        onclick="lmbTableClickEvent(event,this,<?=e($gtabid)?>,<?=e($ID)?>);"
    >

        <td id="tdap_<?=e($ID)?>_<?=e($gtabid)?>" data-fieldid="0" class="element-cell gtabBodyTD<?=$class?>" style="padding-left:4px;width:<?=$gfield[$gtabid]["rowsize"][0]?>px;" nowrap>



<?php
    
    // TODO onContextMenu: before: click

    # grouping
    /*
    if($popg[$gtabid][$ebene]){
        $tdvalues = "<span style=\"width:10px;height:100%;border:none\"></span>";
        for($i=1;$i<=$ebene;$i++){
            $tdvalues .= "<span style=\"width:30px;height:100%\"></span>";
        }
    }
    */

    # checkbox
    if($gtab["checkboxselect"][$gtabid]){
        ?>
        <input type="checkbox" id="chkbelrow_<?=e($ID)?>_<?=e($gtabid)?>" onclick="lmbTableClickCheckbox(this);">
        <?php
    }


    # popup symbol
    if($pophist["cdone"][$gtabid]){
        $pophist_ = $pophist; $pophist_["cdone"] = null;
        echo "<i name=\"popicon_".$gtabid."_".$ID."_".$prev_id."\" id=\"popicon_".$gtabid."_".$ID."_".$prev_id."\" class=\"lmb-icon $grpimage\" border=\"0\" style=\"vertical-align:bottom;cursor:pointer;padding-right:2px;padding-left:2px;\" onclick=\"return ajax_linksPopup(event,this,'".$gtabid."','".$ID."','".$prev_id."','".$prev_gtabid."','".$prev_fieldid."','".$ebene."','".$subtab."','".rawurlencode(serialize($pophist_))."');\"></i>\n";
    }

    #if($tdvalues){
    #	echo "<td id=\"tdap_".$ID."_".$gtabid."\" class=\"gtabBodyTD".$class."\" bgcolor=\"$BGCOLOR1\" style=\"width:25px;\" nowrap>";
    #	echo $tdvalues;
    #	echo "</td>";
    #}

    /*----------------- short info icons -------------------*/

    if($verknpf == 1){
        if($gresult[$gtabid]["verkn_id"][$bzm]){$lstyle = "color:grey;\"";}else{$lstyle = "";}
    }

    # versioning
    if($gtab["viewver"][$gtabid] AND $filter["viewversion"][$gtabid]){
        if($subversion){$c = "red";}else{$c = "green";}
        echo "<span style=\"color:$c;padding-left:2px;padding-right:2px;border:1px solid $c;background-color:".$farbschema["WEB11"]."\">".$gresult[$gtabid]["VID"][$bzm]."</span>\n";
    }
    # lock
    if($lock){
        echo $lock;
    }
    # in archive
    if($gresult[$gtabid]["LMB_STATUS"][$bzm] == 1){
        echo "<i class=\"lmb-icon lmb-page-key\" border=\"0\" style=\"vertical-align:bottom;padding-left:2px;padding-right:2px;\"></i>\n";
    }elseif($gresult[$gtabid]["LMB_STATUS"][$bzm] == 2){
        echo "<i class=\"lmb-icon lmb-trash\" border=\"0\" style=\"vertical-align:bottom;padding-left:2px;padding-right:2px;\"></i>\n";
    }

    #echo "</td>\n";

    /*----------------- show indicator object in first cell -------------------*/
    if($gresult[$gtabid]["indicator"]["object"][$bzm]){
        foreach ($gresult[$gtabid]["indicator"]["object"][$bzm] as $ikey => $ival){
            echo "<span style=\"padding-left:2px;padding-right:2px;\">$ival</span>\n";
        }
    }

    # reminder
    if($gresult[$gtabid]["LMBREM_FRIST"][$bzm]){
        echo "<span style=\"padding-left:2px;padding-right:2px;\" title=\"".get_date($gresult[$gtabid]["LMBREM_FRIST"][$bzm],1). ' - ' . $userdat["bezeichnung"][$gresult[$gtabid]["LMBREM_USER"][$bzm]] . ' - '.strip_tags($gresult[$gtabid]["LMBREM_DESCR"][$bzm])."\">". ($gresult[$gtabid]["LMBREM_CONTENT"][$bzm] ? strip_tags($gresult[$gtabid]["LMBREM_CONTENT"][$bzm]) : get_date($gresult[$gtabid]["LMBREM_FRIST"][$bzm],1) )."</span>\n";
    }
    
    ?>
            
        </td>
        
    <?php
    # --- List of fields ---------------------------------------
    $a = array_keys($gfield[$gtabid]["sort"]);
    $last_key = end($a);
    foreach($gfield[$gtabid]["sort"] as $key => $value){

        $rightspace = '';
        $alterTable = null;

        // --- hide col ---------------------------------------
        if($gfield[$gtabid]['col_hide'][$key]){continue;}
        if($gfield[$gtabid]["field_type"][$key] >= 100 OR $filter["hidecols"][$gtabid][$key]){continue;}

        // align
        if($gfield[$gtabid]["field_type"][$key] == 5){$align = "right";}else{$align = "left";}

        // custmenu
        $custmenu = '';
        if($GLOBALS['gcustmenu'][$gtabid][1][$key]){            # filedbased
            $custmenu = $GLOBALS['gcustmenu'][$gtabid][1][$key];
        }elseif($GLOBALS['gcustmenu'][$gtabid][1][0]){          # tablebased
            $custmenu = $GLOBALS['gcustmenu'][$gtabid][1][0];
        }
        
        ?>
            
        <td
                id="td_<?=e($ID)?>_<?=e($gfield[$gtabid]["field_id"][$key])?>_<?=e($gtabid)?>"
                data-fieldid="<?=e($key)?>"
                data-custmenu="<?=$custmenu?>"
                align="<?=$align?>"
                class="element-cell gtabBodyTD<?=$class.$lclass?>"
                style="width:<?=$gfield[$gtabid]["rowsize"][$key]?>px;<?=$lstyle?>;<?=$rightspace?>"

                oncontextmenu="cell_id = 'td_<?=$ID?>_<?=$gfield[$gtabid]["field_id"][$key]?>_<?=$gtabid?>';"
                <?=($gfield[$gtabid]["color"][$key] AND !$gresult[$gtabid]["color"][$bzm]) ? 'bgcolor="'.$gfield[$gtabid]["color"][$key].'" lmbbgcolor="'.$gfield[$gtabid]["color"][$key].'"' : ''?>
        >
        
        
        <?php

        // define alter mode
        if($filter["alter"][$gtabid] OR $gfield[$gtabid]["listedit"][$key]){
            $alterTable = 1;
        }

        // check field editrule
        if($alterTable AND $gfield[$gtabid]["editrule"][$key]){
            $alterTable = !check_GtabRules($ID,$gtabid,$key,$gfield[$gtabid]["editrule"][$key],$bzm,$gresult);
        }

        // alter table mode / table permission / filed permission / no subversion
        if($alterTable AND $tablePerm_edit AND $gfield[$gtabid]["perm_edit"][$key] AND !$lck['islocked'] AND !$subversion){$edittyp = 1;}else{$edittyp = 2;}

        # PHP / SQL - Argument
        if($gfield[$gtabid]["argument_typ"][$key]){
            if(!$gfield[$gtabid]["argument_edit"][$key]){$edittyp = 2;}
            if($gfield[$gtabid]["argument_typ"][$key] == 15){
                $gresult[$gtabid][$key][$bzm] = cftyp_13($ID,$gresult,$key,$gtabid,'','','',$edittyp,$bzm);
            }
        }

        if($gresult[$gtabid][$key][$bzm] OR $gresult[$gtabid][$key][$bzm] == "0" OR $gfield[$gtabid]["field_type"][$key] == 11 OR $gfield[$gtabid]["ext_type"][$key] OR $edittyp == 1){

            /* ------------------ Default --------------------- */
            $fname = "cftyp_".$gfield[$gtabid]["funcid"][$key];
            /* ------------------ Extension --------------------- */
            if($gfield[$gtabid]["ext_type"][$key]){
                if(function_exists("lmbc_".$gfield[$gtabid]["ext_type"][$key])){
                    $fname = "lmbc_".$gfield[$gtabid]["ext_type"][$key];
                }
            }

            /* ------------------ Typefunction --------------------- */
            $retrn = $fname($bzm,$key,$gtabid,$edittyp,$gresult,0);
            /* ------------------ /Typefunction -------------------- */
        }
        
        ?>
        
        </td>
            
        <?php

    }
    
    ?>
        
    </tr>
    
    <?php


    #---- show specific userrules -------
    if($filter["userrules"][$gtabid] AND ($gtab["edit_userrules"][$gtabid] OR ($gtab["edit_ownuserrules"][$gtabid] AND $gresult[$gtabid]["IS_OWN_USER"][$bzm]))){ ?>

        <tr>
            <TD colspan="<?=($gtab['fieldcount'][$gtabid]+1)?>">
            <?=show_userRules($gtabid,$ID)?>
            </TD>
        </tr>

<?php
    }

    #---- show popup relations -------
    if($filter["popups"][$gtabid][$ID][$prev_id] OR $filter["popups"][$gtabid]["all"][$prev_id]){

        # Buffer
        ob_start();

        #---- popup right-relation -------
        if($gverkn[$gtabid]["id"] AND $popc[$gtabid]){
            $bzm1 = 0;
            foreach($gverkn[$gtabid]["id"] as $key => $value){
                if($popc[$gtabid][$gfield[$gtabid]["verkntabid"][$key]] AND $gtab["groupable"][$gtabid]){
                    # check of alredy done
                    if($value != $gtabid AND in_array($value,$pophist["done"])) continue;
                    $filter["filter"][$gfield[$gtabid]["verkntabid"][$key]] = $filter["filter"][$gtabid];
                    $verkn = set_verknpf($gtabid,$gfield[$gtabid]["field_id"][$key],$ID,0,0,1,0);
                    $filter["anzahl"][$gfield[$gtabid]["verkntabid"][$key]] = "all";
                    $gresult_ = get_gresult($gfield[$gtabid]["verkntabid"][$key],0,$filter,0,$verkn);
                    if($gresult_[$value]["res_viewcount"] > 0){
                        #lmbGlistOpen();
                        echo "
							<div class=\"lmbPopupDiv\">
							<table class=\"lmbPopupGtab\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;\">
							";

                        #echo "<tr><td colspan=\"".($gtab["fieldcount"][$gtabid]+1)."\"><div class=\"gtabHeaderMenuTR\" style=\"padding:3px;\">&nbsp;".$gtab["desc"][$gfield[$gtabid]["verkntabid"][$key]]."</div></td></tr>\n";

                        lmbGlistHeader($gfield[$gtabid]["verkntabid"][$key],$gresult_,1);
                        lmbGlistBody($gfield[$gtabid]["verkntabid"][$key],$gresult_,0,0,0,$filter,$ID,$gtabid,$gfield[$gtabid]["field_id"][$key],1,$ebene,0);
                        echo "</table></div>";
                        #lmbGlistClose();
                    }
                    $bzm1++;
                }
            }
        }

        $output = ob_get_contents();
        ob_end_clean();
        echo "<tr id=\"subtab_".$gtabid."_".$ID."_".$prev_id."\"><td id=\"subtd_".$gtabid."_".$ID."_".$prev_id."\" colspan=\"".($gtab["fieldcount"][$gtabid]+1)."\">\n";
        echo $output;
        echo "</TD></TR>";

        # ---- Endlosverknüpfungen leeren ------
        $pophist["done"] = null;
        $pophist["sdone"] = null;
    }


    $bzm++;
}
