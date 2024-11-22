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

                <?php // detail formular ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[1169]?></label>
                    <div class="col-sm-8">
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','detailform')">
                            <option value="-1"></option>
                            <?php
                            global $gformlist;
                            foreach ($gformlist[$tabid]['name'] as $key => $value) {
                                if($gformlist[$tabid]["typ"][$key] != 1){continue;}
                                if($key == $result_gtab[$tabgroup]['detailform'][$tbzm]){$SELECTED = 'SELECTED';}else{$SELECTED = '';}
                                echo "<option value=\"$key\" $SELECTED>" . $value . "</option>";
                            }
                            ?>
                        </select>
                        <small class="form-text text-muted"><?=$lang[3170]?></small>
                    </div>
                </div>

                <?php // detail formular open as ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2321]?></label>
                    <div class="col-sm-8">
                        <select class="form-select form-select-sm" onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','detailform_opener')">
                            <option value="-1">inframe</option>
                            <option value="2" <?=($result_gtab[$tabgroup]['detailform_opener'][$tbzm] == 2)?'selected':''?>>modal</option>
                            <option value="3" <?=($result_gtab[$tabgroup]['detailform_opener'][$tbzm] == 3)?'selected':''?>>browser tabulator</option>
                        </select>
                        <small class="form-text text-muted"><?=$lang[3038]?></small>
                    </div>
                </div>

                <?php // custmenu ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2982]?></label>
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
                






                <?php
                /* --------------------------------------------------------- */
                /* ----------------- Calendar settings --------------------- */

                if ($result_gtab[$tabgroup]["typ"][$tbzm] == 2): ?>
                    <hr>
                <div class="row">
                    <div class="col" class="tabHeaderItem"><?= $lang[2852] ?></div>
                </div>

                <?php /* viewmode */ ?>
                <?php ${'viewmode_' . $result_gtab[$tabgroup]["params2"][$tbzm]['viewmode']} = 'selected'; ?>
                <div class="row pt-2">
                    <div class="col" valign="top">viewmode</div>
                    <div class="col">
                        <select
                                class="form-select form-select-sm"
                                name="param2[viewmode]"
                                onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                            <option></option>
                            <option <?= $viewmode_dayGridMonth ?>>dayGridMonth</option>
                            <option <?= $viewmode_timeGridWeek ?>>timeGridWeek</option>
                            <option <?= $viewmode_timeGridDay ?>>timeGridDay</option>
                            <option <?= $viewmode_listMonth ?>>listMonth</option>
                            <option <?= $viewmode_listWeek ?>>listWeek</option>
                            <option <?= $viewmode_listDay ?>>listDay</option>
                        </select>
                    </div>
                </div>

                <?php /* weekNumberTitle */ ?>
                <div class="row pt-2">
                    <div class="col">weekNumberTitle</div>
                    <div class="col">
                        <input
                                class="form-control form-control-sm"
                                type="text"
                                name="param2[weekNumberTitle]"
                                value="<?= $result_gtab[$tabgroup]["params2"][$tbzm]['weekNumberTitle'] ?>"
                                onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                    </div>
                </div>

                <?php /* search Calendar -- disabled ?>
                <div class="row pt-2">
                    <div class="col" valign="top">searchCalendar</div>
                    <div class="col">
                        <select
                                class="form-select form-select-sm"
                                multiple
                                name="param2[searchCalendar]"
                                onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                            <option></option>
                            <?php foreach ($gfield[$tabid]['field_name'] as $key => $value) {
                                echo "<option value=\"$key\"";
                                if (is_array($result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar']) && in_array($key, $result_gtab[$tabgroup]["params2"][$tbzm]['searchCalendar'])) {
                                    echo 'selected';
                                }
                                echo ">$value</option>";
                            } ?>
                        </select>
                    </div>
                </div>
                <?php  */ ?>

               <?php /* minTime */ ?>
                    <div class="row pt-3">
                                <div class="col">minTime</div>
                                <div class="col">
                                    <select
                                            class="form-select form-select-sm"
                                            name="param2[minTime]"
                                            onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                                        <option></option>
                                        <?php for ($i = 0; $i <= 23; $i++):
                                            $timeString = (strlen((string)$i) == 1 ? '0' . $i : $i) . ':00';
                                            ?>
                                            <option <?= $result_gtab[$tabgroup]["params2"][$tbzm]['minTime'] == $timeString ? 'selected' : '' ?>>
                                                <?= $timeString ?>
                                            </option>
                                        <?php
                                        endfor; ?>
                                    </select>
                                </div>
                    </div>

                    <div class="row pt-2">
                        <?php /* maxTime */ ?>
                        <div class="col">maxTime</div>
                        <div class="col">
                            <select
                                    class="form-select form-select-sm"
                                    name="param2[maxTime]"
                                    onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                                <option></option>
                                <?php for ($i = 0; $i <= 24; $i++):
                                    $timeString = (strlen((string)$i) == 1 ? '0' . $i : $i) . ':00';
                                    ?>
                                    <option <?= $result_gtab[$tabgroup]["params2"][$tbzm]['maxTime'] == $timeString ? 'selected' : '' ?>>
                                        <?= $timeString ?>
                                    </option>
                                <?php
                                endfor; ?>
                            </select>
                        </div>
                    </div>


                    <div class="row pt-2">
                        <?php /* slotminutes */ ?>
                        <div class="col">slotMinutes</div>
                        <div class="col">
                            <select
                                    class="form-select form-select-sm"
                                    name="param2[slotMinutes]"
                                    onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')">
                                <option></option>
                                <?php for ($i = 5; $i <= 240; $i = $i + 5):
                                    $hours = str_pad((string) floor($i / 60), 2, '0', STR_PAD_LEFT);
                                    $remMins = str_pad((string) ($i % 60), 2, '0', STR_PAD_LEFT);
                                    $timeString = "$hours:$remMins";
                                    ?>
                                    <option <?= $result_gtab[$tabgroup]["params2"][$tbzm]['slotMinutes'] == $timeString ? 'selected' : '' ?>>
                                        <?= $timeString ?>
                                    </option>";
                                <?php
                                endfor; ?>
                            </select>
                        </div>
                    </div>

                <div class="row row-cols-4 pt-2">
                            <?php /* editable */ ?>
                                <div class="col">editable</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[editable]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['editable'] ? 'checked' : '' ?>>
                                </div>

                                <?php /* selectable */ ?>
                                <div class="col">selectable</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[selectable]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['selectable'] ? 'checked' : '' ?>>
                                </div>

                            <?php /* weekNumbers */ ?>
                                <div class="col">weekNumbers</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[weekNumbers]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['weekNumbers'] ? 'checked' : '' ?>>
                                </div>

                                <?php /* weekends */ ?>
                                <div class="col">weekends</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[weekends]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['weekends'] ? 'checked' : '' ?>>
                                </div>

                            <?php /* repetition */ ?>
                                <div class="col">repetition</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[repetition]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['repetition'] ? 'checked' : '' ?>>
                                </div>

                                <?php /* defaultAllDay */ ?>
                                <div class="col">defaultAllDay</div>
                                <div class="col d-flex justify-content-end">
                                    <input type="checkbox" value="1" name="param2[defaultAllDay]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['defaultAllDay'] ? 'checked' : '' ?>>
                                </div>

                    <?php /* nowIndicator */ ?>
                    <div class="col">nowIndicator</div>
                    <div class="col d-flex justify-content-end">
                        <input type="checkbox" value="1" name="param2[nowIndicator]" onchange="ajaxEditTable(this,'<?= $tabid ?>','<?= $tabgroup ?>','params2')" <?= $result_gtab[$tabgroup]["params2"][$tbzm]['nowIndicator'] ? 'checked' : '' ?>>
                    </div>

                </div>
                <?php endif;



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
                        <i class="lmb-icon lmb-refresh cursor-pointer" onclick="open('main_admin.php?action=setup_grusrref&check_table=<?=$tabid?>&check_all=1' ,'refresh','toolbar=0,location=0,status=1,menubar=0,scrollbars=1,resizable=1,width=550,height=400')"></i>
                        <small class="form-text text-muted"><?=$lang[1054]?></small>
                    </div>
                </div>

                <?php // rebuild temp ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2761]?></label>
                    <div class="col-sm-8">
                        <i class="lmb-icon lmb-refresh cursor-pointer" onclick="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','tablesync')"></i>
                        <?php if($tablesync): ?>
                            <i class="lmb-icon lmb-aktiv"></i>
                        <?php endif; ?>
                        <small class="form-text text-muted"><?=$lang[2030]?></small>
                    </div>
                </div>

                <?php // rebuild rules ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2662]?></label>
                    <div class="col-sm-8">
                        <i class="lmb-icon lmb-refresh cursor-pointer" onclick="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','tablesequence')"></i>
                        <?php if($tablesequence): ?>
                            <i class="lmb-icon lmb-aktiv"></i>
                        <?php endif; ?>
                        <small class="form-text text-muted"><?=$lang[2662]?></small>

                        <div class="fst-italic text-muted"><small>(
                            <?php
                            echo dbf_4("LMB_".$result_gtab[$tabgroup]['tabelle'][$tbzm]."_ID");
                            ?>
                        </small>)</div>
                    </div>
                </div>

                <?php if($result_gtab[$tabgroup]['checksum'][$tbzm]){  // rebuild data hash ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3156]?></label>
                    <div class="col-sm-8">
                        <i class="lmb-icon lmb-refresh cursor-pointer" onclick="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','rebuild_checksum')"></i>
                        <?php if($rebuild_checksum): ?>
                            <i class="lmb-icon lmb-aktiv"></i>
                        <?php endif; ?>
                        <small class="form-text text-muted"><?=$lang[3156]?> <?=$lang[1038]?></small>
                    </div>
                </div>
                <?php } ?>




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

                    <?php // archive recursiv ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3173]?></label>
                        <div class="col-sm-8">
                            <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['recursiv_archive'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','recursiv_archive')">
                            <small class="form-text text-muted"><?=$lang[3174]?></small>
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

                <?php // data hash ?>
                <div class="mb-3 row">
                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3156]?></label>
                    <div class="col-sm-8">
                        <input type="checkbox" value="1" <?=($result_gtab[$tabgroup]['checksum'][$tbzm] == 1)?'checked':''?> onchange="ajaxEditTable(this,'<?=$tabid?>','<?=$tabgroup?>','checksum')">
                        <small class="form-text text-muted"><?=$lang[3154]?></small>
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


                <?php if (!$isview && $gtrigger[$tabid]): // trigger ?>
                <div class="mb-3 row">

                    <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[2451]?></label>
                    <div class="col-sm-8">
                    <select style="width:100%" class="form-select form-select-sm select2-selection" multiple="multiple" data-tabid="<?=$tabid?>" id="table_trigger">
                        <option value=""></option>
                    <?php
                    foreach($gtrigger[$tabid]["id"] as $trid => $trval):
                        if(in_array($trid,$result_gtab[$tabgroup]['trigger'][$tbzm])){$SELECTED = "SELECTED";}else{$SELECTED = "";} ?>
                        <option value="<?=$trid?>" <?=$SELECTED?>><?=$gtrigger[$tabid]["trigger_name"][$trid]?> (<?=$gtrigger[$tabid]["type"][$trid]?>)</option>
                    <?php endforeach; ?>
                    </select>
                    <small class="form-text text-muted"><?=$lang[2825]?></small>
                    </div>
                </div>
                <?php endif; ?>


                <?php
                    // global filter
                    $gfilter = getFunctionsFromFile('ext_globalFilter');
                    if(is_array($gfilter)){
                    ?>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label col-form-label-sm"><?=$lang[3163]?></label>
                        <div class="col-sm-8">
                        <select style="width:100%" class="form-select form-select-sm select2-selection" multiple="multiple" data-tabid="<?=$tabid?>" id="table_globalfilter">
                            <option value=""></option>
                            <?php
                            foreach($gfilter as $gfkey => $gfname):
                                if(in_array($gfname,$result_gtab[$tabgroup]['globalfilter'][$tbzm])){$SELECTED = "SELECTED";}else{$SELECTED = "";} ?>
                                <option value="<?=$gfname?>" <?=$SELECTED?>><?=$gfname?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted"><?=$lang[3164]?></small>
                        </div>
                    </div>

                <?php } ?>

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
