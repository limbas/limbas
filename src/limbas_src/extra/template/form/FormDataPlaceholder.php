<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\form;

use Limbas\extra\template\base\HtmlParts\DataPlaceholder;
use Limbas\extra\template\base\TemplateConfig;
use lmb_log;

class FormDataPlaceholder extends DataPlaceholder
{

    // key in the gresult that corresponds to the dataset
    protected $key;

    // store reference to gresult to pass it to list/detail render functions
    protected $gresult = null;

    protected bool $readOnly = false;

    public function resolve($gresultKey = null, $key = null)
    {
        global $gfield;

        if ($this->noResolve) {
            return true;
        }

        $gresult = &$this->gresult;
        if (!$gresult and $gresultKey !== null) {
            $gresult = &TemplateConfig::$instance->gresults[$gresultKey];
        }
        if (!$gresult) {
            return false;
        }

        list($gtabid, $fieldid) = explode(';', $this->fieldlist[0]);

        // fetch IDs of base table
        if ($this->isModeFetchBaseTable()) {
            $this->gresult = &$gresult;
            $this->key = null;
            $tabID = intval($this->trace);
            $this->setValue($gresult[$tabID]['id']);
        }

        // fetch IDs
        if ($this->isModeFetchIDs()) {
            $verknGtabid = $gfield[$gtabid]['verkntabid'][$fieldid];
            $this->gresult = &$gresult;
            $this->key = null;
            $this->setValue($gresult[$verknGtabid]['id']);
            return true;
        }

        // fetch whole array
        if ($this->isModeFetchArr()) {
            $this->gresult = &$gresult;
            $this->key = null;
            $this->setValue($gresult[$gtabid][$fieldid]);
            return true;
        }

        // fetch single dataset
        if ($key == 0 || array_key_exists($key, $gresult[$gtabid][$fieldid])) {
            $this->gresult = &$gresult;
            $this->key = $key;
            $this->setValue($gresult[$gtabid][$fieldid][$key]);
            return true;
        }

        return false; # no resolve
    }

    public function resetValue()
    {
        parent::resetValue();
        $this->key = null;
        $this->gresult = null;
    }

    public function getStructure()
    {
        if ($this->fieldlist === null || !$this->trace) {
            return null;
        }

        if ($this->isModeFetchBaseTable()) {
            $currentStruct = array();
            $tabID = intval($this->getTrace());
            $currentStruct['placeholders']["$tabID;ID"] = $this;
            return $currentStruct;
        }

        $parts = explode(',', $this->trace);
        $structure = array();
        $currentStruct = &$structure;

        $partCount = 0;
        if (is_array($parts)) {
            $partCount = lmb_count($parts);
        }

        for ($i = 1; $i < $partCount; $i += 2) {
            $relationGtabID = $parts[$i - 1];
            $relationFieldID = $parts[$i];
            $identifier = $relationGtabID . ',' . $relationFieldID;
            $currentStruct[$identifier] = array();
            $currentStruct = &$currentStruct[$identifier];
        }

        $currentStruct['placeholders'][$this->fieldlist[0]] = $this;

        return $structure;
    }

    public function getAsHtmlArr(): array
    {
        global $gfield;

        if ($this->isModeFetchArr()) {
            $this->key = $this->indexTableRow->currentResultKey;
        }

        if ($this->fieldlist === null) {
            lmb_log::error("Data placeholder {$this->fullMatch} could not be resolved!", 'Not all data placeholders could be resolved!');
            return array();
        }

        list($gtabid, $fieldid) = explode(';', $this->fieldlist[0]);
        $funcid = $gfield[$gtabid]['funcid'][$fieldid];
        if (is_null($funcid)) {
            lmb_log::error("Data placeholder {$this->fullMatch} could not be resolved! Function not found!", 'Not all data placeholders could be resolved!');
            return array();
        }
        
        if($this->isLabel) {
            return array('<label>' . $this->value . '</label>');
        }
        

        $datid = $this->gresult[$gtabid]['id'][$this->key];

        // check edit rights
        if (!$this->readOnly and $gfield[$gtabid]["editrule"][$fieldid]) {
            $this->readOnly = check_GtabRules($datid, $gtabid, $fieldid, $gfield[$gtabid]["editrule"][$fieldid], $this->key, $this->gresult);
        }
        // option: writeable
        if (!$this->readOnly && (!array_key_exists('w', $this->options) || !$this->options['w'])) {
            $this->readOnly = true;
        }

        // option: css class
        $class = 'fgtabchange';
        if (array_key_exists('class', $this->options)) {
            $class .= ' ' . $this->options['class'];
        }

        // store result for user-calculations
        $tc = TemplateConfig::$instance;
        if ($tc->tableRowIndex) {
            $tc->currentTableData[0][$tc->tableRowIndex[0]][$tc->tableColIndex[0]][] = $this->gresult[$gtabid][$fieldid][$this->key];
        }

        require_once(COREPATH . 'gtab/gtab_type.lib');
        ob_start();

        if ($this->isModeFetchArr() || $this->isDependent() || TemplateConfig::$instance->isListmode()) {
            $fname = 'cftyp_' . $funcid;
            $fname($this->key, $fieldid, $gtabid, $this->readOnly ? 2 : 1, $this->gresult);
        } else {
            display_dftyp($this->gresult, $gtabid, $fieldid, $datid, !$this->readOnly, $class, $null, $null, $null, $null, $this->options);
        }

        $out = ob_get_clean();
        return array('<span style="display: inline-table">' . $out . '</span>');
    }

    public function setReadOnly(): void
    {
        $this->readOnly = true;
    }

}
