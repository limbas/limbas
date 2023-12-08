<?php if($path != null): ?>
    <div class="row">
        <div class="col-md-6 mb-2">
            <div class="row row-cols-lg-auto g-3">
                <?php if ($LINK[304] and $gprinter): ?>
                    <button type="button" id="report_Print" class="btn btn-outline-dark ms-2 me-2" onclick="reportPrint(this)"><?=$lang[391]?></button>
                <?php endif; ?>
                <button type="button" id="report_Archive" class="btn btn-outline-dark me-4" onclick="reportArchive(this)"><?=$lang[1787]?></button>
                <button type="button" class="btn btn-outline-dark me-2" onclick="openInNewTab()"><?=$lang[2321]?></button>
                <?php /*<button type="button" class="btn btn-outline-dark me-2" onclick="downloadUrl('<?=$report_name?>')"><?=$lang[1612]?></button> */?>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="row row-cols-lg-auto g-3 justify-content-end">

                <?php if ($LINK[304] and $gprinter): ?>
                    <div>
                        <label><?=$lang[2935]?>
                            <select name="report_printer" class="form-select form-select-sm me-2">
                                <?php foreach ($gprinter as $id => &$printer): ?>
                                    <option value="<?=$id?>" <?=($greportlist[$gtabid]["printer"][$report_id] == $id) ? 'selected' : ''?>><?=$printer['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                <?php endif; ?>

                <div>
                    <label><?=$lang[4]?><input type="text" name="report_rename" class="form-control form-control-sm me-2" value="<?=$report_name?>"></label>
                </div>

            </div>
        </div>
    </div>

    <iframe id="report_source" src="<?=$path?>" class="h-100 w-100"></iframe>
<?php else: ?>

    <input type="hidden" name="report_medium" id="report_medium" value="<?=$report_medium?>">

    <div class="text-center pt-5">
        <button type="button" class="btn btn-outline-dark" onclick="loadPreview()"><?=$lang[1500]?></button>
    </div>


<?php endif; ?>
