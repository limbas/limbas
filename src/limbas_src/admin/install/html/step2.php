<?php use Limbas\admin\install\InstallMessage; ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <form method="post" action="?lang=<?=e(LANG)?>&step=2">
            <input type="hidden" name="validate" value="1">
        <div class="card mb-3">
            <div class="card-body">

                <h1 class="text-center mb-4"><?=lang('Database configuration')?></h1>


                <?php if($status === InstallMessage::OK): ?>
                    <div class="alert alert-success mb-3">
                        <p><?= lang('Database connection successful') ?>!</p>
                        <p class="mb-0"><?=lang('You can now continue with the installation of Limbas.')?></p>
                    </div>
                    <?php if(isset($databaseStatus) && $databaseStatus === 2): ?>
                        <div class="alert alert-warning mb-3">
                            <p><?= lang('Limbas is already installed on this database.') ?></p>
                            <p><?=lang('If you want to install a new Limbas, you must either use an empty database or empty the existing one.')?></p>
                            <p class="mb-0"><?=lang('To update the existing database, simply continue.')?></p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p><?=lang('Below you should enter your database connection details. If you are not sure about these, contact your host.')?></p>

                    <h2 class="mb-3"><?= lang('Select how you want to connect to the database') ?></h2>
                    <div class="mb-4">
                        <?php if (extension_loaded('pdo')): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="connection" value="pdo" id="radio_pdo" <?= $connectionMethod === 'pdo' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="radio_pdo">
                                    <?= lang('Connect using PDO') ?>
                                </label>
                            </div>
                        <?php endif; ?>
                        <?php if (extension_loaded('odbc')): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="connection" value="driver" id="radio_odbc_driver" <?= $connectionMethod === 'driver' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="radio_odbc_driver">
                                    <?= lang('Connect using the ODBC-driver') ?>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="connection" value="resource"
                                       id="radio_odbc_resource" <?= $connectionMethod === 'resource' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="radio_odbc_resource">
                                    <?= lang('Connect using a pre-specified ODBC-resource') ?>
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>


                    <h2 class="mb-3"><?= lang('Database settings') ?></h2>

                    <div class="row mb-3">
                        <label for="vendor" class="col-md-4 col-form-label"><?= lang('Database type') ?></label>
                        <div class="col-md-8">
                            <select class="form-select" id="vendor" name="vendor" required>
                                <option></option>

                                <?php foreach ($dbVendors as $dbVendorKey => $dbVendorParams) : ?>
                                    <option value="<?=e($dbVendorKey)?>" <?=$dbVendorKey === $dbVendor ? 'selected' : ''?>
                                            data-port="<?=e($dbVendorParams['port'])?>" data-schema="<?=e($dbVendorParams['schema'])?>"
                                        <?php if($dbVendorParams['pdo']): ?>
                                            class="support_pdo"
                                        <?php else: ?>
                                            class="notsupport_pdo" style="display:none"
                                        <?php endif; ?>
                                    >
                                        <?=e($dbVendorParams['name'])?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 hideIfResource">
                        <label for="host" class="col-md-4 col-form-label"><?= lang('Database host') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off"
                                   value="<?=e($dbHost)?>"
                                   name="host" id="host" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="name" class="col-md-4 col-form-label">
                            <span class="hideIfResource"><?= lang('Database name') ?></span>
                            <span class="hideIfPdo hideIfOdbc"><?= lang('Database resource') ?></span>
                        </label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off"
                                   value="<?=e($dbName)?>" name="name" id="name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="user" class="col-md-4 col-form-label"><?= lang('Database user') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off" value="<?=e($dbUser)?>"
                                   name="user" id="user" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label"><?= lang('Database password') ?></label>
                        <div class="col-md-8">
                            <input type="password" class="form-control input-sm " autocomplete="off"
                                   value="<?=e($dbPassword)?>"
                                   name="password" id="password" required>
                        </div>
                    </div>
                    <div class="row mb-3 hideIfResource">
                        <label for="port" class="col-md-4 col-form-label"><?= lang('Database port') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off" name="port"
                                   value="<?=e($dbPort)?>" id="port">
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <label for="schema" class="col-md-4 col-form-label"><?= lang('Database schema') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off" name="schema"
                                   value="<?=e($dbSchema)?>" id="schema">
                        </div>
                    </div>
                    <div class="row mb-3 hideIfResource hideIfPdo">
                        <label for="driver" class="col-md-4 col-form-label"><?= lang('SQL driver') ?></label>
                        <div class="col-md-8">
                            <input type="text" class="form-control input-sm " autocomplete="off" name="driver"
                                   id="driver" value="<?=e($dbDriver)?>">
                        </div>
                    </div>

                    <table class="table table-sm mb-3 table-striped bg-contrast border hideIfPdo">
                        <thead>
                        <tr>
                            <th>
                                <?= lang('Example') ?> odbc.ini
                            </th>
                            <th>
                                <?= lang('Example') ?> odbcinst.ini
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <code id="odbcini">

                                </code>
                            </td>
                            <td>
                                <code id="odbcinst">
                                </code>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                
                <?php endif; ?>
                

                <?php if($status === InstallMessage::ERROR && empty($messages)): ?>
                    <div class="alert alert-danger mb-0">
                        <?= lang('Database connection failed') ?>
                    </div>
                <?php elseif($status === InstallMessage::ERROR && !$configWritten): ?>
                    <div class="alert alert-danger mb-0">
                        <?= lang('Config file could not be written') ?>
                    </div>
                <?php elseif($status !== null): ?>

                    <div class="accordion">
                        <div class="accordion-item <?=$status === InstallMessage::ERROR ? 'border-danger' : '' ?>">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?=$status === InstallMessage::OK ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#systemInformation" aria-expanded="true" aria-controls="collapseOne">
                                    <?php if($status === InstallMessage::ERROR): ?>
                                        <i class="fas fa-circle-xmark text-danger fa-2x me-3"></i> <?=lang('Unfortunately, your server does not fulfil the requirements of Limbas.')?>
                                    <?php else: ?>
                                        <i class="fas fa-circle-check text-success fa-2x me-3"></i> <?=lang('Details')?>
                                    <?php endif; ?>
                                </button>
                            </h2>
                            <div id="systemInformation" class="accordion-collapse collapse <?=$status !== InstallMessage::OK ? 'show' : '' ?>">
                                <div class="accordion-body">

                                    <table class="table table-sm mb-3 table-striped bg-contrast border">
                                        <tbody>
                                        <?php
                                        /** @var InstallMessage $messages */
                                        foreach($messages as $message): ?>
                                            <tr>
                                                <td><?=$message->icon()?></td>
                                                <td><?=$message->name?></td>
                                                <td><?=$message->getMessage()?></td>
                                            </tr>

                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    
                                    <?php if($dbVendor === 'postgres'): ?>

                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <?= lang('you can improve performance by setting <b>syncronous_commit off</b> in postgresql.conf. Be aware and read') ?>
                                                <a href='https://www.postgresql.org/docs/9.1/static/runtime-config-wal.html'><?= lang('postgresql documentation') ?></a>. <?= lang('You can set it to <b>off</b> for installation and set it to <b>on</b> after installation.') ?>
                                            </li>
                                            <li class="list-group-item">
                                                <?= lang('you can adjust security in <b>pg_hba.conf</b>. <u>Trust</u> meens you trust all local users. <u>password</u> meens you need user and password to connect. For more information read') ?>
                                                <a href='https://www.postgresql.org/docs/9.1/static/auth-pg-hba-conf.html'><?= lang('postgresql documentation') ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <?= lang('do not forget to create your cluster or database with <b>initdb --locale=C</b>. Otherwise ist results in a wrong dateformat! Read') ?>
                                                <a href="http://en.limbas.org/wiki/PostgreSQL#Database_Cluster_Initialization"><?= lang('limbas documentation') ?>
                                            </li>
                                            <li class="list-group-item">
                                                <?= lang('Limbas support only <b>UTF-8</b> you have to create the database with <b>WITH ENCODING \'UTF8\'</b>. Read') ?>
                                                <a href="http://en.limbas.org/wiki/UTF8_support"><?= lang('limbas documentation') ?></a>
                                            </li>
                                        </ul>
                                    <?php elseif($dbVendor === 'mysql'): ?>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <?= lang('do not forget to configure mysql without <b>lower_case_table_names = 1</b> in /etc/my.cnf. Otherwise it results in installation error! Read') ?>
                                                <a href="http://www.limbas.org/wiki/MySQL"><?= lang('limbas documentation') ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <?= lang('you can improve performance by using <b>MYISAM</b> instead of <b>InnoDB</b> but be aware of losing transactions and foreign keys. Be aware and read') ?>
                                                <a href='http://www.limbas.org/wiki/MySQL'><?= lang('limbas documentation') ?>
                                            </li>
                                            <li class="list-group-item">
                                                <?= lang('Limbas support only <b>UTF-8</b> you have to configure mysql with <b>default-character-set=utf8</b>. <u>If not needed</u> it will better to use <b>default-character-set=latin1</b>. Read') ?>
                                                <a href="https://dev.mysql.com/doc/refman/5.7/en/charset-applications.html"><?= lang('mysql documentation') ?>
                                            </li>
                                        </ul>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                
                <?php endif; ?>
                
            </div>
            <div class="card-footer text-center">
                <?php if($status === null): ?>
                    <button class="btn btn-primary" type="submit"><?=lang('Send')?></button>
                <?php elseif($status === InstallMessage::OK && isset($databaseStatus) && $databaseStatus === 2): ?>
                    <a class="btn btn-primary" href="../"><?=lang('start Limbas now!')?></a>
                <?php elseif($status === InstallMessage::OK): ?>
                    <a class="btn btn-primary" href="?lang=<?=e(LANG)?>"><?=lang('Continue installation')?></a>
                <?php else: ?>
                    <button class="btn btn-primary" type="submit"><?=lang('Send again')?></button>
                <?php endif; ?>
            </div>
        </div>
        </form>
    </div>
</div>
