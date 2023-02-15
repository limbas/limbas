<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
require_once(__DIR__ . '/../../report/report.dao');
require_once(__DIR__ . '/../base/TemplateConfig.php');
require_once(__DIR__ . '/ReportTemplateElement.php');
require_once(__DIR__ . '/ReportTemplateConfig.php');
require_once(__DIR__ . '/ReportDataPlaceholder.php');

class ReportTemplateResolver {
    
    
    /**
     * @param $gtabid
     * @param $report_id
     * @param $templGtabid
     * @param $template
     * @param array $settings
     * @return array
     */
    public static function getTemplateStructure($gtabid,$report_id,$templGtabid,$template,$settings=[]) {
        global $greportlist;

        $reportspec = $greportlist[$gtabid];
        if(!$reportspec){return [];}

        $ID = 0;
        

        if (empty($template)) {
            return self::resolveReportToElements($gtabid,$report_id,$settings,$ID);
        }


               
        TemplateConfig::$instance = new ReportTemplateConfig($_ = array('referenz_tab' => $gtabid), [],0);
        TemplateConfig::$instance->resolveDataPlaceholders = false;
        TemplateConfig::$instance->noFunctionExecute = true;


        $templatePartElement = self::getTemplateElementByName($templGtabid,$template,2);
        if (!$templatePartElement) {
            return [];
        }


        return self::resolveTemplateElement($templGtabid,$report_id,$gtabid,$templatePartElement);
    }


