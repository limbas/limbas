<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $lang;
global $session;
global $umgvar;

?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="card-title mb-0 h1"><img src="assets/images/logo.svg" alt="" class="align-middle" style="max-height: 1.4em"> <span class="align-middle"><?=e($lang[3237])?> L<span style="color:orange">I</span>MBAS</span></p>
            <span><?=e($umgvar['version'])?></span>
        </div>
        
        <p>
            <?php 
            $currentHour = intval(date('G'));
            if($currentHour >= 4 && $currentHour <= 10): ?>
                <?=e($lang[3234])?>
            <?php elseif($currentHour >= 11 && $currentHour <= 17): ?>
                <?=e($lang[3235])?>
            <?php else: ?>
                <?=e($lang[3236])?>
            <?php endif; ?><?=e($session['vorname'])?>!
        </p>

        <p class="h5"><i class="fas fa-lightbulb fa-fw"></i> <?=e($lang[3230])?>:</p>
        <ul>
            <li><?=e($lang[3226])?></li>
            <li><?=e($lang[3227])?></li>
            <li><?=e($lang[3228])?></li>
            <li><?=e($lang[3229])?></li>
        </ul>

        <p class="h5 mb-3"><i class="fas fa-question-circle fa-fw"></i> <?=e($lang[3231])?>:</p>
        <div class="row">
            <div class="col">
                <a class="btn btn-lg btn-outline-secondary w-100 py-4" href="https://www.limbas.org/" target="_blank"><?=e($lang[3232])?> <i class="fas fa-external-link"></i></a>
            </div>
            <div class="col">
                <a class="btn btn-lg btn-outline-secondary w-100 py-4" href="https://www.limbas.com/" target="_blank"><?=e($lang[3233])?> <i class="fas fa-external-link"></i></a>
            </div>
        </div>
    </div>
    <div class="card-footer">
        LIMBAS. Copyright &copy; 1998-<?= date('Y') ?> LIMBAS GmbH (info@limbas.com). LIMBAS is free
        software; You can redistribute it and/or modify it under the terms of the GPL General Public License
        V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a>
        for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are
        copyright of their respective owners, and are released under different licences.
    </div>
</div>
