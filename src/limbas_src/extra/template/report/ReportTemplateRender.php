<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\report;

use Limbas\extra\template\base\HtmlParts\DynamicDataPlaceholder;
use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateRender;
use lmb_log;

class ReportTemplateRender extends TemplateRender
{


    /**
     * Renders the report preview in html
     * @param $reportTemplateElements
     * @param $report_defformat
     */
    public function renderDynamicDataHtmlPreview(&$reportTemplateElements, $report_defformat): void
    {
        // group by yAbs s.t. elements that are on same y can be displayed next to each other
        $listByY = array();
        foreach ($reportTemplateElements as &$element) {
            if (!$listByY[$element['yAbs']]) {
                $listByY[$element['yAbs']] = array(&$element);
            } else {
                $listByY[$element['yAbs']][] = &$element;
            }
        }

        $mpdf = ($report_defformat == 'mpdf');

        //if ($mpdf) {
        #echo '<div>';
        //}

        foreach ($listByY as &$elementList) {
            echo $mpdf ? '' : '<div>';
            $xSum = 0;
            foreach ($elementList as &$element) {
                // remove first element's x & width from following elements x
                $x = $element['xPercent'] - $xSum;
                echo $mpdf ? '' : "<div style=\"display:inline-block;vertical-align:top;margin-left: $x%;width: {$element['widthPercent']}%;\">";
                echo $element['html'];
                echo $mpdf ? '' : '</div>';
                $xSum += $element['xPercent'] + $element['widthPercent'];
            }
            echo $mpdf ? '' : '</div>';
        }

        //if ($mpdf) {
        #echo '<div>';
        //}
    }


    /**
     * Returns an input/select/... for a given DynamicDataPlaceholder
     * @param $placeholder DynamicDataPlaceholder
     * @return string
     */
    public function formatDynamicDataPlaceholder(DynamicDataPlaceholder $placeholder): string
    {
        global $lang;
        global $resolvedDynamicData;

        // get options
        $options = $placeholder->getOptions();
        if (!$options['type']) {
            $options['type'] = 'text';
        }

        $value = $resolvedDynamicData[$placeholder->getIdentifier()];
        $html = array();
        switch ($options['type']) {
            case 'text':
                $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"$value\" class=\"form-control\">";
                break;

            case 'number':
                $onchange = "checktyp(16,'{$placeholder->getIdentifier()}','{$placeholder->getDescription()}',null,null,this.value,null,null,null,null,null);";
                $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" onchange=\"$onchange\" value=\"$value\" class=\"form-control\">";
                break;

            case 'date':
                $html[] = "<input type=\"text\" name=\"{$placeholder->getIdentifier()}\" value=\"$value\" class=\"form-control\">";
                $html[] = "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" TITLE=\"$lang[700]\" style=\"cursor:pointer;vertical-align:top;\" Onclick=\"lmb_datepicker(event,this,this.previousElementSibling,'','" . dateStringToDatepicker(setDateFormat(1, 1)) . "')\"></i>";
                break;

            case 'select':
                if (!$options['select_id']) {
                    lmb_log::error('Dynamic data: "select_id" not set', $lang[56]);
                    break;
                }
                require_once(COREPATH . 'gtab/sql/add_select.dao');
                $values = pool_select_list_simple($options['select_id'], $options['is_attribute']);

                $html[] = "<select name=\"{$placeholder->getIdentifier()}\" class=\"form-select\">";
                $html[] = '<option value=""></option>';
                foreach ($values['wert'] as $val) {
                    $selected = ($val === $value) ? 'selected' : '';
                    $html[] = "<option value=\"$val\" $selected>$val</option>";
                }
                $html[] = '</select>';
                break;

            case 'extension':
                if (!$options['function']) {
                    lmb_log::error('Dynamic data: "function" for extension not set', $lang[56]);
                    break;
                }
                $funcName = 'report_' . $options['function'];
                if (!function_exists($funcName)) {
                    lmb_log::error('Dynamic data: function "' . $funcName . '" does not exist', $lang[56]);
                    break;
                }
                $html[] = $funcName($placeholder->getIdentifier(), $placeholder->getDescription(), $options, $value);
                break;
        }
        return implode('', $html);
    }


    protected function getTemplateConfig(mixed $element, int $gtabid, int $id): TemplateConfig
    {
        return new ReportTemplateConfig($element, [], $gtabid, $id);
    }
}
