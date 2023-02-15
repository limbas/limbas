<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<script>
    function viewPoolTypes(poolType) {
        document.form1.typ.value = poolType;
        document.form1.viewtyp.value = 'pool';
        document.form1.pool.value = '';
        document.form1.level_id.value = '';
        document.form1.start.value = 1;
        document.form1.submit();
    }

    function setLevel(levelID) {
        document.form1.level_id.value = levelID;
        document.form1.start.value = 1;
        document.form1.submit();
    }

    function deletePoolEntry(entryID) {
        if (confirm('<?= $lang[84] ?>')) {
            document.form1.select_del.value = entryID;
            document.form1.submit();
        }
    }

    function deletePool(poolID) {
        if (confirm('<?= $lang[84] ?>')) {
            document.form1.del_pool.value = poolID;
            document.form1.submit();
        }
    }

    // --- Farbmenüsteuerung -----------------------------------
    var selectid;
    function selectColor(el, event, id) {
        selectid = id;
        setxypos(event,'menu_color');
        limbasDivShow(el,null,'menu_color');
    }

    function set_color(color){
        if(!color){color = "transparent";}
        document.getElementById('select_color_'+selectid).value = color;
        document.getElementById('select_color_div_'+selectid).style.backgroundColor = color;
        document.form1.select_change.value=document.form1.select_change.value+';'+selectid;

        limbasDivHide(null,'menu_color');
    }

</script>


<DIV ID="menu_color" class="lmbContextMenu" style="position:absolute;display:none;z-index:10002;">
<TABLE BORDER="0" cellspacing="0" cellpadding="0">
<TR><TD><?php pop_top('menu_color');?></TD></TR>
<TR><TD>
<?php pop_color(null, null, 'menu_color');?>
</TD></TR>
<TR><TD><?php pop_bottom();?></TD></TR>
</TABLE></DIV>


<form enctype="multipart/form-data" action="main_admin.php" method="post" name="form1">
<input type="hidden" name="MAX_FILE_SIZE" value="<?=$session['uploadsize']?>">
<input type="hidden" name="action" value="setup_fieldselect">
<input type="hidden" name="atid" value="<?= $atid ?>">
<input type="hidden" name="fieldid" value="<?= $fieldid ?>">
<input type="hidden" name="ID" value="<?= $ID ?>">
<input type="hidden" name="pool" value="<?= $pool ?>">
<input type="hidden" name="field_pool" value="<?= $field_pool ?>">
<input type="hidden" name="num_result" value="<?= $num_result ?>">
<input type="hidden" name="start" value="<?= $start ?>">
<input type="hidden" name="typ" value="<?=$typ?>">
<input type="hidden" name="del_pool">
<input type="hidden" name="rename_pool">
<input type="hidden" name="desc_pool">
<input type="hidden" name="select_change">
<input type="hidden" name="select_wert_change">
<input type="hidden" name="select_keyw_change">
<input type="hidden" name="select_sort">
<input type="hidden" name="select_sort_d">
<input type="hidden" name="select_del">
<input type="hidden" name="select_default">
<input type="hidden" name="select_hide">
<input type="hidden" name="select_mandatory">
<input type="hidden" name="select_hidetag">
<input type="hidden" name="select_attribut">
<input type="hidden" name="set_pool">
<input type="hidden" name="viewtyp">
<input type="hidden" name="tag_mode">
<input type="hidden" name="multi_mode">

