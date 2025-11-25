<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\db\functions;

use DateTime;
use Limbas\lib\db\Database;

class Postgres extends DbFunction
{

    public function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed
    {

        if ($driver === 'PDO') {
            if (!$port) {
                $port = 5432;
            }
            $dsn = "pgsql:host=$host;port=$port;dbname=$database";
        } elseif ($driver) {
            $dsn = "Driver=$driver;Server=$host;Port=$port;Database=$database;ReadOnly=No";
            #SQL_CUR_USE_ODBC
            #SQL_CUR_DEFAULT
            #SQL_CUR_USE_DRIVER
        } else {
            $dsn = $database;
        }

        $db = lmbdb_pconnect($dsn, $user, $password);

        if ($db) {
            $this->setVariables($db);
            return $db;
        } else {
            echo "<div class=\"alert alert-danger\"><h1>Database connection failed</h1><p>($dsn)<BR>" . lmbdb_errormsg() . "</p></div>";
            return false;
        }
    }


    /**
     * convert stamp for db
     *
     * @param mixed $date
     * @param bool $withoutTime
     * @return string
     */
    public function convertToDbTimestamp(mixed $date, ?bool $withoutTime = false): string
    { // dbf_1
        if ($withoutTime) {
            if ($date instanceof DateTime) {
                return $date->format('Y-m-d');
            } else {
                return date('Y-m-d', 1);
            }
        } else {
            if ($date instanceof DateTime) {
                return $date->format('Y-m-d H:i:s');
            } else {
                return date('Y-m-d H:i:s', 1);
            }
        }
    }


    /**
     * convert date from db to stamp
     *
     * @param string $value
     * @return false|int
     */
    public function convertFromDbTimestamp(string $value): false|int
    { // dbf_2
        if (!$value) {
            return false;
        }
        $db_date = lmb_substr($value, 0, 19);
        $db_date = preg_replace("/[^0-9]/", ";", $db_date);
        $db_date = explode(";", $db_date);
        if (is_numeric($db_date[0])) {
            $result_stamp = mktime($db_date[3], $db_date[4], $db_date[5], $db_date[1], $db_date[2], $db_date[0]);
        } else {
            $result_stamp = 0;
        }
        return $result_stamp;
    }

    /**
     * parse blob
     *
     * @param string $value
     * @return string
     */
    public function parseBlob(string $value): string
    { // dbf_6
        return $this->parseString($value);
    }

    /**
     * parse string
     *
     * @param string $value
     * @return string
     */
    public function parseString(string $value): string
    {  // dbf_7
        return str_replace("\\", LMB_DBFUNC_UMASCB, str_replace("'", "''", $value));
    }


    /**
     * get sequence
     *
     * @param string $name
     * @return mixed|null
     */
    public function getSequence(string $name): mixed
    { // dbf_8
        global $db;

        $query = "SELECT NEXTVAL('" . $this->handleCaseSensitive($name) . "') AS NEXTSEQ";
        $rs = lmbdb_exec($db, $query) or errorhandle(lmbdb_errormsg($db), $query, "get next sequence", 0, 0);
        return lmbdb_result($rs, 'NEXTSEQ');
    }

    /**
     * case sensitive
     *
     * @param string $value
     * @return string
     */
    public function handleCaseSensitive(string $value): string
    { // $this->handleCaseSensitive
        return lmb_strtolower($value);
    }

    /**
     * timediff
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public function sqlTimeDiff(string $startColumn, string $endColumn): string
    { // dbf_9
        return '(' . $endColumn . '-' . $startColumn . ')';
    }

    /**
     * datediff
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public function sqlDateDiff(string $startColumn, string $endColumn): string
    { // dbf_10
        return '(' . $endColumn . '-' . $startColumn . ')';
    }

    /**
     * calculate checksum
     *
     * @param string $field
     * @param int|null $type
     * @return string
     */
    public function calculateChecksum(string $field, ?int $type = null): string
    { // dbf_12
        global $db;
        global $gtab;
        global $gfield;

        if ($type == 4) {
            return "coalesce(md5(floor(extract(epoch from $field))::text), ' ')";
        } else if ($type == 3) {
            return "coalesce(md5($field::integer::text), ' ')";
        } else {
            return "coalesce(md5($field::text), ' ')";
        }
    }


