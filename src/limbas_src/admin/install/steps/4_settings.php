<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- umgvar settings --- 
 * 
 * fills
 *      setup_language
 *      setup_dateformat
 *      setup_charset
 *      setup_color_scheme
 *      setup_company
 *  
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}

?>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="2">
            <?= lang('Settings') ?>
            <a href="http://limbas.org/wiki/Umgvar" target="new"><i class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= lang('Language') ?>:</td>
        <td><select class="form-select" name="setup_language">
                <option value="1" <?= $_POST['setup_language'] == '1' ? 'selected' : '' ?>>deutsch</option>
                <option value="2" <?= $_POST['setup_language'] == '2' ? 'selected' : '' ?>>english</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= lang('Dateformat') ?>:</td>
        <td><select class="form-select" name="setup_dateformat">
                <option value="1" <?= $_POST['setup_dateformat'] == '1' ? 'selected' : '' ?>>deutsch (dd-mm-yyyy)
                </option>
                <option value="2" <?= $_POST['setup_dateformat'] == '2' ? 'selected' : '' ?>>english (yyyy-mm-dd)
                </option>
                <option value="3" <?= $_POST['setup_dateformat'] == '3' ? 'selected' : '' ?>>us (mm-dd-yy)</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= lang('Charset') ?>:</td>
        <td><select class="form-select" name="setup_charset">
                <option value="UTF-8" <?= $_POST['setup_charset'] == 'UTF-8' ? 'selected' : '' ?>>
                    UTF-8
                </option>
                <option value="ISO-8859-1" <?= $_POST['setup_charset'] == 'ISO-8859-1' ? 'selected' : '' ?>>
                    LATIN1
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= lang('Color Scheme') ?>:</td>
        <td><select class="form-select" name="setup_color_scheme">
                <option value="1" <?= $_POST['setup_color_scheme'] == '1' ? 'selected' : '' ?>>Default</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= lang('Company') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off"
                   value="<?= isset($_POST['setup_company']) ? ($_POST['setup_company'] ?: 'your company') : 'your company' ?>" name="setup_company" size="50"></td>
    </tr>
    </tbody>
</table>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-outline-dark"
                        onclick="switchToStep('<?= $stepKeys[$currentStep - 1] ?>')"><?= lang('Back') ?></button>
            </div>
            <div class="col-6 text-end">
                <button type="submit" class="btn btn-primary" name="install"
                        value="<?= $stepKeys[$currentStep + 1] ?>"><?= lang('Next step') ?></button>
            </div>

        </div>
    </div>
</div>
