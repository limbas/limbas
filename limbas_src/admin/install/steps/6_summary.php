
<table class="table table-condensed">
    <thead>
        <tr>
            <th colspan="2">
                Pre-Installation summary
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td>Installation Path</td><td><?= $setup_path_project ?></td></tr>
        <tr><td>Database Vendor</td><td><?= $DBA["DB"] ?></td></tr>
        <tr><td>Database Name</td><td><?= $setup_database ?></td></tr>
        <tr><td>Database User</td><td><?= $setup_dbuser ?></td></tr>
        <tr><td>Database Host</td><td><?= $setup_host ?></td></tr>
        <tr><td>Database Schema</td><td><?= $setup_dbschema ?></td></tr>
        <?if($setup_dbport){echo "<tr><td>Database Port</td><td>$setup_dbport</td></tr>";}?>
        <tr><td>Installation package</td><td><?= $backupdir ?></td></tr>
    </tbody>
</table>

<div>
    <button type="button" class="btn btn-default" onclick="switchToStep('<?= array_keys($steps)[5] ?>')">Back</button>
    <button type="submit" class="btn btn-success pull-right" name="install" value="<?= array_keys($steps)[7] ?>"> Install Limbas now! </button>
</div>