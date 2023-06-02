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
global $action;


# ----------- Verknüpfungsreiter ------------ // TODO
if(isset($showheader) AND !in_array('4',$showheader)){$verkn_addfrom = null;}
if(!$showheader AND $verkn_addfrom){
    echo "<TR><TD nowrap>";
    print_verknaddfrom($verkn_addfrom,$gtabid);
    echo "</TD></TR>";
    echo "<TR><TD COLSPAN=\"2\"><DIV width=\"100%\" class=\"lmbTableHeader\"></DIV></TD></TR>\n";
}

?>


<nav class="navbar navbar-expand-sm navbar-light bg-nav mb-2 lmbGtabmenu lmbGtabmenu-detail lmbGtabmenu-table-<?=$gtabid?>">
    <div class="container-fluid">
        <a class="navbar-brand" href="#" onclick="document.form1.action.value='gtab_erg';document.form1.form_id.value=document.form1.formlist_id.value;send_form('1');"><?=$gtab['desc'][$gtabid]?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lmbDetailsNavbar" aria-controls="lmbDetailsNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="lmbDetailsNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-sm-0">


                <li class="nav-item lmbGtabmenu-file">
                    <a class="nav-link" href="#" onclick="limbasDivShowContext(event,this,'<?=$gresult[$gtabid]["id"][0]?>','<?=$gtabid?>','<?=$gresult[$gtabid]["ERSTDATUM"][0]?>','<?=$gresult[$gtabid]["EDITDATUM"][0]?>','<?=$userdat['bezeichnung'][$gresult[$gtabid]["ERSTUSER"][0]]?>','<?=$userdat['bezeichnung'][$gresult[$gtabid]["EDITUSER"][0]]?>')"><?=$lang[545]?></a>
                </li>
                <li class="nav-item lmbGtabmenu-view">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuAnsicht');"><?=$lang[1625]?></a>
                </li>
                <li class="nav-item lmbGtabmenu-extra">
                    <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivMenuExtras');"><?=$lang[1939]?></a>
                </li>
                
                <?php
                # extension
                if(function_exists($GLOBALS["gLmbExt"]["menuChangeItems"][$gtabid])){
                    $GLOBALS["gLmbExt"]["menuChangeItems"][$gtabid]($gtabid,$gresult);
                }
                ?>
                
                <?php
                // custmenu
                if($GLOBALS['gcustmenu'][$gtabid][12]['id'][0]):
                    foreach($GLOBALS['gcustmenu'][$gtabid][12]['id'] as $cmkey => $cmid): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="limbasDivShow(this,'','limbasDivCustMenu_<?=$cmid?>');"><?=$lang[$GLOBALS['gcustmenu'][$gtabid][2]['name'][$cmkey]]?></a>
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


                    <?php if($gtab['edit'][$gtabid] && $action == 'gtab_change' AND !$readonly): ?>
                        <li class="nav-item lmbGtabmenuIcon-197">
                            <?= LMBAction::ren(197,'icon'); // speichern ?>
                        </li>
                    <?php endif; ?>

                    <?php if(!empty($ID)): ?>

                        <?php if($gtab["edit"][$gtabid] && !$isview): ?>

                            <?php if($action == "gtab_change"): ?>
                                <li class="nav-item lmbGtabmenuIcon-6">
                                <?php
                                # show history
                                $onClickShowContext = "limbasDivShowContext(event,this,'{$gresult[$gtabid]["id"][0]}','{$gtabid}','{$gresult[$gtabid]["ERSTDATUM"][0]}','{$gresult[$gtabid]["EDITDATUM"][0]}','{$userdat["bezeichnung"][$gresult[$gtabid]["ERSTUSER"][0]]}','{$userdat["bezeichnung"][$gresult[$gtabid]["EDITUSER"][0]]}');";
                                $onClickOpenMenu = "limbasDivShow(this,null,'limbasDivMenuInfo');";
                                echo LMBAction::ren(6,'icon', event: 'onclick="' . $onClickShowContext . $onClickOpenMenu . '"');
                                ?>
                                </li>

                            <?php elseif($gtab["edit"][$gtabid] && !$readonly): ?>
                                <li class="nav-item lmbGtabmenuIcon-3">
                                    <?= LMBAction::ren(3,'icon'); // bearbeiten ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>



                        <?php if($gtab['add'][$gtabid] && $action == 'gtab_change' && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-1">
                                <?= LMBAction::ren(1,'icon'); // neuer Datensatz ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['add'][$gtabid] && $gtab['copy'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-201">
                                <?= LMBAction::ren(201,'icon'); // Datensatz kopieren ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['ver'][$gtabid] && $gtab['add'][$gtabid] && $gresult[$gtabid]["VACT"][0] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-235">
                                <?= LMBAction::ren(235,'icon'); // versionieren ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['delete'][$gtabid] && !$readonly): ?>
                            <li class="nav-item lmbGtabmenuIcon-11">
                                <?= LMBAction::ren(11,'icon'); // löschen ?>
                            </li>
                        <?php endif; ?>

                        <?php if($gtab['lock'][$gtabid] && !$gresult[$gtabid]['LOCK']['USER'][$ID] && !$readonly): ?>
                            <?php if($gresult[$gtabid]['LOCK']['STATIC'][$ID]): ?>
                                <li class="nav-item lmbGtabmenuIcon-271">
                                    <?= LMBAction::ren(271,'icon'); // freigeben ?>
                                </li>
                            <?php else: ?>
                                <li class="nav-item  lmbGtabmenuIcon-270">
                                    <?= LMBAction::ren(270,'icon'); // sperren ?>
                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <?php if($gtab['hide'][$gtabid] && !$readonly): ?>
                            <?php if($gresult[$gtabid]['DEL'][0]): ?>
                                <li class="nav-item lmbGtabmenuIcon-166">
                                    <?= LMBAction::ren(166,'icon'); // Archiv wiederherstellen ?>
                                </li>
                            <?php else: ?>
                                <li class="nav-item lmbGtabmenuIcon-164">
                                    <?= LMBAction::ren(164,'icon'); // archivieren ?>
                                </li>
                            <?php endif; ?>

                        <?php endif; ?>

                        <li class="nav-item lmbGtabmenuIcon-10">
                            <?= LMBAction::ren(10,'icon'); // Liste ?>
                        </li>

                        <?php if($GLOBALS["greportlist_exist"] && ($LINK[175] || $LINK[176] OR $LINK[315])): ?>
                            <li class="nav-item lmbGtabmenuIcon-315">
                                <?= LMBAction::ren(315,'icon'); // Berichte neu ?>
                            </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['gformlist_exist']): ?>
                            <li class="nav-item lmbGtabmenuIcon-132">
                                <?= LMBAction::ren(132,'icon'); // Formulare ?>
                            </li>
                        <?php endif; ?>

                        <?php if($GLOBALS['gdiaglist_exist'] AND $LINK[232]): ?>
                            <li class="nav-item lmbGtabmenuIcon-232">
                                <?= LMBAction::ren(232,'icon'); // Diagramme ?>
                            </li>
                        <?php endif; ?>

                        <?php if($LINK[109]): ?>
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
