<?php

use Limbas\admin\tools\cron\CronJob;

/** @var CronJob $job */
?>
<div class="row mb-3">
    <label for="job-template" class="col-sm-3 col-form-label"><?= $lang[2207] ?> (*.job)</label>
    <div class="col-sm-9">
        <select id="job-template" class="form-select">
            <option value=""></option>
            <?php
            $template = $job->config->getTemplate();
            foreach ($job->config->getAvailableTemplates() as $value => $label) { ?>
                <option <?= $template == $label ? 'selected' : '' # todo this may be false, check this again, maybe replace with value check ?> value="<?= $value ?>"><?= $label ?></option>
            <?php } ?>
        </select>
    </div>
</div>
