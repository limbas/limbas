<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\report;

use Limbas\admin\templates\TemplateEditor;
use Limbas\lib\LimbasController;

class ReportController extends LimbasController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'save' => $this->saveReport($request),
            'delete' => $this->deleteReport($request),
            'update' => $this->updateReport($request),
            'getTemplates' => $this->getTemplates($request),
            'addTemplate' => $this->addTemplate($request),
            default => ['success' => false],
        };
    }
    
    
    public function index(): string
    {
        global $lang;
        global $umgvar;
        global $tabgroup;
        
        $reports = Report::all();
        
        ob_start();
        include(COREPATH . 'admin/report/html/list/index.php');
        return ob_get_clean() ?: '';
    }
    

    public function editor(): string
    {
        require_once COREPATH . 'gtab/gtab.lib';

        $templateEditor = TemplateEditor::getInstanceFromType('report');
        return $templateEditor->render();
    }
    

    /**
     * @param array $request
     * @return array
     */
    private function saveReport(array $request): array
    {
        global $gtab;

        $tabId = intval($request['tabId']);
        if ($tabId !== 0 && !in_array($tabId, $gtab['tab_id'])) {
            $tabId = 0;
        }


        $report = null;
        $success = false;
        if (array_key_exists('copy', $request) && !empty($request['copy'])) {
            $report = Report::get(intval($request['copy']));
            if ($report !== null) {
                $report = $report->copy($request['name']);
            }
            if ($report === null) {
                return ['success' => false];
            }
            $success = true;
        }


        if ($report === null) {
            $report = new Report(
                0,
                $request['name'],
                $tabId,
                $request['format']
            );
            $success = $report->save();
        }


        $html = '';
        if ($success) {
            ob_start();
            $isNew = true;
            include(COREPATH . 'admin/report/html/list/report-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }

    private function updateReport(array $request): array
    {
        $report = Report::get(intval($request['id']));
        if ($report === null) {
            return ['success' => false];
        }
        $field = $request['field'];
        $allowedFields = ['name' => 'name', 'target' => 'dmsFolderId', 'saveName' => 'saveName', 'printer' => 'printer', 'template' => 'savedTemplate', 'standard' => 'standard', 'standard_auto' => 'standardAuto'];
        if (!array_key_exists($field, $allowedFields)) {
            return ['success' => false];
        }
        
        $value = $request['value'];
        if($field === 'standard') {
            $value = PdfStandard::tryFrom($value) ?? PdfStandard::DEFAULT;
        }
        elseif($field === 'standard_auto') {
            $value = boolval($value);
        }

        $report->{$allowedFields[$field]} = $value;
        $success = $report->save();

        return compact('success');
    }

    /**
     * @param array $request
     * @return array
     */
    private function deleteReport(array $request): array
    {
        $report = Report::get(intval($request['id']));
        $success = true;
        if ($report !== null) {
            $success = $report->delete();
        }
        return compact('success');
    }

    private function getTemplates(array $request): array
    {
        $report = Report::get(intval($request['id']));

        $childReports = $report->getChildReports();

        $success = true;
        $html = '';

        foreach ($childReports as $report) {
            ob_start();
            include(COREPATH . 'admin/report/html/list/template-row.php');
            $html .= ob_get_contents();
            ob_end_clean();
        }

        return compact('success', 'html');
    }

    /**
     * @param array $request
     * @return array
     */
    private function addTemplate(array $request): array
    {
        $report = Report::get(intval($request['id']));
        $success = $report->addChild($request['name'] ?? $report->name, $request['template'] ?? '');
        $html = '';
        if ($success) {
            ob_start();
            include(COREPATH . 'admin/report/html/list/template-row.php');
            $html = ob_get_contents();
            ob_end_clean();
        }
        return compact('success', 'html');
    }


}
