<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\setup\fonts;

use Limbas\lib\LimbasController;

class FontController extends LimbasController
{

    /**
     * @param array $request
     * @return array
     */
    public function handleRequest(array $request): array
    {
        return [];
    }


    public function index(): string
    {
        global $lang;
        global $setFont;

        if (!empty($setFont)) {
            Font::applyFonts($setFont);
        }

        $fonts = Font::getSystemFonts();

        ob_start();
        require(COREPATH . 'admin/setup/fonts/fonts.php');
        return ob_get_clean() ?: '';
    }

}
