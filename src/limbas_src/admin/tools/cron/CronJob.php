<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\cron;

use Limbas\lib\db\Database;
use Limbas\lib\LimbasModel;

class CronJob extends LimbasModel
{

    protected static string $tableName = 'LMB_CRONTAB';
    public readonly ?CronConfig $config;

    public string $userName;

    /**
     * @param int $id
     * @param JobType $type
     * @param string $cronMinutes
     * @param string $cronHours
     * @param string $cronMonthdays
     * @param string $cronMonths
     * @param string $cronWeekdays
     * @param CronConfig|null $config
     * @param bool $active
     * @param string $description
     * @param int $userId
     */
    public function __construct(
        public int              $id = 0,
        public readonly JobType $type = JobType::UNSET,
        public string           $cronMinutes = '*',
        public string           $cronHours = '*',
        public string           $cronMonthdays = '*',
        public string           $cronMonths = '*',
        public string           $cronWeekdays = '*',
        public bool             $active = false,
        public string           $description = '',
        public int              $userId = 0,
        CronConfig              $config = null,
    )
    {
        $this->config = $config ?: CronConfig::fromJob($this);
        $this->setUserName();
    }

    private function setUserName(): void {
        global $userdat;
        $this->userName = $userdat['bezeichnung'][$this->userId] ?? '-';
    }


    /**
     * @param int $id
     * @return CronJob|null
     */
    public static function get(int $id): CronJob|null
    {
        $output = self::all(['ID' => $id]);
        if (empty($output)) {
            return null;
        }

        return $output[0];
    }


    /**
     * @param array $where
     * @param array $orderBy
     * @return array
     */
    public static function all(array $where = [], array $orderBy = ['ID' => 'asc']): array
    {
        $rs = Database::select(self::$tableName, where: $where, orderBy: $orderBy);

        $output = [];


        while (lmbdb_fetch_row($rs)) {
            $type = JobType::tryFrom(lmb_strtoupper(lmbdb_result($rs, 'TYPE'))) ?? JobType::UNSET;

            $output[] = new self(
                intval(lmbdb_result($rs, 'ID')),
                $type,
                lmbdb_result($rs, 'CRON_MINUTES') ?? '*',
                lmbdb_result($rs, 'CRON_HOURS') ?? '*',
                lmbdb_result($rs, 'CRON_MONTHDAYS') ?? '*',
                lmbdb_result($rs, 'CRON_MONTHS') ?? '*',
                lmbdb_result($rs, 'CRON_WEEKDAYS') ?? '*',
                boolval(lmbdb_result($rs, 'ACTIVE')),
                lmbdb_result($rs, 'DESCRIPTION') ?? '',
                intval(lmbdb_result($rs, 'USER_ID')),
                CronConfig::fromType($type, lmbdb_result($rs, 'CONFIG')),
            );
        }

        return $output;
    }


    public static function active(): array
    {
        return self::all(['ACTIVE' => true]);
    }


    /**
     * @return bool
     */
    public function save(): bool
    {
        $data = [
            'TYPE' => $this->type->value,
            'CRON_MINUTES' => $this->cronMinutes,
            'CRON_HOURS' => $this->cronHours,
            'CRON_MONTHDAYS' => $this->cronMonthdays,
            'CRON_MONTHS' => $this->cronMonths,
            'CRON_WEEKDAYS' => $this->cronWeekdays,
            'ACTIVE' => $this->active ? LMB_DBDEF_TRUE : LMB_DBDEF_FALSE,
            'DESCRIPTION' => $this->description,
            'USER_ID' => $this->userId,
            'CONFIG' => $this->config->toJson(),
        ];

        lmb_StartTransaction();

        if (empty($this->id)) {
            $nextId = next_db_id(self::$tableName);
            $data['ID'] = $nextId;
            $data['ERSTDATUM'] = date('Y-m-d H:i:s');
            $result = Database::insert(self::$tableName, $data);
            if ($result) {
                $this->id = $nextId;
            }
        } else {
            $result = Database::update(self::$tableName, $data, ['ID' => $this->id]);
        }

        if ($result) {
            lmb_EndTransaction(1);
        } else {
            lmb_EndTransaction(0);
        }

        $this->setUserName();

        return $result;
    }

    /**
     * @return bool
     */
    public function delete(): bool
    {
        return Database::delete(self::$tableName, ['ID' => $this->id]);
    }

    public function run(): void
    {
        if ($this->type === JobType::UNSET) {
            return;
        }

        $commandClass = $this->type->getCommandClass();
        /** @var CommandInterface $instance */
        $instance = new $commandClass();

        $instance->handle(['config' => $this->config->toArray()]);
    }

    public function expression(): string
    {
        return "{$this->cronMinutes} {$this->cronHours} {$this->cronMonthdays} {$this->cronMonths} {$this->cronWeekdays}";
    }
}
