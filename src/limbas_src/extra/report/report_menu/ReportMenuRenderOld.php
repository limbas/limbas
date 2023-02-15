<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class ReportMenuRenderOld extends ReportMenuRender {


    public function printReportTemplateOptions($preview,$gtabid, $report_id, $ID, $report_output, $use_record, $resolvedTemplateGroups, $listmode, $nodata = false, $resolvedDynamicData = '', $saveAsTemplate='') {
        require_once(COREPATH . 'extra/template/report/ReportTemplateResolver.php');

        global $lang;
        global $LINK;

        $datid = $ID ? $ID : lmb_split('_', $use_record, 2)[0];
        $resolvedTemplateGroups = json_decode(urldecode($resolvedTemplateGroups), true);
        if (is_array($resolvedTemplateGroups)) {
            $resolvedTemplateGroups = array_filter($resolvedTemplateGroups);
        } else {
            $resolvedTemplateGroups = [];
        }
        $resolvedDynamicData = json_decode(urldecode($resolvedDynamicData), true);
        if (is_array($resolvedDynamicData)) {
            $resolvedDynamicData = array_filter($resolvedDynamicData);
        } else {
            $resolvedDynamicData = [];
        }
        $resolvedReportTemplate = ReportTemplateResolver::resolveReport( $gtabid, $report_id, $resolvedTemplateGroups, $datid, true, false, $resolvedDynamicData );

        if (empty($preview)) {
            $preview = 0;
        }

        if (!empty($saveAsTemplate)) {
            lmbSaveReportTemplate($gtabid, $report_id,$saveAsTemplate,$resolvedTemplateGroups);
            return true;
        }



        echo '<form class="lmbReportTemplateGroups">';

        foreach ($resolvedTemplateGroups as $identifier => $templateName) {
            if (!empty($templateName)) {
                echo "<input type=\"hidden\" name=\"$identifier\" value=\"$templateName\">";
            }
        }

        // check if any template groups need to be resolved
        if (!$report_output) {

            $unresolvedTemplateGroupsExist = false;

            // for each template element in report
            foreach ($resolvedReportTemplate as $reportTemplate) {

                $unresolvedTemplateGroups = $reportTemplate['unresolved'];
                if ($unresolvedTemplateGroups) {
                    $unresolvedTemplateGroupsExist = true;
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
                    pop_select(null, $opt, null, null, $templateGroup->getIdentifier(), $desc);
                }


            }

            // "continue"-button
            if ($unresolvedTemplateGroupsExist) {
                pop_line();
                pop_submenu2($lang[33], "limbasReportMenuHandler($preview,event,'','{$gtabid}','{$report_id}','{$ID}',0,'{$listmode}',null,null,null,keyValueArrToObj($(this).closest('.lmbContextMenu').find('form').serializeArray()))");
                if ($LINK["setup_report_select"]) {
                    pop_line();
                    pop_submenu2($lang[3079], "limbasReportSaveTemlpate(event,'{$gtabid}','{$report_id}',keyValueArrToObj($(this).closest('.lmbContextMenu').find('form').serializeArray()))");
                }

                echo '</form>';
                return true;
            }
        }
        echo '</form>';


        // check if any dynamic data placeholders need to be resolved
        if (!$nodata) {
            $unresolvedDynamicDataPlaceholdersExist = false;

            // for each template element in report
            foreach ($resolvedReportTemplate as $reportTemplate) {

                $unresolvedTemplateGroups = $reportTemplate['unresolved'];
                // unresolved template group elements exist?
                $unresolvedDynamicDataPlaceholders = $reportTemplate['unresolvedData'];
                if ($unresolvedDynamicDataPlaceholders) {
                    $unresolvedDynamicDataPlaceholdersExist = true;
                }

                // show input/select/extension...
                echo '<form class="lmbReportDynamicData">';
                echo '<div class="lmbReportDynamicData">';
                foreach ($unresolvedDynamicDataPlaceholders as $dynamicDataPlaceholder) {
                    // get options
                    $options = $dynamicDataPlaceholder->getOptions();
                    if (!$options['type']) {
                        $options['type'] = 'text';
                    }

                    switch ($options['type']) {
                        case 'text':
                            pop_input2(null, $dynamicDataPlaceholder->getIdentifier(), '', false, $dynamicDataPlaceholder->getDescription());
                            break;

                        case 'number':
                            $onchange = "checktyp(16,'{$dynamicDataPlaceholder->getIdentifier()}','{$dynamicDataPlaceholder->getDescription()}',null,null,this.value,null,null,null,null,null);";
                            pop_input2($onchange, $dynamicDataPlaceholder->getIdentifier(), '', false, $dynamicDataPlaceholder->getDescription());
                            break;

                        case 'date':
                            pop_left();
                            echo "<span style=\"vertical-align: middle;\">{$dynamicDataPlaceholder->getDescription()}</span>";
                            echo "<input id=\"{$dynamicDataPlaceholder->getIdentifier()}\" name=\"{$dynamicDataPlaceholder->getIdentifier()}\" type=\"text\" />";
                            echo "&nbsp;<i class=\"lmb-icon lmb-edit-caret\" TITLE=\"{$lang[700]}\" style=\"cursor:pointer;vertical-align:top;\" Onclick=\"lmb_datepicker(event,this,this.previousElementSibling,'','" . dateStringToDatepicker(setDateFormat(1, 1)) . "')\"></i>";
                            pop_right();
                            break;

                        case 'select':
                            if (!$options['select_id']) {
                                lmb_log::error('Dynamic data: "select_id" not set', $lang[56]);
                                break;
                            }
                            require_once(COREPATH . 'gtab/sql/add_select.dao');
                            $values = pool_select_list_simple($options['select_id'], $options['is_attribute']);
                            array_unshift($values['wert'], '');
                            $options = array(
                                'val' => $values['wert'],
                                'desc' => $values['wert'],
                                'label' => array(),
                            );
                            pop_select(null, $options, null, null, $dynamicDataPlaceholder->getIdentifier(), $dynamicDataPlaceholder->getDescription());
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
                            $funcName($dynamicDataPlaceholder->getIdentifier(), $dynamicDataPlaceholder->getDescription(), $options);
                            break;
                    }
                }
                echo '</div>';


            }

            // "continue"-button
            if ($unresolvedDynamicDataPlaceholdersExist) {
                pop_line();
                pop_submenu2($lang[33], "limbasReportMenuHandler($preview,event,'','{$gtabid}','{$report_id}','{$ID}',0,'{$listmode}',null,null,null,keyValueArrToObj($(this).closest('.lmbContextMenu').find('form.lmbReportTemplateGroups').serializeArray()),keyValueArrToObj($(this).closest('.lmbContextMenu').find('form.lmbReportDynamicData').serializeArray()) )");

                echo '</form>';
                return true;
            }
        }

        echo '</form>';

        return false;
    }

    
    protected function printReportPrintFormInternal($action, $report_id, $gtabid, $ID, $listmode, $report_medium, $report_medium_opt, $report_rename, $resolvedTemplateGroupsStr, $resolvedDynamicDataStr) {
        global $lang;
        global $gprinter;
        global $LINK;


        echo '<form id="report_form">';
        
        pop_top('limbasDivMenuReportOption');
        pop_select('',$report_medium_opt,$report_medium,1,'report_medium',$lang[2502]."&nbsp;",'');
        if($action != 'gtab_erg'){
            pop_input2('','report_rename',$report_rename,$readonly,$title,$titlesize);
        }
        pop_line();
        pop_menu2($lang[1612],'','','lmb-icon lmb-download','',"limbasReportMenuHandler(0,event,'','{$gtabid}','{$report_id}','{$ID}',1,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");

        pop_line();

        pop_menu2($lang[1739],'','','lmb-icon-cus lmb-script-go','',"limbasReportMenuHandler(1,event,'','{$gtabid}','{$report_id}','{$ID}',1,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");


        pop_menu2($lang[1787],'','lmbReportMenuOptionsArchive','lmb-icon-cus lmb-script-save','',"limbasReportMenuHandler(0,event,'','{$gtabid}','{$report_id}','{$ID}',2,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");
        if ($LINK[304] and $gprinter) {
            # separator
            pop_line();


            # printer selection
            $opt = array();
            $sel = '';
            foreach ($gprinter as $id => $printer) {
                $opt['val'][] = $id;
                $opt['desc'][] = $printer['name'];
                if ($printer['default']) {
                    $sel = $id;
                }
            }
            pop_select('', $opt, $sel, 1, 'lmbReportMenuOptionsPrinter', $lang[2939]);


            # print button
            pop_menu(304, "limbasReportMenuHandler(0,event,'','{$gtabid}','{$report_id}','{$ID}',4,'{$listmode}',null,null,$('#lmbReportMenuOptionsPrinter').val(), {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");


        }
        pop_bottom();

        echo '</form>';
        
    }
    
    
    
    
}
