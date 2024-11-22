<?php

/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\umgvar;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class UmgVar extends LimbasModel
{

    protected static string $tableName = 'LMB_UMGVAR';

    public function __construct(
        public int     $id,
        public int     $sort,
        public ?string $name = null,
        public ?string $value = null,
        public ?string $description = null,
        public ?int    $category = null,
        public ?string $fieldType = null,
        public ?string $fieldOptions = null
    )
    {
        //
    }


    public static function get(int $id): UmgVar|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }

    public static function all(array $where = []): array
    {
        $rs = Database::select(self::$tableName, where: $where, orderBy: ['CATEGORY' => 'asc', 'SORT' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            $output[] = new self(lmbdb_result($rs, 'ID'),
                intval(lmbdb_result($rs, 'SORT')),
                lmbdb_result($rs, 'FORM_NAME'),
                lmbdb_result($rs, 'NORM') ? : null,
                lmbdb_result($rs, 'BESCHREIBUNG') ? : null,
                lmbdb_result($rs, 'CATEGORY') ? : null,
                lmbdb_result($rs, 'FIELD_TYPE') ? : null,
                lmbdb_result($rs, 'FIELD_OPTIONS') ? : null
            );
        }

        return $output;
    }

    public function save(): bool
    {
        $data = [
            'SORT' => $this->sort,
            'FORM_NAME' => $this->name,
            'NORM' => $this->value,
            'BESCHREIBUNG' => $this->description,
            'CATEGORY' => $this->category,
            'FIELD_TYPE' => $this->fieldType,
            'FIELD_OPTIONS' => $this->fieldOptions
        ];

        if (empty($this->id)) {
            $nextId = next_db_id(self::$tableName);

            $data['ID'] = $nextId;

            $result = Database::insert(self::$tableName, $data);

            if ($result) {
                $this->id = $nextId;
            }
        }
        else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        return $result;
    }

    public function delete(): bool
    {
        return Database::delete(self::$tableName, ['ID' => $this->id]);
    }
    
}
