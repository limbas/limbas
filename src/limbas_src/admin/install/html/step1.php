<?php use Limbas\admin\install\InstallMessage; ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-body">

                <h1 class="text-center mb-4"><?=iLang('Welcome to')?> <span class="fw-bold">L<span style="color:#f18e00">I</span>MBAS</span> Version <?=e($version)?></h1>
                
                <p><?=iLang('Before getting started, you will need to know the following items.')?></p>

                <p><?=iLang('Database type')?><br>
                    <?=iLang('Database name')?><br>
                    <?=iLang('Database user')?><br>
                    <?=iLang('Database password')?><br>
                    <?=iLang('Database host')?></p>
                

                <p><?=iLang('You can probably find this information in your web hosting account. If you don\'t have it handy, contact the company hosting your website before proceeding.')?></p>

                <?php if(!$isDocker): ?>
                    <div class="accordion">
                        <div class="accordion-item <?=$serverStatus === InstallMessage::ERROR ? 'border-danger' : '' ?>">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#systemInformation" aria-expanded="true" aria-controls="collapseOne">
                                    <?php if($serverStatus === InstallMessage::ERROR): ?>
                                        <i class="fas fa-circle-xmark text-danger fa-2x me-3"></i> <?=iLang('Unfortunately, your server does not fulfil the requirements of Limbas.')?> (<?=iLang('Details')?>)
                                    <?php elseif($serverStatus === InstallMessage::WARN): ?>
                                        <i class="fas fa-circle-exclamation text-warning fa-2x me-3"></i> <?=iLang('Your server is not ideally configured, but you can continue.')?> (<?=iLang('Details')?>)
                                    <?php else: ?>
                                        <i class="fas fa-circle-check text-success fa-2x me-3"></i> <?=iLang('Your server fulfils all Limbas requirements.')?> (<?=iLang('Details')?>)
                                    <?php endif; ?>
                                </button>
                            </h2>
                            <div id="systemInformation" class="accordion-collapse collapse">
                                <div class="accordion-body">

                                    <?php require(__DIR__ . '/dependencies.php') ?>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                

            </div>
            <div class="card-footer text-center">
                <?php if($serverStatus === InstallMessage::ERROR): ?>
                    <a class="btn btn-warning" href="?lang=<?=e(LANG)?>"><?=iLang('Reload and check again')?></a>
                <?php else: ?>
                    <a class="btn btn-primary" href="?lang=<?=e(LANG)?>&step=2"><?=iLang('Start now')?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="alert alert-warning small">
            LIMBAS. Copyright &copy; 1998-<?= date('Y') ?> LIMBAS GmbH (info@limbas.com). LIMBAS is free
            software; You can redistribute it and/or modify it under the terms of the GPL General Public License
            V2 as published by the Free Software Foundation; Go to <a href="http://www.limbas.org/" title="LIMBAS Website" target="new">http://www.limbas.org/</a>
            for details. LIMBAS comes with ABSOLUTELY NO WARRANTY; Please note that some external scripts are
            copyright of their respective owners, and are released under different licences.
        </div>
    </div>
</div>
