<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */


?>
<!-- include codemirror with sql syntax highlighting and sql code completion -->
<script src="assets/vendor/codemirror/lib/codemirror.js?v=<?= $umgvar["version"] ?>"></script>
<script src="assets/vendor/codemirror/addon/edit/matchbrackets.js?v=<?= $umgvar["version"] ?>"></script>
<script src="assets/vendor/codemirror/addon/edit/matchtags.js?v=<?= $umgvar["version"] ?>"></script>
<script src="assets/vendor/codemirror/mode/sql/sql.js?v=<?= $umgvar["version"] ?>"></script>
<script src="assets/vendor/codemirror/addon/hint/show-hint.js?v=<?= $umgvar["version"] ?>"></script>
<link rel="stylesheet" href="assets/vendor/codemirror/addon/hint/show-hint.css?v=<?= $umgvar["version"] ?>">
<script src="assets/vendor/codemirror/addon/hint/sql-hint.js?v=<?= $umgvar["version"] ?>"></script>
<link rel="stylesheet" href="assets/vendor/codemirror/lib/codemirror.css?v=<?= $umgvar["version"] ?>">
<script src="assets/vendor/sql-formatter/sql-formatter.min.js?v=<?= $umgvar["version"] ?>"></script>

<link href="assets/vendor/select2/select2.min.css" rel="stylesheet">
<script src="assets/vendor/select2/select2.full.min.js"></script>

<link rel="stylesheet" type="text/css" href="assets/vendor/datatables/dataTables.bootstrap5.min.css"/>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.min.js"></script>
<script type="text/javascript" src="assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>

