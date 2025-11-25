
<div class="d-flex mb-3 justify-content-between">
    <button type="button" id="btn-report-archive" class="btn btn-outline-dark" data-report-action="archive" data-text="<?= e($lang[1787]) ?>"><?= e($lang[1787]) ?></button>
    <a type="button" class="btn btn-outline-dark link-open-report" href="#"><?= e($lang[2321]) ?></a>
</div>

<?php if ($LINK[304] && $gprinter): ?>

<hr>
<p class="fw-bold mb-2"><?= e(ucfirst($lang[391])) ?></p>

    <?php include(COREPATH . 'extra/printer/html/options.php'); ?>

    <div class="d-flex py-3 justify-content-between">
        <button type="button" id="btn-report-archive-print" class="btn btn-outline-dark" data-report-action="archivePrint" data-text="<?= e($lang[1787]) ?> &amp; <?= e($lang[391]) ?>"><?= e($lang[1787]) ?> &amp; <?= e($lang[391]) ?></button>
        <button type="button" id="report_Print" class="btn btn-outline-dark" data-report-action="print" data-text="<?= e($lang[391]) ?>"><?= e($lang[391]) ?></button>
    </div>

<?php endif; ?>
