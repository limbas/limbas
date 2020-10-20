<?php

# if button check is pressed: check the db connection
if($db_test == 'validate') {
    $DBA["DBSCHEMA"] = $setup_dbschema;
    $DBA["DBNAME"] = $setup_database;

    if(!$DBA['DB']){$DBA['DB'] = 'postgres';}

    if($radio_odbc == 'pdo'){
        require_once('../../lib/db/db_pdo.lib');
        $setup_dbdriver = 'PDO';
    }else{
        require_once('../../lib/db/db_odbc.lib');
    }

    require_once("../../lib/db/db_{$DBA['DB']}.lib");
    require_once("../../lib/db/db_".$DBA["DB"]."_admin.lib");
    require_once("../../lib/include.lib");
    require_once("../../lib/include_string.lib");

    /* --- get database Version --- */
    if($db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport)) {
        // database version
        $setup_version = dbf_version($DBA);
        $DBA['VERSION'] = $setup_version[1];
        lmbdb_close($db);
        $db = 1;
    }

    /* --- test database --- */
    if($db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport)) {
        lmb_StartTransaction();

        if($DBA['DB'] == 'mysql' AND $radio_odbc != 'pdo'){ // check for lower case table names
            $sqlquery1 = "SELECT CASE WHEN @@lower_case_table_names = 1 THEN 1 ELSE 2 END AS CSENSITIV";
            $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
            if ($rs1 AND lmbdb_result($rs1,'CSENSITIV') == 1) {
                $msg['db_conf'] = $msgOK;
                $msic['db_conf'] = "1";
            } else {
                $msg['db_conf'] = "<span style=\"color:red\">configure mysql with lower_case_table_names = 1 in /etc/my.cnf</span>";
                $msic['db_conf'] = "3";
                $commit = 1;
            }
        } elseif($DBA['DB'] == 'postgres'){
            #$sqlquery1 = "SHOW ALL";
            $sqlquery1 = "SHOW LC_CTYPE";
            $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
            $LC_CTYPE = lmbdb_result($rs1,'LC_CTYPE');
            $sqlquery2 = "SHOW SYNCHRONOUS_COMMIT";
            $rs2 = lmbdb_exec($db, $sqlquery2) or errorhandle(lmbdb_errormsg($db), $sqlquery2, $action, __FILE__, __LINE__);
            $SYNCHRONOUS_COMMIT = lmbdb_result($rs2,'SYNCHRONOUS_COMMIT');
            if ($rs1 AND $LC_CTYPE == 'C') {
                $msg['db_conf'] = $msgOK.'<br><i>(synchronous_commit = '.$SYNCHRONOUS_COMMIT.')</i>';
                $msic['db_conf'] = "1";
            } else {
                $msg['db_conf'] = "<span style=\"color:red\">wrong localisation! Create database cluster or database with <i><u>locale=C</u></i> or alternative create database with:<br><code>create database $setup_database WITH ENCODING '".$setup_version[2]."' <b>LC_COLLATE 'C' LC_CTYPE 'C'</b> OWNER $setup_dbuser TEMPLATE template0;</code></i>";
                $msic['db_conf'] = "3";
                $commit = 1;
            }
        }


        // drop test table
        if ($odbc_table) {
            $sqlquery1 = "DROP TABLE " . dbf_4("LIMBASTEST");
            $rs1 = lmbdb_exec($db, $sqlquery1) or errorhandle(lmbdb_errormsg($db), $sqlquery1, $action, __FILE__, __LINE__);
        }

        // create table
        $sqlquery = "CREATE TABLE " . dbf_4("LIMBASTEST") . " (ID " . LMB_DBTYPE_INTEGER . ",ERSTDATUM " . LMB_DBTYPE_TIMESTAMP . " DEFAULT " . LMB_DBDEF_TIMESTAMP . ",TXT ".LMB_DBTYPE_VARCHAR."(6))";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if ($rs) {
            $msg['db_create'] = $msgOK;
            $msic['db_create'] = "1";
        } else {
            $msg['db_create'] = $msgError;
            $msic['db_create'] = "3";
            $commit = 1;
        }

        // insert into
        if(stripos($setup_version[2],'UTF') !== false){
            $insertstring = 'a1ä2Ü3';
        }else{
            $insertstring = utf8_decode('a1ä2Ü3');
        }

        $sqlquery = "INSERT INTO LIMBASTEST (ID,TXT)  VALUES (1,'".$insertstring."')";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if ($rs) {
            $msg['db_insert'] = $msgOK;
            $msic['db_insert'] = "1";
        } else {
            $msg['db_insert'] = $msgError;
            $msic['db_insert'] = "3";
            $commit = 1;
        }
        // select
        $sqlquery = "SELECT * FROM LIMBASTEST";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if ($rs) {
            $msg['db_select'] = $msgOK;
            $msic['db_select'] = "1";
        } else {
            $msg['db_select'] = $msgError;
            $msic['db_select'] = "3";
            $commit = 1;
        }

        // check cursor
        if($radio_odbc != 'pdo') {
            $sqlquery = "SELECT * FROM LIMBASTEST";
            $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
            if (lmbdb_fetch_row($rs, 1)) {
                lmbdb_result($rs, "ID");
                if (lmbdb_fetch_row($rs, 1)) {
                    if (lmbdb_result($rs, "ID")) {
                        $cursor = 1;
                    } else {
                        $cursor = 0;
                    }
                }
            }
            if ($cursor OR $radio_odbc == 'pdo') {
                $msg['db_cursor'] = $msgOK;
                $msic['db_cursor'] = "1";
            } elseif ($radio_odbc == 'pdo') {
                $msg['db_cursor'] = $msgOK;
                $msic['db_cursor'] = "4";
                $commit = 0;
            } else {
                $msg['db_cursor'] = $msgError;
                $msic['db_cursor'] = "3";
                $commit = 1;
            }
        }

        // delete from
        $sqlquery = "DELETE FROM LIMBASTEST";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if ($rs) {
            $msg['db_delete'] = $msgOK;
            $msic['db_delete'] = "1";
        } else {
            $msg['db_delete'] = $msgError;
            $msic['db_delete'] = "3";
            $commit = 1;
        }
        // drop table
        $sqlquery = "DROP TABLE " . dbf_4("LIMBASTEST");
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if ($rs) {
            $msg['db_drop'] = $msgOK;
            $msic['db_drop'] = "1";
        } else {
            $msg['db_drop'] = $msgError;
            $msic['db_drop'] = "3";
            $commit = 1;
        }

        lmb_EndTransaction(!(isset($commit) && $commit));
        lmbdb_close($db);

        // check utf-8 encoding support
        if(stripos($setup_version[2],'UTF') !== false AND !function_exists("mb_strlen")){
            $msg['db_encoding'] = $msgError.'<br><i>to use UTF8 encoding you need to install php <b>mbstring</b> modul</i>';
            $msic['db_encoding'] = "3";
            $commit = 1;
        }else{
            $msg['db_encoding'] = $msgOK;
            $msic['db_encoding'] = "1";
        }

        # set as checked, if no error occured
        if (!$commit) {
            $db_test = 'valid';
        }
    }
}


