<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\template\report;

use Limbas\extra\template\base\HtmlParts\DataPlaceholder;
use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;

require_once(COREPATH . 'gtab/gtab.lib');
require_once(COREPATH . 'extra/report/report.dao');

class ReportTemplateElement extends TemplateElement {

    /**
     * Resolve templates and data placeholders until everything is resolved
     * @param $datid
     */
    public function resolve($datid, &$gresult=null) {
        do {
            $successData = $this->resolveDataPlaceholders($datid);
            $successTpl = $this->resolveTemplates();
        } while ($successTpl || $successData);
    }


    /**
     * Resolves all DataPlaceholders to their corresponding data
     * @param $datid int current dataset id for which the template is being resolved
     * @return bool whether a placeholder was resolved
     */
    public function resolveDataPlaceholders($datid, &$gresult = null) {
        $unresolvedDataPlaceholders = $this->getUnresolvedDataPlaceholders();
        return self::resolveDataPlaceholdersForTable($datid, $unresolvedDataPlaceholders);
    }

    /**
     * Resolves all DataPlaceholders to their corresponding data
     * @param $datid int current dataset id for which the template is being resolved
     * @param $allDataPlaceholders DataPlaceholder[] placeholders to resolve
     * @return bool whether a placeholder was resolved
     */
    public static function resolveDataPlaceholdersForTable($datid, &$dataPlaceholders): bool
    {
        global $gfield;

        $report = &TemplateConfig::$instance->report;
        $parameter = &TemplateConfig::$instance->parameter;

        if (!$dataPlaceholders) {
            return false;
        }

        # group by trace
        $placeholdersByTrace = array();
        foreach ($dataPlaceholders as &$placeholder) {
            $placeholdersByTrace[$placeholder->getTrace()][] = $placeholder;
        }

        # resolve placeholders
        foreach ($placeholdersByTrace as $trace => &$placeholders) {
            // find ID-fetching placeholders
            $tableRowPlaceholders = array();
            $gsr = array();
            $filter = array();
            $extension = array();
            foreach ($placeholders as $key => &$placeholder) {
                if ($placeholder->isModeFetchIDs()) {
                    $tableRowPlaceholders[] = $placeholder;
                    $gsr = $placeholder->getGsr();
                    $filter = $placeholder->getFilter();
                    $extension = $placeholder->getExtension();
                    unset($placeholders[$key]);
                }
            }

            # collect fieldlist by trace
            $fieldlist = array();
            $i = 0;
            foreach ($placeholders as &$placeholder) {
                $fieldlist[$i] = $placeholder->getFieldlist();
                if ($placeholder->isModeFetchArr()) {
                    $fieldlist[$i][2] = str_repeat(';', 33) . 'true'; // listmode
                }
                $fieldlist[$i][3] = $parameter;
                $fieldlist[$i][5] = $placeholder->getOptions();
                $i++;
            }

            # empty repeated row -> fetch ID only
            if ($tableRowPlaceholders && !$fieldlist) {
                if ($tableRowPlaceholders[0]->isModeFetchBaseTable()) {
                    // get all datasets of current (base) table
                    $relationFieldlist = array();
                    $relationFieldlist[0] = $tableRowPlaceholders[0]->getTrace() . ';ID';
                    $relationFieldlist[2] = str_repeat(';', 33) . 'true'; // listmode
                } else {
                    // modify fieldlist from relationfield to ID field of related table
                    $relationFieldlist = $tableRowPlaceholders[0]->getFieldList();
                    list($gtabid, $fieldid) = explode(';', $relationFieldlist[0]);
                    $verknGtabid = $gfield[$gtabid]['verkntabid'][$fieldid];
                    $relationFieldlist[1] .= '|' . $gtabid . ';' . $verknGtabid . ';' . $fieldid;
                    $relationFieldlist[0] = $verknGtabid . ';' . 'ID';
                }
                $fieldlist[] = $relationFieldlist;
            }





            # get data from fields
            $data = get_dataArray($datid, $report['report_id'], null, $report, null, null, null, $fieldlist, $gsr, $filter, $extension);
            if ($tableRowPlaceholders) {
                foreach ($tableRowPlaceholders as &$p) {
                    $p->setValue($data['id']);
                }
            }

            # set placeholder value
            $i = 0;
            foreach ($placeholders as &$placeholder) {
                if ($placeholder->isModeFetchArr()) {
                    $placeholder->setValue($data[$i]);
                } else {
                    $placeholder->setValue($data[$i][0]);
                }
                $i++;
            }
        }
        return true;
    }

    public function getExtension() {
        $extension = null;

        $parameter = &TemplateConfig::$instance->parameter;
        if ($parameter) {
            eval($parameter);
            if (!isset($extension) or !array_key_exists($this->templateElementGtabid, $extension)) {
                $extension = null;
            } else {
                $extension = $extension[$this->templateElementGtabid];
            }
        }

        return $extension;
    }

}
