<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>

<script>
    $(function() {
        $("[data-colorselect]").spectrum({
            preferredFormat: "hex",
            type: "color",
            showInput: true,
            allowEmpty:true,
            change: function(color) {
                let $input = $('#'+$(this).data('colorselect'));
                $input.val(color.toHexString());
            }
        });
        
        $('[data-editcolor]').on('click',function () {
            $('.color-card').addClass('d-none');
            $('#edit-colors-'+$(this).data('editcolor')).removeClass('d-none');
        });
        
        $('.btn-regen').on('click',function () {
            $('#color-schemas').addClass('text-center').html('<i class="lmb-icon lmb-refresh lmb-rotating"></i>');
        });
        
        $('[name="theme"]').on('change',function () {
           if($(this).val() === 'custom') {
               $('[name="custom_theme"]').removeClass('d-none');
           } else {
               $('[name="custom_theme"]').addClass('d-none');
           }
        });
        
    });
    
</script>
<div class="p-3">
    <div class="row">
        <div class="col-md-5">
            <?php if ($scerrormsg) : ?>
                <div class="card border-danger text-danger mb-3">
                    <div class="card-body">
                        <?=$scerrormsg?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card" id="color-schemas">
                <form action="main_admin.php" method=post name="form1">
                    <input type="hidden" name="action" value="setup_color_schema">
                    <input type="hidden" name="add" value="1">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Schema</th>
                                <th class="ps-3">Aktion</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($color_schemas as $id => $colorSchema) : ?>
                            <tr>
                                <td class="align-middle"><?=$id?></td>
                                <td class="align-middle"><?=e($colorSchema['name']).' ('.e($colorSchema['layout']).')'?></td>
                                <td class="align-middle"><?=e($colorSchema['theme'] ?: 'light') ?></td>
                                <td class="align-middle text-nowrap ps-3"><button type="button" class="btn btn-outline-secondary btn-sm me-1" data-editcolor="<?=$id?>"><i class="lmb-icon lmb-pencil"></i></button><?php if($colorSchema['regenerate']) : ?><a href="main_admin.php?action=setup_color_schema&regenerate=1&id=<?=$id?>" class="btn btn-outline-secondary btn-sm me-1 btn-regen" title="Regenerate CSS"><i class="lmb-icon lmb-refresh"></i></a><?php endif; ?><a href="main_admin.php?action=setup_color_schema&del=1&id=<?=$id?>" class="btn btn-outline-danger btn-sm me-1 text-danger"><i class="lmb-icon lmb-trash"></i></a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td class="align-middle pt-3"><input class="form-control form-control-sm" type="text" size="20" name="name"></td>
                                <td class="align-middle pt-3">
                                    <select class="form-select form-select-sm w-auto" name="layout">
                                        <?php foreach($layouts as $layout): ?>
                                            <option value="<?=e($layout)?>"><?=e($layout)?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td class="align-middle ps-3 pt-3">
                                    <button class="btn btn-sm btn-primary" type="submit"><?=$lang[540]?></button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
        <div class="col-md-7">
            <?php foreach ($color_schemas as $id => $colorSchema) : ?>
                <FORM ACTION="main_admin.php" METHOD="post">
                    <input type="hidden" name="action" value="setup_color_schema">
                    <input type="hidden" name="id" value="<?=$id?>">
                    <div class="card d-none color-card" id="edit-colors-<?=$id?>">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <input type="text" name="name" class="form-control form-control-sm w-auto" value="<?=e($colorSchema['name'])?>">
                                <?=e($colorSchema['layout'])?>
                                <div>
                                    <select class="form-select form-select-sm" name="theme">
                                        <option value="light" <?=empty($colorSchema['theme']) || $colorSchema['theme'] === 'light' ? 'selected' : ''?>>light</option>
                                        <option value="dark" <?=$colorSchema['theme'] === 'dark' ? 'selected' : ''?>>dark</option>
                                        <option value="custom" <?=!empty($colorSchema['theme']) && $colorSchema['theme'] !== 'light' && $colorSchema['theme'] !== 'dark' ? 'selected' : ''?>>custom</option>
                                    </select>
                                    <input type="text" value="<?=e($colorSchema['theme'] ?: 'light')?>" class="form-control form-control-sm <?=!empty($colorSchema['theme']) && $colorSchema['theme'] !== 'light' && $colorSchema['theme'] !== 'dark' ? '' : 'd-none'?>" name="custom_theme" placeholder="Name of the theme">
                                </div>
                            </div>
                        </div>
                        <table class="table table-sm table-striped table-borderless mb-0">
                            <thead><tr><th>Variable</th><th class="w-50">Wert</th></tr></thead>
        
                            <tbody>
                            
                            <?php foreach ($colorSchema['variables'] as $name => $group) :                        
                                ?>
                                <tr class="table-section"><th colspan="2"><?=$name?></th></tr>
                                <?php foreach ($group as $variable => $value) : ?>
                                    <tr>
                                        <td class="align-middle"><?=$variable?></td>
                                        <td><div class="row"><div class="col-2 pe-0"><input type="text" value="<?=$value?>" data-colorselect="cs<?=$id?>var<?=$variable?>"></div><div class="col-10 ps-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>var<?=$variable?>" name="var[<?=$variable?>]" value="<?=$value?>"></div></div></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>

                            <tr class="table-section"><th colspan="2">Eigene Variablen</th></tr>
                            <?php if (!empty($colorSchema['custvars'])) :
                                ?>
                                
                                <?php foreach ($colorSchema['custvars'] as $variable => $value) : ?>
                                <tr>
                                    <td class="align-middle"><?=$variable?></td>
                                    <td><div class="row"><div class="col-2 pe-0"><input type="text" value="<?=$value?>" data-colorselect="cs<?=$id?>var<?=$variable?>"></div><div class="col-10 ps-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>var<?=$variable?>" name="var[<?=$variable?>]" value="<?=$value?>"></div></div></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <tr>
                                <td class="align-middle"><input class="form-control form-control-sm" type="text" name="addvartitle"></td>
                                <td><div class="row"><div class="col-2 pe-0"><input type="text" data-colorselect="cs<?=$id?>addvar"></div><div class="col-10 ps-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>addvar" name="addvarvalue"></div></div></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-outline-secondary" type="button" name="resetdefault" value="1">Standard wiederherstellen</button>
                                </div>
                                <div class="col-sm-6 text-end">
                                    <button class="btn btn-primary" type="submit" name="change" value="1">Speichern</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </FORM>
            <?php endforeach; ?>
        </div>
    </div>
</div>