    public function setVariables($db = null): void
    {
        global $session;

        if ($db === null) {
            $db = Database::get();
        }

        //set default limbas variables
        if (isset($session['user_id']) && is_numeric($session['user_id'])) {
            lmbdb_exec($db, 'SET lmb.userid = ' . $session['user_id'] . ';');
        }
        if (isset($session['mid']) && is_numeric($session['mid'])) {
            lmbdb_exec($db, 'SET lmb.mid = ' . $session['mid'] . ';');
        }
        if (isset($session['multitenant'])) {
            lmbdb_exec($db, 'SET lmb.mids = \'' . implode(',', $session['multitenant']) . '\';');
            //where in: any(string_to_array(current_setting('lmb.mids'), ',') :: int [] )
        }
    }


    public function version(?array $DBA = null): array
    {
        $rs = Database::select('PG_SETTINGS', ['SETTING'], ['NAME' => 'server_version_num']);
        $version = lmbdb_result($rs, "SETTING");
        $version_[] = $version;
        $version_[] = $version;

        // encoding
        if ($DBA) {
            $sql = "SHOW SERVER_ENCODING";
            $rs = Database::query($sql);
            $version_[] = lmbdb_result($rs, "server_encoding");
        }

        return $version_;
    }

    public function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        $sql = "SELECT
	indexname,
    t.tablename,
    foo.attname AS columnname,
    foo.indisunique AS is_unique,
    idx_scan AS index_used
FROM pg_tables t
LEFT OUTER JOIN pg_class c ON t.tablename=c.relname
LEFT OUTER JOIN
    ( SELECT c.relname AS ctablename, ipg.relname AS indexname, idx_scan, indexrelname,a.attname, x.indisprimary,x.indisunique FROM pg_index x
           JOIN pg_class c ON c.oid = x.indrelid
           JOIN pg_class ipg ON ipg.oid = x.indexrelid
           JOIN pg_stat_all_indexes psai ON x.indexrelid = psai.indexrelid 
           JOIN pg_attribute AS a ON a.attrelid = x.indexrelid
)
    AS foo
    ON t.tablename = foo.ctablename
    
WHERE t.schemaname='" . $schema . "'
";

        if (!empty($tableName)) {
            $sql .= " AND LOWER(t.tablename) LIKE '" . lmb_strtolower($tableName) . "'";
        }
        if (!empty($indexName)) {
            $sql .= " AND LOWER(indexname) LIKE '" . lmb_strtolower($indexName) . "'";
        }
        if ($noPrimary) {
            $sql .= " AND foo.indisprimary = FALSE";
        }
        if (!empty($columnName)) {
            $sql .= " AND LOWER(foo.attname) LIKE '" . lmb_strtolower($columnName) . "'";
        }

        $sql .= " ORDER BY 2, 3;";

