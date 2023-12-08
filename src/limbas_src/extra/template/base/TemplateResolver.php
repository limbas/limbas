<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base;

require_once(__DIR__ . '/../../report/report.dao');

use Limbas\extra\template\base\HtmlParts\IfPlaceholder;
use Limbas\extra\template\base\HtmlParts\SubTemplateElementPlaceholder;
use Limbas\extra\template\base\HtmlParts\TableRow;
use Limbas\extra\template\base\HtmlParts\TemplateGroupPlaceholder;
use Limbas\extra\template\report\ReportTemplateConfig;
use Limbas\extra\template\report\ReportTemplateElement;

class TemplateResolver
{


    public function __construct(
        protected int    $elementId,
        protected string $type
    )
    {

    }


    /**
     * @param int $gtabid
     * @param int $report_id
     * @param int $templGtabid
     * @param string $template
     * @param array $settings
     * @return array
     */
    public function getTemplateStructure(int $gtabid, int $report_id, int $templGtabid, string $template = '', array $settings = []): array
    {
        global $greportlist;

        $reportspec = $greportlist[$gtabid];
        if (!$reportspec) {
            return [];
        }

        $ID = 0;


        if (empty($template)) {
            return $this->resolveReportToElements($gtabid, $report_id, $settings, $ID);
        }


        TemplateConfig::$instance = new ReportTemplateConfig([], [], $gtabid, 0);
        TemplateConfig::$instance->resolveDataPlaceholders = false;
        TemplateConfig::$instance->noFunctionExecute = true;


        $templatePartElement = $this->getTemplateElementByName($templGtabid, $template, 2);
        if (!$templatePartElement) {
            return [];
        }


        return $this->resolveTemplateElement($templGtabid, $gtabid, $templatePartElement);
    }

    /**
     * @param $templGtabid
     * @param $gtabid
     * @param TemplateElement $templatePartElement
     * @return array
     */
    protected function resolveTemplateElement($templGtabid, $gtabid, TemplateElement $templatePartElement): array
    {
        global $gtab;

        $output = [];

        $duplicatedGroup = [];

        foreach ($templatePartElement->getParts() as $templatePart) {

            if ($templatePart instanceof SubTemplateElementPlaceholder) {

                $templateElement = $templatePart->getTemplateElement();


                if ($templateElement) {
                    $output[] = [
                        'id' => $templateElement->getId(),
                        'name' => $templateElement->getName(),
                        'ident' => $templateElement->getName(),
                        'settings' => $templateElement->getName(),
                        $this->type . '_id' => $this->elementId,
                        'gtabid' => $gtabid,
                        'tgtabid' => $templGtabid,
                        'haschildren' => $this->hasChildren($templateElement, $templGtabid),
                        'children' => [],
                        'edittype' => 0,
                        'edit' => (bool)$gtab['edit'][$templGtabid],
                        'add' => (bool)$gtab['add'][$templGtabid],
                        'copy' => (bool)$gtab['copy'][$templGtabid],
                        'delete' => (bool)$gtab['delete'][$templGtabid]
                    ];
                }


                continue;
            }


            if ($templatePart instanceof TemplateGroupPlaceholder) {

                $groupName = $templatePart->getGroupName();
                if (in_array($groupName, $duplicatedGroup)) {
                    continue;
                }
                $duplicatedGroup[] = $groupName;

                $output[] = $this->resolveSingleTemplateGroup($templatePart, $templatePart->getTabId($templGtabid), $gtabid);

                continue;
            }

            if ($templatePart instanceof IfPlaceholder) {

                $output = $this->resolveIfPlaceholder($templatePart, $templGtabid, $gtabid, $output);

                continue;
            }

            if ($templatePart instanceof TableRow) {

                $output = $this->resolveTable($templatePart, $templGtabid, $gtabid, $output);
            }

        }

        return $output;
    }


    /**
     * @param TemplateGroupPlaceholder $templateGroupPlaceholder
     * @param $templGtabid
     * @param $gtabid
     * @return array
     */
    protected function resolveSingleTemplateGroup(TemplateGroupPlaceholder $templateGroupPlaceholder, $templGtabid, $gtabid): array
    {
        global $gtab;

        $templateGroup = $this->resolveTemplateGroupToElements($templateGroupPlaceholder, $templateGroupPlaceholder->getTabId($templGtabid), $gtabid);

        return [
            'id' => $templateGroup['id'],
            'name' => $templateGroupPlaceholder->getGroupName(),
            'ident' => $templateGroupPlaceholder->getGroupName(),
            'settings' => '',
            $this->type . '_id' => $this->elementId,
            'gtabid' => $gtabid,
            'tgtabid' => $templGtabid,
            'haschildren' => true,
            'children' => $templateGroup['children'],
            'edittype' => 1,
            'edit' => (bool)$gtab['edit'][$templGtabid],
            'add' => (bool)$gtab['add'][$templGtabid],
            'copy' => (bool)$gtab['copy'][$templGtabid],
            'delete' => (bool)$gtab['delete'][$templGtabid]
        ];
    }

