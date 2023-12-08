<tr id="template-<?=e($mailTemplate->id)?>"
    data-template-name="<?=e($mailTemplate->name)?>"
    data-template-tab-id="<?=e($mailTemplate->tabId)?>"
    data-template-description="<?=e($mailTemplate->description)?>"
>
    <td>
        <a href="main_admin.php?action=setup_template_editor&type=mail&id=<?=e($mailTemplate->id)?>"><i class="fas fa-pencil"></i></a>
    </td>
    <td><?=e($mailTemplate->id)?></td>
    <td><?=e($mailTemplate->name)?></td>
    <td><?=e($mailTemplate->getTabName())?></td>
    <td><?=e($mailTemplate->description)?></td>
    <td class="text-end"><i class="fas fa-times cursor-pointer link-danger" data-delete="<?=e($mailTemplate->id)?>"></i></td>
</tr>