        return $sql;
    }

    public function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        $unique = '';
        if ($isUnique) {
            $unique = 'UNIQUE';
        }
        return "CREATE $unique INDEX " . $this->handleCaseSensitive($indexName) . ' ON ' . $this->handleCaseSensitive($tableName) . '(' . $this->handleCaseSensitive($columnName) . ')';
    }

    public function dropIndexSql(string $indexName, string $tableName): string
    {
        return 'DROP INDEX ' . $indexName;
    }

    public function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        $db = Database::get();

        $sql = "SELECT
	    TC.CONSTRAINT_NAME, TC.TABLE_NAME, KCU.COLUMN_NAME, 
	    CCU.TABLE_NAME AS FOREIGN_TABLE_NAME,
	    CCU.COLUMN_NAME AS FOREIGN_COLUMN_NAME,
	    CONSTRAINT_TYPE 
	FROM 
	    INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS TC 
	    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU ON TC.CONSTRAINT_NAME = KCU.CONSTRAINT_NAME
	    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE AS CCU ON CCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
	WHERE CONSTRAINT_TYPE = 'PRIMARY KEY' ORDER BY TC.TABLE_NAME,KCU.COLUMN_NAME";

        if ($table) {
            $sql .= " AND TC.TABLE_NAME = '" . $this->handleCaseSensitive($table) . "'";
        }
        if ($column) {
            $sql .= " AND KCU.COLUMN_NAME = '" . $this->handleCaseSensitive($column) . "'";
        }

        $contraint = [];
        $rs = lmbdb_exec($db, $sql);
        while (lmbdb_fetch_row($rs)) {
            $contraint["TABLE_NAME"][] = lmbdb_result($rs, "TABLE_NAME");
            $contraint["COLUMN_NAME"][] = lmbdb_result($rs, "COLUMN_NAME");
            $contraint["PK_NAME"][] = lmbdb_result($rs, "CONSTRAINT_NAME");
        }
        return $contraint;
    }

    public function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array
    {
        $db = Database::get();

        $sql = "SELECT
	    TC.CONSTRAINT_NAME, TC.TABLE_NAME, KCU.COLUMN_NAME, 
	    CCU.TABLE_NAME AS FOREIGN_TABLE_NAME,
	    CCU.COLUMN_NAME AS FOREIGN_COLUMN_NAME,
	    CONSTRAINT_TYPE 
	FROM 
	    INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS TC 
	    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU ON TC.CONSTRAINT_NAME = KCU.CONSTRAINT_NAME
	    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE AS CCU ON CCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
	WHERE CONSTRAINT_TYPE = 'UNIQUE'";

        if ($table) {
            $sql .= " AND TC.TABLE_NAME = '" . $this->handleCaseSensitive($table) . "'";
        }
        if ($column) {
            $sql .= " AND KCU.COLUMN_NAME = '" . $this->handleCaseSensitive($column) . "'";
        }

        $contraint = [];
        $rs = lmbdb_exec($db, $sql);
        while (lmbdb_fetch_row($rs)) {
            $contraint["TABLE_NAME"][] = lmbdb_result($rs, "TABLE_NAME");
            $contraint["COLUMN_NAME"][] = lmbdb_result($rs, "COLUMN_NAME");
            $contraint["PK_NAME"][] = lmbdb_result($rs, "CONSTRAINT_NAME");
        }
        return $contraint;
    }

    public function createPrimaryKeySql(string $table, string $column): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ADD PRIMARY KEY (' . $this->handleCaseSensitive($column) . ')';
    }

    public function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ADD CONSTRAINT ' . $this->handleCaseSensitive($constraintName) . " UNIQUE ($column)";
    }

    public function dropPrimaryKeySql(string $table): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' DROP CONSTRAINT ' . $this->handleCaseSensitive($table) . '_pkey ';
    }

    public function dropConstraintSql(string $table, string $constraintName): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' DROP CONSTRAINT ' . $this->handleCaseSensitive($constraintName);
    }

    public function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        $sql = "SELECT
	    TC.CONSTRAINT_NAME AS FKEYNAME, TC.TABLE_NAME AS TABLENAME, KCU.COLUMN_NAME AS COLUMNNAME, 
	    CCU.TABLE_NAME AS REFTABLENAME,
	    CCU.COLUMN_NAME AS REFCOLUMNNAME,
	    CONSTRAINT_TYPE 
	FROM 
	    INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS TC 
	    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU ON TC.CONSTRAINT_NAME = KCU.CONSTRAINT_NAME
	    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE AS CCU ON CCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
	WHERE CONSTRAINT_TYPE = 'FOREIGN KEY'";

        if ($table) {
            $sql .= " AND TC.TABLE_NAME = '" . $this->handleCaseSensitive($table) . "'";
        }
        if ($column) {
            $sql .= " AND KCU.COLUMN_NAME = '" . $this->handleCaseSensitive($column) . "'";
        }

        $sql .= " ORDER BY TC.TABLE_NAME, KCU.COLUMN_NAME";

        return $sql;
    }

    public function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        return "ALTER TABLE $parentTable 
		ADD CONSTRAINT $keyName FOREIGN KEY ($parentColumn) 
		REFERENCES $childTable($childColumn) 
		ON DELETE " . ($restrict ?: 'RESTRICT');
    }

    public function dropForeignKeySql(string $table, string $keyName): string
    {
        return " ALTER TABLE $table DROP CONSTRAINT $keyName CASCADE";
    }

    public function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        $db = Database::get();

        $sql = "SELECT TRIGGER_NAME AS TRIGGERNAME, ACTION_STATEMENT AS DEFINITION, EVENT_MANIPULATION AS EVENT, EVENT_OBJECT_TABLE AS TABLENAME
		FROM
		 	INFORMATION_SCHEMA.TRIGGERS
		WHERE TRIGGER_SCHEMA != 'pg_catalog'";
        if ($triggerName) {
            $sql .= " AND LOWER(TRIGGER_NAME) LIKE '" . lmb_strtolower($triggerName) . "'";
        }

        $res = [];
        $rs = lmbdb_exec($db, $sql);
        while (lmbdb_fetch_row($rs)) {
            $res["triggername"][] = lmbdb_result($rs, "TRIGGERNAME");
            $res["definition"][] = lmbdb_result($rs, "DEFINITION");
            $res["event"][] = lmbdb_result($rs, "EVENT");
            $res["tablename"][] = lmbdb_result($rs, "TABLENAME");
        }

        return $res;
    }

    public function dropTriggerSql(string $table, string $triggerName): string
    {
        return "DROP TRIGGER $triggerName ON $table";
    }

    public function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        if (lmb_strpos($value, strtoupper('EXECUTE PROCEDURE'))) {
            $sql = "CREATE TRIGGER $triggerName $position $action ON " . $this->handleCaseSensitive($table) . " FOR EACH ROW $value";
        } else {
            $sql = "CREATE TRIGGER $triggerName $position $action ON " . $this->handleCaseSensitive($table) . " FOR EACH ROW EXECUTE PROCEDURE $value";
        }
        return $sql;
    }

    public function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string
    {
        if ($childColumn) {
            return "lmb_vkn('$schema.$parentTable','$parentColumn','$action','$childTable','$childColumn','$archive');";
        } else {
            return "lmb_vkn('$schema.$parentTable','$parentColumn','$action','$childTable','','$archive');";
        }
    }

    public function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        return "lmb_lastmodified('$tabId');";
    }

    public function getViewDefinitionSql(string $schema, string $viewName): string
    {
        return "SELECT pg_get_viewdef('$viewName', true) AS DEFINITION";
    }

    public function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        $qu = "SELECT view_name AS VIEWNAME, table_name AS TABLENAME 
		FROM 
			information_schema.view_table_usage 
		WHERE 
			view_schema = '$schema'";
        if ($viewName) {
            $qu .= " AND lower(view_name) = '" . lmb_strtolower($viewName) . "'";
        }
        return $qu;
    }

    public function createViewSql(string $viewName, string $definition): string
    {
        if (lmb_stripos($definition, 'CREATE VIEW') !== false) {
            $qu = $definition;
        } else {
            $qu = "CREATE OR REPLACE VIEW $viewName AS (" . rtrim(trim($definition), ";") . ")";
        }
        return $qu;
    }

    public function dropViewSql(string $viewName): string
    {
        return 'DROP VIEW ' . $this->handleCaseSensitive($viewName);
    }

    public function renameViewSql(string $viewName, string $newName): string
    {
        return 'ALTER VIEW ' . $this->handleCaseSensitive($viewName) . ' RENAME TO ' . $this->handleCaseSensitive($newName);
    }

    public function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        $db = Database::get();

        $w = '';
        if ($viewName) {
            $w .= " AND source_table.relname = '" . $this->handleCaseSensitive($viewName) . "'";
        }
        if ($column) {
            $w .= " AND pg_attribute.attname = '" . $this->handleCaseSensitive($column) . "'";
        }

        /*
         dependent_ns.nspname as dependent_schema,
         source_ns.nspname as source_schema,
         source_table.relname as source_table,
         pg_attribute.attname as column_name,
        */

        $qu = "SELECT DISTINCT 
    dependent_view.relname as dependent_view 
    FROM pg_depend 
    JOIN pg_rewrite ON pg_depend.objid = pg_rewrite.oid 
    JOIN pg_class as dependent_view ON pg_rewrite.ev_class = dependent_view.oid 
    JOIN pg_class as source_table ON pg_depend.refobjid = source_table.oid 
    JOIN pg_attribute ON pg_depend.refobjid = pg_attribute.attrelid 
    AND pg_depend.refobjsubid = pg_attribute.attnum 
    JOIN pg_namespace dependent_ns ON dependent_ns.oid = dependent_view.relnamespace
    JOIN pg_namespace source_ns ON source_ns.oid = source_table.relnamespace
    WHERE 
    source_ns.nspname = '$schema'
    AND pg_attribute.attnum > 0 
    $w";

        #error_log($qu);

        $dep = false;
        if ($rs = lmbdb_exec($db, $qu)) {
            $dep = [];
            while (lmbdb_fetch_row($rs)) {
                $dep[] = lmbdb_result($rs, "dependent_view");
                #$dep["table"][] = lmbdb_result($rs,"source_table");
                #$dep["field"][] = lmbdb_result($rs,"column_name");
            }
        }

        return $dep;
    }

    public function renameTableSql(string $table, string $newName): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' RENAME TO ' . $this->handleCaseSensitive($newName);
    }

    public function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array
    {
        global $db;

        $odbc_table = [];
        $rs = lmbdb_tables($db, null, $schema, $table, $types);
        while (lmbdb_fetch_row($rs)) {
            $odbc_table['table_name'][] = lmbdb_result($rs, 'TABLE_NAME');
            $odbc_table['table_type'][] = lmbdb_result($rs, 'TABLE_TYPE');
            $odbc_table['table_owner'][] = lmbdb_result($rs, 'TABLE_OWNER');
        }

        if (!empty($odbc_table)) {
            return $odbc_table;
        } else {
            return false;
        }
    }

    public function dropTableSql(string $table): string
    {
        return "DROP TABLE " . $this->handleCaseSensitive($table);
    }

    public function getSequences(string $schema, ?string $prefix = null): array
    {
        $db = Database::get();

        $sequ = array();
        $rs = lmbdb_exec($db, "SELECT SEQUENCE_NAME FROM INFORMATION_SCHEMA.SEQUENCES WHERE SEQUENCE_SCHEMA = '" . $this->handleCaseSensitive($schema) . "' ORDER BY SEQUENCE_NAME");
        while (lmbdb_fetch_row($rs)) {
            $name = lmbdb_result($rs, "SEQUENCE_NAME");
            $name_ = explode('_', $name);
            if (!$prefix) {
                $prefix = 'lmb';
            }
            if (strtolower($name_[0]) == strtolower($prefix)) {
                $sequ[] = lmbdb_result($rs, "SEQUENCE_NAME");
            }
        }

        return $sequ;
    }

    public function createSequence(string $name, ?string $start = null): bool
    {
        $db = Database::get();

        #first drop sequence if exists
        $this->dropSequence($name);

        if ($start) {
            $start = ' START ' . $start;
        }
        if ($rs = lmbdb_exec($db, 'CREATE SEQUENCE ' . $this->handleCaseSensitive($name) . $start)) {
            return true;
        } else {
            return false;
        }
    }

    public function dropSequence(string $name): bool
    {
        $db = Database::get();

        $rs0 = lmbdb_exec($db, "SELECT RELNAME FROM PG_CLASS WHERE LOWER(RELNAME) = '" . lmb_strtolower(($name)) . "'");
        if (lmbdb_fetch_row($rs0) && $rs = lmbdb_exec($db, 'DROP SEQUENCE ' . $this->handleCaseSensitive($name) . ' CASCADE')) {
            return true;
        } else {
            return false;
        }
    }

    public function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed
    {
        $db = Database::get();

        /*
        if($column){
            $rs = lmbdb_columns($db,null,$schema,$this->handleCaseSensitive($table),$this->handleCaseSensitive($column));
        }else{
            $rs = lmbdb_columns($db,null,$schema,$this->handleCaseSensitive($table));
        }*/

        // workaround for matviews
        if ($getMatView && LMB_DBFUNC_MATVIEWSHANDLE) {
            $odbctable = Dbf::getTableList($GLOBALS['DBA']["DBSCHEMA"], $table, "'TABLE','VIEW','MATVIEW'");
            if ($odbctable['table_type'][0] == 'MATVIEW' and function_exists('lmbdb_psqlMatColumns')) {
                $rs = lmbdb_psqlMatColumns($db, null, $schema, $table, $column);
                $mode = null;
            }
        }

        if (!isset($rs)) {
            $rs = lmbdb_columns($db, null, $schema, $table, $column);
        }

        if ($returnRs) {
            return $rs;
        }

        while (lmbdb_fetch_row($rs)) {

            $col["tablename"][] = trim(lmbdb_result($rs, "TABLE_NAME"));
            $col["columnname"][] = trim(lmbdb_result($rs, "COLUMN_NAME"));
            $col["columnname_lower"][] = $this->handleCaseSensitive(trim(lmbdb_result($rs, "COLUMN_NAME")));
            $col["datatype"][] = trim(lmbdb_result($rs, "TYPE_NAME"));
            $col["length"][] = trim(lmbdb_result($rs, "PRECISION"));
            $col["scale"][] = trim(lmbdb_result($rs, "SCALE"));
            $col["default"][] = trim(lmbdb_result($rs, "COLUMN_DEF"));

            #$sql = "SELECT scc.column_name as \"Field\", udt_name as \"UDT\", data_type as \"Type\", is_nullable as \"Is Nullable\",keys.key as \"Key\", column_default as \"Default\"

            if ($mode) {
                $sql = "SELECT keys.key as \"Key\"
	        FROM INFORMATION_SCHEMA.COLUMNS scc LEFT JOIN
	           (SELECT table_schema, table_name, column_name, (CASE WHEN (c.contype = 'c') THEN 'CHECK'
	               WHEN (c.contype = 'f') THEN 'FOREIGN KEY'
	               WHEN (c.contype = 'p') THEN 'PRIMARY KEY'
	               WHEN (c.contype = 'u') THEN 'UNIQUE'
	               ELSE NULL END)  as key
	           FROM information_schema.constraint_column_usage col, pg_constraint c
	           WHERE table_schema = '$schema' AND table_name = '$table'
	               AND c.conname = col.constraint_name) as keys  ON scc.column_name = keys.column_name
	        WHERE scc.table_name = '$table' AND scc.column_name = '" . lmbdb_result($rs, "COLUMN_NAME") . "'";
                if ($rs1 = lmbdb_exec($db, $sql)) {
                    $col["mode"][] = lmbdb_result($rs1, "Key");
                } else {
                    $col["mode"][] = "";
                }
            }
        }

        if ($col) {
            return $col;
        } else {
            return false;
        }
    }

    public function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        if ($value || $value === 0) {
            return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ALTER ' . $this->handleCaseSensitive($column) . ' SET DEFAULT ' . $value;
        } else {
            return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ALTER ' . $this->handleCaseSensitive($column) . ' DROP DEFAULT ';
        }
    }

    public function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' RENAME ' . $this->handleCaseSensitive($column) . ' TO ' . $this->handleCaseSensitive($newName);
    }

    public function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ALTER ' . $this->handleCaseSensitive($column) . ' TYPE ' . $this->handleCaseSensitive($type);
    }

    public function dropColumnSql(string $table, array|string $columns): string
    {
        $qu = [];
        if (is_array($columns)) {
            foreach ($columns as $key => $field) {
                $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($field);
            }
        } else {
            $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($columns);
        }

        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ' . implode(',', $qu);
    }

    public function addColumnSql(string $table, string|array $column, string|array $type, string|array $default = null): string
    {
        $adf = [];
        if (is_array($column)) {
            foreach ($column as $key => $field) {
                $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($field) . ' ' . $type[$key];
                if ($default[$key]) {
                    $qu .= ' DEFAULT ' . $default[$key];
                }
                $adf[] = $qu;
            }
        } else {
            $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($column) . ' ' . $type;
            if ($default) {
                $qu .= ' DEFAULT ' . $default;
            }
            $adf[] = $qu;
        }

        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ' . implode(',', $adf);
    }

    public function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool
    {
        global $db;

        $sqlquery = "
CREATE OR REPLACE FUNCTION lmb_vkn() RETURNS trigger AS
$$

DECLARE

statement VARCHAR(1000);
nid INTEGER;

BEGIN


IF TG_ARGV[2] = '+' THEN
     nid = new.id;
END IF;
IF TG_ARGV[2] = '-' THEN
    nid = old.id;
END IF;

statement = 'update ' || TG_ARGV[0] || ' set ' || TG_ARGV[1] || ' = (select count(*) from ' || TG_RELNAME || ' where id = ' || nid || ') where id = ' || nid;
IF TG_ARGV[5] = '2' THEN
statement = 'update ' || TG_ARGV[0] || ' set ' || TG_ARGV[1] || ' = (select count(*) from ' || TG_RELNAME || ',' || TG_ARGV[3] || ' where ' || TG_RELNAME || '.id = ' || nid || ' and ' || TG_ARGV[3] || '.id = ' || TG_RELNAME || '.verkn_id and ' || TG_ARGV[3] || '.del = false) where id = ' || nid;
END IF;

EXECUTE statement;

IF TG_ARGV[4] = '' THEN
    return new;
END IF;

IF TG_ARGV[2] = '+' THEN
     nid = new.verkn_id;
END IF;
IF TG_ARGV[2] = '-' THEN
    nid = old.verkn_id;
END IF;


statement = 'update ' || TG_ARGV[3] || ' set ' || TG_ARGV[4] || ' = (select count(*) from ' || TG_RELNAME || ' where verkn_id = ' || nid || ') where id = ' || nid;
IF TG_ARGV[5] = '2' THEN
statement = 'update ' || TG_ARGV[3] || ' set ' || TG_ARGV[4] || ' = (select count(*) from ' || TG_RELNAME || ',' || TG_ARGV[0] || ' where ' || TG_RELNAME || '.verkn_id = ' || nid || ' and ' || TG_ARGV[0] || '.id = ' || TG_RELNAME || '.id and ' || TG_ARGV[0] || '.del = false) where id = ' || nid;
END IF;

EXECUTE statement;


return new;

END;
$$ 
LANGUAGE 'plpgsql';
";

        //RAISE EXCEPTION '!LMB%', statement;

        //IF TG_ARGV[2] = '0' THEN
        //    nid = new.TG_ARGV[4];
        //    statement = 'update ' || TG_ARGV[0] || ' set ' || TG_ARGV[1] || ' = (select count(*) from ' || TG_RELNAME || ' where ' || TG_ARGV[4] || ' = ' || nid || ') where id = ' || nid;
        //    return new;
        //END IF;


        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_vkn", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }

        $sqlquery = "
CREATE OR REPLACE FUNCTION lmb_lastmodified() RETURNS trigger AS '
DECLARE

statement VARCHAR(200);

BEGIN

statement = ''UPDATE LMB_CONF_TABLES SET LASTMODIFIED = CURRENT_TIMESTAMP WHERE TAB_ID = '' || TG_ARGV[0];
EXECUTE statement;

return new;

END; '  LANGUAGE 'plpgsql';
	";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_lastmodified", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }


        $sqlquery = "
