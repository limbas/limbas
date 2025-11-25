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
global $verkn_relationpath;


$tabbedForm = boolval($filter['groupheader'][$gtabid]);
$hasAnyTab = false;

    ?>


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

    <?php if($tabbedForm): ?>

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
    
    <div class="card <?=$tabbedForm ? 'border-top-0' : '' ?>">

        <?php if (isset($ID)): ?>
        
            <?php if(($action == 'gtab_change' OR $action == 'gtab_neu') AND !$readonly){$edit=1;} ?>            
        

            <div class="card-body <?= $tabbedForm ? 'tab-content' : '' ?>">


                <?php

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
                    } ?>

                    <?php if($gfield[$gtabid]["field_type"][$key] == 100): ?>
                    
                
                        <?php if($hasAnyTab && $tabbedForm): ?>

                            <?php if(!$firstTab): ?>
                                </div>
                            <?php endif; ?>
                
                        <div class="tab-pane <?=$activeTabKey === $key ?'show active':''?>" id="section-<?=$key?>">
                            <?php $firstTab = false; ?>
                        <?php else: ?>
                            <h5 class="my-2 py-2 px-3 bg-secondary text-white" title="<?=$gfield[$gtabid]['beschreibung'][$key]?>"><?= $gfield[$gtabid]["spelling"][$key] ?></h5>
                        <?php endif; ?>
                    
                    <?php endif; ?>
                

                    <?php require(COREPATH . 'gtab/html/forms/default/data-row.php'); ?>

                <?php endforeach; ?>

                <?php if($hasAnyTab && $tabbedForm): ?>
                        </div>
                <?php endif; ?>

            </div>
        
        
        
        
        <?php else: ?>
            <div class="alert alert-danger">
                <?= $lang[98] ?>!
            </div>
        <?php endif; ?>
        
    

    <?php
    if(isset($ID)) {
        require(COREPATH . 'gtab/html/forms/parts/footer.php');
    }
    ?>
    </div>
    
</div>
