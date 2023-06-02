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

if(isset($applyLegacy)): ?>

    <tr><td>

            <div <?=$cl?> style='float:left;'>
                <table border="0" cellpadding="0" cellspacing="2"><tr>
                        <td nowrap class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivMenuDatei');" id="edit4"><?=$lang[545]?></td><td>&nbsp;|&nbsp;</td>
                        <td nowrap class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivMenuBearbeiten');"><?=$lang[843]?></td><td>&nbsp;|&nbsp;</td>
                        <td nowrap class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivMenuAnsicht');"><?=$lang[1625]?></td><td>&nbsp;|&nbsp;</td>
                        <td nowrap class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivMenuExtras');"><?=$lang[1939]?></td>

                        <?php
                        // custmenu
                        if($GLOBALS['gcustmenu'][$gtabid][2]['id'][0]):
                            foreach($GLOBALS['gcustmenu'][$gtabid][2]['id'] as $cmkey => $cmid): ?>
                                <td>&nbsp;|&nbsp;</td><td nowrap class="gtabHeaderMenuTD hoverable" onclick="limbasDivShow(this,'','limbasDivCustMenu_<?=$cmid?>');"><?=$lang[$GLOBALS['gcustmenu'][$gtabid][2]['name'][$cmkey]]?></td>
                            <?php
                            endforeach;
                        endif;
                        ?>

                        <?php if($GLOBALS["view_version_status"]): ?>
                            <td>&nbsp;|&nbsp;</td><td nowrap class="gtabHeaderMenuTD" style="color:red;">&nbsp;&nbsp;&nbsp;(<?=$lang[2172]?> <?=$GLOBALS["view_version_status"]?>)</td>
                        <?php endif; ?>
                        <td width="100%"></td></tr></table>
            </div>
        </td></tr>

    <?php if($session["symbolbar"]): ?>
        <tr><td><div <?=$cl?>>

                    <table border="0" cellpadding="0" cellspacing="2"><tr>


                            <?php
                            if($gtab["edit"][$gtabid]){pop_picmenu(197,'','');}			# save

                            if($gtab["add"][$gtabid] AND !$readonly){pop_picmenu(1,'','');}			# new dataset
                            if($gtab["add"][$gtabid] AND $gtab["copy"][$gtabid] AND !$readonly){pop_picmenu(201,'','');}			# copy

                            if($gtab["ver"][$gtabid] AND $gtab["add"][$gtabid] AND !$readonly){			# verioning
                                pop_picmenu(235,'','');
                            }
                            if($gtab["delete"][$gtabid] AND !$readonly){pop_picmenu(11,'','');	}			# delete


                            if(($gtab["hide"][$gtabid] OR $gtab["trash"][$gtabid]) AND !$readonly){
                                if($filter["status"][$gtabid]) {
                                    pop_picmenu(166,'','');                            # restore
                                }else{
                                    if($gtab["trash"][$gtabid]) {
                                        pop_picmenu(313,'','');                        # trash
                                    }
                                    if($gtab["hide"][$gtabid]) {
                                        pop_picmenu(164,'','');                        # archive
                                    }
                                }
                            }

                            if($gtab["lock"][$gtabid] AND !$readonly AND !$readonly){
                                if($filter["locked"][$gtabid]){
                                    pop_picmenu(271,'','');									# unlock
                                }else{
                                    pop_picmenu(270,'','');									# lock
                                }
                            }

                            pop_picmenu(14,'','');											# search

                            if($gtab["edit"][$gtabid]){
                                if($LINK[161] AND $LINK[3] AND $filter["alter"][$gtabid] AND !$readonly){ 	# edit list
                                    pop_picmenu(161,'','',1);
                                }elseif($LINK[161] AND $LINK[10]){
                                    pop_picmenu(161,'','');
                                }
                            }

                            if($GLOBALS["verknpf"] AND !$readonly){
                                if($GLOBALS["verkn_showonly"]){
                                    pop_picmenu(158,'','');                                 # link
                                }else{
                                    pop_picmenu(157,'','');                                 # unlink
                                }
                            }
                            ?>


                            <td>&nbsp;&nbsp;</td>

                            <?php
                            if($GLOBALS["verknpf"] == 1){pop_picmenu(243,'','',$GLOBALS["verkn"]["showonly"]);}		# zeige verknüpfte
                            if($gtab["viewver"][$gtabid]){pop_picmenu(237,'','',$filter["viewversion"][$gtabid]);}	# zeige versionierte
                            if($gtab["status"][$gtabid]){pop_picmenu(165,'','',($filter["status"][$gtabid]==1 ? true : false));}			# zeige archivierte
                            if($gtab["status"][$gtabid]){pop_picmenu(314,'','',($filter["status"][$gtabid]==2 ? true : false));}			# zeige Papiertkorb
                            if($gtab["lockable"][$gtabid]){pop_picmenu(273,'','',$filter["locked"][$gtabid]);}		# zeige gesperrte
                            if($gtab["multitenant"][$gtabid] AND lmb_count($GLOBALS['lmmultitenants']['mid']) > 1){pop_picmenu(309,'','',$filter["multitenant"][$gtabid]);}	# zeige Mandanten
                            ?>

                            <td>&nbsp;&nbsp;</td>

                            <?php
                            if($GLOBALS["greportlist_exist"]){
                                #pop_picmenu(131,'','');										# Berichte
                                pop_picmenu(315,'','');
                            }
                            if($GLOBALS["gdiaglist_exist"]){
                                pop_picmenu(232,'','');										# Diagramme
                            }
                            ?>

                            <td>&nbsp;&nbsp;</td>

                            <?php
                            pop_picmenu(28,'','');											# refresh

                            # extension
                            if(function_exists($GLOBALS["gLmbExt"]["menuListIcons"][$gtabid])){
                                $GLOBALS["gLmbExt"]["menuListIcons"][$gtabid]($gtabid,$gresult);
                            }
                            ?>

                            <?php if($gfield[$gtabid]["fullsearch"]): ?>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td>
                                    <?php
                                    lmbGlistGlobalSearch($gtabid);
                                    ?>
                                <td>&nbsp;&nbsp;</td>
                                <td>
                                    <?php
                                    lmbGlistSearchFilter($gtabid);
                                    ?>
                                </td>
                            <?php endif; ?>

                        </tr></table></div></td></tr>

    <?php endif; ?>

