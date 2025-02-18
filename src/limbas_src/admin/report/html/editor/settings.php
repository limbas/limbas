<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\setup\fonts\Font;

global $lang;
global $greport;


$paper_size = array('A4'=>'DIN A4','A3'=>'DIN A3','A2'=>'DIN A2','Letter'=>'Letter','custom'=>$lang[3089]);
$orientation = array('P'=>'portrait','L'=>'landscape');

if(is_numeric($greport["page_style"][$report_id][0])) {
    $paper_size_selected['custom'] = 'selected';
}else{
    $paper_size_selected[$greport["page_style"][$report_id][0]] = 'selected';
}

$used_font_selected[$greport["used_fonts"][$report_id][0]] = 'selected';
$orientation_selected[$greport["orientation"][$report_id]] = 'selected';

if(!$greport["dpi"][$report_id]){
    $greport["dpi"][$report_id] = '72';
}

?>


<div class="mb-1 row">
    <label for="paper_size" class="col-sm-4 col-form-label"><?=$lang[3090]?></label>
    <div class="col-sm-8">
        <select class="form-select form-select-sm" id="paper_size" name="paper_size">
            <?php
            foreach($paper_size as $key => $value){
                echo  "<option value=\"$key\" {$paper_size_selected[$key]}>$value</option>";
            }
            ?>
        </select>
    </div>
</div>
<div class="mb-1 row">
    <label for="paper_size" class="col-sm-4 col-form-label"><?=$lang[3091]?></label>
    <div class="col-sm-8">
        <select class="form-select form-select-sm" id="orientation" name="orientation">
            <?php
            foreach($orientation as $key => $value){
                echo  "<option value=\"$key\" {$orientation_selected[$key]}>$value</option>";
            }
            ?>
        </select>
    </div>
</div>

<div class="mb-1 row d-none" id="custom_size">
    <label for="custom_size_w" class="col-sm-4 col-form-label"><?=$lang[1141]?> / <?=$lang[1142]?></label>
    <div class="col-sm-4">
        <div class="input-group input-group-sm">
            <input type="number" class="form-control form-control-sm" id="custom_size_w" name="custom_size_w" value="<?=$greport["page_style"][$report_id][0]?>">
            <span class="input-group-text">mm</span>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="input-group input-group-sm">
            <input type="number" class="form-control form-control-sm" id="custom_size_h" name="custom_size_h" value="<?=$greport["page_style"][$report_id][1]?>">
            <span class="input-group-text">mm</span>
        </div>
    </div>
</div>

<div class="mb-1 row">
    <label for="dpi" class="col-sm-4 col-form-label">DPI</label>
    <div class="col-sm-8">
        <input type="number" class="form-control form-control-sm" id="dpi" name="dpi" value="<?=$greport["dpi"][$report_id]?>">
    </div>
</div>
<div class="mb-1 row">
    <label for="margin" class="col-sm-4 col-form-label"><?=$lang[1111]?></label>
    <div class="col-sm-8">
        <input type="number" class="form-control form-control-sm" id="margin" name="margin" value="<?=$greport["page_style"][$report_id][2]?>">
    </div>
</div>
<div class="mb-1 row">
    <label for="margin_top" class="col-sm-4 col-form-label"><?=$lang[1111]?></label>
    <div class="col-sm-4 mb-1">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="lmb-icon lmb-arrow-up"></i></span>
            <input type="number" class="form-control form-control-sm" id="margin_top" name="margin_top" value="<?=$greport["page_style"][$report_id][2]?>">
        </div>
    </div>
    <div class="col-sm-4 mb-1">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><b>|</b><i class="lmb-icon lmb-arrow-left"></i></span>
            <input type="number" class="form-control form-control-sm" id="margin_right" name="margin_right" value="<?=$greport["page_style"][$report_id][4]?>">
        </div>
    </div>
    <div class="col-sm-4 offset-4">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="lmb-icon lmb-arrow-down"></i></span>
            <input type="number" class="form-control form-control-sm" id="margin_bottom" name="margin_bottom" value="<?=$greport["page_style"][$report_id][3]?>">
        </div>
    </div>
    <div class="col-sm-4">
        <div class="input-group input-group-sm">
            <span class="input-group-text"><i class="lmb-icon lmb-arrow-right"></i><b>|</b></span>
            <input type="number" class="form-control form-control-sm" id="margin_left" name="margin_left" value="<?=$greport["page_style"][$report_id][5]?>">
        </div>
    </div>
</div>

<div class="mb-1 row">
    <label for="default_class" class="col-sm-4 col-form-label"><?=$lang[1170]?></label>
    <div class="col-sm-8">
        <select id="default_font" name="default_font" class="form-select form-select-sm"><option></option>
            <?php
            /** @var Font $font */
            foreach ($fonts as $font): ?>
                <option value="<?=e($font->family)?>" <?=e($used_font_selected[$font->family])?>><?=e($font->family)?> <?=e($font->style)?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


<div class="mb-1 row">
    <label for="default_class" class="col-sm-4 col-form-label"><?=$lang[2984]?> <?=$lang[1170]?></label>
    <div class="col-sm-8">
        <select multiple id="extended_font" name="extended_font" class="form-select form-select-sm">
            <?php
            /** @var Font $font */
            foreach ($fonts as $font):
                if($font->family == $greport['used_fonts'][$report_id][0])
                {
                    continue;
                }
                ?>

                <option value="<?=e($font->family)?>" <?=in_array($font->family,$greport['used_fonts'][$report_id]) ? 'selected' : ''?>><?=e($font->family)?> <?=e($font->style)?></option>

            <?php endforeach; ?>
        </select>
    </div>
</div>
<div class="mb-1 row">
    <label for="default_class" class="col-sm-4 col-form-label"><?= $lang[2581] ?></label>
    <div class="col-sm-8">
        <select id="default_class" name="default_class" class="form-select form-select-sm">
            <option value=""></option>
            <?php

            if (file_exists(EXTENSIONSPATH . 'css') && $handle = opendir(EXTENSIONSPATH . 'css')):
                while (false !== ($file = readdir($handle))):

                    $pathParts = pathinfo($file);

                    if($pathParts['extension'] === 'css'): ?>
                        <option value="<?=e($pathParts['basename'])?>" <?=$pathParts['basename'] === $greport['css'][$report_id] ? 'selected' : ''?>><?=e($pathParts['basename'])?></option>

                    <?php
                    endif;

                endwhile;
                closedir($handle);
            endif;
            ?>

        </select>
    </div>
</div>
<div class="mb-1 row">
    <label for="listmode" class="col-sm-4 col-form-label"><?=$lang[2649]?></label>
    <div class="col-sm-8">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="listmode" name="listmode" <?=($greport["listmode"][$report_id])?'checked':''?>>
        </div>
    </div>
</div>
