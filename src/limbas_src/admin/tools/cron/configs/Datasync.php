<?php

namespace Limbas\admin\tools\cron\configs;

use Limbas\admin\tools\cron\CronConfig;
use Limbas\lib\db\Database;

class Datasync extends CronConfig
{
    public function getTemplate(): string {
        return ($this->getAvailableTemplates())[$this->template] ?? '';
    }

    public function getAvailableTemplates(): array
    {
        $templates = [];
        $rs = Database::query("SELECT ID, NAME FROM LMB_SYNC_TEMPLATE WHERE TABID IS NULL OR TABID = 0 ORDER BY NAME");
        while (lmbdb_fetch_row($rs)) {
            $templates[lmbdb_result($rs, 'ID')] = lmbdb_result($rs, 'NAME');
        }
        return $templates;
    }
}