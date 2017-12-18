
<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="2">
                Pre-Installation summary
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Installation Path</td><td><?php echo $setup_path_project?></td></tr>
        <tr><td>Database Vendor</td><td><?php echo $DBA["DB"]?></td></tr>
        <tr><td>Database Name</td><td><?php echo $setup_database?></td></tr>
        <tr><td>Database User</td><td><?php echo $setup_dbuser?></td></tr>
        <tr><td>Database Host</td><td><?php echo $setup_host?></td></tr>
        <tr><td>Database Schema</td><td><?php echo $setup_dbschema?></td></tr>
        <tr><td>Database Port</td><td><?php echo $setup_dbport?></td></tr>        
        <tr><td>Installation package</td><td><?php echo $backupdir?></td></tr>        
    </tbody>
</table>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?php $s=array_keys($steps); echo $s[5]?>')">Back</button>
    <button type="submit" class="btn btn-success pull-right" name="install" value="<?php $s=array_keys($steps); echo $s[7]?>"> Install Limbas now! </button>
</div>