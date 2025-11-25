<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class MailAccount extends LimbasModel
{
    public const TRANSPORT_SMTP = 1;
    public const TRANSPORT_SENDMAIL = 2;
    public const TRANSPORT_NATIVE = 3;

    /**
     * @param string $name
     * @param string $email
     * @param int $transportType
     * @param string|null $imapHost
     * @param int|null $imapPort
     * @param string|null $imapUser
     * @param string|null $imapPassword
     * @param string|null $imapPath
     * @param string|null $smtpHost
     * @param int|null $smtpPort
     * @param string|null $smtpUser
     * @param string|null $smtpPassword
     * @param int|null $userId
     * @param int|null $tenantId
     * @param int|null $id
     * @param bool|null $isDefault defines the system-wide default account, only one per tenant
     * @param bool|null $isActive
     * @param bool|null $isHidden
     * @param bool|null $isSelected defines if the mail account is preselected when sending mails
     * @param int|null $mailTableId
     * @param int|null $mailSignatureId
     */
    public function __construct(
        public string   $name,
        public string   $email,
        public int      $transportType,
        public ?string  $imapHost = null,
        public ?int     $imapPort = null,
        public ?string  $imapUser = null,
        private ?string $imapPassword = null,
        public ?string  $imapPath = null,
        public ?string  $smtpHost = null,
        public ?int     $smtpPort = null,
        public ?string  $smtpUser = null,
        private ?string $smtpPassword = null,
        public ?int     $userId = null,
        public ?int     $tenantId = null,
        public ?int     $id = null,
        public ?bool    $isDefault = false,
        public ?bool    $isActive = true,
        public ?bool    $isHidden = false,
        public ?bool    $isSelected = false,
        public ?int     $mailTableId = null,
        public ?int     $mailSignatureId = null
    )
    {

    }


    /**
     * @param int $id
     * @return MailAccount|null
     */
    public static function get(int $id): MailAccount|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }

    /**
     * @return MailAccount|null
     */
    public static function getSystemDefaultAccount(): ?MailAccount
    {
        global $session;

        // check if system has multiple tenants => use the default for active tenant
        if (!empty($session['mid'])) {
            $where = [
                'IS_DEFAULT' => 1,
                'IS_ACTIVE' => 1,
                'TENANT_ID' => $session['mid']
            ];

            $output = MailAccount::all($where);
            if (!empty($output)) {
                return $output[0];
            }
        }

        $where = [
            'IS_DEFAULT' => 1,
            'IS_ACTIVE' => 1
        ];
        $output = MailAccount::all($where);
        if (empty($output)) {
            return null;
        }
        return $output[0];
    }

    /**
     * @param bool $onlyIds
     * @return array
     */
    public static function getUserMailAccounts(bool $onlyIds = false): array
    {
        global $session;

        $where = [
            'IS_HIDDEN' => 0,
            'IS_ACTIVE' => 1,
            'USER_ID' => null,
            'TENANT_ID' => null
        ];

        // get all global accounts without tenant
        $output = MailAccount::all($where);

        if (!empty($session['mid'])) {
            $where['TENANT_ID'] = $session['mid'];
            // get all global accounts of the current tenant
            $output = array_merge($output, MailAccount::all($where));
        }

        // get all user accounts
        $where = [
            'IS_HIDDEN' => 0,
            'IS_ACTIVE' => 1,
            'USER_ID' => $session['user_id']
        ];
        $output = array_merge($output, MailAccount::all($where));

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

        $rs = Database::select('LMB_MAIL_ACCOUNTS', where: $where, orderBy: ['ID' => 'asc']);

        $output = [];

        while (lmbdb_fetch_row($rs)) {

            $output[] = new self(
                lmbdb_result($rs, 'NAME') ?? '',
                lmbdb_result($rs, 'EMAIL') ?? '',
                intval(lmbdb_result($rs, 'TRANSPORT_TYPE')),
                lmbdb_result($rs, 'IMAP_HOST') ?? '',
                intval(lmbdb_result($rs, 'IMAP_PORT')),
                lmbdb_result($rs, 'IMAP_USER') ?? '',
                lmbdb_result($rs, 'IMAP_PASSWORD') ?? '',
                lmbdb_result($rs, 'IMAP_PATH') ?? '',
                    lmbdb_result($rs, 'SMTP_HOST') ?? '',
                intval(lmbdb_result($rs, 'SMTP_PORT')),
                lmbdb_result($rs, 'SMTP_USER') ?? '',
                lmbdb_result($rs, 'SMTP_PASSWORD') ?? '',
                intval(lmbdb_result($rs, 'USER_ID')),
                intval(lmbdb_result($rs, 'TENANT_ID')),
                intval(lmbdb_result($rs, 'ID')),
                boolval(lmbdb_result($rs, 'IS_DEFAULT')),
                boolval(lmbdb_result($rs, 'IS_ACTIVE')),
                boolval(lmbdb_result($rs, 'IS_HIDDEN')),
                    intval(lmbdb_result($rs, 'IS_SELECTED')),
                intval(lmbdb_result($rs, 'MAIL_TABLE_ID')),
                    intval(lmbdb_result($rs, 'DEFAULT_SIGNATURE_ID'))
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
            'EMAIL' => $this->email,
            'IS_ACTIVE' => $this->isActive ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'IS_HIDDEN' => $this->isHidden ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'IS_SELECTED' => $this->isSelected ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'TRANSPORT_TYPE' => $this->transportType,
            'IMAP_HOST' => $this->imapHost,
            'IMAP_PORT' => $this->imapPort,
            'IMAP_USER' => $this->imapUser,
            'IMAP_PATH' => trim($this->imapPath, '/'),
            'SMTP_HOST' => $this->smtpHost,
            'SMTP_PORT' => $this->smtpPort,
            'SMTP_USER' => $this->smtpUser,
            'MAIL_TABLE_ID' => $this->mailTableId,
            'DEFAULT_SIGNATURE_ID' => $this->mailSignatureId,
        ];

        if(!empty($this->mailTableId)) {
            $mailTable = $this->getMailTable();
            if(empty($mailTable)) {
                $data['MAIL_TABLE_ID'] = 0;
            }
        }

        if(!empty($this->mailSignatureId)) {
            $mailSignature = $this->getMailSignature();
            if(empty($mailSignature)) {
                $data['DEFAULT_SIGNATURE_ID'] = null;
            }
        }
        

        if (!empty($this->smtpPassword)) {
            $data['SMTP_PASSWORD'] = lmb_encrypt($this->smtpPassword);
        }

        if (!empty($this->imapPassword)) {
            $data['IMAP_PASSWORD'] = lmb_encrypt($this->imapPassword);
        }

        if (empty($this->id)) {

            $nextId = next_db_id('LMB_MAIL_ACCOUNTS');

            $data['ID'] = $nextId;

            $result = Database::insert('LMB_MAIL_ACCOUNTS', $data);

            if ($result) {
                $this->id = $nextId;
            }
        } else {
            $result = Database::update('LMB_MAIL_ACCOUNTS', $data, ['ID' => $this->id]);
        }

        if ($result) {
            $this->updateDefault();
            $this->updateSelected();
        }

        return $result;
    }


    private function updateDefault(): void
    {

        if ($this->isDefault && empty($this->userId)) {
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_DEFAULT' => 0], ['TENANT_ID' => $this->tenantId]);
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_DEFAULT' => 1], ['ID' => $this->id]);
        } else {
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_DEFAULT' => 0], ['ID' => $this->id]);
        }

    }

    private function updateSelected(): void
    {

        if ($this->isSelected && empty($this->userId)) {
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_SELECTED' => 0], ['TENANT_ID' => $this->tenantId, 'USER_ID' => null]);
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_SELECTED' => 1], ['ID' => $this->id]);
        } else {
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_SELECTED' => 0], ['USER_ID' => $this->userId]);
            Database::update('LMB_MAIL_ACCOUNTS', ['IS_SELECTED' => 1], ['ID' => $this->id]);
        }

    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        $db = Database::get();

        $sqlQuery = 'DELETE FROM LMB_MAIL_ACCOUNTS WHERE ID = ?';

        $stmt = lmbdb_prepare($db, $sqlQuery);
        return lmbdb_execute($stmt, [$this->id]);
    }

    /**
     * @return string
     */
    public function getImapPassword(): string
    {
        return lmb_decrypt($this->imapPassword);
    }

    /**
     * @return string
     */
    public function getSmtpPassword(): string
    {
        return lmb_decrypt($this->smtpPassword);
    }

    /**
     * @return string
     */
    public function getTransportName(): string
    {
        return match ($this->transportType) {
            self::TRANSPORT_SMTP => 'SMTP',
            self::TRANSPORT_SENDMAIL => 'Sendmail',
            self::TRANSPORT_NATIVE => 'php.ini',
            default => '',
        };
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

    public function getMailTable(): ?MailTable
    {
        return $this->mailTableId !== null ? MailTable::get($this->mailTableId) : null;
    }

    public function getMailSignature(): ?MailSignature
    {
        return $this->mailSignatureId !== null ? MailSignature::get($this->mailSignatureId) : null;
    }

}
