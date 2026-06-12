<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\admin\tools\cron\CronJob;

?>

<script src="assets/vendor/select2/select2.full.min.js"></script>
<link href="assets/vendor/select2/select2.min.css" rel="stylesheet">

<div class="container-fluid p-3">
    
    <div class="card mb-3">
        <div class="card-body">
            <p><?=e($lang[3245])?>:</p>
            <div>
                * * * * * php <?=e(COREPATH)?>cron.php
            </div>
        </div>
    </div>
    
    <table class="table table-sm table-striped table-hover border bg-contrast align-middle">
        <thead>
        <tr>
            <th></th>
            <th><?= $lang[2068] ?></th>
            <th><?= $lang[1749] ?></th>
            <th>Template</th>
            <th><?= $lang[2070] ?></th>
            <th><?= $lang[1242] ?></th>
            <th><?= $lang[126] ?></th>
            <th><?= $lang[2072] ?></th>
            <th>Start</th>
            <th><?= $lang[160] ?></th>
        </tr>
        </thead>

        <tbody id="table-jobs">
        <?php
        /** @var CronJob $jobs */
        foreach ($jobs as $job) :
            include COREPATH . 'admin/tools/cron/html/job-row.php';
        endforeach; ?>
        </tbody>

    </table>

    <button type="button" class="btn btn-primary" data-url="<?= route('admin.jobs.create') ?>" data-bs-toggle="modal" data-bs-target="#modal-jobs" data-id="0"><i class="fas fa-plus"></i></button>
</div>

<div class="modal fade" id="modal-jobs" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<script type="text/javascript" src="assets/js/admin/tools/cron/cron.js?v=<?= $umgvar['version'] ?>"></script>
