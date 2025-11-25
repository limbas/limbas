<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib\tables;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class TableFilterGroup extends LimbasModel
{

    protected static string $tableName = 'LMB_SNAP_GROUP';
    
    protected static array $instances = [];

    /**
     * @param int $id
     * @param string $name
     * @param bool $intern
     */
    public function __construct(
        public int    $id,
        public string $name,
        public bool   $intern = true
    )
    {
        //
    }


    /**
     * @param int $id
     * @return TableFilter|null
     */
    public static function get(int $id): TableFilterGroup|null
    {
        if(array_key_exists($id, self::$instances)) {
            return self::$instances[$id];
        }
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        self::$instances[$id] = $output[0];

        return $output[0];
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {
        $rs = Database::select(self::$tableName, where: $where);
        
        $output = [];
        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                lmbdb_result($rs, 'NAME'),
                boolval(lmbdb_result($rs, 'INTERN'))
            );
        }

        return $output;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $data = [
            'NAME' => $this->name,
            'INTERN' => $this->intern ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
        ];

        lmb_StartTransaction();

        if (empty($this->id)) {
            $nextId = next_db_id(self::$tableName);
            $data['ID'] = $nextId;
            $result = Database::insert(self::$tableName, $data);
        } else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        if ($result) {
            lmb_EndTransaction(1);
        } else {
            lmb_EndTransaction(0);
        }

        return $result;
    }


    /**
     * @return bool
     */
    public function delete(): bool
    {
        lmb_StartTransaction();

        $deleted = Database::delete(self::$tableName, ['ID' => $this->id]);

        if (!$deleted) {
            lmb_EndTransaction(0);
        } else {

            Database::update('LMB_SNAP',['SNAPGROUP' => null],['SNAPGROUP' => $this->id]);
            
            lmb_EndTransaction(1);
        }

        return $deleted;
    }
    
    
}
