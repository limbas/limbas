<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base\HtmlParts;

use Limbas\extra\template\base\TemplateConfig;
use lmb_log;

/**
 * Class TableRow
 * Corresponds to a tr
 * If the tr has attr data-lmb-data-row="->link->to->relationField", it will be repeated for all related datasets.
 * If there are Placeholders of that relation contained in the repeated row (e.g. ${->link->to->relationField->name} or
 *  ${->link->to->relationField->anotherRelationField->someField}), they will be fetched for the correct dataset (the
 *  one which is set active by the TableRow).
 */
class TableRow extends AbstractHtmlPart {

    /**
     * @var TableCell[] the tds of this tr.
     */
    protected $repeatedDataCells;

    /**
     * @var array the attributes of this tr (attrName -> attrVal).
     * For attr data-lmb-data-row, attrVal is an array representing the arrow chain.
     */
    protected $attributes;

    /**
     * @var null|DataPlaceholder the data placeholder responsible for fetching the #datasets to repeat.
     * Will have DataPlaceholder::modeFetchIDs set.
     */
    protected $relationDataPlaceholder = null;

    /**
     * @var null|int the current row index (when repeating the tr).
     */
    public $currentResultKey = null;

    /**
     * @var null|TableRow the next outer TableRow, if there is any.
     */
    public $parentTableRow = null;

    /**
     * @var DataPlaceholder[] the Placeholders that cannot be resolved in advance because they depend on a specific row
     *  of this tr.
     */
    public $dependentDataPlaceholders = array();

    /**
     * @var DataPlaceholder[] the Placeholders of fields of this relation.
     */
    public $directDataPlaceholders = array();

