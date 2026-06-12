<?php

namespace Limbas\admin\tools\cron\configs;

use Limbas\admin\tools\cron\CronConfig;

class Backup extends CronConfig
{
    public static function getAvailableBackupTypes(): array
    {
        global $DB;

        $availableOptions = [
            'complete_data_backup' => 'Complete Data Backup',
        ];

        switch ($DB["DBVENDOR"]) {
            case 'maxdb76':
                $availableOptions['incremental_data_backup'] = 'Incremental Data Backup';
                $availableOptions['log_backup'] = 'Log Backup';
                break;
            default:
                break;
        }

        return count($availableOptions) <= 1 ? [] : $availableOptions;
    }

    public static function getAvailableBackupMediums(): array
    {
        global $DB;

        $availableOptions = [
            'file' => 'File',
        ];

        switch ($DB["DBVENDOR"]) {
            case 'maxdb76':
                $availableOptions['tape'] = 'Tape';
                break;
            default:
                break;
        }

        return count($availableOptions) <= 1 ? [] : $availableOptions;
    }
}