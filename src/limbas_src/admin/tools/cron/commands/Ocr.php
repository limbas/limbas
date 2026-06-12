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

class Ocr implements CommandInterface
{

    public function handle(array $parameters = []): void
    {
        global $error_msg, $timeout;

        require_once COREPATH . 'admin/tools/jobs/ocr.lib';

        $startTime = gettime();
        $config = $parameters['config'] ?? [];
        $files = $config['files'] ?? [];
        $includeSubdirs = $config['includeSubdirs'] ?? false;

        if (empty($files)) {
            return;
        }

        $lastResult = null;
        $processedAny = false;

        foreach ($files as $fileId) {
            if ($timeout) break;

            $targets = $includeSubdirs ? getSubDirectory($fileId) : [$fileId];

            foreach ($targets as $targetId) {
                if ($timeout) break;

                $lastResult = LmEntDirStartOcr($targetId);
                $processedAny = true;
            }
        }

        if ($processedAny) {
            $duration = number_format(gettime() - $startTime, 4, '.', '');

            $gnum = $lastResult['gnumf'] ?? ($lastResult['gnumfs'] ?? null);
            $jnum = $lastResult['jnumfs'] ?? null;

            $status = $error_msg ? 'FALSE' : 'TRUE';
            $comment = $error_msg ? "errors listed in TEMP/ocr_error.log" : '';

            fill_history("OCR", $status, $duration, $gnum, $jnum, $comment);
        }
    }
}
