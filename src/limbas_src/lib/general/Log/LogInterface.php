<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\general\Log;

use Stringable;
use Throwable;

interface LogInterface
{
    
    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public static function error(string|Stringable|Throwable $message, array $context = []): void;

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public static function warning(string|Stringable|Throwable $message, array $context = []): void;

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public static function notice(string|Stringable|Throwable $message, array $context = []): void;

    /**
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public static function deprecated(string|Stringable $message, array $context = []): void;

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public static function info(string|Stringable|Throwable $message, array $context = []): void;

    
}
