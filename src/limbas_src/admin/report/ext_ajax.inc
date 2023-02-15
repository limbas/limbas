<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
function dyns_reportEditorSaveReport($params) {
    global $db;

    require_once(COREPATH . 'gtab/gtab.lib');

    if($params['contentchanged']){
        $update = [
            "{$params['gtabid']},2,{$params['ID']}" => $params['content']
        ];
        update_data($update);
    }

    if($params['formchanged']){

        // paper size
        $paper_size = $params['paper_size'];
        #if($params['paper_orientation']){
        #    $paper_size = $paper_size.'-'.$params['paper_orientation'];
        #}

        if($paper_size == 'custom' AND $params['custom_size_w'] AND $params['custom_size_h']){
            $paper_size = $params['custom_size_w'].';'.$params['custom_size_h'];
        }elseif($paper_size == 'custom'){
            $paper_size = 'A4;';
        }else{
            $paper_size = $paper_size.';';
        }

        // paper margin
        $mt = $params['margin'];
        $mb = $params['margin'];
        $ml = $params['margin'];
        $mr = $params['margin'];

        if($params['margin_top']){$mt = $params['margin_top'];}
        if($params['margin_bottom']){$mb = $params['margin_bottom'];}
        if($params['margin_left']){$ml = $params['margin_left'];}
        if($params['margin_right']){$mr = $params['margin_right'];}

        //fonts
        $used_fonts[] = $params['default_font'];
        if($params['extended_font']){
            array_push($used_fonts,$params['extended_font']);
        }

        // paper
        $page_style = $paper_size.';'.$mt.';'.$mb.';'.$ml.';'.$mr;

        $sqlquery = "UPDATE LMB_REPORT_LIST SET 
        ROOT_TEMPLATE = " . parse_db_int($params['gtabid']) . ",
        DPI = " . parse_db_int($params['dpi']) . ",
        PAGE_STYLE = '" . parse_db_string($page_style,100) . "',
        LISTMODE = " . parse_db_bool($params['listmode']) . ",
        CSS = '" . parse_db_string($params['default_class']) . "',
        USED_FONTS = '" . parse_db_string(implode(';',$used_fonts)) . "',
        ORIENTATION = '" . parse_db_string($params['orientation']) . "'
        WHERE ID = " . parse_db_int($params['report_id']);
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if(!$rs) {return false;}

        if($params['templatechanged']){
            require_once (COREPATH . 'admin/report/report.lib');
            lmb_report_add_root_template($params['report_id'],$params['template_table']);
        }

    }

    echo json_encode(['success'=>true]);
}

?>
