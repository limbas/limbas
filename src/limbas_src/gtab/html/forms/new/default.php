<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $farbschema;
global $lang;
global $action;
global $gformlist;
global $umgvar;
global $gfield;
global $gtab;
global $form_done;
global $verkn_addfrom;

// display custmenu
if ($gtab["custmenu"][$gtabid]): ?>
    <script>
        top.openMenu(<?=$gtab["custmenu"][$gtabid]?>);
    </script>
<?php endif; ?>


<?php


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
    <input type="hidden" name="filter_status">
    <input type="hidden" name="filter_force_delete">
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
    <input type="hidden" name="wind_force_close">
    <input type="hidden" name="is_new_win" value="<?=$GLOBALS["is_new_win"]?>">
    
    
    <?php if($GLOBALS["verknpf"]): ?>
    <input type="hidden" name="verkn_ID" VALUE="<?=$GLOBALS["verkn_ID"]?>">
    <input type="hidden" name="verkn_tabid" VALUE="<?=$GLOBALS["verkn_tabid"]?>">
    <input type="hidden" name="verkn_fieldid" VALUE="<?=$GLOBALS["verkn_fieldid"]?>">
    <input type="hidden" name="verkn_showonly" VALUE="<?=$GLOBALS["verkn_showonly"]?>">
    <input type="hidden" name="verkn_formid" VALUE="<?=$GLOBALS["verkn_formid"]?>">
    <?php else: ?>
    <input type="hidden" name="verkn_ID">
    <input type="hidden" name="verkn_tabid">
    <input type="hidden" name="verkn_fieldid">
    <input type="hidden" name="verkn_showonly">
    <?php endif; ?>


<?php if (!isset($ID)): ?>
    <script>
        document.form1.action.value='gtab_erg';
        send_form(1);
    </script>
<?php return; endif; ?>

<?php
if ($filter["gwidth"][$gtabid]) {
    $sw = $filter["gwidth"][$gtabid];
} else {
    $sw = "1000px";
}
?>

