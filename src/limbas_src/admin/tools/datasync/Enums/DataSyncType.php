<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Enums;

enum DataSyncType: int
{
    case UNKNOWN = 0;
    case DELETE = 1;
    case CREATE = 2;
    case UPDATE = 3;


    public function name(): string
    {
        return match ($this) {
            self::DELETE => 'Delete',
            self::CREATE => 'Create',
            self::UPDATE => 'Update',
            default => 'Unknown'
        };
    }

}
