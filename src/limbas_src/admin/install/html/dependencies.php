<?php use Limbas\admin\install\InstallMessage; ?>
<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="3">
            php.ini
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    /** @var InstallMessage $phpIniMessage */
    foreach($phpIniMessages as $phpIniMessage): ?>
        <tr>
            <td><?=$phpIniMessage->icon()?></td>
            <td><?=$phpIniMessage->name?></td>
            <td><?=$phpIniMessage->getMessage()?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="3">
            <?= lang('write permissions (recursive)') ?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    /** @var InstallMessage $writePermissionMessage */
    foreach($writePermissionMessages as $writePermissionMessage): ?>
        <tr>
            <td><?=$writePermissionMessage->icon()?></td>
            <td><?=$writePermissionMessage->name?></td>
            <td><?=$writePermissionMessage->getMessage()?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>

<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="3">
            <?=lang('Functions')?>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    /** @var InstallMessage $dependencyMessage */
    foreach($dependencyMessages as $dependencyMessage): ?>
        <tr>
            <td><?=$dependencyMessage->icon()?></td>
            <td><?=$dependencyMessage->name?></td>
            <td><?=$dependencyMessage->getMessage()?></td>
        </tr>

    <?php endforeach; ?>
    </tbody>
</table>
