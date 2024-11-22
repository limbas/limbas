<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\layout\Layout;

?>
</head>


<?php

# Page - Header
echo "<BODY CLASS=\"$bodyclass\" TOPMARGIN=\"0\" Marginheight=\"0\" Marginwidth=\"0\" BORDER=\"0\" LINK=\"$ALINK\" VLINK=\"$VLINK\" LINK=\"$NLINK\" TEXT=\"$TEXT1\" $ONLOAD $ONUNLOAD $ONKEYDOWN $ONCLICK $ONBLUR $ONMOUSEDOWN $ONMOUSEUP >";


if($BODYHEADER || $HEADER){
    require_once(Layout::getFilePath('main_header.php'));

    if (!$layout_bootstrap) {
        echo '<table cellpadding="0" cellspacing="0" style="width:100%;height:100%"><tr><td valign="top" class="lmbfringeFrameMain" id="lmbfringeFrameMain">';
    }
}
