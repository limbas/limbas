<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\report;

use Limbas\extra\template\base\TemplateResolver;

require_once(__DIR__ . '/../../report/report.dao');

class ReportTemplateResolver extends TemplateResolver
{

    public function __construct(int $elementId, string $type = 'report')
    {
        parent::__construct($elementId, $type);
    }

    /**
     * Resolves a complete report and all its root template elements
     *
     * @param int $gtabid
     * @param $elementId
     * @param array $resolvedTemplateGroups
     * @param int $ID
     * @param bool $includeChildren
     * @param bool $resolveElements
     * @param array $resolvedDynamicData
     * @param int $depth
     * @param bool $resolveDataPlaceholders
     * @param bool $noFunctionExecute
     * @return array
     */
    public function resolve(int $gtabid, $elementId, $resolvedTemplateGroups = [], $ID = 0, $includeChildren = true, $resolveElements = true, $resolvedDynamicData = [], $depth = -1, $resolveDataPlaceholders = true, $noFunctionExecute = false): array
    {

        $output = [];

        // for each template element in report
        $this->forEveryTemplateInReport(intval($elementId), intval($ID), function ($templGtabid, $content, $parameter) use ($ID, $resolvedTemplateGroups, $gtabid, $elementId, $includeChildren, $resolveElements, &$output, $resolvedDynamicData, $depth, $resolveDataPlaceholders, $noFunctionExecute) {

            /*if (empty($resolvedTemplateGroups)) {
                $resolvedTemplateGroups = [];
            }
            if (empty($resolvedDynamicData)) {
                $resolvedDynamicData = [];
            }

            TemplateConfig::$instance = new ReportTemplateConfig($_ = array('referenz_tab' => $gtabid), $parameter,$ID);
            TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
            TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;
            TemplateConfig::$instance->resolveDataPlaceholders = $resolveDataPlaceholders;
            TemplateConfig::$instance->noFunctionExecute = $noFunctionExecute;


            $baseElement = new ReportTemplateElement($templGtabid, 'lmbBaseElement', $content);            
            if($ID != 0) {
                $baseElement->resolve(intval($ID));
            } else {
                //resolve only first level
                $baseElement->resolveTemplates($depth);
            }


            $templateOutput = $this->resolveTemplateGroup($baseElement,$templGtabid,$includeChildren);
            $templateOutput['elements'] = [];
            if ($resolveElements) {
                $templateOutput['elements'] = $this->resolveTemplateElement($templGtabid, $gtabid, $baseElement);
            }
            $templateOutput['resolved'] = $resolvedTemplateGroups;
            $templateOutput['gtabid'] = $templGtabid;

            $output[] = $templateOutput;*/

            $output[] = $this->resolveBaseElement($gtabid, $templGtabid, $content, $parameter, $resolvedTemplateGroups, $ID, $includeChildren, $resolveElements, $resolvedDynamicData, $depth, $resolveDataPlaceholders, $noFunctionExecute);

        });

        return $output;
    }


    /**
     * Resolves a complete report and returns it splitted into elements
     *
     * @param int $gtabid
     * @param int $report_id
     * @param array $settings
     * @param int $ID
     * @param bool $is_template
     * @return array
     */
    public function resolveReportToElements(int $gtabid, int $report_id, array $settings = [], int $ID = 0, bool $is_template = false): array
    {
        $output = [];

        $depth = 1;
        $resolveElements = true;
        $isTemplate = false;

        if (!empty($settings) || $is_template) {
            //if there are any settings, the "report" itself must be a saved template
            $depth = -1;
            $resolveElements = false;
            $isTemplate = true;
        }

        $resolvedReportTemplate = $this->resolve($gtabid, $report_id, $settings, $ID, false, $resolveElements, [], $depth, false, true);

        $duplicatedGroup = [];
        foreach ($resolvedReportTemplate as $reportTemplate) {

            if ($isTemplate) {
                foreach ($reportTemplate['unresolved'] as $unresolvedGroup) {
                    $groupName = $unresolvedGroup->getGroupName();
                    if (in_array($groupName, $duplicatedGroup)) {
                        continue;
                    }
                    $duplicatedGroup[] = $groupName;
                    $output[] = $this->resolveSingleTemplateGroup($unresolvedGroup, $unresolvedGroup->getTabId($reportTemplate['gtabid']), $gtabid);
                }

            } else {
                $output = array_merge($output, $reportTemplate['elements']);
            }


        }

        return $output;
    }


    /**
     * Iterates callback for every template element in the tcpdf report specified by $report_id
     * @param $report_id int report id
     * @param $ID int dataset id
     * @param $callback callable ($templGtabid, $content, $parameter): void
     */
    public function forEveryTemplateInReport(int $report_id, int $ID, callable $callback): void
    {

        $report = get_report($report_id, 0);

        if ($report['defformat'] == 'mpdf') {
            $content = $this->getTemplateElementContentById($report['root_template'], $report['root_template_id']);
            $callback($report['root_template'], $content, null);
            return;
        }


        $elements = element_list($ID, $report, 'all');
        foreach ($elements['typ'] as $key => $type) {
            if ($type != 'templ') {
                continue;
            }

            // parse html
            $templGtabid = $report['pic_typ'][$key];
            $content = &$report['value'][$key];
            $parameter = &$report['parameter'][$key];

            $callback($templGtabid, $content, $parameter);
        }
    }

}
