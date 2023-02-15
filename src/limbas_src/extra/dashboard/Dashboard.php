<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace extra\dashboard;

use Database;

abstract class Dashboard
{

    /*
     * TODO: Berechtigungen:
     * Dashboard erstellen ja nein
     * Dashboard wechseln je nach Leserechten auf das Dashboard
     * Dashboard editieren ja nein
     * -> impliziert Widgets hinzufügen/ändern ja nein
     */


    /**
     * @return string[]
     */
    public static function getDashboards(): array
    {
        $dashboards = [0 => 'Standard'];
        $rs = Database::select('LMB_DASHBOARDS');
        while (lmbdb_fetch_row($rs)) {
            //TODO: Leseberechtigung
            $dashboards[lmbdb_result($rs, 'ID')] = lmbdb_result($rs, 'NAME');
        }
        return $dashboards;
    }

    /**
     * @param int|null $id
     * @return array
     */
    public static function loadDashboard(int $id = null): array
    {
        if ($id === null) {
            $id = 0;
        }
        
        $dashboard = ['id' => $id ?: 0, 'widgets' => []];
        $rs = Database::select('LMB_DASHBOARD_WIDGETS', where: ['DASHBOARD' => $id]);
        while (lmbdb_fetch_row($rs)) {
            $wopts = lmbdb_result($rs, 'OPTIONS');
            $options = json_decode($wopts, true);
            if (empty($options)) {
                $options = [];
            }
            $options['x'] = lmbdb_result($rs, 'X');
            $options['y'] = lmbdb_result($rs, 'Y');
            $options['height'] = lmbdb_result($rs, 'HEIGHT');
            $options['width'] = lmbdb_result($rs, 'WIDTH');
            //$options['readonly'] = true; //TODO: Schreibrechte

            $widget = WidgetFactory::create(lmbdb_result($rs, 'ID'), lmbdb_result($rs, 'TYPE'), $options);
            $dashboard['widgets'][] = $widget;
        }
        return $dashboard;
    }

    /**
     * @param string $name
     * @return bool|int
     */
    public static function addDashboard(string $name): bool|int
    {
        $id = next_db_id('LMB_DASHBOARDS');

        Database::insert('LMB_DASHBOARDS', [
            'ID' => $id,
            'name' => $name
        ]);

        return $id;
    }

    /**
     * @param array $params
     * @return bool[]|false[]
     */
    public static function deleteDashboard(array $params): array
    {
        //TODO: prüfen ob Dashboard irgendwem als default zugewiesen ist

        if (empty($params['id'])) {
            return ['success' => false];
        }

        Database::delete('LMB_DASHBOARD_WIDGETS', ['DASHBOARD' => $params['id']], null);
        Database::delete('LMB_DASHBOARDS', ['ID' => $params['id']], null);

        unset($_SESSION['dashboardid']);

        return ['success' => true];
    }

    /**
     * @param array $params
     * @return array
     */
    public static function addWidget(array $params): array
    {
        /*TODO:
            check if widget type exists
            check if dashboard exists
            check edit rights
            lock / unlock dashboard
            json sql injection
        */


        $options = $params;
        unset($options['dashboard']);
        unset($options['type']);
        unset($options['x']);
        unset($options['y']);
        unset($options['height']);
        unset($options['width']);
        unset($options['action']);
        unset($options['actid']);


        $id = next_db_id('LMB_DASHBOARD_WIDGETS');
        $data = [
            'ID' => $id,
            'DASHBOARD' => $params['dashboard'] ?? 0,
            'TYPE' => $params['type'],
            'X' => $params['x'] ?? 0,
            'Y' => $params['y'] ?? 0,
            'HEIGHT' => $params['height'] ?? 1,
            'WIDTH' => $params['width'] ?? 1,
            'OPTIONS' => json_encode($options)
        ];

        $widget = WidgetFactory::create($id, $params['type'], $params);

        Database::insert('LMB_DASHBOARD_WIDGETS', $data);

        return ['id' => $id, 'html' => $widget->render()];
    }

    /**
     * @param array $params
     * @return bool[]
     */
    public static function updateWidget(array $params): array
    {

        foreach ($params['widgets'] as $widget) {
            Database::update('LMB_DASHBOARD_WIDGETS', ['X' => $widget['x'], 'Y' => $widget['y'], 'HEIGHT' => $widget['height'], 'WIDTH' => $widget['width']], ['ID' => $widget['id']]);
        }

        return ['success' => true];
    }

    /**
     * @param array $params
     * @return bool[]
     */
    public static function deleteWidget(array $params): array
    {
        $success = Database::delete('LMB_DASHBOARD_WIDGETS', ['ID' => $params['id']]);

        return ['success' => $success];
    }

    /**
     * @param array $params
     * @return array|false[]
     */
    public static function loadWidgetOptions(array $params): array
    {
        $type = '';
        $options = [];

        $id = $params['id'];

        $rs = Database::select('LMB_DASHBOARD_WIDGETS', where: ['ID' => $id]);
        while (lmbdb_fetch_row($rs)) {
            $type = lmbdb_result($rs, 'TYPE');
            $wopts = lmbdb_result($rs, 'OPTIONS');
            $options = json_decode($wopts, true);
        }


        if (empty($type)) {
            return ['success' => false];
        }

        $widget = WidgetFactory::create($id, $type, $options);

        return ['success' => true, 'html' => $widget->loadOptionsEditor()];
    }

    /**
     * @param array $params
     * @return bool[]
     */
    public static function saveWidgetOptions(array $params): array
    {
        Database::update('LMB_DASHBOARD_WIDGETS', ['OPTIONS' => json_encode($params['options'])], ['ID' => $params['id']]);

        return ['success' => true];
    }

}
