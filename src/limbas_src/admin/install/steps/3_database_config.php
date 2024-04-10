<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/* --- database config --- 
 * 
 * fills
 *      radio_odbc_pdo
 *      radio_odbc_driver
 *      radio_odbc_resource
 *      db_vendor
 *      setup_host
 *      setup_database
 *      setup_dbuser
 *      setup_dbpass
 *      setup_dbschema
 *      setup_dbport
 *      setup_dbdriver
 *      setup_encoding
 *  
 */

if (!defined('LIMBAS_INSTALL')) {
    return;
}

# if button check is pressed: check the db connection
$db_test = false;
$dbConOk = true;
if ($_POST['db_test'] == 'validate') {

    $testResult = testDatabase();

    $dbConOk = true;
    if (empty($testResult)) {
        $dbConOk = false;
        $db_test = false;
    } else {
        $dbConOk = true;
        $db_test = $testResult['valid'] ? 'valid' : false;

        $msg = $testResult['msg'];
        $msic = $testResult['msic'];
    }
}


if (!$_POST['radio_odbc']) {
    $radio_odbc = 'pdo';
} else {
    $radio_odbc = $_POST['radio_odbc'];
}

$vendor['name'] = array(
    'PostgreSQL',
    'mysql',
    'MaxDB V7.6 / V7.9',
    'MSSQL',
    'Sybase',
    'HANA',
    'Ingres 10',
    'oracle'
);
$vendor['value'] = array(
    'postgres',
    'mysql',
    'maxdb76',
    'mssql',
    'mssql',
    'hana',
    'ingres',
    'oracle'
);
$vendor['pdo'] = array(
    1,
    1,
);

$db_vendor = $_POST['db_vendor'];


?>

<script type="text/javascript">


    function clear_values(driver) {
        document.getElementById("db_schema").value = '';
        if (driver && !$('#radio_odbc_pdo').is(':checked')) {
            document.getElementById("db_driver").value = '';
        }
    }


    function update_dbvendor() {
        var db_vendor = document.getElementById("db_vendor");
        var db_schema = document.getElementById("db_schema");
        var db_driver = document.getElementById("db_driver");
        var db_name = document.getElementById("db_name");
        var db_user = document.getElementById("db_user");

        if (db_vendor.value === 'postgres') {
            if (!db_schema.value) {
                db_schema.value = "public";
            }
            if (!db_driver.value || db_driver.value == 'PDO') {
                db_driver.value = "PSQL";
            }
            $('#odbcinst').html('\
                [PSQL]<br>\
                Description = PostgreSQL<br>\
                Driver64 = /usr/lib64/posqlodbcw.so\
            ');
        } else if (db_vendor.value === 'ingres') {
            if (!db_schema.value) {
                db_schema.value = "ingres";
            }
            if (!db_driver.value || db_driver.value == 'PDO') {
                db_driver.value = "IngresSQL";
            }
            $('#odbcinst').html('');
        } else if (db_vendor.value === 'mysql') {
            if (!db_schema.value) {
                db_schema.value = db_name.value;
            }
            if (!db_driver.value || db_driver.value == 'PDO') {
                db_driver.value = "MySQL";
            }
            $('#odbcinst').html('\
                [MySQL]<br>\
                Description = ODBC for MySQL<br>\
                Driver64 = /usr/lib64/libmyodbc5.so<br>\
                Setup64 = /usr/lib64/libodbcmyS.so\
            ');
        } else if (db_vendor.value === 'mssql') {
            if (!db_schema.value) {
                db_schema.value = "dbo";
            }
            if (!db_driver.value || db_driver.value == 'PDO') {
                db_driver.value = "MSSQL";
            }
            $('#odbcinst').html('');
        } else if (db_vendor.value === 'maxdb76') {
            if (!db_schema.value) {
                db_schema.value = db_user.value;
            }
            if (!db_driver.value || db_driver.value == 'PDO') {
                db_driver.value = "MAXDBSQL";
            }
            $('#odbcinst').html('\
                [MAXDBSQL]<br>\
                Description = ODBC for MaxDB<br>\
                Driver = /opt/sdb/interfaces/odbc/lib79/libsdbodbc.so\
            ');
        }
        if ($('#radio_odbc_pdo').is(':checked')) {
            document.getElementById("db_driver").value = 'PDO';
        }

        updateOdbcIni();
    }


    function onInstallTypeChange() {
        if ($('#radio_odbc_driver').is(':checked')) {
            // resource name -> database name
            $('input[name="setup_database"]').parent().parent().children().first().html('Database Name:');

            // remove driver
            //$('#db_driver').val('');

            // write correct driver
            update_dbvendor();

            $('.showIfPDO').hide();
            $('.hideIfResource').show();
            $('.showIfResource').hide();
            $('.showIfODBC').show();
            $('.hideIfPDO').show();
            $('#db_driver').attr('readonly', false);

            $('.support_pdo').show();
            $('.notsupport_pdo').show();

        } else if ($('#radio_odbc_resource').is(':checked')) {
            // database name -> resource name
            $('input[name="setup_database"]').parent().parent().children().first().html('Resource Name:');

            // remove driver
            $('#db_driver').val('');

            $('.showIfPDO').hide();
            $('.hideIfResource').hide();
            $('.showIfResource').show();
            $('.showIfODBC').show();

            $('.support_pdo').show();
            $('.notsupport_pdo').show();

        } else if ($('#radio_odbc_pdo').is(':checked')) {
            // resource name -> database name
            $('input[name="setup_database"]').parent().parent().children().first().html('Database Name:');
            // remove driver
            $('#db_driver').val('PDO');
            $('#db_driver').attr('readonly', true);

            $('.showIfPDO').show();
            $('.showIfODBC').hide();
            $('.hideIfPDO').hide();

            if ($('#db_vendor').val() != 'mysql' && $('#db_vendor').val() != 'postgres') {
                $('#db_vendor').val('');
            }

            $('.support_pdo').show();
            $('.notsupport_pdo').hide();

        }

        updateOdbcIni();
    }

    function updateOdbcIni() {
        var code = '[<b>' + $('#db_name').val() + '</b>]<br>\
                    Description = ' + $('#db_driver').val() + '<br>\
                    Driver = <b>' + $('#db_driver').val() + '</b><br>\
                    Trace = No<br>\
                    TraceFile =<br>\
                    Database = <b>%limbasdb%</b><br>\
                    Servername = <b>%limbasserver%</b><br>\
                    ' + ($('#db_user').val() === '' ? 'Username = <b>%limbasuser%</b><br>' : '') + '\
                    ' + ($('[name="setup_dbpass"]').val() === '' ? 'Password = <b>%limbaspw%</b><br>' : '') + '\
                    Port = <b>%limbasport%</b><br>\
                    Protocol = 6.4<br>\
                    ReadOnly = No<br>\
                    RowVersioning = No<br>\
                    ShowSystemTables = No<br>\
                    ShowOidColumn = No<br>\
                    FakeOidIndex = No<br>\
                    ConnSettings =';

        $('#odbcini').html(code);
    }

    $(function () {
        // hide/show correct inputs depending on selected radio button
        onInstallTypeChange();

        // update example odbc.ini on input change
        $('input').change(updateOdbcIni);
    });
