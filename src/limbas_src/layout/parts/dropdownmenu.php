<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
use Limbas\layout\lib\DropDownMenuItem; ?>
<li class="nav-item dropdown ps-lg-2" data-lmb-dptoggle="hover">
    <a class="nav-link h-100 d-flex flex-column justify-content-between align-items-center">
        <?php if(!empty($value['icon'])): ?>
            <div class="lmbMenuItemTop2Icon"><i class="lmb-icon <?=e($value['icon'])?>"></i></div>
            <div class="lmbMenuItemTop2Text"><?=e($value['name'])?></div>
        <?php else: ?>
            <?=e($value['name'])?>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu rounded-0 py-0 mt-0">
        
        <?php $dropDownMenuItems = [];
        $dropDownFunction = 'dropdownMenu_' . substr($value['link'], 9);
        if(function_exists($dropDownFunction)) {
            $dropDownMenuItems = $dropDownFunction();
        }
        
        /** @var DropDownMenuItem $dropDownMenuItem */
        foreach ($dropDownMenuItems as $dropDownMenuItem):
        ?>
        <a class="dropdown-item <?= $dropDownMenuItem->active ? 'disabled text-bg-primary' : 'cursor-pointer' ?>"
            <?php foreach ($dropDownMenuItem->data as $key => $value): ?>
                data-<?=e($key)?>="<?=e($value)?>"
            <?php endforeach; ?>       
        >
            <?=e($dropDownMenuItem->name)?>
        </a>
        <?php endforeach; ?>
    </div>
</li>
