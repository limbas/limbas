<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
$gview = lmb_getQuestValue($viewid);
?>

<h3><?=$gview["viewname"]?></h3>


<div class="row mb-1">
    <div class="col-sm-2"><?=$lang[1996]?></div>
    <div class="col-sm-10">
        <?php if ($gview["ispublic"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?>
    </div>
</div>

<div class="row mb-1">
    <div class="col-sm-2"><?=$lang[2023]?></div>
    <div class="col-sm-10">
        <?php if ($gview["viewexists"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-2">Syntax</div>
    <div class="col-sm-10">
        <?php if ($gview["isvalid"]) {echo "<i class=\"lmb-icon lmb-check\"></i>";}?>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-sm-2">Event</label>
    <div class="col-sm-10">
        <textarea name="options[event]" class="form-control form-control-sm"><?=htmlentities($gview["event"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea>
    </div>
</div>

<div class="mb-3 row">
    <label class="col-sm-2">Parameter</label>
    <div class="col-sm-10">
        <textarea name="options[params]" class="form-control form-control-sm"><?=htmlentities($gview["params"],ENT_QUOTES,$GLOBALS["umgvar"]["charset"])?></textarea>
    </div>
</div>

<div class="text-end">
    <button class="btn btn-primary btn-sm" onclick="document.form1.options_save.value=1;document.form1.submit();"><?=$lang[842]?></button>
</div>
