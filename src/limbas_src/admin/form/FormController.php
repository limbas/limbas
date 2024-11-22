<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\form;

require_once COREPATH . 'gtab/gtab.lib';

use Limbas\admin\templates\TemplateEditor;
use Limbas\lib\LimbasController;

class FormController extends LimbasController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'save' => $this->saveForm($request),
            'delete' => $this->deleteForm($request),
            'update' => $this->updateForm($request),
            
            'saveTemplate' => $this->saveTemplate($request),
            'loadTemplate' => $this->loadTemplate($request),
            default => ['success' => false],
        };
    }
    
    
    public function index(): string
    {
        global $lang;
        global $umgvar;
        global $tabgroup;
        
        $forms = Form::all();
        
        ob_start();
        include(COREPATH . 'admin/form/html/list/index.php');
        return ob_get_clean() ?: '';
    }
    

    public function show(int $formId): string
    {
        global $umgvar;
        global $session;

        /*require_once COREPATH . 'gtab/gtab.lib';

        $templateEditor = TemplateEditor::getInstanceFromType('form');
        return $templateEditor->render();*/
        
        require_once COREPATH . 'gtab/gtab.lib';

        $form = Form::get($formId);

        ob_start();
        include(COREPATH . 'admin/form/html/editor/show.php');
        return ob_get_clean() ?: '';
    }
    
    
    public function showOld(): string
    {
        global $form_id;
        global $form_name;
        global $form_typ;
        global $referenz_tab;
        
        ob_start();
        include(COREPATH . 'admin/form/html/editor_old/frames.php');
        return ob_get_clean() ?: '';
    }
    
    

    /**
     * @param array $request
     * @return array
     */
    private function saveForm(array $request): array
    {
        global $gtab;

        $tabId = intval($request['tabId']);
        if ($tabId !== 0 && !in_array($tabId, $gtab['tab_id'])) {
            $tabId = 0;
        }


        $form = null;
        $success = false;
        if (array_key_exists('copy', $request) && !empty($request['copy'])) {
            $form = Form::get(intval($request['copy']));
            if ($form !== null) {
                $form = $form->copy($request['name']);
            }
            if ($form === null) {
                return ['success' => false];
            }
            $success = true;
        }


        if ($form === null) {
            $form = new Form(
                0,
                $request['name'],
                $tabId,
                FormMode::OLD,
                FormType::tryFrom(intval($request['type'])) ?? FormType::DETAILS,
                extension: $request['extension'] ?: null
            );
            $success = $form->save();
        }


        $html = '';
        if ($success) {
            ob_start();
            $isNew = true;
            include(COREPATH . 'admin/form/html/list/form-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }

    private function updateForm(array $request): array
    {
        $form = Form::get(intval($request['id']));
        if ($form === null) {
            return ['success' => false];
        }
        $field = $request['field'];
        $allowedFields = ['name' => 'name', 'custom_menu' => 'customMenu'];
        if (!array_key_exists($field, $allowedFields)) {
            return ['success' => false];
        }

        $form->{$allowedFields[$field]} = $request['value'];
        $success = $form->save();
        
        // $gformlist[$reftab]["name"][$edit_id] = $edit_value;
        // $_SESSION['gformlist'][$reftab]["name"][$edit_id] = $edit_value;

        return compact('success');
    }

    /**
     * @param array $request
     * @return array
     */
    private function deleteForm(array $request): array
    {
        $form = Form::get(intval($request['id']));
        $success = true;
        if ($form !== null) {
            $success = $form->delete();
        }
        return compact('success');
    }

    /**
     * @param array $request
     * @return array
     */
    private function saveTemplate(array $request): array
    {
        $form = Form::get(intval($request['id']));

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        $form->css = $data['html'][0]['css'];
        $form->config = $data['data'];
        
        $html = str_replace(['<body','</body>'],['<div','</div>'], $data['html'][0]['html']);


        $update = [
            "$form->rootTemplateTabId,2,$form->rootTemplateElementId" => $html
        ];
        update_data($update);
        
        $success = $form->save();
        return compact('success');
    }


    /**
     * @param array $request
     * @return array
     */
    private function loadTemplate(array $request): array
    {
        $form = Form::get(intval($request['id']));
        
        return $form->config ?? [
            "assets" => [
            ],
            "styles" => [
            ],
            "pages" => [
                [
                    "component" => ""
                ]
            ]
        ];
    }
    
}
