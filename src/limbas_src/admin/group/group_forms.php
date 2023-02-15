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
        <input type="hidden" name="ID" value="<?=$ID?>">
        <input type="hidden" name="action" value="setup_group_forms">

        <div class="row">
        <?php
        $activeTabLinkId = 291;
        require(__DIR__.'/group_tabs.php') ?>

        <div class="tab-content col-9 border border-start-0 bg-white">
            <div class="tab-pane active p-3">

                <h5><i class="lmb-icon lmb-group"></i>&nbsp;<?=$groupdat["name"][$ID]?></h5>

                <hr>

                <table class="table table-sm table-striped table-hover border bg-white">
                    <thead>
                    <tr>
                        <th><i class="lmb-icon-cus lmb-icon-8 lmb-form"></i>&nbsp;<?=$lang[2281]?></th>
                        <th><?=$lang[575]?></th>
                        <th><?=$lang[2088]?></th>
                    </tr>
                    </thead>

                    <?php

                    if($rulelist_){
                        foreach ($rulelist_ as $key => $value):
                            if($gtab["table"][$key]){
                                $desc = $gtab["desc"][$key];
                            }else{
                                $desc = $lang[1986];
                            }
                            ?>

                            <tr class="table-section">
                                <td colspan="3"><i class="lmb-icon-8 lmb-table"></i> <?=$desc?></td>
                            </tr>
                            <?php


                            if($value["id"]){
                                foreach ($value["id"] as $key2 => $value2){
                                    if($value2){
                                        if($grouprule["hasview"][$value2]){$CHECKED1 = "CHECKED";}else{$CHECKED1 = "";}
                                        if($grouprule["hashidden"][$value2]){$CHECKED2 = "CHECKED";}else{$CHECKED2 = "";}
                                        ?>

                                        <tr>
                                            <td><?=$value["name"][$key2]?></td>
                                            <td>
                                                <?php if($levelrule["hasview"][$value2] OR !$group_level): ?>
                                                    <input type="checkbox" name="setrule[<?=$value2?>]" <?=$CHECKED1?>>
                                                <?php else: ?>
                                                    <input type="checkbox" <?=$CHECKED1?> readonly disabled>";
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($CHECKED1): ?>
                                                    <input type="checkbox" name="sethidden[<?=$value2?>]" <?=$CHECKED2?>>
                                                <?php endif; ?>
                                            </td>
                                        </tr>


                                        <?php


                                    }
                                }
                            }
                        endforeach;
                    }
                        
                    ?>

                </table>

                <?php require __DIR__ . '/submit-footer.php'; ?>

            </div>
        </div>
        </div>


    </FORM>
</div>

