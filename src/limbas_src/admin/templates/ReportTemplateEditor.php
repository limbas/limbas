<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\templates;

use Limbas\admin\report\Report;
use Limbas\admin\setup\fonts\Font;
use Limbas\extra\template\TemplateTable;
use Limbas\lib\db\Database;

class ReportTemplateEditor extends TemplateEditor
{

    protected string $type = 'report';

    public function render(): string
    {
        global $gtab;
        global $greport;
        global $greportlist;
        global $report_id;

        $greportlist = resultreportlist();
        $greport = $greportlist[$greportlist['gtabid'][$report_id]];

        $report = Report::get(intval($report_id));

        if ($report->defFormat !== 'mpdf') {
            return $this->renderOld();
        }

        $tableName = $gtab['desc'][$report->gtabId];

        $editorSettings = $this->renderSettings();

        return $this->renderMain($report_id, $report->rootTemplateTabId, $report->rootTemplateElementId, $report->name, $tableName, $editorSettings);
    }


    protected function renderSettings(): string
    {
        global $report_id;
        global $greport;
        global $greportlist;

        $fonts = Font::all();
        
        ob_start();
        require(COREPATH . 'admin/report/html/editor/settings.php');

        return ob_get_clean() ?: '';
    }


    protected function renderOld(): string
    {
        global $report_id;
        global $greport;
        global $greportlist;

        ob_start();

        require(COREPATH . 'admin/report/html/editor_old/frames.php');

        return ob_get_clean() ?: '';
    }


    protected function saveSettings($params): bool
    {
        global $db;
        global $action;

        // paper size
        $paper_size = $params['paper_size'];
        #if($params['paper_orientation']){
        #    $paper_size = $paper_size.'-'.$params['paper_orientation'];
        #}

        $pageStyle = [
            'size_w' => 'A4',
            'size_h' => '',
            'mt' => $params['margin'],
            'mb' => $params['margin'],
            'mr' => $params['margin'],
            'ml' => $params['margin']
        ];
        if ($paper_size == 'custom' and $params['custom_size_w'] and $params['custom_size_h']) {
            $pageStyle['size_w'] = $params['custom_size_w'];
            $pageStyle['size_h'] = $params['custom_size_h'];
        } elseif ($paper_size !== 'custom') {
            $pageStyle['size_w'] = $paper_size;
        }

        // paper margin
        if ($params['margin_top']) {
            $pageStyle['mt'] = $params['margin_top'];
        }
        if ($params['margin_bottom']) {
            $pageStyle['mb'] = $params['margin_bottom'];
        }
        if ($params['margin_left']) {
            $pageStyle['ml'] = $params['margin_left'];
        }
        if ($params['margin_right']) {
            $pageStyle['mr'] = $params['margin_right'];
        }

        //fonts
        $used_fonts[] = $params['default_font'];
        if ($params['extended_font']) {
            array_push($used_fonts, $params['extended_font']);
        }

        // paper
        $page_style = implode(';', $pageStyle);

        $sqlquery = "UPDATE LMB_REPORT_LIST SET 
        DPI = " . parse_db_int($params['dpi']) . ",
        PAGE_STYLE = '" . parse_db_string($page_style, 100) . "',
        LISTMODE = " . parse_db_bool($params['listmode']) . ",
        CSS = '" . parse_db_string($params['default_class']) . "',
        USED_FONTS = '" . parse_db_string(implode(';', $used_fonts)) . "',
        ORIENTATION = '" . parse_db_string($params['orientation']) . "'
        WHERE ID = " . parse_db_int($params['element_id']);
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        if (!$rs) {
            return false;
        }

        return true;
    }

    protected function changeRootTemplate(int $elementId, int $newRootTemplateTabId): bool
    {
        global $db;
        global $action;

        require_once(COREPATH . 'gtab/gtab.lib');


        // get old template IDs
        $sqlquery = "SELECT NAME, ROOT_TEMPLATE, ROOT_TEMPLATE_ID FROM LMB_REPORT_LIST WHERE ID = $elementId";
        $rs = lmbdb_exec($db, $sqlquery) or errorhandle(lmbdb_errormsg($db), $sqlquery, $action, __FILE__, __LINE__);
        $reportName = lmbdb_result($rs, 'NAME');
        $oldTemplateTableId = intval(lmbdb_result($rs, 'ROOT_TEMPLATE'));
        $oldTemplateElementId = intval(lmbdb_result($rs, 'ROOT_TEMPLATE_ID'));

        if ($oldTemplateTableId === $newRootTemplateTabId) {
            return true;
        }

        $templateTable = TemplateTable::get($newRootTemplateTabId);
        if ($templateTable === null) {
            $templateTable = TemplateTable::getDefaultTable();
        }

        if ($templateTable->id === $oldTemplateTableId) {
            return true;
        }

        $rootTemplateElementId = $templateTable->insertRootElement($reportName, $oldTemplateTableId, $oldTemplateElementId);
        if ($rootTemplateElementId !== false) {
            return Database::update('LMB_REPORT_LIST', ['ROOT_TEMPLATE' => $templateTable->id, 'ROOT_TEMPLATE_ID' => $rootTemplateElementId], ['ID' => $elementId]);
        }
        return false;
    }
}
