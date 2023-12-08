<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base;

use Limbas\extra\template\base\HtmlParts\Background;
use Limbas\extra\template\base\HtmlParts\DynamicDataPlaceholder;
use Limbas\extra\template\base\HtmlParts\FunctionPlaceholder;
use Limbas\extra\template\base\HtmlParts\HeaderFooter;
use Limbas\extra\template\base\HtmlParts\Html;
use Limbas\extra\template\base\HtmlParts\IfPlaceholder;
use Limbas\extra\template\base\HtmlParts\SubTemplateElementPlaceholder;
use Limbas\extra\template\base\HtmlParts\TableCell;
use Limbas\extra\template\base\HtmlParts\TableRow;
use Limbas\extra\template\base\HtmlParts\TemplateGroupPlaceholder;

abstract class TemplateConfig implements TemplateConfigInterface
{
    
    /** @var int the gtabid of the data source table */
    protected int $dataTabId;
    

    /**
     * @var TemplateConfig|null
     */
    public static ?TemplateConfig $instance = null;

    /**
     * Used to resolve TemplateGroups to the corresponding TemplateElement
     * @var array of key => value
     * where key is an identifier returned by TemplateGroupPlaceholder::getIdentifier()
     * and value is the name of a valid TemplateElement
     */
    public array $resolvedTemplateGroups = [];

    /**
     * Used to resolve DynamicDataPlaceholders to their corresponding value
     * @var array of key => value
     * where key is an identifier returned by DynamicDataPlaceholder::getIdentifier()
     * and value is an (html) string
     */
    public array $resolvedDynamicData = [];

    /**
     * Used to retrieve the current index of each TableRow. The innermost TableRow will have key 0 and set the value to
     * its index (e.g. 0=>0; 0=>1; 0=>2; ...). If "innermore" TableRow is encountered, it will move the previous indices
     * by one and start iterating on key 0 itself (e.g. 1=>2,0=>0; 1=>2,0=>1; ...).
     * @var array TableRowDepth => Row Index
     */
    public array $tableRowIndex = [];

    /**
     * Used to retrieve the current column index of each TableRow. The innermost TableRow will have key 0 and set the value to
     * its colum index (e.g. 0=>0; 0=>1; 0=>2; ...). If "innermore" TableRow is encountered, it will move the previous indices
     * by one and start iterating on key 0 itself (e.g. 1=>2,0=>0; 1=>2,0=>1; ...).
     * @var array TableRowDepth => Column Index
     */
    public array $tableColIndex = [];

    /**
     * Stores values of DataPlaceholders and FunctionPlaceholders:
     * [->arrow->identifier][row][col][elementIndex] => value
     * As well as data of subTables:
     * [->arrow->identifier][row][col][->sub->arrow->identifier][row]...
     * @var array
     */
    public array $tableData = [];

    /**
     * Like tableData but for each layer of TablesRows
     * $currentTableData[0] will contain data of the innermost table that is currently being iterated over.
     * @var array TableRowDepth => TableData
     */
    public array $currentTableData = [];

    public array $datIDs = [];

    /**
     * @var bool if set to false, all data placeholders are ignored and not resolved
     */
    public bool $resolveDataPlaceholders = true;

    /**
     * @var bool if set to true, all function placeholders are ignored and not resolved
     */
    public bool $noFunctionExecute = false;
    

    public abstract function getFunctionPrefix(): string;

    /**
     * Used to filter template elements to some medium, e.g.:
     * ${form: ->articles}
     * ${report: =printTable()}
     * "form" or "report" is what should be returned by getMedium()
     * @return string the medium this TemplateConfig generates html for
     */
    public abstract function getMedium(): string;

    public function getGtabid(): int
    {
        return $this->dataTabId;
    }
    
    public function getTemplateElementInstance($templateElementGtabid, $name, &$html, $id = 0, $gtabid = null, $datid = null, $recursion = 0): TemplateElement
    {
        return new TemplateElement($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public abstract function getDataPlaceholderInstance($chain, $options, $altValue);

    public function getFunctionPlaceholderInstance($name, $params): FunctionPlaceholder
    {
        return new FunctionPlaceholder($name, $params);
    }

    public function getHtmlInstance($html): Html
    {
        return new Html($html);
    }

    public function getIfPlaceholderInstance($condition, $consequent, $alternative): IfPlaceholder
    {
        return new IfPlaceholder($condition, $consequent, $alternative);
    }

    public function getSubTemplateElementPlaceholderInstance($name, $options): SubTemplateElementPlaceholder
    {
        return new SubTemplateElementPlaceholder($name, $options);
    }

    public function getTemplateGroupPlaceholderInstance($groupName, $options): TemplateGroupPlaceholder
    {
        return new TemplateGroupPlaceholder($groupName, $options);
    }

    public function getDynamicDataPlaceholderInstance($description, $options): DynamicDataPlaceholder
    {
        return new DynamicDataPlaceholder($description, $options);
    }

    public function getTableRowInstance($cells, $attributes): TableRow
    {
        return new TableRow($cells, $attributes);
    }

    public function getTableCellInstance($parts, $attributes): TableCell
    {
        return new TableCell($parts, $attributes);
    }

    public function getHeaderFooterInstance($type, $attributes, $options): HeaderFooter
    {
        return new HeaderFooter($type, $attributes, $options);
    }

    public function getBackgroundInstance($attributes, $options): Background
    {
        return new Background($attributes, $options);
    }


    public abstract function forTemporaryBaseTable($gtabid, $func);

    public abstract function resolveDataPlaceholdersForTemporaryBaseTable($baseTableID, $datid, &$placeholders);
}
