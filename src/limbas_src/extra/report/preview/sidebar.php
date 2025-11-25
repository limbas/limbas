<div class="pt-3">
    <div class="row mb-3">
        <label for="report_rename" class="col-sm-3 col-form-label col-form-label-sm"><?=$lang[4]?></label>
        <div class="col-sm-9">
            <input type="text" class="form-control form-control-sm" id="report_rename" name="report_rename" placeholder="<?=$report_name?>">
        </div>
    </div>

    <?=$reportPreview->getReportActions($gtabid, $report_id); ?>
    
    
</div>


