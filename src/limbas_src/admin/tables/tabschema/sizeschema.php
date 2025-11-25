<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

global $lang;

// todo update to class based approach

use Limbas\lib\db\functions\Dbf;

function getSizeschemaArray(): array
{
    global $DBA;
    global $db;

    $dbSchema = $DBA['DBSCHEMA'];
    $odbc_table = Dbf::getTableList($dbSchema, null, "'TABLE','VIEW','MATVIEW'");
    $sizeschemaArray = [];
    foreach ($odbc_table['table_name'] as $tableName) {
        // todo make this more efficient
        $columns = Dbf::getColumns(
            $dbSchema,
            $tableName,
            null,
            false,
            false
        ) ?: [];

        $columnNames = $columns['columnname'];

        $colCount = count($columnNames);

        $sqlQuery = "SELECT COUNT(*) AS COUNT, MAX(ID) AS MAX FROM " . $tableName;
        if(!in_array('id', $columnNames)) {
            $sqlQuery = "SELECT COUNT(*) AS COUNT, 0 AS MAX FROM " . $tableName;
        }
        $rs = lmbdb_exec($db, $sqlQuery);

        $entryCount = lmbdb_result($rs, 'COUNT');

        $max = lmbdb_result($rs, 'MAX');
        $max = is_numeric($max) ? $max : 0;

        $sqlQuery = "SELECT pg_size_pretty(pg_table_size('{$tableName}')) AS table_size";
        $rs = lmbdb_exec($db, $sqlQuery);

        [$prettySize, $orderSize] = Dbf::prettyPrintTableSize($dbSchema, $tableName ?? '');

        $sizeschemaArray[] = [
            'Name' => $tableName,
            'Entry Count' => $entryCount,
            'Column Count' => $colCount,
            'Max' => $max,
            'Pretty Size' => [$prettySize, $orderSize]
        ];
    }

    return $sizeschemaArray;
}

?>
<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.min.js"></script>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#lmb_sizeschema').DataTable({
            scrollY: 600,
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            colReorder: true
        });
    });
</script>

<?php
// todo make this ajax
// todo add check to show lmb_ tables
$sizeschemaArray = getSizeschemaArray();
?>

<table id="lmb_sizeschema" class="table table-sm table-bordered" style="width:100%">
    <thead>
    <tr>
        <?php foreach ($sizeschemaArray[0] as $columnNames => $columnValue): ?>
            <th><?= $columnNames ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($sizeschemaArray as $sizeschemaEntry): ?>
        <tr>
            <?php foreach ($sizeschemaEntry as $columnName => $columnValue):
                $columnOrder = '';
                if (is_array($columnValue)) {
                    $columnOrder = 'data-order="' . $columnValue[1] . '"';
                    $columnValue = $columnValue[0];
                }
                ?>
                <td <?= $columnOrder ?>><?= e($columnValue) ?></td>
            <?php endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>