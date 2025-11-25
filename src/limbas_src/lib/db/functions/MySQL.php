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

class MySQL extends DbFunction
{

    public function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed
    {
        global $DBA;
        $cur = null;

        #SQL_CUR_USE_ODBC
        #SQL_CUR_DEFAULT
        #SQL_CUR_USE_DRIVER

        if($driver == 'PDO') {
            if($port){$port = ":$port";}
            $dsn = "mysql:host=$host$port;dbname=$database";
        }elseif($driver) {
            $dsn = "Driver=$driver;Server=$host;Database=$database;ReadOnly=No";
            $cur = SQL_CUR_USE_ODBC;
        }else{
            $dsn = $database;
            $cur = SQL_CUR_USE_ODBC;
        }

        if($DBA['VERSION'] AND $DBA['VERSION'] < 10) { # use for newer mariadb >= V10
            $cur = null;
        }

        $db = lmbdb_pconnect($dsn, $user, $password, $cur);

        if($db) {
            return $db;
        }
        else {
            echo("<div class=\"alert alert-danger\"><h1>Database connection failed</h1><p>($dsn)<BR>".lmbdb_errormsg()."</p></div>");
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
    {
        if(!$date){$date=1;}
        if($withoutTime) {
            if($date instanceof DateTime) {
                return $date->format("Y-m-d");
            } else {
                return date("Y-m-d",1);
            }
        } else {
            if($date instanceof DateTime) {
                return $date->format("Y-m-d H:i:s");
            } else {
                return date("Y-m-d H:i:s",1);
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
    {
        if(!$value) {
            return false;
        }
        $db_date = lmb_substr($value,0,19);
        $db_date = preg_replace("/[^0-9]/",";",$db_date);
        $db_date = explode(";",$db_date);
        if(is_numeric($db_date[0])) {
            $result_stamp = mktime($db_date[3],$db_date[4],$db_date[5],$db_date[1],$db_date[2],$db_date[0]);
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
    {
        return $this->parseString($value);
    }

    /**
     * parse string
     *
     * @param string $value
     * @return string
     */
    public function parseString(string $value): string
    {
        return str_replace("\\", LMB_DBFUNC_UMASCB, str_replace("'", "''", $value));
    }

    /**
     * get sequence
     *
     * @param string $name
     * @return mixed|null
     */
    public function getSequence(string $name): mixed
    {
        global $db;

        $query = "SELECT seq_nextval('".$this->handleCaseSensitive($name)."') AS NEXTSEQ";
        $rs = lmbdb_exec($db,$query) or errorhandle(lmbdb_errormsg($db),$query,"get next sequence",0,0);
        return lmbdb_result($rs,"NEXTSEQ");
    }

    /**
     * case sensitive
     *
     * @param string $value
     * @return string
     */
    public function handleCaseSensitive(string $value): string
    {
        return lmb_strtoupper($value);
    }

    /**
     * timediff
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public function sqlTimeDiff(string $startColumn, string $endColumn): string
    {
        return "(" . $endColumn . "-" . $startColumn . ")";
    }

    /**
     * datediff
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public function sqlDateDiff(string $startColumn, string $endColumn): string
    {
        return '(' . $endColumn . '-' . $startColumn . ')';
    }

    public function calculateChecksum(string $field, ?int $type = null): string
    {
        // TODO: Implement calculateChecksum() method.
        return '';
    }

    public function setVariables(): void
    {
        // TODO: Implement setVariables() method.
    }

    public function version(?array $DBA = null): array
    {
        // version
        $sql = "SELECT @@VERSION as VERSION";
        $rs = Database::query($sql);
        $version = lmbdb_result($rs,"version");
        $version_[] = $version;
        $version_[] = substr($version, 0, strpos($version,'.'));

        // encoding
        if($DBA) {
            $sql = "SELECT DEFAULT_CHARACTER_SET_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE UPPER(SCHEMA_NAME) = \"".strtoupper($DBA["DBNAME"])."\"";
            $rs = Database::query($sql);
            $version_[] = lmbdb_result($rs, "default_character_set_name");

            $sql = "SHOW VARIABLES WHERE VARIABLE_NAME = 'default_storage_engine'";
            $rs = Database::query($sql);
            $version_[] = lmbdb_result($rs, "Value");
        }

        return $version_;
    }

    public function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        $sql = "SELECT CARDINALITY AS INDEX_USED, 
			TABLE_NAME TABLENAME,
			CASE NON_UNIQUE WHEN 0 THEN 1 WHEN 1 THEN 0 END AS IS_UNIQUE,
			COLUMN_NAME COLUMNNAME,
			INDEX_NAME INDEXNAME,
			IFNULL(cardinality,0),
			INDEX_TYPE TYPE
			FROM
			INFORMATION_SCHEMA.STATISTICS
			WHERE 
				INDEX_SCHEMA = '".$schema."'";

        if($tableName){
            $sql .= " AND UPPER(TABLE_NAME) = '".lmb_strtoupper($tableName)."'";
        }
        if($indexName){
            $sql .= " AND UPPER(COLUMN_NAME) = '".lmb_strtoupper($indexName)."'";
        }
        if($noPrimary){
            $sql .= "AND NOT INDEX_NAME = 'SYSPRIMARYKEYINDEX' AND NOT INDEX_NAME = 'PRIMARY'";
        }

        $sql .= " ORDER BY TABLE_NAME, INDEX_NAME";

        return $sql;
    }

    public function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        $unique = $isUnique ? 'UNIQUE' : '';
        return "CREATE $unique INDEX $indexName ON " . $this->handleCaseSensitive($tableName) . "($columnName)";
    }

    public function dropIndexSql(string $indexName, string $tableName): string
    {
        return "DROP INDEX $indexName ON $tableName";
    }

    public function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        $sql = "SELECT
	    CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME
	FROM 
		INFORMATION_SCHEMA.KEY_COLUMN_USAGE
	WHERE 
		CONSTRAINT_SCHEMA = '".$this->handleCaseSensitive($schema)."'
		AND CONSTRAINT_NAME = 'PRIMARY'" ;

        if($table){$sql .= " AND TABLE_NAME = '".$this->handleCaseSensitive($table)."'";}
        if($column){$sql .= " AND COLUMN_NAME = '".$this->handleCaseSensitive($column)."'";}

        $rs = Database::query($sql);
        while(lmbdb_fetch_row($rs)){
            $constraint["TABLE_NAME"][] = lmbdb_result($rs,"TABLE_NAME");
            $constraint["COLUMN_NAME"][] = lmbdb_result($rs,"COLUMN_NAME");
            $constraint["PK_NAME"][] = lmbdb_result($rs,"CONSTRAINT_NAME");
        }
        return  $constraint;
    }

    public function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array
    {
        $sql = "SELECT
	    KEY_COLUMN_USAGE.CONSTRAINT_NAME, KEY_COLUMN_USAGE.TABLE_NAME, KEY_COLUMN_USAGE.COLUMN_NAME, 
	    KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME AS FOREIGN_TABLE_NAME,
	    KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME AS FOREIGN_COLUMN_NAME
	FROM 
		INFORMATION_SCHEMA.KEY_COLUMN_USAGE, INFORMATION_SCHEMA.TABLE_CONSTRAINTS
	WHERE 
		KEY_COLUMN_USAGE.CONSTRAINT_SCHEMA = '".$this->handleCaseSensitive($schema)."'
		AND KEY_COLUMN_USAGE.CONSTRAINT_NAME = TABLE_CONSTRAINTS.CONSTRAINT_NAME
		AND TABLE_CONSTRAINTS.CONSTRAINT_TYPE = 'UNIQUE'
		" ;

        if($table){$sql .= " AND TABLE_NAME = '".$this->handleCaseSensitive($table)."'";}
        if($column){$sql .= " AND COLUMN_NAME = '".$this->handleCaseSensitive($column)."'";}

        $rs = Database::query($sql);
        while(lmbdb_fetch_row($rs)){
            $constraint["TABLE_NAME"][] = lmbdb_result($rs,"TABLE_NAME");
            $constraint["COLUMN_NAME"][] = lmbdb_result($rs,"COLUMN_NAME");
            $constraint["PK_NAME"][] = lmbdb_result($rs,"CONSTRAINT_NAME");
        }
        return  $constraint;
    }

    public function createPrimaryKeySql(string $table, string $column): string
    {
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ADD PRIMARY KEY (" . $this->handleCaseSensitive($column) . ")";
    }

    public function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " ADD CONSTRAINT " . $this->handleCaseSensitive($constraintName) . " UNIQUE ($column)";
    }

    public function dropPrimaryKeySql(string $table): string
    {
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " DROP PRIMARY KEY";
    }

    public function dropConstraintSql(string $table, string $constraintName): string
    {
        // TODO: Implement dropConstraintSql() method.
        return '';
    }

    public function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        $sql = "SELECT INFORMATION_SCHEMA.KEY_COLUMN_USAGE.TABLE_NAME TABLENAME,
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE.COLUMN_NAME COLUMNNAME,
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE.REFERENCED_TABLE_NAME REFTABLENAME,
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE.REFERENCED_COLUMN_NAME REFCOLUMNNAME,
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE.CONSTRAINT_NAME FKEYNAME,
				INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.DELETE_RULE RULE
			FROM 
				INFORMATION_SCHEMA.KEY_COLUMN_USAGE,
				 INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
			WHERE 
				INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS.CONSTRAINT_NAME = INFORMATION_SCHEMA.KEY_COLUMN_USAGE.CONSTRAINT_NAME
				AND INFORMATION_SCHEMA.KEY_COLUMN_USAGE.CONSTRAINT_SCHEMA = '$schema'";

        if($table){
            $sql .= " AND UPPER(INFORMATION_SCHEMA.KEY_COLUMN_USAGE.TABLE_NAME) = '" . $this->handleCaseSensitive($table) . "'";
        }
        if($column){
            $sql .= " AND UPPER(INFORMATION_SCHEMA.KEY_COLUMN_USAGE.CONSTRAINT_NAME) LIKE '" . $this->handleCaseSensitive($column) . "'";
        }

        return $sql;
    }

    public function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        $sql = "ALTER TABLE ".$this->handleCaseSensitive($parentTable)." 
		ADD CONSTRAINT $keyName FOREIGN KEY ($parentColumn) 
		REFERENCES ".$this->handleCaseSensitive($childTable)."($childColumn) 
		ON DELETE " . ($restrict ?: 'RESTRICT');
        return $sql;
    }

    public function dropForeignKeySql(string $table, string $keyName): string
    {
        return " ALTER TABLE $table DROP FOREIGN KEY $keyName";
    }

    public function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TRIGGERS";
        if($triggerName) {
            $sql .= " WHERE LOWER(TRIGGER_NAME) LIKE '" . lmb_strtolower($triggerName) . "'";
        }

        $rs = Database::query($sql);
        while(lmbdb_fetch_row($rs)){
            $res["triggername"][] = lmbdb_result($rs,"TRIGGER_NAME");
            $res["definition"][] = lmbdb_result($rs,"ACTION_STATEMENT");
            $res["tablename"][] = lmbdb_result($rs,"EVENT_OBJECT_TABLE");
            $res["event"][] = lmbdb_result($rs,"EVENT_MANIPULATION");
            $res["action"][] = lmbdb_result($rs,"ACTION_TIMING");
        }

        return $res;
    }

    public function dropTriggerSql(string $table, string $triggerName): string
    {
        return "DROP TRIGGER IF EXISTS $triggerName";
    }

    public function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        $sql = "CREATE TRIGGER $triggerName AFTER $action ON $schema.$table FOR EACH ROW BEGIN $value END;";
        return $sql;
    }

    public function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string
    {
        if($action == "+"){
            $t = 'NEW';
        }else{
            $t = 'OLD';
        }

        # vorward relation
        if($archive == 2) { // ignore archive
            $out = "UPDATE $parentTable SET $parentColumn = (SELECT COUNT(*) FROM $relationTable,$childTable WHERE $relationTable.ID = $t.ID AND $childTable.ID = $relationTable.VERKN_ID AND $childTable.DEL = FALSE) WHERE ID = $t.ID;";
        }else{
            $out = "UPDATE $parentTable SET $parentColumn = (SELECT COUNT(*) FROM $relationTable WHERE ID = $t.ID) WHERE ID = $t.ID;";
        }

        # backward relation
        if($childColumn){
            if($archive == 2) { // ignore archive
                $out .= "\nUPDATE $childTable SET $childColumn = (SELECT COUNT(*) FROM  $relationTable, $parentTable WHERE $relationTable.VERKN_ID = $t.VERKN_ID AND  $parentTable.ID = $relationTable.ID AND $parentTable.DEl = FALSE) WHERE ID = $t.VERKN_ID;";
            }else{
                $out .= "\nUPDATE $childTable SET $childColumn = (SELECT COUNT(*) FROM  $relationTable WHERE VERKN_ID = $t.VERKN_ID) WHERE ID = $t.VERKN_ID;";
            }
        }

        return $out;
    }

    public function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        // TODO: Implement lastModifiedTriggerFunctionSql() method.
        return '';
    }

    public function getViewDefinitionSql(string $schema, string $viewName): string
    {
        return "SELECT TABLE_NAME VIEWNAME, VIEW_DEFINITION DEFINITION 
			FROM 
				INFORMATION_SCHEMA.VIEWS
			WHERE 
				TABLE_SCHEMA = '$schema'
				AND TABLE_NAME = '" . $this->handleCaseSensitive($viewName) . "'";
    }

    public function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        $qu = "SELECT TABLE_NAME VIEWNAME, VIEW_DEFINITION DEFINITION
		FROM 
			INFORMATION_SCHEMA.VIEWS
		WHERE 
			TABLE_SCHEMA = '$schema'";
        if($viewName){
            $qu .= " AND LOWER(TABLE_NAME) = '" . $this->handleCaseSensitive($viewName) . "'";
        }
        return $qu;
    }

    public function createViewSql(string $viewName, string $definition): string
    {
        if(lmb_stripos($definition,"CREATE VIEW") !== false){
            $qu = $definition;
        }else{
            $qu = "CREATE OR REPLACE VIEW " . $this->handleCaseSensitive($viewName) . " AS (" . rtrim(trim($definition),";") . ")";
        }

        return $qu;
    }

    public function dropViewSql(string $viewName): string
    {
        $qu = "DROP VIEW $viewName";

        return $qu;
    }

    public function renameViewSql(string $viewName, string $newName): string
    {
        // TODO: Implement renameViewSql() method.
        return '';
    }

    public function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        // TODO: Implement getViewDependencies() method.
        return '';
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

        if($table){$name = $this->handleCaseSensitive($table);}
        if($types){$type = $types;}

        $rs = lmbdb_tables($db,null,null,$name,$type);

        while(lmbdb_fetch_row($rs)){
            $odbc_table["table_name"][] = lmbdb_result($rs,"TABLE_NAME");
            if(stripos(lmbdb_result($rs,"TABLE_TYPE"),'TABLE') !== false){
                $odbc_table["table_type"][] = 'TABLE';
            }else{
                $odbc_table["table_type"][] = lmbdb_result($rs,"TABLE_TYPE");
            }
        }

        if($odbc_table){
            return $odbc_table;
        }else{
            return false;
        }
    }

    public function dropTableSql(string $table): string
    {
        $qu = "DROP TABLE ".$this->handleCaseSensitive($table);

        return $qu;
    }

    public function getSequences(string $schema, ?string $prefix = null): array
    {
        $sequ = array();
        $sql = "SELECT SEQUENCE_NAME FROM LMB_SEQUENCES ORDER BY SEQUENCE_NAME";
        $rs = Database::query($sql);
        if(!$prefix) {
            $prefix = 'lmb';
        }
        $prefix = strtolower($prefix);

        while(lmbdb_fetch_row($rs)) {
            $name = lmbdb_result($rs, "SEQUENCE_NAME");
            $name_ = explode('_',$name);

            if(strtolower($name_[0]) == $prefix) {
                $sequ[] = lmbdb_result($rs, "SEQUENCE_NAME");
            }
        }

        return $sequ;
    }

    public function createSequence(string $name, ?string $start = null): bool
    {
        $db = Database::get();

        $this->dropSequence($name);

        if(!$start) {
            $start = 1;
        }

        if(lmbdb_exec($db,"SELECT SEQ_SET('" . $this->handleCaseSensitive($name) . "', $start, 1);")) {
            return true;
        } else {
            return false;
        }
    }

    public function dropSequence(string $name): bool
    {
        $db = Database::get();

        if(lmbdb_exec($db,"DELETE FROM LMB_SEQUENCES WHERE SEQUENCE_NAME = '$name'")){
            return true;
        }else{
            return false;
        }
    }

    public function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed
    {
        $db = Database::get();

        $sql = "SELECT TABLE_NAME, COLUMN_NAME, COLUMN_DEFAULT, COLUMN_KEY, IFNULL(NUMERIC_PRECISION, CHARACTER_MAXIMUM_LENGTH) AS 'PRECISION', NUMERIC_SCALE AS 'SCALE', DATA_TYPE AS TYPE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$schema' AND LOWER(TABLE_NAME) = '".lmb_strtolower($table)."'";
        if($column){
            $sql .= " AND LOWER(COLUMN_NAME) = '".lmb_strtolower($column)."'";
        }
        $rs = lmbdb_exec($db,$sql);

        if ($returnRs) {
            return $rs;
        }

        while(lmbdb_fetch_row($rs)) {
            $col["tablename"][] = lmbdb_result($rs, "TABLE_NAME");
            $col["columnname"][] = lmbdb_result($rs, "COLUMN_NAME");
            $col["columnname_lower"][] = $this->handleCaseSensitive(trim(lmbdb_result($rs, "COLUMN_NAME")));
            $col["datatype"][] = lmbdb_result($rs, "TYPE_NAME");
            $col["length"][] = lmbdb_result($rs, "PRECISION");
            $col["default"][] = lmbdb_result($rs, "COLUMN_DEFAULT");
            $col["scale"][] = trim(lmbdb_result($rs, "SCALE"));
            if(lmbdb_result($rs, "COLUMN_KEY") == 'PRI') {
                $col["mode"][] = 'PRIMARY KEY';
            }
            elseif(lmbdb_result($rs, "COLUMN_KEY") == 'UNI') {
                $col["mode"][] = 'UNIQUE';
            }
            else {
                $col["mode"][] = '';
            }
        }

        if($col){
            return $col;
        }else{
            return false;
        }
    }

    public function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        $col = $this->getColumns($schema, $table, $column);

        if($col["scale"][0]){
            $ct = $col["datatype"][0].'('.$col["length"][0].','.$col["scale"][0].')';
        }elseif($col["length"][0]){
            $ct = $col["datatype"][0].'('.$col["length"][0].')';
        }else{
            $ct = $col["datatype"][0];
        }

        return "ALTER TABLE ".$this->handleCaseSensitive($table)." CHANGE COLUMN ".$this->handleCaseSensitive($column)." ".$this->handleCaseSensitive($column)." $ct DEFAULT ".$value;
    }

    public function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        $col = $this->getColumns($schema, $table, $column);

        if($col["scale"][0]){
            $ct = $col["datatype"][0].'('.$col["length"][0].','.$col["scale"][0].')';
        }elseif($col["length"][0]){
            $ct = $col["datatype"][0].'('.$col["length"][0].')';
        }else{
            $ct = $col["datatype"][0];
        }

        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " CHANGE COLUMN " . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($newName) ." $ct";
    }

    public function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        return "ALTER TABLE " . $this->handleCaseSensitive($table) . " MODIFY " . $this->handleCaseSensitive($column) . " " . $this->handleCaseSensitive($type);
    }

    public function dropColumnSql(string $table, array|string $columns): string
    {
        if(is_array($columns)){
            foreach($columns as $key => $field){
                $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST.' '.$this->handleCaseSensitive($field);
            }
        }else{
            $qu[] = LMB_DBFUNC_DROP_COLUMN_FIRST.' '.$this->handleCaseSensitive($columns);
        }

        return 'ALTER TABLE '.$this->handleCaseSensitive($table).' '.implode(',',$qu);
    }

    public function addColumnSql(string $table, array|string $column, array|string $type, array|string $default = null): string
    {
        if(is_array($column)) {
            foreach($column as $key => $field){
                $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($field) . ' ' . $type[$key];
                if($default[$key]) {
                    $qu .= ' DEFAULT ' . $default[$key];
                }
                $adf[] = $qu;
            }
        } else {
            $qu = LMB_DBFUNC_ADD_COLUMN_FIRST . ' ' . $this->handleCaseSensitive($column) . ' ' . $type;
            if($default) {
                $qu .= ' DEFAULT ' . $default;
            }
            $adf[] = $qu;
        }

        return "ALTER TABLE " . $this->handleCaseSensitive($type) . " " . implode(',',$adf);
    }

    public function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool
    {
        $db = Database::get();

        # sequences workaround for mysql
        if(!$this->getTableList($GLOBALS['DBA']['DBSCHEMA'],'LMB_SEQUENCES',"'TABLE'")){
            $sqlquery = "CREATE TABLE LMB_SEQUENCES ( SEQUENCE_NAME VARCHAR(50) COLLATE UTF8_BIN NOT NULL, CURRENT_VALUE BIGINT(20) NOT NULL DEFAULT '0', INCREMENT INT(11) NOT NULL DEFAULT '1', PRIMARY KEY (SEQUENCE_NAME) );";
            $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,"create TABLE lmb_sequences ()",__FILE__,__LINE__);
            if(!$rs){$commit = 1;}
        }

        // seq_nextval
        $this->dropLimbasVknFunction(array($GLOBALS['DBA']['DBSCHEMA']),'function','seq_nextval');
        $sqlquery = "
        CREATE FUNCTION `SEQ_NEXTVAL`(SEQNAME VARCHAR(100)) RETURNS INT(11) 
        BEGIN UPDATE LMB_SEQUENCES SET CURRENT_VALUE=(@RET:=CURRENT_VALUE)+INCREMENT WHERE SEQUENCE_NAME=SEQNAME;
        RETURN @RET; 
        END;";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,"create procedure seq_nextval",__FILE__,__LINE__);
        if(!$rs){$commit = 1;}

        // seq_set
        $this->dropLimbasVknFunction($GLOBALS['DBA']['DBSCHEMA'],'function','seq_set');
        $sqlquery = "CREATE FUNCTION SEQ_SET(SEQNAME VARCHAR(100), CVAL INT(11), INC INT(11)) RETURNS INT(11) 
        BEGIN REPLACE INTO LMB_SEQUENCES(SEQUENCE_NAME, CURRENT_VALUE, INCREMENT) VALUES(SEQNAME, CVAL, INC);
         RETURN CVAL; 
         END;";
        $rs = lmbdb_exec($db,$sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,"create procedure seq_set",__FILE__,__LINE__);
        if(!$rs){$commit = 1;}

        // lmb_try_query // todo
        $sqlquery = "";

        // lmb_lastmodified // todo
        $sqlquery = "";


        if($commit){return false;}
        return true;
    }

    public function dropLimbasVknFunction($schema = '', $type = '', $name = ''): void
    {
        $db = Database::get();
        if($type AND $name) {
            $sqlquery = "DROP $type $schema.$name";
            $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db),$sqlquery,"drop procedure $schema.$name",__FILE__,__LINE__);

        }
    }

    public function prettyPrintTableSize(string $schema, string $table): array
    {
        // TODO: Implement prettyPrintTableSize() method.
        return ["", ""];
    }

    public function createMedium(array $path, string $typ): bool
    {
        // TODO: Implement createMedium() method?
        return true;
    }

    public function deleteMedium(string $medium, string $typ): bool
    {
        // TODO: Implement deleteMedium() method?
        return true;
    }

    public function backupData(string $medium, array $path, string $typ): array
    {
        global $DBA;

        $sys = "mysqldump -h" . $DBA["DBHOST"] . " -u" . $DBA["DBCUSER"] . " -p" . $DBA["DBCPASS"] . " " . $DBA["DBCNAME"] . " | gzip > " . $path["path"] . ".gz";
        exec($sys,$res,$ret);

        if(!$ret){
            clearstatcache();
            if(file_exists($path["path"].".gz")){
                $out[7] = filesize($path["path"].".gz");
            }
            if($out[7] > 1000){
                $out[0] = "OK";
                $out[10] = $path["medname"];
                return $out;
            }else{
                $out[0] = 'FALSE';
                return $out;
            }
        }else{
            $out[0] = 'FALSE';
            return $out;
        }
    }
}