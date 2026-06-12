<?php

use Limbas\admin\tools\cron\CronJob;
use Limbas\admin\tools\cron\JobType;

/** @var CronJob $job */

$recursive_file_display = function ($currentLevel = 0) use ($job, &$recursive_file_display): void {
    global $file_struct;

    $imgs = [
        'plus' => 'assets/images/legacy/outliner/plusonly.gif',
        'blank' => 'assets/images/legacy/outliner/blank.gif',
    ];

    if ($currentLevel > 0) { ?>
        <div class="folding-list ms-2" style="display:none; ">
    <?php }

    foreach ($file_struct['id'] as $index => $fileId) {
        $fileLevel = $file_struct['level'][$index];
        if ($fileLevel == $currentLevel) {
            $readonly = !empty($file_struct['readonly'][$index]);
            $fileName = $file_struct['name'][$index];

            $has_children = in_array($fileId, $file_struct['level']);
            $img_src = $has_children ? $imgs['plus'] : $imgs['blank'];
            $checked = in_array($fileId, $job->config->files ?? []) ? 'checked' : '';
            ?>

            <div id="f_<?= $fileId ?>" class="file-row d-flex w-100">
                <div class="d-flex align-items-center">
                    <img class="<?= $has_children ? "file-popup cursor-pointer" : "" ?>"
                         src="<?= $img_src ?>"
                         alt="">
                </div>
                <div class="d-flex align-items-center">
                    <i class="lmb-icon lmb-folder-closed"></i>
                </div>
                <div class="flex-grow-1 ps-1">
                    <label class="w-100" for="ifile_<?= $fileId ?>">
                        <?= e($fileName) ?>
                    </label>
                </div>
                <div>
                    <?php if (!$readonly) { ?>
                        <input type="checkbox" <?= $checked ?> data-fileid="<?= $fileId ?>" id="ifile_<?= $fileId ?>"">
                    <?php } ?>
                </div>
            </div>

            <?php if ($has_children) {
                $recursive_file_display($fileId);
            } else { ?>
                <div class="folding-list-empty d-none"></div>
            <?php } ?>
        <?php }
    }

    if ($currentLevel > 0) { ?>
        </div>
    <?php }
};

$includeSubdirsChecked = $job->config->includeSubdirs ? 'checked' : '';

?>

<div class="row mb-3">
    <div class="col-6">
        <div class="row border border-1 mx-1">
            <div class="col">
                <div class="row px-1 py-2 border-bottom border-1">
                    <div class="col fw-bold">
                        <?= $lang[2080] ?>
                    </div>
                    <div class="col-auto">
                        <label for="include-subdirs"><?= $lang[2081] ?></label>
                        <input id="include-subdirs" type="checkbox" <?= $includeSubdirsChecked ?>>
                    </div>
                </div>
                <div class="row px-1 py-2">
                    <div class="col">
                        <?php $recursive_file_display() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($job->type == JobType::INDIZE) {
        include COREPATH . 'admin/tools/cron/html/settings/fieldselect.php';
    } ?>
</div>