<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace extra\dashboard\widgets;

use extra\dashboard\Widget;

class WidgetLmbinfo extends Widget
{
    public function __construct(int $id, ?array $options = [])
    {
        $this->minWidth = 5;
        $this->minHeight = 5;
        $this->noresize = true;
        $this->name = 'Limbas Info';
        $this->icon = 'lmb-info-circle';
        parent::__construct($id, $options);
    }

    /**
     * @return string
     */
    protected function internalRender(): string
    {
        global $umgvar;

        ob_start();
        include('html/lmbinfo.php');
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }
}
