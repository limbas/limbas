<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
?>
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= $tab == 3 ? ' active bg-contrast" href="' : '" href="main_admin.php?action=setup_datasync&tab=3' ?>">Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab == 1 ? ' active bg-contrast" href="' : '" href="main_admin.php?action=setup_datasync&tab=1' ?>">Clients</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab == 2 ? ' active bg-contrast" href="' : '" href="main_admin.php?action=setup_datasync&tab=2' ?>">Templates</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab == 4 ? ' active bg-contrast" href="' : '" href="main_admin.php?action=setup_datasync&tab=4' ?>">Validate</a>
    </li>
</ul>
