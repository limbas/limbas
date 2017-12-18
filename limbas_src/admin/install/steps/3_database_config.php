<?php

# if button check is pressed: check the db connection
if($db_test == 'validate') {
    /* Check for unixODBC installation and bail out verbosely, if not installed */
    if (($DBA['DB']=="postgres" || $DBA['DB']=="ingres") && (!function_exists("odbc_pconnect"))){
        ?>
            <div class="alert alert-danger" role="alert">
                    <strong>Error: unixODBC not installed!</strong>
                    Please install unixODBC and <a href="<?=$_SERVER['PHP_SELF']?>">retry installation</a>.
            </div></body></html>
        <?php
        exit(1);
    }

    require_once("../../lib/db/db_{$DBA['DB']}.lib");
    require_once("../../lib/include.lib");

    /* --- test database --- */
    $db = dbq_0($setup_host,$setup_database,$setup_dbuser,$setup_dbpass,$setup_dbdriver,$setup_dbport);
    lmb_StartTransaction();

    // drop test table
    if($odbc_table){
            $sqlquery1 = "DROP TABLE ".dbf_4("LIMBASTEST");
            $rs1 = @odbc_exec($db,$sqlquery1) or errorhandle(odbc_errormsg($db),$sqlquery1,$action,__FILE__,__LINE__);
    }

    // create table
    $sqlquery = "CREATE TABLE ".dbf_4("LIMBASTEST")." (ID ".LMB_DBTYPE_INTEGER.",ERSTDATUM ".LMB_DBTYPE_TIMESTAMP." DEFAULT ".LMB_DBDEF_TIMESTAMP.")";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($rs){$msg['db_create'] = $msgOK;$msic[db_create] = "1";}else{$msg['db_create'] = $msgError;$msic[db_create] = "3";$commit = 1;}
    // insert into
    $sqlquery = "INSERT INTO LIMBASTEST (ID)  VALUES (1)";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($rs){$msg['db_insert'] = $msgOK;$msic[db_insert] = "1";}else{$msg['db_insert'] = $msgError;$msic[db_insert] = "3";$commit = 1;}
    // select
    $sqlquery = "SELECT * FROM LIMBASTEST";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($rs){$msg['db_select'] = $msgOK;$msic[db_select] = "1";}else{$msg['db_select'] = $msgError;$msic[db_select] = "3";$commit = 1;}
    // delete from
    $sqlquery = "DELETE FROM LIMBASTEST";
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($rs){$msg['db_delete'] = $msgOK;$msic[db_delete] = "1";}else{$msg['db_delete'] = $msgError;$msic[db_delete] = "3";$commit = 1;}
    // drop table
    $sqlquery = "DROP TABLE ".dbf_4("LIMBASTEST");
    $rs = odbc_exec($db,$sqlquery) or errorhandle(odbc_errormsg($db),$sqlquery,$action,__FILE__,__LINE__);
    if($rs){$msg['db_drop'] = $msgOK;$msic[db_drop] = "1";}else{$msg['db_drop'] = $msgError;$msic[db_drop] = "3";$commit = 1;}

    lmb_EndTransaction(!(isset($commit) && $commit));
    odbc_close($db);

    # set as checked, if no error occured
    if(!$commit) {
        $db_test = 'valid';
    }
}

# database vendors
$vendorNames = array(
    'PostgreSQL', 
    'mysql',
    'MaxDB V7.6',
    'MSSQL',
    'Ingres 10'
);
$vendorValues = array(
    'postgres', 
    'mysql',
    'maxdb76',
    'mssql',
    'ingres'
);

?>
            
