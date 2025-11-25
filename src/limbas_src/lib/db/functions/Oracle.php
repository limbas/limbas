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

class Oracle extends DbFunction
{

    public function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed
    {
        if ($driver == "DSN") {
            $db = lmbdb_pconnect("$database", "$user", "$password");
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
        $rs = Database::query("SELECT " . $this->handleCaseSensitive($name) . ".NEXTVAL AS NEXTSEQ FROM DUAL");
        return lmbdb_result($rs, "NEXTSEQ");
    }

    public function handleCaseSensitive(string $value): string
    {
        return lmb_strtoupper($value);
    }

    public function sqlTimeDiff(string $startColumn, string $endColumn): string
    {
        // TODO: Implement sqlTimeDiff() method.
        return '';
    }

    public function sqlDateDiff(string $startColumn, string $endColumn): string
    {
        // TODO: Implement sqlDateDiff() method.
        return '';
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
        // TODO: Implement version() method.
        return [];
    }

    public function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        $sql = "select user_indexes.table_name tablename, user_ind_columns.column_name, user_indexes.index_name indexname, '1' index_used, user_indexes.index_type type
	 from user_ind_columns, user_indexes
 		where user_ind_columns.index_name = user_indexes.index_name
 		 and owner = '" . $schema . "'";

        if ($tableName) {
            $sql .= " AND LOWER(TABLE_NAME) = '" . lmb_strtolower($tableName) . "'";
        }
        if ($indexName) {
            $sql .= " AND LOWER(COLUMN_NAME) = '" . lmb_strtolower($indexName) . "'";
        }
        if ($noPrimary) {
            $sql .= "AND NOT INDEX_NAME = 'SYSPRIMARYKEYINDEX'";
        }

        $sql .= " ORDER BY TABLE_NAME,INDEX_NAME";

        return $sql;
    }

