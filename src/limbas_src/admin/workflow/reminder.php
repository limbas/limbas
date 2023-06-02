<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */





?>

<script language="JavaScript">
    function deleteReminder(id, name) {
        var desc = confirm('Delete reminder ' + id + ' (' + name + ')?');
        if(desc){
            document.location.href='main_admin.php?action=setup_reminder&delid=' + id;
        }
    }
</script>

<div class="container-fluid p-3">
    <FORM ACTION="main_admin.php" METHOD="post" NAME="form1">
        <input type="hidden" name="action" value="setup_reminder">
        <input type="hidden" name="editid">

        <table class="table table-sm table-striped mb-0 border bg-white">
            <thead>
            <tr>
                <th>ID</th>
                <th></th>
                <th></th>
                <th><?=$lang[4]?></th>
                <th><?=$lang[164]?></th>
                <th><?=$lang[2756]?></th>
                <th><?=$lang[1169]?></th>
                <th><?=$lang[2742]?></th>
                <th><?=$lang[2685]?></th>
            </tr>
            </thead>

            <?php
            
            if($reminder):
                
                foreach ($reminder as $tabid => $value):
                    #if($tabid == "name_id") { continue; }

                    echo '<tr><td colspan="9">'.$gtab['table'][$tabid].'</td></tr>';

                    foreach ($value['name'] as $id => $name): ?>
                        
                        <tr>
                            <td><?=$id?></td>
                            <td><i class="lmb-icon lmb-trash cursor-pointer" onclick="deleteReminder(<?=$id?>, '<?=$name?>')"></i></td>
                            <td><a href="main_admin.php?action=setup_reminder&sort=up&sortid=<?=$id?>&tabid=<?=$tabid?>"><i class="lmb-icon lmb-long-arrow-up"></i></a>&nbsp;<a href="main_admin.php?action=setup_reminder&sort=down&sortid=<?=$id?>&tabid=<?=$tabid?>"><i class="lmb-icon lmb-long-arrow-down"></i></a></td>
                            <td><input type="text" name="remindername[<?=$id?>]" value="<?=$name?>" onchange="document.form1.editid.value=<?=$id?>;document.form1.submit();" class="form-control form-control-sm"></td>
                            <td><?=$gtab['table'][$tabid]?></td>
                            <td>
                                <?php
                                if($gformlist[$tabid]['desc']):
                                    $forms = '';
                                    foreach($gformlist[$tabid]["name"] as $key => $val){
                                        if($gformlist[$tabid]["typ"][$key] == 2){
                                            if($key == $value['forml_id'][$id]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                            $forms .= "<option value=\"".$key."\" $SELECTED>".$val;
                                        }
                                    }

                                    if ($forms): ?>

                                        <select name="reminderforml[<?=$id?>]" onchange="document.form1.editid.value=<?=$id?>;document.form1.submit();" class="form-select form-select-sm">
                                            <option></option>
                                            <?=$forms?>
                                        </select>
                                <?php
                                        
                                    endif;
                                endif;
                                
                                ?>
                            </td>
                            <td>
                                <?php
                                if($gformlist[$tabid]['name']):
                                    $forms = '';
                                    foreach($gformlist[$tabid]["name"] as $key => $val){
                                        if($gformlist[$tabid]["typ"][$key] == 1){
                                            if($key == $value['formd_id'][$id]){$SELECTED = "SELECTED";}else{$SELECTED = "";}
                                            $forms .= "<option value=\"".$key."\" $SELECTED>".$val;
                                        }
                                    }

                                    if ($forms): ?>

                                        <select name="reminderformd[<?=$id?>]" onchange="document.form1.editid.value=<?=$id?>;document.form1.submit();" class="form-select form-select-sm">
                                            <option></option>
                                            <option value="-1" <?if($value['formd_id'][$id] == -1){echo 'selected';}?>>-<?=$lang[2710]?>-</option>
                                            <option disabled>________________</option>
                                            <?=$forms?>
                                        </select>
                                    <?php

                                    endif;
                                endif;

                                ?>
                            </td>
                            <td><input type="checkbox" name="remindergrouping[<?=$id?>]" <?=$value['groupbased'][$id]?'checked':''?> value="1" onchange="document.form1.editid.value=<?=$id?>;document.form1.submit();"></td>
                            <td><input type="checkbox" name="reminderdefault[<?=$id?>]" <?=$value['defaultselection'][$id]?'checked':''?> value="1" onchange="document.form1.editid.value=<?=$id?>;document.form1.submit();"></td>
                        </tr>
                    <?php
                    endforeach;
                endforeach;
            endif;

            ?>

            <tfoot>


                <tr>
                    <th colspan="3"></th>
                    <th><?=$lang[4]?></th>
                    <th><?=$lang[164]?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
    
                <tr>
                    <td colspan="3"></td>
                    <td><input type="text" name="new_remindername" class="form-control form-control-sm"></td>
                    <td>
                        <SELECT NAME="new_remindertable" class="form-select form-select-sm">
                            <OPTION></OPTION>
                            <?php
                            foreach($gtab["table"] as $tabkey => $tabval){
                                echo "<option value=\"".$tabkey."\">".$tabval.'</option>';
                            }
                            ?>
                        </SELECT>
                    </td>
                    <td><button type="submit" name="new_reminder" class="btn btn-primary btn-sm" value="1"><?=$lang[2740]?></button></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
            
            
        </table>


    </FORM>

</div>