<script type="text/javascript">


    function clear_values(driver){
        document.getElementById("db_schema").value='';
        if(driver){document.getElementById("db_driver").value='';}
    }


    function update_dbvendor(){
        var db_vendor = document.getElementById("db_vendor");
        var db_schema = document.getElementById("db_schema");
        var db_driver = document.getElementById("db_driver");
        var db_name = document.getElementById("db_name");
        var db_user = document.getElementById("db_user");

        if (db_vendor.value == 'postgres'){
            if(!db_schema.value){db_schema.value = "public";}
            if(!db_driver.value){db_driver.value = "PSQL";}
            $('#odbcinst').html('\
                [PSQL]<br>\
                Description = PostgreSQL<br>\
                Driver64 = /usr/lib64/posqlodbcw.so\
            ');
        }else if (db_vendor.value == 'ingres'){
            if(!db_schema.value){db_schema.value = "ingres";}
            if(!db_driver.value){db_driver.value = "IngresSQL";}
            $('#odbcinst').html('');
        }else if (db_vendor.value == 'mysql'){
            if(!db_schema.value){db_schema.value = db_name.value;}
            if(!db_driver.value){db_driver.value = "MySQL";}
            $('#odbcinst').html('\
                [MySQL]<br>\
                Description = ODBC for MySQL<br>\
                Driver64 = /usr/lib64/libmyodbc5.so<br>\
                Setup64 = /usr/lib64/libodbcmyS.so\
            ');
        }else if (db_vendor.value == 'mssql'){
            if(!db_schema.value){db_schema.value = "dbo";}
            if(!db_driver.value){db_driver.value = "MSSQL";}
            $('#odbcinst').html('');
        }else if (db_vendor.value == 'maxdb76'){
            if(!db_schema.value){db_schema.value = db_user.value;}
            if(!db_driver.value){db_driver.value = "MAXDBSQL";}
            $('#odbcinst').html('\
                [MAXDBSQL]<br>\
                Description = ODBC for MaxDB<br>\
                Driver = /opt/sdb/interfaces/odbc/lib79/libsdbodbc.so\
            ');
        }
        
        updateOdbcIni();
    }


    function onInstallTypeChange() {
        if($('#radio_odbc_driver').is(':checked')) {
            // resource name -> database name
            $('input[name="setup_database"]').parent().parent().children().first().html('Database Name:');
            
            // write correct driver
            update_dbvendor();
            
            $('.hideIfResource').show();
            $('.showIfResource').hide();
            
        } else if($('#radio_odbc_resource').is(':checked')) {
            // database name -> resource name
            $('input[name="setup_database"]').parent().parent().children().first().html('Resource Name:');    

            // remove driver
            $('#db_driver').val('');
            
            $('.hideIfResource').hide();
            $('.showIfResource').show();
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
        <tr>
            <td>
                <div class="radio">
                    <label style="display:block;">
                        <input type="radio" name="radio_odbc" value="driver" id="radio_odbc_driver" onchange="onInstallTypeChange();" <?= $radio_odbc && $radio_odbc=="resource" ? "" : "checked" ?>>Connect using the ODBC-driver
                    </label>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="radio">
                    <label style="display:block;">
                        <input type="radio" name="radio_odbc" value="resource" id="radio_odbc_resource" onchange="onInstallTypeChange();" <?= $radio_odbc=="resource" ? "checked" : "" ?>>Connect using a pre-specified ODBC-resource
                    </label>
                </div>
            </td>
        </tr>
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
                <select class="form-control input-sm " id="db_vendor" name="DBA[DB]" onchange="clear_values(1);update_dbvendor();"><option></option>
                    <?php 
                    foreach ($vendorNames as $key => $value) {
                        if($DBA['DB'] == $vendorValues[$key]){$selected = 'selected';}else{$selected = '';}
                        echo "<option value=\"".$vendorValues[$key]."\" $selected>".$vendorNames[$key]."</option>";
                    }
                    ?>

                </select>
            </td>
        </tr>          
        <tr class="hideIfResource"><td style="vertical-align: middle;">Database Host:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_host ? $setup_host : 'localhost' ?>" name="setup_host"></td></tr>
        <tr><td style="vertical-align: middle;">Database Name:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_database ? $setup_database : 'limbas' ?>" name="setup_database" id="db_name" onchange="clear_values();update_dbvendor()"></td></tr>
        <tr><td style="vertical-align: middle;">Database User:</td><td><input type="text" class="form-control input-sm " autocomplete="off" value="<?= $setup_dbuser ?>" name="setup_dbuser" id="db_user" onchange="clear_values();update_dbvendor()"></td></tr>
        <tr><td style="vertical-align: middle;">Database Password:</td><td><input type="password" class="form-control input-sm " autocomplete="off" value="<?= $setup_dbpass ?>" name="setup_dbpass"></td></tr>
        <tr><td style="vertical-align: middle;">Database Schema:</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbschema" value="<?= $setup_dbschema ?>" id="db_schema"></td></tr>
        <tr class="hideIfResource"><td style="vertical-align: middle;">Database Port:</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbport" value="<?= $setup_dbport?>" id="db_port"></td></tr>
        <tr class="hideIfResource"><td style="vertical-align: middle;">SQL Driver (unixODBC):</td><td><input type="text" class="form-control input-sm " autocomplete="off" name="setup_dbdriver" id="db_driver" value="<?= $setup_dbdriver ?>"></td></tr>
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
            <tr><?= insIcon($msic[db_create]); ?><td style="vertical-align: middle;">create table</td><td><?php echo $msg[db_create]; ?></td></tr>
            <tr><?= insIcon($msic[db_insert]); ?><td style="vertical-align: middle;">insert</td><td><?php echo $msg[db_insert]; ?></td></tr>
            <tr><?= insIcon($msic[db_select]); ?><td style="vertical-align: middle;">select</td><td><?php echo $msg[db_select]; ?></td></tr>
            <tr><?= insIcon($msic[db_delete]); ?><td style="vertical-align: middle;">delete</td><td><?php echo $msg[db_delete]; ?></td></tr>                    
            <tr><?= insIcon($msic[db_drop]); ?><td style="vertical-align: middle;">drop table</td><td><?php echo $msg[db_drop]; ?></td></tr> 
        </tbody>            
    </table>

    <?php
    
    
    # Information
if($DBA['DB'] == 'postgres'){
?>


    <table class="table table-condensed">
        <thead>
            <tr>
                <th colspan="3">
                    Information
                </th>
            </tr>
        </thead>
        <tbody>
            <tr><td><i>you can improve performance by setting <b>syncronous_commit off</b> in postgresql.conf. Be aware and read <a href='https://www.postgresql.org/docs/9.1/static/runtime-config-wal.html'>postgresql documentation</a>. You can set it to <b>off</b> for installation and set it to <b>on</b> after installation.</i></td></tr>
            <tr><td><i>you can adjust security in <b>pg_hba.conf</b>. <u>Trust</u> meens you trust all local users. <u>password</u> meens you need user and password to connect. For more information read <a href='https://www.postgresql.org/docs/9.1/static/auth-pg-hba-conf.html'>postgresql documentation.</a></i></td></tr>
			<tr><td><i>do not forget to create your cluster with <b>initdb --local=C</b>. Otherwise ist results in a wrong dateformat! Read <a href="http://en.limbas.org/wiki/PostgreSQL#Database_Cluster_Initialization">limbas documentation</a></i></td></tr>
			<tr><td><i>If you want to use <b>UTF-8</b> you have to create the database with <b>WITH ENCODING 'UTF8'</b>. <u>If not needed</u> it will better to use <b>WITH ENCODING 'SQL_ASCII'</b>. Read <a href="http://en.limbas.org/wiki/UTF8_support">limbas documentation</a></i></td></tr>
        </tbody>            
    </table>

<?php
}



    
}

if($db_test != 'valid'){
?>

<table class="table table-condensed">
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

<?php }?>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?php $s=array_keys($steps); echo $s[2]?>')">Back</button>
    <?php if($db_test && $db_test == 'valid') { ?>
        <button type="submit" class="btn btn-info pull-right" name="install" value="<?php $s=array_keys($steps); echo $s[4]?>">Next step</button>
    <?php } else { ?>
        <input type="hidden" name="db_test" value="validate">
        <button type="submit" class="btn btn-info pull-right" name="install" value="<?php $s=array_keys($steps); echo $s[3]?>">Check</button>
    <?php } ?>
</div>