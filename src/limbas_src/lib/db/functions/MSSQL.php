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
use Symfony\Component\VarDumper\Cloner\Data;

class MSSQL extends DbFunction
{

    public function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed
    {
        if ($driver) {
            $dsn = "Driver=$driver;Server=$host;Database=$database;ReadOnly=No";
            #SQL_CUR_USE_ODBC
            #SQL_CUR_DEFAULT
            #SQL_CUR_USE_DRIVER
            @$db = lmbdb_pconnect($dsn, $user, $password);
        } else {
            @$db = lmbdb_pconnect($database, $user, $password);
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
                return date("Y-m-d H:i:s", 1);
            }
            return $date->format("Y-m-d H:i:s");
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
        // TODO: Implement getSequence() method.
        return null;
    }

    public function handleCaseSensitive(string $value): string
    {
        return lmb_strtoupper($value);
    }

    public function sqlTimeDiff(string $startColumn, string $endColumn): string
    {
        return "(" . $endColumn . "-" . $startColumn . ")";
    }

    public function sqlDateDiff(string $startColumn, string $endColumn): string
    {
        return "(" . $endColumn . "-" . $startColumn . ")";
    }

    public function calculateChecksum(string $field, ?int $type = null): string
    {
        // TODO: Implement calculateChecksum() method.
        return '';
    }

    public function setVariables(): void
    {
        // TODO: Implement setVariables() method?
        return;
    }

    public function version(?array $DBA = null): array
    {
        // TODO: Implement version() method.
        return [0];
    }

    public function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        $sql = "SELECT allobj.name AS TABLENAME,
	               col.name AS COLUMNNAME,
	               ind.name AS INDEXNAME,
	               ind.is_unique AS IS_UNIQUE,
	               0 INDEX_USED
            FROM sys.indexes ind,
                 sys.all_objects allobj,
                 sys.schemas schemas,
                 sys.index_columns indcol,
                 sys.columns col
            WHERE ind.object_id = allobj.object_id
              AND schemas.name = '" . $schema . "'
              AND schemas.schema_id = allobj.schema_id
              AND ind.object_id = indcol.object_id
              AND ind.object_id = col.object_id
              AND indcol.column_id = col.column_id
              AND ind.index_id = indcol.index_id
              ";


        // TYPE ??


        if ($tableName) {
            //$sql .= " AND LOWER(TABLENAME) = '".lmb_strtolower($tableName)."'";
            $sql .= " AND LOWER(allobj.name) = '" . lmb_strtolower($tableName) . "'";
        }
        if ($indexName) {
            //$sql .= " AND LOWER(COLUMNNAME) = '".lmb_strtolower($indexName)."'";
            $sql .= " AND LOWER(ind.name) = '" . lmb_strtolower($indexName) . "'";
        }
        if ($columnName) {
            //$sql .= " AND LOWER(COLUMNNAME) = '".lmb_strtolower($indexName)."'";
            $sql .= " AND LOWER(col.name) = '" . lmb_strtolower($columnName) . "'";
        }
        if ($indexPrefix) {
            $sql .= " AND LOWER(ind.name) LIKE '" . lmb_strtolower($indexPrefix) . "%'";
        }

        if ($noPrimary) {
            $sql .= "AND ind.is_primary_key = 0";
        } else {
            $sql .= "AND ind.is_primary_key = 1";
        }

        $sql .= " ORDER BY allobj.name, ind.name";

