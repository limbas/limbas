<?php

class FormDataPlaceholder extends DataPlaceholder {

    protected $isReadOnly = true;

    // key in the gresult that corresponds to the dataset
    protected $key;

    // store reference to gresult to pass it to list/detail render functions
    protected $gresult = null;

    public function __construct($chain, $flags, $altValue) {
        if ($flags === 'writeable') {
            $this->isReadOnly = false;
        }
        parent::__construct($chain, $flags, $altValue);
    }

    public function resolve($gresultKey=null, $key=null) {
        $gresult = &$this->gresult;
        if (!$gresult and $gresultKey !== null) {
            $gresult = &TemplateConfig::$instance->gresults[$gresultKey];
        }
        if (!$gresult) {
            return false;
        }
        list($gtabid, $fieldid) = explode(';', $this->fieldlist[0]);
        if (array_key_exists($key, $gresult[$gtabid][$fieldid])) {
            $this->gresult = &$gresult;
            $this->key = $key;
            $this->setValue($gresult[$gtabid][$fieldid][$key]);
            return true;
        }

        return false; # no resolve
    }

    public function getStructure() {
        if ($this->fieldlist === null || !$this->trace) {
            return null;
        }

        $parts = explode(',', $this->trace);
        $structure = array();
        $currentStruct = &$structure;
        for ($i = 1; $i < count($parts); $i += 2) {
            $relationFieldID = $parts[$i];
            $currentStruct[$relationFieldID] = array();
            $currentStruct = &$currentStruct[$relationFieldID];
        }

        $currentStruct['placeholders'][$this->fieldlist[0]] = $this;

        return $structure;
    }

    public function getAsHtmlArr() {
        global $gtabid;
        global $gfield;

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

        require_once('gtab/gtab_type.lib');
        ob_start();

        $type = $this->isReadOnly ? 2 : 1;
        if (TemplateConfig::$instance->isListmode()) {
            $fname = 'cftyp_' . $funcid;
            $fname($this->key, $fieldid, $gtabid, $type, $this->gresult);
        } else {
            $fname = 'dftyp_' . $funcid;
            $fname($this->gresult[$gtabid]['id'][$this->key], $this->gresult, $fieldid, $gtabid, '', '', '', $type, '');
        }

        $out = ob_get_clean();
        return array('<span style="display: inline-table">' . $out . '</span>');
    }

}
