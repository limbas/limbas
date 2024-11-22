<?php use Limbas\admin\setup\umgvar\UmgVar; /** @var UmgVar $umgVar */?>
<tr id="umgvar-<?=e($umgVar->id)?>" <?=isset($isNew)?'class="table-success"':''?>>
    <td><?=e($umgVar->name)?></td>
    <td><?=e($umgVar->description)?></td>
    <td>
        <?php
        switch ($umgVar->fieldType):
            case 'bool': ?>
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           value="<?= $umgVar->value ? 0 : 1 ?>"
                           data-update="value" data-id="<?=e($umgVar->id)?>"
                        <?= $umgVar->value == 1 ? 'checked' : '' ?>
                    >
                </div>
                <?php
                break;
            case 'select':
                $values = json_decode($umgVar->fieldOptions);
                if(is_array($values) || is_object($values)):
                    ?>
                    <select class="form-select form-select-sm"
                            type="text"
                            data-update="value" data-id="<?=e($umgVar->id)?>">

                        <?php foreach ($values as $optionName => $optionValue): ?>
                            <option value="<?= e($optionValue) ?>" <?= $umgVar->value == $optionValue ? 'selected' : ''?>>
                                <?= is_object($values) ? $optionName : $optionValue ?>
                            </option>
                        <?php endforeach; ?>


                    </select>
                    <?php
                    break;
                endif;
            default: ?>

                <input class="form-control form-control-sm umgvar-change" type="text" size="50"
                       value="<?= e($umgVar->value) ?>"
                       data-update="value" data-id="<?=e($umgVar->id)?>">

            <?php
        endswitch; ?>
    </td>
    <?php if ($umgvar['admin_mode']): ?>

        <td>
            <select class="form-select form-select-sm umgvar-change-admin"
                    name="cat[<?= $umgVar->id ?>]">
                <?php foreach ($categories as $value => $none): ?>
                    <option value="<?=$value?>" <?=$umgVar->category == $value ? 'selected' : ''?>><?=$lang[$value]?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td class="text-center align-middle">
            <i class="fas fa-times cursor-pointer link-danger" data-delete="<?= e($umgVar->id) ?>"></i>
        </td>

    <?php endif; ?>
</tr>
