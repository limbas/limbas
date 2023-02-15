<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

//lmbGlistFooter

global $lang;
global $session;

$subtab=null;
$style=null;


if($filter['anzahl'][$gtabid] == 'all'){
    $filter['anzahl'][$gtabid] = $gresult[$gtabid]['res_count'];
}


$maxpage = ceil($gresult[$gtabid]['max_count']/$filter['anzahl'][$gtabid]);

?>

<div class="d-flex flex-row align-items-center" id="GtabTableFooter">

    <?php if($gresult[$gtabid]['over_limit'] == 1): ?>
        <span class="text-danger" onclick="lmbGetResultlimit();"> <span id="GtabResCount">&gt; <?=$gresult[$gtabid]['res_count']?></span><?=$lang[93]?></span>
    <?php elseif($gresult[$gtabid]['over_limit'] == 2): ?>
        <span class="text-danger" onclick="lmbGetResultlimit();"> <span id="GtabResCount"></span>? <?=$lang[93]?> </span>
    <?php else: ?>
        <span><span id="GtabResCount" class="me-1"><?=$gresult[$gtabid]['res_count']?></span> <?=$lang[93]?></span>
    <?php endif; ?>
    
    <span class="ms-3 me-2"><?=$lang[89]?></span>
    
    <?php if($filter['page'][$gtabid] == 1): ?>
        <i class="lmb-icon lmb-first text-muted" title="<?=$lang[1294]?>"></i>
        <i class="lmb-icon lmb-previous text-muted" title="<?=$lang[1296]?>"></i>
    <?php else: ?>
        <i class="lmb-icon lmb-first" title="<?=$lang[1294]?>" onclick="document.form1.elements['filter_page[<?=$gtabid?>]'].value='1';send_form(1,2);"></i>";
        <i class="lmb-icon lmb-previous" title="<?=$lang[1296]?>" onclick="document.form1.elements['filter_page[<?=$gtabid?>]'].value='<?=($filter['page'][$gtabid] - 1)?>';send_form(1,2);"></i>";
    <?php endif; ?>

    <input type="text" class="form-control form-select-sm d-inline w-auto" name="filter_page[<?=$gtabid?>]" value="<?=$filter['page'][$gtabid]?>/<?=$maxpage?>">
    
    
    <?php if($filter['page'][$gtabid] == $maxpage): ?>
        <i class="lmb-icon lmb-next text-muted" title="<?=$lang[1297]?>"></i>
        <i class="lmb-icon lmb-last text-muted" title="<?=$lang[1295]?>"></i>
    <?php else: ?>
        <i class="lmb-icon lmb-next" title="<?=$lang[1297]?>" onclick="document.form1.elements['filter_page[<?=$gtabid?>]'].value='<?=($filter['page'][$gtabid] + 1)?>';send_form(1,2);"></i>
        <i class="lmb-icon lmb-last" title="<?=$lang[1295]?>" onclick="document.form1.elements['filter_page[<?=$gtabid?>]'].value='<?=$maxpage?>';send_form(1,2);"></i>
    <?php endif; ?>
    
    
    <span class="ms-3"><?=$lang[96]?></span>

    <input type="text" class="form-control form-select-sm d-inline w-auto mx-1" name="filter_anzahl[<?=$gtabid?>]" value="<?=$filter['anzahl'][$gtabid]?>" maxlength="3">
    <span><?=$lang[88]?></span>
    <?php if($session['debug']): ?>
    <span class="text-muted ms-3">(<?=$gresult[$gtabid]['need_time']?> sec.)</span>
    <?php endif; ?>
    
</div>