    public function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        return "CREATE INDEX " . $indexName . " ON " . $tableName . "(" . $columnName . ")";
    }

    public function dropIndexSql(string $indexName, string $tableName): string
    {
        return "DROP INDEX " . $indexName;
    }

    public function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        $db = Database::get();

        $pri_key = [];
        $rs = lmbdb_primarykeys($db, $column, $schema, $table);
        while (lmbdb_fetch_row($rs)) {
            $pri_key["COLUMN_NAME"][] = lmbdb_result($rs, "COLUMN_NAME");
            $pri_key["PK_NAME"][] = lmbdb_result($rs, "PK_NAME");
        }
        return $pri_key;
    }

    public function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array
    {
        // TODO: Implement getUniqueConstraints() method.
        return [];
    }

    public function createPrimaryKeySql(string $table, string $column): string
    {
        return "ALTER TABLE " . $table . " ADD PRIMARY KEY (" . $column . ")";
    }

    public function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        // TODO: Implement createConstraintSql() method.
        return '';
    }

    public function dropPrimaryKeySql(string $table): string
    {
        return "ALTER TABLE " . $table . " DROP PRIMARY KEY";
    }

    public function dropConstraintSql(string $table, string $constraintName): string
    {
        // TODO: Implement dropConstraintSql() method.
        return '';
    }


    #select * from all_cons_columns

    /*
    select user_constraints.constraint_name fkeyname, user_constraints.table_name tablename, user_constraints.r_constraint_name, user_cons_columns.column_name columnname, user_indexes.table_name reftablename, user_ind_columns.column_name refcolumnname, user_constraints.delete_rule rule 
    
    from user_constraints,user_cons_columns, user_indexes, user_ind_columns
    
    where 
    user_constraints.constraint_type = 'R' and user_constraints.owner = 'LIMBASUSER' 
    and user_cons_columns.constraint_name = user_constraints.constraint_name
    and user_indexes.index_name = user_constraints.r_constraint_name
    and user_ind_columns.index_name = user_indexes.index_name
    */
    public function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        /*	$sql = "Select TABLENAME,
				COLUMNNAME,
				REFTABLENAME,
				REFCOLUMNNAME,
				FKEYNAME,
				RULE
			FROM 
				DOMAIN.FOREIGNKEYCOLUMNS 
			WHERE 
				OWNER = '".$schema."'";*/


        #Bin mir nicht sicher ob der Alias in Abfragen gültig ist.Ausprobieren!

        $sql = "select user_constraints.table_name tablename,
	   user_cons_columns.column_name columnname,
	    user_indexes.table_name reftablename,
	     user_ind_columns.column_name refcolumnname,
	      user_constraints.delete_rule rule 
			from
			 user_constraints,user_cons_columns, user_indexes, user_ind_columnsNS 
			WHERE 
				user_constraints.constraint_type = 'R'
				and user_cons_columns.constraint_name = user_constraints.constraint_name
				and user_indexes.index_name = user_constraints.r_constraint_name
				and user_ind_columns.index_name = user_indexes.index_name
				 and user_constraints.owner = '" . $schema . "'";

        if ($table) {
            $sql .= " AND LOWER(TABLENAME) = '" . lmb_strtolower($table) . "'";
        }
        if ($column) {
            $sql .= " AND LOWER(FKEYNAME) LIKE '" . lmb_strtolower($column) . "'";
        }

        return $sql;
    }

    #ALTER TABLE table_name add CONSTRAINT constraint_name FOREIGN KEY(column1,...) REFERENCES parent_table (column1, column2, ... column_n);
    public function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        if (!$restrict) {
            $restrict = "RESTRICT";
        }

        #return "ALTER TABLE ".$parentTable." ADD CONSTRAINT ".$keyName." FOREIGN KEY (".$parentColumn.") REFERENCES ".$childTable." (".$childColumn.") ;";	
        return "ALTER TABLE " . $parentTable . " add CONSTRAINT " . $keyName . " FOREIGN KEY(" . $parentColumn . ") REFERENCES " . $childTable . " (" . $childColumn . ") ON DELETE " . $restrict . ";";
    }

    public function dropForeignKeySql(string $table, string $keyName): string
    {
        return " ALTER TABLE " . $table . " DROP CONSTRAINT " . $keyName;
    }

    public function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        /*done, evtl noch namen in schleife anpassen triggername oder trigger_name*/

        $sql = "SELECT trigger_name triggername, trigger_body DEFINITION,table_name TABLENAME, triggering_event
		FROM
		 	user_triggers";
        if ($triggerName) {
            $sql .= " WHERE LOWER(TRIGGER_NAME) LIKE '" . lmb_strtolower($triggerName) . "'";
        }

        $rs = Database::query($sql);
        while (lmbdb_fetch_row($rs)) {
            $res["triggername"][] = lmbdb_result($rs, "TRIGGERNAME");
            $res["definition"][] = lmbdb_result($rs, "DEFINITION");
            $res["tablename"][] = lmbdb_result($rs, "TABLENAME");

            $res["event"][] = lmbdb_result($rs, "TRIGGERING_EVENT");

        }

        return $res;
    }

    public function dropTriggerSql(string $table, string $triggerName): string
    {
        return "DROP TRIGGER " . $table . "." . $triggerName;
    }

    public function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        /*
        CREATE or REPLACE TRIGGER trigger_name
        BEFORE INSERT
            ON table_name
            [ FOR EACH ROW ]
        DECLARE
            -- variable declarations
        BEGIN
            -- trigger code
        EXCEPTION
            WHEN ...
            -- exception handling
        END;
        */
        $sql = "CREATE OR REPLACE TRIGGER " . $triggerName . " AFTER " . $action . " ON " . $table . " BEGIN " . $value . " END";
        return $sql;
    }

    public function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string
    {
        if ($action == "+") {
            $act = ":NEW.ID,:NEW.VERKN_ID";
        } else {
            $act = ":OLD.ID,:OLD.VERKN_ID";
        }
        return "lmb_vkn(" . $act . ",'" . $schema . '.' . $parentTable . "','" . $parentColumn . "','" . $action . "','" . $schema . "." . $childTable . "','" . $childColumn . "','" . $schema . "." . $relationTable . "');";
    }

    public function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        // TODO: Implement lastModifiedTriggerFunctionSql() method.
        #return "UPDATE LMB_CONF_TABLES SET LASTMODIFIED = CURRENT_TIMESTAMP WHERE TAB_ID = ".$tabId.";";
        return '';
    }

    public function getViewDefinitionSql(string $schema, string $viewName): string
    {
        #c where owner = '' and view_name = ''
        /*return "SELECT VIEWNAME, DEFINITION 
			FROM 
				DOMAIN.VIEWDEFS
			WHERE 
				OWNER = '".$schema."'
				AND VIEWNAME = '".$this->handleCaseSensitive($viewName)."'";*/
        return "SELECT view_name,
			 text
			 from all_views
			WHERE 
				OWNER = '" . $schema . "'
				AND VIEW_NAME = '" . $this->handleCaseSensitive($viewName) . "'";
    }

    public function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        #select all_views.view_name viewname, user_dependencies.referenced_name
        #  from all_views, user_dependencies
        # where user_dependencies.name = all_views.view_name and user_dependencies.type = 'VIEW' and all_views.owner = 'SYS'

        $qu = "select all_views.view_name viewname,
			 user_dependencies.referenced_nam
		FROM 
			all_views, user_dependencies
		WHERE 
			user_dependencies.name = all_views.view_name and user_dependencies.type = 'VIEW' and all_views.owner = '" . $schema . "'";
        if ($viewName) {
            $qu .= " AND LOWER(VIEW_NAME) = '" . lmb_strtolower($viewName) . "'";
        }
        return $qu;
    }

    public function createViewSql(string $viewName, string $definition): string
    {
        #CREATE VIEW view_name AS Select......

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
        #RENAME old_name TO new_name;
        #nur objekte....
        return "RENAME " . $this->handleCaseSensitive($viewName) . " TO " . $this->handleCaseSensitive($newName);
    }

    public function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        // TODO: Implement getViewDependencies() method.
        return false;
    }

    public function renameTableSql(string $table, string $newName): string
    {
        //What is the difference?
        //ALTER TABLE table_name RENAME TO new_table_name;
        //return "RENAME ".$this->handleCaseSensitive($table)." TO ".$this->handleCaseSensitive($newName);

        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " RENAME TO " . $this->handleCaseSensitive($newName);
    }

    public function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array
    {
        $db = Database::get();

        $odbc_table = [];
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
        // TODO: Implement dropTableSql() method.
        return '';
    }

    public function getSequences(string $schema): array
    {
        // TODO: Implement getSequences() method.
        return [];
    }

    public function createSequence(string $name, ?string $start = null): bool
    {
        #CREATE SEQUENCE supplier_seq MINVALUE 1 MAXVALUE 999999999999999999999999999  START WITH 1 INCREMENT BY 1 CACHE 20;

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
        #DROP SEQUENCE sequence_name;

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

        $sql = "SELECT * FROM DOMAIN.COLUMNS WHERE OWNER = '" . $schema . "' AND LOWER(TABLENAME) = '" . lmb_strtolower($table) . "'";
        if ($column) {
            $sql .= " AND LOWER(COLUMNNAME) = '" . lmb_strtolower($column) . "'";
        }

        $rs = lmbdb_exec($db, $sql);
        while (lmbdb_fetch_row($rs)) {
            $col["tablename"][] = $this->handleCaseSensitive(lmbdb_result($rs, "TABLENAME"));
            $col["columnname"][] = $this->handleCaseSensitive(lmbdb_result($rs, "COLUMNNAME"));
            $col["columnname_lower"][] = $this->handleCaseSensitive(trim(lmbdb_result($rs, "COLUMNNAME")));
            $col["datatype"][] = lmbdb_result($rs, "DATATYPE");
            $col["length"][] = lmbdb_result($rs, "LEN");
            $col["scale"][] = trim(lmbdb_result($rs, "DEC"));
            $col["default"][] = lmbdb_result($rs, "DEFAULT");
            $col["mode"][] = lmbdb_result($rs, "MODE");
        }

        if (!empty($col)) {
            return $col;
        } else {
            return false;
        }
    }

    public function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        #ALTER TABLE table_name MODIFY column_name column_type;
        //return "ALTER TABLE ".$this->handleCaseSensitive($table)." MODIFY ".$this->handleCaseSensitive($column)." DEFAULT ".$this->handleCaseSensitive($value);
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " MODIFY " . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($value);
    }

    public function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        #ALTER TABLE table_name RENAME COLUMN old_name to new_name;
        #return "ALTER TABLE ".$this->handleCaseSensitive($table)." RENAME COLUMN ".$this->handleCaseSensitive($column).".TEMP_CONVERT TO ".$this->handleCaseSensitive($newName);
        //return "RENAME COLUMN ".$this->handleCaseSensitive($table).".".$this->handleCaseSensitive($column)." TO ".$this->handleCaseSensitive($newName);
        return "ALTER TABLE  COLUMN " . $this->handleCaseSensitive($table) . "." . $this->handleCaseSensitive($column) . " TO " . $this->handleCaseSensitive($newName);
    }

    public function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        #ALTER TABLE table_name MODIFY column_name column_type;
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " MODIFY " . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($type);
    }

    public function dropColumnSql(string $table, array|string $columns): string
    {
        #ALTER TABLE table_name DROP COLUMN column_name;
        if (!is_array($columns)) {
            return "ALTER TABLE " . $this->handleCaseSensitive($table) . " DROP COLUMN " . $this->handleCaseSensitive($columns) . ";";
        }

        //should be correct, but is untested
        $sql = "ALTER TABLE " . $this->handleCaseSensitive($table) . " DROP (";
        $sql .= implode(",", $columns);
        $sql .= ");";
        return $sql;
    }

    public function addColumnSql(string $table, array|string $column, array|string $type, array|string $default = null): string
    {
        // TODO: Implement addColumnSql() method.
        return '';
    }

    public function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool
    {
        # drop procedure
        if ($dropOldProcedure) {
            $this->dropLimbasVknFunction($schema);
        }


        /*CREATE [OR REPLACE] PROCEDURE procedure_name
            [ (parameter [,parameter]) ]
        IS
            [declaration_section]
        BEGIN
            executable_section
        [EXCEPTION
            exception_section]
        END [procedure_name];*/

        if (empty($schema)) {
            $target = 'lmb_vkn';
        } else {
            $target = $schema . '.lmb_vkn';
        }

        $sqlquery = "
	
create or replace procedure " . $target . "(id in number,vid in number,tabname in varchar2,fieldname in varchar2,act in varchar2 ,rtabname in varchar2,rfieldname in varchar2,vtabname in varchar2)
is

statement1 char(200); 
statement2 char(200);

Begin

statement1 := 'update ' || tabname || ' set ' || fieldname || ' = (select count(*) from ' || vtabname || ' where id = '|| id ||') where id = ' || id;
EXECUTE immediate statement1;

statement2 := '';

IF rtabname <> '' THEN
statement2 := 'update ' || rtabname || ' set ' || rfieldname || ' = (select count(*) from ' || vtabname || ' where verkn_id = '|| vid ||') where id = ' || vid;
IF statement2 <> '' THEN
EXECUTE immediate statement2;
end if;
end if;

End;
";

        if ($rs = Database::query($sqlquery)) {
            return true;
        } else {
            return false;
        }
    }

    public function dropLimbasVknFunction(?string $schema = null): void
    {
        if (empty($schema)) {
            $target = 'lmb_vkn';
        } else {
            $target = $schema . '.lmb_vkn';
        }

        $sqlquery = "drop procedure " . $target . ".lmb_vkn";
        $rs = Database::query($sqlquery);
    }

    public function prettyPrintTableSize(string $schema, string $table): array
    {
        // TODO: Implement prettyPrintTableSize() method.
        return ["", ""];
    }

    public function createMedium(array $path, string $typ): bool
    {
        // TODO: Implement createMedium() method.
        return false;
    }

    public function deleteMedium(string $medium, string $typ): bool
    {
        // TODO: Implement deleteMedium() method.
        return false;
    }

    public function backupData(string $medium, array $path, string $typ): array
    {
        global $DBA;

        if($DBA["LMHOST"] == $DBA["DBHOST"] OR $DBA["DBHOST"] == "127.0.0.1" OR $DBA["DBHOST"] == "localhost"){
            $bu["path"] = $DBA["DBPATH"];
        }else{
            $bu["path"] = "ssh ".$DBA["DBHOST"]." ".$DBA["DBPATH"];
            $bu["sshpath"] = "ssh ".$DBA["DBHOST"];
        }

        $sys = $bu["path"]."/dbmcli -n ".$DBA["DBHOST"]." -d ".$DBA["DBNAME"]." -uUTL -u ".$DBA["DBCUSER"].",".$DBA["DBCPASS"]." backup_start $medium RECOVERY $typ";
        exec($sys,$out,$ret);

        if($out[0] == "OK"){

            foreach ($out as $key => $value){
                if($value){
                    $det = explode(" ",$value);
                    $outres[] = $det[lmb_count($det)-1];
                }
            }

            if($typ == "DATA"){
                $outres[7] = ($outres[7]*2);
            }elseif($typ == "PAGES"){
                $outres[7] = ($outres[7]/4);
            }

            # --- zippen ---
            $sys1 = trim($bu["sshpath"]." gzip ".$path["path"]);
            exec($sys1,$out1,$ret1);
            if(file_exists($bu["sshpath"].".gz")){
                $outres[] = "gzip ok";
            }else{
                $outres[] = "gzip false";
            }
            return $outres;
        }else{
            fill_history($out,$path,$typ);
            return false;
        }
    }
}