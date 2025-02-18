<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\db;

use Exception;
use PDO;
use PDOStatement;
use function dbq_0;

class Database
{

    private static array $instances = [];
    
    
    public static function get(int $key = 0) {
        
        if (!array_key_exists($key,self::$instances)) {
            self::$instances[$key] = self::connect();
        }
        
        return self::$instances[$key];
    }
    
    
    private static function connect(): PDO|bool|null
    {
        global $DBA;
        global $db; //legacy
        ob_start();
        $db = dbq_0($DBA['DBHOST'],$DBA['DBNAME'],$DBA['DBUSER'],$DBA['DBPASS'],$DBA['ODBCDRIVER'],$DBA['PORT']);
        ob_end_clean();
        return $db;
    }

    private static function close(int $key = 0): void
    {
        lmbdb_close(self::get($key));
    }
    
    private static function closeAll(): void
    {
        lmbdb_close_all();
    }
    
    public static function checkIfInstalled(): void
    {
        $db = Database::get();
        
        if($db === false || $db === null) {
            // config exists (checked in db_wrapper), but no connection to database
            throw new Exception('Database connection failed', 600);
        }

        $sqlquery = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE lower(TABLE_NAME) = \'lmb_umgvar\'';
        $rs = lmbdb_exec($db, $sqlquery);
        
        if(!$rs) {
            header('HTTP/1.1 302 Found (Moved Temporarily)');
            header('Location: install/');
            exit;
        }
        
        $data = lmbdb_fetch_array($rs);
        if (!is_array($data) || !array_key_exists('TABLE_NAME',$data) || empty($data['TABLE_NAME']) ) {
            header('HTTP/1.1 302 Found (Moved Temporarily)');
            header('Location: install/');
            exit;
        }
        
    }


    /**
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param int|null $limit
     * @return bool|PDOStatement
     */
    public static function select(string $table, array $fields = [], array $where = [], int $limit = null, array $orderBy = [], int $offset = null): bool|PDOStatement
    {
        $db = self::get();

        $orderString = '';
        [$whereString,$whereValues] = self::prepareWhereString($where);

        if(!empty($orderBy)) {
            $orderString = ' ORDER BY ' . implode(', ', array_map(
                    function ($field, $dir) { return strtoupper($field) . ' ' . $dir; },
                    array_keys($orderBy),
                    $orderBy
                ));
        }
        

        $limitString = '';
        if (!empty($limit)) {
            $limitString = ' LIMIT ' . $limit;
            if (!empty($offset)) {
                $limitString .= ' OFFSET ' . $offset;
            }
        }
        
        $fieldString = empty($fields) ? '*' : implode(',',array_map('strtoupper', $fields));

        $sql = 'SELECT ' . $fieldString . ' FROM ' . $table . $whereString . $orderString . $limitString;

        $stmt = lmbdb_prepare($db,$sql);
        lmbdb_execute($stmt, $whereValues);
        return $stmt;
    }
    
    /**
     * @param string $table
     * @param array $data
     * @return bool
     */
    public static function insert(string $table, array $data): bool
    {
        $db = self::get();

        $fields = array_keys($data);
        $values = array_values($data);
        
        $fieldCount = count($data);
        $valueString = '?' . str_repeat(',?', $fieldCount - 1);

        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', array_map('strtoupper', $fields)) . ') VALUES (' . $valueString  . ')';
        return lmbdb_execute(lmbdb_prepare($db,$sql), $values);
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool
     */
    public static function update(string $table, array $data, array $where): bool
    {
        $db = self::get();

        $values = [];
        $fields = [];
        foreach($data as $field => $value) {
            $fields[] = $field . ' = ?';
            $values[] = $value;
        }
        [$whereString,$whereValues] = self::prepareWhereString($where);
        $values = array_merge($values,$whereValues);
        

        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $fields) . $whereString;

        return lmbdb_execute(lmbdb_prepare($db,$sql), $values);
    }

    /**
     * @param string $table
     * @param array $where
     * @return bool
     */
    public static function delete(string $table, array $where = [], bool $all = false): bool
    {
        $db = self::get();
        [$whereString,$whereValues] = self::prepareWhereString($where);

        if($all === true) {
            $sql = 'DELETE FROM ' . $table  . ' WHERE ' . LMB_DBDEF_TRUE;   
        }
        elseif(empty($where)) {
            return false;
        }
        else {
            $sql = 'DELETE FROM ' . $table . $whereString;
        }

        return lmbdb_execute(lmbdb_prepare($db,$sql), $whereValues);
    }

    /**
     * @param string $sql
     * @param array $values
     * @return bool|PDOStatement
     */
    public static function query(string $sql, array $values = []): bool|PDOStatement
    {
        $db = self::get();
        $stmt = lmbdb_prepare($db,$sql);
        lmbdb_execute($stmt, $values);
        return $stmt;
    }


    /**
     * @param string $table
     * @param array $where
     * @return int
     */
    public static function count(string $table, array $where): int
    {
        $db = self::get();
        [$whereString,$whereValues] = self::prepareWhereString($where);

        $sql = 'SELECT COUNT(*) as C FROM ' . $table . ' ' . $whereString;

        $stmt = lmbdb_prepare($db,$sql);
        lmbdb_execute($stmt, $whereValues);
        if($stmt) {
            lmbdb_fetch_row($stmt);
            $count = intval(lmbdb_result($stmt,'C'));
        } else {
            $count = 0;
        }
        
        return $count;
    }
    
    protected static function prepareWhereString(array $where): array
    {
        $whereValues = [];
        $whereString = '';
        if(!empty($where)) {
            $whereArray = [];
            foreach($where as $field => $value) {
                if($value === null) {
                    $whereArray[] = strtoupper($field) . ' IS ' . LMB_DBDEF_NULL;
                } else {
                    $whereValues[] = $value;
                    $whereArray[] = strtoupper($field) . ' = ?';
                }
            }
            $whereString = ' WHERE ' . implode(' AND ', $whereArray);
        }
        return [$whereString, $whereValues];
    }
}
