<?php

use Limbas\extra\template\report\ReportTemplateRender;

$reportTemplateRender = new ReportTemplateRender();
?>
<div class="col-md-5 pt-2">

    <div class="row">
        <div class="col-sm-8">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="vis-tab" data-bs-toggle="tab" href="#vis" role="tab" aria-controls="vis" aria-selected="true">Visuelle Dateneingabe</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="data-tab" data-bs-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="false">Dateneingabe</a>
                </li>
            </ul>
        </div>
        <div class="col-sm-4 text-end">
            <button type="button" class="btn btn-outline-dark" data-report-action="preview"><?=$lang[33]?></button>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane show active" id="vis" role="tabpanel" aria-labelledby="vis-tab">
            <div id="report_preview_wrapper">
                <div id="report_preview">
                    <div id="report_preview_form">
                        <?php
                        $reportTemplateRender->renderDynamicDataHtmlPreview($reportTemplateElements, intval($gtabid), intval($report_id));
                        ?>
                    </div>
                </div>
            </div>
            <div class="text-end mt-3">
                <button type="button" class="btn btn-outline-dark" data-report-action="preview"><?=$lang[33]?></button>
            </div>
        </div>
        <div class="tab-pane" id="data" role="tabpanel" aria-labelledby="data-tab">




            <div id="content_form" class="p-3">

                <?php
                foreach ($reportTemplateElements as &$reportTemplateElement):
                    foreach ($reportTemplateElement['allDynamicDataPlaceholders'] as $placeholder): ?>

                        <div class="mb-3 row">
                            <label for="<?=$placeholder->getIdentifier()?>" class="col-sm-3 col-form-label"><?=$placeholder->getDescription()?></label>
                            <div class="col-sm-9">
                                <?=$reportTemplateRender->formatDynamicDataPlaceholder($placeholder)?>
                            </div>
                        </div>

                    <?php
                    endforeach;
                endforeach;
                ?>

            </div>

            <div class="text-end">
                <button type="button" class="btn btn-outline-dark" data-report-action="preview"><?=$lang[33]?></button>
            </div>
        </div>
    </div>

</div>
<div class="col-md-5 pt-2">

    <?php require __DIR__ . '/pdf_preview.php'; ?>

</div>
