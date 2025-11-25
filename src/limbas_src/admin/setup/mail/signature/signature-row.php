<?php use Limbas\extra\mail\MailSignature; /** @var MailSignature $mailSignature */ ?>
<tr id="signature-<?=e($mailSignature->id)?>"
    data-mail-name="<?=e($mailSignature->name)?>"
    data-mail-content="<?=e($mailSignature->content)?>"
    <?php if($adminSignatures): ?>
        data-mail-user-id="<?=e($mailSignature->userId)?>"
        <?php if($multiTenantEnabled): ?>
        data-mail-tenant-id="<?=e($mailSignature->tenantId)?>"
        <?php endif; ?>
    <?php endif; ?>
    data-mail-status="<?=$mailSignature->isActive?1:0?>"
    data-mail-default="<?=$mailSignature->isDefault?1:0?>"
    data-mail-hidden="<?=$mailSignature->isHidden?1:0?>"
>
    <td><i class="fas fa-pencil cursor-pointer link-primary" data-bs-toggle="modal" data-bs-target="#modal-mail-signature" data-id="<?=e($mailSignature->id)?>"></i></td>
    <td><?=e($mailSignature->id)?></td>
    <td><?=e($mailSignature->name)?></td>
    <?php if($adminSignatures): ?>
        <td><?=e($mailSignature->getUserName())?></td>
        <?php if($multiTenantEnabled): ?>
        <td><?=e($mailSignature->getTenantName())?></td>
        <?php endif; ?>
    <?php endif; ?>
    <td>
        <?php if($mailSignature->isActive): ?>
            <i class="fa-solid fa-circle-check text-success" title="active"></i>
        <?php else: ?>
            <i class="fa-solid fa-circle-xmark text-danger" title="inactive"></i>
        <?php endif; ?>
        <?php if($mailSignature->isDefault): ?>
            <i class="fa-solid fa-asterisk text-warning me-1" title="default" data-default="<?=e($mailSignature->tenantId ?? 0)?>"></i>
        <?php endif; ?>
        <?php if($mailSignature->isHidden): ?>
            <i class="fa-solid fa-eye-slash text-muted me-1" title="hidden"></i>
        <?php endif; ?>
    </td>
    <td class="text-end"><i class="fas fa-times cursor-pointer link-danger" data-delete="<?=e($mailSignature->id)?>"></i></td>
</tr>