    public function __construct($repeatedDataCells, $attributes) {
        $this->repeatedDataCells = $repeatedDataCells;
        $this->attributes = $attributes;

        // check if is repeating TableRow
        if (!array_key_exists('data-lmb-data-row', $attributes)) {
            return;
        }

        // store filter
        $dataRowFilter = null;
        if (array_key_exists('data-lmb-data-row-filter', $attributes) && $attributes['data-lmb-data-row-filter'] instanceof FunctionPlaceholder) {
            $dataRowFilter = $attributes['data-lmb-data-row-filter'];
            unset($this->attributes['data-lmb-data-row-filter']);
        }

        // create relationDataPlaceholder (placeholder fetching #datasets this relation has)
        $relationFieldChain = $attributes['data-lmb-data-row'];
        $this->relationDataPlaceholder = TemplateConfig::$instance->getDataPlaceholderInstance($relationFieldChain, array(), null);
        $this->relationDataPlaceholder->setModeFetchIDs($this, $dataRowFilter);
        unset($attributes['data-lmb-data-row']);

        // find sub-TableRows
        $subTableRows = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getTableRows();
        }, $this->repeatedDataCells));
        foreach ($subTableRows as &$subTableRow) {
            // already has parent
            if ($subTableRow->parentTableRow !== null) {
                continue;
            }

            // check inner repeating base table
            if ($this->relationDataPlaceholder->isModeFetchBaseTable() && $subTableRow->relationDataPlaceholder->isModeFetchBaseTable()) {
                lmb_log::error("Listform table rows must not be contained in each other!", "Listform table rows must not be contained in each other!");
            }

            // sub table row not dependent on this table row?
            if (!$subTableRow->relationDataPlaceholder->startsWith($this->relationDataPlaceholder)) {
                continue;
            }

            // set dependent on this table row('s value)
            $subTableRow->parentTableRow = &$this;
            $subTableRow->getRelationDataPlaceholder()->setDependentFrom($this);
            $this->dependentDataPlaceholders[] = $subTableRow->getRelationDataPlaceholder();

            // if the subTableRow contains fields directly of its relation, these can be fetched when fetching the
            // number of datasets the relation has. This is done in this TableRow -> move dependencies to me.
            foreach ($subTableRow->directDataPlaceholders as &$p) {
                $p->setDependentFrom($this, $subTableRow);
                $this->dependentDataPlaceholders[] = &$p;
            }
            $subTableRow->directDataPlaceholders = array();
        }

        // get data placeholders that depend on this tr's id
        $childDataPlaceholders = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $this->repeatedDataCells));
        foreach ($childDataPlaceholders as &$placeholder) {
            // already dependent on some (other) TableRow
            if ($placeholder->isDependent() || $placeholder->isModeFetchArr()) {
                continue;
            }

            // depends on this relation?
            if ($placeholder->startsWith($this->relationDataPlaceholder)) {
                if ($placeholder->isRelationFieldFrom($this->relationDataPlaceholder)) {
                    $placeholder->setModeFetchArr($this);
                    $this->directDataPlaceholders[] = &$placeholder;
                } else {
                    $placeholder->setDependentFrom($this);
                    $this->dependentDataPlaceholders[] = &$placeholder;
                }
            }
        }
    }

    public function getAsHtmlArr(): array
    {
        global $gfield;

        // get target #rows
        $numRows = 1;
        if ($this->relationDataPlaceholder) {
            $numRows = $this->relationDataPlaceholder->getValueCount();
        }

        // convert attributes to strings
        $attributesStrArr = array();
        foreach ($this->attributes as $key => $val) {
            $attributesStrArr[] = "{$key}=\"{$val}\"";
        }

        // iterate over all rows
        $allTrsHtmlArr = array();

        // new "layer" of repeated rows
        if ($this->relationDataPlaceholder) {
            array_unshift(TemplateConfig::$instance->currentTableData, array());
            array_unshift(TemplateConfig::$instance->tableRowIndex, 0);
            array_unshift(TemplateConfig::$instance->tableColIndex, 0);
            array_unshift(TemplateConfig::$instance->datIDs, 0);
        }
        for ($i = 0; $i < $numRows; $i++) {
            $this->currentResultKey = $i;

            // set current row
            if ($this->relationDataPlaceholder) {
                array_shift(TemplateConfig::$instance->tableRowIndex);
                array_unshift(TemplateConfig::$instance->tableRowIndex, $i);
                array_shift(TemplateConfig::$instance->datIDs);
                array_unshift(TemplateConfig::$instance->datIDs, $this->getCurrentDatid());
            }

            if ($this->dependentDataPlaceholders) {
                // reset data
                foreach ($this->dependentDataPlaceholders as &$p) {
                    $p->resetValue();
                }

                // fetch data
                if ($this->relationDataPlaceholder->isModeFetchBaseTable()) {
                    $verknTabID = TemplateConfig::$instance->getGtabid(); // dont change
                } else {
                    $fieldIdentifier = explode(';', $this->relationDataPlaceholder->getFieldlist()[0]);
                    $tabID = $fieldIdentifier[0];
                    $fieldID = $fieldIdentifier[1];
                    $verknTabID = $gfield[$tabID]['verkntabid'][$fieldID];
                }
                TemplateConfig::$instance->resolveDataPlaceholdersForTemporaryBaseTable($verknTabID, $this->getCurrentDatid(), $this->dependentDataPlaceholders);
            }

            // get html of tds
            $repeatedDataCellsCount = 0;
            if (is_array($this->repeatedDataCells)) {
                $repeatedDataCellsCount = lmb_count($this->repeatedDataCells);
            }
            $dataCellsHtmlArr = array();
            for ($u = 0; $u < $repeatedDataCellsCount; $u++) {
                // set current col
                if ($this->relationDataPlaceholder) {
                    array_shift(TemplateConfig::$instance->tableColIndex);
                    array_unshift(TemplateConfig::$instance->tableColIndex, $u);
                }
                $dataCellsHtmlArr[] = $this->repeatedDataCells[$u]->getAsHtmlArr();
            }
            $dataCellsHtmlArr = array_merge(...$dataCellsHtmlArr);

            // add opening <tr>
            array_push($allTrsHtmlArr, '<tr ', join(' ', $attributesStrArr), '>');

            // add children
            array_push($allTrsHtmlArr, ...$dataCellsHtmlArr);

            // add closing </tr>
            array_push($allTrsHtmlArr, '</tr>');
        }
        // remove "layer" of this table row
        if ($this->relationDataPlaceholder) {
            array_shift(TemplateConfig::$instance->tableColIndex);
            array_shift(TemplateConfig::$instance->tableRowIndex);
            array_shift(TemplateConfig::$instance->datIDs);

            // outermost table?
            $tableData = array_shift(TemplateConfig::$instance->currentTableData);
            if (!TemplateConfig::$instance->currentTableData) {
                // ->store in global tableData
                TemplateConfig::$instance->tableData[$this->relationDataPlaceholder->getFullMatch()] = $tableData;
            } else {
                // ->store in next outer tableData
                $row = TemplateConfig::$instance->tableRowIndex[0];
                $col = TemplateConfig::$instance->tableColIndex[0];
                $id = $this->relationDataPlaceholder->getFullMatch();
                TemplateConfig::$instance->currentTableData[0][$row][$col][$id] = $tableData;
            }
        }

        return $allTrsHtmlArr;
    }

    public function getUnresolvedDataPlaceholders() {
        $childDataPlaceholders = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $this->repeatedDataCells));

        // not repeated  -> return all child placeholders
        if (!$this->relationDataPlaceholder) {
            return $childDataPlaceholders;
        }

        $unresolvedPlaceholders = $this->relationDataPlaceholder->getUnresolvedDataPlaceholders();
        foreach ($childDataPlaceholders as &$placeholder) {
            // ignore dependent data placeholders, they will be resolved from their TableRow
            if ($placeholder->isDependent()) {
                continue; // these will be fetched for each row
            }
            array_push($unresolvedPlaceholders, ...$placeholder->getUnresolvedDataPlaceholders());
        }

        return $unresolvedPlaceholders;
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $this->repeatedDataCells));
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedTemplateGroupPlaceholders();
        }, $this->repeatedDataCells));
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDynamicDataPlaceholders();
        }, $this->repeatedDataCells));
    }

    public function getAllDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAllDynamicDataPlaceholders();
        }, $this->repeatedDataCells));
    }

    public function getTableRows() {
        $childTemplateRows = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getTableRows();
        }, $this->repeatedDataCells));
        if ($this->relationDataPlaceholder) {
            $childTemplateRows[] = $this;
        }
        return $childTemplateRows;
    }

    public function getCurrentDatid() {
        return $this->relationDataPlaceholder->getValue();
    }

    public function getRelationDataPlaceholder() {
        return $this->relationDataPlaceholder;
    }

    public function getCells() {
        return $this->repeatedDataCells;
    }

    public function getDescription() {
        return $this->relationDataPlaceholder
            ? $this->relationDataPlaceholder->getFullMatch()
            : null;
    }

    public function isRepeated() {
        return !!$this->relationDataPlaceholder;
    }

}
