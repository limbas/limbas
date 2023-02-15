<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




$tbzm = $result_gtab[$tabgroup]["argresult"][$tabid];
if($gtab["typ"][$tabid] == 5){$isview = 1;}
$col = dbf_5(array($DBA["DBSCHEMA"],$result_gtab[$tabgroup]["tabelle"][$tbzm]));


	
?>
<form action="main_dyns_admin.php" method="post" name="form3" id="form3">
    <input type="hidden" name="val">
    <input type="hidden" name="tabgroup" value="<?=$tabgroup?>">

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="options-tab" data-bs-toggle="tab" href="#options" role="tab"><?=$lang[2795]?></a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link" id="info-tab" data-bs-toggle="tab" href="#info" role="tab"><?=$lang[2836]?></a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane show active py-3" id="options" role="tabpanel">

        <div class="row">
            <div class="col-6">
                
                <?php // tablename ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[951]?></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" value="<?=$result_gtab[$tabgroup]['tabelle'][$tbzm]?>" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','tabname')" <?=$readlonly?>>
                        <small class="form-text text-muted"><?=$lang[2833]?></small>
                    </div>
                </div>

                <?php // spelling ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[924]?></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" value="<?=$result_gtab[$tabgroup]['beschreibung'][$tbzm]?>" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','desc')">
                        <small class="form-text text-muted"><?=$lang[2834]?></small>
                    </div>
                </div>

                <?php // tablegroup ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[900]?></label>
                    <div class="col-sm-8">
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','setmaingroup')">
                            <option value="0"></option>
                            <?php foreach($tabgroup_["id"] as $bzm1 => $value1): ?>
                            <option value="<?=$value1?>" <?=($value1 == $result_gtab[$tabgroup]["maingroup"][$tbzm])?'selected':''?>><?=$tabgroup_["name"][$bzm1]?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted"><?=$lang[2834]?></small>
                    </div>
                </div>


                <?php // type ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[925]?></label>
                    <div class="col-sm-8">
                        <span class="form-control-plaintext">
                            <?php
                            if($result_gtab[$tabgroup]["typ"][$tbzm] == 1){echo $lang[164];}
                            elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 2){echo $lang[1929];}
                            elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 6){echo $lang[767];}
                            elseif($result_gtab[$tabgroup]["typ"][$tbzm] == 5){
                                if($result_gtab[$tabgroup]["viewtype"][$tbzm] == 1){
                                    echo $lang[2656];
                                }elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 2){
                                    echo $lang[2657];
                                }elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 3){
                                    echo $lang[2658];
                                }elseif($result_gtab[$tabgroup]["viewtype"][$tbzm] == 4){
                                    echo $lang[2659];
                                }else{
                                    echo $lang[2656];
                                }
                            }
                            ?>
                        </span>
                    </div>
                </div>

                <?php // numfields ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[953]?></label>
                    <div class="col-sm-8">
                        <span class="form-control-plaintext"><?=$result_gtab[$tabgroup]["num_gtab"][$tbzm]?></span>
                    </div>
                </div>
                
                <hr>

                <?php // keyfield
                if($isview):
                 ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2950]?></label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" value="<?=$result_gtab[$tabgroup]['keyfield'][$tbzm]?>" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','keyfield')">
                        <small class="form-text text-muted"><?=$lang[2951]?></small>
                    </div>
                </div>
                <?php endif; ?>



                <?php // versioning
                if(!$isview):
                ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2132]?></label>
                    <div class="col-sm-8">
                        
                        <?php
                        if(!$result_gtab[$tabgroup]["versioning"][$tbzm]){$selected_0 = "selected";}
                        if($result_gtab[$tabgroup]["versioning"][$tbzm] == 1){$selected_1 = "selected";$det = $lang[2142];}
                        if($result_gtab[$tabgroup]["versioning"][$tbzm] == 2){$selected_2 = "selected";$det = $lang[2142];}
                        
                        if($result_gtab[$tabgroup]["id"][$tbzm] == $result_gtab[$tabgroup]["verknid"][$tbzm]): ?>
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','versioning')">
                            <option value="-1"></option>
                            <option value="1" <?=$selected_1?>><?=$lang[2142]?></option>
                            <option value="2" <?=$selected_2?>><?=$lang[2143]?></option>
                        </select>
                        
                        <?php if (!$result_gtab[$tabgroup]["versioning"][$tbzm] AND $result_gtab[$tabgroup]["validity"][$tbzm] == 2): ?>
                        <span class="text-danger"><?=$lang[3008]?></span>
                        <?php endif; ?>
                        
                        <?php else: ?>

                        <span class="form-control-plaintext"><?=$det?></span>
                        
                        <?php endif; ?>
                        
                        <small class="form-text text-muted"><?=$lang[2822]?></small>
                    </div>
                </div>
                <?php endif; ?>



                <?php // numrowcalc
                if($result_gtab[$tabgroup]["num_gtab"][$tbzm] > 0):
                ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2688]?></label>
                    <div class="col-sm-8">
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','numrowcalc')">
                            <option value="-1"><?=$lang[2685]?></option>
                            <option value="1" <?=($result_gtab[$tabgroup]['numrowcalc'][$tbzm] == 1)?'selected':''?>><?=$lang[2686]?></option>
                            <option value="2" <?=($result_gtab[$tabgroup]['numrowcalc'][$tbzm] == 2)?'selected':''?>><?=$lang[2687]?></option>
                        </select>
                        <small class="form-text text-muted"><?=$lang[2823]?></small>
                    </div>
                </div>
                <?php endif; ?>


                <?php // custmenu ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2555]?></label>
                    <div class="col-sm-8">
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','custmenu')">
                            <option value="-1"></option>
                            <?php
                            global $LINK;
                            foreach ($LINK['name'] as $key => $value) {
                                if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000) {
                                    if($key == $result_gtab[$tabgroup]['custmenu'][$tbzm]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                                    echo "<option value=\"$key\" $SELECTED>" . $lang[$value] . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted"><?=$lang[3104]?></small>
                    </div>
                </div>

                <?php // indicator ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1255]?></label>
                    <div class="col-sm-8">
                        <textarea onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','indicator')" rows="3" class="form-control form-control-sm"><?=$result_gtab[$tabgroup]['indicator'][$tbzm]?></textarea>
                        <small class="form-text text-muted"><?=$lang[2824]?></small>
                    </div>
                </div>

                <?php // order by ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1837]?></label>
                    <div class="col-sm-8">
                        <textarea onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','orderby')" rows="3" class="form-control form-control-sm"><?=$result_gtab[$tabgroup]['orderby'][$tbzm]?></textarea>
                        <small class="form-text text-muted"><?=$lang[3048]?></small>
                    </div>
                </div>
                
                <?php // trigger
                if($gtrigger[$tabid]["id"]){
                ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2216]?></label>
                    <div class="col-sm-8" <div id="triggerpool">
                        <?php
                        foreach($gtrigger[$tabid]["id"] as $trid => $trval){
                            if(in_array($trid,$result_gtab[$tabgroup]["trigger"][$tbzm])){$CHECKED = "CHECKED";}else{$CHECKED = "";}
                            echo "<input type=\"checkbox\" id=\"trigger_$trid\" $CHECKED onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','trigger')\"> ".$gtrigger[$tabid]["trigger_name"][$trid]." (".$gtrigger[$tabid]["type"][$trid].")<br>";
                        }
                        ?>
                        <small class="form-text text-muted"><?=$lang[2825]?></small>
                    </div>
                </div>
                <?php }?>





                <?php
                /* --------------------------------------------------------- */
                /* ----------------- Calendar settings --------------------- */
                if($result_gtab[$tabgroup]["typ"][$tbzm] == 2){
                    echo "<tr><td><hr></td><td><hr></td></tr>
		<tr><td colspan=\"2\" align=\"center\" class=\"tabHeaderItem\">".$lang[2852]."</td></tr>";
                    echo "<tr><td valign=\"top\">".$lang[2850]."</td><td>";
                    echo "<select onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params1')\"><option>";
                    $sqlquery = "SELECT FIELD_ID,FIELD_NAME FROM LMB_CONF_FIELDS WHERE TAB_ID = $tabid AND FIELD_TYPE = 11 AND DATA_TYPE = 24";
                    $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
                    while(lmbdb_fetch_row($rs)) {
                        if(lmbdb_result($rs, "FIELD_ID") == $result_gtab[$tabgroup]["params1"][$tbzm]){$selected = 'selected';}else{$selected = '';}
                        echo "<option value=\"".lmbdb_result($rs, "FIELD_ID")."\" $selected>".lmbdb_result($rs, "FIELD_NAME");
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
                    echo "<tr><td valign=\"top\">searchCalendar</td><td><select multiple style=\"width:100%\" name=\"param2[searchCalendar]\" size=\"2\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
                    foreach ($gfield[$tabid]['field_name'] as $key => $value){
                        echo "<option value=\"$key\" ";
                        if(is_array($result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar']) AND in_array($key,$result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar'])){echo 'selected';}
                        echo ">$value";
                    }
                    echo "</select></td></tr>";

                    # search Resource
                    if($result_gtab[$tabgroup]["params1"][$tbzm]){
                        $rtabid = $gfield[$tabid]['verkntabid'][$result_gtab[$tabgroup]["params1"][$tbzm]];
                        echo "<tr><td valign=\"top\">searchResource</td><td><select multiple style=\"width:100%\" name=\"param2[searchResource]\" size=\"2\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
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
                    echo "<tr><td valign=\"top\">Search Kanban</td><td><select multiple style=\"width:100%\" name=\"param2[searchKanban]\" size=\"" . (lmb_count($gfield[$tabid]['field_name']) + 1) . "\" onchange=\"ajaxEditTable(this,'$tabid','$tabgroup','params2')\"><option>";
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

                
                
                ?>
                
                <?php if (!$isview): ?>
                
                <hr>

                <?php // rebuild rules ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[575]?></label>
                    <div class="col-sm-8">
                        <div class="form-control-plaintext"><i class="lmb-icon lmb-refresh cursor-pointer" onclick="open('main_admin.php?action=setup_grusrref&check_table=<?=$tabid?>&check_all=1' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400')"></i></div>
                        <small class="form-text text-muted"><?=$lang[1054]?></small>
                    </div>
                </div>

                <?php // rebuild rules ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2761]?></label>
                    <div class="col-sm-8">
                        <div class="form-control-plaintext"><i class="lmb-icon lmb-refresh cursor-pointer" onclick="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','tablesync')"></i>
                        <?php if($tablesync): ?>
                            <i class="lmb-icon lmb-aktiv"></i>
                        <?php endif; ?>
                        </div>
                        <small class="form-text text-muted"><?=$lang[2030]?></small>
                    </div>
                </div>
                
                <?php endif; ?>
            </div>
            <div class="col-6">

                <?php if (!$isview): ?>
                
                <?php // spelling ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1779]?></label>
                    <div class="col-sm-8">
                        <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['logging'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','logging')">
                        <small class="form-text text-muted"><?=$lang[2826]?></small>
                    </div>
                </div>

                    <?php // lockable ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[657]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['lockable'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','lockable')">
                            <small class="form-text text-muted"><?=$lang[2827]?></small>
                        </div>
                    </div>

                    <?php // linecolor ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1601]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['linecolor'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','linecolor')">
                            <small class="form-text text-muted"><?=$lang[2828]?></small>
                        </div>
                    </div>

                    <?php // validate ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3071]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['validate'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','validate')">
                            <small class="form-text text-muted"><?=$lang[3072]?></small>
                        </div>
                    </div>

                    <?php // ajaxpost ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2640]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['ajaxpost'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','ajaxpost')">
                            <small class="form-text text-muted"><?=$lang[2830]?></small>
                        </div>
                    </div>

                    <?php // groupable ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2362]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['groupable'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','groupable')">
                            <small class="form-text text-muted"><?=$lang[2831]?></small>
                        </div>
                    </div>

                    <?php // delete recursiv ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2469]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['recursiv_delete'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','recursiv_delete')">
                            <small class="form-text text-muted"><?=$lang[2470]?></small>
                        </div>
                    </div>

                    <?php // validity ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3000]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['validity'][$tbzm] >= 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','validity')">&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang[2132]?>&nbsp;<input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['validity'][$tbzm] == 2)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','validity_version')"><br>
                            <small class="form-text text-muted"><?=$lang[3001]?></small>
                        </div>
                    </div>

                    <?php // datasync ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2915]?></label>
                        <div class="col-sm-8">
                            <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','datasync')">
                                <option value=""></option>
                                <option value="1" <?=($result_gtab[$tabgroup]['datasync'][$tbzm] == 1)?'selected':''?>>client based</option>
                                <option value="2" <?=($result_gtab[$tabgroup]['datasync'][$tbzm] == 2)?'selected':''?>>global</option>
                            </select>
                            <small class="form-text text-muted"><?=$lang[2963]?></small>
                        </div>
                    </div>
                
                
                
                <?php endif; ?>


                <?php // multitenant ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2961]?></label>
                    <div class="col-sm-8">
                        <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['multitenant'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','multitenant')">
                        <small class="form-text text-muted"><?=$lang[2964]?></small>
                    </div>
                </div>

                <?php // theme ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm">Theme</label>
                    <div class="col-sm-8">
                        <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['theme'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','theme')">
                        <small class="form-text text-muted">disable bootstrap theme</small>
                    </div>
                </div>

            </div>
        </div>

                    
        
        
        
    </div>
    <div class="tab-pane" id="info" role="tabpanel">
        
        <table class="table table-sm table-striped table-hover">
            <?php
            if($col):

                foreach ($col['columnname'] as $tkey => $tvalue): ?>
            
                <tr>
                    <th><?=$tvalue?></th>
                    <td><?=$col["datatype"][$tkey]?></td>
                    <td><?=$col["length"][$tkey]?><?=($col["scale"][$tkey])?'('.$col["scale"][$tkey].')':''?></td>
                </tr>
            
            <?php

                endforeach;
            endif;
            ?>
        </table>
        
    </div>
</div>
</form>
