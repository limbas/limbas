<?php

require_once __DIR__.'/../../template/report/ReportTemplateResolver.php';


class LmbReportSelect {
    
    public static function printReportSelect($gtabid,$search='') {
        global $LINK;
        global $greportlist_exist;
        global $umgvar;
        global $lang;

        if ($greportlist_exist AND ($LINK[175] OR $LINK[176] OR $LINK[315])) {
            $reportlist = self::getReportList($gtabid, $search);
            if (!empty($reportlist['reportlist'])) {
                $page=1;
                $perPage=10;
                $maxPage = $reportlist['max_page'];
                $reportlist = $reportlist['reportlist'];
                require 'report_select_template.php';
            }
        }
    }

    public static function getReportListTable($gtabid,$search='',$page=1,$perPage=10) {
        global $lang;

        if (!ctype_digit($page)) {
            $page = 1;
        }

        if (!ctype_digit($perPage)) {
            $perPage = 10;
        }
        
        
        $reportlist = self::getReportList($gtabid,$search,$page,$perPage);
        
        $tablecontent = '';
        $pagination = '';
        
        
        foreach ($reportlist['reportlist'] as $report) {
            ob_start();
            include('report_select_row.php');
            $tablecontent .= ob_get_clean();
        }
        
        if (empty($reportlist['reportlist'])) {
            $tablecontent = '<tr><td colspan="2">'.$lang[3085].'</td></tr>';
        }

        $maxPage = $reportlist['max_page'];
        ob_start();
        include('report_select_pagination.php');
        $pagination .= ob_get_clean();
        
        return [
            'table'=>$tablecontent,
            'pagination'=>$pagination
        ];
    }


    private static function getReportList($gtabid,$search='',$page=1,$perPage=10) {
        global $greportlist;
        
        if (!array_key_exists('resolved',$greportlist[$gtabid])) {
            $reportlist = [];
            $duplicateFilter = [];

            foreach($greportlist[$gtabid]["id"] as $key => $reportid){
                if($greportlist[$gtabid]["hidden"][$key]){continue;}

                if ($greportlist[$gtabid]["is_template"][$key]) {

                    $reportlist = array_merge($reportlist,self::getPartsListofTemplate($key,$gtabid,$greportlist[$gtabid]['parent_id'][$key],json_decode($greportlist[$gtabid]['saved_template'][$key],true),$duplicateFilter));

                } else {

                    $reportlist[] = [
                        'id' => $reportid,
                        'name' => $greportlist[$gtabid]["name"][$key],
                        'preview' => $greportlist[$gtabid]["preview"][$key],
                        'event' => "reportModalSelectItem(event,this,'$gtabid','$reportid','0',0,'{$greportlist[$gtabid]['listmode'][$key]}')"
                    ];
                }


            }
            $greportlist[$gtabid]['resolved'] = $reportlist;
        }

        return self::paginateReportList(self::getFilteredReportList($greportlist[$gtabid]['resolved'],$search),$page,$perPage);
    }

    private static function getFilteredReportList($reportlist,$search) {

        if (!empty($search)) {
            function filterArray($needle,$haystack,$column){
                return strpos(strtolower($haystack[$column]), $needle) !== false;
            }

            $search = strtolower($search);
            $reportlist = array_filter($reportlist, function ($v) use ($search) {
                return filterArray($search, $v, 'name');
            });
        }


        $name = array_column($reportlist, 'name');

        array_multisort($name, SORT_ASC, $reportlist);
        
        return $reportlist;
    }
    
    private static function paginateReportList($reportlist,$page,$perPage) {
        $item_count = count($reportlist);

        $offset = ($page - 1) * $perPage;

        if ($offset > $item_count) {
            $offset = 0;
        }

        $maxPage = ceil($item_count / $perPage);
        
        return [
            'item_count' => $item_count,
            'max_page' => $maxPage,
            'reportlist' => array_slice($reportlist, $offset, $perPage)
            ];
    }
    

    private static function getPartsListofTemplate($key,$gtabid,$report_id,$settings,&$duplicateFilter) {
        global $greportlist;

        $output = [];

        $templateElements = ReportTemplateResolver::resolveReportToElements($gtabid,$report_id,$settings,0,true);

        $elementCount = count($templateElements);

        if ($elementCount <= 0 || $elementCount > 1) {

            //nothing has to be resolved => treat like normal report
            //more than one subgroup => let user resolve and just pass through settings
            
            $output[] = [
                'id' => $report_id,
                'name' => $greportlist[$gtabid]["name"][$key],
                'preview' => $greportlist[$gtabid]["preview"][$key],
                'event' => "reportModalSelectItem(event,this,'$gtabid','$report_id','0',0,'{$greportlist[$gtabid]['listmode'][$key]}', null, null, null, ".htmlspecialchars(json_encode($settings), ENT_QUOTES).")"
            ];

        } elseif ($elementCount == 1) {

            //exactly one subgroup => every child is a report
            $unresolvedGroupName = $templateElements[0]['name'];

            $append = '';

            //if this group was already resolved => skip
            if (in_array($unresolvedGroupName,$duplicateFilter)) {
                $append = ' ('.$greportlist[$gtabid]["name"][$key].')';
                //return [];
            }
            $duplicateFilter[] = $unresolvedGroupName;

            foreach ($templateElements[0]['children'] as $childtemplate) {

                $settings[$unresolvedGroupName] = $childtemplate['name'];

                $output[] = [
                    'id' => $report_id,
                    'name' => $childtemplate['name'].$append,
                    'preview' => $greportlist[$gtabid]["preview"][$key],
                    'event' => "reportModalSelectItem(event,this,'$gtabid','$report_id','0',0,'{$greportlist[$gtabid]['listmode'][$key]}', null, null, null, ".htmlspecialchars(json_encode($settings), ENT_QUOTES).")"
                ];
            }

        }

        return $output;
    }
    
    
}




