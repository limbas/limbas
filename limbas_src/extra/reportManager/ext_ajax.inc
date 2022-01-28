<?php




function dyns_reportManagerLoadStructure($params){
    global $greportlist;
    
    header('Content-Type: application/json');

    $reportspec = $greportlist[$params['gtabid']];
    if(!$reportspec){return;}

    require_once('extra/report/report.dao');
    require_once('extra/template/report/ReportTemplateConfig.php');
    require_once('extra/template/report/ReportTemplateResolver.php');
    
    $settings = [];
    if(array_key_exists('settings',$params) && !empty($params['settings'])) {
        $settings = $params['settings'];
    }
    
    echo json_encode(ReportTemplateResolver::getTemplateStructure($params['gtabid'],$params['reportid'],$params['tgtabid'],$params['template'],$settings));
    
    return;
}

function dyns_reportManagerGetForm($params) {
    global $gtab;
    global $lmmultitenants;
    global $umgvar;
    global $session;

    require_once('gtab/gtab.lib');

    $ID = $params['id'];
    $gtabid = $params['gtabid'];

    //ob_start();

    if($ID){
        $gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
    }/*elseif($ID == 0) {
        $gresult = get_default_values($gtabid);
    }*/
    else {
        echo json_encode(['content'=>'']);
        return;
    }

    $readonly = check_DataPermission($gtabid,$ID,$gresult);

    # ----------- multitenant permission  -----------
    if($umgvar['multitenant'] AND $gtab['multitenant'][$gtabid] AND $lmmultitenants['mid'][$session['mid']] != $gresult[$gtabid]['MID'][0] AND !$session["superadmin"]){
        $action = 'gtab_deterg';
        $readonly = 1;
    }

    //printContextMenus($gtabid,1,$ID,$gresult,$readonly);
    //defaultView($gtabid,$ID,$gresult,$readonly,null);

    //$out1 = ob_get_contents();
    //ob_end_clean();


    //formlist_inparea($key,$gformid,$gtabid,$field_id,$tab_id,&$gresult,$datid);

    echo json_encode(['content'=>$gresult[$gtabid][2][0]]);
}

function dyns_reportManagerAddTemplate($params) {
    global $umgvar;
    global $gfield;
    global $gtab;

    require_once('gtab/gtab.lib');
    require_once('gtab/sql/add_select.dao');

    $group = $params['group'];
    $gtabid = $params['tgtabid'];
    $name = $params['name'];
    $sourceTemplate = $params['source'];

    $ID = new_data($gtabid);
    
    if (!$ID) {
        echo json_encode(['success'=>false]);
        return;
    }
    
    $update = [
        "$gtabid,1,$ID" => $name
    ];
    
    
    if (!empty($sourceTemplate)) {
        $sourcegtabid = $params['sourcegtabid'];
        
        $contentfid = $gfield[$gtabid]['argresult_name']['CONTENT'];
        $contentsfid = $gfield[$sourcegtabid]['argresult_name']['CONTENT'];

        $gresult = get_gresult($sourcegtabid,null,null,null,0,0,$sourceTemplate);
        
        $update["$gtabid,$contentfid,$ID"] = $gresult[$sourcegtabid][$contentsfid][0];
    }
    
    update_data($update);

    select_select($gtabid,$gfield[$gtabid]['argresult_name']['GROUPS'],$ID,[$group]);

    $output = [
        'id'=>$ID,
        'name' => htmlentities($name,ENT_QUOTES, $umgvar['charset']),
        'settings' => '',
        'report_id' => 0,
        'gtabid' => 0,
        'tgtabid' => $gtabid,
        'haschildren'=>false,
        'children'=>[],
        'isgroup'=>false,
        'edit'=>($gtab['edit'][$gtabid]) ? true : false,
        'add'=>($gtab['add'][$gtabid]) ? true : false,
        'copy'=>($gtab['copy'][$gtabid]) ? true : false,
        'delete'=>($gtab['delete'][$gtabid]) ? true : false
    ];

    echo json_encode(['success'=>true,'element'=>$output]);
}

function dyns_reportManagerSaveTemplate($params) {

    require_once('gtab/gtab.lib');

    $id = $params['id'];
    $gtabid = $params['gtabid'];
    $content = $params['content'];

    $update = [
        "$gtabid,2,$id" => $content
    ];

    update_data($update);

    echo json_encode(['success'=>true]);
}

function dyns_reportManagerDeleteTemplate($params) {
    //TODO: delete report template; postponed due to major changes in the new bootstrap template
    echo json_encode(['success'=>true]);
}
