<?php

use Limbas\extra\template\base\TemplateConfig;

/**
 * Returns the current dataset ID.
 * If not in a repeated table row, the base dataset ID is returned
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function form_datid($depth=0) {
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return TemplateConfig::$instance->datIDs[$depth];
}

/**
 * Returns the current index of a table row
 * @param int $startFrom start counting from this value (default: 0, 1, 2, ...)
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function form_index($startFrom=0, $depth=0) {
    if (!$startFrom) {
        $startFrom = 0;
    } else {
        $startFrom = intval($startFrom);
    }
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return $startFrom + TemplateConfig::$instance->tableRowIndex[$depth];
}

/**
 * Returns the current column index of a table row
 * @param int $startFrom start counting from this value (default: 0, 1, 2, ...)
 * @param int $depth 0 for index of closest repeated table row, 1 for index of next-closest repeated table row, ...
 * @return int
 */
function form_colIndex($startFrom=0, $depth=0) {
    if (!$startFrom) {
        $startFrom = 0;
    } else {
        $startFrom = intval($startFrom);
    }
    if (!$depth) {
        $depth = 0;
    } else {
        $depth = intval($depth);
    }
    return $startFrom + TemplateConfig::$instance->tableColIndex[$depth];
}

/**
 * Returns current collected table row data (values of Data- and FunctionPlaceholders):
 * [->arrow->identifier][row][col][elementIndex] => value
 * Also contains data of subTables:
 * [->arrow->identifier][row][col][->sub->arrow->identifier][row]...
 * If this is called from inside a TableRow, data will only contain the data of that row.
 * If more data is needed, take a look at TemplateConfig::tableData for outer table row's data
 * @see TemplateConfig::tableData
 * @return array
 */
function form_tableRowData() {
    if (!TemplateConfig::$instance->tableRowIndex) {
        return TemplateConfig::$instance->tableData;
    } else {
        $currentRow = form_index();
        return TemplateConfig::$instance->currentTableData[0][$currentRow];
    }
}

// allow overwrite
if (!function_exists('form_pageBreak')) {
    /**
     * Breaks the current page and continues on the next one.
     * @return string
     */
    function form_pageBreak() {
        return '<hr>';
    }
}
