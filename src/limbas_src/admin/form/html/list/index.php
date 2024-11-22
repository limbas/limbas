<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
use Limbas\admin\form\Form;
use Limbas\admin\form\FormType;

?>


<div class="container-fluid p-3">

        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 border bg-contrast" id="table-forms">

                <thead>
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th><?=e($lang[160])?></th>
                    <th><?=e($lang[1179])?></th>
                    <th><?=e($lang[925])?></th>
                    <th><?=e($lang[1162])?></th>
                    <th><?=e($lang[1986])?></th>
                    <th><?=e($lang[2555])?></th>
                    <th><?=e($lang[1638])?></th>
                </tr>
                </thead>
                <tbody>

                <?php
                #----------------- Berichte -------------------

                
                $currentGtabId = null;

                /** @var Form $form */
                foreach ($forms as $form):

                if($currentGtabId !== $form->gtabId):
                $currentGtabId = $form->gtabId;
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

                require(COREPATH . 'admin/form/html/list/form-row.php');

                endforeach;

                ?>

                </tbody>

                

            </table>
        </div>

    
    <div>
        <table class="table table-sm table-striped mb-0 border bg-contrast">
            <thead>
            <tr>
                <TD><?=$lang[4]?></TD>
                <TD><?=$lang[164]?></TD>
                <TD><?=$lang[1464]?></TD>
                <TD><?=$lang[925]?></TD>
                <TD><?=$lang[1986]?></TD>
                <TD></TD>
            </tr>
            </thead>

            <tr>
                <td><input type="text" id="new-form-name" name="form_name" size="20" class="form-control form-control-sm"></td>
                <td>
                    <select id="new-form-table" class="form-select form-select-sm">
                        <option></option>
                        <?php foreach ($tabgroup['id'] as $key0 => $value0): ?>

                            <optgroup label="<?=e($tabgroup['name'][$key0])?>">
                                <?php foreach ($gtab['tab_id'] as $key => $value):
                                    if($gtab['tab_group'][$key] == $value0): ?>
                                        <option value="<?=e($value)?>"><?=e($gtab["desc"][$key])?></option>
                                <?php 
                                endif;
                                endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </td>

                <td>

                    <select id="new-form-copy" class="form-select form-select-sm">
                        <option value="0"></option>
                        <?php if($forms):
                            foreach ($forms as $form): ?>
                                <option value="<?=e($form->id)?>"><?=e($form->name)?></option>
                            <?php   endforeach;
                        endif;
                        ?>
                    </select>
                    
                </td>


                <td>
                    <select id="new-form-type" class="form-select form-select-sm">
                        <?php foreach (FormType::cases() as $formType): ?>
                            <option value="<?=$formType->value?>"><?=$formType->name()?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>

                    <select id="new-form-extension" class="form-select form-select-sm">
                        <option></option>
                        
                        <?php
                        $extfiles = read_dir(EXTENSIONSPATH,1);
                        foreach ($extfiles['name'] as $key1 => $filename):
                            if($extfiles['typ'][$key1] == 'file' && $extfiles['ext'][$key1] == 'ext'):
                                $path = lmb_substr($extfiles["path"][$key1],lmb_strlen(EXTENSIONSPATH),100);
                        ?>
                                
                                <option value="<?=e($path.$filename)?>"><?=e($path.$filename)?></option>
                        <?php endif; endforeach; ?>
                    </select>
                </td>
                
                <td><button type="button" class="btn btn-primary btn-sm" id="btn-save-form"><?=e($lang[1186])?></button></td>
            </tr>

        </table>
    </div>
</div>



<script type="text/javascript" src="assets/js/admin/form/index.js?v=<?=e($umgvar['version'])?>"></script>
