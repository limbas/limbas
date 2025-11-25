<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\tinymce;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class TinyMceConfig extends LimbasModel
{

    /**
     * @param string $name
     * @param array $config
     * @param int|null $id
     * @param bool|null $isDefault defines the system-wide default editor configuration
     */
    public function __construct(
        public string $name,
        public array  $config,
        public ?int   $id = null,
        public ?bool  $isDefault = false
    )
    {
        //
    }


    /**
     * @param int $id
     * @return TinyMceConfig|null
     */
    public static function get(int $id): TinyMceConfig|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    /**
     * @return TinyMceConfig|null
     */
    public static function getDefault(): ?TinyMceConfig
    {
        $where = [
            'IS_DEFAULT' => 1
        ];
        $output = TinyMceConfig::all($where);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {

        $rs = Database::select('LMB_TINYMCE_CONFIGS', where: $where, orderBy: ['ID' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {

            $output[] = new self(
                lmbdb_result($rs, 'NAME') ?? '',
                json_decode(lmbdb_result($rs, 'CONFIG') ?? '[]', true) ?? [],
                intval(lmbdb_result($rs, 'ID')),
                boolval(lmbdb_result($rs, 'IS_DEFAULT'))
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
            'CONFIG' => json_encode($this->config)
        ];

        if (empty($this->id)) {

            $nextId = next_db_id('LMB_TINYMCE_CONFIGS');

            $data['ID'] = $nextId;

            $result = Database::insert('LMB_TINYMCE_CONFIGS', $data);

            if ($result) {
                $this->id = $nextId;
            }
        } else {
            $result = Database::update('LMB_TINYMCE_CONFIGS', $data, ['ID' => $this->id]);
        }

        if ($result) {
            $this->updateDefault();
        }

        return $result;
    }


    private function updateDefault(): void
    {
        Database::update('LMB_TINYMCE_CONFIGS', ['IS_DEFAULT' => $this->isDefault ? 1 : 0], ['ID' => $this->id]);
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return Database::delete('LMB_TINYMCE_CONFIGS',['ID' => $this->id]);
    }
    
}
