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

class WidgetChart extends Widget
{

    public function __construct(int $id, ?array $options = [])
    {
        $this->name = 'Diagramm';
        $this->icon = 'lmb-line-chart';
        $this->hasOptions = true;
        $this->hasjs = true;
        parent::__construct($id, $options);
    }

    /**
     * @return string
     */
    protected function internalRender(): string
    {
        $uninitialized = '';
        $display = '';
        if (!$this->checkConfig()) {
            $display = 'class="d-none"';
            $uninitialized = '<div class="dash-uninitialized"><i class="lmb-icon lmb-line-chart"></i><br>Kein Diagramm ausgewählt.</div>';
        }


        return '<div class="bg-contrast h-100 w-100 p-2 border">' . $uninitialized . '<canvas id="dash-chart-' . $this->id . '" ' . $display . ' data-chartid="' . $this->options['chartid'] . '"></canvas></div>';
    }

    /**
     * checks if widget is correctly configured
     *
     * @return bool
     */
    private function checkConfig(): bool
    {

        if (!array_key_exists('chartid', $this->options)) {
            return false;
        }

        return true;
    }


    /**
     * @return string
     */
    public function loadOptionsEditor(): string
    {
        $charts = $this->getChartList();
        $options = $this->options;

        $output = '<div class="form-group row"><label for="wopt-chart" class="col-sm-2 col-form-label">Diagramm</label><div class="col-sm-10"><select class="form-select" id="wopt-chart" name="chartid"><option>-- select --</option>';
        foreach ($charts as $chart) {
            $output .= '<option value="' . $chart['id'] . '"' . (($options['chartid'] == $chart['id']) ? 'selected' : '') . '>' . $chart['name'] . '</option>';
        }
        $output .= '</select></div></div>';


        return $output;
    }


    /**
     * loads a list of al charts defined in Limbas
     *
     * @return array
     */
    private function getChartList(): array
    {
        global $db;
        $sqlquery = 'SELECT 
				ID, 
				TAB_ID, 
				DIAG_NAME,
				DIAG_TYPE
			FROM 
				LMB_CHART_LIST';
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, '', __FILE__, __LINE__);


        $charts = array();
        while (lmbdb_fetch_row($rs)) {
            $charts[] = [
                'id' => lmbdb_result($rs, "ID"),
                'name' => lmbdb_result($rs, "DIAG_NAME"),
                'type' => lmbdb_result($rs, "DIAG_TYPE")
            ];
        }
        return $charts;
    }
}
