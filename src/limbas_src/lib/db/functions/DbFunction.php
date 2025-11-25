<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\db\functions;

abstract class DbFunction
{

    /**
     * dbq_0
     *
     * @param string $host
     * @param string $database
     * @param string $user
     * @param string $password
     * @param string|null $driver
     * @param int|null $port
     * @return mixed
     */
    public abstract function connect(string $host, string $database, string $user, string $password, ?string $driver = null, ?int $port = null): mixed;


    /**
     * convert stamp for db
     * dbf_1
     *
     * @param mixed $date
     * @param bool $withoutTime
     * @return string
     */
    public abstract function convertToDbTimestamp(mixed $date, ?bool $withoutTime = false): string;


    /**
     * convert date from db to stamp
     * dbf_2
     *
     * @param string $value
     * @return false|int
     */
    public abstract function convertFromDbTimestamp(string $value): false|int;

    /**
     * parse blob
     * dbf_6
     *
     * @param string $value
     * @return string
     */
    public abstract function parseBlob(string $value): string;

    /**
     * parse string
     * dbf_7
     *
     * @param string $value
     * @return string
     */
    public abstract function parseString(string $value): string;


    /**
     * get sequence
     * dbf_8
     *
     * @param string $name
     * @return mixed|null
     */
    public abstract function getSequence(string $name): mixed;

    /**
     * case sensitive
     * dbf_4
     *
     * @param string $value
     * @return string
     */
    public abstract function handleCaseSensitive(string $value): string;

    /**
     * time diff
     * dbf_9
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public abstract function sqlTimeDiff(string $startColumn, string $endColumn): string;

    /**
     * date diff
     * dbf_10
     *
     * @param string $startColumn
     * @param string $endColumn
     * @return string
     */
    public abstract function sqlDateDiff(string $startColumn, string $endColumn): string;

    /**
     * calculate checksum
     * dbf_12
     *
     * @param string $field
     * @param int|null $type
     * @return string
     */
    public abstract function calculateChecksum(string $field, ?int $type = null): string;


    /**
     * @return void
     */
    public abstract function setVariables(): void;


    /**
     * @param array|null $DBA
     * @return array
     */
    public abstract function version(?array $DBA = null): array;


    /**
     * dbq_2
     *
     * @param string $schema
     * @param string|null $indexName
     * @param string|null $tableName
     * @param string|null $columnName
     * @param bool|null $noPrimary
     * @param string|null $indexPrefix
     * @return string
     */
    public abstract function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string;

    /**
     * dbq_4
     * TODO: what about schema?
     *
     * @param string $indexName
     * @param string $tableName
     * @param string $columnName
     * @param bool|null $isUnique
     * @return string
     */
    public abstract function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string;

    /**
     * dbq_5
     * TODO: what about schema?
     *
     * @param string $indexName
     * @param string $tableName
     * @return string
     */
    public abstract function dropIndexSql(string $indexName, string $tableName): string;


    /**
     * get primary keys
     * dbq_23
     *
     * @param string $schema
     * @param string|null $table
     * @param string|null $column
     * @return array
     */
    public abstract function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array;


    /**
     * get UNIQUE constraints
     * dbq_26
     *
     * @param string $schema
     * @param string|null $table
     * @param string|null $column
     * @return array
     */
    public abstract function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array;

    /**
     * create primary key
     * dbq_17
     *
     * @param string $table
     * @param string $column
     * @return string sql
     */
    public abstract function createPrimaryKeySql(string $table, string $column): string;

    /**
     * create constraint
     * dbq_24
     *
     * @param string $table
     * @param string $column
     * @param string $constraintName
     * @return string sql
     */
    public abstract function createConstraintSql(string $table, string $column, string $constraintName): string;

    /**
     * drop primary key
     * dbq_18
     *
     * @param string $table
     * @return string sql
     */
    public abstract function dropPrimaryKeySql(string $table): string;

    /**
     * drop constraint
     * dbq_25
     *
     * @param string $table
     * @param string $constraintName
     * @return string sql
     */
    public abstract function dropConstraintSql(string $table, string $constraintName): string;


    ############# foreign keys ########################

    /**
     * get details for foreign keys for specific table or key name
     * dbq_3
     *
     * @param string $schema
     * @param string|null $table
     * @param string|null $column
     * @return string sql
     */
    public abstract function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string;


    /**
     * add foreign key
     * dbq_11
     *
     * @param string $parentTable
     * @param string $parentColumn
     * @param string $childTable
     * @param string $childColumn
     * @param string $keyName
     * @param string|null $restrict
     * @return string sql
     */
    public abstract function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string;


    /**
     * drop foreign key
     * dbq_6
     *
     * @param string $table
     * @param string $keyName
     * @return string sql
     */
    public abstract function dropForeignKeySql(string $table, string $keyName): string;


    ############# trigger ########################


    /**
     * get information about database trigger
     * dbf_3
     *
     * @param string $schema
     * @param string|null $triggerName
     * @return array
     */
    public abstract function getTriggerInformation(string $schema, ?string $triggerName = null): array;


    /**
     * drop database trigger
     * dbq_10
     *
     * @param string $table
     * @param string $triggerName
     * @return string sql
     */
    public abstract function dropTriggerSql(string $table, string $triggerName): string;

