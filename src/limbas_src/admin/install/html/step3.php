<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="post" action="?lang=<?=e(LANG)?>">
            <input type="hidden" name="install" value="1">
            <div class="card mb-3">
                <div class="card-body">

                    <h1 class="text-center mb-4">Limbas <?=iLang('installation')?></h1>

                    <h2 class="mb-3"><?= iLang('Information') ?></h2>
                    <p><?=iLang('Please enter the following information. Don\'t worry, you can change all these settings again later.')?></p>

                    <div class="row mb-3">
                        <label for="language" class="col-md-4 col-form-label"><?= iLang('Language') ?></label>
                        <div class="col-md-8">
                            <select class="form-select" name="language" id="language" required>
                                <option value="1" <?= $language === 1 ? 'selected' : '' ?>>deutsch</option>
                                <option value="2" <?= $language === 2 || ($language !== 1 && LANG !== 'de') ? 'selected' : '' ?>>english</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="dateformat" class="col-md-4 col-form-label"><?= iLang('Dateformat') ?></label>
                        <div class="col-md-8">
                            <select class="form-select" name="dateformat" id="dateformat" required>
                                <option value="1" <?= $dateformat === 1 ? 'selected' : '' ?>>deutsch (dd-mm-yyyy)
                                </option>
                                <option value="2" <?= $dateformat === 2 ? 'selected' : '' ?>>english (yyyy-mm-dd)
                                </option>
                                <option value="3" <?= $dateformat === 3 ? 'selected' : '' ?>>us (mm-dd-yy)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="company" class="col-md-4 col-form-label"><?= iLang('Company') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm" name="company" id="company" autocomplete="off"
                                   value="<?= e($company ?: 'your company') ?>"  maxlength="50" required>
                        </div>
                    </div>
                    
                    <hr>


                    <div class="row mb-3">
                        <label for="username" class="col-md-4 col-form-label"><?= iLang('Username') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm" name="username" id="username" autocomplete="off"
                                   value="<?= e($username ?: 'admin') ?>"  maxlength="50" required>
                            <div class="form-text">
                                <?=iLang('User names may only contain alphanumeric characters, spaces, underscores, hyphens and dots.')?>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="password" class="col-md-4 col-form-label"><?= iLang('Password') ?></label>
                        <div class="col-md-8">
                            <input type="password" class="form-control input-sm " autocomplete="off"
                                   name="password" id="password" required>
                            <div class="form-text">
                                <?=iLang('Important: You will need this password to log in. Please keep it in a safe place.')?>
                            </div>
                        </div>
                    </div>


                    <h2 class="mb-3"><?= iLang('Select a data package') ?></h2>


                    <?php if(empty($packages) && !$hasClean): ?>
                        <div class="alert alert-danger" role="alert"><?=iLang('No installation archive found! Please place at least one under:') . '<br>' . BACKUPPATH?></div>
                    <?php else: ?>
                        <div class="row mb-3">
                            <label for="package-clean" class="col-md-4 col-form-label"><?= iLang('Empty') ?></label>
                            <div class="col-md-8">
                                <?php if($hasClean): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="package" id="package-clean" checked value="clean.tar.gz">
                                        <label class="form-check-label" for="package-clean">
                                            <?=iLang('Install limbas without example values (ready for your application)')?>
                                        </label>
                                    </div>
                                    <div class="form-text">
                                        <?=iLang('This is recommended if you want to start a new project.')?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning" role="alert"><?=iLang('No clean install archive found! You must install one of the following example values:')?></div>
                                <?php endif; ?>


                            </div>
                        </div>

                        <?php if(!empty($packages)): ?>
                            <hr>
                        <div class="row mb-3">
                            <div class="col-md-4 col-form-label"><?= iLang('Filled') ?></div>
                            <div class="col-md-8">
                                <?php foreach($packages as $key => $package): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="package" id="package-<?=e($key)?>" value="<?=e($package)?>">
                                        <label class="form-check-label" for="package-<?=e($key)?>">
                                            <?=e($package)?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    

                </div>
                <div class="card-footer text-center">
                    <?php if(empty($packages) && !$hasClean): ?>
                        <a class="btn btn-warning" href="?lang=<?=e(LANG)?>"><?=iLang('Reload')?></a>
                    <?php else: ?>
                        <button class="btn btn-primary" type="submit"><?=iLang('Install Limbas')?></button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

