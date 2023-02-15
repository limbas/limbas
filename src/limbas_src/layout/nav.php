<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<?php
$multfrdispl = "";
$hiddendispl = "display:none";

$menu_setting = lmbGetMenuSetting();
if(!$leftMenuOpen){
	$multfrdispl = "display:none";
	$hiddendispl = "";
}
?>

<div id="hiddensidenav" style="height:90%;cursor:pointer;<?=$hiddendispl?>" data-hideshow-sidebars="nav">
    <div class="lmbFrameShow">
        <i class="lmb-icon lmb-icon-aw lmb-caret-right"></i>
    </div>
</div>

<div id="sidenav" style="<?=$multfrdispl?>">

<div id="lmbAjaxContainer" class="ajax_container" style="position:absolute;visibility:hidden;" OnClick="activ_menu=1;"></div>

<div id="framecontext" class="evt-hide-frame-con" data-hideshow-sidebars-right="nav">
    <div class="lmbfringeFrameNav clearfix">
        <div class="evt-hide-frame text-center d-lg-none p-3"  data-hideshow-sidebars="nav"><i class="lmb-icon lmb-erase"></i></div>
<?php


$defaultExpandMenu = "none";

# index for #arrowSub{$tabGroupIndex} and #subElt_{$tabGroupIndex}
$tabGroupIndex = 0;

# width
if(!$dwme = $menu_setting["frame"]["nav"]){
	$dwme = 180;
}

# render all menus
foreach($menu as $key => $val) {
    recRender($key, $val);
}

?>

<div class="evt-hide-frame"  data-hideshow-sidebars="nav"><i class="lmb-icon lmb-icon-8 lmb-caret-left"></i></div>

</div>
</div>

<?php

if (isset($activeMenu)) {
    # show last opened menu in nav frame
    $displayMainMenu = "mainMenu(" . $activeMenu . ")";
} else {
    # show first menu in nav frame
    foreach ($LINK["name"] as $key => $value) {
        if ($LINK["subgroup"][$key] == 2 AND $LINK["typ"][$key] == 1) {
            $displayMainMenu = "mainMenu(" . $key . ")";
            break;
        }
    }
}

echo '<script>' . $displayMainMenu . '</script>';

?>
</div>
