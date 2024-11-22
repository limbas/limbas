<div class="container form-legacy">
    <div class="row ">
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["SUBJECT"]] ?>
            <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["SUBJECT"], $ID, 1, null, "width:100%;height:36px;"); ?>
        </div>
    </div>

    <div class="row ">
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["STARTSTAMP"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["STARTSTAMP"], $ID, 1, null, "width:93%;"); ?>
            </div>
        </div>
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["ENDSTAMP"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["ENDSTAMP"], $ID, 1, null, "width:93%;"); ?>
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["COLOR"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["COLOR"], $ID, 1, null, "width:93%;"); ?>
            </div>
        </div>
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["ALLDAY"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["ALLDAY"], $ID, 1); ?>
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["REPEATUNTIL"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["REPEATUNTIL"], $ID, 1, null, "width:96%;"); ?>
            </div>
        </div>
    </div>

    <div class="row ">
        <div class="col position-relative">
            <?= $gfield[$gtabid]['spelling'][$gfield[$gtabid]["argresult_name"]["DESCRIPTION"]] ?>
            <div class="position-relative">
                <?php display_dftyp($gresult, $gtabid, $gfield[$gtabid]["argresult_name"]["DESCRIPTION"], $ID, 1, null, "width:100%;height:100px"); ?>
            </div>
        </div>
    </div>

    <div class="row pt-2">
        <div class="col position-relative">
            <input type="button" value="<?= $lang[33] ?>" onclick="lmb_calEdit(event);">
            <?php if ($ID > 0): ?>
                <input type="button" value="details"
                       onclick="lmb_popupDetails(event,'<?= $gtabid ?>','<?= $ID ?>')">
                <input style="float:right" onclick="lmb_calDelete(event,'<?= $gtabid ?>','<?= $ID ?>')"
                       type="button" value="<?= $lang[160] ?>">
            <?php endif; ?>
        </div>
    </div>
</div>
<?php