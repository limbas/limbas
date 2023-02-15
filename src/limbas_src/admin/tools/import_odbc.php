<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>



<?php

# database vendors
$vendorNames = array(
    'PostgreSQL', 
    'mysql'
);
$vendorValues = array(
    'postgres', 
    'mysql'
);
?>



<div class="row">
    <div class="col-md-9 col-lg-7">

        <div class="card border-top-0 mb-3">
            <div class="card-body">

                <div class="row mb-2">
                    <label for="odbc-vendor" class="col-sm-3 col-form-label">Database Vendor:</label>
                    <div class="col-sm-9">
                        <select name="odbc[odbc_vendor]" id="odbc-vendor" class="form-select">
                            <option></option>
                            <?php
                            foreach ($vendorNames as $key => $value) {
                                if($odbc['odbc_vendor'] == $vendorValues[$key]){$selected = 'selected';}else{$selected = '';}
                                echo "<option value=\"".$vendorValues[$key]."\" $selected>".$vendorNames[$key]."</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="odbc-host" class="col-sm-3 col-form-label">Database Host</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-host" name="odbc[odbc_host]" class="form-control" value="<?=$odbc['odbc_host']?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="odbc-database" class="col-sm-3 col-form-label">Database Name</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-database" name="odbc[odbc_database]" class="form-control" value="<?=$odbc['odbc_database']?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="odbc-dbuser" class="col-sm-3 col-form-label">Database User</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-dbuser" name="odbc[odbc_dbuser]" class="form-control" value="<?=$odbc['odbc_dbuser']?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="odbc-dbpass" class="col-sm-3 col-form-label">Database Password</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-dbpass" name="odbc[odbc_dbpass]" class="form-control" value="<?=$odbc['odbc_dbpass']?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="odbc-dbschema" class="col-sm-3 col-form-label">Database Schema</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-dbschema" name="odbc[odbc_dbschema]" class="form-control" value="<?=$odbc['odbc_dbschema']?>">
                    </div>
                </div>
                <div class="row mb-2">
                    <label for="odbc-dbport" class="col-sm-3 col-form-label">Database Port</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-dbport" name="odbc[odbc_dbport]" class="form-control" value="<?=$odbc['odbc_dbport']?>">
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="odbc-dbdriver" class="col-sm-3 col-form-label">SQL Driver (unixODBC):</label>
                    <div class="col-sm-9">
                        <input type="text" id="odbc-dbdriver" name="odbc[odbc_dbdriver]" class="form-control" value="<?=$odbc['odbc_dbdriver']?>">
                    </div>
                </div>

                <hr>

                <?php if($import_action <= 0):?>

                    <div class="row mb-2">
                        <label for="odbc-attach" class="col-sm-3 col-form-label"><?=$lang[1003]?>:</label>
                        <div class="col-sm-9">
                            <select name="odbc[odbc_attach]" id="odbc-attach" class="form-select">
                                <option value=""></option>
                                <?php
                                $gtab_ = $gtab;
                                asort($gtab_['table']);
                                foreach ($gtab_["table"] as $key => $value){
                                    if($odbc['odbc_attach'] == $gtab_["tab_id"][$key]){$selected = 'selected';}else{$selected = '';}
                                    echo "<option value=\"".$gtab_["tab_id"][$key]."\" $selected>".$gtab_["table"][$key]."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="odbc[odbc_attach]" value="<?=$odbc['odbc_attach']?>">
                <?php endif;?>

                <div class="text-end">
                    <button class="btn btn-primary" type="button" onclick="document.form1.import_action.value=0;document.form1.submit();"><?=$lang[979]?></button>
                </div>
            </div>
        </div>
    </div>
</div>

    



<?php

if ($odbc['odbc_vendor'] AND file_exists(COREPATH . 'lib/odbc/db_' . $odbc['odbc_vendor'] . '.lib')) {
    require_once (COREPATH . 'lib/odbc/db_' . $odbc['odbc_vendor'] . '.lib');
    require_once (COREPATH . 'lib/odbc/db_' . $odbc['odbc_vendor'] . '_admin.lib');
    
    $dbfunc = $odbc['odbc_vendor'] . '_dbq_0';
    if (! $odbc_connect = $dbfunc($odbc['odbc_host'], $odbc['odbc_database'], $odbc['odbc_dbuser'], $odbc['odbc_dbpass'], $odbc['odbc_dbdriver'], $odbc['odbc_dbport'])) {
        echo lmbdb_errormsg($odbc_connect);
        return false;
    }
    $odbc['odbc_connect'] = $odbc_connect;
}else{
    return false;
}



/*
$filename = $odbc['odbc_table'];
$attach_gtabid = $odbc['odbc_attach'];

$dbfunc = $odbc['odbc_vendor'].'_dbf_5';
if(!$fieldlist = $dbfunc($odbc_connect,array($odbc['odbc_dbschema'],$odbc['odbc_table']))){return false;}
    
foreach ($fieldlist['columnname'] as $key => $value){
    $header[] = $value;
}
*/

// show all odbc tables
if($import_action == 0) {

        $dbfunc = $odbc['odbc_vendor'] . '_dbf_20';
        if (! $tablelist = $dbfunc($odbc_connect, array($odbc['odbc_dbschema'],null,"'TABLE','VIEW'"))) {
            return false;
        }
        
        echo '<hr>';
        echo '<table width="100%" class="tabfringe" cellspacing="1" cellpadding="0">';
        echo '<tr class="tabHeader"><td class="tabHeaderItem">table</td><td class="tabHeaderItem">typ</td></tr>';
        foreach ($tablelist['table_name'] as $key => $value) {
            echo "<tr><td><a onclick=\"document.form1.import_action.value=1;document.getElementsByName('odbc[odbc_table]')[0].value='" . $value . "';document.form1.submit();\">" . $value . "</a></td><td>" . $tablelist['table_type'][$key] . "</td></tr>";
        }
        echo '</table>';

// choose table and merge fields
}elseif($import_action == 1 AND $odbc['odbc_table']) {
    
    // attach table
    if ($odbc['odbc_attach']) {

        $dbfunc = $odbc['odbc_vendor'].'_dbf_5';
        if(!$fieldlist = $dbfunc($odbc_connect,array($odbc['odbc_dbschema'],$odbc['odbc_table']))){return false;}
            
        foreach ($fieldlist['columnname'] as $key => $value){
            $header[] = $value;
        }

        // load template
        if($template == 'load'){
            import_template($ifield,$odbc['odbc_attach'],$template,$template_val);
            $template = null;
        }
        
        import_attach_fieldmapping($odbc['odbc_attach'],$header,$ifield,$odbc['odbc_table']);

        // edit templates
        import_template($ifield,$odbc['odbc_attach'],$template,$template_val);

    // create new table
    }else{
        import_create_fieldmapping($ifield,'odbc',null,$odbc,null,null,$txt_encode);
    }
    
// start import
} elseif ($import_action == 2) {
    
    // attach data to existing table
    if ($odbc['odbc_attach']) {
        echo "<br>";
        $result = import_attach_fromodbc($ifield,$odbc,$txt_encode,1);
        if ($result['false']) {
            echo "
        		<p style=\"color:red;\">&nbsp;&nbsp;" . $result['false'] . " " . $lang[1012] . "</p><br>
        		<p style=\"color:green;\">&nbsp;&nbsp;" . $result['true'] . " " . $lang[1017] . "</p>";
            } else {
                echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
            }
    
    // create new table and fill data
    }else{
        if($ifield = import_create_addtable('odbc', $ifield, $add_permission, 1)){
            if($ifield['data'] == 2){
                $result = import_create_fillodbc($ifield ,$odbc, $txt_encode, 1);
                if ($result['false']) {
                    echo"
            		<p style=\"color:red;\">&nbsp;&nbsp;".$result['false']." ".$lang[1012] . "</p><br>
            		<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
                } else {
                    echo "<p style=\"color:green;\">&nbsp;&nbsp;".$result['true']." ".$lang[1017] . "</p>";
                }
            }
        }
    }

}






?>
