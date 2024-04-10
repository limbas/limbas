<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
if (!defined('LIMBAS_INSTALL')) { return; } ?>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="2">
            <?= lang('Pre-Installation summary') ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="w-50"><?= lang('Installation Path') ?></td>
        <td><?= COREPATH ?></td>
    </tr>
    <?php if(!$skipDatabase): ?>
    <tr>
        <td><?= lang('Database Vendor') ?></td>
        <td><?= $_POST['db_vendor'] ?></td>
    </tr>
    <tr>
        <td><?= lang('Database Name') ?></td>
        <td><?= $_POST['setup_database'] ?></td>
    </tr>
    <tr>
        <td><?= lang('Database User') ?></td>
        <td><?= $_POST['setup_dbuser'] ?></td>
    </tr>
    <tr>
        <td><?= lang('Database Host') ?></td>
        <td><?= $_POST['setup_host'] ?></td>
    </tr>
    <tr>
        <td><?= lang('Database Schema') ?></td>
        <td><?= $_POST['setup_dbschema'] ?></td>
    </tr>
    <?php if ($_POST['setup_dbport']) {
        echo '<tr><td>' . lang('Database Port') . '</td><td>' . $_POST['setup_dbport'] . '</td></tr>';
    } ?>
    <?php endif; ?>
    <tr>
        <td><?= lang('Installation package') ?></td>
        <td><?= $_POST['backupdir'] ?></td>
    </tr>
    </tbody>
</table>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <tbody>
    <tr>
        <td class="w-50"><?= lang('Language') ?></td>
        <td><?= $_POST['setup_language'] == '1' ? 'deutsch' : 'english' ?></td>
    </tr>
    <tr>
        <td><?= lang('Dateformat') ?></td>
        <td><?= $_POST['setup_dateformat'] == '1' ? 'deutsch (dd-mm-yyyy)' : ( $_POST['setup_dateformat'] == '2' ? 'english (yyyy-mm-dd)' : ($_POST['setup_dateformat'] == '3' ? 'us (mm-dd-yy)' : '') ) ?></td>
    </tr>
    <tr>
        <td><?= lang('Charset') ?></td>
        <td><?= $_POST['setup_charset'] == 'UTF-8' ? 'UTF-8' : 'ISO-8859-1' ?></td>
    </tr>
    <tr>
        <td><?= lang('Color Scheme') ?></td>
        <td><?= $_POST['setup_color_scheme'] == '2' ? 'Default' : 'Dark' ?></td>
    </tr>
    <tr>
        <td><?= lang('Company') ?>:</td>
        <td><?= $_POST['setup_company'] ?></td>
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
                <button type="submit" class="btn btn-success" name="install"
                        value="<?= $stepKeys[$currentStep + 1] ?>"> <?= lang('Install Limbas now!') ?> </button>
            </div>

        </div>
    </div>
</div>
