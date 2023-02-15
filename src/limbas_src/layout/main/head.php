<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
if ($layout_bootstrap){  //check legacy switch TODO: remove in future versions
    echo '<!doctype html>'."\n";
}
?>
<html>

<head>
    <title>Limbas Enterprise Unifying Framework V <?=$umgvar['version']?></title>
    
    <meta name="title" content="Limbas Enterprise Unifying Framework V <?=$umgvar['version']?>">
    <meta name="author" content="LIMBAS GmbH">
    <meta name="publisher" content="LIMBAS GmbH">
    <meta name="copyright" content="LIMBAS GmbH">
    <meta name="description" content="Limbas Enterprise Unifying Framework">
    <meta name="version" content="<?=$umgvar['version'] ?>">
    <meta name="date" content="2023-02-15">

    <meta charset="<?=$umgvar['charset'] ?>" />

    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <?php if(!isset($noScript)): ?>
    
    <script type="text/javascript" src="assets/js/lib/global.js?v=<?=$umgvar['version']?>"></script>
    <script type='text/javascript' src='assets/vendor/jquery/jquery.min.js?v=<?=$umgvar['version']?>'></script>

    <?php endif; ?>


    
