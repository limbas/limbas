<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\templates;

use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;
use Limbas\extra\template\report\ReportTemplateConfig;
use Limbas\extra\template\TemplateTable;

abstract class TemplateEditor
{

    protected string $type;


    public static function getInstanceFromType(string $type): ?TemplateEditor
    {
        return match ($type) {
            'report' => new ReportTemplateEditor(),
            'mail' => new MailTemplateEditor(),
            default => null,
        };
    }


    public abstract function render(): string;

    protected abstract function saveSettings(array $params): bool;

    protected abstract function changeRootTemplate(int $elementId, int $newRootTemplateTabId): bool;

    protected function renderMain($elementId, $templateTableId, $templateId, $title, $tableName, $editorSettings): string
    {

        ob_start();

        TemplateConfig::$instance = new ReportTemplateConfig([], [], 0, null);
        $template = TemplateElement::fromId($templateTableId, $templateId);

        $templateTables = TemplateTable::all();

        $type = $this->type;

        require(__DIR__ . '/html/editor/editor.php');
        return ob_get_clean() ?: '';
    }


    public function save(array $params): bool
    {
        require_once(COREPATH . 'gtab/gtab.lib');

        // update content of the template element
        if ($params['contentchanged']) {
            $update = [
                "{$params['gtabid']},2,{$params['ID']}" => $params['content']
            ];
            update_data($update);
        }

        $success = true;

        // if source table has changed => create new element in new table
        if ($params['templatechanged']) {
            require_once(COREPATH . 'admin/report/report.lib');
            $success = $this->changeRootTemplate(intval($params['element_id']), intval($params['template_table']));
        }

        // update type specific settings
        if ($params['formchanged']) {
            $success = $success && $this->saveSettings($params);
        }

        return $success;

    }
}
