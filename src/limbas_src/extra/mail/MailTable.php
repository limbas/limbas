<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\mail;

use Limbas\extra\mail\attachments\MailAttachment;

class MailTable
{

    protected array $relations = [];

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

        $mailTables = [];
        foreach ($gtab['tab_id'] as $tabId => $value) {
            if ($gtab['typ'][$tabId] === 6) {
                $mailTables[] = new MailTable(intval($tabId), $gtab['desc'][$tabId]);
            }
        }

        return $mailTables;
    }

    /**
     * @param int $id
     * @return MailTable|null
     */
    public static function get(int $id): ?MailTable
    {
        global $gtab;

        if (self::isMailTable($id)) {
            return new MailTable($id, $gtab['desc'][$id]);
        }
        return null;
    }

    protected static function isMailTable(int $tabId): bool
    {
        global $gtab;
        return array_key_exists($tabId, $gtab['typ']) && $gtab['typ'][$tabId] === 6;
    }

    public function setSendStatus(bool $status, int $mailTableDatId): void
    {

    }


    public function insertEntry(MailAccount $fromAccount, string|array $to, string $subject, string $message, ?array $attachments = null, string|array $cc = null, string|array $bcc = null): false|int
    {
        global $gfield;

        $recordId = new_data($this->id);

        if ($recordId === false) {
            return false;
        }

        if (!is_array($to)) {
            $to = [$to];
        }

        $fieldIds = $gfield[$this->id]['argresult_name'];

        $update = [
            $this->id . ',' . $fieldIds['FROM_ADDR'] . ',' . $recordId => $fromAccount->email,
            $this->id . ',' . $fieldIds['TO_ADDR'] . ',' . $recordId => json_encode($to),
            $this->id . ',' . $fieldIds['SUBJECT'] . ',' . $recordId => $subject,
            $this->id . ',' . $fieldIds['MESSAGE'] . ',' . $recordId => $message,
            $this->id . ',' . $fieldIds['MAIL_ACCOUNT_ID'] . ',' . $recordId => $fromAccount->id
        ];

        ob_start();
        update_data($update);

        $this->setRelations($recordId);

        /** @var MailAttachment $attachment */
        foreach ($attachments as $attachment) {
            $attachment->uploadToDms($this->id, $recordId, intval($fieldIds['ATTACHMENTS']));
        }

        ob_end_clean();

        return $recordId;
    }

    protected function setRelations(int $recordId): void
    {

        foreach ($this->relations as $relation) {

            if (empty($relation['fieldid'])) {
                $relation['fieldid'] = $this->resolveFieldId($relation['tabid']);
            }

            if ($relation['fieldid'] === false) {
                continue;
            }

            $rel = init_relation($relation['tabid'], $relation['fieldid'], $relation['id'], [$recordId]);
            set_relation($rel);
        }

    }

    protected function resolveFieldId(int $tabId): int|false
    {
        global $gfield;

        if (!array_key_exists($tabId, $gfield)) {
            return false;
        }

        foreach ($gfield[$tabId]['verkntabid'] as $fieldId => $vTabId) {
            if (self::isMailTable($vTabId)) {
                return intval($fieldId);
            }
        }

        return false;
    }

    public function addRelation(int $tabId, int $id, int $fieldId = null): bool
    {
        global $gtab;

        if (!in_array($tabId, $gtab['tab_id']) || $id <= 0) {
            return false;
        }

        $this->relations[] = [
            'tabid' => $tabId,
            'id' => $id,
            'fieldid' => $fieldId
        ];

        return true;
    }

    public function clearRelations(): void
    {
        $this->relations = [];
    }

    public function getRelations(): array
    {
        return $this->relations;
    }

}