<?php else: ?>

    <nav class="navbar navbar-expand-sm navbar-light bg-nav mb-3 lmbGtabmenu lmbGtabmenu-list lmbGtabmenu-table-<?=$gtabid?>">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" onclick="send_form('1');"><span id="tabs_<?=$gtabid?>"><?=$gtab['desc'][$gtabid]?></span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lmbDetailsNavbar" aria-controls="lmbDetailsNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="lmbDetailsNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                    <li class="nav-item lmbGtabmenu-file">
                        <a class="nav-link " href="#" onclick="limbasDivShow(this,'','limbasDivMenuDatei');" id="edit4"><?=$lang[545]?></a>
                    </li>
                    <li class="nav-item lmbGtabmenu-edit">
                        <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuBearbeiten');"><?=$lang[843]?></a>
                    </li>
                    <li class="nav-item lmbGtabmenu-view">
                        <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuAnsicht');"><?=$lang[1625]?></a>
                    </li>
                    <li class="nav-item lmbGtabmenu-extra">
                        <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuExtras');"><?=$lang[1939]?></a>
                    </li>

                    <?php
                    // custmenu
                    if($GLOBALS['gcustmenu'][$gtabid][2]['id'][0]):
                        foreach($GLOBALS['gcustmenu'][$gtabid][2]['id'] as $cmkey => $cmid): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivCustMenu_<?=$cmid?>');"><?=$lang[$GLOBALS['gcustmenu'][$gtabid][2]['name'][$cmkey]]?></a>
                            </li>
                        <?php
                        endforeach;
                    endif;
                    ?>



                </ul>

                <?php if($session["symbolbar"]): ?>

                    <ul class="navbar-nav ms-auto">


                        <?php if($gtab['edit'][$gtabid]): ?>
                            <li class="nav-item lmbGtabmenuIcon-197">
                                <?= LMBAction::ren(197,'icon'); // speichern ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['add'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-1">
                                <?= LMBAction::ren(1,'icon'); // neuer Datensatz ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['add'][$gtabid] && $gtab['copy'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-201">
                                <?= LMBAction::ren(201,'icon'); // Datensatz kopieren ?>
                            </li>
                        <?php endif; ?>


                        <?php if($gtab['ver'][$gtabid] && $gtab['add'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-235">
                                <?= LMBAction::ren(235,'icon'); // versionieren ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['delete'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-11">
                                <?= LMBAction::ren(11,'icon'); // löschen ?>
                            </li>
                        <?php endif; ?>

                        <?php if(($gtab['hide'][$gtabid] || $gtab['trash'][$gtabid]) && !$readonly): ?>

                            <?php if($gresult[$gtabid]['LMB_STATUS'][0]): ?>
                                <li class="nav-item lmbGtabmenuIcon-166">
                                    <?= LMBAction::ren(166,'icon'); // Archiv wiederherstellen ?>
                                </li>
                            <?php else: ?>
                                <li class="nav-item lmbGtabmenuIcon-313">
                                    <?= LMBAction::ren(313,'icon'); // trash ?>
                                </li>
                                <li class="nav-item lmbGtabmenuIcon-164">
                                    <?= LMBAction::ren(164,'icon'); // archivieren ?>
                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if($gtab['lock'][$gtabid] && !$readonly): ?>

                            <?php if($filter["locked"][$gtabid]): ?>
                                <li class="nav-item lmbGtabmenuIcon-271">
                                    <?= LMBAction::ren(271,'icon'); // unlock ?>
                                </li>
                            <?php else: ?>
                                <li class="nav-item lmbGtabmenuIcon-270">
                                    <?= LMBAction::ren(270,'icon'); // lock ?>
                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <li class="nav-item lmbGtabmenuIcon-14">
                            <?= LMBAction::ren(14,'icon'); // suchen ?>
                        </li>


                        <?php if($gtab['edit'][$gtabid] && ($LINK[161] || $LINK[3] || $LINK[10]) ): ?>
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

                            <?php if($GLOBALS['verkn_showonly']): ?>
                                <li class="nav-item lmbGtabmenuIcon-158">
                                        <?= LMBAction::ren(158,'icon'); // verknüpfen ?>
                                </li>
                            <?php else: ?>
                                <li class="nav-item lmbGtabmenuIcon-157">
                                    <?= LMBAction::ren(157,'icon'); // entknüpfen ?>
                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if($GLOBALS['greportlist_exist'] && ($LINK[175] || $LINK[176] OR $LINK[315])): ?>
                            <li class="nav-item lmbGtabmenuIcon-315">
                                <?= LMBAction::ren(315,'icon'); // Berichte neu ?>
                            </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['gdiaglist_exist'] && $LINK[232]): ?>
                            <li class="nav-item lmbGtabmenuIcon-232">
                                <?= LMBAction::ren(232,'icon'); // Diagramme ?>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item lmbGtabmenuIcon-28">
                            <?= LMBAction::ren(28,'icon'); // refresh ?>
                        </li>


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

<?php endif; ?>
