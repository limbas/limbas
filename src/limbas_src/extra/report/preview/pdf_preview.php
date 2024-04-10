
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
            <?=$reportPreview->getReportActions($gtabid, $report_id); ?>
        </div>
        
        <div class="d-flex align-items-end gap-2">
            <div>
                <label><?=$lang[4]?></label>
                <input type="text" name="report_rename" class="form-control form-control-sm me-2" value="" placeholder="<?=$report_name?>">
            </div>
        </div>
    </div>

    

    <div class="text-center pt-5 btn-show-preview">
        <button type="button" class="btn btn-outline-dark" data-report-action="preview"><?=$lang[1500]?></button>
    </div>
    <div class="text-center pt-5 load-preview d-none">
        <i class="fas fa-spinner fa-spin fa-3x"></i>
    </div>
    
    
    <iframe id="report_source" src="" class="h-100 w-100"></iframe>
