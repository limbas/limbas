<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Enums;

enum ConflictMode: int
{

    case DISABLED = 0;
    case MAIN_WINS = 1;
    case CLIENT_WINS = 2;
    case LATEST_WINS = 3;
    case MANUAL = 4;


    public function text(): string
    {
        return match ($this) {
            self::DISABLED => 'Disabled',
            self::MAIN_WINS => 'Main wins',
            self::CLIENT_WINS => 'Client wins',
            self::LATEST_WINS => 'Latest wins',
            self::MANUAL => 'Manual',
        };
    }
}