<div class="container-fluid p-3">
    <div class="card">
        <div class="card-body">


            <?php /* --- Tabellenliste ------------------------------- */
            if (!$use_codemirror) {
                $use_codemirror = 'true';
            }
            ?>

            <form action="main_admin.php" method="post" name="form2">
                <input type="hidden" name="action" value="setup_tabtools">
                <input type="hidden" name="empty">
                <input type="hidden" name="delete">
                <input type="hidden" name="sqlFavoriteName">
                <input type="hidden" name="use_codemirror" value="<?= $use_codemirror ?>">

                <h3><?= $lang[577] ?></h3>
                <div class="row mb-2">
                    <div class="col-6">
                        <select id="select_sql-table" name="table" class="form-select form-select-sm">
                            <?php
                            $odbc_table = dbf_20(array($DBA['DBSCHEMA'], null, "'TABLE','VIEW','MATVIEW'"));
                            foreach ($odbc_table['table_name'] as $tableKey => $tvalue) {
                                $domaintables['tablename'][] = $odbc_table['table_name'][$tableKey];
                                $domaintables['owner'][] = $odbc_table['table_owner'][$tableKey];
                                $domaintables['type'][] = $odbc_table['table_type'][$tableKey];

                                $tableName = $odbc_table['table_name'][$tableKey];
                                $tableType = lmb_strtoupper($odbc_table['table_type'][$tableKey]);

                                $optGroupName = match (true) {
                                    str_starts_with($tableName, 'verk_') => 'verkTables',
                                    str_starts_with($tableName, 'lmb_') || str_starts_with($tableName, 'ldms_') => 'lmbTables',
                                    $tableType == 'VIEW' => 'viewTables',
                                    default => 'normalTables'
                                };
                                $$optGroupName[$tableKey] = [
                                    ($tableType == 'VIEW' ? 'VIEW :: ' : '') . $tableName,
                                    isset($table) && $table == $tableName ? 'selected' : ''
                                ];
                            }
                            $optGroups = [
                                    $lang[577] => $normalTables,
                                    $lang[2949] => $lmbTables,
                                    $lang[1460] => $verkTables,
                                    $lang[735] => $viewTables
                            ];
                            foreach ($optGroups as $oGName => $oGTables):?>
                                <optgroup label="<?= $oGName ?>">
                                    <?php foreach ($oGTables as $tableKey => [$tableName, $selected]): ?>
                                        <option value='<?= $tableName ?>' <?= $selected ?>><?= $tableName ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-primary btn-sm" type="submit" name="info" value="info">info</button>
                        <button class="btn btn-primary btn-sm" type="submit" name="show"
                                value="<?= $lang[1061] ?>"><?= $lang[1061] ?></button>
                        <button class="btn btn-warning btn-sm" type="button"
                                onclick="if(confirm('<?= $lang[2153] ?>')){ document.form2.empty.value=1; document.form2.submit(); }"><?= $lang[550] ?></button>
                        <button class="btn btn-danger btn-sm" type="button"
                                onclick="if(confirm('<?= $lang[2287] ?>')){ document.form2.delete.value='table'; document.form2.submit(); }"><?= $lang[160] ?></button>
                    </div>
                </div>

                <h3><?= $lang[1060] ?></h3>
                <div class="row mb-2">
                    <div class="col-6">
                        <select id="select_sql-domain-table" name="domaintable" class="form-select form-select-sm">
                            <?php
                            foreach ($DBA['DOMAINTABLE'] as $key => $val) {
                                echo "<option value='{$DBA['DOMAINSCHEMA'][$key]}.{$DBA['DOMAINTABLE'][$key]}'>{$DBA['DOMAINTABLE'][$key]}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-primary btn-sm" type="submit" name="showsys"
                                value="<?= $lang[1061] ?>"><?= $lang[1061] ?></button>
                    </div>
                </div>


                <?php if ($sqlFavorites) : ?>
                    <h3><?= $lang[2932] ?></h3>
                    <div class="row mb-2">
                        <div class="col-6">
                            <select name="favorite" class="form-select form-select-sm">
                                <?php
                                foreach ($sqlFavorites as $favoriteID => $favoriteName) {
                                    $selected = ($favorite == $favoriteID) ? 'selected' : '';
                                    echo "<option value='$favoriteID' $selected>$favoriteName</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary btn-sm" type="submit" name="showFavorite"
                                    value="<?= $lang[1061] ?>"><?= $lang[1061] ?></button>
                            <button class="btn btn-primary btn-sm" type="button"
                                    onclick="if(confirm('<?= $lang[2287] ?>')){ document.form2.delete.value='favorite'; document.form2.submit(); }"><?= $lang[160] ?></button>
                        </div>
                    </div>

                <?php endif; ?>

                <h3>SQL-Query</h3>
                <div class="mb-3 border">
                    <textarea id="sqlvalue" name="sqlvalue" <?php if ($use_codemirror != 'true') {
                        echo 'class="form-control" style="height:300px;"';
                    } ?> ><?= $sqlvalue ?></textarea>
                    <?php if ($use_codemirror == 'true') { ?>
                        <script>
                            var editor = CodeMirror.fromTextArea(document.getElementById("sqlvalue"), {
                                lineNumbers: true,
                                matchBrackets: true,
                                mode: "text/x-sql",
                                indentWithTabs: true,
                                smartIndent: true,
                                autofocus: true,
                                extraKeys: {
                                    "Ctrl-Enter": function () {
                                        $("#sqlexec").trigger("click");
                                    },
                                    "Ctrl-Space": "autocomplete"
                                }
                            });

                            /**
                             * Format sql only if content was changed (!clean) and formatted content differs current content
                             * This prevents the page jumps caused by scrolling the codemirror into view after changing the content
                             * @param checkClean whether to skip formatting if the content wasnt changed
                             */
                            function formatSQL(checkClean = true) {
                                if (checkClean && editor.doc.isClean())
                                    return;
                                var oldValue = editor.getValue();
                                var newValue = sqlFormatter.format(oldValue, {indent: "    "});
                                if (oldValue.length !== newValue.length || oldValue !== newValue) {
                                    editor.setValue(newValue);
                                }
                                editor.doc.markClean();
                            }

                            editor.on('blur', formatSQL);

                            $(function () {
                                formatSQL(false);
                            });
                        </script>
                    <?php } ?>
                </div>

                <div class="row">
                    <div class="col-sm-8">
                        <button class="btn btn-primary btn-sm" id="sqlexec" type="submit" value="<?= $lang[1065] ?>"
                                name="sqlexec"><?= $lang[1065] ?></button>
                        <button class="btn btn-primary btn-sm" type="button" name="sqlFavorite"
                                onclick="const name = window.prompt('Name:'); document.form2.sqlFavoriteName.value  = name; document.form2.submit();"><?= $lang[2218] ?></button>
                        format Code: <input type="checkbox" class="align-middle" value="2"
                                            onclick="document.form2.use_codemirror.value=this.checked; document.form2.submit();" <?= ($use_codemirror == 'true') ? 'checked' : '' ?> >
                    </div>
                    <div class="col-sm-4 text-end text-nowrap">
                        <?= $lang[2770] ?>: <input type="text" name="sqlexecnum"
                                                   class="form-control form-control-sm d-inline-block w-auto align-middle"
                                                   value="<?= $sqlexecnum ?>">
                    </div>
                </div>
            </form>

            <div class="mt-3">
                <p><?= $resultMsg ?></p>
                <div class="table-responsive mb-3">
                    <?php

                    if ($rssql) {
                        echo ODBCResourceToHTML($rssql, 'id="lmb_resource" class="table table-sm table-bordered" style="width:100%"', '', $sqlexecnum);
                    } elseif (lmb_strtoupper(lmb_substr($sqlvalue, 0, 7)) == 'EXPLAIN') {
                        $rs = lmbdb_exec($db, $sqlvalue);
                        echo '<table id="lmb_resource" class="table table-sm table-bordered" style="width:100%">';
                        while ($ra = lmbdb_fetch_array($rs)) {

                            if (!$bzm) {
                                echo "<thead><tr>";
                                foreach ($ra as $col => $value) {
                                    echo "<th>$col</th>";
                                }
                                echo "</tr></thead><tbody>";
                            }

                            echo '<tr>';
                            foreach ($ra as $col => $value) {
                                echo "<td>$value</td>";
                            }
                            echo '</tr>';
                            $bzm++;
                        }
                        echo '</tbody></table>';
                    }

                    $zeit0 = gettime();

                    if ($show and $table) {
                        # show table
                        echo "<h3>$table</h3>";
                        $sqlquery = "SELECT * FROM $table";
                        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                        if ($rs) {
                            echo ODBCResourceToHTML($rs, 'id="lmb_resource" class="table table-sm table-bordered" style="width:100%"', '', $sqlexecnum);
                        }
                    } else if ($info and $table) {
                        # show info of table
                        echo "<h3>$table</h3>";
                        $rs = dbf_5(array($DBA['DBSCHEMA'], $table, null, 1));
                        echo ODBCResourceToHTML($rs, 'id="lmb_resource" class="table table-sm table-bordered" style="width:100%"', '', $sqlexecnum);
                    } else if ($showsys and $domaintable) {
                        # show system info
                        echo "<h3>$domaintable</h3>";
                        $sqlquery = "SELECT * FROM $domaintable";
                        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
                        if ($rs) {
                            echo ODBCResourceToHTML($rs, 'id="lmb_resource" class="table table-sm table-bordered" style="width:100%"', '', $sqlexecnum);
                        }
                    } else if (!$table and !$showsys) {
                        # show all tables and views
                        echo '<table id="lmb_resource" class="table table-sm table-bordered" style="width:100%">';
                        echo "<thead><tr><th>$lang[164]</th><th>$lang[1067]</th><th>$lang[1068]</th></tr></thead><tbody>";
                        $bzm = 0;
                        while ($domaintables['tablename'][$bzm]) {
                            echo '<TR><TD>' . $domaintables['tablename'][$bzm] . '</TD><TD>' . $domaintables['owner'][$bzm] . '</TD><TD>' . $domaintables['type'][$bzm] . '</TD></TR>';
                            $bzm++;
                        }
                        echo '</tbody></table>';
                    }

                    $zeit = number_format(gettime() - $zeit0, 8, ',', '.');
                    ?>
                </div>
                <span class='text-success'>finished execution!</span>&nbsp;(<?= $zeit ?> sec.)
            </div>


        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {
        $('#lmb_resource').DataTable({
            scrollY: 600,
            scrollX: true,
            scrollCollapse: true,
            paging: false,
            fixedColumns: true,
            colReorder: true
        });
        $('#select_sql-table').select2();
        $('#select_sql-domain-table').select2();
    });
</script>
