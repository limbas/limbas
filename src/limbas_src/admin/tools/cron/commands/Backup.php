<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\cron\commands;

use Limbas\admin\tools\cron\CommandInterface;

class Backup implements CommandInterface
{

    public function handle(array $parameters = []): void
    {
        $backup_alive = lmbdb_result($rs, "ALIVE");

        [
            'backupType' => $type,
            'backupMedium' => $medium,
            'backupTarget' => $target,
            'backupAlive' => $alive,
        ] = $parameters['config'];

        $legacyMedium = match ($medium) {
            'file' => 1,
            'tape' => 2,
            default => 1,
        };

        $legacyType = match ($type) {
            'complete_data_backup' => 1,
            'incremental_data_backup' => 2,
            'log_backup' => 3,
            default => 1,
        };

        $device = 1;

        require_once(COREPATH . 'admin/tools/backup.dao');
        lmb_backup_database($target, $target, $legacyMedium, $device, $legacyType);
    }
}
