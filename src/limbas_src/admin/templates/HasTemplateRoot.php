<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\templates;

use Database;
use Limbas\extra\template\TemplateTable;

trait HasTemplateRoot
{

    /**
     * @param int|bool $newRootTemplateTabId
     * @return bool
     */
    public function insertOrUpdateTemplateRoot(int|bool $newRootTemplateTabId = false): bool
    {
        $insert = false;
        if (empty($this->rootTemplateElementId)) {
            $insert = true;
        }

        if ($newRootTemplateTabId === false && !$insert) {
            // element already exists
            return true;
        }

        $templateTable = TemplateTable::get($this->rootTemplateTabId);
        if ($templateTable === null) {
            return true;
        }

        if ($newRootTemplateTabId === $templateTable->id && !$insert) {
            // new and old root table are the same and element already exists
            return true;
        }

        $changeTable = false;
        if ($newRootTemplateTabId !== false && $insert) {
            $templateTable = TemplateTable::get($newRootTemplateTabId);

        } elseif ($newRootTemplateTabId !== false) {
            $templateTable = TemplateTable::get($newRootTemplateTabId);
            $changeTable = true;
        }


        if ($changeTable && !$insert) {
            $rootTemplateElementId = $templateTable->insertRootElement($this->name, $this->rootTemplateTabId, $this->rootTemplateElementId);
        } else {
            $rootTemplateElementId = $templateTable->insertRootElement($this->name);
        }


        if ($rootTemplateElementId !== false) {
            $this->rootTemplateTabId = $templateTable->id;
            $this->rootTemplateElementId = $rootTemplateElementId;

            if (self::$tableName === 'LMB_MAIL_TEMPLATES') {
                return Database::update('LMB_MAIL_TEMPLATES', ['ROOT_TEMPLATE_TAB_ID' => $this->rootTemplateTabId, 'ROOT_TEMPLATE_ELEMENT_ID' => $this->rootTemplateElementId], ['ID' => $this->id]);
            } elseif (self::$tableName === 'LMB_REPORT_LIST') {
                return Database::update('LMB_REPORT_LIST', ['ROOT_TEMPLATE' => $this->rootTemplateTabId, 'ROOT_TEMPLATE_ID' => $this->rootTemplateElementId], ['ID' => $this->id]);
            }
            return false;
        }

        return false;
    }

}
