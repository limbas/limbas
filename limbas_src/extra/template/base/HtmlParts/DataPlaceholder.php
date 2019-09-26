<?php

/**
 * Class DataPlaceholder
 * A placeholder for a field of a dataset
 *
 * Syntax:
 *  ${->Field} where 'Field' is the Database-Fieldname of the dataset for which the template is being resolved
 *  ${->Field|DefaultValue} where 'DefaultValue' is inserted if the content of 'Field' is empty
 *  ${->RelationField->Field} for relations
 */
abstract class DataPlaceholder extends AbstractHtmlPart {

    /**
     * @var array limbas fieldlist in format needed by get_dataArray()
     * @see get_dataArray()
     */
    protected $fieldlist;

    /**
     * @var string the trace of the target table, used for grouping requests: (gtabid,fieldid,)+
     */
    protected $trace = null;

    /**
     * @var string the value from the database, if set
     */
    protected $value = null;

    /**
     * @var bool false, if the placeholder still needs to be resolved using the db
     */
    protected $isResolved = false;

    /**
     * @var string full placeholder match. Used for error handling
     */
    protected $fullMatch;

    public function __construct($fieldNames, $flags, $altValue) {
        global $gfield;

        $this->fullMatch = '->' . implode('->', $fieldNames);

        # store alternative value if given
        if ($altValue) {
            $this->value = $altValue;
        }

        # resolve field trace
        $numParts = count($fieldNames);

        $this->fieldlist = array();
        $trace = array();
        $currentGtabid = TemplateConfig::$instance->getGtabid();
        $fieldID = null;
        for ($i = 0; $i < $numParts; $i++) {
            $trace[] = $currentGtabid;

            # resolve field name -> id
            $fieldID = $gfield[$currentGtabid]['argresult_name'][lmb_strtoupper($fieldNames[$i])];
            if ($fieldID === null) {
                $this->fieldlist = null;
                $this->setValue('');
                return;
            }

            if ($i == $numParts - 1) { # last element
                $this->fieldlist[0] = $currentGtabid . ';' . $fieldID;
            } else { # relation
                $verknGtabid = $gfield[$currentGtabid]['verkntabid'][$fieldID];
                if ($verknGtabid === null) {
                    $this->fieldlist = null;
                    $this->setValue('');
                    return;
                }
                $this->fieldlist[1] .= '|' . $currentGtabid . ';' . $verknGtabid . ';' . $fieldID;

                $trace[] = $fieldID;
                $currentGtabid = $verknGtabid;
            }
        }

        # no permission?
        if(!$gfield[$currentGtabid]['sort'][$fieldID]){
            lmb_log::error("No permission in data placeholder {$this->fullMatch}!", 'No permission for field!', $currentGtabid, $fieldID);
            $this->fieldlist = null;
            $this->setValue('');
            return;
        }

        $this->trace = implode(',', $trace);
    }

    public function getAsHtmlArr() {
        if (!$this->isResolved or $this->fieldlist === null) {
            lmb_log::error("Data placeholder {$this->fullMatch} could not be resolved!", 'Not all data placeholders could be resolved!');
        }
        if ($this->value === null) {
            return array('');
        }
        return array($this->value);
    }

    public function getUnresolvedDataPlaceholders() {
        if ($this->isResolved || $this->resolve()) {
            return array();
        }

        return array($this);
    }

    protected abstract function resolve();

    public function getFieldlist() {
        return $this->fieldlist;
    }

    public function getTrace() {
        return $this->trace;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->isResolved = true;
        if ($value) {
            $this->value = $value;
        }
    }

    public function isResolved() {
        return $this->isResolved;
    }

}