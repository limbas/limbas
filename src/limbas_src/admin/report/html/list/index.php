<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
use Limbas\admin\report\Report;

?>

<div class="container-fluid p-3">


        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 border bg-contrast" id="table-reports">

                <thead>
                <tr>
                    <th>ID</th>
                    <th colspan="2"></th>
                    <th><?=e($lang[160])?></th>
                    <th><?=e($lang[1137])?></th>
                    <th><?=e($lang[1162])?></th>
                    <th><?=e($lang[2111])?></th>
                    <th><?=e($lang[2511])?></th>
                    <th>Report Class</th>
                    <th>PDF Standard</th>
                    <th><?=e($lang[2935])?></th>
                </tr>
                </thead>
                <tbody>

                <?php
                #----------------- Berichte -------------------

                
                $currentGtabId = null;

                /** @var Report $report */
                foreach ($reports as $report):

                if($currentGtabId !== $report->gtabId):
                $currentGtabId = $report->gtabId;
                if($gtab['table'][$currentGtabId]){
                    $cat = $gtab['desc'][$currentGtabId];
                }else{
                    $cat = $lang[1986];
                }

                ?>
                </tbody>
                <tbody id="table-<?=e($currentGtabId)?>">
                <tr class="table-section"><td colspan="12"><?=e($cat)?></td></tr>

                <?php

                endif;

                require(COREPATH . 'admin/report/html/list/report-row.php');

                endforeach;

                ?>

                </tbody>

                <tfoot>


                <tr>
                    <th colspan="4"></th>
                    <th><?=e($lang[4])?></th>
                    <th><?=e($lang[164])?></th>
                    <th><?=e($lang[1163])?></th>
                    <th><?=e($lang[1464])?></th>
                    <th colspan="5"></th>
                </tr>

                <tr>
                    <td colspan="4"></td>
                    <td><input type="text" id="new-report-name" class="form-control form-control-sm"></td>
                    <td>
                        <select id="new-report-table" class="form-select form-select-sm">
                            <OPTION VALUE="-1"></OPTION>
                            <?php foreach ($tabgroup['id'] as $key0 => $value0): ?>
                                <optgroup label="<?=e($tabgroup['name'][$key0])?>">
                                    <?php foreach ($gtab['tab_id'] as $key => $value):
                                        if($gtab['tab_group'][$key] == $value0): ?>
                                            <option value="<?=e($value)?>"><?=e($gtab["desc"][$key])?></option>
                                        <?php endif; endforeach; ?>
                                </optgroup>;
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td>
                        <select id="new-report-format" class="form-select form-select-sm">
                            <option value="mpdf">mpdf</option>
                            <option value="tcpdf">tcpdf</option>
                        </select>
                    </td>
                    <td>
                        <select id="new-report-copy" class="form-select form-select-sm">
                            <option value="0"></option>
                            <?php if($reports):
                                foreach ($reports as $report): ?>
                                    <option value="<?=e($report->id)?>"><?=e($report->name)?></option>
                                <?php   endforeach;
                            endif;
                            ?>
                        </select>
                    </td>
                    <td><button type="button" class="btn btn-primary btn-sm" id="btn-save-report"><?=e($lang[1165])?></button></td>
                    <td></td>
                </tr>
                </tfoot>

            </table>
        </div>

</div>


<div class="modal fade" id="modal-report-settings" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">Template-Berichte</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="report-settings-loader" class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                </div>
                <table id="report-settings-holder" class="table table-striped table-hover d-none">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th><?=e($lang[4])?></th>
                            <th>Template</th>
                        </tr>
                    </thead>
                    <tbody id="table-report-templates">
                        
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="4"><i class="fas fa-plus cursor-pointer" id="btn-add-report-template"></i></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
            </div>
        </div>
    </div>
</div>

<script id="report-template-new" type="text/x-custom-template">
    <tr id="template-<?=e($report->id)?>">
        <td>0</td>
        <td><i data-delete-template="0" class="lmb-icon lmb-trash cursor-pointer"></i></td>
        <td>
            <input type="text" name="name" class="form-control form-control-sm">
        </td>
        <td>
            <input type="text" name="template" class="form-control form-control-sm">
        </td>
    </tr>
</script>

<script type="text/javascript" src="assets/js/admin/report/index.js?v=<?=e($umgvar['version'])?>"></script>
