<?php
use Limbas\admin\tools\cron\CronJob;
use Limbas\Controllers\Admin\JobController;

/** @var CronJob $job */
?>
<tr id="job-<?= e($job->id) ?>">
    <td><i class="fas fa-pencil cursor-pointer link-primary" data-url="<?= route('admin.jobs.edit', ['id' => $job->id]) ?>" data-bs-toggle="modal" data-bs-target="#modal-jobs" data-id="<?= e($job->id) ?>"></i></td>
    <td><?= e($job->id) ?></td>
    <td><?= e($job->type->value) ?></td>
    <td><?= e($job->config->getTemplate()) ?></td>
    <td><?= e($job->expression()) ?></td>
    <td><?= e($job->userName) ?></td>
    <td><?= e($job->description) ?></td>
    <td>
        <?php if ($job->active): ?>
            <i class="fa-solid fa-circle-check text-success" title="active"></i>
        <?php else: ?>
            <i class="fa-solid fa-circle-xmark text-danger" title="inactive"></i>
        <?php endif; ?>
    </td>
    <td>
        <i
                class="lmb-icon lmb-action cursor-pointer"
                data-url="<?= route('admin.jobs.run', ['id' => $job->id]) ?>"
                data-run="<?= e($job->id) ?>"
        >
        </i>
    </td>
    <td>
        <i
                class="fas fa-times cursor-pointer link-danger"
                data-url="<?= route('admin.jobs.delete', ['id' => $job->id]) ?>"
                data-delete="<?= e($job->id) ?>">
        </i>
    </td>
</tr>
