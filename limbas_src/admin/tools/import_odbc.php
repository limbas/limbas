<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID: 148
 */
?>



<?php

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
<TABLE ID="tab7" width="100%" cellspacing="2" cellpadding="1"
	class="tabBody importcontainer">
	<TR class="tabHeader">
		<TD class="tabHeaderItem" COLSPAN="5">ODBC Import</TD>
	</TR>
	<tr>
		<td class="tabBody">Database Vendor:</td>
		<td><select name="odbc[odbc_vendor]"><option></option>
<?php
    foreach ($vendorNames as $key => $value) {
        if($odbc['odbc_vendor'] == $vendorValues[$key]){$selected = 'selected';}else{$selected = '';}
        echo "<option value=\"".$vendorValues[$key]."\" $selected>".$vendorNames[$key]."</option>";
    }
?>
</select></td>
	</tr>
	<tr class="tabBody">
		<td>Database Host:</td>
		<td><input type="text" style="width: 250px;"
			value="<?= $odbc['odbc_host'] ?>" name="odbc[odbc_host]"></td>
	</tr>
	<tr class="tabBody">
		<td>Database Name:</td>
		<td><input type="text" style="width: 250px;"
			value="<?= $odbc['odbc_database'] ?>" name="odbc[odbc_database]"></td>
	</tr>
	<tr class="tabBody">
		<td>Database User:</td>
		<td><input type="text" style="width: 250px;"
			value="<?= $odbc['odbc_dbuser'] ?>" name="odbc[odbc_dbuser]"></td>
	</tr>
	<tr class="tabBody">
		<td>Database Password:</td>
		<td><input type="password" style="width: 250px;"
			value="<?= $odbc['odbc_dbpass'] ?>" name="odbc[odbc_dbpass]"></td>
	</tr>
	<tr class="tabBody">
		<td>Database Schema:</td>
		<td><input type="text" name="odbc[odbc_dbschema]"
			style="width: 250px;" value="<?= $odbc['odbc_dbschema'] ?>"></td>
	</tr>
	<tr class="tabBody">
		<td>Database Port:</td>
		<td><input type="text" name="odbc[odbc_dbport]" style="width: 250px;"
			value="<?= $odbc['odbc_dbport']?>"></td>
	</tr>
	<tr class="tabBody">
		<td>SQL Driver (unixODBC):</td>
		<td><input type="text" name="odbc[odbc_dbdriver]"
			style="width: 250px;" value="<?= $odbc['odbc_dbdriver'] ?>"></td>
	</tr>
	<tr class="tabBody">
		<td colspan=2><hr></td>
	</tr>

<?php if($import_action <= 0){?>
<tr class="tabBody">
		<td><?=$lang[1003]?></td>
		<td><select name="odbc[odbc_attach]"><option value="">
	<?php
	$gtab_ = $gtab;
	asort($gtab_['table']);
	foreach ($gtab_["table"] as $key => $value){
	   if($odbc['odbc_attach'] == $gtab_["tab_id"][$key]){$selected = 'selected';}else{$selected = '';}
	   echo "<option value=\"".$gtab_["tab_id"][$key]."\" $selected>".$gtab_["table"][$key]."</option>";
	}
	?>
	</select></td>
	</tr>
<?php }else{
    echo "<input type=\"hidden\" name=\"odbc[odbc_attach]\" value=\"".$odbc['odbc_attach']."\">";
}?>
<tr class="tabBody">
		<td colspan=2>&nbsp;</td>
	</tr>
	<TR class="tabBody">
		<TD></TD>
		<TD><input type="button" value="<?=$lang[2243]?>"
			onclick="document.form1.import_action.value=0;document.form1.submit();"></TD>
	</TR>
	<TR class="tabBody">
		<TD colspan="2" class="tabFooter"></TD>
	</TR>
</TABLE>



<?php

if ($odbc['odbc_vendor'] AND file_exists($umgvar['path'] . '/lib/odbc/db_' . $odbc['odbc_vendor'] . '.lib')) {
    require_once ($umgvar['path'] . '/lib/odbc/db_' . $odbc['odbc_vendor'] . '.lib');
    require_once ($umgvar['path'] . '/lib/odbc/db_' . $odbc['odbc_vendor'] . '_admin.lib');
    
    $dbfunc = $odbc['odbc_vendor'] . '_dbq_0';
    if (! $odbc_connect = $dbfunc($odbc['odbc_host'], $odbc['odbc_database'], $odbc['odbc_dbuser'], $odbc['odbc_dbpass'], $odbc['odbc_dbdriver'], $odbc['odbc_dbport'])) {
        echo odbc_errormsg($odbc_connect);
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