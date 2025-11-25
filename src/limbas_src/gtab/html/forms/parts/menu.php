<?php

use Limbas\gtab\lib\forms\LMBAction;

global $lang;
global $session;
global $gtab;
global $userdat;
global $ID;
global $LINK;
global $gfield;
global $gformlist;
global $greportlist;
global $farbschema;
global $verkn_relationpath;
global $detail_isopenas;
global $action;

// breadcrumb
include(COREPATH . 'gtab/html/forms/parts/breadcrumb.php');
?>

<nav class="navbar navbar-expand-sm center-navigation bg-nav mb-2 lmbGtabmenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>">
    <div class="container-fluid">

        <?php
        if($verkn_relationpath){
            $link = "jump_to_breadcrumb()";
        }elseif(!$detail_isopenas OR $detail_isopenas == 'same'){
            $link = "document.location.href='main.php?action=gtab_erg&gtabid=$gtabid&snap_id=$snap_id'";
        }
        ?>
        <?php if($link){?>
        <button type="button" class="btn btn-outline-light btn-sm me-2" onclick="<?=$link?>"><?=$lang[3222]?></button>
        <?php } ?>

        <a class="navbar-brand" href="#" onclick="document.form1.action.value='gtab_erg';document.form1.form_id.value=document.form1.formlist_id.value;send_form('1');"><?=$gtab['desc'][$gtabid]?></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lmbDetailsNavbar" aria-controls="lmbDetailsNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="lmbDetailsNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">

                <?php if(!$gtab["menudisplay"][$gtabid][2]['file']):?>
                <li class="nav-item lmbGtabmenu-file">
                    <a class="nav-link" href="#" onclick="limbasDivShowContext(event,this,'<?=$gresult[$gtabid]["id"][0]?>','<?=$gtabid?>','<?=$gresult[$gtabid]["ERSTDATUM"][0]?>','<?=$gresult[$gtabid]["EDITDATUM"][0]?>','<?=$userdat['bezeichnung'][$gresult[$gtabid]["ERSTUSER"][0]]?>','<?=$userdat['bezeichnung'][$gresult[$gtabid]["EDITUSER"][0]]?>')"><?=$lang[545]?></a>
                </li>
                <?php endif; ?>
                <?php if(!$gtab["menudisplay"][$gtabid][2]['edit']):?>
                <li class="nav-item lmbGtabmenu-view">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuAnsicht');"><?=$lang[1625]?></a>
                </li>
                <?php endif; ?>
                <?php if(!$gtab["menudisplay"][$gtabid][2]['extra']):?>
                <li class="nav-item lmbGtabmenu-extra">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuExtras');"><?=$lang[1939]?></a>
                </li>
                <?php endif; ?>

                <?php
                # extension
                if(function_exists($GLOBALS["gLmbExt"]["menuChangeItems"][$gtabid])){
                    $GLOBALS["gLmbExt"]["menuChangeItems"][$gtabid]($gtabid,$gresult);
                }
                ?>
                
                <?php
                // custmenu
                if($GLOBALS['gcustmenu'][$gtabid][12]['id'][0]):
                    foreach($GLOBALS['gcustmenu'][$gtabid][12]['id'] as $cmkey => $cmid):
                        if($GLOBALS['gcustmenu'][$gtabid][12]['directlink'][$cmkey]){
                            $clink = lmb_pop_custmenu($cmid,$gtabid, $ID, linkonly:1);
                        }else{
                            $clink = "limbasDivShow(this,'','limbasDivCustMenu_$cmid');";
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="<?=$clink?>"><?=$lang[$GLOBALS['gcustmenu'][$gtabid][12]['name'][$cmkey]]?></a>
                        </li>
                <?php
                    endforeach;
                endif;
                ?>
                
                <!--
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown03" data-bs-toggle="dropdown" aria-expanded="false"><?=$lang[1939]?></a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown03">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                -->


                

            </ul>
            
            <?php if($session['symbolbar']): ?>
                <ul class="navbar-nav ms-auto">

                    <?php if($gtab['edit'][$gtabid] && $action == 'gtab_change' && !$readonly && !$gtab["menudisplay"][$gtabid][2][197]): ?>
                        <li class="nav-item lmbGtabmenuIcon-197">
                            <?= LMBAction::ren(197,'icon'); // speichern ?>
                        </li>
                    <?php endif; ?>

                    <?php if(!empty($ID)): ?>

                        <?php if($gtab['add'][$gtabid] && $action == 'gtab_change' && !$readonly && !$gtab["menudisplay"][$gtabid][2][1]): ?>
                            <li class="nav-item lmbGtabmenuIcon-1">
                                <?= LMBAction::ren(1,'icon'); // neuer Datensatz ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab["edit"][$gtabid] && !$isview): ?>
                            <?php if($action == "gtab_change" && !$gtab["menudisplay"][$gtabid][2][6]): ?>
                                <li class="nav-item lmbGtabmenuIcon-6">
                                    <?= LMBAction::ren(6,'icon'); // Datensatz kopieren ?>
                                </li>

                            <?php elseif($gtab["edit"][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][2][3]): ?>
                                <li class="nav-item lmbGtabmenuIcon-3">
                                    <?= LMBAction::ren(3,'icon'); // bearbeiten ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>


                        <?php if($gtab['add'][$gtabid] && $gtab['copy'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][2][201]): ?>
                            <li class="nav-item lmbGtabmenuIcon-201">
                                <?= LMBAction::ren(201,'icon'); // Datensatz kopieren ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['ver'][$gtabid] && $gtab['add'][$gtabid] && $gresult[$gtabid]["VACT"][0] && !$readonly && !$gtab["menudisplay"][$gtabid][2][235]): ?>
                            <li class="nav-item lmbGtabmenuIcon-235">
                                <?= LMBAction::ren(235,'icon'); // versionieren ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['delete'][$gtabid] && !$readonly && !$gtab["menudisplay"][$gtabid][2][11]): ?>
                            <li class="nav-item lmbGtabmenuIcon-11">
                                <?= LMBAction::ren(11,'icon'); // löschen ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['lock'][$gtabid] && !$gresult[$gtabid]['LOCK']['USER'][$ID] && !$readonly): ?>
                            <?php if($gresult[$gtabid]['LOCK']['STATIC'][$ID]): ?>
                                <?php if(!$gtab["menudisplay"][$gtabid][2][271]): ?>
                                <li class="nav-item lmbGtabmenuIcon-271">
                                    <?= LMBAction::ren(271,'icon'); // freigeben ?>
                                </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if(!$gtab["menudisplay"][$gtabid][2][270]): ?>
                                <li class="nav-item  lmbGtabmenuIcon-270">
                                    <?= LMBAction::ren(270,'icon'); // sperren ?>
                                </li>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endif; ?>


                        <?php if(($gtab['hide'][$gtabid] || $gtab['trash'][$gtabid]) && !$readonly): ?>

                            <?php if($gresult[$gtabid]['LMB_STATUS'][0]): ?>
                                <?php if(!$gtab["menudisplay"][$gtabid][2][166]): ?>
                                <li class="nav-item lmbGtabmenuIcon-166">
                                    <?= LMBAction::ren(166,'icon'); // Archiv wiederherstellen ?>
                                </li>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if($gtab['trash'][$gtabid] && !$gtab["menudisplay"][$gtabid][2][313]): ?>
                                <li class="nav-item lmbGtabmenuIcon-313">
                                    <?= LMBAction::ren(313,'icon'); // trash ?>
                                </li>
                                <?php endif; ?>
                                <?php if($gtab['hide'][$gtabid] && !$gtab["menudisplay"][$gtabid][2][164]): ?>
                                <li class="nav-item lmbGtabmenuIcon-164">
                                    <?= LMBAction::ren(164,'icon'); // archivieren ?>
                                </li>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endif; ?>


                        <?php if($LINK[9] && !$gtab["menudisplay"][$gtabid][2][9]): ?>
                            <li class="nav-item lmbGtabmenuIcon-9">
                            <?php
                            # show history
                            $onClickShowContext = "limbasDivShowContext(event,this,'{$gresult[$gtabid]["id"][0]}','{$gtabid}','{$gresult[$gtabid]["ERSTDATUM"][0]}','{$gresult[$gtabid]["EDITDATUM"][0]}','{$userdat["bezeichnung"][$gresult[$gtabid]["ERSTUSER"][0]]}','{$userdat["bezeichnung"][$gresult[$gtabid]["EDITUSER"][0]]}');";
                            $onClickOpenMenu = "limbasDivShow(this,null,'limbasDivMenuInfo');";
                            echo LMBAction::ren(9,'icon', event: 'onclick="' . $onClickShowContext . $onClickOpenMenu . '"');
                            ?>
                            </li>
                        <?php endif; ?>

                        <?php if($LINK[10] && !$gtab["menudisplay"][$gtabid][2][10]): ?>
                        <li class="nav-item lmbGtabmenuIcon-10">
                            <?= LMBAction::ren(10,'icon'); // Liste ?>
                        </li>
                        <?php endif; ?>

                        <?php if($GLOBALS["greportlist_exist"] && ($LINK[175] || $LINK[176] OR $LINK[315]) && !$gtab["menudisplay"][$gtabid][2][315]): ?>
                            <li class="nav-item lmbGtabmenuIcon-315">
                                <?= LMBAction::ren(315,'icon'); // Berichte neu ?>
                            </li>
                        <?php endif; ?>

                        <?php if($LINK[322] && !$gtab["menudisplay"][$gtabid][2][322]): ?>
                            <li class="nav-item lmbGtabmenuIcon-322">
                                <?= LMBAction::ren(322,'icon'); // Mails ?>
                            </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['gformlist_exist'] && !$gtab["menudisplay"][$gtabid][2][132]): ?>
                            <li class="nav-item lmbGtabmenuIcon-132">
                                <?= LMBAction::ren(132,'icon'); // Formulare ?>
                            </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['gdiaglist_exist'] && $LINK[232] && !$gtab["menudisplay"][$gtabid][2][232]): ?>
                            <li class="nav-item lmbGtabmenuIcon-232">
                                <?= LMBAction::ren(232,'icon'); // Diagramme ?>
                            </li>
                        <?php endif; ?>

                        <?php if($LINK[109] && !$gtab["menudisplay"][$gtabid][2][109]): ?>
                        <li class="nav-item lmbGtabmenuIcon-109">
                            <?= LMBAction::ren(109,'icon'); // Wiedervorlage ?>
                        </li>
                        <?php endif; ?>


                    <?php endif; ?>

                    <?php
                    if(function_exists($GLOBALS["gLmbExt"]["menuChangeIcons"][$gtabid])){
                        $GLOBALS["gLmbExt"]["menuChangeIcons"][$gtabid]($gtabid,$gresult);
                    }
                    ?>

                </ul>
            <?php endif; ?>
            
            
            <?php if($gtab['viewver'][$gtabid] || lmb_count($gtab["rverkn"][$gtab["verkn"][$gtabid]]) > 1): ?>
                <div class="d-flex ms-3 gap-1">
                    
                    <?php if($gtab["viewver"][$gtabid] && $gresult[$gtabid]['V_ID']): ?>
                    
                    <?php $vactive = ($gresult[$gtabid]['VACT'][0] || $gtab['validity'][$gtabid] == 2); ?>

                    <select class="form-select form-select-sm <?= $vactive ? 'text-success' : 'text-danger' ?>" ID="versionSelection" NAME="versionSelection" data-active="<?= $vactive ? 1 : 0 ?>" OnChange="document.form1.ID.value=this.value;send_form(1);">
                        <?php foreach ($gresult[$gtabid]["V_ID"] as $key => $value): ?>
                            <option value="<?=$value?>" <?= ($ID == $value) ? 'selected' : ''?>>Version <?=$key?></option>
                        <?php endforeach; ?>
                    </select>

                    <?php endif; ?>
                    
                    <?php
                    if(lmb_count($gtab["rverkn"][$gtab["verkn"][$gtabid]]) > 1): ?>
                        <select class="form-select form-select-sm" OnChange="document.form1.gtabid.value=this.value;send_form(1);">
                    <?php foreach($gtab["rverkn"][$gtab["verkn"][$gtabid]] as $key => $value): ?>
                        <option value="<?=$value?>" <?= ($value == $gtabid) ? 'selected' : ''?>><?=$gtab["desc"][$value]?></option>
                    <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
</nav>
