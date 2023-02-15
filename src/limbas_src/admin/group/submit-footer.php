
<?php if($session['user_id'] != 1 AND $session['group_id'] == $ID):
    lmb_alert("no permission to change own group!");
else: ?>
    <div class="d-flex justify-content-start align-items-center gap-3">
        <?php if(isset($useSubmitJavascript)): ?>
            <button type="button" class="btn btn-primary" value="1" name="change_rules" onclick="send();document.form1.submit();"><?=$lang[33]?></button>
        <?php else: ?>
            <button type="submit" class="btn btn-primary" value="1" name="change_rules"><?=$lang[33]?></button>
        <?php endif; ?>
        
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="addsubgroup" name="addsubgroup">
            <label class="form-check-label" for="addsubgroup">
                <?=$lang[2107]?>
            </label>
        </div>
    </div>

<?php endif; ?>
