<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\mailTemplates;

use Database;
use Limbas\extra\template\mail\MailTemplateRender;
use Limbas\extra\template\TemplateTable;

class MailTemplate
{

    /**
     * @param string $name
     * @param int $tabId
     * @param string $description
     * @param string|null $createDate
     * @param string|null $editDate
     * @param int|null $createUser
     * @param int|null $editUser
     * @param string|null $css
     * @param string|null $savedTemplate
     * @param int|null $parentId
     * @param int|null $rootTemplateTabId
     * @param int|null $rootTemplateElementId
     * @param int|null $id
     */
    public function __construct(
        public string  $name,
        public int     $tabId,
        public string  $description = '',
        public ?string $createDate = null,
        public ?string $editDate = null,
        public ?int    $createUser = null,
        public ?int    $editUser = null,
        public ?string $css = null,
        public ?string $savedTemplate = null,
        public ?int    $parentId = null,
        public ?int    $rootTemplateTabId = null,
        public ?int    $rootTemplateElementId = null,
        public ?int    $id = null
    )
    {

    }


    /**
     * @param int $id
     * @return MailTemplate|null
     */
    public static function get(int $id): MailTemplate|null
    {
        $output = self::all(['ID' => $id]);
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

        $rs = Database::select('LMB_MAIL_TEMPLATES', where: $where, orderBy: ['TAB_ID' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {

            $output[] = new self(
                lmbdb_result($rs, 'NAME') ?? '',
                intval(lmbdb_result($rs, 'TAB_ID')),
                lmbdb_result($rs, 'DESCRIPTION') ?? '',
                lmbdb_result($rs, 'ERSTDATUM') ?? '',
                lmbdb_result($rs, 'EDITDATUM') ?? '',
                intval(lmbdb_result($rs, 'ERSTUSER')),
                intval(lmbdb_result($rs, 'EDITUSER')),
                lmbdb_result($rs, 'CSS') ?? '',
                lmbdb_result($rs, 'SAVED_TEMPLATE') ?? '',
                intval(lmbdb_result($rs, 'PARENT_ID')) ?: null,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE_TAB_ID')) ?: null,
                intval(lmbdb_result($rs, 'ROOT_TEMPLATE_ELEMENT_ID')) ?: null,
                intval(lmbdb_result($rs, 'ID'))
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
            'TAB_ID' => $this->tabId,
            'DESCRIPTION' => $this->description,
            'CSS' => $this->css,
            'SAVED_TEMPLATE' => $this->savedTemplate,
            'PARENT_ID' => $this->parentId,
            'ROOT_TEMPLATE_TAB_ID' => $this->rootTemplateTabId,
            'ROOT_TEMPLATE_ELEMENT_ID' => $this->rootTemplateElementId
        ];

        if (empty($this->id)) {

            $nextId = next_db_id('LMB_MAIL_TEMPLATES');

            $data['ID'] = $nextId;

            $result = Database::insert('LMB_MAIL_TEMPLATES', $data);

            if ($result) {
                $this->id = $nextId;
                $this->insertOrUpdateTemplateRoot();

            }
        } else {
            $result = Database::update('LMB_MAIL_TEMPLATES', $data, ['ID' => $this->id]);
        }

        return $result;
    }


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
            return Database::update('LMB_MAIL_TEMPLATES', ['ROOT_TEMPLATE_TAB_ID' => $this->rootTemplateTabId, 'ROOT_TEMPLATE_ELEMENT_ID' => $this->rootTemplateElementId], ['ID' => $this->id]);
        }

        return false;
    }


    /**
     * @return bool
     */
    public function delete(): bool
    {
        $db = Database::get();

        $sqlQuery = 'DELETE FROM LMB_MAIL_TEMPLATES WHERE ID = ?';

        $stmt = lmbdb_prepare($db, $sqlQuery);
        return lmbdb_execute($stmt, [$this->id]);
    }


    /**
     * @return string
     */
    public function getTabName(): string
    {
        global $gtab;

        if (empty($this->tabId) || !array_key_exists($this->tabId, $gtab['desc'])) {
            return 'global';
        }

        return $gtab['desc'][$this->tabId] ?: '';
    }

    public function getRendered(int $gtabid, int $id, array $resolvedTemplateGroups = [], array $resolvedDynamicData = []) {
        $templateRender = new MailTemplateRender();
        return $templateRender->getHtml([],$this->rootTemplateTabId,$this->rootTemplateElementId,$gtabid,$id, $resolvedTemplateGroups, $resolvedDynamicData);
    }
    
}