    /**
     * @param TemplateGroupPlaceholder|TemplateElement $baseElement
     * @param $templGtabid
     * @param bool $includeChildren
     * @return array
     */
    public function resolveTemplateGroup(TemplateGroupPlaceholder|TemplateElement $baseElement, $templGtabid, bool $includeChildren = true): array
    {
        global $gtab, $gfield, $db;

        // unresolved template group elements exist?
        $unresolvedTemplateGroups = $baseElement->getUnresolvedTemplateGroupPlaceholders();

        // collect needed groups
        $neededGroups = [];
        $neededGroupsPerTab = [];
        $tabIds = [];
        foreach ($unresolvedTemplateGroups as $templateGroup) {
            $tabId = $templateGroup->getTabId($templGtabid);
            if (!array_key_exists($tabId, $neededGroupsPerTab)) {
                $neededGroupsPerTab[$tabId] = [];
            }
            $groupName = $templateGroup->getGroupName();
            $neededGroups[] = $groupName;
            $neededGroupsPerTab[$tabId][] = $groupName;
            $tabIds[] = $tabId;
        }


        $neededGroups = array_unique($neededGroups);
        $tabIds = array_unique($tabIds);


        $childrenByGroup = array();
        $groupIds = [];
        if ($includeChildren) {
            // get datasets of groups


            foreach ($tabIds as $tabId) {

                $templGtabid = $tabId;

                $groupFieldId = $gfield[$templGtabid]['argresult_name']['GROUPS'];

                $neededGroupsPerTab[$tabId] = array_unique($neededGroupsPerTab[$tabId]);

                $gsr = array();
                foreach ($neededGroupsPerTab[$tabId] as $i => $group) {
                    $gsr[$templGtabid][$groupFieldId][$i] = $group;
                    $gsr[$templGtabid][$groupFieldId]['txt'][$i] = 2; // eq
                    $gsr[$templGtabid][$groupFieldId]['andor'][$i] = 2; // or
                }

                $filter = array();
                $filter['nolimit'][$templGtabid] = 1;
                $filter['anzahl'][$templGtabid] = 'all';

                $extension = array();
                $extension['select'][0] = 'LMB_SELECT_W.WERT AS GROUPNAME, LMB_SELECT_W.ID AS GROUPID';
                $extension['from'][0] = 'LMB_SELECT_D';
                $extension['from'][1] = 'LMB_SELECT_W';
                $extension['where'][0] = $gtab['table'][$templGtabid] . '.ID = LMB_SELECT_D.DAT_ID';
                $extension['where'][1] = 'LMB_SELECT_D.W_ID = LMB_SELECT_W.ID';
                $extension['where'][2] = 'LMB_SELECT_D.TAB_ID = ' . $templGtabid;

                $onlyfield = array();
                $onlyfield[$templGtabid] = array(1, $groupFieldId, 'GROUPNAME', 'GROUPID');

                $gresult = get_gresult($templGtabid, 1, $filter, $gsr, null, $onlyfield, null, $extension);


                if ($gresult[$templGtabid]['res_count'] > 0) {
                    // group by template group
                    for ($i = 0; $i < $gresult[$templGtabid]['res_count']; $i++) {
                        $groupName = $gresult[$templGtabid]['GROUPNAME'][$i];
                        $ID = $gresult[$templGtabid]['GROUPID'][$i];
                        $templateName = $gresult[$templGtabid][1][$i];

                        $groupIds[$groupName][] = $ID;
                        $childrenByGroup[$groupName][] = [
                            'group_id' => $ID,
                            'name' => $templateName
                        ];
                    }
                } else {

                    //just get the id of the group
                    $poolId = $gfield[$templGtabid]['select_pool'][$groupFieldId];
                    $sql = 'SELECT ID, WERT FROM LMB_SELECT_W WHERE POOL = ' . $poolId . ' AND WERT IN (\'' . implode('\',\'', $neededGroups) . '\')';
                    $rs = lmbdb_exec($db, $sql);

                    while (lmbdb_fetch_row($rs)) {
                        $groupIds[lmbdb_result($rs, 'WERT')] = lmbdb_result($rs, 'ID');
                    }

                }
            }


        }


        $output['unresolved'] = $unresolvedTemplateGroups;
        $output['needed'] = $neededGroups;
        $output['children'] = $childrenByGroup;
        $output['groupIds'] = $groupIds;
        $output['unresolvedData'] = $baseElement->getUnresolvedDynamicDataPlaceholders();


        return $output;

    }


