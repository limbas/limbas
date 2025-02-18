<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\select;

use Limbas\extra\template\base\TemplateResolver;
use Limbas\lib\db\Database;

require_once COREPATH . 'gtab/gtab.lib';

abstract class TemplateSelector
{
    protected static bool $modalRendered;

    protected string $type;

    public static function getTemplateSelectorByType(string $type): ?TemplateSelector
    {
        return match ($type) {
            'report' => new ReportTemplateSelector($type),
            'mail' => new MailTemplateSelector($type),
            default => null,
        };
    }

    public static function printTemplateSelectModal(int $gtabid): void
    {
        global $umgvar;
        global $lang;

        if (!isset($modalRendered)) {
            self::$modalRendered = true;
        } elseif (self::$modalRendered === true) {
            return;
        }

        require_once COREPATH . 'extra/template/select/html/modal.php';
    }
    
    public static function resetTemplateCache(int $tabId = null): bool
    {
        
        $where = [];
        if(!empty($tabId)) {
            $where = [
                'TAB_ID' => $tabId
            ];
        }
        
        return Database::update('LMB_CONF_TABLES',[
            'REPORT_RESOLVE_CACHE' => null,
        ],$where);
    }


    protected abstract function getElementList(int $gtabid, string $search = '', int $page = 1, int $perPage = 10): array;

    protected abstract function getFinalResolvedParameters(int $elementId, int $gtabid, ?int $id, array $ids, $use_record, $resolvedTemplateGroups): array;

    protected abstract function getTemplateResolver(int $elementId): TemplateResolver;


    public function getElementListRendered(int $gtabid, string $search = '', int $page = 1, int $perPage = 10, int $id = 0): array
    {
        global $lang;

        if (empty($page)) {
            $page = 1;
        }

        if (empty($perPage)) {
            $perPage = 10;
        }


        $elementList = $this->getElementList($gtabid, $search, $page, $perPage);

        $tableContent = '';
        $pagination = '';


        foreach ($elementList['elements'] as $element) {
            ob_start();
            include(COREPATH . 'extra/template/select/html/row.php');
            $tableContent .= ob_get_clean() ?: '';
        }

        if (empty($elementList['elements'])) {
            $tableContent = '<tr><td colspan="2">' . $lang[3085] . '</td></tr>';
        }

        $maxPage = $elementList['max_page'];
        ob_start();
        include(COREPATH . 'extra/template/select/html/pagination.php');
        $pagination .= ob_get_clean();

        return [
            'skipResolve' => false,
            'table' => $tableContent,
            'pagination' => $pagination
        ];
    }

    protected function getFilteredElementList(array $elementList, string $search): array
    {

        if (!empty($search)) {
            function filterArray($needle, $haystack, $column): bool
            {
                return str_contains(strtolower($haystack[$column]), $needle);
            }

            $search = strtolower($search);
            $elementList = array_filter($elementList, function ($v) use ($search) {
                return filterArray($search, $v, 'name');
            });
        }


        $name = array_column($elementList, 'name');

        array_multisort($name, SORT_ASC, $elementList);

        return $elementList;
    }

    protected function paginateElementList($elementList, $page, $perPage): array
    {
        $itemCount = lmb_count($elementList);

        $offset = ($page - 1) * $perPage;

        if ($offset > $itemCount) {
            $offset = 0;
        }

        $maxPage = ceil($itemCount / $perPage);

        return [
            'item_count' => $itemCount,
            'max_page' => $maxPage,
            'elements' => array_slice($elementList, $offset, $perPage)
        ];
    }


    public function resolveSelect($params): array
    {

        $elementId = intval($params['elementId']);
        $gtabid = intval($params['gtabid']);
        $use_record = null;
        $ids = [];

        $id = $params['id'] ?? 0;
        if(is_array($id) && !empty($id)) {
            $use_record = array_filter(array_map('intval', $id));
            $use_record = implode(';', array_map(fn($value): string => $value . '_' . $gtabid, $use_record));

            $ids = $id;
            $id = $id[0];
        }
        $id = intval($id);


        $resolvedTemplateGroups = $params['resolvedTemplateGroups'];
        $saveAsTemplate = $params['saveAsTemplate'];


        //check if anything needs to be resolved
        $hasTemplateOptions = empty($elementId) ? false : $this->printReportTemplateOptions($gtabid, $elementId, $id, $use_record, $resolvedTemplateGroups, $saveAsTemplate);

        if ($hasTemplateOptions !== false) {
            return [
                'resolved' => false,
                'html' => $hasTemplateOptions,
                'type' => $this->type
            ];
        }

        if($use_record !== null) {
            $id = null;
        }
        

        ///everything is resolved => load final parameters
        return [
            'resolved' => true,
            'html' => '',
            'type' => $this->type,
            'params' => $this->getFinalResolvedParameters($elementId, $gtabid, $id, $ids, $use_record, $resolvedTemplateGroups)
        ];
    }


