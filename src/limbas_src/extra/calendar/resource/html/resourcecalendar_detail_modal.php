<?php
global $lang;
?>

<div class="modal fade" id="resource-detail-modal" tabindex="-1">
    <form>
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resource-detail-modal-title"><?= $lang[1441] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resource-detail-modal-body">
                <div class="row mb-3">
                    <div class="col">
                        <label for="input-startdate" class="form-label mb-1"><?= $lang[2382] ?></label>
                        <input type="datetime-local" class="form-control" id="input-startdate" name="startdate" required>
                    </div>
                    <div class="col">
                        <label for="input-enddate" class="form-label mb-1"><?= $lang[2385] ?></label>
                        <input type="datetime-local" class="form-control" id="input-enddate" name="enddate" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="input-resource" class="form-label mb-1"><?= /* todo */ 'Resource' ?></label>
                        <select class="form-select" id="input-resource" name="resource" required>

                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="input-title" class="form-label mb-1"><?= $lang[2381] ?></label>
                        <input type="text" class="form-control" id="input-title" name="title">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="input-color" class="form-label mb-1"><?= $lang[294] ?></label>
                        <input type="color" class="form-control" id="input-color" name="color">
                    </div>
                    <div class="col">
                        <div class="form-check">
                            <label class="form-check-label" for="input-allday">
                                <?= $lang[2705] ?>
                            </label>
                            <input class="form-check-input" type="checkbox" value="" id="input-allday" name="allday">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="resource-detail-modal-footer-default">
                <button type="button" id="rc-btn-delete" class="btn btn-danger"><?= $lang[160] ?></button>
                <button type="submit" id="rc-btn-submit" class="btn btn-success"><?= $lang[842] ?></button>
                <button type="button" id="rc-btn-close" class="btn btn-dark" data-bs-dismiss="modal"><?= $lang[844] ?></button>
            </div>
        </div>
    </div>
    </form>
</div>
