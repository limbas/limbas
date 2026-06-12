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
use Limbas\admin\tools\datasync\DatasyncProcess;
use Throwable;

class Datasync implements CommandInterface
{

    public function handle(array $parameters = []): void
    {

        define('IS_DATASYNC', 1);

        require_once(COREPATH . 'admin/tools/datasync/loaddatasync.php');

        try {
            $templateId = $parameters['config']['template'];

            $datasync = new DatasyncProcess();
            $clientid = $datasync->start($templateId);

            // rsync files
            if (is_numeric($clientid) && $clientid > 0) {
                lmb_fileSync_init($templateId, $clientid);
            }
        } catch (Throwable $t) {
            error_log(print_r($t, 1));
        }

    }
}
