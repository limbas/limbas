<?php
/*
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\db\functions;

use Limbas\lib\db\Database;

class Hana extends DbFunction
{

    public function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed
    {
        // CHAR_AS_UTF8=true

        if ($driver == "DSN") {
            $db = lmbdb_pconnect("$database", "$user", "$password") or
            die('<BR><BR><div style="text-align:center;">Database connection failed!<BR>' . lmbdb_errormsg($db) . '</div>');
        } elseif ($driver) {
            $dsn = "Driver=$driver;ServerNode=$host;ServerDB=$database;ReadOnly=No";
            $db = lmbdb_pconnect($dsn, $user, $password);
        } else {
            $db = lmbdb_pconnect("$host:$database", "$user", "$password");
        }
        if ($db) {
            return $db;
        } else {
            echo("<h1>Database connection failed</h1><p>($dsn)<BR>" . lmbdb_errormsg() . "</p>");
            return false;
        }

    }

    public function convertToDbTimestamp(mixed $date, ?bool $withoutTime = false): string
    {
        if ($withoutTime) {
            if (!is_object($date)) {
                return date("Y-m-d", 1);
            }
            return $date->format("Y-m-d");
        } else {
            if (!is_object($date)) {
                return date("Y-m-d H:i:s.", 1);
            }
            return $date->format("Y-m-d H:i:s.");
        }
    }

    public function convertFromDbTimestamp(string $value): false|int
    {
        // TODO: Implement convertFromDbTimestamp() method.
        return false;
    }

    public function parseBlob(string $value): string
    {
        return $this->parseString($value);
    }

    public function parseString(string $value): string
    {
        return str_replace("'", "''", $value);
    }

    public function getSequence(string $name): mixed
    {
        $rs = Database::query("SELECT " . $this->handleCaseSensitive($name) . ".NEXTVAL AS NEXTSEQ FROM DUMMY");
        return lmbdb_result($rs, "NEXTSEQ");
    }

    public function handleCaseSensitive(string $value): string
    {
        return lmb_strtoupper($value);
    }

    public function sqlTimeDiff(string $startColumn, string $endColumn): string
    {
        return "TIMEDIFF(" . $startColumn . "," . $endColumn . ")";
    }

    public function sqlDateDiff(string $startColumn, string $endColumn): string
    {
        return "DATEDIFF(" . $startColumn . "," . $endColumn . ")";
    }

    public function calculateChecksum(string $field, ?int $type = null): string
    {
        // TODO: Implement calculateChecksum() method.
        return '';
    }

    public function setVariables(): void
    {
        // TODO: Implement setVariables() method.
        return;
    }

    public function version(?array $DBA = null): array
    {
        $sql = "SELECT VALUE FROM SYS.M_SYSTEM_OVERVIEW WHERE NAME = 'Version'";
        $rs = Database::query($sql);
        $version = explode(' ', lmbdb_result($rs, "VALUE"));
        $version_[] = $version[0] . ' ' . $version[1];
        $version_[] = $version[0];

        // encoding
        if ($DBA) {
            $version_[] = 'UTF-8';
        }

        return $version_;
    }

    public function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        $sql = "SELECT TABLE_NAME AS TABLENAME,
				COLUMN_NAME AS COLUMNNAME,
				INDEX_NAME AS INDEXNAME,
                CASE WHEN CONSTRAINT = 'PRIMARY KEY' THEN 1 ELSE 0 END AS IS_UNIQUE
			FROM 
				SYS.INDEX_COLUMNS
			WHERE 
				SCHEMA_NAME = '" . $schema . "'";

        if ($tableName) {
            $sql .= " AND TABLE_NAME = '" . lmb_strtoupper($tableName) . "'";
        }
        if ($indexName) {
            $sql .= " AND INDEX_NAME = '" . lmb_strtoupper($indexName) . "'";
        }
        if ($columnName) {
            $sql .= "AND COLUMN_NAME = '" . lmb_strtoupper($columnName) . "'";
        }
        if ($noPrimary) {
            $sql .= "AND CONSTRAINT IS NULL";
        }

        $sql .= " ORDER BY TABLE_NAME,INDEX_NAME";

        return $sql;
    }

    public function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        if ($isUnique) {
            $unique = 'UNIQUE';
        }
        return "CREATE $unique INDEX " . $indexName . " ON " . $tableName . "(" . $columnName . ")";
    }

    public function dropIndexSql(string $indexName, string $tableName): string
    {
        return "DROP INDEX " . $indexName;
    }

    public function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        $sql = "SELECT TABLE_NAME,
				COLUMN_NAME,
				CONSTRAINT_NAME
			FROM 
				SYS.CONSTRAINTS
			WHERE 
			    IS_PRIMARY_KEY = 'TRUE'
				AND SCHEMA_NAME = '" . $schema . "'";

        if ($table) {
            $sql .= " AND TABLE_NAME = '" . $this->handleCaseSensitive($table);
        }
        if ($column) {
            $sql .= " AND COLUMN_NAME = '" . $this->handleCaseSensitive($column);
        }

        $sql .= "ORDER BY TABLE_NAME,COLUMN_NAME";

        $constraint = [];
        $rs = Database::query($sql);
        while (lmbdb_fetch_row($rs)) {
            $constraint["TABLE_NAME"][] = lmbdb_result($rs, "TABLE_NAME");
            $constraint["COLUMN_NAME"][] = lmbdb_result($rs, "COLUMN_NAME");
            $constraint["PK_NAME"][] = lmbdb_result($rs, "CONSTRAINT_NAME");
        }

        return $constraint;
    }

    public function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array
    {
        $sql = "SELECT TABLE_NAME,
				COLUMN_NAME,
				CONSTRAINT_NAME
			FROM 
				SYS.CONSTRAINTS
			WHERE 
			    IS_PRIMARY_KEY = 'FALSE'
			    AND IS_UNIQUE_KEY = 'TRUE'
				AND SCHEMA_NAME = '" . $schema . "'";

        if ($table) {
            $sql .= " AND TABLE_NAME = '" . $this->handleCaseSensitive($table) . "'";
        }
        if ($column) {
            $sql .= " AND COLUMN_NAME = '" . $this->handleCaseSensitive($column) . "'";
        }

        $sql .= " ORDER BY TABLE_NAME,COLUMN_NAME";

        $constraint = [];
        $rs = Database::query($sql);
        while (lmbdb_fetch_row($rs)) {
            $constraint["TABLE_NAME"][] = lmbdb_result($rs, "TABLE_NAME");
            $constraint["COLUMN_NAME"][] = lmbdb_result($rs, "COLUMN_NAME");
            $constraint["PK_NAME"][] = lmbdb_result($rs, "CONSTRAINT_NAME");
        }
        return $constraint;
    }

    public function createPrimaryKeySql(string $table, string $column): string
    {
        return "ALTER TABLE " . $table . " ADD PRIMARY KEY (" . $this->handleCaseSensitive($column) . ")";
    }

    public function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        return "ALTER TABLE " . $table . " ADD CONSTRAINT " . $this->handleCaseSensitive($constraintName) . " UNIQUE (" . $this->handleCaseSensitive($column) . ")";
    }

    public function dropPrimaryKeySql(string $table): string
    {
        echo "ALTER TABLE " . $table . " DROP PRIMARY KEY";
        return "ALTER TABLE " . $table . " DROP PRIMARY KEY";
    }

    public function dropConstraintSql(string $table, string $constraintName): string
    {
        return "ALTER TABLE $table DROP CONSTRAINT " . $this->handleCaseSensitive($constraintName);
    }

    public function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        $sql = "SELECT TABLE_NAME AS TABLENAME,
				COLUMN_NAME AS COLUMNNAME,
				REFERENCED_TABLE_NAME AS REFTABLENAME,
				REFERENCED_COLUMN_NAME AS REFCOLUMNNAME,
				CONSTRAINT_NAME AS FKEYNAME,
				DELETE_RULE AS RULE
			FROM 
				SYS.REFERENTIAL_CONSTRAINTS 
			WHERE 
				SCHEMA_NAME = '" . $schema . "'";

        if ($table) {
            $sql .= " AND TABLE_NAME = '" . lmb_strtoupper($table) . "'";
        }
        if ($column) {
            $sql .= " AND CONSTRAINT_NAME LIKE '" . lmb_strtoupper($column) . "'";
        }

        return $sql;
    }

    public function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        if (!$restrict) {
            $restrict = "RESTRICT";
        }

        return "ALTER TABLE " . $parentTable . " 
		ADD CONSTRAINT " . $keyName . " FOREIGN KEY (" . $parentColumn . ") 
		REFERENCES " . $childTable . "(" . $childColumn . ") 
		ON DELETE " . $restrict;
    }

    public function dropForeignKeySql(string $table, string $keyName): string
    {
        return " ALTER TABLE " . $table . " DROP CONSTRAINT " . $keyName;
    }

    public function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        $sql = "SELECT TRIGGER_NAME,DEFINITION,SUBJECT_TABLE_NAME,TRIGGER_EVENT
		FROM
		 	SYS.TRIGGERS 
		WHERE SCHEMA_NAME = '" . $schema . "' AND TRIGGER_NAME NOT LIKE '_SYS_TRIGGER%';
		 	";
        if ($triggerName) {
            $sql .= " WHERE LOWER(TRIGGERNAME) LIKE '" . lmb_strtolower($triggerName) . "'";
        }

        $res = [];
        $rs = Database::query($sql);
        while (lmbdb_fetch_row($rs)) {
            $res["triggername"][] = lmbdb_result($rs, "TRIGGER_NAME");
            $res["definition"][] = lmbdb_result($rs, "DEFINITION");
            $res["tablename"][] = lmbdb_result($rs, "SUBJECT_TABLE_NAME");
            $res["event"][] = lmbdb_result($rs, "TRIGGER_EVENT");
        }

        return $res;
    }

    public function dropTriggerSql(string $table, string $triggerName): string
    {
        return "DROP TRIGGER " . $triggerName;
    }

    public function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        $sql = "CREATE TRIGGER " . $schema . "." . $triggerName . " AFTER " . $action . " ON " . $schema . "." . $table . " 
        REFERENCING OLD ROW OLD, NEW ROW NEW
        FOR EACH ROW
        BEGIN 
        " . $value . "
        END;";
        return $sql;
    }

    public function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string
    {
        global $DBA;
        if ($action == "+") {
            $a_id = ":NEW.ID";
            $a_vidd = ":NEW.VERKN_ID";
        } else {
            $a_id = ":OLD.ID";
            $a_vidd = ":OLD.VERKN_ID";
        }

        if ($childColumn) {
            $t = "UPDATE " . $DBA["DBSCHEMA"] . "." . $childTable . " SET " . $childColumn . " = (SELECT COUNT(*) FROM " . $DBA["DBSCHEMA"] . "." . $relationTable . " WHERE VERKN_ID = $a_vidd) where id = $a_vidd;";
        } else {
            $t = "UPDATE " . $DBA["DBSCHEMA"] . "." . $parentTable . " SET " . $parentColumn . " = (SELECT COUNT(*) FROM " . $DBA["DBSCHEMA"] . "." . $relationTable . " WHERE ID = $a_id) where id = $a_id;";
        }

        return $t;
    }

    public function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        return "UPDATE LMB_CONF_TABLES SET LASTMODIFIED = CURRENT_TIMESTAMP WHERE TAB_ID = " . $tabId . ";";
    }

    public function getViewDefinitionSql(string $schema, string $viewName): string
    {
        return "SELECT VIEWNAME, DEFINITION 
			FROM 
				DOMAIN.VIEWDEFS
			WHERE 
				OWNER = '" . $schema . "'
				AND VIEWNAME = '" . $this->handleCaseSensitive($viewName) . "'";
    }

    public function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        $qu = "SELECT VIEWNAME,TABLENAME 
		FROM 
			DOMAIN.VIEWTABLES 
		WHERE 
			OWNER = '" . $schema . "'";
        if ($viewName) {
            $qu .= " AND LOWER(VIEWNAME) = '" . lmb_strtolower($viewName) . "'";
        }
        return $qu;
    }

    public function createViewSql(string $viewName, string $definition): string
    {
        if (lmb_stripos($definition, "CREATE VIEW") !== false) {
            $qu = $definition;
        } else {
            $qu = "CREATE OR REPLACE VIEW " . $viewName . " AS (" . rtrim(trim($definition), ";") . ")";
        }

        return $qu;
    }

    public function dropViewSql(string $viewName): string
    {
        $qu = "DROP VIEW " . $viewName;

        return $qu;
    }

    public function renameViewSql(string $viewName, string $newName): string
    {
        return "RENAME VIEW " . $this->handleCaseSensitive($viewName) . " TO " . $this->handleCaseSensitive($newName);
    }

    public function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        // TODO: Implement getViewDependencies() method.
        return false;
    }

    public function renameTableSql(string $table, string $newName): string
    {
        return "RENAME TABLE " . $this->handleCaseSensitive($table) . " TO " . $this->handleCaseSensitive($newName);
    }

    public function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array
    {
        $db = Database::get();

        $name = '%';
        $type = "'TABLE','VIEW'";

        if ($table) {
            $name = $this->handleCaseSensitive($table);
        }
        if ($types) {
            $type = $types;
        }

        $rs = lmbdb_tables($db, null, $schema, $name, $type);
        $odbc_table = [];
        while (lmbdb_fetch_row($rs)) {
            $odbc_table["table_name"][] = lmbdb_result($rs, "TABLE_NAME");
            $odbc_table["table_type"][] = lmbdb_result($rs, "TABLE_TYPE");
            $odbc_table["table_owner"][] = lmbdb_result($rs, "TABLE_OWNER");
        }

        if (empty($odbc_table)) {
            return false;
        } else {
            return $odbc_table;
        }
    }

    public function dropTableSql(string $table): string
    {
        $qu = "DROP TABLE " . $this->handleCaseSensitive($table);

        return $qu;
    }

    public function getSequences(string $schema): array
    {
        // TODO: Implement getSequences() method.
        return [];
    }

    public function createSequence(string $name, ?string $start = null): bool
    {

        #first drop sequence if exists
        $this->dropSequence($name);

        if ($start) {
            $start = " START WITH " . $start;
        }
        if ($rs = Database::query("CREATE SEQUENCE " . $this->handleCaseSensitive($name) . $start)) {
            return true;
        } else {
            return false;
        }
    }

    public function dropSequence(string $name): bool
    {
        if ($rs = Database::query("DROP SEQUENCE " . $this->handleCaseSensitive($name))) {
            return true;
        } else {
            return false;
        }
    }

    public function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed
    {
        $db = Database::get();

        if ($returnRs) {
            if ($column) {
                $rs = lmbdb_columns($db, null, $schema, $this->handleCaseSensitive($table), $this->handleCaseSensitive($column));
            } else {
                $rs = lmbdb_columns($db, null, $schema, $this->handleCaseSensitive($table));
            }
            return $rs;
        }

        $sql = "SELECT SYS.TABLE_COLUMNS.TABLE_NAME, SYS.TABLE_COLUMNS.COLUMN_NAME , SYS.TABLE_COLUMNS.DATA_TYPE_NAME, SYS.TABLE_COLUMNS.LENGTH, SYS.TABLE_COLUMNS.SCALE, SYS.TABLE_COLUMNS.SCALE, SYS.TABLE_COLUMNS.DEFAULT_VALUE, SYS.CONSTRAINTS.IS_PRIMARY_KEY
	FROM SYS.TABLE_COLUMNS LEFT OUTER JOIN SYS.CONSTRAINTS ON (SYS.TABLE_COLUMNS.TABLE_NAME = SYS.CONSTRAINTS.TABLE_NAME AND SYS.TABLE_COLUMNS.COLUMN_NAME = SYS.CONSTRAINTS.COLUMN_NAME)
	AND  SYS.TABLE_COLUMNS.SCHEMA_NAME = '" . $schema . "' AND SYS.TABLE_COLUMNS.TABLE_NAME = '" . lmb_strtoupper($table) . "'";
        if ($column) {
            $sql .= " AND SYS.TABLE_COLUMNS.COLUMN_NAME = '" . lmb_strtoupper($column) . "'";
        }

        $rs = lmbdb_exec($db, $sql);
        while (lmbdb_fetch_row($rs)) {
            $col["tablename"][] = $this->handleCaseSensitive(lmbdb_result($rs, "TABLE_NAME"));
            $col["columnname"][] = $this->handleCaseSensitive(lmbdb_result($rs, "COLUMN_NAME"));
            $col["columnname_lower"][] = $this->handleCaseSensitive(trim(lmbdb_result($rs, "COLUMN_NAME")));
            $col["datatype"][] = lmbdb_result($rs, "DATA_TYPE_NAME");
            $col["length"][] = lmbdb_result($rs, "LENGTH");
            $col["scale"][] = trim(lmbdb_result($rs, "SCALE"));
            $col["default"][] = lmbdb_result($rs, "DEFAULT_VALUE");
            $col["mode"][] = lmbdb_result($rs, "IS_PRIMARY_KEY");
        }

        if (!empty($col)) {
            return $col;
        } else {
            return false;
        }
    }

    private function isValidFieldTypeExtension(string $fieldTypeExtension): bool
    {
        $hasext = array('NVARCHAR', 'REAL', 'DECIMAL', 'ASCII', 'BYTE', 'SERIAL');

        return in_array(strtoupper($fieldTypeExtension), $hasext);
    }


    public function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        // get field type
        $ftype_ = $this->getColumns($schema, $table, $column);


        if ($this->isValidFieldTypeExtension($ftype_['datatype'])) {
            if ($ftype_['scale']) {
                $ftype = $ftype_['datatype'] . '(' . $ftype_['length'] . ',' . $ftype_['scale'] . ')';
            } else {
                $ftype = $ftype_['datatype'] . '(' . $ftype_['length'] . ')';
            }
        } else {
            $ftype = $ftype_['datatype'];
        }

        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ALTER (" . $this->handleCaseSensitive($column) . " $ftype DEFAULT " . $value . ")";
    }

    public function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        return "RENAME COLUMN " . $this->handleCaseSensitive($table) . "." . $this->handleCaseSensitive($column) . " TO " . $this->handleCaseSensitive($newName);
    }

    public function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ALTER (" . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($type) . ')';
    }

    public function dropColumnSql(string $table, array|string $columns): string
    {
        if (is_array($columns)) {
            foreach ($columns as $key => $field) {
                $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($field);
            }
        } else {
            $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($columns);
        }

        return 'ALTER TABLE ' . $this->handleCaseSensitive($table) . ' ' . implode(',', $qu);
    }

    public function addColumnSql(string $table, array|string $column, array|string $type, array|string $default = null): string
    {
        if (is_array($column)) {
            foreach ($column as $key => $field) {
                $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' (' . $this->handleCaseSensitive($field) . ' ' . $type[$key];
                if ($default[$key]) {
                    $qu .= ' DEFAULT ' . $default[$key];
                }
                $qu .= ')';
                $adf[] = $qu;
            }
        } else {
            $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' (' . $this->handleCaseSensitive($column) . ' ' . $type;
            if ($default) {
                $qu .= ' DEFAULT ' . $default;
            }
            $qu .= ')';
            $adf[] = $qu;
        }

        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " " . implode(',', $adf);
    }

    // todo - not suppported with triggers!

    public function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool
    {
        # drop procedure
        if ($dropOldProcedure) {
            $this->dropLimbasVknFunction($schema);
        }

        $sqlquery = "
CREATE PROCEDURE " . $schema . ".lmb_vkn(in id DECIMAL(16),in vid DECIMAL(16), in tabname nvarchar(60), in fieldname nvarchar(30), in act nvarchar(1) , in rtabname nvarchar(60), in rfieldname nvarchar(30), in vtabname nvarchar(30)) 
Language SQLScript

AS
BEGIN

EXEC 'update' ||:tabname|| ' set '||:fieldname||' = (select count(*) from '||:vtabname||' where id = '||:id||' ) where id = '||:id;

IF :rtabname <> '' THEN
EXEC 'update ' || :rtabname || ' set ' || :rfieldname || ' = (select count(*) from ' || :vtabname || ' where verkn_id = '|| :vid ||') where id = ' || :vid;
END IF;

End;
";

        if ($rs = Database::query($sqlquery)) {
            return true;
        } else {
            return false;
        }
    }

    public function dropLimbasVknFunction(string $schema = ''): void
    {
        $sqlquery = "DROP PROCEDURE " . $schema . ".lmb_vkn";
        $rs = Database::query($sqlquery);
    }

    public function prettyPrintTableSize(string $schema, string $table): array
    {
        // TODO: Implement prettyPrintTableSize() method.
        return ["", ""];
    }

    public function createMedium(array $path, string $typ): bool
    {
        global $DBA;

        if ($DBA["LMHOST"] == $DBA["DBHOST"] or $DBA["DBHOST"] == "127.0.0.1" or $DBA["DBHOST"] == "localhost") {
            $bu["path"] = $DBA["DBPATH"];
        } else {
            $bu["path"] = "ssh " . $DBA["DBHOST"] . " " . $DBA["DBPATH"];
        }

        $sys = $bu['path'] . "/dbmcli -n " . $DBA["DBHOST"] . " -d " . $DBA["DBNAME"] . " -u " . $DBA["DBCUSER"] . "," . $DBA["DBCPASS"] . " medium_put " . $path["medname"] . " " . $path["path"] . " " . $path["type"] . " $typ " . $path["size"] . " 0 " . $path["over"];
        $out = `$sys`;
        $out = explode("\n", $out);
        if ($out[0] == "OK") {
            return $path['medname'];
        } else {
            if (function_exists("fill_history")) {
                fill_history($out, $path, $typ);
            }
            return false;
        }
    }

    public function deleteMedium(string $medium, string $typ): bool
    {
        global $DBA;

        if ($DBA["LMHOST"] == $DBA["DBHOST"] or $DBA["DBHOST"] == "127.0.0.1" or $DBA["DBHOST"] == "localhost") {
            $bu["path"] = $DBA["DBPATH"];
        } else {
            $bu["path"] = "ssh " . $DBA["DBHOST"] . " " . $DBA["DBPATH"];
        }

        $sys = $bu["path"] . "/dbmcli -n " . $DBA["DBHOST"] . " -d " . $DBA["DBNAME"] . " -u " . $DBA["DBCUSER"] . "," . $DBA["DBCPASS"] . " medium_delete " . $medium;
        $out = `$sys`;
        $out = explode("\n", $out);
        if ($out[0] == "OK") {
            return true;
        } else {
            if (function_exists("fill_history")) {
                fill_history($out, $path, $typ);
            }
            return false;
        }
    }

    public function backupData(string $medium, array $path, string $typ): array
    {
        global $DBA;

        if ($DBA["LMHOST"] == $DBA["DBHOST"] or $DBA["DBHOST"] == "127.0.0.1" or $DBA["DBHOST"] == "localhost") {
            $bu["path"] = $DBA["DBPATH"];
        } else {
            $bu["path"] = "ssh " . $DBA["DBHOST"] . " " . $DBA["DBPATH"];
            $bu["sshpath"] = "ssh " . $DBA["DBHOST"];
        }

        $sys = $bu["path"] . "/dbmcli -n " . $DBA["DBHOST"] . " -d " . $DBA["DBNAME"] . " -uUTL -u " . $DBA["DBCUSER"] . "," . $DBA["DBCPASS"] . " backup_start $medium RECOVERY $typ";
        exec($sys, $out, $ret);

        if ($out[0] == "OK") {

            foreach ($out as $key => $value) {
                if ($value) {
                    $det = explode(" ", $value);
                    $outres[] = $det[lmb_count($det) - 1];
                }
            }

            if ($typ == "DATA") {
                $outres[7] = ($outres[7] * 2);
            } elseif ($typ == "PAGES") {
                $outres[7] = ($outres[7] / 4);
            }

            # --- zippen ---
            $sys1 = trim($bu["sshpath"] . " gzip " . $path["path"]);
            exec($sys1, $out1, $ret1);
            if (file_exists($bu["sshpath"] . ".gz")) {
                $outres[] = "gzip ok";
            } else {
                $outres[] = "gzip false";
            }
            return $outres;
        } else {
            if (function_exists("fill_history")) {
                fill_history($out, $path, $typ);
            }
            return false;
        }
    }
}