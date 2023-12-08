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
        $db = dbq_0($DBA['DBHOST'],$DBA['DBNAME'],$DBA['DBUSER'],$DBA['DBPASS'],$DBA['ODBCDRIVER'],$DBA['PORT']);
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
    public static function select(string $table, array $fields = [], array $where = [], int $limit = null, array $orderBy = []): bool|PDOStatement
    {
        $db = self::get();

        $whereString = '';
        $orderString = '';

        $values = [];
        if (!empty($where)) {
            $whereFields = [];
            foreach($where as $field => $value) {
                if($value === null) {
                    $whereFields[] = $field . ' IS ' . LMB_DBDEF_NULL;
                } else {
                    $values[] = $value;
                    $whereFields[] = $field . ' = ?';
                }
                
            }
            $whereString = ' WHERE ' . implode(' AND ', $whereFields);
        }

        if(!empty($orderBy)) {
            $orderString = ' ORDER BY ' . implode(', ', array_map(
                    function ($field, $dir) { return $field . ' ' . $dir; },
                    array_keys($orderBy),
                    $orderBy
                ));
        }
        

        $limitString = '';
        if (!empty($limit)) {
            $limitString = ' LIMIT ' .$limit;
        }
        
        $fieldString = empty($fields) ? '*' : implode(',',$fields);

        $sql = 'SELECT ' . $fieldString . ' FROM ' . $table . $whereString . $orderString . $limitString;

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

        $whereString = '';
        if(!empty($where)) {
            $whereArray = [];
            foreach($where as $field => $value) {
                if($value === null) {
                    $whereArray[] = $field . ' IS ' . LMB_DBDEF_NULL;
                } else {
                    $values[] = $value;
                    $whereArray[] = $field . ' = ?';
                }
                $whereString = ' WHERE ' . implode(' AND ', $whereArray);
            }
        }
        

        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $fields) . $whereString;

        /*
        UPDATE LMB_MAIL_ACCOUNTS SET USER_ID = ?,TENANT_ID = ?,NAME = ?,EMAIL = ?,IS_ACTIVE = ?,IS_HIDDEN = ?,TRANSPORT_TYPE = ?,IMAP_HOST = ?,IMAP_PORT = ?,IMAP_USER = ?,SMTP_HOST = ?,SMTP_PORT = ?,SMTP_USER = ? WHERE ID = ?
        UPDATE LMB_MAIL_ACCOUNTS SET IS_DEFAULT = ? WHERE USER_ID IS NULL AND TENANT_ID IS NULL
        UPDATE LMB_MAIL_ACCOUNTS SET IS_DEFAULT = ? WHERE ID = ?*/

        return lmbdb_execute(lmbdb_prepare($db,$sql), $values);
    }

    /**
     * @param string $table
     * @param array $where
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
