<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $DBA, $lang;

use Limbas\lib\db\functions\Dbf;

?>

<div class="p-3">
    <?= $lang[30] ?> : <input type="text" onkeyup="lmb_search_pause(this.value)" class="form-control form-control-sm w-auto d-inline-block">
</div>


<?php

$tables = Dbf::getTableList($DBA['DBSCHEMA'], null, 'TABLE');
$tables = $tables["table_name"];

?>

<div class="list-hierarchy">
    <ul class="list-group list-group-flush">
        <?php
        foreach ($tables as $key => $value):
            if ($dep = lmb_checkViewDependency($value)): ?>
                <li class="roottree list-group-item">
                    <div><?= $value ?></div>
                    <?= lmb_make_tree($value); ?>
                </li>
            <?php endif;
        endforeach;
        ?>
    </ul>
</div>