<input type="hidden" name="level_id" value="<?= isset($level_id) ? $level_id : 0 ?>">


    <div class="container-fluid p-3">
        
        
        <ul class="nav nav-tabs">
            <?php if(($fieldid AND $result_fieldselect['field_type'] == 4) OR !$fieldid){?>
            <li class="nav-item">
                <a class="nav-link <?= ($typ !== 'LMB_ATTRIBUTE' && $viewtyp != 'tools') ? 'active bg-white' : ''?>" href="#" onclick="viewPoolTypes('LMB_SELECT')"><?=$lang[1830]?></a>
            </li>
            <?php }?>
            <?php if(($fieldid AND $result_fieldselect['field_type'] == 19) OR !$fieldid){?>
            <li class="nav-item">
                <a class="nav-link <?= ($typ === 'LMB_ATTRIBUTE' && $viewtyp != 'tools') ? 'active bg-white' : ''?>" href="#" onclick="viewPoolTypes('LMB_ATTRIBUTE')"><?=$lang[2211]?></a>
            </li>
            <?php }?>
            <?php if($viewtyp == 'tools' || $pool) : ?>

            <li class="nav-item">
                <a class="nav-link <?= ($viewtyp == 'tools') ? 'active bg-white ' : ''?>" href="#" onclick="document.form1.viewtyp.value='tools';document.form1.submit();">Tools</a>
            </li>
            
            <?php endif; ?>
            
        </ul>
        
        
        <div class="tab-content border border-top-0 bg-white">
            <div class="tab-pane active">
                
                
                <?php if ($viewtyp == "tools") : # Tools ?>
                    <div class="p-3">
                        <p><?=$lang[1830]?>: <span class="fw-bold"><?= $result_pool['name'][$pool] ?></span></p>
                        <div class="mb-3">
                            <label for="select_import"><?= $lang[1040] ?> (<?=$lang[1842]?>, tab delimited)</label><br>
                            <input type="file" name="select_import" id="select_import"> <label class="ms-3"><input type="checkbox" name="select_import_add" checked> <?=$lang[1003]?></label>
                        </div>

                        <button type="submit" class="btn btn-primary">importieren</button>
                    </div>
                    
                <?php


                elseif($pool) : # Pool entries

                    if($gtabid){$atid = $gtabid;}
                    $hasMultiLang = ($gfield[$atid]['multilang'][$fieldid] OR $umgvar['multi_language']);
                    $numLangCols = $hasMultiLang ? lmb_count($umgvar['multi_language']) : 0;
                    $attributeTypes = array(
                        '1' => 'text',
                        '16' => 'int',
                        '49' => 'float',
                        '20' => 'bool',
                        '40' => 'date',
                        '12' => 'pool'
                    );

                    ?>

                    <div class="p-3">
                        <div class="row mb-3">
                            <div class="col-5">
                                <?= $lang[164] ?>: <span class="fw-bold"><?= $gtab['desc'][$atid] ?></span>
                                | <?= $lang[168] ?>: <span
                                        class="fw-bold"><?= $gfield[$atid]['spelling'][$fieldid] ?></span>
                            </div>
                            <div class="col text-end">
                                <span class="fw-bold"><?= $result_fieldselect['num_ges'] ?></span>&nbsp;<?= $lang[1843] ?>
                                ,&nbsp;<?= $lang[1844] ?>&nbsp;<span
                                        class="fw-bold"><?= $result_fieldselect['num_rows']; ?></span>&nbsp;<?= $lang[1846] ?>
                                | <input type="hidden" id="showAllLevels" name="showAllLevels"
                                         value="<?= $showAllLevels ? '1' : '' ?>"><a
                                        onclick="if(document.getElementById('showAllLevels').value=='1'){document.getElementById('showAllLevels').value = '';}else{document.getElementById('showAllLevels').value='1'}document.form1.level_id.value=0;document.form1.submit();"><?= $lang[2288] ?></a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <?= $lang[1830] ?><br> <input type="text" name="select_pool[<?= $pool ?>]"
                                                          value="<?= $result_pool['name'][$pool] ?>"
                                                          onchange="document.form1.rename_pool.value=<?= $pool ?>;"
                                                          class="form-control form-control-sm">
                            </div>

                            <div class="col-2">
                                <?= $lang[1837] ?><br>
                                <select name="fssort"
                                        onchange="document.form1.select_sort.value='1'; document.form1.submit();"
                                        class="form-select form-select-sm d-inline w-75">
                                    <?php if ($typ == 'LMB_ATTRIBUTE') { ?>
                                        <option
                                        value="" <?= $result_fieldselect['sort'] == '' ? 'selected' : '' ?>><?= $lang[1838] ?></option><?php } ?>
                                    <option value="SORT" <?= $result_fieldselect['sort'] == 'SORT' ? 'selected' : '' ?>><?= $lang[1837] ?></option>
                                    <option value="WERT ASC" <?= $result_fieldselect['sort'] == 'WERT ASC' ? 'selected' : '' ?>><?= $lang[1840] ?></option>
                                    <option value="WERT DESC" <?= $result_fieldselect['sort'] == 'WERT DESC' ? 'selected' : '' ?>><?= $lang[1839] ?></option>
                                </select>
                            </div>

                            <?php if ($result_fieldselect['field_type'] == 19 or $result_fieldselect['data_type'] == 32) { ?>
                                <div class="col text-end">
                                    <input type="checkbox" <?= $result_pool['tag_mode'][$pool] ? 'checked' : '' ?>
                                           onchange="document.form1.tag_mode.value=1;document.form1.submit();"
                                           class="me-2">TAG-Mode
                                    <input type="checkbox" <?= $result_pool['multi_mode'][$pool] ? 'checked' : '' ?>
                                           onchange="document.form1.multi_mode.value=1;document.form1.submit();"
                                           class="ms-3 me-2">Multiple-Mode
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <hr>
                


                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <td></td>
                                <td><input type="text" name="find_wert" value="<?= htmlentities($find_wert, ENT_QUOTES, $umgvar['charset']) ?>" class="form-control form-control-sm"></td>
                                <?= str_repeat('<td></td>', $numLangCols) ?>
                                <td><input type="text" name="find_keyw" value="<?= htmlentities($find_keyw, ENT_QUOTES, $umgvar['charset']) ?>" class="form-control form-control-sm"></td>
                                <td><input type="text" name="num_result" value="<?= $num_result ?>" class="form-control form-control-sm"></td>
                                <td colspan="3"><input type="submit" value="<?= $lang[30] ?>" name="search_select" class="form-control form-control-sm"></td>
                            </tr>
                            <tr>
                                <th>ID</th>
                                <th><?= $lang[29] ?></th>
                                <?php
                                if($hasMultiLang){
                                    require_once(COREPATH . 'admin/setup/language.lib');
                                    $langdef = get_language_list();
                                    foreach($umgvar['multi_language'] as $lkey => $langid){
                                        echo '<th>'.$langdef['language'][$langid].'</th>';
                                    }
                                }
                                ?>
                                <th><?= $lang[27] ?></th>
                                <th><?= $lang[1614] ?></th>

                                <?php if ($typ == 'LMB_ATTRIBUTE') {?>
                                    <th><?= $lang[2211] ?></th>
                                    <th><?= $lang[294] ?></th>
                                    <th><?= $lang[2955] ?></th>
                                    <th><?= $lang[2720] ?></th>
                                <?php } ?>
                                <th><?= $lang[633] ?></th>
                                <th><?= $lang[2956] ?></th>
                                <th></th>

                            </tr>

                            <?php
                            # parent history
                            if($result_fieldselect['parent'] && (!empty($result_fieldselect['parent'])) && is_array($result_fieldselect['parent'])){
                                echo '<tr><td colspan="' . (6 + $numLangCols) . '"><a href="#" onclick="setLevel(\'\');return false;" style="font-weight:bold;">' . $result_pool['name'][$pool] . '</a>';
                                $result_fieldselect['parent'] = array_reverse($result_fieldselect['parent'], true);
                                $last = array_pop($result_fieldselect['parent']);
                                foreach ($result_fieldselect['parent'] as $parentID => $parentName){
                                    echo " > <a href=\"#\" onclick=\"setLevel($parentID);document.form1.submit();return false;\" style=\"font-weight:bold;\" title=\"$parentName\">$parentName</a>";
                                }
                                echo " > $last</td></tr>";
                            }
                            ?>
                        
                        
                        </thead>

                        

                        <?php

                        # Ergebnisliste
                        if ($result_fieldselect['id']) :
                            foreach ($result_fieldselect['id'] as $key => $entryID) :
                                ?>
                                <tr>
                                    <td><?= $result_fieldselect['id'][$key] ?></td>
                                    <td><input type="text" name="select_wert[<?= $entryID ?>]" value="<?= $result_fieldselect['wert'][$key] ?>" onchange="document.form1.select_change.value=document.form1.select_change.value+';<?= $entryID ?>';" class="form-control form-control-sm"></td>
                                    <?php
                                    # multilang
                                    if ($hasMultiLang) {
                                        foreach ($umgvar['multi_language'] as $lkey => $langid) {
                                            echo "<td><input type=\"text\" name=\"select_wert_{$langid}[{$entryID}]\" value=\"{$result_fieldselect['wert_'.$langid][$key]}\" onchange=\"document.form1.select_change.value=document.form1.select_change.value+';{$entryID}';\"  class=\"form-control form-control-sm\"></td>";
                                        }
                                    }
                                    ?>
                                    <td nowrap><input type="text" name="select_keyw[<?= $entryID ?>]" value="<?= $result_fieldselect['keywords'][$key] ?>" onchange="document.form1.select_change.value=document.form1.select_change.value+';<?= $entryID ?>';" class="form-control form-control-sm"></td>
                                    <td nowrap>
                                        <?php if ($result_fieldselect['def'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                        <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_default.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                                        <?php if ($result_fieldselect['sort'] == 'SORT' OR !$result_fieldselect['sort']) { ?>
                                            <i class="lmb-icon lmb-long-arrow-up" style="cursor:pointer" onclick="document.form1.select_sort_d.value=1;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                                            <i class="lmb-icon lmb-long-arrow-down" style="cursor:pointer" onclick="document.form1.select_sort_d.value=2;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                                            &nbsp;&nbsp;
                                        <?php } ?>
                                    </td>
                                    <?php if ($typ == 'LMB_ATTRIBUTE') :
                                        ?>
                                    
                                    
                                        <td nowrap title="<?=$title?>">
                                            <div style="max-width:100px"><?php
                                                if($result_fieldselect['attrpool'][$key] AND $result_fieldselect['type'][$key] == 12){
                                                    $title = '('.$result_subpool['name'][$result_fieldselect['attrpool'][$key]].')';
                                                }else{$title = '';}
                                                echo $attributeTypes[$result_fieldselect['type'][$key]]. ' '. $title;
                                                ?>
                                            </div>
                                        </td>

                                        <TD>
                                            <div id="select_color_div_<?= $entryID ?>" OnClick="selectColor(this, event,'<?=$entryID?>')" STYLE="cursor:pointer;width:20px;height:20px;border:1px solid black;background-color:<?= $result_fieldselect['color'][$key];?>"></div>
                                            <input type="hidden" name="select_color[<?= $entryID ?>]" id="select_color_<?= $entryID ?>" value="<?= $result_fieldselect['color'][$key] ?>">
                                        </TD>

                                        <td>
                                            <?php if($result_fieldselect['type'][$key] > 0){?>
                                                <?php if ($result_fieldselect['mandatory'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_mandatory.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                                            <?php }?>
                                        </td>

                                        <td>
                                            <?php if($result_fieldselect['type'][$key] > 0){?>
                                                <?php if ($result_fieldselect['hidetag'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_hidetag.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                                            <?php } ?>
                                        </td>


                                    <?php endif; ?>

                                    <td><input type="checkbox" <?= $result_fieldselect['hide'][$key] ? 'checked' : '' ?> onclick="document.form1.select_hide.value='<?= $entryID ?>';document.form1.submit();">&nbsp;</td>
                                    <td nowrap>
                                        <?php
                                        global $gfield;
                                        if (!$fieldid or in_array($gfield[$atid]['data_type'][$fieldid], array(32 /* multiselect ajax */, 46 /* attribute */))) {
                                            $imgst = 'style="cursor:pointer;"';
                                            if (!$result_fieldselect['haslevel'][$key]) {
                                                $imgst = 'style="cursor:pointer; opacity:0.3;"';
                                            }
                                            echo "<i class=\"lmb-icon lmb-connection\" onclick=\"setLevel($entryID)\" $imgst></i>";
                                        }
                                        ?>
                                    </td><td>
                                        <i class="lmb-icon lmb-trash" style="cursor:pointer" onclick="deletePoolEntry(<?= $entryID ?>);"></i>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        endif;
                        ?>

                        <tfoot>


                        <tr>
                            <td></td>
                            <td><input type="text" name="new_wert" class="form-control form-control-sm"></td>
                            <?= str_repeat('<td></td>', $numLangCols) ?>
                            <td><input type="text" name="new_keyword" class="form-control form-control-sm"></td>
                            <?php if($typ == 'LMB_ATTRIBUTE') { ?>
                                <td></td><td colspan="3">
                                    <select name="new_fieldtype" onchange="if(this.value=='12'){$('#new_attrpool').show();}else{$('#new_attrpool').hide();};"  class="form-select form-select-sm"><option value="0"><?=$lang[1246]?></option><option value="-1">-----------</option>
                                        <?php
                                        foreach ($attributeTypes as $type => $name) {
                                            echo "<option value=\"$type\">$name</option>";
                                        }
                                        ?>
                                    </select>
                                    <select name="new_attrpool" id="new_attrpool" style="display:none" class="form-select form-select-sm">
                                        <?php
                                        foreach($result_subpool['name'] as $spkey => $spname){
                                            echo "<option value=\"" . $spkey . "\">" . $spname . "</option>";
                                        }
                                        ?>
                                    </select>
                                </td>
                            <?php } ?>
                            <td colspan="4">
                                <button class="btn btn-sm btn-primary" type="submit" name="add_select" value="1"><?= $lang[34] ?></button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="<?= 11 + $numLangCols ?>"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="<?= 4 + $numLangCols ?>">
                                <?php
                                $currentPage = ceil($start / $num_result);
                                $maxPage = max(1, ceil($result_fieldselect['num_ges'] / $num_result));
                                ?>
                                <i class="lmb-icon lmb-first" style="<?= $start == 1 ? 'color: gray;' : 'cursor:pointer;' ?>" onclick="document.form1.start.value='1';document.form1.submit();"></i>
                                <i class="lmb-icon lmb-previous" style="font-size:1.5em;<?= $start == 1 ? 'color: gray;' : 'cursor:pointer;' ?>" onclick="document.form1.start.value='<?= max(1, $start - $num_result) ?>';document.form1.submit();"></i>&nbsp;
                                <?= $currentPage ?> / <?= $maxPage ?>
                                <i class="lmb-icon lmb-next" style="font-size:1.5em;<?= $currentPage == $maxPage ? 'color: gray;' : 'cursor: pointer;' ?>"  onclick="document.form1.start.value='<?= $currentPage == $maxPage ? $start : ($start + $num_result) ?>';document.form1.submit();"></i>
                                <i class="lmb-icon lmb-last" style="<?= $currentPage == $maxPage ? 'color: gray;' : 'cursor: pointer;' ?>"  onclick="document.form1.start.value='<?= $currentPage == $maxPage ? $start : max(1, (($maxPage - 1) * $num_result) + 1) ?>';document.form1.submit();"></i>
                            </td>
                            <td colspan="2"><button class="btn btn-sm btn-primary" type="submit" value="1"><?=$lang[842]?></button></td>
                        </tr>
                        
                        
                        
                        
                        
                        
                        
                        <?php if(false):?>
                        <tr class="border-bottom border-top">
                            <td colspan="6">
                                <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[522] ?></button>
                            </td>
                        </tr>

                        <tr>
                            <th><?php echo $lang[926];?></th>
                            <th><?php echo $lang[29];?></th>
                            <th><?php echo $lang[126];?></th>
                            <th><?php echo $lang[2957];?></th>
                            <th><?php echo $lang[632];?></th>
                            <th></th>
                        </tr>

                        <tr>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="name"></td>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="value"></td>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="70" NAME="description"></td>
                            <td><INPUT TYPE="checkbox" NAME="overridable" value="1"></td>
                            <td><INPUT TYPE="checkbox" NAME="active" value="1"></td>
                            <td><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
                        </tr>
                        <?php endif; ?>
                        </tfoot>
                    </table>


                <?php else: # pool overwiew ?>

                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th class="border-top-0">ID</th>
                                <th class="border-top-0"><?php echo $lang[4];?></th>
                                <th class="border-top-0"><?php echo $lang[86];?></th>
                                <th class="border-top-0"><?php echo $lang[1856];?></th>
                                <th class="border-top-0" colspan="2"></th>
                            </tr>
                        </thead>

                        <?php

                        $pooltype = 4;
                        if($typ == 'LMB_ATTRIBUTE'){$pooltype = 19;}

                        if ($result_pool['id']) {
                            foreach ($result_pool['id'] as $key => $value) {
                                $selectOnClick = 'onclick="document.form1.pool.value=\'' . $key  . '\'; document.form1.submit();" style="cursor:pointer;"';
                                if ($field_pool == $key) {
                                    $bgcolor = $farbschema['WEB7'];
                                    $CHECKED = 'CHECKED';
                                } else {
                                    $bgcolor = '';
                                    $CHECKED = '';
                                }
                                ?>
                                <tr>
                                    <td <?= $selectOnClick ?>><?= $result_pool['id'][$key] ?></td>
                                    <td <?= $selectOnClick ?>><?= htmlentities($result_pool['name'][$key], ENT_QUOTES, $umgvar['charset']) ?></td>
                                    <td><?= $result_pool['num'][$key] ?></td>
                                    <td>
                                        <?php
                                        $isused = array();
                                        foreach($gfield as $key0 => $value0){
                                            foreach($value0['field_type'] as $key1 => $value1) {
                                                if($value1 == $pooltype AND $value0['select_pool'][$key1] == $key){
                                                    $isused[] =  "<a onclick=\"document.location.href='main_admin.php?action=setup_gtab_ftype&atid=$key0'\";>".$gtab['desc'][$key0]."</a>";
                                                }
                                            }
                                        }
                                        echo implode(', ',array_unique($isused));
                                        ?>
                                    </td>

                                    <td><i class="lmb-icon lmb-trash" onclick="deletePool(<?= $key ?>)"></i></td>
                                    <td>
                                        <?php if ($atid) { ?>
                                            <input type="radio" name="aktive_pool" title="<?= $lang[2226] ?>" value="<?= $key ?>" onclick="document.form1.set_pool.value='<?= $key ?>'; document.form1.submit();" <?= $CHECKED ?>>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        
                        <tfoot>
                        <tr>
                            <td></td>
                            <td><input type="text" title="<?= $lang[4] ?>" class="form-control form-control-sm" name="new_wert"></td>
                            <td colspan="3"><button class="btn btn-sm btn-primary" type="submit" name="add_pool" value="1"><?= $lang[1832] ?></button></td>
                        </tr>
                        </tfoot>
                    </table>

                <?php endif; ?>
                
                
                
                
                

            </div>
        </div>
    </div>
    
</form>
