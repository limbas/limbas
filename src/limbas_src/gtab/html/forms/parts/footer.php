<div class="card-footer text-end">
    <div class="row">
        <div class="col-md-<?= ($action != 'gtab_deterg' && !$readonly) ? 6 : 12 ?>">

            <div class="d-flex flex-row align-items-center">
                <?=$lang[722]?>

                <i title="<?=$lang[857]?>" class="lmb-icon lmb-first" onclick="document.form1.scrollto.value='start';send_form('1');"></i>
                <i title="<?=$lang[860]?>" class="lmb-icon lmb-previous" onclick="document.form1.scrollto.value='prev';send_form('1');"></i>
                <input type="text" class="form-control form-select-sm d-inline w-auto" value="<?=$ID?>" onchange="document.form1.scrollto.value=this.value;send_form('1');">

                <i title="<?=$lang[859]?>" class="lmb-icon lmb-next" onclick="document.form1.scrollto.value='next';send_form('1');"></i>
                <i title="<?=$lang[858]?>" class="lmb-icon lmb-last" onclick="document.form1.scrollto.value='end';send_form('1');"></i>
            </div>

        </div>
        <?php if ($action != 'gtab_deterg' && !$readonly):

            $ajax = false;
            if($GLOBALS['actid'] == 'openSubForm' AND $_REQUEST['subformlayername']){
                $ajax = true;
            }
            ?>
            <div class="col-md-6 ps-0">

                <button type="button" class="btn btn-outline-secondary submit" name="lmbSbm" id="lmbSbm" onclick="document.form1.action.value='gtab_change'; send_form('1'<?=($ajax ? ',\'1\'' : '')?>);"><?=$lang[33]?></button>
                <button type="button" class="btn btn-outline-secondary submit" name="lmbSbmClose" id="lmbSbmClose_<?=$gtabid?>_<?=$ID?>" onclick="document.form1.action.value='gtab_change'; send_form(1,0,1);" style="display: none"><?=$lang[2796]?></button>
            </div>
        <?php endif; ?>
    </div>
</div>
