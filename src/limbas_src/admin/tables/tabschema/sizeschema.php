<?php
global $lang;

// todo update to class based approach

function getSizeschemaArray(): array
{
    global $DBA;
    global $db;

    $dbSchema = $DBA['DBSCHEMA'];
    $odbc_table = dbf_20([$dbSchema, null, "'TABLE','VIEW','MATVIEW'"]);
    $sizeschemaArray = [];
    foreach ($odbc_table['table_name'] as $tableName) {
        // todo make this more efficient
        $columns = dbf_5([
            $dbSchema,
            $tableName,
            null,
            false,
            false
        ]) ?: [];

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

        [$prettySize, $orderSize] = dbq_30([$dbSchema, $tableName]);

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