<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\gtab\lib\relations\ChainRelation;

?>
<FORM ACTION="main_admin.php" METHOD="post" NAME="form5">
    <input type="hidden" name="action" value="setup_verkn_editor">
    <input type="hidden" name="tabid" value="<?= $tabid ?>">
    <input type="hidden" name="fieldid" value="<?= $fieldid; ?>">
    <input type="hidden" name="category" value="relationtree">


    <?php if ($category == 'relationtree'): ?>

        <p><?= $lang[2855] ?></p>

        <?php

        if (($rfield['verkntabletype'] == 1 || $rfield['verkntabletype'] == 3) && $rfield['datatype'] != 25):

            $chainRelation = new ChainRelation(intval($tabid), intval($rfield['verkntabid']));
            $chainRelations = $chainRelation->getPossibleRelations();


            foreach ($chainRelations as $chainRelation): ?>
                <div class="mb-3 form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input"
                               onchange="LIM_relationTree(this,'<?= implode(',', $chainRelation->md5) ?>')" <?= $rfield['verkntree'] == $chainRelation->id ? 'checked' : '' ?>>
                        &rarr; <?= implode(' &rarr; ', $chainRelation->names) ?>
                    </label>
                </div>

            <?php
            endforeach;
        endif;
        ?>

        <?php if (!empty($chainRelations)): ?>
            <div class="text-center">
                <button type="submit" class="btn btn-primary"><?= $lang[33] ?></button>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</FORM>
