<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
if (!defined('LIMBAS_INSTALL')) { return; } ?>

<div class="alert alert-warning small" role="alert">
    LIMBAS. Copyright &copy; 1998-<?= date('Y') ?> LIMBAS GmbH (info@limbas.com). LIMBAS is free
    software; You can redistribute it and/or modify it under the terms of the GPL General Public License
    V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a>
    for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are
    copyright of their respective owners, and are released under different licences.
</div>

<div class="card">
    <div class="card-body">


        <div class="row">
            <div class="col-md-9">
                <h1 class="card-title fw-bold text-muted"><?= lang('Welcome to') ?></h1>
            </div>
            <div class="col-md-3">
                <select id="lang-select" class="form-select">
                    <option value="en" <?= (LANG == 'en' ? 'selected' : '') ?>>English</option>
                    <option value="de" <?= (LANG == 'de' ? 'selected' : '') ?>>Deutsch</option>
                    <option value="fr" <?= (LANG == 'fr' ? 'selected' : '') ?>>Français</option>
                </select>
            </div>
        </div>

        <div class="text-center mb-5">
            <img src="../assets/images/limbas-logo-text.png" class="w-75">
            <h3 class="fw-bold text-muted">V. <?= $revision ?></h3>
        </div>

        <?php if ($deleteConfigError): ?>
            <div class="alert alert-danger">
                <p><?= lang('The configuration file could not be deleted.') ?></p>
                <p class="mb-0"><?= lang('You have to delete it manually.') ?></p>
            </div>
        <?php endif; ?>

        <?php if ($configExists): ?>
            <div class="row">
                <div class="col-6">
                    <button type="submit" class="btn btn-warning" name="install"
                            value="restart"><?= lang('Remove existing config and start') ?></button>
                </div>
                <div class="col-6 text-end">
                    <button type="submit" class="btn btn-primary" name="install"
                            value="<?= $stepKeys[$currentStep + 1] ?>"><?= lang('Start') ?></button>
                </div>
            </div>
        <?php else: ?>
            <button type="submit" class="btn btn-primary btn-block" name="install"
                    value="<?= $stepKeys[$currentStep + 1] ?>"><?= lang('Start') ?></button>
        <?php endif; ?>


    </div>
</div>