    protected function printReportTemplateOptions(int $gtabid, int $elementId, $ID, $use_record, $resolvedTemplateGroups, $saveAsTemplate = ''): bool|string
    {

        global $lang;
        global $LINK;

        $datid = $ID ?: lmb_split('_', $use_record, 2)[0];
        $resolvedTemplateGroups = json_decode(urldecode($resolvedTemplateGroups), true);
        if (is_array($resolvedTemplateGroups)) {
            $resolvedTemplateGroups = array_filter($resolvedTemplateGroups);
        } else {
            $resolvedTemplateGroups = [];
        }


        $templateResolver = $this->getTemplateResolver($elementId);
        $resolvedReportTemplate = $templateResolver->resolve($gtabid, $elementId, $resolvedTemplateGroups, $datid, true, false);


        /*if (!empty($saveAsTemplate)) {
            lmbSaveReportTemplate($gtabid, $elementId, $saveAsTemplate, $resolvedTemplateGroups);
            return '';
        }*/

        $unresolvedTemplateGroupsExist = false;

        // for each template element in report
        foreach ($resolvedReportTemplate as $reportTemplate) {
            $unresolvedTemplateGroups = $reportTemplate['unresolved'];
            if ($unresolvedTemplateGroups) {
                $unresolvedTemplateGroupsExist = true;
                break;
            }
        }

        if ($unresolvedTemplateGroupsExist) {


            $html = '';

            // for each template element in report
            foreach ($resolvedReportTemplate as $reportTemplate) {

                $unresolvedTemplateGroups = $reportTemplate['unresolved'];
                if ($unresolvedTemplateGroups) {
                    foreach ($resolvedTemplateGroups as $identifier => $templateName) {
                        if (!empty($templateName)) {
                            $html .= "<input type=\"hidden\" name=\"$identifier\" value=\"$templateName\">";
                        }
                    }
                }


                $childrenByGroup = $reportTemplate['children'];

                // show selects
                foreach ($unresolvedTemplateGroups as $templateGroup) {
                    // select options
                    $opt = array();
                    $opt['val'][] = '';
                    $opt['desc'][] = '';
                    foreach ($childrenByGroup[$templateGroup->getGroupName()] as $groupChildren) {
                        $opt['val'][] = $groupChildren['name'];
                        $opt['desc'][] = $groupChildren['name'];
                    }

                    // description
                    $desc = $templateGroup->getDescription();
                    if (!$desc) {
                        $desc = $templateGroup->getGroupName();
                    }
                    $html .= $this->pop_select(null, $opt, $templateGroup->getIdentifier(), $desc);
                }


            }

            //$html .= $this->pop_submenu2($lang[33], "reportModalSelectItem(event,null,'{$gtabid}','{$elementId}','{$ID}',keyValueArrToObj($(this).closest('form').serializeArray()))");


            /*$html .= '<div class="text-center"><button type="button" class="btn btn-primary" data-select-template data-name="" data-gtabid="' . $gtabid . '" data-element-id="' . $elementId. '" data-resolved="' . e(json_encode($resolvedTemplateGroups)) . '" data-resolve="1">' . $lang[33] . '</button></div>';
            
            if ($LINK["setup_report_select"]) {
                //$html .= $this->pop_submenu2($lang[3079], "limbasReportSaveTemlpate(event,'{$gtabid}','{$elementId}',keyValueArrToObj($(this).closest('form').serializeArray()))");
            }

            return '<form>' . $html . '</form>';*/


            ob_start();
            include(COREPATH . 'extra/template/select/html/resolved.php');
            return ob_get_clean() ?: '';
        }

        return false;
    }


    protected function pop_select($zl, $opt, $name, $title = null): string
    {

        if (!is_array($opt) || empty($opt)) return '';

        $options = "";
        if ($opt["val"]) {
            foreach ($opt["val"] as $key => $value) {
                $selected = "";
                if ($opt["label"][$key]) {
                    $options .= "<optgroup label=\"" . $opt["label"][$key] . "\">";
                }
                $options .= "<option value=\"$value\" $selected>{$opt["desc"][$key]}</option>";
            }
        }


        return '<div class="row mb-3">
                    <label for="' . $name . '" class="col-3 col-form-label">' . $title . '</label>
                    <div class="col-9">
                      <select name="' . $name . '" id="' . $name . '" onchange="' . $zl . '" class="form-select form-select-sm">
                                      ' . $options . '
                       </select>
                    </div>
                  </div>';
    }


}