if(!$radio_odbc){$radio_odbc = 'pdo';}

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




?>
            
<script type="text/javascript">


    function clear_values(driver){
        document.getElementById("db_schema").value='';
        if(driver && !$('#radio_odbc_pdo').is(':checked')){document.getElementById("db_driver").value='';}
    }


    function update_dbvendor(){
        var db_vendor = document.getElementById("db_vendor");
        var db_schema = document.getElementById("db_schema");
        var db_driver = document.getElementById("db_driver");
        var db_name = document.getElementById("db_name");
        var db_user = document.getElementById("db_user");

        if (db_vendor.value === 'postgres'){
            if(!db_schema.value){db_schema.value = "public";}
            if(!db_driver.value || db_driver.value == 'PDO'){db_driver.value = "PSQL";}
            $('#odbcinst').html('\
                [PSQL]<br>\
                Description = PostgreSQL<br>\
                Driver64 = /usr/lib64/posqlodbcw.so\
            ');
        }else if (db_vendor.value === 'ingres'){
            if(!db_schema.value){db_schema.value = "ingres";}
            if(!db_driver.value || db_driver.value == 'PDO'){db_driver.value = "IngresSQL";}
            $('#odbcinst').html('');
        }else if (db_vendor.value === 'mysql'){
            if(!db_schema.value){db_schema.value = db_name.value;}
            if(!db_driver.value || db_driver.value == 'PDO'){db_driver.value = "MySQL";}
            $('#odbcinst').html('\
                [MySQL]<br>\
                Description = ODBC for MySQL<br>\
                Driver64 = /usr/lib64/libmyodbc5.so<br>\
                Setup64 = /usr/lib64/libodbcmyS.so\
            ');
        }else if (db_vendor.value === 'mssql'){
            if(!db_schema.value){db_schema.value = "dbo";}
            if(!db_driver.value || db_driver.value == 'PDO'){db_driver.value = "MSSQL";}
            $('#odbcinst').html('');
        }else if (db_vendor.value === 'maxdb76'){
            if(!db_schema.value){db_schema.value = db_user.value;}
            if(!db_driver.value || db_driver.value == 'PDO'){db_driver.value = "MAXDBSQL";}
            $('#odbcinst').html('\
                [MAXDBSQL]<br>\
                Description = ODBC for MaxDB<br>\
                Driver = /opt/sdb/interfaces/odbc/lib79/libsdbodbc.so\
            ');
        }
        if($('#radio_odbc_pdo').is(':checked')){document.getElementById("db_driver").value='PDO';}
        
        updateOdbcIni();
    }


    function onInstallTypeChange() {
        if($('#radio_odbc_driver').is(':checked')) {
            // resource name -> database name
            $('input[name="setup_database"]').parent().parent().children().first().html('Database Name:');

            // remove driver
            $('#db_driver').val('');

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

        } else if($('#radio_odbc_resource').is(':checked')) {
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

        }else if($('#radio_odbc_pdo').is(':checked')) {
            // resource name -> database name
            $('input[name="setup_database"]').parent().parent().children().first().html('Database Name:');
            // remove driver
            $('#db_driver').val('PDO');
            $('#db_driver').attr('readonly', true);

            $('.showIfPDO').show();
            $('.showIfODBC').hide();
            $('.hideIfPDO').hide();

            if($('#db_vendor').val() != 'mysql' && $('#db_vendor').val() != 'postgres') {
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
    
    $(function() {
        // hide/show correct inputs depending on selected radio button
        onInstallTypeChange();
        
        // update example odbc.ini on input change
        $('input').change(updateOdbcIni);
    });
</script>


<table class="table table-condensed">  
    <thead>
        <tr>
            <th>
                Select how you want to connect to the database
                <a href="http://www.limbas.org/wiki/Datenbank" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if(extension_loaded('pdo')) { ?>
        <tr>
            <td>
                <div class="radio">
                    <label style="display:block;">
                        <input type="radio" name="radio_odbc" value="pdo" id="radio_odbc_pdo" onchange="onInstallTypeChange();" <?= $radio_odbc=="pdo" ? "checked" : "" ?> <?php if($db_test == 'valid') {echo 'readonly';}?>>Connect using PDO
                    </label>
                </div>
            </td>
        </tr>
    <?php } ?>
    <?php if(extension_loaded('odbc')){ ?>
        <tr>
            <td>
                <div class="radio">
                    <label style="display:block;">
                        <input type="radio" name="radio_odbc" value="driver" id="radio_odbc_driver" onchange="onInstallTypeChange();" <?= $radio_odbc=='driver' ? "checked" : "" ?> <?php if($db_test == 'valid') {echo 'readonly';}?>>Connect using the ODBC-driver
                    </label>
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div class="radio">
                    <label style="display:block;">
                        <input type="radio" name="radio_odbc" value="resource" id="radio_odbc_resource" onchange="onInstallTypeChange();" <?= $radio_odbc=="resource" ? "checked" : "" ?> <?php if($db_test == 'valid') {echo 'readonly';}?>>Connect using a pre-specified ODBC-resource
                    </label>
                </div>
            </td>
        </tr>
        <?php }?>
    </tbody>
</table>


<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="2">
                Database settings
                <a href="http://www.limbas.org/wiki/Datenbank" target="new"><i class="lmb-icon lmb-help"></i></a>
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td style="vertical-align: middle;">Database Vendor:</td>
            <td>
                <select class="form-control input-sm " id="db_vendor" name="DBA[DB]" onchange="clear_values(1);update_dbvendor();" <?php if($db_test == 'valid') {echo 'readonly';}?>><option></option>
                    <?php
                    echo "</optgroup><optgroup label=\"---stable---\">";
                    foreach ($vendor['name'] as $key => $value) {
                        if($vendor['pdo'][$key]){$style='class="support_pdo"';}else{$style='style="display:none" class="notsupport_pdo"';}

                        if($DBA['DB'] == $vendor['value'][$key]){$selected = 'selected';}else{$selected = '';}
                        if($vendor['name'][$key] == 'Sybase'){echo "</optgroup><optgroup $style label=\"---beta---\">";}
                        echo "<option value=\"".$vendor['value'][$key]."\" $style $selected>".$vendor['name'][$key]."</option>";
                    }
                    echo "</optgroup>";
                    ?>
                </select>
            </td>
        </tr>
        <tr class="hideIfResource showIfPDO"><td style="vertical-align: middle;">Database Host:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_host ? $setup_host : 'localhost' ?>" name="setup_host" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr><td style="vertical-align: middle;">Database Name:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_database ? $setup_database : 'limbas' ?>" name="setup_database" id="db_name" onchange="clear_values();update_dbvendor()" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr><td style="vertical-align: middle;">Database User:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_dbuser ?>" name="setup_dbuser" id="db_user" onchange="clear_values();update_dbvendor()" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr><td style="vertical-align: middle;">Database Password:</td><td><input type="password" class="form-control input-sm " autocomplete="off" value="<?= $setup_dbpass ?>" name="setup_dbpass" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr><td style="vertical-align: middle;">Database Schema:</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbschema" value="<?= $setup_dbschema ?>" id="db_schema" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr class="hideIfResource showIfPDO"><td style="vertical-align: middle;">Database Port:</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbport" value="<?= $setup_dbport?>" id="db_port" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <tr class="hideIfResource showIfPDO"><td style="vertical-align: middle;">SQL Driver:</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbdriver" id="db_driver" value="<?= $setup_dbdriver ?>" <?php if($db_test == 'valid') {echo 'readonly';}?>></td></tr>
        <?php if($setup_version[0]){
            $setup_version_ = $setup_version; unset($setup_version_[1]);
            echo "<tr class=\"hideIfResource\"><td style=\"vertical-align: middle;\">Database Version:</td><td><input type=\"text\" class=\"form-control input-sm \" disabled autocomplete=\"off\" name=\"_setup_version\" value=\"".implode(' - ',$setup_version_)."\"></td></tr>";
            echo "<tr class=\"hideIfResource\"><td style=\"vertical-align: middle;\">Database Encoding:</td><td><input type=\"text\" class=\"form-control input-sm \" readonly autocomplete=\"off\" name=\"setup_encoding\" value=\"".$setup_version[2]."\"></td></tr>";
        }?>
        <input type="hidden" name="DBA[VERSION]" value="<?=$DBA['VERSION']?>">
    </tbody>
</table>




<?php
if($db_test) {
    # display results of db test
    ?>

    <table class="table table-condensed">
        <thead>
            <tr>
                <th colspan="3">
                    Database test
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if($msic['db_conf']){?><tr><?= insIcon($msic['db_conf']); ?><td style="vertical-align: top;">configuration</td><td><?= $msg['db_conf']; ?></td></tr><?php }?>
            <tr><?= insIcon($msic['db_create']); ?><td style="vertical-align: top;">create table</td><td><?= $msg['db_create'] ?></td></tr>
            <tr><?= insIcon($msic['db_insert']); ?><td style="vertical-align: top;">insert</td><td><?= $msg['db_insert'] ?></td></tr>
            <tr><?= insIcon($msic['db_select']); ?><td style="vertical-align: top;">select</td><td><?= $msg['db_select'] ?></td></tr>
            <tr><?= insIcon($msic['db_delete']); ?><td style="vertical-align: top;">delete</td><td><?= $msg['db_delete'] ?></td></tr>
            <tr><?= insIcon($msic['db_drop']); ?><td style="vertical-align: top;">drop table</td><td><?= $msg['db_drop'] ?></td></tr>
            <?php if($radio_odbc != 'pdo'){?><tr><?= insIcon($msic['db_cursor']); ?><td style="vertical-align: top;">cursor support (only odbc)</td><td><?= $msg['db_cursor'] ?></td></tr><?php }?>
            <tr><?= insIcon($msic['db_encoding']); ?><td style="vertical-align: top;">encoding support</td><td><?= $msg['db_encoding'] ?></td></tr>
        </tbody>            
    </table>




    <table class="table table-condensed">
        <thead>
            <tr>
                <th colspan="3">
                    Information
                </th>
            </tr>
        </thead>
        <tbody>
    <?php
    if($msic['db_cursor'] > 1){
        ?>
        <tr><td><i>LIMBAS need ODBC cursor support for ODBC connections. In some cases versions of mysql, mariadb, php or combinations of them does not support cursors. You can try to adjust the connection string in funtion <b>dbq_0</b> in file <b>/lib/db/db_mysql.lib</b></i></td></tr>
        <?php
    }
    # Information
    if($DBA['DB'] == 'postgres'){
        ?>
            <tr><td><i>you can improve performance by setting <b>syncronous_commit off</b> in postgresql.conf. Be aware and read <a href='https://www.postgresql.org/docs/9.1/static/runtime-config-wal.html'>postgresql documentation</a>. You can set it to <b>off</b> for installation and set it to <b>on</b> after installation.</i></td></tr>
            <tr><td><i>you can adjust security in <b>pg_hba.conf</b>. <u>Trust</u> meens you trust all local users. <u>password</u> meens you need user and password to connect. For more information read <a href='https://www.postgresql.org/docs/9.1/static/auth-pg-hba-conf.html'>postgresql documentation.</a></i></td></tr>
			<tr><td><i>do not forget to create your cluster or database with <b>initdb --locale=C</b>. Otherwise ist results in a wrong dateformat! Read <a href="http://en.limbas.org/wiki/PostgreSQL#Database_Cluster_Initialization">limbas documentation</a></i></td></tr>
			<tr><td><i>If you want to use <b>UTF-8</b> you have to create the database with <b>WITH ENCODING 'UTF8'</b>. <u>If not needed</u> it will better to use <b>WITH ENCODING 'SQL_ASCII'</b>. Read <a href="http://en.limbas.org/wiki/UTF8_support">limbas documentation</a></i></td></tr>

        <?php
    }else if($DBA['DB'] == 'mysql'){
        ?>
        <tr><td><i>do not forget to configure mysql with <b>lower_case_table_names = 1</b> in /etc/my.cnf. Otherwise ist results in installation error! Read <a href="http://www.limbas.org/wiki/MySQL">limbas documentation</a></i></td></tr>
        <tr><td><i>you can improve performance by using <b>MYISAM</b> instead of <b>InnoDB</b> but be aware of losing transactions and foreign keys. Be aware and read <a href='http://www.limbas.org/wiki/MySQL'>limbas documentation</a></i></td></tr>
        <tr><td><i>If you want to use <b>UTF-8</b> you have to configure mysql with <b>default-character-set=utf8</b>. <u>If not needed</u> it will better to use <b>default-character-set=latin1</b>. Read <a href="https://dev.mysql.com/doc/refman/5.7/en/charset-applications.html">mysql documentation</a></i></td></tr>
        <?php
    }
    ?>
    </tbody>
    </table>
    <?php
    
}

if($db_test != 'valid'){
?>

<table class="table table-condensed showIfODBC">
    <thead>
        <tr>
            <th class="showIfResource" style="display: none;">
                Example odbc.ini
            </th>
            <th>
                Example odbcinst.ini
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
    <div class="alert alert-info showIfPDO" role="alert">You can use <b>pdo_pgsql</b> or <b>pdo_mysql</b> with PDO. For other databases please use <b>ODBC.</div>

<?php }?>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?= array_keys($steps)[2] ?>')">Back</button>
    <?php if($db_test && $db_test == 'valid') { ?>
        <button type="submit" class="btn btn-info pull-right" name="install" value="<?= array_keys($steps)[4] ?>">Next step</button>
    <?php } else { ?>
        <input type="hidden" name="db_test" value="validate">
        <button type="submit" class="btn btn-info pull-right" name="install" value="<?= array_keys($steps)[3] ?>">Check</button>
    <?php } ?>
</div>