    /**
     * @param $baseElement
     * @param $templGtabid
     * @param $gtabid
     * @return array
     */
    protected function resolveTemplateGroupToElements($baseElement, $templGtabid, $gtabid): array
    {
        global $gtab;


        $groupData = $this->resolveTemplateGroup($baseElement, $templGtabid);

        $unresolvedTemplateGroups = $groupData['unresolved'];
        $childrenByGroup = $groupData['children'];

        $children = [];
        $ID = 0;
        // show selects
        foreach ($unresolvedTemplateGroups as $templateGroup) {

            $groupName = $templateGroup->getGroupName();

            $ID = $groupData['groupIds'][$groupName];

            foreach ($childrenByGroup[$groupName] as $groupChildren) {

                $templateElement = $this->getTemplateElementByName($templGtabid, $groupChildren['name']);

                if ($templateElement) {
                    $children[] = [
                        'id' => $templateElement->getId(),
                        'name' => $groupChildren['name'],
                        'settings' => $groupChildren['name'],
                        $this->type . '_id' => $this->elementId,
                        'gtabid' => $gtabid,
                        'tgtabid' => $templGtabid,
                        'haschildren' => $this->hasChildren($templateElement, $templGtabid),
                        'children' => [],
                        'isgroup' => false,
                        'edit' => (bool)$gtab['edit'][$templGtabid],
                        'add' => (bool)$gtab['add'][$templGtabid],
                        'copy' => (bool)$gtab['copy'][$templGtabid],
                        'delete' => (bool)$gtab['delete'][$templGtabid]
                    ];
                }
            }

        }

        return ['id' => $ID, 'children' => $children];

    }


    /**
     * @param IfPlaceholder $templatePart
     * @param int $templGtabid
     * @param int $gtabid
     * @param array $output
     * @param bool $elseif
     * @return array
     */
    protected function resolveIfPlaceholder(IfPlaceholder $templatePart, int $templGtabid, int $gtabid, array $output, bool $elseif = false): array
    {
        global $gtab;

        $condition = $templatePart->getCondition();
        $consequent = $templatePart->getConsequent($templGtabid);
        $alternative = $templatePart->getAlternative($templGtabid);

        if ($consequent) {
            $consequent->resolveTemplates(1);


            if (is_a($condition, 'FunctionPlaceholder')) {
                $name = $condition->getFunctionName();
            } else {
                $name = $condition->getFullMatch();
            }

            //$output = array_merge($output,resolveTemplateElement($templGtabid,$gtabid,$consequent));
            $output[] = [
                'id' => 0,
                'name' => ($elseif ? 'ELSE' : '') . 'IF: ' . $name,
                'ident' => ($elseif ? 'ELSE' : '') . 'IF' . preg_replace('/[^A-Za-z0-9]/', '', $name),
                'settings' => '',
                $this->type . '_id' => 0,
                'gtabid' => 0,
                'tgtabid' => 0,
                'haschildren' => true,
                'children' => $this->resolveTemplateElement($templGtabid, $gtabid, $consequent),
                'edittype' => 2,
                'edit' => (bool)$gtab['edit'][$templGtabid],
                'add' => (bool)$gtab['add'][$templGtabid],
                'copy' => (bool)$gtab['copy'][$templGtabid],
                'delete' => (bool)$gtab['delete'][$templGtabid]
            ];
        }

        if ($alternative) {
            if ($alternative instanceof IfPlaceholder) {
                $output = $this->resolveIfPlaceholder($alternative, $templGtabid, $gtabid, $output, true);
            } else {
                $alternative->resolveTemplates(1);

                //$output = array_merge($output,resolveTemplateElement($templGtabid,$gtabid,$alternative));
                $output[] = [
                    'id' => 0,
                    'name' => 'ELSE',
                    'ident' => 'ELSE',
                    'settings' => '',
                    $this->type . '_id' => 0,
                    'gtabid' => 0,
                    'tgtabid' => 0,
                    'haschildren' => true,
                    'children' => $this->resolveTemplateElement($templGtabid, $gtabid, $alternative),
                    'edittype' => 2,
                    'edit' => (bool)$gtab['edit'][$templGtabid],
                    'add' => (bool)$gtab['add'][$templGtabid],
                    'copy' => (bool)$gtab['copy'][$templGtabid],
                    'delete' => (bool)$gtab['delete'][$templGtabid]
                ];
            }
        }

        return $output;
    }

