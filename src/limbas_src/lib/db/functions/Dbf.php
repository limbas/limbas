<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\db\functions;

abstract class Dbf
{

    private static DbFunction $instance;


    private static function get(): ?DbFunction
    {
        global $DBA;

        $vendor = $DBA['DB'];

        if (!isset(self::$instance)) {
            self::$instance = match ($vendor) {
                'postgres' => new Postgres(),
                'mysql' => new MySQL(),
                'mssql' => new MSSQL(),
                'oracle' => new Oracle(),
                'hana' => new Hana(),
                'maxdb76' => new MaxDB76(),
                default => null
            };
        }

        return self::$instance;
    }


    public static function connect(string $host, string $database, string $user, string $password, string $driver = null, int $port = null): mixed
    {
        return self::get()?->connect($host, $database, $user, $password, $driver, $port);
    }

    public static function convertToDbTimestamp(mixed $date, ?bool $withoutTime = false): string
    {
        return self::get()?->convertToDbTimestamp($date, $withoutTime);
    }


    public static function convertFromDbTimestamp(string $value): false|int
    {
        return self::get()?->convertFromDbTimestamp($value);
    }

    public static function parseBlob(string $value): string
    {
        return self::get()?->parseBlob($value);
    }

    public static function parseString(string $value): string
    {
        return self::get()?->parseString($value);
    }

    public static function getSequence(string $name): mixed
    {
        return self::get()?->getSequence($name);
    }

    public static function handleCaseSensitive(?string $value): string
    {
        return self::get()?->handleCaseSensitive($value ?? '');
    }

    public static function sqlTimeDiff(string $startColumn, string $endColumn): string
    {
        return self::get()?->sqlTimeDiff($startColumn, $endColumn);
    }

    public static function sqlDateDiff(string $startColumn, string $endColumn): string
    {
        return self::get()?->sqlDateDiff($startColumn, $endColumn);
    }

    public static function calculateChecksum($field, $type = null): string
    {
        return self::get()?->calculateChecksum($field ?? '', intval($type));
    }

    public static function setVariables(): void
    {
        self::get()?->setVariables();
    }


    public static function version($DBA = null): array
    {
        return self::get()?->version($DBA);
    }


    public static function getIndicesSql(string $schema, ?string $indexName = null, ?string $tableName = null, ?string $columnName = null, ?bool $noPrimary = false, ?string $indexPrefix = null): string
    {
        return self::get()?->getIndicesSql($schema, $indexName, $tableName, $columnName, $noPrimary, $indexPrefix);
    }

    public static function createIndexSql(string $indexName, string $tableName, string $columnName, ?bool $isUnique = false): string
    {
        return self::get()?->createIndexSql($indexName, $tableName, $columnName, $isUnique);
    }

    public static function dropIndexSql(string $indexName, string $tableName): string
    {
        return self::get()?->dropIndexSql($indexName, $tableName);
    }

    public static function getPrimaryKeys(string $schema, ?string $table = null, ?string $column = null): array
    {
        return self::get()?->getPrimaryKeys($schema, $table, $column);
    }

    public static function getUniqueConstraints(string $schema, ?string $table = null, ?string $column = null): array
    {
        return self::get()?->getUniqueConstraints($schema, $table, $column);
    }

    public static function createPrimaryKeySql(string $table, string $column): string
    {
        return self::get()?->createPrimaryKeySql($table, $column);
    }

    public static function createConstraintSql(string $table, string $column, string $constraintName): string
    {
        return self::get()?->createConstraintSql($table, $column, $constraintName);
    }

    public static function dropPrimaryKeySql(string $table): string
    {
        return self::get()?->dropPrimaryKeySql($table);
    }

    public static function dropConstraintSql(string $table, string $constraintName): string
    {
        return self::get()?->dropConstraintSql($table, $constraintName);
    }

    public static function getForeignKeySql(string $schema, ?string $table, ?string $column = null): string
    {
        return self::get()?->getForeignKeySql($schema, $table, $column);
    }

    public static function addForeignKeySql(string $parentTable, string $parentColumn, string $childTable, string $childColumn, string $keyName, ?string $restrict = null): string
    {
        return self::get()?->addForeignKeySql($parentTable, $parentColumn, $childTable, $childColumn, $keyName, $restrict);
    }

    public static function dropForeignKeySql(string $table, string $keyName): string
    {
        return self::get()?->dropForeignKeySql($table, $keyName);
    }

