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

<FORM ACTION="main_admin.php" METHOD=post name="form1">
    <input type="hidden" name="action" value="setup_currency">
    <?php if (isset($currates)) { ?>
        <input type="hidden" name="currates" value="1">
    <?php } ?>
    <input type="hidden" name="changecur">


    <ul class="nav nav-tabs">        
        <li class="nav-item">
            <a class="nav-link <?=(isset($currates))?'':'active bg-contrast'?>" href="main_admin.php?action=setup_currency"><?=$lang[$LINK["name"][$LINK_ID['setup_currency']]]?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?=(isset($currates))?'active bg-contrast':''?>" href="main_admin.php?action=setup_currency&currates=1"><?=$lang[2973]?></a>
        </li>
    </ul>
    <div class="tab-content border border-top-0 bg-contrast">
        <div class="tab-pane active">

            
            

            <?php if (!isset($currates)) { ?>

                <table class="table table-sm table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0"><?= $lang[4]; ?></th>
                        <th class="border-top-0"><?= $lang[2976]; ?></th>
                        <th class="border-top-0"><?=$lang[2718]; ?></th>
                        <th class="border-top-0"></th>
                    </tr>
                    </thead>

                    <?php foreach ($result_currencies as $id => $currency) : ?>
                    
                    <TR>
                        <TD><INPUT TYPE="text" NAME="cur_n<?= $id ?>" value="<?=$currency['name']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'" class="form-control form-control-sm">
                        </TD>
                        <TD><INPUT TYPE="text" NAME="cur_c<?= $id ?>" value="<?=$currency['code']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'" class="form-control form-control-sm">
                        </TD>
                        <TD><INPUT TYPE="text" NAME="cur_s<?= $id ?>" value="<?=$currency['symbol']; ?>" OnChange="document.form1.changecur.value=document.form1.changecur.value+',<?= $id ?>'" class="form-control form-control-sm">
                        </TD>
                        <TD><A HREF="main_admin.php?action=setup_currency&del=1&id=<?= urlencode($id) ?>"><i class="lmb-icon lmb-trash"></i></A></TD>
                    </TR>
                    <?php endforeach; ?>
                    <tfoot>
                        <tr class="border-bottom border-top">
                            <td colspan="6">
                                <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[522] ?></button>
                            </td>
                        </tr>
                        
                        
                        
                        <tr>
                            <th><?= $lang[4];?></th>
                            <th><?= $lang[2976];?></th>
                            <th><?= $lang[2718];?></th>
                            <th></th>
                        </tr>
    
                        <tr>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="name"></td>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="code" maxlength="3"></td>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="70" NAME="symbol"></td>
                            <td><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
                        </tr>
                    </tfoot>
                </table>
                
                
            <?php } else { ?>

                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="border-top-0"><?= $lang[2049]; ?> <?= $lang[1367]; ?></th>
                            <th class="border-top-0"><?= $lang[2974]; ?> <?= $lang[1367]; ?></th>
                            <th class="border-top-0"><?=$lang[2973]; ?></th>
                            <th class="border-top-0" colspan="2"></th>
                        </tr>
                    </thead>

                    <?php foreach ($result_rates as $exrate) : ?>

                    <TR>
                        <TD><?= $exrate['curfromc']; ?></TD>
                        <TD><?= $exrate['curtoc']; ?></TD>
                        <TD><?= $exrate['excount']; ?></TD>
                        <TD colspan="2"></TD>
                        </TR>
                    <?php endforeach; ?>
                    <tfoot>


                    <tr>
                        <th><?= $lang[2049]; ?> <?= $lang[1367]; ?></th>
                        <th><?= $lang[2974]; ?> <?= $lang[1367]; ?></th>
                        <th><?= $lang[2975]; ?></th>
                        <th><?= $lang[197]; ?></th>
                        <th></th>
                    </tr>

                    <TR>
                        <TD><select name="curfrom" class="form-select form-select-sm">
                                <?php
                                foreach ($result_currencies as $id => $currency) {
                                    echo '<option value="'.$id.'">'.$currency['code'].'</option>';
                                }
                                ?>
                            </select></TD>
                        <TD><select name="curto" class="form-select form-select-sm">
                                <?php
                                foreach ($result_currencies as $id => $currency) {
                                    echo '<option value="'.$id.'">'.$currency['code'].'</option>';
                                }
                                ?>
                            </select></TD>
                        <TD><INPUT TYPE="number" NAME="rate" step="0.0001" class="form-control form-control-sm"></TD>
                        <TD><INPUT TYPE="date" NAME="rday" class="form-control form-control-sm"></TD>
                        <TD><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></TD>
                    </TR>
                    </tfoot>
                </table>
                
            <?php } ?>
            
        </div>
    </div>   
    
</FORM>

</div>
