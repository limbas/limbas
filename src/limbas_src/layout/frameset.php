<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */




if ($umgvar['introlink']) {
    $intro = $umgvar['introlink'];
} else {
    $intro = 'main.php?action=intro';
}

if ($action == 'redirect' and !empty($src)) {
    $intro = 'main.php?' . $src;
}

$menu_setting = lmbGetMenuSetting();

$LeftMenuSize = 200;
$leftMenuOpen = true;
if ($menu_setting['frame']['nav']) {
    $LeftMenuSize = $menu_setting['frame']['nav'];
    if ($LeftMenuSize > 30) {
        $LeftMenuSize += 10;
    }
}
if (boolval($menu_setting['frame']['open']['nav'] ?? true) === false) {
    $leftMenuOpen = false;
    $LeftMenuSize = 15;
}
$rightMenuSize = 200;
$rightMenuOpen = true;
if ($menu_setting['frame']['multiframe']) {
    $rightMenuSize = $menu_setting['frame']['multiframe'];
}
if (boolval($menu_setting['frame']['open']['multiframe'] ?? true) === false) {
    $rightMenuOpen = false;
    $rightMenuSize = 15;
}
require_once (COREPATH . 'gtab/gtab.lib');
require_once (COREPATH . 'lib/context.lib');

?>


<body>

<link rel="stylesheet" type="text/css" href="<?=$session['css']?>?v=<?=$umgvar["version"]?>">
<script type="text/javascript" src="assets/js/lib/global.js?v=<?=$umgvar["version"]?>"></script>
<script type="text/javascript" src="assets/vendor/jquery/jquery.min.js?v=<?=$umgvar["version"]?>"></script>
<script src="assets/js/layout/nav.js?v=<?=$umgvar["version"]?>"></script>


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

    <?php
    if ($LINK[252]) {
        require_once(Layout::getFilePath('top.php'));
    }
    
    ?>
    <div id="mainset" class="frame-fill frame-container">
        <?php if ($LINK[251]): ?>
            <div id="nav" name="nav" style="width: <?= $LeftMenuSize ?>px;" class="mainsidebar <?=($leftMenuOpen)?'open':''?>">
                <?php
                $GLOBALS["ltmp"]["history_action"] = 1;
                $bodyclass = "nav";
                
                $includeFiles = ['nav.dao','nav_render.php','nav.php'];
                foreach($includeFiles as $includeFile) {
                    require_once(Layout::getFilePath($includeFile));
                }
                
                ?>
            </div>
        <?php endif; ?>
        <div id="main-container" class="frame-fill frame-container-vertical">
            <div class="lmbItemHeaderMain">
                <div class="row">
                    <div class="col">
                        <span id="lmb-main-title"></span>
                        <span id="lmb-main-subtitle"></span>
                    </div>
                    <div class="col-4 text-end" id="lmb-helplink"></div>
                </div>
            </div>

            <iframe id="main" name="main" src="<?php echo $intro ?>" class="frame-fill"></iframe>
        </div>
        <?php if ($LINK[253] and $session['multiframe']): ?>
            <div id="multiframecont" name="multiframecont" style="width: <?= $rightMenuSize ?>px;" class="mainsidebar <?=($rightMenuOpen)?'open':''?>">
                <?php
                $STYLE = "";
                $GLOBALS["ltmp"]["history_action"] = 1;
                $ONCLICK = "OnClick=\"body_click();\"";
                $ONLOAD = "startautoopen();";
                $bodyclass = "multiframe";


                require_once(Layout::getFilePath('multiframe.dao'));
                require_once(Layout::getFilePath('multiframe.php'));
                
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal" id="general-main-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="general-main-modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="general-main-modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Schließen</button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="toast-success" class="toast text-bg-success">
            <div class="toast-body" id="toast-success-body"></div>
        </div>
    
        <div id="toast-error" class="toast text-bg-danger">
            <div class="toast-body" id="toast-error-body"></div>
        </div>
    
        <div id="toast-warning" class="toast text-bg-warning">
            <div class="toast-body" id="toast-warning-body"></div>
        </div>
    </div>


    <script type="text/javascript" src="assets/vendor/bootstrap/bootstrap.bundle.min.js?v=<?=$umgvar["version"]?>"></script>
</body>
