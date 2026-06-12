<?php

use Limbas\admin\tools\cron\CronJob;

/** @var CronJob $job */

?>

<div class="col-6">
    <div class="row">
        <div class="col">
    <?php foreach (array_keys($gtab['table']) as $tableId) {
        $field = $gfield[$tableId] ?? [];
        $indizeList = $field['indize'] ?? [];
        if (empty($indizeList) || !in_array("1", $indizeList)) {
            continue;
        }
        $checkedFields = $job->config->fields[$tableId];
        $tableDescription = $gtab['desc'][$tableId] ?? '';
        ?>

        <div class="row mb-2 mx-1 ps-2 pt-0 pb-0 border-1 border">
            <div class="col">
                <div class="row py-1 fw-bold"><?= e($tableDescription) ?></div>
                <?php foreach (array_keys($field['id'] ?? []) as $fieldId) {
                    $fieldIndize = $field['indize'][$fieldId];
                    $fieldDataType = $field['data_type'][$fieldId] ?? null;
                    $fieldDescription = $field['beschreibung'][$fieldId] ?? '';
                    $fieldSpelling = $field['spelling'][$fieldId] ?? '';

                    $checked = in_array($fieldId, $checkedFields ?? []) ? 'checked' : '';

                    if (!empty($fieldIndize) && $fieldDataType == 39) { ?>
                        <div class="row pb-1">
                            <div class="col" title="<?= e($fieldDescription) ?>">
                                <label class="w-100" for="field_<?= $tableId ?>_<?= $fieldId ?>">
                                    <?= e($fieldSpelling) ?>
                                </label>
                            </div>
                            <div class="col-auto">
                                <input
                                        type="checkbox"
                                        data-tableid="<?= $tableId ?>"
                                        id="field_<?= $tableId ?>_<?= $fieldId ?>"
                                        data-fieldid="<?= $fieldId ?>"
                                    <?= $checked ?>
                                >
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    <?php } ?>
        </div>
    </div>
</div>