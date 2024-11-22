<?php use Limbas\admin\form\Form;
use Limbas\admin\form\FormMode;

global $gtab, $LINK; /** @var Form $form */?>

<tr id="form-<?=e($form->id)?>" <?=isset($isNew)?'class="table-success"':''?>>
    <td><?=e($form->id)?></td>
    <td>
        <?php if(empty($form->extension) && $form->mode === FormMode::NEW): ?>
            <a href="main_admin.php?&action=setup_form_show&id=<?=e($form->id)?>"><i class="lmb-icon lmb-pencil cursor-pointer"></i></a>
        <?php elseif(empty($form->extension)): ?>
            <a href="main_admin.php?&action=setup_form_frameset&form_typ=<?=$form->formType->value?>&form_id=<?=e($form->id)?>&referenz_tab=<?=e($form->gtabId)?>&type=form"><i class="lmb-icon lmb-pencil cursor-pointer"></i></a>
        <?php endif; ?>
    </td>
    <td><i data-delete="<?=e($form->id)?>" class="lmb-icon lmb-trash cursor-pointer"></i></td>
    <td>
        <input type="text" data-update="name" data-id="<?=e($form->id)?>" value="<?=e($form->name)?>" class="form-control form-control-sm" style="min-width: 160px">
    </td>
    
    <td><?=e($form->formType->name())?></td>

    <td><?=e($gtab['desc'][$form->gtabId])?></td>
    <td><?=e($form->extension)?></td>


    <td>
        <select data-update="custom_menu" data-id="<?=e($form->id)?>" class="form-select form-select-sm">
            <option value="0"></option>
            <?php foreach ($LINK['name'] as $key => $value):
                if ($LINK['typ'][$key] == 1 AND $LINK['subgroup'][$key] == 2 AND $key >= 1000):
                ?>
            
                <option value="<?=e($key)?>" <?=$key === $form->customMenu ? 'selected' : ''?>><?=e($lang[$value])?></option>
            <?php 
            endif;
            endforeach; ?>
        </select>
        
    </td>
    
    <td>
        <?=e($form->getCreatedByUserName())?>
    </td>
    
</tr>
