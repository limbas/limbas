<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace extra\dashboard;

use extra\dashboard\widgets\widgetError;

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'Widget.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'widgets/WidgetError.php');

abstract class WidgetFactory
{
    /**
     * creates an instance of relevant widget class
     *
     * @param int $id
     * @param string $name
     * @param array $options
     * @return widgetError|Widget
     * @global array $umgvar
     */
    public static function create(int $id, string $name, array $options): widgetError|Widget
    {
        global $umgvar;

        $className = 'Widget' . ucfirst(strtolower($name));
        $internal = __DIR__ . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . $className . '.php';
        $external = EXTENSIONSPATH . 'dashboard/' . $className . '.php';

        if (file_exists($internal)) {
            require_once($internal);
        } else if (file_exists($external)) {
            require_once($external);
        }

        $className = '\\extra\\dashboard\\widgets\\' . $className;
        
        if (!class_exists($className)) {
            $options['widgetName'] = $name;
            return new widgetError(0, $options);
        }
        
        return new $className($id, $options);
    }


    /**
     * returns an array of all widgets
     *
     * @param array $extensionWidgets
     * @return array
     */
    public static function getAllWidgets(array $extensionWidgets = []): array
    {
        $internalWidgets = ['Chart', 'Lmbinfo', 'Function']; // 'Table',

        $widgets = [];
        foreach ($internalWidgets as $widget) {
            $wName = 'Widget' . $widget;
            require_once(__DIR__ . DIRECTORY_SEPARATOR . 'widgets' . DIRECTORY_SEPARATOR . $wName . '.php');
            $className = '\\extra\\dashboard\\widgets\\' . $wName;
            $widgets[] = new $className(0, null);
        }


        foreach ($extensionWidgets as $extensionWidget) {
            if (!str_starts_with($extensionWidget, 'widget')) {
                continue;
            }
            $widgets[] = new $extensionWidget(0, null);
        }


        return $widgets;
    }

}
