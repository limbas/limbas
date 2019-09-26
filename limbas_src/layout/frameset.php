<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 8
 */


if ($umgvar['introlink']) {
    $intro = $umgvar['introlink'];
} else {
    $intro = 'main.php?action=intro';
}

if ($action == 'redirect' and !empty($src)) {
    $intro = 'main.php?' . $src;
}

require_once('layout/' . $session['layout'] . '/css.php');

$menu_setting = lmbGetMenuSetting();

if ($menu_setting['frame']['nav']) {
    $LeftMenuSize = $menu_setting['frame']['nav'];
    if ($LeftMenuSize > 30) {
        $LeftMenuSize += 10;
    }
}
if ($menu_setting['frame']['multiframe']) {
    $rightMenuSize = $menu_setting['frame']['multiframe'];
}

?>

<link rel="stylesheet" type="text/css" href="USER/<?= $session['user_id'] ?>/layout.css">

<body id="topset" class="frame-container-vertical">
    <iframe id="top1" name="top1" src="main.php?&action=top" style="height: <?= $topFrameSize ?>px; overflow: hidden;"></iframe>
    <?php if ($LINK[252]): ?>
        <iframe id="message" name="message" src="main.php?&action=top2" style="height: <?= $topMenuSize ?>px; /* overflow: hidden; *//* doesnt work in firefox */"></iframe>
    <?php endif; ?>
    <div id="mainset" class="frame-fill frame-container">
        <?php if ($LINK[251]): ?>
            <iframe id="nav" name="nav" src="main.php?&action=nav&sparte=gtab&tab_group=1&refresh=no" style="width: <?= $LeftMenuSize ?>px;"></iframe>
        <?php endif; ?>
        <div id="main-container" class="frame-fill frame-container-vertical">
            <iframe id="main_top" name="main_top" src="main.php?action=layoutframe&layoutframe=top" style="height: 41px; overflow:hidden;"></iframe>
            <iframe id="main" name="main" src="<?= $intro ?>" class="frame-fill"></iframe>
        </div>
        <?php if ($LINK[253] and $session['multiframe']): ?>
            <iframe id="multiframe" name="multiframe" src="main.php?&action=multiframe" style="width: <?= $rightMenuSize ?>px;"></iframe>
        <?php endif; ?>
    </div>
</body>