</script>


<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th>
            <?= lang('Select how you want to connect to the database') ?>
            <a href="http://www.limbas.org/wiki/Datenbank" target="new"><i class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <?php if (extension_loaded('pdo')) { ?>
        <tr>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radio_odbc" value="pdo" id="radio_odbc_pdo"
                           onchange="onInstallTypeChange();" <?= $radio_odbc == "pdo" ? "checked" : "" ?> <?php if ($db_test == 'valid') {
                        echo 'readonly';
                    } ?>>
                    <label class="form-check-label" for="radio_odbc_pdo">
                        <?= lang('Connect using PDO') ?>
                    </label>
                </div>
            </td>
        </tr>
    <?php } ?>
    <?php if (extension_loaded('odbc')) { ?>
        <tr>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radio_odbc" value="driver" id="radio_odbc_driver"
                           onchange="onInstallTypeChange();" <?= $radio_odbc == 'driver' ? "checked" : "" ?> <?php if ($db_test == 'valid') {
                        echo 'readonly';
                    } ?>>
                    <label class="form-check-label" for="radio_odbc_driver">
                        <?= lang('Connect using the ODBC-driver') ?>
                    </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="radio_odbc" value="resource"
                           id="radio_odbc_resource"
                           onchange="onInstallTypeChange();" <?= $radio_odbc == "resource" ? "checked" : "" ?> <?php if ($db_test == 'valid') {
                        echo 'readonly';
                    } ?>>
                    <label class="form-check-label" for="radio_odbc_resource">
                        <?= lang('Connect using a pre-specified ODBC-resource') ?>
                    </label>
                </div>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>


<table class="table table-sm mb-3 table-striped bg-contrast border">
    <thead>
    <tr>
        <th colspan="2">
            <?= lang('Database settings') ?>
            <a href="http://www.limbas.org/wiki/Datenbank" target="new"><i class="lmb-icon lmb-help"></i></a>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= lang('Database Vendor') ?>:</td>
        <td>
            <select class="form-select" id="db_vendor" name="db_vendor"
                    onchange="clear_values(1);update_dbvendor();" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>>
                <option></option>
                <?php
                echo "</optgroup><optgroup label=\"---stable---\">";
                foreach ($vendor['name'] as $key => $value) {
                    if ($vendor['pdo'][$key]) {
                        $style = 'class="support_pdo"';
                    } else {
                        $style = 'style="display:none" class="notsupport_pdo"';
                    }

                    if ($db_vendor == $vendor['value'][$key]) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    if ($vendor['name'][$key] == 'Sybase') {
                        echo "</optgroup><optgroup $style label=\"---beta---\">";
                    }
                    echo "<option value=\"" . $vendor['value'][$key] . "\" $style $selected>" . $vendor['name'][$key] . "</option>";
                }
                echo "</optgroup>";
                ?>
            </select>
        </td>
    </tr>
    <tr class="hideIfResource showIfPDO">
        <td><?= lang('Database Host') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off"
                   value="<?= $_POST['setup_host'] ?: 'localhost' ?>"
                   name="setup_host" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr>
        <td><?= lang('Database Name') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off"
                   value="<?= $_POST['setup_database'] ?: 'limbas' ?>" name="setup_database" id="db_name"
                   onchange="clear_values();update_dbvendor()" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr>
        <td><?= lang('Database User') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $_POST['setup_dbuser'] ?>"
                   name="setup_dbuser" id="db_user"
                   onchange="clear_values();update_dbvendor()" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr>
        <td><?= lang('Database Password') ?>:</td>
        <td><input type="password" class="form-control input-sm " autocomplete="off"
                   value="<?= $_POST['setup_dbpass'] ?>"
                   name="setup_dbpass" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr>
        <td><?= lang('Database Schema') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbschema"
                   value="<?= $_POST['setup_dbschema'] ?>" id="db_schema" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr class="hideIfResource showIfPDO">
        <td><?= lang('Database Port') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbport"
                   value="<?= $_POST['setup_dbport'] ?>" id="db_port" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
    <tr class="hideIfResource showIfPDO">
        <td><?= lang('SQL Driver') ?>:</td>
        <td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbdriver"
                   id="db_driver" value="<?= $_POST['setup_dbdriver'] ?>" <?php if ($db_test == 'valid') {
                echo 'readonly';
            } ?>></td>
    </tr>
        <input type="hidden" name="db_version" value="<?= $_POST['db_version'] ?>">
    </tbody>
</table>


<?php
if ($db_test) {
    # display results of db test
    ?>

    <?php if ($dbConOk): ?>
        <table class="table table-sm mb-3 table-striped bg-contrast border">
            <thead>
            <tr>
                <th colspan="3">
                    <?= lang('Database test') ?>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php if ($msic['db_conf']) { ?>
                <tr><?= Msg::icon($msic['db_conf']); ?>
                <td>configuration</td>
                <td><?= $msg['db_conf']; ?></td></tr><?php } ?>
            <tr><?= Msg::icon($msic['db_create']); ?>
                <td>create table</td>
                <td><?= $msg['db_create'] ?></td>
            </tr>
            <tr><?= Msg::icon($msic['db_insert']); ?>
                <td>insert</td>
                <td><?= $msg['db_insert'] ?></td>
            </tr>
            <tr><?= Msg::icon($msic['db_select']); ?>
                <td>select</td>
                <td><?= $msg['db_select'] ?></td>
            </tr>
            <tr><?= Msg::icon($msic['db_delete']); ?>
                <td>delete</td>
                <td><?= $msg['db_delete'] ?></td>
            </tr>
            <tr><?= Msg::icon($msic['db_drop']); ?>
                <td>drop table</td>
                <td><?= $msg['db_drop'] ?></td>
            </tr>
            <?php if ($radio_odbc != 'pdo') { ?>
                <tr><?= Msg::icon($msic['db_cursor']); ?>
                <td>cursor support (only odbc)</td>
                <td><?= $msg['db_cursor'] ?></td></tr><?php } ?>
            <tr><?= Msg::icon($msic['db_encoding']); ?>
                <td>encoding support</td>
                <td><?= $msg['db_encoding'] ?></td>
            </tr>
            </tbody>
        </table>
    <?php endif; ?>


    <table class="table table-sm mb-3 table-striped bg-contrast border">
        <thead>
        <tr>
            <th colspan="3">
                <?= lang('Information') ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($msic['db_cursor'] > 1) {
            ?>
            <tr>
                <td>
                    <i><?= lang('LIMBAS need ODBC cursor support for ODBC connections. In some cases versions of mysql, mariadb, php or combinations of them does not support cursors. You can try to adjust the connection string in funtion <b>dbq_0</b> in file <b>/lib/db/db_mysql.lib</b>') ?></i>
                </td>
            </tr>
            <?php
        }
        # Information
        if ($db_vendor == 'postgres') {
            ?>
            <tr>
                <td>
                    <i><?= lang('you can improve performance by setting <b>syncronous_commit off</b> in postgresql.conf. Be aware and read') ?>
                        <a href='https://www.postgresql.org/docs/9.1/static/runtime-config-wal.html'><?= lang('postgresql documentation') ?></a>. <?= lang('You can set it to <b>off</b> for installation and set it to <b>on</b> after installation.') ?>
                    </i></td>
            </tr>
            <tr>
                <td>
                    <i><?= lang('you can adjust security in <b>pg_hba.conf</b>. <u>Trust</u> meens you trust all local users. <u>password</u> meens you need user and password to connect. For more information read') ?>
                        <a href='https://www.postgresql.org/docs/9.1/static/auth-pg-hba-conf.html'><?= lang('postgresql documentation') ?></a>.</i>
                </td>
            </tr>
            <tr>
                <td>
                    <i><?= lang('do not forget to create your cluster or database with <b>initdb --locale=C</b>. Otherwise ist results in a wrong dateformat! Read') ?>
                        <a href="http://en.limbas.org/wiki/PostgreSQL#Database_Cluster_Initialization"><?= lang('limbas documentation') ?></a></i>
                </td>
            </tr>
            <tr>
                <td>
                    <i><?= lang('If you want to use <b>UTF-8</b> you have to create the database with <b>WITH ENCODING \'UTF8\'</b>. Read') ?>
                        <a href="http://en.limbas.org/wiki/UTF8_support"><?= lang('limbas documentation') ?></a></i>
                </td>
            </tr>

            <?php
        } else if ($db_vendor == 'mysql') {
            ?>
            <tr>
                <td>
                    <i><?= lang('do not forget to configure mysql with <b>lower_case_table_names = 1</b> in /etc/my.cnf. Otherwise ist results in installation error! Read') ?>
                        <a href="http://www.limbas.org/wiki/MySQL"><?= lang('limbas documentation') ?></a></i></td>
            </tr>
            <tr>
                <td>
                    <i><?= lang('you can improve performance by using <b>MYISAM</b> instead of <b>InnoDB</b> but be aware of losing transactions and foreign keys. Be aware and read') ?>
                        <a href='http://www.limbas.org/wiki/MySQL'><?= lang('limbas documentation') ?></a></i></td>
            </tr>
            <tr>
                <td>
                    <i><?= lang('If you want to use <b>UTF-8</b> you have to configure mysql with <b>default-character-set=utf8</b>. <u>If not needed</u> it will better to use <b>default-character-set=latin1</b>. Read') ?>
                        <a href="https://dev.mysql.com/doc/refman/5.7/en/charset-applications.html"><?= lang('mysql documentation') ?></a></i>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    <?php

}

if ($db_test != 'valid') {
    ?>

    <table class="table table-sm mb-3 table-striped bg-contrast border showIfODBC">
        <thead>
        <tr>
            <th class="showIfResource" style="display: none;">
                <?= lang('Example') ?> odbc.ini
            </th>
            <th>
                <?= lang('Example') ?> odbcinst.ini
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="showIfResource" style="display: none;">
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
    <div class="alert alert-info showIfPDO"
         role="alert"><?= lang('You can use <b>pdo_pgsql</b> or <b>pdo_mysql</b> with PDO. For other databases please use <b>ODBC</b>.') ?></div>

<?php } ?>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <button type="button" class="btn btn-outline-dark"
                        onclick="switchToStep('<?= $stepKeys[$currentStep - 1] ?>')"><?= lang('Back') ?></button>
            </div>
            <div class="col-6 text-end">
                <?php if ($db_test && $db_test == 'valid') { ?>
                    <button type="submit" class="btn btn btn-primary" name="install"
                            value="<?= $stepKeys[$currentStep + 1] ?>"><?= lang('Next step') ?></button>
                <?php } else { ?>
                    <input type="hidden" name="db_test" value="validate">
                    <button type="submit" class="btn btn btn-primary" name="install"
                            value="<?= $stepKeys[$currentStep] ?>"><?= lang('Check') ?></button>
                <?php } ?>
            </div>

        </div>
    </div>
</div>

<div>


</div>
