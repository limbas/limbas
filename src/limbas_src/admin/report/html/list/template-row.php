<?php use Limbas\admin\report\Report; global $gtab; /** @var Report $report */?>
<tr id="report-<?=e($report->id)?>">
    <td><?=e($report->id)?></td>
    <td><i data-delete="<?=$report->id?>" class="lmb-icon lmb-trash cursor-pointer"></i></td>
    <td>
        <input type="text" data-update="name" data-id="<?=e($report->id)?>" value="<?=e($report->name)?>" class="form-control form-control-sm">
    </td>
    <td><input type="text" data-update="template" data-id="<?=e($report->id)?>" value="<?=e($report->savedTemplate)?>" class="form-control form-control-sm"></td>
</tr>
