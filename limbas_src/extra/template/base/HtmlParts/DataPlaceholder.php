<?php

/**
 * Class DataPlaceholder
 * A placeholder for a field of a dataset
 *
 * Syntax:
 *  ${->Field} where 'Field' is the Database-Fieldname of the dataset for which the template is being resolved
 *  ${=>CurrentTable->Field}
 *  ${=>1:1RelationTable->Field}
 *  ${->Field|DefaultValue} where 'DefaultValue' is inserted if the content of 'Field' is empty
 *  ${->RelationField->Field} for relations
 *  ${->Field[w]} in form for editable input field
 *  ${->Field[class=my-class test-class input]}
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
     * @var string|array the value from the database, if set. Is array if $modeFetchArr is set
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

    /**
     * @var array options passed to placeholder: ${->test[w][class=test]} => array('w' => true, 'class' => 'test')
     */
    protected $options;

    /**
     * Only for data placeholders that belong to a TableRow
     * @var bool true if this relation data placeholder fetches only related IDs, not any actual field.
     * @see DataPlaceholder::setModeFetchIDs()
     * @see TableRow::$relationDataPlaceholder
     */
    protected $modeFetchIDs = false;

    /**
     * Function that returns a filter for the gresult, only for modeFetchIDS
     * @var FunctionPlaceholder|null
     */
    protected $dataRowFilter = null;

    /**
     * @var bool true if this data placeholder takes values for all result datasets, not just one.
     * @see DataPlaceholder::setModeFetchArr()
     */
    protected $modeFetchArr = false;

    /**
     * Only if modeFetchIDs or modeFetchArr is set.
     * @var TableRow|null the table row that specifies from which index of this placeholder's value array the current
     * value should be taken.
     * @see TableRow::$currentResultKey
     */
    protected $indexTableRow = null;

    /**
     * @var bool true if this placeholder's data source is dependent from a TableRow's state (index).
     * @see DataPlaceholder::setDependentFrom()
     */
    protected $modeDependent = false;

    /**
     * If a table row should iterate over datasets of the base table directly (list form), this is set to true.
     * The fieldlist then is empty and the trace just contains the table ID.
     * @var bool
     */
    protected $modeFetchBaseTable = false;


    /**
     * @var bool if set to true the placeholder is not resolved
     */
    protected $noResolve = false;
    

    public function __construct($fieldIdentifiers, $options, $altValue, $noResolve) {
        global $gfield, $gtab;
        
        $this->noResolve = $noResolve;
        
        if ($this->noResolve) {
            return;
        }

        $this->options = $options;
        $currentGtabid = TemplateConfig::$instance->getGtabid();

        // mode fetch base table?
        if (count($fieldIdentifiers) === 1 && $fieldIdentifiers[0]['table'] && !$fieldIdentifiers[0]['name']) {
            $tableName = $fieldIdentifiers[0]['table'];
            $tableID = $gtab['argresult_id'][lmb_strtoupper($tableName)];
            if ($tableID != $currentGtabid) {
                lmb_log::error(
                    "Given table '$tableName' ($tableID) doesn't match base table {$gtab['table'][$currentGtabid]} ($currentGtabid)!",
                    'Repeating table row: Given table doesn\'t match report/form base table!',
                    $currentGtabid
                );
            }
            $this->fullMatch = '=>' . $tableName;
            $this->modeFetchBaseTable = true;
            $this->fieldlist = array();
            $this->trace = "{$currentGtabid}";
            return;
        }

        $fieldIdentifierStrs = array_map(function($identifier) {
            $str = '';
            if (array_key_exists('table', $identifier)) {
                $str = '=>' . $identifier['table'];
            }
            $str .= '->' . $identifier['name'];
            return $str;
        }, $fieldIdentifiers);
        $this->fullMatch = implode('', $fieldIdentifierStrs);

        # store alternative value if given
        if ($altValue) {
            $this->value = $altValue;
        }

        # resolve field trace
        $numParts = count($fieldIdentifiers);

        $this->fieldlist = array();
        $trace = array();
        $fieldID = null;
        for ($i = 0; $i < $numParts; $i++) {
            # resolve table
            if (array_key_exists('table', $fieldIdentifiers[$i])) {
                $tableName = $fieldIdentifiers[$i]['table'];
                $tableID = $gtab['argresult_id'][lmb_strtoupper($tableName)];
                if ($tableID != $currentGtabid) { // specifying current table is allowed as hint to developers
                    $relationTableID = $gtab['verkn'][$tableID];

                    // not current table & not 1:1 relation table -> not allowed
                    if ($currentGtabid != $relationTableID) {
                        lmb_log::error("Invalid table {$tableName} in placeholder {$this->fullMatch}!", "Invalid table {$tableName}!", $currentGtabid);
                        $this->fieldlist = null;
                        $this->setValue('');
                        return;
                    }

                    $currentGtabid = $tableID;
                }
            }

            $trace[] = $currentGtabid;

            # resolve field name -> id
            $fieldID = $gfield[$currentGtabid]['argresult_name'][lmb_strtoupper($fieldIdentifiers[$i]['name'])];
            if ($fieldID === null) {
                lmb_log::error("Invalid field {$fieldIdentifiers[$i]['name']} in placeholder {$this->fullMatch}!", "Invalid field {$fieldIdentifiers[$i]['name']}!", $currentGtabid);
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
        // if i fetch the base table, I must not be dependent on another table row
        if ($this->isModeFetchBaseTable() && $this->isDependent()) {
            lmb_log::error(
                "Usage of table row repeating on base table is only allowed in list forms!",
                "Usage of table row repeating on base table is only allowed in list forms!",
                TemplateConfig::$instance->getGtabid()
            );
        }

        if (!$this->isResolved or $this->fieldlist === null) {
            lmb_log::error("Data placeholder {$this->fullMatch} could not be resolved!", 'Not all data placeholders could be resolved!');
        }

        $value = $this->getValue();

        // store result for user-calculations
        $tc = TemplateConfig::$instance;
        if ($tc->tableRowIndex) {
            $tc->currentTableData[0][$tc->tableRowIndex[0]][$tc->tableColIndex[0]][] = $value;
        }

        if ($value === null) {
            $value = '';
        }
        return array($value);
    }

    public function getUnresolvedDataPlaceholders() {
        if ($this->isResolved || $this->resolveInner()) {
            return array();
        }

        return array($this);
    }

    protected function resolveInner() {
        if ($this->options['show'] === 'description') { // show only description
            global $gfield;
            list($gtabid, $fieldid) = $this->getTabAndField();
            $this->setValue($gfield[$gtabid]['spelling'][$fieldid]);
            return true;
        } else if ($this->options['show'] === 'title') { // show only title
            global $gfield;
            list($gtabid, $fieldid) = $this->getTabAndField();
            $this->setValue($gfield[$gtabid]['beschreibung'][$fieldid]);
            return true;
        }

        // load data
        return $this->resolve();
    }

    /**
     * @param DataPlaceholder $relationDataPlaceholder
     * @return bool if this DataPlaceholder contains the given relation
     * @example '->Kunde->Kontakte->Name' startsWith '->Kunde'
     */
    public function startsWith($relationDataPlaceholder) {
        $thisTrace = $this->trace . ',';
        $otherTrace = $relationDataPlaceholder->trace . ',';
        return substr($thisTrace, 0, strlen($otherTrace)) === $otherTrace;
    }

    /**
     * @param DataPlaceholder $relationDataPlaceholder
     * @return bool if this DataPlaceholder is a direct field from the given relation
     * @example '->Kunde->Kontakte->Name' isRelationFieldFrom '->Kunde->Kontakte' but not from '->Kunde'
     */
    public function isRelationFieldFrom($relationDataPlaceholder) {
        return $relationDataPlaceholder->trace === $this->trace;
    }

    /**
     * Loads the required data
     * @return mixed
     */
    protected abstract function resolve();

    public function getFieldlist() {
        return $this->fieldlist;
    }

    public function getTrace() {
        return $this->trace;
    }

    public function getValue() {
        // get correct entry from value array
        if ($this->isModeFetchArr() || $this->isModeFetchIDs()) {
            return $this->value[$this->indexTableRow->currentResultKey];
        }
        return $this->value;
    }

    /**
     * Only if modeFetchArr or modeFetchIDs is set
     * @return int the number of datasets of this Data Placeholder
     */
    public function getValueCount() {
        if ($this->isModeFetchArr() || $this->isModeFetchIDs()) {
            return count($this->value);
        }
        return 1;
    }

    public function setValue($value) {
        $this->isResolved = true;
        if ($value) {
            $this->value = $value;
        }
    }

    /**
     * Used in TableRow. Discards this Data Placeholder's set value. When a TableRow is repeated, all previously fetched
     * values must be discarded.
     */
    public function resetValue() {
        $this->isResolved = false;
        $this->value = null;
    }

    public function isResolved() {
        return $this->isResolved;
    }

    public function getTabAndField() {
        if ($this->fieldlist === null) {
            return array(null, null);
        }
        return explode(';', $this->fieldlist[0]);
    }

    public function getFullMatch() {
        return $this->fullMatch;
    }

    /**
     * Sets this relation data placeholder's mode to fetch IDs of related datasets
     * @param TableRow $forTableRow
     * @param FunctionPlaceholder|null $dataRowFilter
     * @see TableRow::$relationDataPlaceholder
     */
    public function setModeFetchIDs(&$forTableRow, $dataRowFilter=null) {
        global $gfield;

        // already ajdusted
        if ($this->modeFetchIDs) {
            return;
        }
        $this->modeFetchIDs = true;
        $this->indexTableRow = $forTableRow;
        $this->dataRowFilter = $dataRowFilter;

        // modify trace to resemble fetching the ID field
        if (!$this->isModeFetchBaseTable()) {
            $fieldIdentifier = explode(';', $this->fieldlist[0]);
            $tabID = $fieldIdentifier[0];
            $fieldID = $fieldIdentifier[1];
            $verknTabID = $gfield[$tabID]['verkntabid'][$fieldID];
            $this->trace .= ',' . $fieldID . ',' . $verknTabID;
        }
    }

    public function isModeFetchIDs() {
        return $this->modeFetchIDs;
    }

    public function getGsr() {
        if (!$this->dataRowFilter) {
            return array();
        }
        $result = $this->dataRowFilter->result();
        if (array_key_exists('gsr', $result)) {
            return $result['gsr'];
        }
        return array();
    }

    public function getFilter() {
        if (!$this->dataRowFilter) {
            return array();
        }
        $result = $this->dataRowFilter->result();
        if (array_key_exists('filter', $result)) {
            return $result['filter'];
        }
        return array();
    }

    public function getExtension() {
        if (!$this->dataRowFilter) {
            return array();
        }
        $result = $this->dataRowFilter->result();
        if (array_key_exists('extension', $result)) {
            return $result['extension'];
        }
        return array();
    }

    /**
     * Sets this data placeholder's mode to fetch array of values for all available datasets instead of just the first.
     * By doing this, all related fields of a TableRow will be loaded in one query instead of one query per row.
     * @param TableRow $forTableRow
     */
    public function setModeFetchArr(&$forTableRow) {
        $this->modeFetchArr = true;
        $this->indexTableRow = $forTableRow;
    }

    public function isModeFetchArr() {
        return $this->modeFetchArr;
    }

    /**
     * This data placeholder can now only be resolved when the given tableRow fixes their ID.
     * E.g. if a TableRow over ->Kunden exists, then ->Kunden->Kontakte->Name must be queried for each ->Kunden.
     * Therefore ->Kunden->Kontakte->Name is dependent on ->Kunden and will be modified to just be ->Kontakte->Name.
     * This way, the TableRow can query it directly.
     * @param TableRow $tableRow
     */
    public function setDependentFrom(&$tableRow) {
        $placeholder = $tableRow->getRelationDataPlaceholder();
        if (!$placeholder->isModeFetchBaseTable()) {
            $otherFieldlistLen = count(explode('|', $placeholder->getFieldlist()[1]));
            $fieldlistArr = explode('|', $this->fieldlist[1]);
            array_splice($fieldlistArr, 0, $otherFieldlistLen + 1);
            $this->fieldlist[1] = implode('|', $fieldlistArr);

            $parentTraceLength = count(explode(',', $placeholder->getTrace()));
            $reducedTrace = array_splice(explode(',', $this->trace), $parentTraceLength - 1);
            $this->trace = implode(',', $reducedTrace);
        }
        $this->modeDependent = true;
    }

    public function isDependent() {
        return $this->modeDependent;
    }

    public function isModeFetchBaseTable() {
        return $this->modeFetchBaseTable;
    }

}
