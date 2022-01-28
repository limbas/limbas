<?php

require_once(__DIR__ . '/TemplateConfigInterface.php');
require_once(__DIR__ . '/DynamicDataUnresolvedException.php');
require_once(__DIR__ . '/TemplateElement.php');
require_once(__DIR__ . '/TemplateGroupUnresolvedException.php');
require_once(__DIR__ . '/HtmlParts/AbstractHtmlPart.php');
require_once(__DIR__ . '/HtmlParts/DataPlaceholder.php');
require_once(__DIR__ . '/HtmlParts/DynamicDataPlaceholder.php');
require_once(__DIR__ . '/HtmlParts/FunctionPlaceholder.php');
require_once(__DIR__ . '/HtmlParts/Html.php');
require_once(__DIR__ . '/HtmlParts/IfPlaceholder.php');
require_once(__DIR__ . '/HtmlParts/SubTemplateElementPlaceholder.php');
require_once(__DIR__ . '/HtmlParts/TableRow.php');
require_once(__DIR__ . '/HtmlParts/TableCell.php');
require_once(__DIR__ . '/HtmlParts/TemplateGroupPlaceholder.php');

abstract class TemplateConfig implements TemplateConfigInterface {

    /**
     * @var TemplateConfig
     */
    public static $instance = null;

    /**
     * Used to resolve TemplateGroups to the corresponding TemplateElement
     * @var array of key => value
     * where key is an identifier returned by TemplateGroupPlaceholder::getIdentifier()
     * and value is the name of a valid TemplateElement
     */
    public $resolvedTemplateGroups = array();

    /**
     * Used to resolve DynamicDataPlaceholders to their corresponding value
     * @var array of key => value
     * where key is an identifier returned by DynamicDataPlaceholder::getIdentifier()
     * and value is an (html) string
     */
    public $resolvedDynamicData = array();

    /**
     * Used to retrieve the current index of each TableRow. The innermost TableRow will have key 0 and set the value to
     * its index (e.g. 0=>0; 0=>1; 0=>2; ...). If "innermore" TableRow is encountered, it will move the previous indices
     * by one and start iterating on key 0 itself (e.g. 1=>2,0=>0; 1=>2,0=>1; ...).
     * @var array TableRowDepth => Row Index
     */
    public $tableRowIndex = array();

    /**
     * Used to retrieve the current column index of each TableRow. The innermost TableRow will have key 0 and set the value to
     * its colum index (e.g. 0=>0; 0=>1; 0=>2; ...). If "innermore" TableRow is encountered, it will move the previous indices
     * by one and start iterating on key 0 itself (e.g. 1=>2,0=>0; 1=>2,0=>1; ...).
     * @var array TableRowDepth => Column Index
     */
    public $tableColIndex = array();

    /**
     * Stores values of DataPlaceholders and FunctionPlaceholders:
     * [->arrow->identifier][row][col][elementIndex] => value
     * As well as data of subTables:
     * [->arrow->identifier][row][col][->sub->arrow->identifier][row]...
     * @var array
     */
    public $tableData = array();

    /**
     * Like tableData but for each layer of TablesRows
     * $currentTableData[0] will contain data of the innermost table that is currently being iterated over.
     * @var array TableRowDepth => TableData
     */
    public $currentTableData = array();

    public $datIDs = array();

    /**
     * @var bool if set to false, all data placeholders are ignored and not resolved
     */
    public $resolveDataPlaceholders = true;

    /**
     * @var bool if set to true, all function placeholders are ignored and not resolved
     */
    public $noFunctionExecute = false;
    

    public abstract function getGtabid();

    public abstract function getFunctionPrefix();

    /**
     * Used to filter template elements to some medium, e.g.:
     * ${form: ->articles}
     * ${report: =printTable()}
     * "form" or "report" is what should be returned by getMedium()
     * @return string the medium this TemplateConfig generates html for
     */
    public abstract function getMedium();

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html, $id = 0, $gtabid = null, $datid = null, $recursion = 0) {
        return new TemplateElement($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public abstract function getDataPlaceholderInstance($chain, $options, $altValue);

    public function getFunctionPlaceholderInstance($name, $params) {
        return new FunctionPlaceholder($name, $params);
    }

    public function getHtmlInstance($html) {
        return new Html($html);
    }

    public function getIfPlaceholderInstance($condition, $consequent, $alternative) {
        return new IfPlaceholder($condition, $consequent, $alternative);
    }

    public function getSubTemplateElementPlaceholderInstance($name, $options) {
        return new SubTemplateElementPlaceholder($name, $options);
    }

    public function getTemplateGroupPlaceholderInstance($groupName, $options) {
        return new TemplateGroupPlaceholder($groupName, $options);
    }

    public function getDynamicDataPlaceholderInstance($description, $options) {
        return new DynamicDataPlaceholder($description, $options);
    }

    public function getTableRowInstance($cells, $attributes) {
        return new TableRow($cells, $attributes);
    }

    public function getTableCellInstance($parts, $attributes) {
        return new TableCell($parts, $attributes);
    }

    public abstract function forTemporaryBaseTable($gtabid, $func);

    public abstract function resolveDataPlaceholdersForTemporaryBaseTable($baseTableID, $datid, &$placeholders);
}
