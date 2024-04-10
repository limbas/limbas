<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

use Limbas\extra\template\TemplateTable;

global $lang;
global $umgvar;

?>

<script src="main.php?action=syntaxcheckjs"></script>
<script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar['version']?>"></script>
<script src="assets/js/admin/templates/editor.js?v=<?=$umgvar['version']?>"></script>

<div class="container-fluid p-3">

    <form id="form1">
        <input type="hidden" name="action" value="saveTemplate">
        <input type="hidden" name="actid" value="manageTemplates">
        <input type="hidden" name="gtabid" value="<?=$templateTableId?>">
        <input type="hidden" name="ID" id="formid" value="<?=$templateId?>">


        <input type="hidden" name="type" value="<?=$type?>">
        <input type="hidden" name="element_id" value="<?=$elementId?>">


        <input type="hidden" name="content" id="content">
        
        
        <div class="row">
        <div class="col-md-8" id="editor-div">
            <div class="w-100">
                <textarea id="g_0_0" NAME="g_0_0">
                    <?=e($template->getHtml())?>
                </textarea>

                <?php
                echo lmb_ini_wysiwyg('g_0_0',null,null,1,650,  intval($templateTableId));
                ?>

            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h1><?=$title?></h1>
                    <div class="row">
                        <div class="col-sm-4"><?=$lang[1162]?></div>
                        <div class="col-sm-8 fw-bold">
                            <?=$tableName?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><?=$lang[403]?></div>
                <div class="card-body">
                    <form>
                        <div class="mb-1 row">
                            <label for="template_table" class="col-sm-4 col-form-label">Template-Table</label>
                            <div class="col-sm-8">
                                <select class="form-select form-select-sm" id="template_table" name="template_table">
                                    <?php
                                    /** @var TemplateTable $templateTable */
                                    foreach($templateTables as $templateTable): ?>
                                        <option value="<?=$templateTable->id?>" <?=($templateTableId === $templateTable->id) ? 'selected' : ''?>><?=$templateTable->name?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <?=$editorSettings?>

                    </form>
                </div>
                <div class="card-footer text-end">
                    <button type="button" class="btn btn-secondary" id="btn-save" data-title="<?=$lang[842]?>" disabled><?=$lang[842]?></button>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
