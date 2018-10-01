<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 104
 */
?>


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
<input type="hidden" name="set_pool">
<input type="hidden" name="viewtyp">
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
</script>

<div class="lmbPositionContainerMainTabPool">
<table class="tabpool" cellspacing="0" cellpadding="0" style="width:500px">


<?php
if ($viewtyp == "filter") { # Tools
	?>
    <tr>
        <td>
            <table cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td nowrap class="tabpoolItemActive" onclick="document.form1.viewtyp.value='pool';document.form1.submit();"><?= $result_pool['name'][$pool] ?></td>
                    <td class="tabpoolItemSpace">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
	<tr>
        <td class="tabpoolfringe">
            <table cellspacing="1" cellpadding="2" class="tabBody">
	        	<tr><td><?= $lang[1040] ?><i> (<?=$lang[1842]?>, tab delimited)</i></td></tr>
                <tr><td><input type="file" name="select_import"> <?=$lang[1003]?> <input type="checkbox" name="select_import_add" style="border:none; background-color:transparent;" checked></td></tr>
	            <tr><td><input type="submit" name="select_import" value="importieren"></td></tr>
        	</table>
        </td>
    </tr>
<?php

} elseif($pool) { # Pool entries
	if($gtabid){$atid = $gtabid;}
    $hasMultiLang = ($gfield[$atid]['multilang'][$fieldid] OR !$fieldid) AND $umgvar['multi_language'];
    $numLangCols = $hasMultiLang ? count($umgvar['multi_language']) : 0;

    $attributeTypes = array(
        '1' => 'text',
        '16' => 'int',
        '49' => 'float',
        '40' => 'date',
        '20' => 'bool'
    );

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
            <table cellspacing="1" cellpadding="2" width="100%" class="tabBody">
                <tr>
                    <td></td>
                    <td><input type="text" style="width:130px;" name="select_pool[<?=$pool?>]" value="<?= $result_pool['name'][$pool] ?>" onchange="document.form1.rename_pool.value=<?= $pool ?>;"></td>
                	<td colspan="<?= 1 + $numLangCols ?>"><b><?= $result_fieldselect['num_ges'] ?></b>&nbsp;<?=$lang[1843]?>,&nbsp;<?=$lang[1844]?>&nbsp;<B><?=$result_fieldselect['num_rows'];?></B>&nbsp;<?=$lang[1846]?></td>
                    <td colspan="2"><label><input type="checkbox" name="showAllLevels" <?= $showAllLevels ? 'checked' : '' ?> onchange="document.form1.level_id.value=0;document.form1.submit();"><?= $lang[2649] ?></label></td>
                    <td><input type="button" value="Tools" onclick="document.form1.viewtyp.value='filter';document.form1.submit();"></td>
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
                                <option value="SORT" <?= $result_fieldselect['sort'] == 'SORT' ? 'selected' : '' ?>><?=$lang[1838]?></option>
                                <option value="WERT ASC" <?= $result_fieldselect['sort'] == 'WERT ASC' ? 'selected' : '' ?>><?=$lang[1840]?></option>
                                <option value="WERT DESC" <?= $result_fieldselect['sort'] == 'WERT DESC' ? 'selected' : '' ?>><?=$lang[1839]?></option>
                            </select>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="<?= 6 + $numLangCols ?>"><hr></td>
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
                    <td class="tabHeaderItem"><?= $lang[1257] ?></td>
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
                        if ($result_fieldselect['def'][$key]){
                            $CHECKED = 'CHECKED';
                        } else {
                            $CHECKED = '';
                        }
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
                                <input type="checkbox" <?= $CHECKED ?> onclick="document.form1.select_default.value='<?= $entryID ?>';document.form1.submit();">&nbsp;
                            <?php if ($result_fieldselect['sort'] == 'SORT' OR !$result_fieldselect['sort']) { ?>
                                <i class="lmb-icon lmb-long-arrow-up" style="cursor:pointer" onclick="document.form1.select_sort_d.value=1;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                                <i class="lmb-icon lmb-long-arrow-down" style="cursor:pointer" onclick="document.form1.select_sort_d.value=2;document.form1.select_sort.value='<?= $entryID ?>';document.form1.submit();"></i>
                            <?php } ?>
                            <?php if ($typ == 'LMB_ATTRIBUTE') { ?>
                                &nbsp;<?= $attributeTypes[$result_fieldselect['type'][$key]] ?>
                            <?php } ?>                            </td>
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
                                <i class="lmb-icon lmb-trash" style="cursor:pointer" onclick="deletePoolEntry(<?= $entryID ?>);"></i>
                            </td>
                        </tr>
                        <?php
		            }
                }
                ?>
                <tr>
                    <td></td>
                    <td><input type="text" style="width:130px;" name="new_wert"></td>
                    <?= str_repeat('<td></td>', $numLangCols) ?>
                    <td><input type="text" style="width:130px;" name="new_keyword"></td>
                    <td>
                    <?php if($typ == 'LMB_ATTRIBUTE') { ?>
                            <select name="new_fieldtype">
                                <?php
                                foreach ($attributeTypes as $type => $name) {
                                    echo "<option value=\"$type\">$name</option>";
                                }
                                ?>
                            </select>
                    <?php } ?>
                    </td>
                    <td colspan="2"><input type="submit" value="<?= $lang[34] ?>" name="add_select"></td>
                </tr>
                <tr>
                    <td colspan="<?= 6 + $numLangCols ?>"><hr></td>
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
	        <table cellspacing="0" cellpadding="2" width="100%" class="tabfringe hoverable">
                <tr class="tabHeader">
                    <td class="tabHeaderItem">ID</td>
                    <td class="tabHeaderItem"><?= $lang[4] ?></td>
                    <td class="tabHeaderItem"><?= $lang[86] ?></td>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <?php
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
                            <td nowrap <?= $selectOnClick ?>><?= $result_pool['id'][$key] ?></td>
                            <td nowrap <?= $selectOnClick ?>><?= htmlentities($result_pool['name'][$key], ENT_QUOTES, $umgvar['charset']) ?></td>
                            <td nowrap <?= $selectOnClick ?>><?= $result_pool['num'][$key] ?></td>
                            <td nowrap><i class="lmb-icon lmb-trash" onclick="deletePool(<?= $key ?>)" style="cursor:pointer;"></i></td>
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