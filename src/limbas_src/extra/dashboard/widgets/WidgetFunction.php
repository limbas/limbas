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

class WidgetFunction extends Widget
{
    
    private $functions;

    public function __construct(int $id, ?array $options = [])
    {
        $this->name = 'Erweiterung';
        $this->icon = 'lmb-plugin';
        $this->hasOptions = true;
        $this->functions = $this->loadDashboardFunctions();
        parent::__construct($id, $options);
    }

    /**
     * @return string
     */
    protected function internalRender(): string
    {


        $status = $this->checkConfig();


        if ($status !== true) {
            $output = '<div class="dash-uninitialized"><i class="lmb-icon lmb-plugin"></i><br>' . $status . '</div>';
        } else {
            $func = 'dashboard_' . $this->options['function_name'];
            $output = $func();
        }


        return '<div class="bg-white h-100 w-100 p-2 border">' . $output . '</div>';
    }


    /**
     * checks if widget is correctly configured
     *
     * @return bool|string
     */
    private function checkConfig(): bool|string
    {

        if (!array_key_exists('function_name', $this->options)) {
            return 'Funktion nicht angegeben.';
        }

        if (!in_array($this->options['function_name'],$this->functions)) {
            return 'Funktion nicht gefunden.';
        }

        if (!function_exists('dashboard_' . $this->options['function_name'])) {
            return 'Funktion nicht gefunden.';
        }

        return true;
    }


    /**
     * get all defined functions starting with dashboard_
     * 
     * @return array
     */
    private function loadDashboardFunctions(): array
    {

        $extensionWidgets = [];
        if($GLOBALS['gLmbExt']['ext_dashboard.inc']){
            foreach ($GLOBALS['gLmbExt']['ext_dashboard.inc'] as $key => $extfile){
                require_once($extfile);
            }
        }

        $functions = get_defined_functions();
        $functions = preg_grep('/^dashboard_/', $functions['user']);
        
        if ($functions === false) {
            $functions = [];
        }
        
        return array_map(function($value) {
            return substr($value,10);
        }, $functions);
    }


    /**
     * @return string
     */
    public function loadOptionsEditor(): string
    {

        $output = '<div class="row"><label for="wopt-function" class="col-sm-2 col-form-label">Funktion</label><div class="col-sm-10"><select class="form-select" id="wopt-function" name="function_name"><option>-- select --</option>';
        foreach ($this->functions as $function) {
            $output .= '<option value="' . $function . '"' . (($this->options['function_name'] == $function) ? 'selected' : '') . '>' . $function . '</option>';
        }
        $output .= '</select></div></div>';

        return $output;
    }
}
