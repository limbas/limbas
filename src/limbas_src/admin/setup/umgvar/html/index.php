<?php global $LINK, $lang, $LINK_ID, $umgvar, $DBA;
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\setup\umgvar\UmgVar;

?>
<script type="text/javascript" src="assets/js/admin/setup/umgvar.js?v=<?=e($umgvar['version'])?>"></script>

<div class="container-fluid p-3">

        <!-- navbar oben -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active bg-contrast" href="#"><?= $lang[$LINK["desc"][43]] ?></a>
            </li>
            <?php if ($LINK['setup_custvar']): ?>
                <li class="nav-item">
                    <a class="nav-link"
                       href="main_admin.php?action=setup_custvar"><?= $lang[$LINK["desc"][$LINK_ID['setup_custvar']]] ?></a>
                </li>
            <?php endif; ?>
        </ul>

        <!-- Umgvar Liste -->
        <div class="tab-content border border-top-0 bg-contrast">
            <div class="tab-pane active">
                <table class="table table-sm table-striped mb-0" id="table-umgvar">
                    <tbody>
                    <?php

                    /* --- Ergebnisliste --------------------------------------- */
                    foreach ($categories as $category => $umgVarArray):
                        /* postgres fulltextsearch ausschließen falls postgres nicht genutzt wird */
                        if ($category === 2995 /* postgres fulltextsearch */ && $DBA['DB'] !== 'postgres') {
                            continue;
                        }
                        ?>
                        </tbody>

                    <tbody id="table-<?=e($category)?>">

                        <TR class="table-section">
                            <TD colspan="5"><?=e($lang[$category])?></TD>
                        </TR>
                        
                        <?php
                        /** @var UmgVar $umgVar */
                        foreach ($umgVarArray as $umgVar) :
                            require(COREPATH . 'admin/setup/umgvar/html/umgvar-row.php');
                        endforeach;
                    endforeach;
                    ?>
                    </tbody>

                    <tfoot>

                    <!-- admin umgvar hinzufügen -->
                    <?php if ($umgvar['admin_mode']) : ?>
                        <tr>
                            <td>
                                <INPUT id="new-umgvar-name" class="form-control form-control-sm" TYPE="TEXT" SIZE="20">
                            </td>
                            <td>
                                <INPUT id="new-umgvar-description" class="form-control form-control-sm" TYPE="TEXT" SIZE="20">
                            </td>
                            <td>
                                <INPUT id="new-umgvar-value" class="form-control form-control-sm" TYPE="TEXT" SIZE="50">
                            </td>
                            <td>
                                <select id="new-umgvar-category" class="form-select form-select-sm">
                                    <option value=""></option>
                                    <?php foreach ($categories as $value => $_): ?>
                                        <option value="<?=e($value)?>"><?=e($lang[$value])?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <button id="btn-save-umgvar" class="btn btn-sm btn-primary" type="button"><?= $lang[540] ?></button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end"><?php echo $lang[1749] .':'?></td>
                            <td><INPUT id="new-umgvar-new-category" class="form-control form-control-sm" TYPE="TEXT"></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>

                    </tfoot>
                </table>
            </div>
        </div>
        
</div>

