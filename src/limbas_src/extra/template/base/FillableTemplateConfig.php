<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base;

use Limbas\extra\template\base\HtmlParts\DynamicDataPlaceholder;
use Limbas\extra\template\base\HtmlParts\DynamicDataPlaceholderInput;
use Limbas\extra\template\report\ReportTemplateConfig;
use ReflectionClass;

class FillableTemplateConfig extends ReportTemplateConfig {
    public function getDynamicDataPlaceholderInstance($description, $options): DynamicDataPlaceholder
    {
        // in case the arguments change
        $class = new ReflectionClass(DynamicDataPlaceholderInput::class);
        return $class->newInstanceArgs(func_get_args());
    }
}

