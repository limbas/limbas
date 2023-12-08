<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

require_once __DIR__ . '/preview/preview.dao';

?>


<script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/report/preview.js?v=<?=$umgvar['version']?>"></script>
<script>
    /**
     * The current generated file path. Will be updated once preview is requested again
     * @type {string}
     */
    preview_path = '<?= $path ?>';
</script>
<form id="report_form" method="POST" class="h-100">
    <input type="hidden" name="action" value="report_preview">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="report_id" value="<?= $report_id ?>">
    <input type="hidden" name="ID" value="<?= $ID ?>">
    <input type="hidden" name="use_record" value="<?= $use_record ?>">
    <input type="hidden" name="resolvedTemplateGroups" value="<?= htmlentities(json_encode($resolvedTemplateGroups ?? [])) ?>">
    <input type="hidden" name="resolvedDynamicData" value="<?= htmlentities(json_encode($resolvedDynamicData ?? [])) ?>">
    <input type="hidden" name="preview" value="<?= $preview ?>">
    <input type="hidden" name="report_output" id="reportOutput" value="0">

       
    
    <div class="container-fluid h-100">
        <?php if ($dynamicDataPlaceholdersExist): ?>
            <?php require __DIR__ . '/preview/data_preview.php'; ?>
        <?php else: ?>
            <?php require __DIR__ . '/preview/pdf_preview.php'; ?>
        <?php endif; ?>
    </div>
    
    
</form>
