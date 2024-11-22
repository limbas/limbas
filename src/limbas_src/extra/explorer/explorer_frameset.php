<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




/* --- Frameset ---------------------------------------------------------- */?>

<div id="explorer_topset" class="frame-container">
    <div class="resizer_attributes resizer_actual border-end h-100">
        <iframe src="main.php?action=explorer_tree" id="explorer_tree" name="explorer_tree" class="resized overflow-scroll"></iframe>
    </div>
    <div class="resizer_attributes w-100 h-100">
        <iframe src="main.php?action=explorer_main&ID=0&typ=1&LID=<?=$LID?>" id="explorer_main" name="explorer_main" class="frame-fill"></iframe>
    </div>
</div>
