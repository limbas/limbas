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
 * ID: 104
 */
?>

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


<div class="lmbPositionContainerMainTabPool">
<table class="tabpool" cellspacing="0" cellpadding="0" style="width:700px">

<?php

if ($viewtyp == "tools") { # Tools
	?>

    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td nowrap class="tabpoolItemInactive" onclick="viewPoolTypes('LMB_SELECT')"><?=$lang[1830]?></td>
                    <td nowrap class="tabpoolItemInactive" onclick="viewPoolTypes('LMB_ATTRIBUTE')"><?=$lang[2211]?></td>
                    <td nowrap class="tabpoolItemActive" onclick="document.form1.viewtyp.value='tools';document.form1.submit();">Tools</td>
                    <td class="tabpoolItemSpace">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

	<tr>
        <td class="tabpoolfringe">
            <table cellspacing="1" cellpadding="2" class="tabBody">

                <tr><td><?=$lang[1830]?>:  <b><?= $result_pool['name'][$pool] ?></></td></tr>
                <tr><td><hr></td></tr>
	        	<tr><td><?= $lang[1040] ?><i> (<?=$lang[1842]?>, tab delimited)</i></td></tr>
                <tr><td><input type="file" name="select_import"> <?=$lang[1003]?> <input type="checkbox" name="select_import_add" style="border:none; background-color:transparent;" checked></td></tr>
	            <tr><td><input type="submit" name="select_import" value="importieren"></td></tr>
        	</table>
        </td>
    </tr>
<?php

} elseif($pool) { # Pool entries
	if($gtabid){$atid = $gtabid;}
    $hasMultiLang = ($gfield[$atid]['multilang'][$fieldid] OR $umgvar['multi_language']);
    $numLangCols = $hasMultiLang ? count($umgvar['multi_language']) : 0;
    $attributeTypes = array(
        '1' => 'text',
        '16' => 'int',
        '49' => 'float',
        '20' => 'bool',
        '40' => 'date',
        '12' => 'pool'
    );

    ?>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td nowrap class="<?= $typ === 'LMB_ATTRIBUTE' ? 'tabpoolItemInactive ' : 'tabpoolItemActive'?>" onclick="viewPoolTypes('LMB_SELECT')"><?=$lang[1830]?></td>
                    <td nowrap class="<?= $typ === 'LMB_ATTRIBUTE' ? 'tabpoolItemActive ' : 'tabpoolItemInactive'?>" onclick="viewPoolTypes('LMB_ATTRIBUTE')"><?=$lang[2211]?></td>
                    <td nowrap class="tabpoolItemInactive" onclick="document.form1.viewtyp.value='tools';document.form1.submit();">Tools</td>
                    <td class="tabpoolItemSpace">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="tabpoolfringe">
            <table cellspacing="1" cellpadding="2" width="100%" class="tabBody">
                <tr>
                    <td></td>
                    <td><input type="text" style="width:130px;" name="select_pool[<?=$pool?>]" value="<?= $result_pool['name'][$pool] ?>" onchange="document.form1.rename_pool.value=<?= $pool ?>;"></td>
                	<td nowrap colspan="<?= 3 + $numLangCols ?>"><b><?= $result_fieldselect['num_ges'] ?></b>&nbsp;<?=$lang[1843]?>,&nbsp;<?=$lang[1844]?>&nbsp;<B><?=$result_fieldselect['num_rows'];?></B>&nbsp;<?=$lang[1846]?>
                        | <input type="hidden" id="showAllLevels" name="showAllLevels" value="<?= $showAllLevels ? '1' : '' ?>"><a onclick="if(document.getElementById('showAllLevels').value=='1'){document.getElementById('showAllLevels').value = '';}else{document.getElementById('showAllLevels').value='1'};document.form1.level_id.value=0;document.form1.submit();"><?= $lang[2288] ?></a>
                    </td>
                    <td colspan="3" nowrap>
                    <input type="checkbox" <?= $result_pool['tag_mode'][$pool] ? 'checked' : '' ?> onchange="document.form1.tag_mode.value=1;document.form1.submit();">TAG-Mode
                    <input type="checkbox" <?= $result_pool['multi_mode'][$pool] ? 'checked' : '' ?> onchange="document.form1.multi_mode.value=1;document.form1.submit();">Multiple-Mode
                    </td>
                </tr>
                <?php if ($atid and $fieldid) { ?>
                    <tr>
                        <td></td>
                        <td colspan="<?= 6 + $numLangCols ?>"><?= $lang[164] ?>: <b><?= $gtab['desc'][$atid] ?></b> | <?= $lang[168] ?>: <b><?= $gfield[$atid]['spelling'][$fieldid] ?></b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><?=$lang[1837]?></td>
                        <td colspan="<?= 4 + $numLangCols ?>">
                            <select style="width:130px;" name="fssort" onchange="document.form1.select_sort.value='1'; document.form1.submit();">
                                <?php if ($typ == 'LMB_ATTRIBUTE') {?><option value="" <?= $result_fieldselect['sort'] == '' ? 'selected' : '' ?>><?=$lang[1838]?></option><?php } ?>
                                <option value="SORT" <?= $result_fieldselect['sort'] == 'SORT' ? 'selected' : '' ?>><?=$lang[1837]?></option>
                                <option value="WERT ASC" <?= $result_fieldselect['sort'] == 'WERT ASC' ? 'selected' : '' ?>><?=$lang[1840]?></option>
                                <option value="WERT DESC" <?= $result_fieldselect['sort'] == 'WERT DESC' ? 'selected' : '' ?>><?=$lang[1839]?></option>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="<?= 11 + $numLangCols ?>"><hr></td>
                </tr>
                <tr class="tabHeader">
                    <td></td>
                    <td><input type="text" style="width:130px;" name="find_wert" value="<?= htmlentities($find_wert, ENT_QUOTES, $umgvar['charset']) ?>"></td>
                    <?= str_repeat('<td></td>', $numLangCols) ?>
                    <td><input type="text" style="width:130px;" name="find_keyw" value="<?= htmlentities($find_keyw, ENT_QUOTES, $umgvar['charset']) ?>"></td>
                    <td><input type="text" style="width:40px;" name="num_result" value="<?= $num_result ?>"></td>
                    <td colspan="2"><input type="submit" value="<?= $lang[30] ?>" name="search_select"></td>
                </tr>
                <tr class="tabHeader">
                    <td class="tabHeaderItem">ID</td>
                    <td class="tabHeaderItem"><?= $lang[29] ?></td>
                    <?php
                    if($hasMultiLang){
                        require_once('admin/setup/language.lib');
                        $langdef = get_language_list();
                        foreach($umgvar['multi_language'] as $lkey => $langid){
                            echo '<td class="tabHeaderItem">'.$langdef['language'][$langid].'</td>';
                        }
                    }
                    ?>
                    <td class="tabHeaderItem"><?= $lang[27] ?></td>
                    <td class="tabHeaderItem"><?= $lang[1614] ?></td>

                    <?php if ($typ == 'LMB_ATTRIBUTE') {?>
                        <td class="tabHeaderItem"><?= $lang[2211] ?></td>
                        <td align="center" class="tabHeaderItem"><?= $lang[294] ?></td>
                        <td class="tabHeaderItem"><?= $lang[2955] ?></td>
                        <td class="tabHeaderItem"><?= $lang[2720] ?></td>
                    <?php } ?>
                    <td class="tabHeaderItem"><?= $lang[633] ?></td>
                    <td class="tabHeaderItem"><?= $lang[2956] ?></td>
                    <td class="tabHeaderItem"></td>

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

                # Ergebnisliste
                if ($result_fieldselect['id']) {
                    foreach ($result_fieldselect['id'] as $key => $entryID) {
                        ?>
                        <tr>
                            <td><?= $result_fieldselect['id'][$key] ?></td>
                            <td><input type="text" style="width:130px;" name="select_wert[<?= $entryID ?>]" value="<?= $result_fieldselect['wert'][$key] ?>" onchange="document.form1.select_change.value=document.form1.select_change.value+';<?= $entryID ?>';"></td>
                            <?php
                            # multilang
                            if ($hasMultiLang) {
                                foreach ($umgvar['multi_language'] as $lkey => $langid) {
                                     echo "<td><input type=\"text\" style=\"width:130px;\" name=\"select_wert_{$langid}[{$entryID}]\" value=\"{$result_fieldselect['wert_'.$langid][$key]}\" onchange=\"document.form1.select_change.value=document.form1.select_change.value+';{$entryID}';\"></td>";
                                }
                            }
                            ?>
                            <td nowrap><input type="text" style="width:130px;" name="select_keyw[<?= $entryID ?>]" value="<?= $result_fieldselect['keywords'][$key] ?>" onchange="document.form1.select_change.value=document.form1.select_change.value+';<?= $entryID ?>';"></td>
			                <td nowrap>
                                <?php if ($result_fieldselect['def'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_default.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                            <?php if ($result_fieldselect['sort'] == 'SORT' OR !$result_fieldselect['sort']) { ?>
                                <i class="lmb-icon lmb-long-arrow-up" style="cursor:pointer" onclick="document.form1.select_sort_d.value=1;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                                <i class="lmb-icon lmb-long-arrow-down" style="cursor:pointer" onclick="document.form1.select_sort_d.value=2;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                                &nbsp;&nbsp;
                            <?php } ?>
                            </td>
                            <?php if ($typ == 'LMB_ATTRIBUTE') {
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

                                <TD nowrap align="center">
                                <div id="select_color_div_<?= $entryID ?>" OnClick="selectColor(this, event,'<?=$entryID?>')" STYLE="cursor:pointer;width:20px;height:20px;border:1px solid black;background-color:<?= $result_fieldselect['color'][$key];?>"></div>
                                <input type="hidden" name="select_color[<?= $entryID ?>]" id="select_color_<?= $entryID ?>" value="<?= $result_fieldselect['color'][$key] ?>">
                                </TD>

                                <td nowrap align="center">
                                <?php if($result_fieldselect['type'][$key] > 0){?>
                                <?php if ($result_fieldselect['mandatory'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_mandatory.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                                <?php }?>
                                </td>

                                <td nowrap align="center">
                                <?php if($result_fieldselect['type'][$key] > 0){?>
                                <?php if ($result_fieldselect['hidetag'][$key]){$CHECKED = 'CHECKED';} else {$CHECKED = '';}?>
                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_hidetag.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                                <?php } ?>
                                </td>


                            <?php } ?>

			                <td align="center"><input type="checkbox" <?= $result_fieldselect['hide'][$key] ? 'checked' : '' ?> onclick="document.form1.select_hide.value='<?= $entryID ?>';document.form1.submit();">&nbsp;</td>
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
		            }
                }
                ?>
                <tr>
                    <td></td>
                    <td valign="top"><input type="text" style="width:130px;" name="new_wert"></td>
                    <?= str_repeat('<td></td>', $numLangCols) ?>
                    <td valign="top"><input type="text" style="width:130px;" name="new_keyword"></td>
                    <?php if($typ == 'LMB_ATTRIBUTE') { ?>
                        <td></td><td colspan="3" valign="top"><select style="width:80px" name="new_fieldtype" onchange="if(this.value=='12'){$('#new_attrpool').show();}else{$('#new_attrpool').hide();};"><option value="0"><?=$lang[1246]?></option><option value="-1">-----------</option>
                                <?php
                                foreach ($attributeTypes as $type => $name) {
                                    echo "<option value=\"$type\">$name</option>";
                                }
                                ?>
                            </select>
                        <br><select name="new_attrpool" id="new_attrpool" style="display:none">
                            <?php
                                foreach($result_subpool['name'] as $spkey => $spname){
                                    echo "<option value=\"" . $spkey . "\">" . $spname . "</option>";
                                }
                            ?>
                        </select>
                        </td>
                    <?php } ?>
                    <td colspan="3" valign="top" align="right"><input type="submit" value="<?= $lang[34] ?>" name="add_select"></td>
                </tr>
                <tr>
                    <td colspan="<?= 11 + $numLangCols ?>"><hr></td>
                </tr>
                <tr class="tabFooter">
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
                    <td colspan="2"><input type="submit" value="<?=$lang[842]?>"></td>
                </tr>
            </table>
        </td>
    </tr>

<?php

} else { # pool overwiew

	?>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td nowrap class="<?= $typ === 'LMB_ATTRIBUTE' ? 'tabpoolItemInactive ' : 'tabpoolItemActive'?>" onclick="viewPoolTypes('LMB_SELECT')"><?=$lang[1830]?></td>
                    <td nowrap class="<?= $typ === 'LMB_ATTRIBUTE' ? 'tabpoolItemActive ' : 'tabpoolItemInactive'?>" onclick="viewPoolTypes('LMB_ATTRIBUTE')"><?=$lang[2211]?></td>
                    <td class="tabpoolItemSpace">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
	<tr>
        <td class="tabpoolfringe">
	        <table cellspacing="0" cellpadding="3" width="100%" class="tabfringe hoverable">
                <tr class="tabHeader">
                    <td class="tabHeaderItem">ID</td>
                    <td class="tabHeaderItem"><?= $lang[4] ?></td>
                    <td class="tabHeaderItem"><?= $lang[86] ?></td>
                    <td class="tabHeaderItem"><?= $lang[1856] ?></td>
                    <td colspan="2"> </td>
                </tr>
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
                        <tr class="tabBody" style="background-color: <?= $bgcolor ?>">
                            <td nowrap style="width:40px;vertical-align:top" <?= $selectOnClick ?>><?= $result_pool['id'][$key] ?></td>
                            <td nowrap style="vertical-align:top;cursor:pointer"<?= $selectOnClick ?>><?= htmlentities($result_pool['name'][$key], ENT_QUOTES, $umgvar['charset']) ?></td>
                            <td nowrap style="vertical-align:top"><?= $result_pool['num'][$key] ?></td>
                            <td style="vertical-align:top;max-width:300px">
                            <?php
                            $isused = array();
                            foreach($gfield as $key0 => $value0){
                                foreach($value0['field_type'] as $key1 => $value1) {
                                    if($value1 == $pooltype AND $value0['select_pool'][$key1] == $key){
                                        $isused[] =  "<a onclick=\"document.location.href='main_admin.php?action=setup_gtab_ftype&atid=$key0'\";>".$gtab['desc'][$key0]."</a>";
                                    }
                                }
                            }
                            echo implode(array_unique($isused),', ');
                            ?>
                            </td>

                            <td nowrap style="width:40px;text-align:center"><i class="lmb-icon lmb-trash" onclick="deletePool(<?= $key ?>)" style="cursor:pointer;width:20px"></i></td>
                            <td nowrap>
                            <?php if ($atid) { ?>
                                <input type="radio" name="aktive_pool" title="<?= $lang[2226] ?>" style="border:none; background-color:transparent;" value="<?= $key ?>" onclick="document.form1.set_pool.value='<?= $key ?>'; document.form1.submit();" <?= $CHECKED ?>>
                            <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr><td colspan="5"><hr></td></tr>
                <tr>
                    <td></td>
                    <td><input type="text" title="<?= $lang[4] ?>" style="width:100%;" name="new_wert"></td>
                    <td colspan="3"><input type="submit" name="add_pool" value="<?= $lang[1832] ?>" style="cursor: pointer;"></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php

}

?>

</table>
</div>
</form>