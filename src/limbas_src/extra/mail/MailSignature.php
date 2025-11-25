<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\extra\template\mail\MailTemplateRender;
use Limbas\lib\db\Database;
use Limbas\lib\general\Log\Log;
use Limbas\lib\LimbasModel;
use Throwable;

class MailSignature extends LimbasModel
{
    
    public string $renderedContent;

    /**
     * @param string $name
     * @param string $content
     * @param int|null $userId
     * @param int|null $tenantId
     * @param int|null $id
     * @param bool|null $isDefault defines the system-wide default signature, only one per tenant
     * @param bool|null $isActive
     * @param bool|null $isHidden
     */
    public function __construct(
        public string   $name,
        public string   $content,
        public ?int     $userId = null,
        public ?int     $tenantId = null,
        public ?int     $id = null,
        public ?bool    $isDefault = false,
        public ?bool    $isActive = true,
        public ?bool    $isHidden = false
    )
    {
        $this->renderedContent = $content;
        $this->render();
    }


    /**
     * @param int $id
     * @return MailSignature|null
     */
    public static function get(int $id): MailSignature|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    /**
     * @return MailSignature|null
     */
    public static function getSystemDefaultMailSignature(): ?MailSignature
    {
        global $session;

        // check if system has multiple tenants => use the default for active tenant
        if (!empty($session['mid'])) {
            $where = [
                'IS_DEFAULT' => 1,
                'IS_ACTIVE' => 1,
                'TENANT_ID' => $session['mid']
            ];

            $output = MailSignature::all($where);
            if (!empty($output)) {
                return $output[0];
            }
        }

        $where = [
            'IS_DEFAULT' => 1,
            'IS_ACTIVE' => 1
        ];
        $output = MailSignature::all($where);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }

    /**
     * @param bool $onlyIds
     * @return array
     */
    public static function getUserMailSignatures(bool $onlyIds = false): array
    {
        global $session;

        $where = [
            'IS_HIDDEN' => 0,
            'IS_ACTIVE' => 1,
            'USER_ID' => null,
            'TENANT_ID' => null
        ];

        // get all global mail signature without tenant
        $output = MailSignature::all($where);

        if (!empty($session['mid'])) {
            $where['TENANT_ID'] = $session['mid'];
            // get all global mail signatures of the current tenant
            $output = array_merge($output, MailSignature::all($where));
        }

        // get all user mail signatures
        $where = [
            'IS_HIDDEN' => 0,
            'IS_ACTIVE' => 1,
            'USER_ID' => $session['user_id']
        ];
        $output = array_merge($output, MailSignature::all($where));

        $uniqueIds = array_unique(array_column($output, 'id'));
        if ($onlyIds) {
            return $uniqueIds;
        }
        return array_values(array_intersect_key($output, $uniqueIds));
    }


    /**
     * @param array $where
     * @return array
     */
    public static function all(array $where = []): array
    {

        $rs = Database::select('LMB_MAIL_SIGNATURES', where: $where, orderBy: ['ID' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {
            
            $output[] = new self(
                lmbdb_result($rs, 'NAME') ?? '',
                lmbdb_result($rs, 'CONTENT') ?? '',
                intval(lmbdb_result($rs, 'USER_ID')),
                intval(lmbdb_result($rs, 'TENANT_ID')),
                intval(lmbdb_result($rs, 'ID')),
                boolval(lmbdb_result($rs, 'IS_DEFAULT')),
                boolval(lmbdb_result($rs, 'IS_ACTIVE')),
                boolval(lmbdb_result($rs, 'IS_HIDDEN'))
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
            'USER_ID' => $this->userId,
            'TENANT_ID' => $this->tenantId,
            'NAME' => $this->name,
            'CONTENT' => $this->content,
            'IS_ACTIVE' => $this->isActive ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'IS_HIDDEN' => $this->isHidden ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
        ];

        if (empty($this->id)) {

            $nextId = next_db_id('LMB_MAIL_SIGNATURES');

            $data['ID'] = $nextId;

            $result = Database::insert('LMB_MAIL_SIGNATURES', $data);

            if ($result) {
                $this->id = $nextId;
            }
        } else {
            $result = Database::update('LMB_MAIL_SIGNATURES', $data, ['ID' => $this->id]);
        }

        if ($result) {
            $this->updateDefault();
        }

        return $result;
    }


    private function updateDefault(): void
    {

        if ($this->isDefault && empty($this->userId)) {
            Database::update('LMB_MAIL_SIGNATURES', ['IS_DEFAULT' => 0], ['TENANT_ID' => $this->tenantId]);
            Database::update('LMB_MAIL_SIGNATURES', ['IS_DEFAULT' => 1], ['ID' => $this->id]);
        } else {
            Database::update('LMB_MAIL_SIGNATURES', ['IS_DEFAULT' => 0], ['ID' => $this->id]);
        }

    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $db = Database::get();

        $sqlQuery = 'DELETE FROM LMB_MAIL_SIGNATURES WHERE ID = ?';

        $stmt = lmbdb_prepare($db, $sqlQuery);
        return lmbdb_execute($stmt, [$this->id]);
    }


    public function getTenantName(): string
    {
        global $lmmultitenants;

        if (!empty($this->tenantId) && !empty($lmmultitenants['name']) && array_key_exists($this->tenantId, $lmmultitenants['name'])) {
            return $lmmultitenants['name'][$this->tenantId];
        }

        return '';
    }

    public function getUserName(): string
    {
        global $userdat;

        if (!empty($this->userId) && !empty($userdat['username']) && array_key_exists($this->userId, $userdat['username'])) {
            return $userdat['username'][$this->userId];
        }

        return '';
    }
    
    
    public function render(): void
    {
        try {
            $templateRender = new MailTemplateRender();
            $this->renderedContent = $templateRender->getHtml([],0,0,0,0, content: $this->content);
        }
        catch (Throwable $t) {
            Log::warning($t);
        }
    }


}
