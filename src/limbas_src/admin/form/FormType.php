<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\form;

enum FormType: int
{

    case DETAILS = 1;

    case LIST = 2;

    case SEARCH = 3;
    
    public function name(): string
    {
        global $lang;
        return match ($this) {
            self::DETAILS => $lang[1183],
            self::LIST => $lang[1184],
            self::SEARCH => 'Search',
        };
    }
    
}
