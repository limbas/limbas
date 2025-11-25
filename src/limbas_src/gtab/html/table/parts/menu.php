<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\gtab\lib\forms\LMBAction;

?>

<?php

global $farbschema;
global $LINK;
global $lang;
global $gtab;
global $gfield;
global $session;
global $filter;
global $readonly;
global $gsr;

if(!$subtab){$cl = 'class="gtabHeaderMenuTR"';}

// breadcrumb
include(COREPATH . 'gtab/html/forms/parts/breadcrumb.php');
?>


<nav class="navbar navbar-expand-sm center-navigation bg-nav mb-3 lmbGtabmenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>">
    <div class="container-fluid">


        <?php
        if($verkn_relationpath){
            $link = "jump_to_breadcrumb()";
            ?>
            <button type="button" class="btn btn-outline-light btn-sm me-2" onclick="<?=$link?>"><?=$lang[3222]?></button>
            <?php
        }
        ?>


        <a class="navbar-brand" href="#" onclick="document.form1.elements['filter_page[<?=$gtabid?>]'].value='1';send_form('1');"><span id="tabs_<?=$gtabid?>"><?=$gtab['desc'][$gtabid]?></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lmbDetailsNavbar" aria-controls="lmbDetailsNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="lmbDetailsNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                <?php if(!$gtab["menudisplay"][$gtabid][1]['file']):?>
                <li class="nav-item lmbGtabmenu-file">
                    <a class="nav-link " href="#" onclick="limbasDivShow(this,'','limbasDivMenuDatei');" id="edit4"><?=$lang[545]?></a>
                </li>
                <?php endif; ?>
                <?php if(!$gtab["menudisplay"][$gtabid][1]['edit']):?>
                <li class="nav-item lmbGtabmenu-edit">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuBearbeiten');"><?=$lang[843]?></a>
                </li>
                <?php endif; ?>
                <?php if(!$gtab["menudisplay"][$gtabid][1]['view']):?>
                <li class="nav-item lmbGtabmenu-view">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuAnsicht');"><?=$lang[1625]?></a>
                </li>
                <?php endif; ?>
                <?php if(!$gtab["menudisplay"][$gtabid][1]['extra']):?>
                <li class="nav-item lmbGtabmenu-extra">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuExtras');"><?=$lang[1939]?></a>
                </li>
                <?php endif; ?>

                <?php
                // custmenu
                if($GLOBALS['gcustmenu'][$gtabid][2]['id'][0]):
                    foreach($GLOBALS['gcustmenu'][$gtabid][2]['id'] as $cmkey => $cmid):
                        if($GLOBALS['gcustmenu'][$gtabid][2]['directlink'][$cmkey]){
                            $clink = lmb_pop_custmenu($cmid,$gtabid, $ID, linkonly:1);
                        }else{
                            $clink = "limbasDivShow(this,'','limbasDivCustMenu_$cmid');";
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="<?=$clink?>"><?=$lang[$GLOBALS['gcustmenu'][$gtabid][2]['name'][$cmkey]]?></a>
                        </li>
                    <?php
                    endforeach;
                endif;
                ?>

            </ul>

            <?php if($session["symbolbar"]): ?>

                <ul class="navbar-nav ms-auto">

                    <?php if($gtab['edit'][$gtabid] && !$gtab["menudisplay"][$gtabid][1][197]): ?>
                        <li class="nav-item lmbGtabmenuIcon-197">
                            <?= LMBAction::ren(197,'icon'); // speichern ?>
                        </li>
                    <?php endif; ?>

                    <?php if($gtab['add'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][1]): ?>
                        <li class="nav-item lmbGtabmenuIcon-1">
                            <?= LMBAction::ren(1,'icon'); // neuer Datensatz ?>
                        </li>
                    <?php endif; ?>

                    <?php if($gtab['add'][$gtabid] && $gtab['copy'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][201]): ?>
                        <li class="nav-item lmbGtabmenuIcon-201">
                            <?= LMBAction::ren(201,'icon'); // Datensatz kopieren ?>
                        </li>
                    <?php endif; ?>

                    <?php if($gtab['ver'][$gtabid] && $gtab['add'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][235]): ?>
                        <li class="nav-item lmbGtabmenuIcon-235">
                            <?= LMBAction::ren(235,'icon'); // versionieren ?>
                        </li>
                    <?php endif; ?>

                    <?php if($gtab['delete'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][1][11]): ?>
                        <li class="nav-item lmbGtabmenuIcon-11">
                            <?= LMBAction::ren(11,'icon'); // löschen ?>
                        </li>
                    <?php endif; ?>

                    <?php if(($gtab['hide'][$gtabid] || $gtab['trash'][$gtabid]) && !$readonly): ?>

                        <?php if($gresult[$gtabid]['LMB_STATUS'][0]): ?>
                            <?php if(!$gtab["menudisplay"][$gtabid][1][166]): ?>
                            <li class="nav-item lmbGtabmenuIcon-166">
                                <?= LMBAction::ren(166,'icon'); // Archiv wiederherstellen ?>
                            </li>
                            <?php endif?>
                        <?php else: ?>
                            <?php if($gtab['trash'][$gtabid] && !$gtab["menudisplay"][$gtabid][1][313]): ?>
                            <li class="nav-item lmbGtabmenuIcon-313">
                                <?= LMBAction::ren(313,'icon'); // trash ?>
                            </li>
                            <?php endif; ?>
                            <?php if($gtab['hide'][$gtabid] && !$gtab["menudisplay"][$gtabid][1][164]): ?>
                            <li class="nav-item lmbGtabmenuIcon-164">
                                <?= LMBAction::ren(164,'icon'); // archivieren ?>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if($gtab['lock'][$gtabid] && !$readonly): ?>

                        <?php if($filter["locked"][$gtabid]): ?>
                            <?php if(!$gtab["menudisplay"][$gtabid][1][271]): ?>
                            <li class="nav-item lmbGtabmenuIcon-271">
                                <?= LMBAction::ren(271,'icon'); // unlock ?>
                            </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if(!$gtab["menudisplay"][$gtabid][1][270]): ?>
                            <li class="nav-item lmbGtabmenuIcon-270">
                                <?= LMBAction::ren(270,'icon'); // lock ?>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if($LINK[14] && !$gtab["menudisplay"][$gtabid][1][14]): ?>
                    <li class="nav-item lmbGtabmenuIcon-14">
                        <?= LMBAction::ren(14,'icon'); // suchen ?>
                    </li>
                    <?php endif; ?>

                    <?php if($gtab['edit'][$gtabid] && ($LINK[161] || $LINK[3] || $LINK[10]) && !$gtab["menudisplay"][$gtabid][1][161]): ?>
                        <?php if($LINK[161] AND $LINK[3] AND $filter['alter'][$gtabid]): ?>
                            <li class="nav-item lmbGtabmenuIcon-161">
                                <?= LMBAction::ren(161,'icon', true); // Liste bearbeiten ?>
                            </li>
                        <?php elseif ($LINK[161] AND $LINK[10]): ?>
                            <li class="nav-item lmbGtabmenuIcon-161">
                                <?= LMBAction::ren(161,'icon'); ?>
                            </li>
                        <?php endif; ?>

                    <?php endif; ?>


                    <?php if($GLOBALS['verknpf']): ?>

                        <?php if(!$gtab["menudisplay"][$gtabid][1][243]): ?>
                        <li class="nav-item lmbGtabmenuIcon-243">
                            <?= LMBAction::ren(243,'icon', boolval($GLOBALS['verkn_showonly'])); // show related ?>
                        </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['verkn_showonly']): ?>
                            <?php if(!$gtab["menudisplay"][$gtabid][1][158]): ?>
                            <li class="nav-item lmbGtabmenuIcon-158">
                                <?= LMBAction::ren(158,'icon'); // verknüpfen ?>
                            </li>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if(!$gtab["menudisplay"][$gtabid][1][157]): ?>
                            <li class="nav-item lmbGtabmenuIcon-157">
                                <?= LMBAction::ren(157,'icon'); // entknüpfen ?>
                            </li>
                            <?php endif; ?>
                        <?php endif; ?>

                    <?php endif; ?>
                    
                    <?php if($GLOBALS['greportlist_exist'] && !$gtab["menudisplay"][$gtabid][1][315] && ($LINK[175] || $LINK[176] OR $LINK[315])): ?>
                        <li class="nav-item lmbGtabmenuIcon-315">
                            <?= LMBAction::ren(315,'icon'); // Berichte neu ?>
                        </li>
                    <?php endif; ?>

                    <?php if($LINK[322] && !$gtab["menudisplay"][$gtabid][1][322]): ?>
                        <li class="nav-item lmbGtabmenuIcon-322">
                            <?= LMBAction::ren(322,'icon'); // Mails ?>
                        </li>
                    <?php endif; ?>

                    <?php if($GLOBALS['gdiaglist_exist'] && $LINK[232] && !$gtab["menudisplay"][$gtabid][1][232]): ?>
                        <li class="nav-item lmbGtabmenuIcon-232">
                            <?= LMBAction::ren(232,'icon'); // Diagramme ?>
                        </li>
                    <?php endif; ?>

                    <?php if($LINK[28] && !$gtab["menudisplay"][$gtabid][1][28]): ?>
                    <li class="nav-item lmbGtabmenuIcon-28">
                        <?= LMBAction::ren(28,'icon'); // refresh ?>
                    </li>
                    <?php endif; ?>

                    <?php
                    # extension
                    if(function_exists($GLOBALS['gLmbExt']['menuListIcons'][$gtabid])){
                        $GLOBALS['gLmbExt']['menuListIcons'][$gtabid]($gtabid,$gresult);
                    }
                    ?>

                </ul>
            <?php endif; ?>

        </div>
    </div>
</nav>
