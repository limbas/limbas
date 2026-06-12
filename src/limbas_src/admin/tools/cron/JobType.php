<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 */

namespace Limbas\admin\tools\cron;

use Limbas\admin\tools\cron\commands\Backup as BackupCommand;
use Limbas\admin\tools\cron\commands\Datasync as DatasyncCommand;
use Limbas\admin\tools\cron\commands\Indize as IndizeCommand;
use Limbas\admin\tools\cron\commands\Ocr as OcrCommand;
use Limbas\admin\tools\cron\commands\Reminder as ReminderCommand;
use Limbas\admin\tools\cron\commands\Rsync as RsyncCommand;
use Limbas\admin\tools\cron\commands\StructureSync as StructureSyncCommand;
use Limbas\admin\tools\cron\commands\Template as TemplateCommand;

use Limbas\admin\tools\cron\configs\Backup as BackupConfig;
use Limbas\admin\tools\cron\configs\Datasync as DatasyncConfig;
use Limbas\admin\tools\cron\configs\Indize as IndizeConfig;
use Limbas\admin\tools\cron\configs\Ocr as OcrConfig;
use Limbas\admin\tools\cron\configs\Reminder as ReminderConfig;
use Limbas\admin\tools\cron\configs\Rsync as RsyncConfig;
use Limbas\admin\tools\cron\configs\StructureSync as StructureSyncConfig;
use Limbas\admin\tools\cron\configs\Template as TemplateConfig;

enum JobType: string
{
    case UNSET = 'UNSET';
    case BACKUP = 'BACKUP';
    case TEMPLATE = 'TEMPLATE';
    case DATASYNC = 'DATASYNC';
    case INDIZE = 'INDIZE';
    case REMINDER = 'REMINDER';
    case OCR = 'OCR';
    case RSYNC = 'RSYNC';
    case STRUCTURE_SYNC = 'STRUCTURESYNC';

    /**
     * @return class-string<CommandInterface>|''
     */
    public function getCommandClass(): string
    {
        return match ($this) {
            self::BACKUP         => BackupCommand::class,
            self::TEMPLATE       => TemplateCommand::class,
            self::DATASYNC       => DatasyncCommand::class,
            self::INDIZE         => IndizeCommand::class,
            self::REMINDER       => ReminderCommand::class,
            self::OCR            => OcrCommand::class,
            self::RSYNC          => RsyncCommand::class,
            self::STRUCTURE_SYNC => StructureSyncCommand::class,
            default              => '',
        };
    }

    /**
     * @return class-string<CronConfig>|''
     */
    public function getConfigClass(): string
    {
        return match ($this) {
            self::BACKUP         => BackupConfig::class,
            self::TEMPLATE       => TemplateConfig::class,
            self::DATASYNC       => DatasyncConfig::class,
            self::INDIZE         => IndizeConfig::class,
            self::REMINDER       => ReminderConfig::class,
            self::OCR            => OcrConfig::class,
            self::RSYNC          => RsyncConfig::class,
            self::STRUCTURE_SYNC => StructureSyncConfig::class,
            default              => '',
        };
    }
}