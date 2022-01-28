<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
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

require_once ('gtab/gtab.lib');
require_once ("lib/context.lib");

?>

<link rel="stylesheet" type="text/css" href="layout/css/<?=$session['layout']?>.<?=$session['farbschema']?>.css?v=<?=$umgvar["version"]?>">
<script type="text/javascript" src="lib/global.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript" src="extern/jquery/jquery-3.2.1.min.js?v=<?=$umgvar["version"]?>"></script>

<script src="layout/<?=$session['layout']?>/nav.js?v=<?=$umgvar["version"]?>"></script>


<body>

<FORM ACTION="main.php" METHOD="post" NAME="form1" TARGET="main" class="d-none">
    <INPUT TYPE="hidden" NAME="ID" VALUE="<?= $session["user_id"] ?>">
    <INPUT TYPE="hidden" NAME="aktivid">
    <INPUT TYPE="hidden" NAME="action">
    <INPUT TYPE="hidden" NAME="error_msg" VALUE="<?=$lang[25]?>">
    <INPUT TYPE="hidden" NAME="csvexp">
    <INPUT TYPE="hidden" NAME="tab_group">
    <INPUT TYPE="hidden" NAME="gtabid">
    <INPUT TYPE="hidden" NAME="snap_id">
    <INPUT TYPE="hidden" NAME="source" VALUE="root">
</FORM>

<FORM ACTION="main_admin.php" METHOD="post" NAME="form2" TARGET="main" class="d-none">
    <INPUT TYPE="hidden" NAME="aktivid">
    <INPUT TYPE="hidden" NAME="action">
    <INPUT TYPE="hidden" NAME="frame1para">
    <INPUT TYPE="hidden" NAME="frame2para">
    <INPUT TYPE="hidden" NAME="error_msg" VALUE="<?=$lang[25]?>">
</FORM>

    <?php if ($LINK[252]): ?>

        <?php include('top.php'); ?>

    <?php endif; ?>
    <div id="mainset" class="frame-fill frame-container">
        <?php if ($LINK[251]): ?>
            <div id="nav" name="nav" style="width: <?= $LeftMenuSize ?>px;" class="mainsidebar">
                <?php
                $GLOBALS["ltmp"]["history_action"] = 1;
                $bodyclass = "nav";
                require_once ('nav.dao');
                require_once ('nav.php');
                ?>
            </div>
        <?php endif; ?>
        <div id="main-container" class="frame-fill frame-container-vertical">
            <div class="lmbItemHeaderMain">
                <div class="row">
                    <div class="col-8" id="lmb-main-title"></div>
                    <div class="col-4 text-right" id="lmb-helplink"></div>
                </div>
            </div>

            <iframe id="main" name="main" src="<?php echo $intro ?>" class="frame-fill"></iframe>
        </div>
        <?php if ($LINK[253] and $session['multiframe']): ?>
            <div id="multiframecont" name="multiframecont" style="width: <?= $rightMenuSize ?>px;" class="mainsidebar">
                <?php
                $STYLE = "";
                $GLOBALS["ltmp"]["history_action"] = 1;
                $ONCLICK = "OnClick=\"body_click();\"";
                $ONLOAD = "startautoopen();";
                $bodyclass = "multiframe";

                require_once ('multiframe.dao');
                require_once ('multiframe.php');
                ?>
            </div>
        <?php endif; ?>
    </div>

    <script type="text/javascript" src="extern/bootstrap/dist/js/bootstrap.bundle.min.js?v=<?=$umgvar["version"]?>"></script>
</body>
