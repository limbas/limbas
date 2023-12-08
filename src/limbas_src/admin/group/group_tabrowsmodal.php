<div class="modal fade" id="tableRightsModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= "Liste" ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0" id="tableRightsContent">

            </div>
            <div class="modal-footer">
                <?php
                $useSubmitJavascript = true;
                require COREPATH . 'admin/group/submit-footer.php';
                ?>
            </div>
        </div>
    </div>
</div>