<?php use Limbas\admin\report\Report; global $gtab, $gprinter; /** @var Report $report */?>
<tr id="report-<?=e($report->id)?>" <?=isset($isNew)?'class="table-success"':''?>>
    <td><?=e($report->id)?></td>
    <td>
        <a href="main_admin.php?action=setup_report_frameset&report_id=<?=e($report->id)?>&referenz_tab=<?=e($report->gtabId)?>&type=report"><i class="lmb-icon lmb-pencil cursor-pointer"></i></a>
    </td>
    <td>
        <i class="fas fa-cog cursor-pointer" data-bs-toggle="modal" data-bs-target="#modal-report-settings" data-id="<?=e($report->id)?>"></i>
    </td>
    <td><i data-delete="<?=$report->id?>" class="lmb-icon lmb-trash cursor-pointer"></i></td>
    <td>
        <input type="text" data-update="name" data-id="<?=e($report->id)?>" value="<?=e($report->name)?>" class="form-control form-control-sm" style="min-width: 200px">
    </td>
    <td><?=e($gtab['desc'][$report->gtabId])?></td>
    <td>
        <div class="d-flex align-items-start mw-100">
            <div class="col-8">
                <select data-update="target" data-id="<?=e($report->id)?>" class="form-select form-select-sm">
                    <option value="0"></option>
                    <?php foreach ($report->dmsFolderList() as $key => $folder): ?>
                        <option value="<?=e($key)?>" <?= $report->dmsFolderId === $key ? 'selected' : ''?>><?=e($folder)?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-4">
                <input type="text" data-update="target" data-id="<?=e($report->id)?>" value="<?=e($report->dmsFolderId)?>" class="form-control form-control-sm">
            </div>
        </div>
    </td>
    <td><input type="text" data-update="saveName" data-id="<?=e($report->id)?>" value="<?=e($report->saveName)?>" class="form-control form-control-sm"></td>
    <td>
        <?=e($report->defFormat)?>
    </td>

    <td>
        <select data-update="printer" data-id="<?=e($report->id)?>" class="form-select form-select-sm">
            <option value='0'></option>
            <?php foreach ($gprinter as $id => $printer): ?>
            <option value="<?=e($id)?>" <?=$greport[$gtabid]["printer"][$key] == $id ? 'selected' : ''?>><?=e($printer['name'])?></option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
