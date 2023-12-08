<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\report;

use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateConfigInterface;

require_once __DIR__ . '/functions.php';

class ReportTemplateConfig extends TemplateConfig implements TemplateConfigInterface
{

    public $report;
    public $parameter;

    /**
     * ReportTemplateConfig constructor.
     * @param array $element
     * @param $parameter string limbas report editor param
     * @param int $gtabid
     * @param $datid
     */
    public function __construct(array $element, $parameter, int $gtabid, $datid)
    {
        $this->report = $element;
        $this->report['referenz_tab'] = $gtabid;
        $this->dataTabId = $gtabid;
        $this->parameter = $parameter;
        $this->datIDs[] = $datid;
    }

    public function getFunctionPrefix(): string
    {
        return 'report_';
    }

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html, $id = 0, $gtabid = null, $datid = null, $recursion = 0): ReportTemplateElement
    {
        return new ReportTemplateElement($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public function getDataPlaceholderInstance($chain, $options, $altValue): ReportDataPlaceholder
    {
        return new ReportDataPlaceholder($chain, $options, $altValue, !$this->resolveDataPlaceholders);
    }

    public function getMedium(): string
    {
        return 'report';
    }

    public function forTemporaryBaseTable($gtabid, $func)
    {
        $oldTab = $this->dataTabId;
        $this->dataTabId = $gtabid;
        $result = $func();
        $this->dataTabId = $oldTab;
        return $result;
    }

    public function resolveDataPlaceholdersForTemporaryBaseTable($baseTableID, $datid, &$placeholders)
    {
        $oldTab = $this->dataTabId;
        $this->dataTabId = $baseTableID;
        ReportTemplateElement::resolveDataPlaceholdersForTable($datid, $placeholders);
        $this->dataTabId = $oldTab;
    }
}

