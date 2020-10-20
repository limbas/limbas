<?php

class FormDataPlaceholder extends DataPlaceholder {

    // key in the gresult that corresponds to the dataset
    protected $key;

    // store reference to gresult to pass it to list/detail render functions
    protected $gresult = null;

    public function resolve($gresultKey=null, $key=null) {
        $gresult = &$this->gresult;
        if (!$gresult and $gresultKey !== null) {
            $gresult = &TemplateConfig::$instance->gresults[$gresultKey];
        }
        if (!$gresult) {
            return false;
        }
        list($gtabid, $fieldid) = explode(';', $this->fieldlist[0]);
        if ($key == 0 || array_key_exists($key, $gresult[$gtabid][$fieldid])) {
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
            $relationGtabID = $parts[$i - 1];
            $relationFieldID = $parts[$i];
            $identifier = $relationGtabID . ',' . $relationFieldID;
            $currentStruct[$identifier] = array();
            $currentStruct = &$currentStruct[$identifier];
        }

        $currentStruct['placeholders'][$this->fieldlist[0]] = $this;

        return $structure;
    }

    public function getAsHtmlArr() {
        global $gfield;
        global $gtab;
        global $session;

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

        $datid = $this->gresult[$gtabid]['id'][$this->key];

        // check access privileges
        $readonly = false;
        if ($gtab['editrule'][$gtabid]) { # edit Permission
            $readonly = check_GtabRules($datid, $gtabid, $fieldid, $gtab['editrule'][$gtabid], $this->key, $this->gresult);
        }
        if ($gtab['has_userrules'][$gtabid] and !$readonly and !$gtab['edit_userrules'][$gtabid]) { # specific user/grouprules
            $readonly = !check_GtabUserRules($gtabid, $datid, $session['user_id'], 'edit');
        }

        // option: writeable
        if (!$readonly && (!array_key_exists('w', $this->options) || !$this->options['w'])) {
            $readonly = true;
        }

        // option: css class
        $class = 'fgtabchange';
        if (array_key_exists('class', $this->options)) {
            $class .= ' ' . $this->options['class'];
        }

        require_once('gtab/gtab_type.lib');
        ob_start();

        if (TemplateConfig::$instance->isListmode()) {
            // check edit rights
            if (!$readonly AND $gfield[$gtabid]["editrule"][$fieldid]) {
                $readonly = check_GtabRules($datid, $gtabid, $fieldid, $gfield[$gtabid]["editrule"][$fieldid], $this->key, $this->gresult);
            }

            $fname = 'cftyp_' . $funcid;
            $fname($this->key, $fieldid, $gtabid, $readonly ? 2 : 1, $this->gresult);
        } else {
            display_dftyp($this->gresult, $gtabid, $fieldid, $datid, !$readonly, $class);
        }

        $out = ob_get_clean();
        return array('<span style="display: inline-table">' . $out . '</span>');
    }

}
