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
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_workflow">
        <input type="hidden" name="editid">
        <input type="hidden" name="wflid" value="<?=$wflid?>">

        <?php if ($wflid): ?>
        
        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr><th colspan="5"><?=$wfltask["wfl_name"]?></th>
            <tr>
                <th>ID</th>
                <th></th>
                <th><?=$lang[4]?></th>
                <th><?=$lang[2331]?></th>
                <th><?=$lang[164]?></th>
            </tr>
            </thead>

            <?php

            if($wfltask['name']):
                foreach ($wfltask['name'] as $key => $value): ?>
                
                <tr>
                    <td><?=$key?></td>
                    <td><a href="main_admin.php?action=setup_workflow&wflid=<?=$wflid?>&delid=<?=$key?>"><i class="lmb-icon lmb-trash cursor-pointer"></i></a></td>
                    <td><input type="text" id="taskname[<?=$key?>]"  name="taskname[<?=$key?>]" value="<?=$wfltask["name"][$key]?>" onchange="document.form1.editid.value=<?=$key?>;document.form1.submit();" class="form-control form-control-sm"></td>
                    <td><textarea name="taskparams[<?=$key?>]" onchange="document.form1.editid.value=<?=$key?>;document.form1.submit();" style="height: 16px;" onblur="this.style.height=$(document.getElementById('taskname[<?=$key?>]')).outerHeight();" onfocus="this.style.height='80px';" class="form-control form-control-sm"><?=htmlentities($wfltask["uparams"][$key],ENT_QUOTES,$umgvar["charset"])?></textarea></td>
                    <td>
                        <select name="tasktabid[<?=$key?>]" onchange="document.form1.editid.value=<?=$key?>;document.form1.submit();" class="form-select form-select-sm">
                            <option></option>
                            <?php
                            foreach($gtab["table"] as $tabkey => $tabval){
                                echo '<option value="'.$tabkey.'" '.(($wfltask["tab_id"][$key] == $tabkey)?'':'').'>'.$tabval.'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            
            <?php
                endforeach;
            endif;
                
            ?>

            <tfoot>

            <tr>
                <th colspan="2"></th>
                <th><?=$lang[4]?></th>
                <th></th>
                <th></th>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td><input type="text" name="new_taskname" class="form-control form-control-sm"></td>
                <td><button type="submit" name="new_task" class="btn btn-primary btn-sm" value="1"><?=$lang[2754]?></button></td>
                <td></td>
            </tr>
            </tfoot>

        </table>

        <?php else: ?>

            <table class="table table-sm table-striped mb-0 border bg-white">
                
                <?php if($workflow): ?>
                <thead>
                <tr>
                    <th>ID</th>
                    <th colspan="2"></th>
                    <th><?=$lang[4]?></th>
                    <th><?=$lang[2331]?></th>
                </tr>
                </thead>

                <?php endif; ?>
                
                <?php

                if($workflow):
                    
                    foreach ($workflow['name'] as $key => $value): ?>
                
                    <tr>
                        <td><?=$key?></td>
                        <td><a href="main_admin.php?action=setup_workflow&wflid=<?=$key?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></a></td>
                        <td><a href="main_admin.php?action=setup_workflow&delid=<?=$key?>"><i class="lmb-icon lmb-trash cursor-pointer"></i></a></td>
                        <td><input type="text" id="workflowname[<?=$key?>]" name="workflowname[<?=$key?>]" value="<?=$workflow["name"][$key]?>" onchange="document.form1.editid.value=<?=$key?>;document.form1.submit();" class="form-control form-control-sm"></td>
                        <td><textarea name="workflowparams[<?=$key?>]" onchange="document.form1.editid.value=$key;document.form1.submit();" style="height: 16px;" onblur="this.style.height=$(document.getElementById('workflowname[<?=$key?>]')).outerHeight();" onfocus="this.style.height='80px';" class="form-control form-control-sm"><?=htmlentities($workflow["params"][$key],ENT_QUOTES,$umgvar["charset"])?></textarea></td>
                    </tr>
                    
                    
                        <?php
                    endforeach;
                endif;
                

                ?>

                <tfoot>

                <tr>
                    <th colspan="3"></th>
                    <th><?=$lang[4]?></th>
                    <th></th>
                    <th></th>
                </tr>

                <tr>
                    <td colspan="3"></td>
                    <td><input type="text" name="new_workflowname" class="form-control form-control-sm"></td>
                    <td><button type="submit" name="new_workflow" class="btn btn-primary btn-sm" value="1"><?=$lang[2752]?></button></td>
                    <td></td>
                </tr>
                </tfoot>

            </table>

        <?php endif; ?>
    </FORM>

</div>