    /**
     * @param TableRow $templatePart
     * @param $templGtabid
     * @param $gtabid
     * @param $output
     * @return array
     */
    protected function resolveTable($templatePart, $templGtabid, $gtabid, $output): array
    {

        $cells = $templatePart->getCells();
        $parts = [];

        //extract parts of all cells and ignore table structure
        foreach ($cells as $cell) {
            $parts = array_merge($parts, $cell->getParts());
        }

        $html = '';

        $templateElement = new TemplateElement(
            $templGtabid, '', $html
        );
        $templateElement->setParts($parts);

        return array_merge($output, $this->resolveTemplateElement($templGtabid, $gtabid, $templateElement));
    }


    /**
     * Checks if a TemplateElement has any children
     * @param TemplateElement $templateElement
     * @param int $tgtabid
     * @return bool
     */
    protected function hasChildren(TemplateElement $templateElement, int $tgtabid): bool
    {
        if (!empty($templateElement->getUnresolvedSubTemplateElementPlaceholders()) || !empty($templateElement->getUnresolvedTemplateGroupPlaceholders())) {
            return true;
        }


        foreach ($templateElement->getParts() as $part) {

            if ($part instanceof IfPlaceholder) {

                $consequent = $part->getConsequent($tgtabid);
                $alternative = $part->getAlternative($tgtabid);

                if ($consequent && (!empty($consequent->getUnresolvedSubTemplateElementPlaceholders()) || !empty($consequent->getUnresolvedTemplateGroupPlaceholders()))) {
                    return true;
                }

                if ($alternative && (!empty($alternative->getUnresolvedSubTemplateElementPlaceholders()) || !empty($alternative->getUnresolvedTemplateGroupPlaceholders()))) {
                    return true;
                }
            }

        }


        return false;
    }


    /**
     * Gets a TemplateElement Object by its template name
     * @param $templGtabid
     * @param $template
     * @param int $depth
     * @return TemplateElement|null
     */
    protected function getTemplateElementByName($templGtabid, $template, $depth = 1): ?TemplateElement
    {
        $content = '<lmb type="template" name="' . $template . '"></lmb>';


        $templateElement = new TemplateElement($templGtabid, $template, $content);
        $templateElement->resolveTemplates();//$depth);

        $subTemplateParts = $templateElement->getParts();

        if (empty($subTemplateParts)) {
            return null;
        }


        return $subTemplateParts[0]->getTemplateElement();
    }

    /**
     * Gets the content of a specific template record
     *
     * @param $templGtabid
     * @param $id
     * @return string
     */
    public function getTemplateElementContentById($templGtabid, $id): string
    {
        TemplateConfig::$instance = new ReportTemplateConfig([], '', 0, 0);
        TemplateConfig::$instance->resolveDataPlaceholders = false;
        TemplateConfig::$instance->noFunctionExecute = true;

        $templateElement = TemplateElement::fromID($templGtabid, $id);
        return $templateElement->getHtml();
    }

