<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $form_done;
global $verkn_addfrom;

global $lang;
global $session;
global $gtab;
global $userdat;
global $ID;

global $gfield;
global $verknpool;
global $gverkn;
global $greminder;


if($form_done){return true;}
$form_done = 1;

$action = $GLOBALS["action"];
if($GLOBALS["old_action"] == 'gtab_readonly'){$action = 'gtab_change';} # for scrolling after locked or versioned readonly dataset

?>
<form action="main.php" method="post" name="form1" id="form1" enctype="multipart/form-data" autocomplete="off">
    <input type="hidden" name="empty">
    <input type="hidden" name="ID" value="<?=$GLOBALS["ID"]?>">
    <input type="hidden" name="action" value="<?=$action?>">
    <input type="hidden" name="old_action" value="<?=$GLOBALS["action"]?>">
    <input type="hidden" name="gtabid" value="<?=$GLOBALS["gtabid"]?>">
    <input type="hidden" name="tab_group" value="<?=$GLOBALS["gtab"]["tab_group"][$GLOBALS["gtabid"]]?>">
    <input type="hidden" name="change_field">
    <input type="hidden" name="form_id" VALUE="<?=$GLOBALS["form_id"]?>">
    <input type="hidden" name="formlist_id" value="<?=$GLOBALS["formlist_id"];?>">
    <input type="hidden" name="snap_id" value="<?=$GLOBALS["snap_id"]?>">
    <input type="hidden" name="wfl_id" value="<?=$GLOBALS["wfl_id"]?>">
    <input type="hidden" name="wfl_inst" value="<?=$GLOBALS["wfl_inst"]?>">
    <input type="hidden" name="set_form">
    <input type="hidden" name="change_ok">
    <input type="hidden" name="view_symbolbar">
    <input type="hidden" name="view_all_verkn">
    <input type="hidden" name="filter_reset">
    <input type="hidden" name="filter_groupheader">
    <input type="hidden" name="filter_groupheaderKey">
    <input type="hidden" name="filter_tabulatorKey">
    <input type="hidden" name="filter_save">
    <input type="hidden" name="filter_gwidth">
    <input type="hidden" name="filter_validity">
    <input type="hidden" name="versdesc">
    <input type="hidden" name="lockingtime">
    <input type="hidden" name="history_search">
    <input type="hidden" name="request_flt" value="<?=$GLOBALS["request_flt"]?>">
    <input type="hidden" name="deterg">
    <input type="hidden" name="del_">
    <input type="hidden" name="use_record">
    <input type="hidden" name="use_typ">
    <input type="hidden" name="history_fields">
    <input type="hidden" name="verkn_key_field">
    <input type="hidden" name="posy">
    <input type="hidden" name="funcid">
    <input type="hidden" name="gfrist" VALUE="<?=$GLOBALS["gfrist"]?>">
    <input type="hidden" name="gfrist_desc">
    <input type="hidden" name="scrollto">
    <input type="hidden" name="verknpf" VALUE="<?=$GLOBALS["verknpf"]?>">
    <input type="hidden" name="verkn_addfrom" VALUE="<?=$GLOBALS["verkn_addfrom"]?>">
    <input type="hidden" name="verkn_poolid" VALUE="<?=$GLOBALS["verkn_poolid"]?>">
    <input type="hidden" name="filter_force_delete">
    <input type="hidden" name="wind_force_close">
    <input type="hidden" name="is_new_win" value="<?=$GLOBALS["is_new_win"]?>">

    <?php if($GLOBALS["verknpf"]){?>
        <input type="hidden" name="verkn_ID" VALUE="<?=$GLOBALS["verkn_ID"]?>">
        <input type="hidden" name="verkn_tabid" VALUE="<?=$GLOBALS["verkn_tabid"]?>">
        <input type="hidden" name="verkn_fieldid" VALUE="<?=$GLOBALS["verkn_fieldid"]?>">
        <input type="hidden" name="verkn_showonly" VALUE="<?=$GLOBALS["verkn_showonly"]?>">
        <input type="hidden" name="verkn_formid" VALUE="<?=$GLOBALS["verkn_formid"]?>">
    <?php }else{?>
        <input type="hidden" name="verkn_ID">
        <input type="hidden" name="verkn_tabid">
        <input type="hidden" name="verkn_fieldid">
        <input type="hidden" name="verkn_showonly">
    <?php }

    ?>

    <div class="container-fluid pb-4">

        <?php include(COREPATH . 'gtab/html/forms/parts/linktags.php'); ?>

        <?php include(COREPATH . 'gtab/html/forms/parts/menu.php'); ?>

        <?php if($ID) {include(COREPATH . 'gtab/html/forms/parts/notices.php');} ?>


        <nav>
            <div class="nav nav-tabs" role="tablist">

                <?php
                /** @var Limbas\gtab\lib\forms\elements\SectionElement $sectionElement */
                foreach($this->sections as $key => $sectionElement): ?>

                    <button class="nav-link <?=$key === 0 ? 'active' : ''?>" id="section-<?=$sectionElement->getId()?>-tab" data-bs-toggle="tab" data-bs-target="#section-<?=$sectionElement->getId()?>" type="button" role="tab" aria-controls="section-<?=$sectionElement->getId()?>" aria-selected="true"><?=$sectionElement->getLabel()?></button>

                <?php endforeach; ?>
            </div>
        </nav>

        <div class="row justify-content-center">
            <div class="col-lg-12">

                <div class="card border-top-0">
                    <?php if (($gtab["viewver"][$gtabid] && $gresult[$gtabid]['V_ID']) || (is_array($gtab["rverkn"][$gtab["verkn"][$gtabid]]) && lmb_count($gtab["rverkn"][$gtab["verkn"][$gtabid]]) > 1)): ?>
                        <div class="card-header">

                            <div class="d-flex flex-row align-items-center justify-content-end">
                                <?php
                                //switch between versions // print_version
                                if($gtab["viewver"][$gtabid] && $gresult[$gtabid]['V_ID']):

                                    $currentVersion = $gresult[$gtabid]['VACT'][0] || $gtab['validity'][$gtabid] == 2;
                                    ?>
                                    <select id="versionSelection" name="versionSelection" onchange="document.form1.ID.value=this.value;send_form(1);" class="form-select form-select-sm d-inline w-auto <?=($currentVersion)?'text-success':'text-danger'?>">
                                        <?php foreach ($gresult[$gtabid]["V_ID"] as $key => $value): ?>
                                            <option value="<?=$value?>" <?=($ID == $value) ? 'selected' : ''?>>Version <?=$key?></option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php endif; ?>


                                <?php
                                # 1:1 Relation-Select
                                if(is_array($gtab["rverkn"][$gtab["verkn"][$gtabid]]) && lmb_count($gtab["rverkn"][$gtab["verkn"][$gtabid]]) > 1): ?>
                                    <select onchange="document.form1.gtabid.value=this.value;send_form(1);" class="form-select form-select-sm d-inline w-auto ms-2">
                                        <?php
                                        foreach($gtab["rverkn"][$gtab["verkn"][$gtabid]] as $key => $value){
                                            echo "<OPTION VALUE=\"".$value."\"";
                                            if($value == $gtabid){echo "SELECTED";}
                                            echo ">".$gtab["desc"][$value];
                                        }
                                        ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="card-body tab-content">
                        <?php
                        /** @var Limbas\gtab\lib\forms\elements\SectionElement $sectionElement */
                        foreach($this->sections as $key => $sectionElement): ?>
                            <div class="tab-pane <?=$key === 0 ? 'show active' : ''?>" id="section-<?=$sectionElement->getId()?>" role="tabpanel" aria-labelledby="section-<?=$sectionElement->getId()?>-tab" tabindex="0">
                                <?php
                                /** @var Limbas\gtab\lib\forms\elements\LMBFormElement $formElement */
                                foreach($sectionElement->getChildren() as $formElement): ?>

                                    <?php if ($formElement->getType() !== 'section'): ?>
                                        <div class="row mb-3">
                                            <label for="<?= $formElement->getId() ?>" class="col-sm-4 col-form-label"><?= $formElement->getLabel() ?></label>
                                            <div class="col-sm-8">
                                                <?= $formElement->render() ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if(isset($ID)): ?>
                        <div class="card-footer text-end">
                            <div class="row">
                                <div class="col-md-<?= ($action != 'gtab_deterg' && !$readonly) ? 7 : 12 ?>">

                                    <div class="d-flex flex-row align-items-center">
                                        <?=$lang[722]?>

                                        <i title="<?=$lang[857]?>" class="lmb-icon lmb-first" onclick="document.form1.scrollto.value='start';send_form('1');"></i>
                                        <i title="<?=$lang[860]?>" class="lmb-icon lmb-previous" onclick="document.form1.scrollto.value='prev';send_form('1');"></i>
                                        <input type="text" class="form-control form-select-sm d-inline w-auto" value="<?=$ID?>" onchange="document.form1.scrollto.value=this.value;send_form('1');">

                                        <i title="<?=$lang[859]?>" class="lmb-icon lmb-next" onclick="document.form1.scrollto.value='next';send_form('1');"></i>
                                        <i title="<?=$lang[858]?>" class="lmb-icon lmb-last" onclick="document.form1.scrollto.value='end';send_form('1');"></i>
                                    </div>

                                </div>
                                <?php if ($action != 'gtab_deterg' && !$readonly): ?>
                                    <div class="col-md-5">

                                        <button type="button" class="btn btn-outline-secondary submit" name="lmbSbm" id="lmbSbm" onclick="document.form1.action.value='gtab_change'; send_form('1');"><?=$lang[33]?></button>
                                        <button type="button" class="btn btn-outline-secondary submit" name="lmbSbmClose" id="lmbSbmClose_<?=$gtabid?>_<?=$ID?>" onclick="document.form1.action.value='gtab_change'; send_form(1,0,1);"><?=$lang[2796]?></button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </div>


    </div>


</form>