<div class="p-3 form-legacy">
    
    <?php
        require( COREPATH . 'gtab/html/forms/parts/linktags.php');
    ?>
    
    <?php
        require( COREPATH . 'gtab/html/forms/parts/menu.php');
    ?>
    
    <?php
    if($ID) {
        require(COREPATH . 'gtab/html/forms/parts/notices.php');
    }
    ?>

    <?php if (isset($ID)): ?>


    <?php if ($filter["groupheader"][$gtabid]): ?>

        
        <nav class="nav nav-tabs" id="form-tab-nav">

            <?php
            $previousActiveTab = intval($filter['groupheaderKey'][$gtabid]);
            $activeTab = false;
            $activeTabKey = null;
            $hasAnyTab = false;
            
            foreach ($gfield[$gtabid]['sort'] as $gkey => $gvalue):
                $gkey = intval($gkey);
            
                if ($gfield[$gtabid]["field_type"][$gkey] == 100):

                    # ----------- Viewrule -----------
                    if ($gfield[$gtabid]["viewrule"][$gkey]) {
                        $returnval = eval(trim($gfield[$gtabid]["viewrule"][$gkey]) . ";");
                        if ($returnval) {
                            continue;
                        }
                    }

                    $hasAnyTab = true;
                    
                    if($previousActiveTab === $gkey) {
                        $activeTab = true;
                        $activeTabKey = $gkey;
                    }
                    
                ?>

                <button class="nav-link <?= $activeTab ? 'active' : '' ?>" id="section-<?=$gkey?>-tab" data-bs-toggle="tab" data-bs-target="#section-<?=$gkey?>" type="button" role="tab" aria-controls="section-<?=$gkey?>" aria-selected="true" data-tab-key="<?=$gkey?>"><?=$gfield[$gtabid]["spelling"][$gkey]?></button>
                
                <?php
                    $activeTab = false;
            endif;
            endforeach;
            ?>
        </nav>
        
    <?php endif; ?>
    
    <?php endif; ?>

    <div class="card <?= $filter['groupheader'][$gtabid] ? 'border-top-0' : '' ?>">
        <div class="card-body <?= $filter['groupheader'][$gtabid] ? 'tab-content' : '' ?>">
        

    <?php

    if (isset($ID)): ?>


        <?php
        if(($action == 'gtab_change' OR $action == 'gtab_neu') AND !$readonly){$edit=1;}

        $bzm = 1;
        $firstTab = true;
        $gf = null;
        
        foreach ($gfield[$gtabid]["sort"] as $key => $value):

            if($gfield[$gtabid]["grouping"][$key] OR $gfield[$gtabid]["field_name"][$key] == 'LMB_VALIDTO'){
                continue;
            }

            # ----------- Viewrule -----------
            if($gfield[$gtabid]["viewrule"][$key]){
                $returnval = eval(trim($gfield[$gtabid]["viewrule"][$key]).";");
                if($returnval){
                    continue;
                }
            }

            if($gfield[$gtabid]["field_type"][$key] == 100):
                if($filter['groupheader'][$gtabid]):
                    # grouping header with tabs
                    if($gf != $key): $gf = $key ?>
                        <?php if(!$firstTab): ?>
                            </div>
                        <?php endif; ?>
                        <div class="tab-pane <?=$activeTabKey === $key ?'show active':''?>" id="section-<?=$key?>">
                <?php $firstTab = false; endif; ?>
            <?php else: ?>
                    <h5 class="my-2 py-2 px-3 bg-secondary text-white" title="<?=$gfield[$gtabid]['beschreibung'][$key]?>"><?= $gfield[$gtabid]["spelling"][$key] ?></h5>
            <?php
            
                endif;
            endif;

            if($gfield[$gtabid]["funcid"][$key]):
                
                if($gfield[$gtabid]["INDIZE"][$key])
                {
                    $indexed1 = "Indexed: ".$gfield[$gtabid]["INDIZE_TIME"][$key];
                    $indexed2 = " (<B STYLE=\"color:green\">i</B>)";
                }
                else{
                    $indexed1 = null;
                    $indexed2 = null;
                }
                
                ?>

                <div class="row mb-2 position-relative">
                    <label class="col-md-3 col-form-label" title="<?=$gfield[$gtabid]["beschreibung"][$key]?>" <?= ($edittyp) ? 'OnClick="fieldtype(\'' . $gfield[$gtabid]['data_type'][$key] . '\',\'' . $gfield[$gtabid]['form_name'][$key] . '\',\'' . $indexed1 . '\');"' : '' ?>><?=$gfield[$gtabid]["spelling"][$key].$indexed2?><span class="gtabBodyDetailNeedTitle"><?=lmb_substr($gfield[$gtabid]["need"][$key],0,1)?></span></label>
                    <div class="col-md-9">
                        <?php display_dftyp($gresult,$gtabid,$key,$ID,$edit,' ',null,null,$bzm); ?>
                    </div>
                </div>
            <?php
                $bzm++;
                
            endif; ?>
                            
        <?php endforeach; ?>
    
    <?php else: ?>
        <div class="alert alert-danger">
            <?= $lang[98] ?>!
        </div>
    <?php endif; ?>

      <?php if($hasAnyTab && $filter['groupheader'][$gtabid]): ?>
                        </div>
        <?php endif; ?>

        </div>

        <?php if(isset($ID)): ?>
            <div class="card-footer text-end">
                <div class="row">
                    <div class="col-md-<?= ($action != 'gtab_deterg' && !$readonly) ? 6 : 12 ?>">

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
                        <div class="col-md-6 ps-0">

                            <button type="button" class="btn btn-outline-secondary submit" name="lmbSbm" id="lmbSbm" onclick="document.form1.action.value='gtab_change'; send_form('1');"><?=$lang[33]?></button>
                            <button type="button" class="btn btn-outline-secondary submit" name="lmbSbmClose" id="lmbSbmClose_<?=$gtabid?>_<?=$ID?>" onclick="document.form1.action.value='gtab_change'; send_form(1,0,1);" style="display: none"><?=$lang[2796]?></button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        
    </div>
</div>