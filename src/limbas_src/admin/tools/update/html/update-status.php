<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
$updateNecessary = $systemInformation['updateNecessary'];
$versions = $systemInformation['versions'];

$version = $systemInformation['version'];
$patches = $systemInformation['patches'];
$completed = $systemInformation['completed'];
$uIds = $systemInformation['id'];

?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p>Source Version: <span class="fw-bold"><?= $versions['source'] ?></span></p>
                        <p>System tables Version: <span class="fw-bold"><?= $versions['db'] ?></span></p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="<?=rtrim($umgvar['url'],'/')?>/" class="btn btn-outline-primary">Back to Limbas</a>
                    </div>
                </div>

                <?php if($updateNecessary): ?>
                    <div class="alert alert-danger">
                        The database version differs from the source version! You need to run the updates.
                    </div>
                <?php elseif(!$completed['current']): ?>
                    <div class="alert alert-warning">
                        Not all patches run successfully. Please check them manually.
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        Nothing to do ... system tables are synchronous to source!
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <?php if(empty($clientId)): ?>
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <p>Installed Version: <span class="fw-bold"><?= $versions['source'] ?></span></p>
    
                    <?php if($latestVersion !== false && $latestVersion !== true): ?>
                        <p>Available Version: <span class="fw-bold"><?= $latestVersion ?></span></p>
    
                        <div class="alert alert-warning mb-3">
                            A newer version is available. Please follow the steps below.
                        </div>
                    <?php elseif($latestVersion !== true && !$umgvar['update_check']): ?>

                        <div class="alert alert-warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Automatic update checks are disabled. You have to check it manually:</div>
                                <a href="main_admin.php?action=setup_update&updatecheck=1" class="btn btn-outline-secondary">Search for updates</a>
                            </div>
                        </div>
                    <?php else: ?>

                        <p>Available Version: <span class="fw-bold"><?= $versions['source'] ?></span></p>

                        <div class="alert alert-success mb-3">
                            Great, you are using the latest version!
                        </div>
                    <?php endif; ?>
    
                    <?php if(!$umgvar['update_check']): ?>
    
                        
    
                    <?php endif; ?>
    
    
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if($latestVersion !== false && $latestVersion !== true): ?>

    <div class="alert alert-warning mb-3" role="alert">
        <p class="fw-bold">Steps to update your system:</p>
        <ul>
            <li>download latest "LIMBAS source"</li>
            <li>backup your system! (including a copy of old "limbas_src", "public/assets" and "vendor" directories)</li>
            <li>replace old "limbas_src" directory with its newest version.</li>
            <li>replace the "public/assets" directory with its newest version.</li>
            <li>replace the "vendor" directory with its newest version.</li>
            <li>login to limbas</li>
            <li>limbas will redirect you to the "system update page" (this page)</li>
            <li>click the "do update" button.</li>
            <li>reset your system</li>
        </ul>
    </div>

<?php endif; ?>

<div class="row">

    <?php foreach(['current','previous'] as $versionName): ?>

        <?php if($version[$versionName] !== false): ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h2 class="card-title">Version <?= $version[$versionName] ?> <i class="fas fa<?= $completed[$versionName] ? '-check-circle text-success" title="All patches successful' : '-times-circle text-danger" title="Some patches failed.' ?>"></i></h2>
                            <?php if(!$completed[$versionName] && !(!$completed['previous'] && $versionName === 'current')): ?>
                                <button class="btn btn-outline-<?= $updateNecessary && !$completed[$versionName] ? 'danger' : 'warning' ?> btn-run-all" type="button" data-version="<?=$uIds[$versionName]?>" data-client="<?= $clientId ?>">Run all updates</button>
                            <?php endif; ?>
                        </div>
                        
                        <?php if($completed['previous'] || $versionName !== 'current') : ?>
                        <table class="table table-sm table-striped table-hover mb-0">
                            <thead>
                            <tr>
                                <th>Patch</th>
                                <th>Description</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($patches[$versionName] as $patchNr => $patch): ?>
                            
                                <?php $ident = $patch['uid'] . $patchNr . '-' . $clientId ?>
                            
                                <tr>
                                    <td><?= $patchNr ?></td>
                                    <td><?= $patch['desc'] ?></td>
                                    <td>
                                        <i id="patch<?=$ident?>success" class="fas fa-check-circle text-success <?= $patch['status'] ? '' : 'd-none' ?>" data-bs-trigger="hover" data-bs-toggle="popover" data-bs-content="Successfully applied."></i>
                                        <i id="patch<?=$ident?>loading" class="fas fa-spinner fa-spin text-muted d-none"></i>
                                        <?php if(!$patch['status']): ?>
                                            <div id="patch<?=$ident?>actions" data-pending="<?=$patchNr?>" data-version="<?=$patch['uid']?>" data-client="<?= $clientId ?>">

                                                <i class="fas fa-times-circle text-danger me-2 <?=$patch['status'] !== false ? 'd-none' : ''?>" id="patch<?=$ident?>error-content" data-bs-trigger="hover" data-bs-toggle="popover" title="Patch with error" data-bs-content=" <?=htmlspecialchars($patch['error'])?>"></i>

                                                <i class="fas fa-info-circle text-warning me-2 <?=$patch['status'] === false ? 'd-none' : ''?>" id="patch<?=$ident?>new"  data-bs-trigger="hover" data-bs-toggle="popover" data-bs-content="New patch"></i>

                                                <i class="fas fa-rotate text-muted cursor-pointer me-2" title="run patch" data-update="<?=$patch['uid']?>" data-run="<?=$patchNr?>" data-client="<?= $clientId ?>"></i>
                                                <i class="fas fa-check cursor-pointer" title="mark as done" data-update="<?=$patch['uid']?>" data-done="<?=$patchNr?>" data-client="<?= $clientId ?>"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                Please run all updates of previous version first.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

</div>