    /**
     * Resolves a complete report and all its root template elements
     * 
     * @param $gtabid
     * @param $report_id
     * @param array $resolvedTemplateGroups
     * @param int $ID
     * @param bool $includeChildren
     * @param bool $resolveElements
     * @param array $resolvedDynamicData
     * @param int $depth
     * @return array
     */
    public static function resolveReport($gtabid,$report_id,$resolvedTemplateGroups=[],$ID=0,$includeChildren=true,$resolveElements=true,$resolvedDynamicData=[],$depth=-1,$resolveDataPlaceholders=true,$noFunctionExecute=false) {
        
        $output = [];

        // for each template element in report
        self::forEveryTemplateInReport($report_id, $ID, function($templGtabid, $content, $parameter) use ($ID, $resolvedTemplateGroups, $gtabid, $report_id, $includeChildren,$resolveElements, &$output, $resolvedDynamicData, $depth, $resolveDataPlaceholders,$noFunctionExecute) {

            if (empty($resolvedTemplateGroups)) {
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


            $templateOutput = self::resolveTemplateGroup($baseElement,$templGtabid,$includeChildren);
            $templateOutput['elements'] = [];
            if ($resolveElements) {
                $templateOutput['elements'] = self::resolveTemplateElement($templGtabid, $report_id, $gtabid, $baseElement);
            }
            $templateOutput['resolved'] = $resolvedTemplateGroups;
            $templateOutput['gtabid'] = $templGtabid;

            $output[] = $templateOutput;

        });

        return $output;
    }


    /**
     * Resolves a complete report and returns it splitted into elements
     * 
     * @param $gtabid
     * @param $report_id
     * @param array $settings
     * @param int $ID
     * @return array
     */
    public static function resolveReportToElements($gtabid,$report_id,$settings=[],$ID=0,$is_template=false) {
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
        
        $resolvedReportTemplate = self::resolveReport($gtabid,$report_id,$settings,$ID,false,$resolveElements, [], $depth,false, true);
        
        $duplicatedGroup = [];
        foreach ($resolvedReportTemplate as $reportTemplate) {
            
            if ($isTemplate) {
                foreach ($reportTemplate['unresolved'] as $unresolvedGroup) {
                    $groupName = $unresolvedGroup->getGroupName();
                    if (in_array($groupName,$duplicatedGroup)) {
                        continue;
                    }
                    $duplicatedGroup[] = $groupName;
                    $output[] = self::resolveSingleTemplateGroup($unresolvedGroup,$unresolvedGroup->getTabId($reportTemplate['gtabid']),$report_id,$gtabid);
                }
                
            } else {
                $output = array_merge($output, $reportTemplate['elements']);
            }
            
            
        }

        return $output;
    }
    
    /**
     * @param $templGtabid
     * @param $report_id
     * @param $gtabid
     * @param TemplateElement $templatePartElement
     * @return array
     */
    private static function resolveTemplateElement($templGtabid, $report_id, $gtabid, $templatePartElement) {
        global $gtab;
        
        $output = [];

        $duplicatedGroup = [];

        foreach ($templatePartElement->getParts() as $templatePart) {

            if ($templatePart instanceof SubTemplateElementPlaceholder) {

                $templateElement = $templatePart->getTemplateElement();


                if ($templateElement) {
                    $output[] = [
                        'id' => $templateElement->getId(), //getTemplateElementID($templGtabid,$templateElement->getName()),
                        'name' => $templateElement->getName(),
                        'ident' => $templateElement->getName(),
                        'settings' => $templateElement->getName(),
                        'report_id' => $report_id,
                        'gtabid' => $gtabid,
                        'tgtabid' => $templGtabid,
                        'haschildren'=>self::hasChildren($templateElement,$templGtabid),
                        'children'=>[],
                        'edittype'=>0,
                        'edit'=>($gtab['edit'][$templGtabid]) ? true : false,
                        'add'=>($gtab['add'][$templGtabid]) ? true : false,
                        'copy'=>($gtab['copy'][$templGtabid]) ? true : false,
                        'delete'=>($gtab['delete'][$templGtabid]) ? true : false
                    ];
                }


                continue;
            }


            if ($templatePart instanceof TemplateGroupPlaceholder) {

                $groupName = $templatePart->getGroupName();
                if (in_array($groupName,$duplicatedGroup)) {
                    continue;
                }
                $duplicatedGroup[] = $groupName;
                
                $output[] = self::resolveSingleTemplateGroup($templatePart,$templatePart->getTabId($templGtabid),$report_id,$gtabid);

                continue;
            }

            if ($templatePart instanceof IfPlaceholder) {

                $output = self::resolveIfPlaceholder($templatePart,$templGtabid,$report_id,$gtabid,$output);

                continue;
            }

            if ($templatePart instanceof TableRow) {

                $output = self::resolveTable($templatePart,$templGtabid,$report_id,$gtabid, $output);

                continue;
            }
            
        }

        return $output;
    }


    /**
     * @param TemplateGroupPlaceholder $templateGroupPlaceholder
     * @param $templGtabid
     * @param $report_id
     * @param $gtabid
     * @return array
     */
    private static function resolveSingleTemplateGroup($templateGroupPlaceholder,$templGtabid,$report_id,$gtabid) {
        global $gtab;
        
        $templateGroup = self::resolveTemplateGroupToElements($templateGroupPlaceholder,$templateGroupPlaceholder->getTabId($templGtabid),$report_id,$gtabid);

        return [
            'id'=>$templateGroup['id'],
            'name' => $templateGroupPlaceholder->getGroupName(),
            'ident' => $templateGroupPlaceholder->getGroupName(),
            'settings' => '',
            'report_id' => $report_id,
            'gtabid' => $gtabid,
            'tgtabid' => $templGtabid,
            'haschildren'=>true,
            'children'=>$templateGroup['children'],
            'edittype'=>1,
            'edit'=>($gtab['edit'][$templGtabid]) ? true : false,
            'add'=>($gtab['add'][$templGtabid]) ? true : false,
            'copy'=>($gtab['copy'][$templGtabid]) ? true : false,
            'delete'=>($gtab['delete'][$templGtabid]) ? true : false
        ];
    }
    
    /**
     * @param TemplateGroupPlaceholder $baseElement
     * @param $templGtabid
     * @param $reportid
     * @param $gtabid
     * @return array
     */
    public static function resolveTemplateGroup($baseElement,$templGtabid,$includeChildren=true) {
        global $gtab, $gfield, $db;

        // unresolved template group elements exist?
        $unresolvedTemplateGroups = $baseElement->getUnresolvedTemplateGroupPlaceholders();

        // collect needed groups
        $neededGroups = [];
        $neededGroupsPerTab = [];
        $tabIds = [];
        foreach ($unresolvedTemplateGroups as $templateGroup) {
            $tabId = $templateGroup->getTabId($templGtabid);
            if (!array_key_exists($tabId,$neededGroupsPerTab)) {
                $neededGroupsPerTab[$tabId] = [];
            }
            $groupName = $templateGroup->getGroupName();
            if ($groupName === 'Content') {
                //$tabId = 58;
            }
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
            
            
            foreach($tabIds as $tabId) {

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
                $onlyfield[$templGtabid] = array(1, $groupFieldId, 'GROUPNAME','GROUPID');

                $gresult = get_gresult($templGtabid, 1, $filter, $gsr, null, $onlyfield, null, $extension);


                if ($gresult[$templGtabid]['res_count'] > 0) {
                    // group by template group
                    for ($i = 0; $i < $gresult[$templGtabid]['res_count']; $i++) {
                        $groupName = $gresult[$templGtabid]['GROUPNAME'][$i];
                        $ID = $gresult[$templGtabid]['GROUPID'][$i];
                        $templateName = $gresult[$templGtabid][1][$i];

                        $groupIds[$groupName][] = $ID;
                        $childrenByGroup[$groupName][] = [
                            'group_id'=>$ID,
                            'name'=>$templateName
                        ];
                    }
                } else {

                    //just get the id of the group
                    $poolId = $gfield[$templGtabid]['select_pool'][$groupFieldId];
                    $sql = 'SELECT ID, WERT FROM LMB_SELECT_W WHERE POOL = ' . $poolId . ' AND WERT IN (\''.implode('\',\'',$neededGroups).'\')';
                    $rs = lmbdb_exec($db,$sql);

                    while(lmbdb_fetch_row($rs)) {
                        $groupIds[lmbdb_result($rs,'WERT')] = lmbdb_result($rs,'ID');
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
     * @param $reportid
     * @param $gtabid
     * @return array
     */
    private static function resolveTemplateGroupToElements($baseElement,$templGtabid,$reportid,$gtabid) {
        global $gtab;
        

        $groupData = self::resolveTemplateGroup($baseElement,$templGtabid);
            
        $unresolvedTemplateGroups = $groupData['unresolved'];
        $childrenByGroup = $groupData['children'];

        $children = [];
        $ID = 0;
        // show selects
        foreach ($unresolvedTemplateGroups as $templateGroup) {

            $groupName = $templateGroup->getGroupName();
            
            $ID = $groupData['groupIds'][$groupName];

            foreach ($childrenByGroup[$groupName] as $groupChildren) {

                $templateElement = self::getTemplateElementByName($templGtabid,$groupChildren['name']);

                if ($templateElement) {
                    $children[] = [
                        'id' => $templateElement->getId(),
                        'name' => $groupChildren['name'],
                        'settings' => $groupChildren['name'],
                        'report_id' => $reportid,
                        'gtabid' => $gtabid,
                        'tgtabid' => $templGtabid,
                        'haschildren' => self::hasChildren($templateElement,$templGtabid),
                        'children' => [],
                        'isgroup' => false,
                        'edit'=>($gtab['edit'][$templGtabid]) ? true : false,
                        'add'=>($gtab['add'][$templGtabid]) ? true : false,
                        'copy'=>($gtab['copy'][$templGtabid]) ? true : false,
                        'delete'=>($gtab['delete'][$templGtabid]) ? true : false
                    ];
                }
            }

        }

        return ['id'=>$ID,'children'=>$children];
        
    }
    
    
    /**
     * @param IfPlaceholder $templatePart
     * @param $templGtabid
     * @param $report_id
     * @param $gtabid
     * @param $output
     * @param bool $elseif
     * @return array
     */
    private static function resolveIfPlaceholder($templatePart,$templGtabid,$report_id,$gtabid,$output,$elseif=false) {
        global $gtab;
        
        $condition = $templatePart->getCondition();
        $consequent = $templatePart->getConsequent($templGtabid);
        $alternative = $templatePart->getAlternative($templGtabid);

        if ($consequent) {
            $consequent->resolveTemplates(1);

            
            if (is_a($condition,'FunctionPlaceholder')) {
                $name = $condition->getFunctionName();
            } else {
                $name = $condition->getFullMatch();
            }
            
            //$output = array_merge($output,resolveTemplateElement($templGtabid,$report_id,$gtabid,$consequent));
            $output[] = [
                'id'=>0,
                'name' => ($elseif?'ELSE':'').'IF: '. $name,
                'ident' => ($elseif?'ELSE':'').'IF'.preg_replace('/[^A-Za-z0-9]/', '', $name),
                'settings' => '',
                'report_id' => 0,
                'gtabid' => 0,
                'tgtabid' => 0,
                'haschildren'=>true,
                'children'=>self::resolveTemplateElement($templGtabid,$report_id,$gtabid,$consequent),
                'edittype'=>2,
                'edit'=>($gtab['edit'][$templGtabid]) ? true : false,
                'add'=>($gtab['add'][$templGtabid]) ? true : false,
                'copy'=>($gtab['copy'][$templGtabid]) ? true : false,
                'delete'=>($gtab['delete'][$templGtabid]) ? true : false
            ];
        }

        if ($alternative) {
            if ($alternative instanceof IfPlaceholder) {
                $output = self::resolveIfPlaceholder($alternative,$templGtabid,$report_id,$gtabid,$output,true);
            } else {
                $alternative->resolveTemplates(1);

                //$output = array_merge($output,resolveTemplateElement($templGtabid,$report_id,$gtabid,$alternative));
                $output[] = [
                    'id'=>0,
                    'name' => 'ELSE',
                    'ident' => 'ELSE',
                    'settings' => '',
                    'report_id' => 0,
                    'gtabid' => 0,
                    'tgtabid' => 0,
                    'haschildren'=>true,
                    'children'=>self::resolveTemplateElement($templGtabid,$report_id,$gtabid,$alternative),
                    'edittype'=>2,
                    'edit'=>($gtab['edit'][$templGtabid]) ? true : false,
                    'add'=>($gtab['add'][$templGtabid]) ? true : false,
                    'copy'=>($gtab['copy'][$templGtabid]) ? true : false,
                    'delete'=>($gtab['delete'][$templGtabid]) ? true : false
                ];
            }
        }

        return $output;
    }

    /**
     * @param TableRow $templatePart
     * @param $templGtabid
     * @param $report_id
     * @param $gtabid
     * @param $output
     * @return array
     */
    private static function resolveTable($templatePart,$templGtabid,$report_id,$gtabid,$output) {
        
        $cells = $templatePart->getCells();
        $parts = [];
        
        //extract parts of all cells and ignore table structure
        foreach ($cells as $cell) {
            $parts = array_merge($parts,$cell->getParts());
        }
        
        $html = '';
        
        $templateElement = new TemplateElement(
            $templGtabid,'',$html
        );
        $templateElement->setParts($parts);
        
        $output = array_merge($output,self::resolveTemplateElement($templGtabid, $report_id, $gtabid, $templateElement));
        
        
        return $output;
    }


    /**
     * Checks if a TemplateElement has any children
     * @param $templateElement
     * @param $tgtabid
     * @return bool
     */
    private static function hasChildren($templateElement,$tgtabid) {
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
     * @return null
     */
    private static function getTemplateElementByName($templGtabid,$template,$depth=1) {
        $content = '<lmb type="template" name="'.$template.'"></lmb>';
        
        
        $templateElement = new ReportTemplateElement($templGtabid, $template, $content);
        $templateElement->resolveTemplates();//$depth);

        $subTemplateParts = $templateElement->getParts();
        
        if (empty($subTemplateParts)) {
            return null;
        }
        
        
        $templatePartElement = $subTemplateParts[0]->getTemplateElement();

        return $templatePartElement;
    }

    /**
     * Gets the content of a specific template record
     * 
     * @param $templGtabid
     * @param $id
     * @return mixed|null
     */
    public static function getTemplateElementContentById($templGtabid,$id) {
        global $db, $gtab;
        
        $sqlquery = "SELECT CONTENT FROM {$gtab['table'][$templGtabid]} WHERE ID = $id";
        $rs = lmbdb_exec($db,$sqlquery);
        return lmbdb_result($rs, "CONTENT");
    }


    /**
     * Iterates callback for every template element in the tcpdf report specified by $report_id
     * @param $report_id int report id
     * @param $ID int dataset id
     * @param $callback callable ($templGtabid, $content, $parameter): void
     */
    public static function forEveryTemplateInReport($report_id, $ID, $callback) {
        
        $report = get_report($report_id, 0);

        if ($report['defformat'] == 'mpdf') {
            $content = self::getTemplateElementContentById($report['root_template'],$report['root_template_id']);
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