CREATE OR REPLACE FUNCTION YEAR(val TIMESTAMP) RETURNS smallint AS $$
BEGIN

RETURN extract(year from val);

END; $$

LANGUAGE PLPGSQL;
	";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_lastmodified", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }


        $sqlquery = "
CREATE OR REPLACE FUNCTION MONTH(val TIMESTAMP) RETURNS smallint AS $$
BEGIN

RETURN extract(month from val);

END; $$

LANGUAGE PLPGSQL;
	";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_lastmodified", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }

        $sqlquery = "
CREATE OR REPLACE FUNCTION DAY(val TIMESTAMP) RETURNS smallint AS $$
BEGIN

RETURN extract(day from val);

END; $$

LANGUAGE PLPGSQL;
	";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_lastmodified", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }


        $sqlquery = "

CREATE OR REPLACE FUNCTION LMB_CONVERT_NUM(val VARCHAR) RETURNS NUMERIC AS $$

DECLARE x NUMERIC;
BEGIN
    x = $1::NUMERIC;
    RETURN x;
EXCEPTION WHEN others THEN
    RETURN NULL;
END;
$$
LANGUAGE PLPGSQL;

";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure LMB_CONVERT_NUM", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }


        $sqlquery = "
CREATE OR REPLACE FUNCTION LMB_CONVERT_CHAR(val NUMERIC) RETURNS VARCHAR AS $$

