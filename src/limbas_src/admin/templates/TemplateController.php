<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\templates;

use JetBrains\PhpStorm\NoReturn;
use Limbas\extra\template\report\ReportTemplateResolver;
use Limbas\extra\template\wysiwyg\Wysiwyg;

class TemplateController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'saveTemplate' => $this->saveTemplate($request),
            'loadStructure' => $this->loadStructure($request),
            'getForm' => $this->getForm($request),
            'addTemplate' => $this->addTemplate($request),
            'saveSingleTemplate' => $this->saveSingleTemplate($request),
            'deleteSingleTemplate' => $this->deleteSingleTemplate($request),
            'wysiwyg' => $this->handleWysiwygAction($request),
            default => ['success' => false]
        };
    }


    protected function saveTemplate(array $params): array
    {
        $editor = TemplateEditor::getInstanceFromType($params['type']);
        return ['success' => $editor->save($params)];
    }

    protected function loadStructure(array $params): array
    {
        global $greportlist;

        $reportspec = $greportlist[$params['gtabid']];
        if(!$reportspec){return [];}

        require_once(COREPATH . 'extra/report/report.dao');
        require_once(COREPATH . 'extra/template/report/ReportTemplateConfig.php');
        require_once(COREPATH . 'extra/template/report/ReportTemplateResolver.php');

        $settings = [];
        if(array_key_exists('settings',$params) && !empty($params['settings'])) {
            $settings = $params['settings'];
        }
        
        $gtabid = intval($params['gtabid'] ?? 0);
        $reportId = intval($params['reportid'] ?? 0);
        $tgtabid = intval($params['tgtabid'] ?? 0);
        $template = $params['template'] ?? '';
        

        $reportTemplateResolver = new ReportTemplateResolver($reportId);
        return $reportTemplateResolver->getTemplateStructure($gtabid,$reportId,$tgtabid,$template,$settings);
    }

    protected function getForm(array $params): array
    {
        global $gtab;
        global $lmmultitenants;
        global $umgvar;
        global $session;

        require_once(COREPATH . 'gtab/gtab.lib');

        $ID = $params['id'];
        $gtabid = $params['gtabid'];

        //ob_start();

        if($ID){
            $gresult = get_gresult($gtabid,null,null,null,0,0,$ID);
        }/*elseif($ID == 0) {
        $gresult = get_default_values($gtabid);
    }*/
        else {
            return ['content'=>''];
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

        return ['content'=>$gresult[$gtabid][2][0]];
    }

    protected function addTemplate(array $params): array
    {
        global $umgvar;
        global $gfield;
        global $gtab;

        require_once(COREPATH . 'gtab/gtab.lib');
        require_once(COREPATH . 'gtab/sql/add_select.dao');

        $group = $params['group'];
        $gtabid = $params['tgtabid'];
        $name = $params['name'];
        $sourceTemplate = $params['source'];

        $ID = new_data($gtabid);

        if (!$ID) {
            return ['success'=>false];
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

        pool_select_update_old($gtabid,$gfield[$gtabid]['argresult_name']['GROUPS'],$ID,[$group]);

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
            'edit'=> (bool)$gtab['edit'][$gtabid],
            'add'=> (bool)$gtab['add'][$gtabid],
            'copy'=> (bool)$gtab['copy'][$gtabid],
            'delete'=> (bool)$gtab['delete'][$gtabid]
        ];

        return ['success'=>true,'element'=>$output];
    }

    protected function saveSingleTemplate(array $params): array
    {

        require_once(COREPATH . 'gtab/gtab.lib');

        $id = $params['id'];
        $gtabid = $params['gtabid'];
        $content = $params['content'];

        $update = [
            "$gtabid,2,$id" => $content
        ];

        update_data($update);

        return ['success'=>true];
    }

    protected function deleteSingleTemplate(array $params): array
    {
        //TODO: delete report template; postponed due to major changes in the new bootstrap template

        $id = $params['id'];
        $gtabid = $params['gtabid'];

        return ['success'=>del_data($id,$gtabid)];
    }

    #[NoReturn] protected function handleWysiwygAction(array $params): array
    {
        header('Content-Type: text/html; charset=utf-8');
        $wysiwyg = new Wysiwyg();
        
        switch($params['taction']) {
            case 'functionSelection':
                $wysiwyg->functionSelection($params);
                break;
            case 'paramSelection':
                $wysiwyg->paramSelection($params);
                break;
            case 'getTemplateGroups':
                $wysiwyg->getTemplateGroups($params);
                break;
            case 'forTableSelection':
                $wysiwyg->forTableSelection($params);
                break;
            case 'dataFieldSelection':
                $wysiwyg->dataFieldSelection($params);
                break;
        }

        exit();
    }
    
    
}
