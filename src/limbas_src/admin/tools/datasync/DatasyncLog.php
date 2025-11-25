<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\tools\datasync;

use Limbas\lib\general\Log\Log;
use Limbas\lib\general\Log\Logger;

abstract class DatasyncLog extends Log
{
    protected static Logger $logger;
    protected static string $logFile;
    
    protected static function init(): void
    {
        if(!isset(static::$logger)) {
            static::$logger = Logger::get('datasync', false, static::$logFile ?? null);
        }
    }
    
    public function appendLog(array $logMessages, string $prefix = ''): void
    {
        static::$logger->appendLog($logMessages,$prefix);
    }
    
}
