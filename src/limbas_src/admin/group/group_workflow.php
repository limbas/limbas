<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>


<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" name="form1">
        <input type="hidden" name="action" VALUE="setup_group_workfl">
        <input type="hidden" name="ID" VALUE="<?=$ID?>">
        <input type="hidden" name="maingroup">
        <input type="hidden" name="change">
        <input type="hidden" name="duf" value="1">

        <div class="row">
        <?php
        $activeTabLinkId = 293;
        require(__DIR__.'/group_tabs.php') ?>

        <div class="tab-content col-9 border border-start-0 bg-contrast">
            <div class="tab-pane active p-3">

                <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>

                <hr>

                <table class="table table-sm table-striped table-hover border bg-contrast">
                    <thead>
                    <tr>
                        <th><i class="lmb-icon-cus lmb-icon-8 lmb-workflow-car"></i>&nbsp;<?=$lang[2035]?></th>
                        <th><?=$lang[575]?></th>
                        <th><?=$lang[2088]?></th>
                    </tr>
                    </thead>
                    
                    <?php

                    if($rulelist_){
                        foreach ($rulelist_['id'] as $key => $rid){
                            if($grouprule["hasview"][$rid]){$CHECKED1 = "CHECKED";}else{$CHECKED1 = "";}
                            if($grouprule["hashidden"][$rid]){$CHECKED2 = "CHECKED";}else{$CHECKED2 = "";}

                            ?>

                            <tr>
                                <td><?=$rulelist_["name"][$key]?></td>
                                <td>
                                    <?php if($levelrule["hasview"][$rid] OR !$group_level): ?>
                                        <input type="checkbox" name="setrule[<?=$rid?>]" <?=$CHECKED1?>>
                                    <?php else: ?>
                                        <input type="checkbox" <?=$CHECKED1?> readonly disabled>";
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($CHECKED1): ?>
                                        <input type="checkbox" name="sethidden[<?=$rid?>]" <?=$CHECKED2?>>
                                    <?php endif; ?>
                                </td>
                            </tr>


                            <?php
                            
                        }
                    }

                    ?>

                </table>
                

                <?php require __DIR__ . '/submit-footer.php'; ?>

            </div>
        </div>
        </div>

    </FORM>
</div>
