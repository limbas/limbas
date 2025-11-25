<?php use Limbas\admin\setup\tinymce\TinyMceConfig; /** @var TinyMceConfig $config */ ?>
<tr id="config-<?=e($config->id)?>"
    data-config-name="<?=e($config->name)?>"
    data-config-default="<?=$config->isDefault?1:0?>"
    data-config-config="<?=e(json_encode($config->config))?>"
>
    <td><i class="fas fa-pencil cursor-pointer link-primary" data-bs-toggle="modal" data-bs-target="#modal-config" data-id="<?=e($config->id)?>"></i></td>
    <td><?=e($config->id)?></td>
    <td><?=e($config->name)?></td>
    <td>
        <?php if($config->isDefault): ?>
            <i class="fa-solid fa-asterisk text-warning me-1" title="default" data-default="0"></i>
        <?php endif; ?>
    </td>
    <td class="text-end"><i class="fas fa-times cursor-pointer link-danger" data-delete="<?=e($config->id)?>"></i></td>
</tr>