    /**
     * Returns information about template elements of the given report
     * @param $gtabid
     * @param $report_id
     * @param $ID
     * @param $use_record
     * @param $resolvedTemplateGroups
     * @param $resolvedDynamicData
     * @return array of array
     */
    public function getDynamicDataPlaceholders($gtabid, $report_id, $ID, $use_record, $resolvedTemplateGroups, $resolvedDynamicData): array
    {

        if ($GLOBALS['greportlist'][$gtabid]["defformat"][$report_id] != 'tcpdf' && $GLOBALS['greportlist'][$gtabid]["defformat"][$report_id] != 'mpdf') { #REPTYPE#
            return array(array(), false, false);
        }

        require_once(COREPATH . 'gtab/gtab.lib');

        // return values
        $reportTemplateElements = [];
        $dynamicDataPlaceholdersExist = false;
        $unresolvedDynamicDataPlaceholdersExist = false;

        // get report
        $report = get_report($report_id, 0);
        $pageWidth = intval($report['page_style'][0]);

        if ($pageWidth <= 0) {
            $pageWidth = 1;
        }

        // for each template element in report
        if ($report['defformat'] == 'mpdf') { #REPTYPE#
            $content = $this->getTemplateElementContentById($report['root_template'], $report['root_template_id']);

            $elements = [
                'typ' => ['templ']
            ];

            $report['pic_typ'] = [];
            $report['pic_typ'][0] = $report['root_template'];
            $report['value'] = [];
            $report['value'][0] = $content;
            $report['parameter'] = [];
            $report['parameter'][0] = null;
            $report['posx'] = [];
            $report['posx'][0] = 0;
            $report['posy'] = [];
            $report['posy'][0] = 0;
            $report['width'] = [];
            $report['width'][0] = $pageWidth;

        } else {
            $elements = element_list(null, $report, 'all');
        }


        foreach ($elements['typ'] as $key => $type) {
            // ignore other elements
            if ($type != 'templ') {
                continue;
            }

            // get limbas report element params
            $templGtabid = $report['pic_typ'][$key];
            $content = &$report['value'][$key];
            $parameter = &$report['parameter'][$key];
            $posX = intval($report['posx'][$key]);
            $posY = intval($report['posy'][$key]);
            $width = intval($report['width'][$key]);

            // create config
            $datid = $ID ?: lmb_split('_', $use_record, 2)[0];
            $reportParams = array('listmode' => $report['listmode']);
            TemplateConfig::$instance = new FillableTemplateConfig($reportParams, $parameter, $gtabid, $datid);
            TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
            TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;

            // create initial element and build tree
            $baseElement = new ReportTemplateElement($templGtabid, 'lmbBaseElement', $content);
            $baseElement->resolve(intval($datid));

            $dynamicDataPlaceholders = $baseElement->getAllDynamicDataPlaceholders();
            if ($dynamicDataPlaceholders) {
                $dynamicDataPlaceholdersExist = true;
            }
            $unresolvedDynamicDataPlaceholders = $baseElement->getUnresolvedDynamicDataPlaceholders();
            if ($unresolvedDynamicDataPlaceholders) {
                $unresolvedDynamicDataPlaceholdersExist = true;
            }
            $reportTemplateElements[] = array(
                'unresolvedDynamicDataPlaceholders' => $unresolvedDynamicDataPlaceholders,
                'allDynamicDataPlaceholders' => $dynamicDataPlaceholders,
                'html' => implode('', $baseElement->getAsHtmlArr()),
                'xPercent' => round(100 * $posX / $pageWidth),
                'yAbs' => $posY,
                'widthPercent' => round(100 * $width / $pageWidth)
            );
        }

        // sort by y pos increasing
        usort($reportTemplateElements, function ($a, $b) {
            $res = $a['yAbs'] - $b['yAbs'];
            if ($res != 0) {
                return $res;
            }
            return $a['xPercent'] - $b['xPercent'];
        });
        return array($reportTemplateElements, $dynamicDataPlaceholdersExist, $unresolvedDynamicDataPlaceholdersExist);
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
        return [];
    }


    protected function resolveBaseElement(int $gtabid, $templGtabid, $content, $parameter, $resolvedTemplateGroups = [], $ID = 0, $includeChildren = true, $resolveElements = true, $resolvedDynamicData = [], $depth = -1, $resolveDataPlaceholders = true, $noFunctionExecute = false): array
    {
        if (empty($resolvedTemplateGroups)) {
            $resolvedTemplateGroups = [];
        }
        if (empty($resolvedDynamicData)) {
            $resolvedDynamicData = [];
        }

        TemplateConfig::$instance = new ReportTemplateConfig([], $parameter, $gtabid, $ID);
        TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
        TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;
        TemplateConfig::$instance->resolveDataPlaceholders = $resolveDataPlaceholders;
        TemplateConfig::$instance->noFunctionExecute = $noFunctionExecute;


        $baseElement = new ReportTemplateElement($templGtabid, 'lmbBaseElement', $content);
        if ($ID != 0) {
            $baseElement->resolve(intval($ID));
        } else {
            //resolve only first level
            $baseElement->resolveTemplates($depth);
        }


        $templateOutput = $this->resolveTemplateGroup($baseElement, $templGtabid, $includeChildren);
        $templateOutput['elements'] = [];
        if ($resolveElements) {
            $templateOutput['elements'] = $this->resolveTemplateElement($templGtabid, $gtabid, $baseElement);
        }
        $templateOutput['resolved'] = $resolvedTemplateGroups;
        $templateOutput['gtabid'] = $templGtabid;

        return $templateOutput;
    }
}
