<?php use Limbas\extra\mail\MailAccount; /** @var MailAccount $mailAccount */ ?>
<tr id="account-<?=e($mailAccount->id)?>"
    data-mail-name="<?=e($mailAccount->name)?>"
    data-mail-email="<?=e($mailAccount->email)?>"
    <?php if($adminMails): ?>
        data-mail-user-id="<?=e($mailAccount->userId)?>"
        <?php if($multiTenantEnabled): ?>
        data-mail-tenant-id="<?=e($mailAccount->tenantId)?>"
        <?php endif; ?>
    <?php endif; ?>
    data-mail-type="<?=e($mailAccount->transportType)?>"
    data-mail-imap-host="<?=e($mailAccount->imapHost)?>"
    data-mail-imap-port="<?=e($mailAccount->imapPort)?>"
    data-mail-imap-user="<?=e($mailAccount->imapUser)?>"
    data-mail-imap-path="<?=e($mailAccount->imapPath)?>"
    data-mail-smtp-host="<?=e($mailAccount->smtpHost)?>"
    data-mail-smtp-port="<?=e($mailAccount->smtpPort)?>"
    data-mail-smtp-user="<?=e($mailAccount->smtpUser)?>"
    data-mail-status="<?=$mailAccount->isActive?1:0?>"
    data-mail-default="<?=$mailAccount->isDefault?1:0?>"
    data-mail-hidden="<?=$mailAccount->isHidden?1:0?>"
    data-mail-selected="<?=$mailAccount->isSelected?1:0?>"
    data-mail-table="<?=e($mailAccount->mailTableId)?>"
    data-mail-signature="<?=e($mailAccount->mailSignatureId)?>"
>
    <td><i class="fas fa-pencil cursor-pointer link-primary" data-bs-toggle="modal" data-bs-target="#modal-mail-account" data-id="<?=e($mailAccount->id)?>"></i></td>
    <td><?=e($mailAccount->id)?></td>
    <td><?=e($mailAccount->name)?></td>
    <td><?=e($mailAccount->email)?></td>
    <?php if($adminMails): ?>
        <td><?=e($mailAccount->getUserName())?></td>
        <?php if($multiTenantEnabled): ?>
        <td><?=e($mailAccount->getTenantName())?></td>
        <?php endif; ?>
    <?php endif; ?>
    <td><?=e($mailAccount->getTransportName())?></td>
    <td><?=e($mailAccount->imapHost) . ':' . e($mailAccount->imapPort)?><br><?=e($mailAccount->imapUser)?></td>
    <td><?=e($mailAccount->smtpHost) . ':' . e($mailAccount->smtpPort)?><br><?=e($mailAccount->smtpUser)?></td>
    <td>
        <?php if($mailAccount->isActive): ?>
            <i class="fa-solid fa-circle-check text-success" title="active"></i>
        <?php else: ?>
            <i class="fa-solid fa-circle-xmark text-danger" title="inactive"></i>
        <?php endif; ?>
        <?php if($mailAccount->isDefault): ?>
            <i class="fa-solid fa-asterisk text-warning me-1" title="default" data-default="<?=e($mailAccount->tenantId ?? 0)?>"></i>
        <?php endif; ?>
        <?php if($mailAccount->isHidden): ?>
            <i class="fa-solid fa-eye-slash text-muted me-1" title="hidden"></i>
        <?php endif; ?>
        <?php if($mailAccount->isSelected): ?>
            <i class="fa-solid fa-check me-1" title="preselected" data-selected="<?= ($mailAccount->userId ? 'u' . $mailAccount->userId : 't' . ($mailAccount->tenantId ?? 0))?>"></i>
        <?php endif; ?>
    </td>
    <td class="text-end"><i class="fas fa-times cursor-pointer link-danger" data-delete="<?=e($mailAccount->id)?>"></i></td>
</tr>
