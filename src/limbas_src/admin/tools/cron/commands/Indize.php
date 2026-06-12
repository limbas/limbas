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

class Indize implements CommandInterface
{

    public function handle(array $parameters = []): void
    {
        global $error_msg, $timeout;

        require_once COREPATH . 'admin/tools/jobs/indize.lib';

        $startTime = gettime();
        $config = $parameters['config'] ?? [];
        $fields = $config['fields'] ?? [];
        $files = $config['files'] ?? [];
        $includeSubdirs = $config['includeSubdirs'] ?? false;

        if (empty($fields) && empty($files)) {
            return;
        }

        $processedTypes = [];
        $lastResult = null;

        foreach ($fields as $entry) {
            if ($timeout) break;
            $tableId = $entry['tab'] ?? null;
            $fieldId = $entry['field'] ?? null;

            if ($tableId && $fieldId) {
                $info = table_infos($tableId, $fieldId);
                $lastResult = prepare_memo_indize($info);
                $processedTypes['Databased Index'] = true;
            }
        }

        if (!$timeout) {
            foreach ($files as $fileId) {
                if ($timeout) break;

                $targets = $includeSubdirs ? getSubDirectory($fileId) : [$fileId];

                foreach ($targets as $targetId) {
                    if ($timeout) break;
                    $lastResult = prepare_filelevel_indize($targetId);
                    $processedTypes['Filestructure Index'] = true;
                }
            }
        }

        if (!empty($processedTypes)) {
            $typeKeys = array_keys($processedTypes);
            $finalLabel = (count($typeKeys) > 1) ? "Mixed Index" : $typeKeys[0];
            $duration = number_format(gettime() - $startTime, 4, '.', '');

            $gnum = $lastResult['gnumf'] ?? ($lastResult['gnumfs'] ?? null);
            $jnum = $lastResult['jnumfs'] ?? null;

            $status = $error_msg ? 'FALSE' : 'TRUE';
            $comment = $error_msg ? "errors listed in TEMP/index_error.log" : '';

            fill_history($finalLabel, $status, $duration, $gnum, $jnum, $comment);
        }
    }

}
