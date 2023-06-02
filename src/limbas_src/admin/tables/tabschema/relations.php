
<div class="p-3">
    <?= $lang[30] ?> : <input type="text" onkeyup="lmb_search_pause(this.value)" class="form-control form-control-sm w-auto d-inline-block">
</div>


<?php

$tables = dbf_20(array($DBA['DBSCHEMA'], null, 'TABLE'));
$tables = $tables["table_name"];

?>

<div class="list-hierarchy">
    <ul>
        <?php
        foreach ($tables as $key => $value):
            if ($dep = lmb_checkViewDependency($value)): ?>

                <li class="roottree">
                    <div><?= $value ?></div>
                    <?= lmb_make_tree($value); ?>
                </li>

            <?php endif;
        endforeach;
        ?>
    </ul>
</div>
