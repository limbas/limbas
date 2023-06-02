<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
global $umgvar;

require_once __DIR__ . '/reportManager.dao';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title></title>

    <script type="text/javascript" src="assets/js/lib/global.js?v=<?=$umgvar["version"]?>"></script>
    <script src="main.php?action=syntaxcheckjs"></script>
    <script type="text/javascript" src="assets/vendor/jquery/jquery.min.js?v=<?=$umgvar["version"]?>"></script>
    
    <script type="text/javascript" src="assets/js/extra/extensions/ext.js?v=<?=$umgvar["version"]?>"></script>

    <link rel="stylesheet" type="text/css" href="assets/css/<?=$session['css']?>?v=<?=$umgvar["version"]?>">
</head>

<body>
<script src="assets/vendor/tinymce/tinymce.min.js?v=<?=$umgvar["version"]?>"></script>

<div class="container-fluid p-3">

    <ul class="nav nav-tabs">
        <?php if (!$type) : ?>
            <li class="nav-item">
                <a class="nav-link active bg-white" href="#"><?=$lang[$LINK["name"][$LINK_ID['user_reportmanager']]]?></a>
            </li>
            <?php if ($LINK['user_templatemanager'] && !empty($reportTemplates)): ?>
                <li class="nav-item">
                    <a class="nav-link" href="main.php?action=user_templatemanager&type=1"><?=$lang[$LINK["name"][$LINK_ID['user_templatemanager']]]?></a>
                </li>
            <?php endif; ?>
        <?php elseif($LINK['user_templatemanager']): ?>
            <?php if ($LINK['user_reportmanager']): ?>
                <li class="nav-item">
                    <a class="nav-link" href="main.php?action=user_reportmanager"><?=$lang[$LINK["name"][$LINK_ID['user_reportmanager']]]?></a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link active bg-white"><?=$lang[$LINK["name"][$LINK_ID['user_templatemanager']]]?></a>
            </li>
            
        <?php endif; ?>
    </ul>
    <div class="tab-content border border-top-0 bg-white">
        <div class="tab-pane active p-3">

            <div class="row">
                <div class="col-md-4">
                    <p class="fw-bold"><?=$lang[2783]?></p>
                    <?php if ($LINK['user_reportmanager'] && !$type): ?>
                    <?php if (empty($reports)) : ?>
                        <p><?=$lang[3080]?></p>
                    <?php endif; ?>
                    <div class="list-group list-group-root">
                        <?php foreach($reports as $rid => $report) : ?>
                            <a href="#report-<?=$rid?>" class="list-group-item list-group-item-action" data-bs-toggle="collapse" data-load="report-<?=$rid?>" data-reportid="<?=$rid?>" data-template="" data-gtabid="<?=$report['gtabid']?>" data-settings="">
                                <i class="lmb-icon lmb-caret-right"></i><?=$report['name']?> <span class="small align-middle text-muted"><?=$report['table_name']?></span>
                            </a>
                            <div class="list-group collapse ps-2" id="report-<?=$rid?>">

                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php elseif ($LINK['user_templatemanager'] && $type == 1): ?>
                        <?php if (empty($reportTemplates)) : ?>
                            <p><?=$lang[3080]?></p>
                        <?php endif; ?>
                        <div class="list-group list-group-root">
                            <?php foreach($reportTemplates as $tid => $reportTemplate) : ?>
                                <a href="#template-<?=$tid?>" class="list-group-item list-group-item-action" data-bs-toggle="collapse" data-load="template-<?=$tid?>" data-reportid="<?=$reportTemplate['report_id']?>" data-template="" data-settings="<?=htmlentities($reportTemplate['settings'],ENT_QUOTES,$umgvar['charset'])?>" data-gtabid="<?=$reportTemplate['gtabid']?>">
                                    <div class="row">
                                        <div class="col-10">
                                            <i class="lmb-icon lmb-caret-right"></i><?=$reportTemplate['name']?> <span class="small align-middle text-muted"><?=$reportTemplate['table_name']?></span>
                                        </div>
                                        <div class="col-2 text-end d-none">
                                            <i class="cursor-pointer lmb-icon lmb-trash" data-delete="r-<?=$tid?>" data-delete-name="<?=$reportTemplate['name']?>" data-gtabid="-"></i>
                                        </div>
                                    </div>
                                </a>                            
                                <div class="list-group collapse ps-2" id="template-<?=$tid?>">
                                    
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8 d-none" id="editor-div">
                    
                    <div class="sticky-top">
                        <div class="row mb-2">
                            <div class="col-sm-10 fw-bold">
                                <?=$lang[1259]?>: <span id="edit-template-name"></span>
                            </div>
                            <div class="col-sm-2 text-end">
                                <?php //TODO: lang ?>
                                <i class="cursor-pointer lmb-icon lmb-page-copy" id="btn-show-copy-template" title="Template kopieren"></i>
                            </div>
                            <div class="col-sm-2 text-end d-none">
                                <i class="cursor-pointer lmb-icon lmb-trash text-danger" id="btn-show-delete-template"></i>
                            </div>
                        </div>
                        
                        <div class="w-100">
    
    
                            <?php
    
                            //$formname = 'g_'.$gtabid.'_'.$field_id;
                            $formname = 'g_0_0';
                            global $lang;
                            ?>
    
                            <FORM id="form1" name="form1">
                                <input type="hidden" name="action" value="edit_long">
                                <input type="hidden" name="gtabid" id="form_gtabid">
                                <input type="hidden" name="field_id" value="2" id="form_fieldid">
                                <input type="hidden" name="ID" id="form_id">
                                <textarea id="<?=$formname?>" NAME="<?=$formname?>"></textarea>
                            </FORM>
    
                            <?php
                            echo lmb_ini_wysiwyg($formname,null,null,1,650);
                            ?>
                            <button type="button" class="btn btn-secondary mt-3" id="btn-save" disabled><?=$lang[842]?></button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    
    
    
</div>

<div class="modal" id="modal-new-template" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-new-template-title"><?=$lang[3081]?> <span id="add-to-group"></span></h5>
                <h5 class="modal-title" id="modal-copy-template-title"><?php //TODO lang ?>Template &quot;<span id="copy-name"></span>&quot; kopieren</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="new-template-name"><?=$lang[3082]?>:</label>
                    <input type="text" class="form-control" id="new-template-name">
                </div>
                <div class="mb-3" id="template-copy">
                    <label for="new-template-group">Kopieren nach:</label>
                    <select class="form-select" id="new-template-group">
                        
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-primary" id="btn-save-new-template"><?=$lang[842]?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="modal-delete-template" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[3083]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                &quot;<span id="delete-name"></span>&quot;<br>
                <?=$lang[3084]?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=$lang[2227]?></button>
                <button type="button" class="btn btn-danger" id="btn-delete-template"><?=$lang[160]?></button>
            </div>
        </div>
    </div>
</div>


<script src="assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?=rtrim($umgvar['url'],'/')?>/assets/js/extra/reportManager/reportManager.js"></script>
</body>
</html>

