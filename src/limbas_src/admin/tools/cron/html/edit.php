<?php

use Limbas\admin\tools\cron\CronJob;
use Limbas\admin\tools\cron\JobType;
use Limbas\Controllers\UserGroupController;

/** @var CronJob $job */
?>

<div class="modal-header">
    <h1 class="modal-title fs-5"><?= $lang[2079] ?></h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <label for="job-type" class="col-sm-3 col-form-label"><?= $lang[1749] ?></label>
        <div class="col-sm-9">
            <select <?= ($job->type && $job->type !== JobType::UNSET) ? 'disabled' : '' ?> class="form-select" id="job-type" name="job_type">
                <option value="" disabled <?= (!$job->type || $job->type === JobType::UNSET) ? 'selected' : '' ?>></option>
                <?php
                $jobTypes = JobType::cases();
                usort($jobTypes, fn($a, $b) => $a->value <=> $b->value);
                foreach ($jobTypes as $jobType) { ?>
                    <?php
                    if ($jobType === JobType::UNSET) continue;

                    $isSelected = ($job->type === $jobType) ? 'selected' : '';
                    ?>
                    <option value="<?= $jobType->value ?>" <?= $isSelected ?>>
                        <?= $jobType->value ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-sm-3"><?= $lang[1931] ?></div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label for="job-minutes" class="col-form-label pb-1 pt-0"><?= $lang[2074] ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="job-minutes" value="<?= $job->cronMinutes ?>">
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label for="job-hours" class="col-form-label pb-1 pt-0"><?= $lang[2075] ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="job-hours" value="<?= $job->cronHours ?>">
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label for="job-month-days" class="col-form-label pb-1 pt-0"><?= $lang[2076] ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="job-month-days" value="<?= $job->cronMonthdays ?>">
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label for="job-months" class="col-form-label pb-1 pt-0"><?= $lang[1437] ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="job-months" value="<?= $job->cronMonths ?>">
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <label for="job-week-days" class="col-form-label pb-1 pt-0"><?= $lang[2078] ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" id="job-week-days" value="<?= $job->cronWeekdays ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <label for="job-description" class="col-sm-3 col-form-label"><?= $lang[126] ?></label>
        <div class="col-sm-9">
            <input type="text" class="form-control" id="job-description" value="<?= $job->description ?>">
        </div>
    </div>
    <div class="row mb-3">
        <label class="form-check-label col-sm-3 col-form-label" for="job-active"><?= $lang[2072] ?></label>
        <div class="col-sm-9 d-flex align-items-center">
            <input class="form-check-input mt-0" type="checkbox" value="" id="job-active" <?= $job->active ? 'checked' : '' ?>>
        </div>
    </div>
    <div class="row mb-3">
        <label for="job-user-input" class="col-sm-3 col-form-label"><?= $lang[1242] ?></label>
        <div class="col-sm-9">
            <select id="job-user-input" type="text" class="form-select" name="job_user">

            </select>
        </div>
    </div>
    <?php
    switch ($job->type) {
        case JobType::OCR:
        case JobType::INDIZE:
            include COREPATH . 'admin/tools/cron/html/settings/fileselect.php';
            break;
        case JobType::TEMPLATE:
        case JobType::DATASYNC:
        case JobType::STRUCTURE_SYNC:
        case JobType::RSYNC:
            include COREPATH . 'admin/tools/cron/html/settings/templateselect.php';
            break;
        case JobType::BACKUP:
            include COREPATH . 'admin/tools/cron/html/settings/backupconfig.php';
            break;
        default:
            break;
    }
    ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang[2227] ?></button>
    <button type="button" data-id="<?= $job->id ?>" data-url="<?= $route ?>" class="btn btn-primary btn-save-job"><?= $lang[842] ?></button>
</div>