    public static function getTriggerInformation(string $schema, ?string $triggerName = null): array
    {
        return self::get()?->getTriggerInformation($schema, $triggerName);
    }

    public static function dropTriggerSql(string $table, string $triggerName): string
    {
        return self::get()?->dropTriggerSql($table, $triggerName);
    }

    public static function createTriggerSql(string $schema, string $triggerName, string $table, string $action, string $value, string $position): string
    {
        return self::get()?->createTriggerSql($schema, $triggerName, $table, $action, $value, $position);
    }

    public static function relationTriggerFunctionSql(string $schema, string $relationTable, string $parentTable, string $parentColumn, string $action, string $childTable, ?string $childColumn, int $archive): string
    {
        return self::get()?->relationTriggerFunctionSql($schema, $relationTable, $parentTable, $parentColumn, $action, $childTable, $childColumn, $archive);
    }

    public static function lastModifiedTriggerFunctionSql(int $tabId): string
    {
        return self::get()?->lastModifiedTriggerFunctionSql($tabId);
    }

    public static function getViewDefinitionSql(string $schema, string $viewName): string
    {
        return self::get()?->getViewDefinitionSql($schema, $viewName);
    }

    public static function getExistingViewsSql(string $schema, ?string $viewName = null): string
    {
        return self::get()?->getExistingViewsSql($schema, $viewName);
    }

    public static function createViewSql(string $viewName, string $definition): string
    {
        return self::get()?->createViewSql($viewName, $definition);
    }

    public static function dropViewSql(string $viewName): string
    {
        return self::get()?->dropViewSql($viewName);
    }

    public static function renameViewSql(string $viewName, string $newName): string
    {
        return self::get()?->renameViewSql($viewName, $newName);
    }

    public static function getViewDependencies(string $schema, string $viewName, ?string $column = null): false|array
    {
        return self::get()?->getViewDependencies($schema, $viewName, $column);
    }

    public static function renameTableSql(string $table, string $newName): string
    {
        return self::get()?->renameTableSql($table, $newName);
    }

    public static function getTableList(string $schema, ?string $table = null, ?string $types = null): false|array
    {
        return self::get()?->getTableList($schema, $table, $types);
    }

    public static function dropTableSql(string $table): string
    {
        return self::get()?->dropTableSql($table);
    }

    public static function getSequences(string $schema): array
    {
        return self::get()?->getSequences($schema);
    }

    public static function createSequence(string $name, ?string $start = null): bool
    {
        return self::get()?->createSequence($name, $start);
    }

    public static function dropSequence(string $name): bool
    {
        return self::get()?->dropSequence($name);
    }

    public static function getColumns(string $schema, string $table, ?string $column, ?bool $returnRs = false, ?bool $getMatView = false, $mode = null): mixed
    {
        return self::get()?->getColumns($schema, $table, $column, $returnRs, $getMatView, $mode);
    }

    public static function setColumnDefaultSql(string $schema, string $table, string $column, mixed $value = null): string
    {
        return self::get()?->setColumnDefaultSql($schema, $table, $column, $value);
    }

    public static function renameColumnSql(string $schema, string $table, string $column, string $newName): string
    {
        return self::get()?->renameColumnSql($schema, $table, $column, $newName);
    }

    public static function modifyColumnTypeSql(string $table, string $column, string $type): string
    {
        return self::get()?->modifyColumnTypeSql($table, $column, $type);
    }

    public static function dropColumnSql(string $table, string|array $columns): string
    {
        return self::get()?->dropColumnSql($table, $columns);
    }

    public static function addColumnSql(string $table, string|array $column, string|array $type, string|array $default = null): string
    {
        return self::get()?->addColumnSql($table, $column, $type, $default);
    }

    public static function createLimbasVknFunction(string $schema, bool $dropOldProcedure = false): bool
    {
        return self::get()?->createLimbasVknFunction($schema, $dropOldProcedure);
    }

    public static function dropLimbasVknFunction(): void
    {
        self::get()?->dropLimbasVknFunction();
    }

    public static function prettyPrintTableSize(string $schema, string $table): array
    {
        return self::get()?->prettyPrintTableSize($schema, $table);
    }

    public static function createMedium(array $path, string $typ): bool
    {
        return self::get()?->createMedium($path, $typ);
    }

    public static function deleteMedium(string $medium, string $typ): bool
    {
        return self::get()?->deleteMedium($medium, $typ);
    }

    public static function backupData(string $medium, array $path, string $typ): array
    {
        return self::get()?->backupData($medium, $path, $typ);
    }

}
