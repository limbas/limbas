<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class Database
{

    private static $instances = [];
    
    
    public static function get($key = 0) {
        
        if (!array_key_exists($key,self::$instances)) {
            self::$instances[$key] = self::connect();
        }
        
        return self::$instances[$key];
    }
    
    
    private static function connect() {
        global $DBA;
        global $db; //legacy
        $db = dbq_0($DBA["DBHOST"],$DBA["DBNAME"],$DBA["DBUSER"],$DBA["DBPASS"],$DBA["ODBCDRIVER"],$DBA["PORT"]);
        return $db;
    }

    private static function close($key = 0) {
        lmbdb_close(self::get($key));
    }
    
    private static function closeAll() {
        lmbdb_close_all();
    }
    
    public static function checkIfInstalled() {
        $db = self::get();

        $sqlquery = 'SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE lower(TABLE_NAME) = \'lmb_umgvar\'';
        $rs = lmbdb_exec($db, $sqlquery);
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
    public static function select(string $table, array $fields = [], array $where = [], int $limit = null): bool|PDOStatement
    {
        $db = self::get();

        $whereString = '';
        
        if (!empty($where)) {
            $values = [];
            $whereFields = [];
            foreach($where as $field => $value) {
                $values[] = $value;
                $whereFields[] = $field . ' = ?';
            }
            $whereString = ' WHERE ' . implode(' AND ', $whereFields);
        }
        

        $limitString = '';
        if (!empty($limit)) {
            $limitString = ' LIMIT ' .$limit;
        }
        
        $fieldString = empty($fields) ? '*' : implode(',',$fields);

        $sql = 'SELECT ' . $fieldString . ' FROM ' . $table . $whereString . $limitString;

        $stmt = lmbdb_prepare($db,$sql);
        lmbdb_execute($stmt, $values);
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

        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fields) . ') VALUES (' . $valueString  . ')';
        
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

        $whereString = [];
        foreach($where as $field => $value) {
            $values[] = $value;
            $whereString[] = $field . ' = ?';
        }

        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $fields) . ' WHERE ' . implode(',', $whereString);

        return lmbdb_execute(lmbdb_prepare($db,$sql), $values);
    }

    /**
     * @param string $table
     * @param array $where
     * @param ?int $limit
     * @return bool
     */
    public static function delete(string $table, array $where): bool
    {
        $db = self::get();

        $values = [];
        $whereString = [];
        foreach($where as $field => $value) {
            $values[] = $value;
            $whereString[] = $field . ' = ?';
        }

        $sql = 'DELETE FROM ' . $table . ' WHERE ' . implode(',', $whereString);

        return lmbdb_execute(lmbdb_prepare($db,$sql), $values);
    }
}
