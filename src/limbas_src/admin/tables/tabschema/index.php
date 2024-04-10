<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<div class="container-fluid p-3">

    <ul class="nav nav-tabs">
        <?php if ($LINK[108]): ?>
            <li class="nav-item">
                <a class="nav-link <?= (!$typ || $typ == 1) ? 'active bg-contrast' : '' ?>"
                   href="main_admin.php?action=setup_tabschema&typ=1"><?= $lang[$LINK["desc"][215]] ?></a>
            </li>
        <?php endif; ?>
        <?php if ($LINK[258]): ?>
            <li class="nav-item">
                <a class="nav-link <?= ($typ == 2) ? 'active bg-contrast' : '' ?>"
                   href="main_admin.php?action=setup_tabschema&typ=2"><?= $lang[2912] ?></a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="tab-content border border-top-0 bg-contrast">
        <div class="tab-pane active p-3">
            <?php if ($typ == 2) {
                require(__DIR__ . '/relations.php');
            }
            else {
                require(__DIR__ . '/tabschema.php');
            } ?>
        </div>
    </div>
</div>
