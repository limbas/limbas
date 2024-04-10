<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\report\preview\ReportPreview;

$reportPreview = new ReportPreview();

[
    $LINK,
    $gprinter,
    $greportlist,
    $umgvar,

    $gtabid,
    $report_id,
    $ID,
    $use_record,
    $report_name,

    $resolvedTemplateGroups,
    $resolvedDynamicData,
    $reportTemplateElements,
    $dynamicDataPlaceholdersExist,
    $unresolvedDynamicDataPlaceholdersExist
] = $reportPreview->getRequestParameter();


?>


<script src="assets/js/gtab/html/gtab.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/printer/printer.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/extra/report/preview.js?v=<?=$umgvar['version']?>"></script>
<style>
    <?= $reportPreview->getCss($gtabid, $report_id); ?>
</style>

<form id="report_form" method="POST" class="h-100" data-has-data="<?= $dynamicDataPlaceholdersExist ? '1' : '0'?>">
    <input type="hidden" name="action" value="report_preview">
    <input type="hidden" name="gtabid" value="<?= $gtabid ?>">
    <input type="hidden" name="report_id" value="<?= $report_id ?>">
    <input type="hidden" name="ID" value="<?= $ID ?>">
    <input type="hidden" name="use_record" value="<?= $use_record ?>">
    <input type="hidden" name="resolvedTemplateGroups" value="<?= htmlentities(json_encode($resolvedTemplateGroups ?? [])) ?>">
    <input type="hidden" name="resolvedDynamicData" value="<?= htmlentities(json_encode($resolvedDynamicData ?? [])) ?>">
    
    <div class="container-fluid h-100">
        <?php if ($dynamicDataPlaceholdersExist): ?>
            <?php require __DIR__ . '/data_preview.php'; ?>
        <?php else: ?>
            <?php require __DIR__ . '/pdf_preview.php'; ?>
        <?php endif; ?>
    </div>
    
</form>
