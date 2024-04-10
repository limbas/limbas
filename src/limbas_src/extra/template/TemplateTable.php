<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template;

use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;
use Limbas\extra\template\report\ReportTemplateConfig;

class TemplateTable
{

    public function __construct(
        public int    $id,
        public string $name
    )
    {

    }


    /**
     * @return array
     */
    public static function all(): array
    {
        global $gtab;

        $templateTables = [];
        foreach ($gtab['tab_id'] as $tabId => $value) {
            if ($gtab['typ'][$tabId] === 8) {
                $templateTables[] = new TemplateTable(intval($tabId), $gtab['desc'][$tabId]);
            }
        }

        return $templateTables;
    }

    /**
     * @param int $id
     * @return TemplateTable|null
     */
    public static function get(int $id): ?TemplateTable
    {
        global $gtab;

        if (array_key_exists($id, $gtab['typ']) && $gtab['typ'][$id] === 8) {
            return new TemplateTable($id, $gtab['desc'][$id]);
        }
        if ($id <= 0) {
            return self::getDefaultTable();
        }
        return null;
    }

    public static function getDefaultTable(): ?TemplateTable
    {
        global $gtab;
        $templateTabId = intval($gtab['argresult_id']['LMB_DEFAULT_TEMPLATES']) ?: 1;
        return self::get($templateTabId);
    }


    public function insertRootElement(string $name, ?int $oldTemplateTableId = null, ?int $oldTemplateElementId = null): bool|int
    {
        //global $gtab;
        require_once(COREPATH . 'gtab/gtab.lib');

        $templateContent = '';

        // move root element 
        if (!empty($oldTemplateTableId) && !empty($oldTemplateElementId)) {

            TemplateConfig::$instance = new ReportTemplateConfig([], null, 0, null);
            $oldTemplate = TemplateElement::fromId($oldTemplateTableId, $oldTemplateElementId);
            if ($oldTemplate !== null) {
                $templateContent = $oldTemplate->getHtml();
            }

            // delete old template otherwise it is a copy
            if($this->id !== $oldTemplateTableId) {
                del_data($oldTemplateTableId, $oldTemplateElementId);
            }
        }

        $templateElementId = intval(new_data($this->id));

        if (empty($templateElementId)) {
            return false;
        }

        $name = str_replace(['Ä', 'Ö', 'Ü', 'ä', 'ö', 'ü', 'ß'], ['Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss'], $name);
        $name = preg_replace('/[^a-z0-9]/i', '_', $name);
        $name = preg_replace('/_+/', '_', $name);
        $update = [
            "$this->id,1,$templateElementId" => "root_body_$name",
            "$this->id,2,$templateElementId" => $templateContent
        ];
        update_data($update);

        return $templateElementId;
    }

}