    /**
     * create trigger
     * dbq_13
     *
     * @param string $schema
     * @param string $triggerName
     * @param string $table
     * @param string $action
     * @param string $value
     * @param string $position
     * @return string sql
     */
    public abstract function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string;

    /**
     * limbas specific trigger function for limbas relation schema
     * dbq_14
     *
     * @param string $schema
     * @param string $relationTable
     * @param string $parentTable
     * @param string $parentColumn
     * @param string $action
     * @param string $childTable
     * @param string|null $childColumn
     * @param int $archive
     * @return string sql
     */
    public abstract function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn = null, int $archive = 1): string;


    /**
     * limbas specific trigger function for last modified
     * dbq_27
     *
     * @param int $tabId
     * @return string sql
     */
    public abstract function lastModifiedTriggerFunctionSql(int $tabId): string;

    ############# view ########################


    /**
     * get view definition
     * dbq_8
     *
     * @param string $schema
     * @param string $viewName
     * @return string sql
     */
    public abstract function getViewDefinitionSql(string $schema, string $viewName): string;

    /**
     * existing views
     * dbq_12
     *
     * @param string $schema
     * @param string|null $viewName
     * @return string sql
     */
    public abstract function getExistingViewsSql(string $schema, ?string $viewName = null): string;

    /**
     * create view
     * dbq_19
     *
     * @param string $viewName
     * @param string $definition
     * @return string sql
     */
    public abstract function createViewSql(string $viewName, string $definition): string;

    /**
     * drop view
     * dbq_20
     *
     * @param string $viewName
     * @return string sql
     */
    public abstract function dropViewSql(string $viewName): string;


    /**
     * rename view
     * dbf_24
     *
     * @param string $viewName
     * @param string $newName
     * @return string sql
     */
    public abstract function renameViewSql(string $viewName, string $newName): string;


    /**
     * check view dependencies
     * dbf_25
     *
     * @param string $schema
     * @param string $viewName
     * @param string|null $column
     * @return false|array
     */
    public abstract function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array;


    ############# tables ########################

    /**
     * rename table
     * dbf_17
     *
     * @param string $table
     * @param string $newName
     * @return string sql
     */
    public abstract function renameTableSql(string $table, string $newName): string;

    /**
     * list of tables / views
     * dbf_20
     *
     * @param string $schema
     * @param string|null $table
     * @param string|null $types
     * @return false|array
     */
    public abstract function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array;


    /**
     * drop table
     * dbq_28
     *
     * @param string $table
     * @return string sql
     */
    public abstract function dropTableSql(string $table): string;

    ############# SEQUENCE ########################

    /**
     * get all sequences
     * dbf_26
     *
     * @param string $schema
     * @return array
     */
    public abstract function getSequences(string $schema): array;


    /**
     * create sequence
     * dbf_21
     *
     * @param string $name
     * @param string|null $start
     * @return bool
     */
    public abstract function createSequence(string $name, ?string $start = null): bool;

    /**
     * drop sequence
     * dbf_22
     *
     * @param string $name
     * @return bool
     */
    public abstract function dropSequence(string $name): bool;

    ############# columns ########################

    /**
     * list of columns
     * dbf_5
     *
     * @param string $schema
     * @param string $table
     * @param string|null $column
     * @param bool $returnRs
     * @param bool $getMatView
     * @param null $mode
     * @return mixed
     */
    public abstract function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed;

    /**
     * modify column default
     * dbq_9
     *
     * @param string $schema
     * @param string $table
     * @param string $column
     * @param mixed|null $value
     * @return string sql
     */
    public abstract function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string;

    /**
     * rename column
     * dbq_7
     *
     * @param string $schema
     * @param string $table
     * @param string $column
     * @param string $newName
     * @return string sql
     */
    public abstract function renameColumnSql(string $schema, string $table, string $column, string $newName): string;

    /**
     * modify column type
     * dbq_15
     *
     * @param string $table
     * @param string $column
     * @param string $type
     * @return sql string
     */
    public abstract function modifyColumnTypeSql(string $table, string $column, string $type): string;

    /**
     * drop column
     * dbq_22
     *
     * @param string $table
     * @param string|array $columns
     * @return sql string
     */
    public abstract function dropColumnSql(string $table, string|array $columns): string;

    /**
     * add column
     * dbq_29
     *
     * @param string $table
     * @param string|array $column
     * @param string|array $type
     * @param string|array|null $default
     * @return string sql
     */
    public abstract function addColumnSql(string $table, string|array $column, string|array $type, string|array $default = null): string;


    ############# stored procedures ########################


    /**
     * limbas based lmb_vkn procedure
     * dbq_16
     *
     * @param string $schema
     * @param bool $dropOldProcedure
     * @return bool
     */
    public abstract function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool;

    /**
     * drop limbas based lmb_vkn procedure
     * dbq_21
     *
     * @return void
     */
    public abstract function dropLimbasVknFunction(): void;

    /**
     * pretty print table size
     * dbq_30
     *
     * @param string $schema
     * @param string $table
     * @return array table size, order size
     */
    public abstract function prettyPrintTableSize(string $schema, string $table): array;


    ################# BACKUP #################


    # -------- create Medium --------
    public abstract function createMedium(array $path, string $typ): bool;

    # -------- delete medium --------
    public abstract function deleteMedium(string $medium, string $typ): bool;

    # -------- start backup --------
    public abstract function backupData(string $medium, array $path, string $typ): array;

}
