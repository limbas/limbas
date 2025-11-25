<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\lib\general\Log;

enum LogLevel: int
{
    case INFO = 0;
    case DEPRECATED = 16384; //E_USER_DEPRECATED 
    case NOTICE = 8; // E_NOTICE
    case WARNING = 2; // E_WARNING
    case ERROR = 1; // E_ERROR
    
    

    public function name(): string
    {
        return match ($this) {
            self::INFO => 'INFO',
            self::DEPRECATED => 'DEPRECATED',
            self::NOTICE => 'NOTICE',
            self::WARNING => 'WARNING',
            self::ERROR => 'ERROR'
        };
    }
    
    
}
