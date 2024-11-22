<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\form\FormType;

global $gfield;
global $popg;
global $verknpf;
?>

<div class="p-3 d-flex flex-column">

    <?php require(COREPATH  . 'gtab/html/table/parts/menu.php'); ?>
    
    <?php lmbGlistNotice($gtabid,$filter); ?>

    <?php if($gfield[$gtabid]['fullsearch']): ?>
    <div class="row">
        <div class="col-9">
        <div class="input-group input-group-sm mb-3">
            <button class="btn btn-outline-secondary" type="button" onclick="<?=$LINK["link_url"][14]?>">
                <i class="lmb-icon lmb-page-find"></i>
                <i class="lmb-icon lmb-exclamation alert-warning" id="fullsearchInfo" style=display:<?=($gsr[$gtabid] ? '' : 'none')?>></i>
            </button>
            <input type="text" id="globalSearch" onclick="event.stopPropagation();" class="form-control" name="gs[<?=$gtabid?>][0][0]" value="<?=$gsr[$gtabid][0][0]?>" onchange="checktyp(13,'gs[<?=$gtabid?>][0][0]','FulltextSearch','0','<?=$gtabid?>',this.value,'');">
        </div>
        </div>

        <div class="col-3">
        <div class="input-group input-group-sm mb-3">

            <?php if ($LINK[225]){?>
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#snapfilter-menu"><i class="lmb-icon lmb-filter"></i></button>
            <?php }else{?>
                <span class="input-group-text"><i class="lmb-icon lmb-filter"></i></span>
            <?php }?>

            <select id="globalFilter" class="form-select" onchange="document.form1.snap_id.value=this.value;document.form2.snap_id.value=this.value;send_form(1);"><option value='0'>
                <?php
                SNAP_print_select($gtabid,$snap_id);
                ?>
            </select>
        </div>
        </div>

        <?php #require(COREPATH  . 'gtab/html/contextmenus/gtab_filter.php'); ?>
    </div>
    <?php endif; ?>


    <?php if(!empty($form) && $form->formType === FormType::SEARCH):  ?>
        <?php $form->render() ?>        
    <?php endif; ?>


        
    <div class="mb-3 legacy-table d-inline-block flex-fill table-responsive">
            
            <table class="lmbfringegtab" id="GtabTableFringe" border="0" cellpadding="0" cellspacing="0">
                <?php

                # ----- Gruppiert -------
                if($popg[$gtabid][0]['null']){
                    foreach($popg[$gtabid][0]['null'] as $key => $value){
                        if($value AND $gfield[$gtabid]["groupable"][$key]){$group_fields[] = $key;}
                    }
                    # --- Prüfe ob Feld ausgewählt oder auf "ohne" gesetzt
                    if($group_fields){
                        $gresult = get_gresult($gtabid,1,$filter,$gsr,$verkn);


                        lmbGlistSearch($gtabid,0,0);
                        lmbGlistHeader($gtabid,$gresult,0);
                        table_group($gtabid,$group_fields,$filter,0,$gsr,$verkn);

                    }else{
                        $usedef = 1;
                        unset($popg[$gtabid]);
                    }
                }else{
                    $usedef = 1;
                }

                if($usedef){

                    # scrolling Header X-position
                    echo "<tr><td><div id=\"GtabTableFull\" style=\"overflow-x:auto;overflow-y:hidden;\"><table cellpadding=\"0\" cellspacing=\"0\">";
                    lmbGlistSearch($gtabid,$verknpf,$verkn);
                    lmbGlistHeader($gtabid,$gresult,0);
                    lmbGlistBody($gtabid,$gresult,0,$verkn,$verknpf,$filter,0,0,0,0,1);
                    echo "</table></div></td></tr>";



                }

                ?>

                <tr><td class="lmbGtabBottom"></td></tr>
            </table>
        
    </div>

    <div class="card card-body p-2">
        <?php require(COREPATH . 'gtab/html/table/parts/footer.php'); ?>
    </div>
    
</div>
