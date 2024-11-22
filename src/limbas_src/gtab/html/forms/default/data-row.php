<?php

if ($gfield[$gtabid]["funcid"][$key]):

    if ($gfield[$gtabid]["INDIZE"][$key]) {
        $indexed1 = "Indexed: " . $gfield[$gtabid]["INDIZE_TIME"][$key];
        $indexed2 = " (<B STYLE=\"color:green\">i</B>)";
    } else {
        $indexed1 = null;
        $indexed2 = null;
    }

    ?>

    <div class="row mb-2 position-relative">
        <label class="col-md-3 col-form-label"
               title="<?= $gfield[$gtabid]["beschreibung"][$key] ?>"
            <?= ($edittyp) ? 'OnClick="fieldtype(\'' . $gfield[$gtabid]['data_type'][$key] . '\',\'' . $gfield[$gtabid]['form_name'][$key] . '\',\'' . $indexed1 . '\');"' : '' ?>>
            <?= $gfield[$gtabid]["spelling"][$key] . $indexed2 ?>
            <span class="gtabBodyDetailNeedTitle"><?= lmb_substr($gfield[$gtabid]["need"][$key], 0, 1) ?></span>
        </label>
        <div class="col-md-9">

            <?php display_dftyp($gresult, $gtabid, $key, $ID, $edit, ' ', null, null, $bzm); ?>

        </div>
    </div>
    <?php
    $bzm++;

endif; ?>
