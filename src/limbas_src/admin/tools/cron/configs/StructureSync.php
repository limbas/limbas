<?php

namespace Limbas\admin\tools\cron\configs;

use Limbas\admin\tools\cron\CronConfig;
use Limbas\lib\db\Database;

class StructureSync extends CronConfig
{
    public function getTemplate(): string {
        return ($this->getAvailableTemplates())[$this->template] ?? '';
    }

    public function getAvailableTemplates(): array
    {
        $templates = [];
        $rs = Database::query("SELECT ID, NAME FROM LMB_SYNCSTRUCTURE_TEMPLATE ORDER BY NAME");
        while (lmbdb_fetch_row($rs)) {
            $templates[lmbdb_result($rs, 'ID')] = lmbdb_result($rs, 'NAME');
        }
        return $templates;
    }
}