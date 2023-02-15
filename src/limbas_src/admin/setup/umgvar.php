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
            <input type="hidden" name="action" value="setup_umgvar">
            <input type="hidden" name="changecat">

            <!-- navbar oben -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active bg-white" href="#"><?= $lang[$LINK["desc"][43]] ?></a>
                </li>
                <?php if ($LINK['setup_custvar']): ?>
                    <li class="nav-item">
                        <a class="nav-link"
                           href="main_admin.php?action=setup_custvar"><?= $lang[$LINK["desc"][$LINK_ID['setup_custvar']]] ?></a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Umgvar Liste -->
            <div class="tab-content border border-top-0 bg-white">
                <div class="tab-pane active">
                    <table class="table table-sm table-striped mb-0">
                        <?php
                        $syscat = array(1893, 1894, 1895, 1896, 1898, 2700, 1899, 1900, 1995, 2818, 2819, 2820, 2935, 2995);

                        /* --- Ergebnisliste --------------------------------------- */
                        foreach ($result_category as $value0) :
                            /* postgres fulltextsearch ausschließen falls postgres nicht genutzt wird */
                            if ($value0 === 2995 /* postgres fulltextsearch */ && $DBA['DB'] !== 'postgres') {
                                continue;
                            }
                            ?>

                            <TR class="table-section">
                                <TD colspan="5"><?= $lang[$value0] ?></TD>
                            </TR>
                            <?php
                            foreach ($result_umgvar['id'] as $key1 => $value1) :
                                if ($result_umgvar['category'][$key1] == $value0) : ?>


                                    <tr>
                                        <td><?= $result_umgvar['form_name'][$key1] ?></td>
                                        <td><?= $result_umgvar['beschreibung'][$key1] ?></td>
                                        <td>
                                            <?php
                                            switch ($result_umgvar['field_type'][$key1]):
                                                case 'bool': ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input"
                                                                   type="checkbox"
                                                                   value="<?= $result_umgvar['norm'][$key1] == 1 ? 0 : 1 ?>"
                                                                   name="umg_<?= $result_umgvar['id'][$key1] ?>"
                                                                   onchange="document.form1.changecat.value=document.form1.changecat.value+',<?= $result_umgvar["id"][$key1] ?>'"
                                                                   <?= $result_umgvar['norm'][$key1] == 1 ? 'checked' : '' ?>
                                                            >
                                                        </div>
                                                    <?php
                                                    break;
                                                case 'select':
                                                    $values = json_decode($result_umgvar['field_options'][$key1]);
                                                    if(is_array($values) || is_object($values)):
                                                        ?>
                                                            <select class="form-select form-select-sm"
                                                                    type="text"
                                                                    name="umg_<?= $result_umgvar['id'][$key1] ?>"
                                                                    onchange="document.form1.changecat.value=document.form1.changecat.value+',<?= $result_umgvar["id"][$key1] ?>'">
                                                                
                                                                <?php foreach ($values as $optionName => $optionValue): ?>
                                                                    <option value="<?= htmlentities($optionValue) ?>" <?= $optionValue == $result_umgvar['norm'][$key1] ? 'selected' : ''?>>
                                                                        <?= is_object($values) ? $optionName : $optionValue ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                                
                                                                
                                                            </select>
                                                        <?php
                                                        break;
                                                    endif;
                                                default: ?>
                                                    
                                                        <input class="form-control form-control-sm" TYPE="TEXT" SIZE="50"
                                                               onchange="document.form1.changecat.value=document.form1.changecat.value+',<?= $result_umgvar["id"][$key1] ?>'"
                                                               value="<?= htmlentities($result_umgvar['norm'][$key1]) ?>"
                                                               name="umg_<?= $result_umgvar['id'][$key1] ?>">
    
                                                <?php
                                            endswitch; ?>
                                        </td>
                                        <?php if ($umgvar["admin_mode"]): ?>

                                            <td>
                                                <select class="form-select form-select-sm"
                                                        name="cat[<?= $result_umgvar["id"][$key1] ?>]"
                                                        OnChange="document.form1.changecat.value=document.form1.changecat.value+',<?= $result_umgvar["id"][$key1] ?>'">
                                                    <?php
                                                    foreach ($result_category as $value) {
                                                        echo "<option value=\"$value\" " . (($result_umgvar["category"][$key1] == $value) ? 'selected' : '') . ">" . $lang[$value] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                            <td class="text-center"><A
                                                        HREF="main_admin.php?action=setup_umgvar&del=1&id=<?= urlencode($result_umgvar["id"][$key1]) ?>"><i
                                                            class="lmb-icon lmb-trash"></i></A></td>

                                        <?php endif; ?>
                                    </tr>


                                <?php endif;
                            endforeach;
                        endforeach;
                        ?>

                        <!-- ändern button -->
                        <tfoot>
                        <tr class="border-bottom border-top">
                            <td colspan="6">
                                <button class="btn btn-sm btn-primary" type="submit" name="change"
                                        value="1"><?= $lang[522] ?></button>
                            </td>
                        </tr>

                        <!-- admin umgvar hinzufügen -->
                        <?php if ($umgvar["admin_mode"]) : ?>
                            <tr>
                                <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="20" NAME="name"></td>
                                <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="20"
                                           NAME="description"></td>
                                <td><INPUT class="form-control form-control-sm" TYPE="TEXT" SIZE="50" NAME="norm"></td>
                                <td>
                                    <select name="category" class="form-select form-select-sm">
                                        <option value=""></option>
                                        <?php
                                        foreach ($syscat as $value) {
                                            echo "<option value=\"$value\">" . $lang[$value] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="submit" name="add"
                                            value="1"><?= $lang[540] ?></button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td><INPUT class="form-control form-control-sm" TYPE="TEXT" NAME="newcategory"></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>

                        </tfoot>
                    </table>
                </div>
            </div>
        </FORM>
    </div>
