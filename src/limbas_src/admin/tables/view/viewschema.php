<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

?>
<svg class="overflow-visible" height="100%" width="100%">
    <defs>
        <marker
                id="arrow"
                viewBox="0 0 10 10"
                refX="5"
                refY="5"
                markerWidth="6"
                markerHeight="6"
                orient="auto-start-reverse">
            <path d="M 0 0 L 10 5 L 0 10 z"/>
        </marker>
    </defs>
    <g id="svg_lines">

    </g>
</svg>
<?php

if ($setdrag) {
    lmb_SetTabschemaPattern($setdrag, $viewid, $gview["relationstring"]);
}

$pat = lmb_GetTabschemaPattern($viewid);

if ($pat["id"]) {
    foreach ($pat["id"] as $key => $value) {
        viewschema_tab($pat, $key);
    }
}

$relationset = lmb_getQuestRelation($viewid, $gview["relationstring"]);

if ($relationset) {
    $bzm = 0;
    foreach ($relationset["tabl"] as $key => $value) {
        viewschema_link($relationset, $key, $bzm);
        $bzm++;
    }
}


?>
<script>
    paint_lines();
</script>
