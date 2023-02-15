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
        <input type="hidden" name="action" value="setup_custvar">
        <input type="hidden" name="changevar">

        <ul class="nav nav-tabs">
            <?php if ($LINK[43]): ?>
                <li class="nav-item">
                    <a class="nav-link" href="main_admin.php?action=setup_umgvar"><?=$lang[$LINK["desc"][43]]?></a>
                </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link active bg-white" href="#"><?=$lang[$LINK["desc"][$LINK_ID['setup_custvar']]]?></a>
            </li>
        </ul>
        <div class="tab-content border border-top-0 bg-white">
            <div class="tab-pane active">

                <table class="table table-sm table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="border-top-0"><?php echo $lang[926];?></th>
                        <th class="border-top-0"><?php echo $lang[29];?></th>
                        <th class="border-top-0"><?php echo $lang[126];?></th>
                        <th class="border-top-0"><?php echo $lang[2957];?></th>
                        <th class="border-top-0"><?php echo $lang[632];?></th>
                        <th class="border-top-0"></th>
                    </tr>
                    </thead>

                    <?php

                    /* --- Ergebnisliste --------------------------------------- */
                    foreach ($result_custvar["id"] as $key1 => $value1) :
                        #$rowcol = lmb_getRowColor();
                        ?>


                        <tr>
                            <td><?= $result_custvar["key"][$key1] ?>&nbsp;</td>
                            <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="50" OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'" VALUE="<?= str_replace("\"", "&quot;", str_replace("<", "&lt;", $result_custvar['value'][$key1])) ?>" NAME="cv_val_<?= $result_custvar['id'][$key1] ?>">
                            </td>
                            <td><?= $result_custvar["description"][$key1] ?></td>
                            <td><INPUT TYPE="checkbox" NAME="cv_ov_<?= $result_custvar['id'][$key1] ?>" value="1" <?php echo ($result_custvar["overridable"][$key1]) ? 'checked' : ''; ?> OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'"><?php echo ($result_custvar["overridden"][$key1]) ? ' <i class="lmb-icon lmb-long-arrow-down" title="'.$lang[2960].'"></i>' : ''; ?>
                            </td>
                            <td><INPUT TYPE="checkbox" NAME="cv_ac_<?= $result_custvar['id'][$key1] ?>" value="1" <?php echo ($result_custvar["active"][$key1]) ? 'checked' : ''; ?> OnChange="document.form1.changevar.value=document.form1.changevar.value+',<?= $result_custvar["id"][$key1] ?>'">
                            </td>
                            <td><A HREF="main_admin.php?action=setup_custvar&del=1&id=<?= urlencode($result_custvar["id"][$key1]) ?>"><i class="lmb-icon lmb-trash"></i></A></td>
                        </tr>

                    <?php endforeach; ?>

                    <tfoot>
                    <tr class="border-bottom border-top">
                        <td colspan="6">
                            <button class="btn btn-sm btn-primary" type="submit" name="change" value="1"><?= $lang[522] ?></button>
                        </td>
                    </tr>

                    <tr>
                        <th><?php echo $lang[926];?></th>
                        <th><?php echo $lang[29];?></th>
                        <th><?php echo $lang[126];?></th>
                        <th><?php echo $lang[2957];?></th>
                        <th><?php echo $lang[632];?></th>
                        <th></th>
                    </tr>

                    <tr>
                        <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="name"></td>
                        <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="30" NAME="value"></td>
                        <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="70" NAME="description"></td>
                        <td><INPUT TYPE="checkbox" NAME="overridable" value="1"></td>
                        <td><INPUT TYPE="checkbox" NAME="active" value="1"></td>
                        <td><button class="btn btn-sm btn-primary" type="submit" name="add" value="1"><?= $lang[540] ?></button></td>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>



    </FORM>
</div>