        return $sql;
    }

    public function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        return "CREATE INDEX " . $indexName . " ON " . $tableName . "(" . $columnName . ")";
    }

    public function dropIndexSql(string $indexName, string $tableName): string
    {
        //return "DROP INDEX ".$indexName;

        return "DROP INDEX " . $indexName . " ON " . $tableName;
    }

    public function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        $sql = "SELECT
	    TC.CONSTRAINT_NAME, TC.TABLE_NAME, KCU.COLUMN_NAME, 
	    CCU.TABLE_NAME AS FOREIGN_TABLE_NAME,
	    CCU.COLUMN_NAME AS FOREIGN_COLUMN_NAME,
	    CONSTRAINT_TYPE 
	FROM 
	    INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS TC 
	    JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU ON TC.CONSTRAINT_NAME = KCU.CONSTRAINT_NAME
	    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE AS CCU ON CCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
	WHERE CONSTRAINT_TYPE = 'PRIMARY KEY'";

        if ($table) {
            $sql .= " AND TC.TABLE_NAME = '" . $this->handleCaseSensitive($table) . "'";
        }
        if ($column) {
            $sql .= " AND KCU.COLUMN_NAME = '" . $this->handleCaseSensitive($column) . "'";
        }

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
        /**
         * #Name des primary key einer Tabelle herausfinden:
         * select * from INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_TYPE = 'PRIMARY KEY' AND TABLE_NAME = '<table_name>';
         *
         * #primary key löschen:
         * ALTER TABLE <table_name> DROP CONSTRAINT <primary_key_name>
         *
         * #primary key hinzufügen:
         * ALTER TABLE <table_name> ADD constraint <primary_key_name> PRIMARY KEY (<spalten_name>)
         **/

        return "ALTER TABLE " . $table . " ADD PRIMARY KEY (" . $column . ")";
    }

    public function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        return "ALTER TABLE " . $table . " ADD CONSTRAINT " . $constraintName . " UNIQUE (" . $column . ")";
    }

    public function dropPrimaryKeySql(string $table): string
    {
        return "ALTER TABLE " . $table . " DROP PRIMARY KEY";
    }

    public function dropConstraintSql(string $table, string $constraintName): string
    {
        return "ALTER TABLE " . $table . " DROP CONSTRAINT " . $constraintName;
    }

    public function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        /*$sql = "Select TABLENAME,
				COLUMNNAME,
				REFTABLENAME,
				REFCOLUMNNAME,
				NAME as FKEYNAME,
				delete_referential_action_desc AS RULE
			FROM 
				sys.foreign_keys
			WHERE 
				OWNER = '".$schema."'";*/


        $sql = "Select allobj.name AS TABLENAME,
                   col.name AS COLUMNNAME,
                   allobjects.name AS REFTABLENAME,
                   cols.name AS REFCOLUMNNAME,
                   forkey.name AS FKEYNAME,
                   forkey.delete_referential_action_desc AS LMB_RULE

            from sys.foreign_key_columns forkeycol,
                 sys.foreign_keys forkey,
                 sys.all_objects allobj,
                 sys.all_objects allobjects,
                 sys.columns col,
                 sys.columns cols

            where forkeycol.parent_object_id = allobj.object_id
              AND forkeycol.constraint_object_id = forkey.object_id
              AND forkeycol.referenced_object_id = allobjects.object_id
              AND forkeycol.parent_object_id = col.object_id
              AND forkeycol.parent_column_id = col.column_id
              AND forkeycol.referenced_object_id = cols.object_id
              AND forkeycol.referenced_column_id = cols.column_id";

        if ($table) {
            //$sql .= " AND LOWER(TABLENAME) = '".lmb_strtolower($table)."'";
            $sql .= " AND LOWER(allobj.name) = '" . lmb_strtolower($table) . "'";
        }
        if ($column) {
            //$sql .= " AND LOWER(FKEYNAME) LIKE '".lmb_strtolower($column)."'";
            $sql .= "AND LOWER(forkey.name) LIKE '" . lmb_strtolower($column) . "'";
        }

        return $sql;
    }

    public function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        if (!$restrict) {
            $restrict = "RESTRICT";
        }

        /*return "ALTER TABLE ".$parentTable." 
            ADD FOREIGN KEY ".$keyName." (".$parentColumn.") 
            REFERENCES ".$childTable." 
            ON ".$restrict;*/

        return "ALTER TABLE " . $parentTable . " ADD CONSTRAINT " . $keyName . " FOREIGN KEY (" . $parentColumn . ") REFERENCES " . $childTable . " (" . $childColumn . ") ON DELETE " . $restrict . ";";
    }

    public function dropForeignKeySql(string $table, string $keyName): string
    {
        //return " ALTER TABLE ".$table." DROP FOREIGN KEY ".$keyName;

        return "ALTER TABLE " . $table . " DROP CONSTRAINT " . $keyName . ";";
    }

    public function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        /*	$sql = "SELECT TRIGGERNAME,DEFINITION,TABLENAME,INSERT,UPDATE,DELETE
                FROM
                     DOMAIN.TRIGGERS"; */
        /*$sql = "SELECT tr.name AS TRIGGERNAME, object_definition(tr.object_id) AS DEFINITIONdefinition , allobj.name 
                from sys.triggers tr, sys.all_objects allobj 
                where tr.type='TR' AND tr.parent_id = allobj.object_id";*/

        $sql = "SELECT tr.name AS TRIGGERNAME, object_definition(tr.object_id) AS DEFINITION, allobj.name AS TABLENAME, trev.type_desc AS TRIGGERTYPE
            from sys.trigger_events trev, sys.triggers tr, sys.all_objects allobj 
            where trev.object_id = tr.object_id AND tr.parent_id = allobj.object_id AND tr.type = 'TR'";

        if ($triggerName) {
            //$sql .= " WHERE LOWER(TRIGGERNAME) LIKE '".lmb_strtolower($triggerName)."'";
            $sql .= " AND LOWER(tr.name) LIKE '" . lmb_strtolower($triggerName) . "'";
        }


        $rs = Database::query($sql);
        while (lmbdb_fetch_row($rs)) {
            $res["triggername"][] = lmbdb_result($rs, "TRIGGERNAME");
            $res["definition"][] = lmbdb_result($rs, "DEFINITION");
            $res["tablename"][] = lmbdb_result($rs, "TABLENAME");
            $res["event"][] = lmbdb_result($rs, "TRIGGERTYPE");
        }


        return $res;
    }

    public function dropTriggerSql(string $table, string $triggerName): string
    {
        //return "DROP TRIGGER ".$triggerName." 
        //	OF ".$table;
        return "DROP TRIGGER " . $triggerName;
    }

    public function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        //$sql = "CREATE TRIGGER ".$triggerName." FOR ".$schema.".".$table. " AFTER ".$action." EXECUTE (".$value.")";
        $sql = "CREATE TRIGGER " . $triggerName . " ON " . $table . " " . $position . " " . $action . " AS BEGIN " . $value . " END";
        return $sql;
    }

    public function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string
    {
        if ($action == "+") {
            //{$act = ":NEW.ID,:NEW.VERKN_ID";}
            if ($childColumn == null) {
                return "DECLARE @id int;

     	            SET @id = (select distinct ID from inserted);
	                EXEC lmb_vkn @id, '" . $schema . "." . $parentTable . "', '" . $parentColumn . "', '" . $schema . "." . $relationTable . "', 0, '" . $schema . "." . $childTable . "', '" . $childColumn . "';";
            } else {
                return "DECLARE @id int;
	                DECLARE @verkn_id int;

	                SET @id = (select distinct ID from inserted);
	                SET @verkn_id = (select VERKN_ID from inserted);
	                EXEC lmb_vkn @id, '" . $schema . "." . $parentTable . "', '" . $parentColumn . "', '" . $schema . "." . $relationTable . "', @verkn_id, '" . $schema . "." . $childTable . "', '" . $childColumn . "';";
            }
        } else {
            //{$act = ":OLD.ID,:OLD.VERKN_ID";}
            if ($childColumn == null) {
                return "DECLARE @id int;
	               DECLARE @verkn_id int;
	    
	               SET @id = (select distinct ID from deleted);
	               EXEC lmb_vkn @id, '" . $schema . "." . $parentTable . "', '" . $parentColumn . "', '" . $schema . "." . $relationTable . "', 0, '" . $schema . "." . $childTable . "', '" . $childColumn . "';";
            } else {
                return "DECLARE @id int;
	               DECLARE @verkn_id int;
	    
	               SET @id = (select distinct ID from deleted);
	               SET @verkn_id = (select VERKN_ID from deleted);
	               EXEC lmb_vkn @id, '" . $schema . "." . $parentTable . "', '" . $parentColumn . "', '" . $schema . "." . $relationTable . "', @verkn_id, '" . $schema . "." . $childTable . "', '" . $childColumn . "';";
            }
        }
        //return "Call ".$DBA["DBSCHEMA"].".lmb_vkn(".$act.",'".$schema."','".$relationTable."','".$parentTable."','".$parentColumn."','".$action."');";
    }

    public function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        // TODO: Implement lastModifiedTriggerFunctionSql() method.
        #return "UPDATE LMB_CONF_TABLES SET LASTMODIFIED = CURRENT_TIMESTAMP WHERE TAB_ID = ".$tabId.";";
        return '';
    }

    public function getViewDefinitionSql(string $schema, string $viewName): string
    {
        /*return "SELECT VIEWNAME, DEFINITION 
			FROM 
				DOMAIN.VIEWDEFS
			WHERE 
				OWNER = '".$schema."'
				AND VIEWNAME = '".$this->handleCaseSensitive($viewName)."'";
	    */
        return "SELECT views.name AS VIEWNAME,
                   object_definition(views.object_id) AS DEFINITION
            FROM sys.all_views views,
                 sys.all_objects allobj,
                 sys.schemas schemas
            WHERE views.object_id = allobj.object_id
              AND schemas.name = '" . $schema . "'
              AND schemas.schema_id = allobj.schema_id
              AND views.name = '" . $this->handleCaseSensitive($viewName) . "'";
    }

    public function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        /*$qu = "SELECT VIEWNAME,TABLENAME 
                FROM
                        DOMAIN.VIEWTABLES 
                WHERE 
                        OWNER = '".$schema."'";
        if($viewName){
                $qu .= " AND LOWER(VIEWNAME) = '".lmb_strtolower($viewName)."'";
        }*/

        $qu = "select VIEW_NAME AS VIEWNAME,
                  TABLE_NAME AS TABLENAME
           FROM INFORMATION_SCHEMA.VIEW_TABLE_USAGE
           WHERE TABLE_SCHEMA = '" . $schema . "'";
        if ($viewName) {
            $qu .= " AND LOWER(VIEW_NAME) = '" . lmb_strtolower($viewName) . "'";
        }

        return $qu;
    }

    public function createViewSql(string $viewName, string $definition): string
    {
        if (lmb_stripos($definition, "CREATE VIEW") !== false) {
            $qu = $definition;
        } else {

            $sqlquery1 = $this->dropViewSql($viewName);
            $rs1 = Database::query($sqlquery1);

            $qu = "CREATE VIEW " . $viewName . " AS (" . rtrim(trim($definition), ";") . ")";
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
        return "EXEC sp_rename '" . $this->handleCaseSensitive($viewName) . "', '" . $this->handleCaseSensitive($newName) . "'";
    }

    public function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        // TODO: Implement getViewDependencies() method.
        return false;
    }

    public function renameTableSql(string $table, string $newName): string
    {
        return "EXEC sp_rename '" . $this->handleCaseSensitive($table) . "', '" . $this->handleCaseSensitive($newName) . "'";
    }

    public function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array
    {
        $db = Database::get();

        $rs = lmbdb_tables($db, null, $schema, $this->handleCaseSensitive($table), $types);
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
        // TODO: Implement createSequence() method.
        return false;
    }

    public function dropSequence(string $name): bool
    {
        // TODO: Implement dropSequence() method.
        return false;
    }

    public function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed
    {
        global $DBA;
        $db = Database::get();

        # get primary key
        if ($mode) {
            $rs1 = lmbdb_exec($db, $this->getIndicesSql($DBA["DBSCHEMA"], null, $this->handleCaseSensitive($table), null, false));
            while (lmbdb_fetch_row($rs1)) {
                $key[lmb_strtolower(trim(lmbdb_result($rs1, "COLUMNNAME")))] = 'PRIMARY KEY';
            }
        }

        if ($column) {
            $rs = lmbdb_columns($db, null, $schema, $this->handleCaseSensitive($table), $this->handleCaseSensitive($column));
        } else {
            $rs = lmbdb_columns($db, null, $schema, $this->handleCaseSensitive($table));
        }

        if ($returnRs) {
            return $rs;
        }

        while (lmbdb_fetch_row($rs)) {
            $col["tablename"][] = trim(lmbdb_result($rs, "TABLE_NAME"));
            $col["columnname"][] = trim(lmbdb_result($rs, "COLUMN_NAME"));
            $col["columnname_lower"][] = $this->handleCaseSensitive(trim(lmbdb_result($rs, "COLUMN_NAME")));
            $col["length"][] = trim(lmbdb_result($rs, "PRECISION"));
            $col["scale"][] = trim(lmbdb_result($rs, "SCALE"));
            $col["mode"][] = $key[lmb_strtolower(trim(lmbdb_result($rs, "COLUMN_NAME")))];

            $datatype = explode(' ', trim(lmbdb_result($rs, "TYPE_NAME")));
            $col["datatype"][] = $datatype[0];

            $default = trim(lmbdb_result($rs, "COLUMN_DEF"));
            $default = str_replace('()', '()#', $default);
            $default = trim($default, '(');
            $default = trim($default, ')');
            $default = trim($default, "'");
            $col["default"][] = str_replace('()#', '()', $default);
        }

        lmbdb_free_result($rs);


        /*
         * --- if this code is used, recheck the getIndicesSql call arguments
         *
        if($col AND $mode){
            foreach ($col["columnname"] as $key => $value){
                $rs1 = lmbdb_exec($db,$this->getIndicesSql($DBA["DBSCHEMA"],null, $col["tablename"][$key], $value, false));
                if(lmbdb_fetch_row($rs1)){
                    $col["mode"][$key] = 'PRIMARY KEY';
                }else{
                    $col["mode"][$key] = '';
                }
            }
        }
        */

        if (!empty($col)) {
            return $col;
        } else {
            return false;
        }
    }

    public function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        $db = Database::get();
        //	return "ALTER TABLE ".$this->handleCaseSensitive($table)." MODIFY ".$this->handleCaseSensitive($column)." DEFAULT ".$this->handleCaseSensitive($value);

        $sqlquery = "select sysdefconst.name AS DEFAULTCONSTRAINT
                    from sys.default_constraints as sysdefconst,
                         sys.columns as col
                    where col.object_id = OBJECT_ID(N'" . $this->handleCaseSensitive($table) . "')
                      AND col.name = '" . $this->handleCaseSensitive($column) . "'
                      AND col.default_object_id = sysdefconst.object_id";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "find default constraint");
        if ($default_constr = lmbdb_result($rs, "DEFAULTCONSTRAINT")) {
            $sqlquery = "ALTER TABLE " . $this->handleCaseSensitive($table) . " drop constraint " . $default_constr;
            $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "drop default constraint");
        }

        if ($value != "NULL" and $value) {
            return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ADD CONSTRAINT def_" . $this->handleCaseSensitive($table) . "_" . $this->handleCaseSensitive($column) . " DEFAULT " . $value . " FOR " . $this->handleCaseSensitive($column);
        } else {
            return false;
        }
    }

    public function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        #return "ALTER TABLE ".$this->handleCaseSensitive($table)." RENAME COLUMN ".$this->handleCaseSensitive($column).".TEMP_CONVERT TO ".$this->handleCaseSensitive($newName);
        //return "RENAME COLUMN ".$this->handleCaseSensitive($table).".".$this->handleCaseSensitive($column)." TO ".$this->handleCaseSensitive($newName);
        return "EXEC sp_rename '" . $this->handleCaseSensitive($table) . "." . $this->handleCaseSensitive($column) . "', '" . $this->handleCaseSensitive($newName) . "', 'COLUMN'";
    }

    public function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        //return "ALTER TABLE ".$this->handleCaseSensitive($table)." MODIFY ".$this->handleCaseSensitive($column)." ".$this->handleCaseSensitive($type);
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ALTER COLUMN " . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($type);
    }

    public function dropColumnSql(string $table, array|string $columns): string
    {
        if (!is_array($columns)) {
            return "EXEC DropColumnCascading " . $this->handleCaseSensitive($table) . ", " . $this->handleCaseSensitive($columns);
        }

        // TODO: Change array handling?
        $sql = '';
        foreach ($columns as $column) {
            $sql .= 'EXEC DropColumnCascading ' . $this->handleCaseSensitive($table) . ', ' . $this->handleCaseSensitive($column) . ';';
        }
        return $sql;
    }

    public function addColumnSql(string $table, array|string $column, array|string $type, array|string $default = null): string
    {
        // TODO: Implement addColumnSql() method.
        return '';
    }

    public function createLimbasVknFunction($schema, $dropOldProcedure = false): bool
    {
        $db = Database::get();

        if ($dropOldProcedure) {
            $act = "ALTER";
        } else {
            $act = "CREATE";
        }

        $sqlquery = "$act PROC lmb_vkn
          @id int,
          @tabName NVARCHAR(60),
          @fieldName NVARCHAR(60),
          @verknTabName NVARCHAR(60),
          @verkn_id int = 0,
          @reTabName NVARCHAR(60) = NULL,
          @reFieldName NVARCHAR(60) = NULL
        AS
        BEGIN
          DECLARE @cmd NVARCHAR(200);

          set @cmd = N'UPDATE ' + @tabName + N' set ' + @fieldName + ' = (select count(*) from ' + @verknTabName + ' where ID = ' + CONVERT(NVARCHAR(10),@id) + ') where ID = ' + CONVERT(NVARCHAR(10),@id);
	      exec(@cmd);
	
          IF (@verkn_id > 0)
	      BEGIN
            set @cmd = N'UPDATE ' + @reTabName + N' set ' + @reFieldName + ' = (select count(*) from ' + @verknTabName + ' where VERKN_ID = ' + CONVERT(NVARCHAR(10),@verkn_id) + ') where ID = ' + CONVERT(NVARCHAR(10),@verkn_id);
            exec(@cmd);
          END
        END;";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure lmb_vkn", __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }


        $sqlquery = "$act PROC DropColumnCascading @tablename nvarchar(500), @columnname nvarchar(500)
        AS

        SELECT CONSTRAINT_NAME, 'C' AS type
          INTO #dependencies
          FROM INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE WHERE TABLE_NAME = @tablename AND COLUMN_NAME = @columnname

        INSERT INTO #dependencies
        select d.name, 'C'
          from sys.default_constraints d
          join sys.columns c ON c.column_id = d.parent_column_id AND c.object_id = d.parent_object_id
          join sys.objects o ON o.object_id = d.parent_object_id
          WHERE o.name = @tablename AND c.name = @columnname

        INSERT INTO #dependencies
        SELECT i.name, 'I'
          FROM sys.indexes i
          JOIN sys.index_columns ic ON ic.index_id = i.index_id and ic.object_id=i.object_id
          JOIN sys.columns c ON c.column_id = ic.column_id and c.object_id=i.object_id
          JOIN sys.objects o ON o.object_id = i.object_id
          where o.name = @tableName AND i.type=2 AND c.name = @columnname AND is_unique_constraint = 0

        DECLARE @dep_name nvarchar(500)
        DECLARE @type nchar(1)

        DECLARE dep_cursor CURSOR
        FOR SELECT * FROM #dependencies

        OPEN dep_cursor

        FETCH NEXT FROM dep_cursor 
          INTO @dep_name, @type;

        DECLARE @sql nvarchar(max)

        WHILE @@FETCH_STATUS = 0
        BEGIN
          SET @sql = 
            CASE @type
               WHEN 'C' THEN 'ALTER TABLE [' + @tablename + '] DROP CONSTRAINT [' + @dep_name + ']'
               WHEN 'I' THEN 'DROP INDEX [' + @dep_name + '] ON dbo.[' + @tablename + ']'
          END
          print @sql
          EXEC sp_executesql @sql
          FETCH NEXT FROM dep_cursor 
            INTO @dep_name, @type;
        END

        DEALLOCATE dep_cursor

        DROP TABLE #dependencies

        SET @sql = 'ALTER TABLE [' + @tablename + '] DROP COLUMN [' + @columnname + ']'

        print @sql
        EXEC sp_executesql @sql";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure DropColumnCascading", __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }

        $sqlquery = "$act function Date(@DateTime DateTime)
	returns datetime
	as
	    begin
	    return dateadd(dd,0, datediff(dd,0,@DateTime))
	    end";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure Date", __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }

        $sqlquery = "$act function Time(@DateTime DateTime)
	returns datetime
	as
	    begin
	    return dateadd(day, -datediff(day, 0, @datetime), @datetime)
	    end";

        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, "create procedure Time", __FILE__, __LINE__);
        if (!$rs) {
            $commit = 1;
        }

        if (!empty($commit)) {
            return false;
        }
        return true;
    }

    public function dropLimbasVknFunction(): void
    {
        $db = Database::get();

        $sqlquery = "DROP PROC lmb_vkn";
        $rs = lmbdb_exec($db, $sqlquery);

        $sqlquery = "DROP PROC DropColumnCascading";
        $rs = lmbdb_exec($db, $sqlquery);

        $sqlquery = "DROP FUNCTION Date";
        $rs = lmbdb_exec($db, $sqlquery);

        $sqlquery = "DROP FUNCTION Time";
        $rs = lmbdb_exec($db, $sqlquery);
    }

    public function prettyPrintTableSize(string $schema, string $table): array
    {
        // TODO: Implement prettyPrintTableSize() method.
        return ["", ""];
    }

    public function createMedium(array $path, string $typ): bool
    {
        // TODO: Implement createMedium() method.
        return true;
    }

    public function deleteMedium(string $medium, string $typ): bool
    {
        // TODO: Implement deleteMedium() method.
        return true;
    }

    public function backupData(string $medium, array $path, string $typ): array
    {
        // TODO: Implement backupData() method.
        return [];
    }
}