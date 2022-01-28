<?php


class ReportMenuRenderBs4 extends ReportMenuRender {


    public function printReportTemplateOptions($preview,$gtabid, $report_id, $ID, $report_output, $use_record, $resolvedTemplateGroups, $listmode, $nodata = false, $resolvedDynamicData = '', $saveAsTemplate='') {
        require_once('extra/template/report/ReportTemplateResolver.php');

        global $lang;
        global $LINK;

        $datid = $ID ? $ID : lmb_split('_', $use_record, 2)[0];
        $resolvedTemplateGroups = array_filter(json_decode(urldecode($resolvedTemplateGroups), true));
        $resolvedDynamicData = json_decode(urldecode($resolvedDynamicData), true);
        $resolvedReportTemplate = ReportTemplateResolver::resolveReport( $gtabid, $report_id, $resolvedTemplateGroups, $datid, true, false, $resolvedDynamicData );


        if (!empty($saveAsTemplate)) {
            lmbSaveReportTemplate($gtabid, $report_id,$saveAsTemplate,$resolvedTemplateGroups);
            return true;
        }

        // check if any template groups need to be resolved
        if (!$report_output) {

            $unresolvedTemplateGroupsExist = false;
            
            $html = '';

            // for each template element in report
            foreach ($resolvedReportTemplate as $reportTemplate) {

                $unresolvedTemplateGroups = $reportTemplate['unresolved'];
                if ($unresolvedTemplateGroups) {
                    $unresolvedTemplateGroupsExist = true;
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
                    $html .= $this->pop_select(null, $opt, null, null, $templateGroup->getIdentifier(), $desc);
                }


            }

            if ($unresolvedTemplateGroupsExist) {

                $html .= $this->pop_submenu2($lang[33], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',0,'{$listmode}',null,null,null,keyValueArrToObj($(this).closest('form').serializeArray()))");
                if ($LINK["setup_report_select"]) {
                    $html .= $this->pop_submenu2($lang[3079], "limbasReportSaveTemlpate(event,'{$gtabid}','{$report_id}',keyValueArrToObj($(this).closest('form').serializeArray()))");
                }

                echo '<form>';
                echo $html;
                echo '</form>';
                
                return true;
            }
        }



        // check if any dynamic data placeholders need to be resolved
        if (!$nodata) {

            $html = '';
            
            $unresolvedDynamicDataPlaceholdersExist = false;


            $html .= '<form class="lmbReportTemplateGroups">';
            $html .= '<div class="lmbReportTemplateGroups">';
            foreach ($resolvedTemplateGroups as $identifier => $templateName) {
                $html .= "<input type=\"hidden\" name=\"$identifier\" value=\"$templateName\">";
            }
            $html .= '</div>';
            $html .= '</form>';


            $html .= '<form class="lmbReportDynamicData">';
            // for each template element in report
            foreach ($resolvedReportTemplate as $reportTemplate) {
                
                // unresolved template group elements exist?
                $unresolvedDynamicDataPlaceholders = $reportTemplate['unresolvedData'];
                if ($unresolvedDynamicDataPlaceholders) {
                    $unresolvedDynamicDataPlaceholdersExist = true;
                }

                // show input/select/extension...
                $html .= '<div class="lmbReportDynamicData">';
                foreach ($unresolvedDynamicDataPlaceholders as $dynamicDataPlaceholder) {
                    // get options
                    $options = $dynamicDataPlaceholder->getOptions();
                    if (!$options['type']) {
                        $options['type'] = 'text';
                    }

                    switch ($options['type']) {
                        case 'text':
                            $html .= $this->pop_input2(null, $dynamicDataPlaceholder->getIdentifier(), '', false, $dynamicDataPlaceholder->getDescription());
                            break;

                        case 'number':
                            $onchange = "checktyp(16,'{$dynamicDataPlaceholder->getIdentifier()}','{$dynamicDataPlaceholder->getDescription()}',null,null,this.value,null,null,null,null,null);";
                            $html .= $this->pop_input2($onchange, $dynamicDataPlaceholder->getIdentifier(), '', false, $dynamicDataPlaceholder->getDescription());
                            break;

                        case 'date':
                            $html .= $this->pop_input2('', $dynamicDataPlaceholder->getIdentifier(), '', false, $dynamicDataPlaceholder->getDescription(),null,'date');
                            break;

                        case 'select':
                            if (!$options['select_id']) {
                                lmb_log::error('Dynamic data: "select_id" not set', $lang[56]);
                                break;
                            }
                            require_once('gtab/sql/add_select.dao');
                            $values = select_list_simple($options['select_id'], $options['is_attribute']);
                            array_unshift($values['wert'], '');
                            $options = array(
                                'val' => $values['wert'],
                                'desc' => $values['wert'],
                                'label' => array(),
                            );
                            $html .= $this->pop_select(null, $options, null, null, $dynamicDataPlaceholder->getIdentifier(), $dynamicDataPlaceholder->getDescription());
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
                $html .= '</div>';


            }
            $html .= '</form>';

            // "continue"-button
            if ($unresolvedDynamicDataPlaceholdersExist) {
                $html .= $this->pop_submenu2($lang[33], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',0,'{$listmode}',null,null,null,keyValueArrToObj($(this).siblings('form.lmbReportTemplateGroups').serializeArray()),keyValueArrToObj($(this).siblings('form.lmbReportDynamicData').serializeArray()) )");

                
                echo $html;
                
                return true;
            }
        }

        return false;
    }

    
    
    protected function printReportPrintFormInternal($action, $report_id, $gtabid, $ID, $listmode, $report_medium, $report_medium_opt, $report_rename, $resolvedTemplateGroupsStr, $resolvedDynamicDataStr) {
        global $lang;
        global $gprinter;
        global $LINK;

        $html = $this->pop_select('',$report_medium_opt,$report_medium,1,'report_medium',$lang[2502]."&nbsp;",'');

        if($action != 'gtab_erg'){
            $html .= $this->pop_input2('','report_rename',$report_rename,$readonly,$title,$titlesize);
        }

        $html .= $this->pop_submenu2($lang[1612], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',1,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");
            
        $html .= $this->pop_submenu2($lang[1739], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',1,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr},1)");

        $html .= $this->pop_submenu2($lang[1787], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',2,'{$listmode}',null,null,null, {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");



        if ($LINK[304] and $gprinter) {

            $html .= '<hr>';


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
            $html .= $this->pop_select('', $opt, $sel, 1, 'lmbReportMenuOptionsPrinter', $lang[2939]);

            $html .= $this->pop_submenu2($lang[$LINK["name"][304]], "reportModalSelectItem(event,null,'{$gtabid}','{$report_id}','{$ID}',4,'{$listmode}',null,null,$('#lmbReportMenuOptionsPrinter').val(), {$resolvedTemplateGroupsStr}, {$resolvedDynamicDataStr})");


        }

        echo '<form id="report_form_bs">';
        echo $html;
        echo '</form>';
    }



    private function pop_select($zl,$opt,$sel,$size,$name,$title=null,$titlesize=null,$icon=null){

        if(!is_array($opt) || empty($opt)) return;
        
        $options = "";
        if($opt["val"]){
            foreach($opt["val"] as $key => $value){
                $selected = "";
                if($sel == $value) $selected = "selected=\"selected\"";
                if($opt["label"][$key]){
                    $options .= "<optgroup label=\"".$opt["label"][$key]."\">";
                }
                $options .= "<option value=\"$value\" $selected>{$opt["desc"][$key]}</option>";
            }
        }
        
        
        return '<div class="form-group row mb-3">
                    <label for="'.$name.'" class="col-3 col-form-label">'.$title.'</label>
                    <div class="col-9">
                      <select name="'.$name.'" id="'.$name.'" onchange="'.$zl.'" class="form-control form-control-sm">
                                      '.$options.'
                       </select>
                    </div>
                  </div>';
    }

    private function pop_input2($zl,$name,$val,$readonly,$title,$titlesize=null,$type='text'){
        if(!$title){$title = "";}

        return '<div class="form-group row mb-3">
                    <label for="'.$name.'" class="col-3 col-form-label">'.$title.'</label>
                    <div class="col-9">
                      <input type="'.$type.'" name="'.$name.'" id="'.$name.'" value="'.$val.'" '.$readonly.' onchange="'.$zl.'" class="form-control form-control-sm">
                    </div>
                  </div>';
    }

    private function pop_submenu2($val,$zl,$zv=null,$width=null,$imgclass=null){        
        return '<button type="button" class="btn btn-outline-secondary btn-block mb-3" onclick="'.$zl.'";return false;" title="'.$zv.'">'.$val.'</button>';
    }
}