DECLARE x VARCHAR;
BEGIN
    x = $1::VARCHAR;
    RETURN x;
EXCEPTION WHEN others THEN
    RETURN NULL;
END;
$$
LANGUAGE PLPGSQL;
";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure LMB_CONVERT_CHAR", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }

        $sqlquery = "
CREATE
OR REPLACE FUNCTION LMB_TRY_QUERY (query VARCHAR) RETURNS BOOLEAN AS $$

BEGIN
  BEGIN
  EXECUTE query;
  return true;

  EXCEPTION WHEN OTHERS 
  THEN
  return false;
  END;

END; $$
LANGUAGE PLPGSQL;
";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure LMB_TRY_QUERY", __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }


        return true;
    }

    public function dropLimbasVknFunction(): void
    {
        $db = Database::get();
        lmbdb_exec($db, 'drop function lmb_vkn()');
    }

    public function prettyPrintTableSize(string $schema, string $table): array
    {
        $db = Database::get();

        $sqlQuery = "SELECT pg_size_pretty(pg_table_size('$table')) AS table_size, pg_table_size('$table') AS order_size";
        $rs = lmbdb_exec($db, $sqlQuery);
        return [lmbdb_result($rs, "table_size"), lmbdb_result($rs, "order_size")];
    }

    public function createMedium(array $path, string $typ): bool
    {
        return true;
    }

    public function deleteMedium(string $medium, string $typ): bool
    {
        return true;
    }

    public function backupData(string $medium, array $path, string $typ): array
    {
        global $DBA;

        $host = '';
        if ($DBA['DBHOST'] and $DBA['DBHOST'] != 'localhost') {
            $host = '-h ' . $DBA['DBHOST'];
        }

        if ($DBA['PORT']) {
            $port = '-p ' . $DBA['PORT'];
        }

        $sys = "pg_dump $host $port " . $DBA["DBCNAME"] . " -U " . $DBA["DBCUSER"] . " | gzip > " . $path["path"] . ".gz";
        exec($sys, $res, $ret);

        if (!$ret) {
            clearstatcache();
            if (file_exists($path["path"] . ".gz")) {
                $out[7] = filesize($path["path"] . ".gz");
            }
            if ($out[7] > 1000) {
                $out[0] = "OK";
                $out[10] = $path["medname"];
                return $out;
            } else {
                $out[0] = 'FALSE';
                return $out;
            }
        } else {
            $out[0] = 'FALSE';
            return $out;
        }
    }
}
