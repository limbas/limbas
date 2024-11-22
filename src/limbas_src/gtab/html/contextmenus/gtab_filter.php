
<div class="modal fade" id="searchFilterModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content mh-90">
            <div class="modal-header">
                <h5 class="modal-title"><?=$lang[2608]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-visible" id="searchFilterModal-body">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="snapfilter-menu" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content mh-90">
            <div class="modal-header">
                <h5 class="modal-title" id="snapfilter-menu-title"><?=$lang[2608]?> <?=$gsnap[$gtabid]["name"][$snap_id]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="snapfilter-menu-body">

              <div class="d-grid gap-2">

                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="lmb-icon lmb-plus"></i></span>
                    <input type="text" class="form-control" value="">
                    <button type="button" class="btn btn-primary" onclick="limbasSnapshotSaveas(<?=$gtabid?>,$(this).prev().val())"><?=$lang[1998]?></button>
                </div>

                <?php if($snap_id){?>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="lmb-icon lmb-save"></i></span>
                    <button type="button" class="btn btn-sm btn-outline-primary form-control" onclick="limbasSnapshotSave();"><?=$lang[1002]?></button>
                </div>
                <?php if(($gsnap[$gtabid]["owner"][$snap_id] OR $session['group_id'] == 1) OR $gsnap[$gtabid]["del"][$snap_id]){?>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="lmb-icon lmb-trash"></i></span>
                    <button type="button" class="btn btn-sm btn-outline-danger form-control" onclick="limbasSnapshotDelete();"><?=$lang[160]?></button>
                </div>
                <?php }}?>

                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="lmb-icon lmb-cog"></i></span>
                    <button type="button" class="form-control btn btn-outline-sm btn-secondary" onclick="limbasSnapshotManage('<?=$gtabid?>')"><?=$lang[2000]?></button>
                </div>

              </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="snapfilter-manage" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div id="modald-snapfilter_manage" class="modal-dialog modal-dialog-centered modal-xl overflow-hidden h-100">
        <div class="modal-content mh-90">
            <div class="modal-header">
                <h5 class="modal-title" id="snapfilter-manage-title"><?=$lang[2608]?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body overflow-hidden" id="napfilter-manage-body">
              <iframe id="snapfilter-manage-content" class="w-100 h-100"></iframe>
            </div>
        </div>
    </div>
</div>

<?php require(COREPATH . 'extra/explorer/mini_explorer_modal.php'); ?>
