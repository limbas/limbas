<div id="form-printer-options">

    <?php if ($LINK[304] and $gprinter): ?>
        <div class="form-group mb-2">
            <label class="form-label mb-0"><?=$lang[2935]?></label>
            <select name="report_printer" class="form-select form-select-sm">
                <?php foreach ($gprinter as $id => &$printer): ?>
                    <option value="<?=$id?>" <?=($greportlist[$gtabid]["printer"][$report_id] == $id) ? 'selected' : ''?>><?=$printer['name']?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endif; ?>
    
    <div class="form-group">
        <label class="form-label mb-0">
            Anzahl Ausdrucke
        </label>
        <input type="number" class="form-control form-select-sm " value="1" min="1" name="pageCount">
    </div>
    
    <?php if($umgvar['printer_cache']): ?>
    <hr class="my-2">
    
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" name="directPrint">
            <label class="form-check-label">
                Direkt drucken (ohne Cache)
            </label>
        </div>
    </div>
    <?php endif; ?>
    
</div> 
