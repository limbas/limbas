<?php
/*
 * Copyright notice
 * (c) 1998-2019 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.6
 */

/*
 * ID: 106
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
        
        $('[data-editcolor]').click(function () {
            $('.color-card').addClass('d-none');
            $('#edit-colors-'+$(this).data('editcolor')).removeClass('d-none');
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
            <div class="card">
                <form action="main_admin.php" method=post name="form1">
                    <input type="hidden" name="action" value="setup_color_schema">
                    <input type="hidden" name="add" value="1">
                    <table class="table table-striped table-borderless mb-0">
                        <thead><tr><th></th><th>Name</th><th>Aktion</th></tr></thead>
                        
                        <tbody>
                        <?php foreach ($color_schemas as $id => $color_schema) : ?>
                        <tr>
                            <td class="align-middle"><?=$id?></td>
                            <td class="align-middle"><?=$color_schema['name'].' ('.$color_schema['layout'].')'?></td>
                            <td class="align-middle text-nowrap"><button type="button" class="btn btn-outline-secondary btn-sm mr-1" data-editcolor="<?=$id?>"><i class="lmb-icon lmb-pencil"></i></button><?php if($color_schema['regenerate']) : ?><a href="main_admin.php?action=setup_color_schema&regenerate=1&id=<?=$id?>" class="btn btn-outline-secondary text-secondary btn-sm mr-1" title="Regenerate CSS"><i class="lmb-icon lmb-refresh"></i></a><?php endif; ?><a href="main_admin.php?action=setup_color_schema&del=1&id=<?=$id?>" class="btn btn-outline-danger btn-sm mr-1 text-danger"><i class="lmb-icon lmb-trash"></i></a></td></tr>
                        
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                            <td class="text-nowrap">
                                <select class="form-control form-control-sm d-inline w-auto" name="layout">
                                    <?php
                                    foreach($layouts as $layout){
                                        echo "<option value=\"".$layout."\">$layout</option>";
                                    }
                                    ?>
                                </select>
                                <input class="form-control form-control-sm d-inline w-auto" type="text" size="20" name="name"></td><td><button class="btn btn-sm btn-primary" type="submit"><?=$lang[540]?></button></td></tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>
        <div class="col-md-7">
            <?php foreach ($color_schemas as $id => $color_schema) : ?>
                <FORM ACTION="main_admin.php" METHOD=post name="form1">
                    <input type="hidden" name="action" value="setup_color_schema">
                    <input type="hidden" name="id" value="<?=$id?>">
                    <div class="card d-none color-card" id="edit-colors-<?=$id?>">
                        <div class="card-header">
                            <?=$color_schema['name'].' ('.$color_schema['layout'].')'?>
                        </div>
                        <table class="table table-sm table-striped table-borderless mb-0">
                            <thead><tr><th>Variable</th><th class="w-50">Wert</th></tr></thead>
        
                            <tbody>
                            
                            <?php foreach ($color_schema['variables'] as $name => $group) :                        
                                ?>
                                <tr><th colspan="2"><?=$name?></th></tr>
                                <?php foreach ($group as $variable => $value) : ?>
                                    <tr>
                                        <td class="align-middle"><?=$variable?></td>
                                        <td><div class="row"><div class="col-2 pr-0"><input type="text" value="<?=$value?>" data-colorselect="cs<?=$id?>var<?=$variable?>"></div><div class="col-10 pl-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>var<?=$variable?>" name="var[<?=$variable?>]" value="<?=$value?>"></div></div></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>


                            <?php if (!empty($color_schema['custvars'])) :
                                ?>
                                <tr><th colspan="2">Eigene Variablen</th></tr>
                                <?php foreach ($color_schema['custvars'] as $variable => $value) : ?>
                                <tr>
                                    <td class="align-middle"><?=$variable?></td>
                                    <td><div class="row"><div class="col-2 pr-0"><input type="text" value="<?=$value?>" data-colorselect="cs<?=$id?>var<?=$variable?>"></div><div class="col-10 pl-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>var<?=$variable?>" name="var[<?=$variable?>]" value="<?=$value?>"></div></div></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <tr>
                                <td class="align-middle"><input class="form-control form-control-sm" type="text" name="addvartitle"></td>
                                <td><div class="row"><div class="col-2 pr-0"><input type="text" data-colorselect="cs<?=$id?>addvar"></div><div class="col-10 pl-0"><input class="form-control form-control-sm" type="text" id="cs<?=$id?>addvar" name="addvarvalue"></div></div></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-outline-secondary" type="submit" name="resetdefault">Standard wiederherstellen</button>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <button class="btn btn-primary" type="submit" name="change">Speichern</button>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </FORM>
            <?php endforeach; ?>
        </div>
    </div>
</div>
