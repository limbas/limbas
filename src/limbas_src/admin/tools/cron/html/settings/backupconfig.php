<?php

use Limbas\admin\tools\cron\configs\Backup as BackupConfig;
use Limbas\admin\tools\cron\CronJob;

/** @var CronJob $job */

if ($availableBackupTypes = BackupConfig::getAvailableBackupTypes()) { ?>
    <div class="row mb-3">
        <label for="job-backuptype" class="col-sm-3 col-form-label">Type</label>
        <div class="col-sm-9">
            <select id="job-backuptype" name="backup_type" class="form-select">
                <?php foreach ($availableBackupTypes as $value => $label) { ?>
                    <option value="<?= $value ?>" <?= ($job->config->backupType == $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php }

if ($availableBackupMediums = BackupConfig::getAvailableBackupMediums()) { ?>
    <div class="row mb-3">
        <label for="job-backupmedium" class="col-sm-3 col-form-label">Medium</label>
        <div class="col-sm-9">
            <select id="job-backupmedium" name="backup_medium" class="form-select">
                <?php foreach ($availableBackupMediums as $value => $label) { ?>
                    <option value="<?= $value ?>" <?= ($job->config->backupMedium == $value) ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
<?php } ?>

<div class="row mb-3">
    <label for="job-backuptarget" class="col-sm-3 col-form-label">Target</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="job-backuptarget" value="<?= $job->config->backupTarget ?>">
    </div>
</div>

<div class="row mb-3">
    <label for="job-backupalive" class="col-sm-3 col-form-label">Alive</label>
    <div class="col-sm-9">
        <input type="number" class="form-control" id="job-backupalive" value="<?= $job->config->backupAlive ?>">
    </div>
</div>