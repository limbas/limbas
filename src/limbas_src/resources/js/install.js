/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


$(function () {
    // hide/show correct inputs depending on selected radio button
    onDbConnectionChange();
    $('input[name="connection"]').on('change', onDbConnectionChange);

    $('#vendor').on('change', onDbVendorChange);
    $('#name').on('keyup', onDbNameChange);
    
    
    // update example odbc.ini on input change
    $('input').change(updateOdbcIni);


    $('#lang-select').change(function () {
        let lang = $(this).val();
        window.location.href = window.location.protocol + '//' + window.location.host + window.location.pathname + '?lang=' + lang;
        return false;
    });
});

function onDbNameChange() {
    const $vendor = $('#vendor');
    const vendor = $vendor.val();
    if (vendor === 'mysql') {
        $('#schema').val($(this).val());
    }
}


function onDbVendorChange() {
    
    const $vendor = $('#vendor');
    const vendor = $vendor.val();

    const $schema = $('#schema');
    let schema = $schema.val();

    const $driver = $('#driver');
    let driver = $driver.val();
    
    const $odbcInst = $('#odbcinst');

    if (vendor === 'postgres') {
        schema = 'public';
        if (!driver || driver === 'PDO') {
            driver = 'PSQL';
        }
        $odbcInst.html('\
                [PSQL]<br>\
                Description = PostgreSQL<br>\
                Driver64 = /usr/lib64/posqlodbcw.so\
            ');
    } else if (vendor === 'ingres') {
        schema = 'ingres';
        if (!driver || driver === 'PDO') {
            driver = 'IngresSQL';
        }
        $odbcInst.html('');
    } else if (vendor === 'mysql') {
        schema = $('#name').val();
        if (!driver || driver === 'PDO') {
            driver = 'MySQL';
        }
        $odbcInst.html('\
                [MySQL]<br>\
                Description = ODBC for MySQL<br>\
                Driver64 = /usr/lib64/libmyodbc5.so<br>\
                Setup64 = /usr/lib64/libodbcmyS.so\
            ');
    } else if (vendor === 'mssql') {
        schema = 'dbo';
        if (!driver || driver === 'PDO') {
            driver = 'MSSQL';
        }
        $odbcInst.html('');
    } else if (vendor === 'maxdb76') {
        schema = $('#user').val();
        if (!driver || driver === 'PDO') {
            driver = 'MAXDBSQL';
        }
        $odbcInst.html('\
                [MAXDBSQL]<br>\
                Description = ODBC for MaxDB<br>\
                Driver = /opt/sdb/interfaces/odbc/lib79/libsdbodbc.so\
            ');
    }
    
    
    if ($('#radio_pdo').prop('checked')) {
        driver = 'PDO';
    }


    $schema.val(schema);
    $driver.val(driver);

    updateOdbcIni();
}


function onDbConnectionChange() {
    
    const $hideIfResource = $('.hideIfResource');
    const $hideIfOdbc = $('.hideIfOdbc');
    const $hideIfPdo = $('.hideIfPdo');
    const $driver = $('#driver');
    
    $hideIfResource.removeClass('d-none');
    $hideIfPdo.removeClass('d-none');
    $driver.prop('readonly', false);
    
    
    if ($('#radio_odbc_driver').prop('checked')) {
        $hideIfOdbc.addClass('d-none');
        $driver.val('');
        
        onDbVendorChange();

        $driver.prop('readonly', true);
        
        $('.support_pdo').show();
        $('.notsupport_pdo').show();

    } else if ($('#radio_odbc_resource').prop('checked')) {
        $hideIfResource.addClass('d-none');

        // remove driver
        $driver.val('');

        $('.support_pdo').show();
        $('.notsupport_pdo').show();

    } else if ($('#radio_pdo').prop('checked')) {
        $hideIfPdo.addClass('d-none');
        
        // remove driver
        $driver.val('PDO').prop('readonly', true);
        
        const $vendor = $('#vendor');
        const vendor = $vendor.val();

        if (vendor !== 'mysql' && vendor !== 'postgres') {
            $vendor.val('').change();
        }

        $('.support_pdo').show();
        $('.notsupport_pdo').hide();
    }

    updateOdbcIni();
}


function showprogress(id, value){
    let $progressBar = $('#'+id);
    $progressBar.width(Math.round(value)+'%');
    $progressBar.text(Math.round(value)+'%');
}

function updateOdbcIni() {
    
    const driver = $('#driver').val();
    const name = $('#name').val();
    const user = $('#user').val();
    const password = $('#password').val();

    $('#odbcini').html('[<b>' + name + '</b>]<br>\
                    Description = ' + driver + '<br>\
                    Driver = <b>' + driver + '</b><br>\
                    Trace = No<br>\
                    TraceFile =<br>\
                    Database = <b>%limbasdb%</b><br>\
                    Servername = <b>%limbasserver%</b><br>\
                    ' + (user === '' ? 'Username = <b>%limbasuser%</b><br>' : '') + '\
                    ' + (password === '' ? 'Password = <b>%limbaspw%</b><br>' : '') + '\
                    Port = <b>%limbasport%</b><br>\
                    Protocol = 6.4<br>\
                    ReadOnly = No<br>\
                    RowVersioning = No<br>\
                    ShowSystemTables = No<br>\
                    ShowOidColumn = No<br>\
                    FakeOidIndex = No<br>\
                    ConnSettings =');
}

function scrolldown(){
    $(document).scrollTop($(document).height());
}

