<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\layout\Layout;

# redirect group
if($groupdat["redirect"][$session["group_id"]] && $_REQUEST['action'] !== 'redirect'){
    if(lmb_strtolower(lmb_substr($groupdat["redirect"][$session["group_id"]],0,4)) == "http"){
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".$groupdat["redirect"][$session["group_id"]]);
        return;
    }else{
        $_REQUEST['action'] = "redirect";
        $src = $groupdat["redirect"][$session["group_id"]];
        //header("HTTP/1.1 301 Moved Permanently");
        //header("Location: index.php?action=redirect&src=".urlencode($groupdat["redirect"][$session["group_id"]]));
    }
}

$layout_bootstrap = true;
$noScript = true;

require(Layout::getFilePath('main/head.php'));

?>

    </head>

<?php

/* --- Frameset ---------------------------------------------------------- */
if($session['layout']){
    require(Layout::getFilePath('frameset.php'));
}

?>

    </html>

<?php
/* --- DB-CLOSE ------------------------------------------------------ */
if ($db) {lmbdb_close($db);}
