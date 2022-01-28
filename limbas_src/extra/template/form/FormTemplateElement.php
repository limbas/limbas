<?php

class FormTemplateElement extends TemplateElement {

    public function __construct($templateElementGtabid, $name, $html, $id = 0, $gtabid = null, $datid = null, $recursion = 0) {
        if (lmb_substr($html, 0, 3) === '<p>' and lmb_substr($html, -4) === '</p>') {
            $html = lmb_substr($html, 3, -4);
        }
        parent::__construct($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public function resolve($datid, &$gresult=null) {
        if (!$gresult) {
            global $gresult;
        }
        do {
            $successData = $this->resolveDataPlaceholders($datid, $gresult);
            $successTpl = $this->resolveTemplates();
        } while ($successTpl || $successData);
    }

    /**
     * Resolves passed data placeholders for the base table set in the TemplateConfig and the given datid.
     * Used to query subtables in TableRow.
     * @param $datid
     * @param $dataPlaceholders
     * @return bool
     */
    public static function resolveDataPlaceholdersForTable($datid, $dataPlaceholders) {
        # group by trace
        $placeholdersByStructure = array();
        foreach ($dataPlaceholders as &$placeholder) {
            $struct = $placeholder->getStructure();
            $placeholdersByStructure = array_replace_recursive($placeholdersByStructure, $struct);
        }

        # resolve first-level placeholders
        if ($placeholdersByStructure['placeholders']) {
            # check which fields to request
            $missingFieldIDs = array();
            $missingPlaceholders = array();
            $gsr = array();
            $filter = array();
            $extension = array();
            foreach ($placeholdersByStructure['placeholders'] as $key => &$placeholder) {
                if ($placeholder->isModeFetchIDs()) {
                    $gsr = $placeholder->getGsr();
                    $filter = $placeholder->getFilter();
                    $extension = $placeholder->getExtension();
                }
                // note that $tabID can differ from $gtabid in case a 1:1 relation table is requested
                list($tabID, $fieldID) = explode(';', $key);
                $missingFieldIDs[intval($tabID)][] = intval($fieldID);
                $missingPlaceholders[intval($tabID)][] = &$placeholder;
            }

            # fetch data and store in gresult
            $tabIdReadonly = array();
            foreach ($missingFieldIDs as $tabID => $fieldIDs) {
                TemplateConfig::$instance->gresults[] = get_gresult($tabID, 1, $filter, $gsr, null, array($tabID => $fieldIDs), $datid, $extension);
                $subGresultKey = count(TemplateConfig::$instance->gresults) - 1;
                $subGresult = &TemplateConfig::$instance->gresults[$subGresultKey];

                # check edit permission
                if (!array_key_exists($tabID, $tabIdReadonly)) {
                    $tabIdReadonly[$tabID] = check_DataPermission($tabID, $datid, $subGresult);
                }
                if ($tabIdReadonly[$tabID]) {
                    foreach ($missingPlaceholders[$tabID] as &$placeholder) {
                        $placeholder->setReadOnly();
                    }
                }

                # no dataset
                if ($subGresult[$tabID]['res_count'] == 0) {
                    # -> set fields for later lookup
                    foreach ($fieldIDs as $tmpFieldID) {
                        $subGresult[$tabID][$tmpFieldID][0] = null;
                    }

                    # -> set id 0 to create new dataset
                    $subGresult[$tabID]['id'][0] = 0;
                }

                # resolve remaining with new gresult
                self::setDataPlaceholderValues($missingPlaceholders[$tabID], $subGresultKey, $tabID, $datid);
            }
        }

        # recursively advance on relation tables
        foreach ($placeholdersByStructure as $relationFieldIdentifier => &$subStructure) {
            list($tabID, $relationFieldID) = explode(',', $relationFieldIdentifier);
            if (!is_numeric($tabID) || !is_numeric($relationFieldID)) {
                continue;
            }

            $verkn = set_verknpf($tabID, $relationFieldID, $datid, 0, 0, 1, 0);
            self::resolveDataPlaceholdersRec($subStructure, $verkn);
        }

        return true;
    }

    /**
     * Resolves this element's data placeholders for the base table set in the TemplateConfig and the given datid.
     * Additionally accepts gresult of opened Limbas dataset to prevent querying it twice.
     * @param $datid
     * @param $gresult
     * @return bool
     */
    protected function resolveDataPlaceholders($datid, &$gresult) {
        $allDataPlaceholders = $this->getUnresolvedDataPlaceholders();
        if (!$allDataPlaceholders) {
            return false;
        }

        # group by trace
        $placeholdersByStructure = array();
        foreach ($allDataPlaceholders as &$placeholder) {
            $struct = $placeholder->getStructure();
            $placeholdersByStructure = array_replace_recursive($placeholdersByStructure, $struct);
        }
        TemplateConfig::$instance->gresults[] = $gresult;
        $gresultKey = count(TemplateConfig::$instance->gresults) - 1;

        # resolve first-level placeholders
        $gtabid = TemplateConfig::$instance->getGtabid();
        if ($placeholdersByStructure['placeholders']) {
            $tableRowPlaceholders = array();

            # check which fields to request
            $allFieldIDs = array();
            $missingFieldIDs = array();
            $existingPlaceholders = array();
            $missingPlaceholders = array();
            $tabIdReadonly = array();
            $gsr = array();
            $filter = array();
            $extension = array();
            foreach ($placeholdersByStructure['placeholders'] as $key => &$placeholder) {
                // fetch IDs on base table -> must be listmode
                if ($placeholder->isModeFetchIDs()) {
                    $tableRowPlaceholders[] = &$placeholder;
                    $gsr = $placeholder->getGsr();
                    $filter = $placeholder->getFilter();
                    $extension = $placeholder->getExtension();
                    unset($placeholdersByStructure['placeholders'][$key]);
                    continue;
                }

                // note that $tabID can differ from $gtabid in case a 1:1 relation table is requested
                list($tabID, $fieldID) = explode(';', $key);
                $allFieldIDs[intval($tabID)][] = intval($fieldID);
                if (!array_key_exists($fieldID, $gresult[$tabID])) {
                    $missingFieldIDs[intval($tabID)][] = intval($fieldID);
                    $missingPlaceholders[intval($tabID)][] = &$placeholder;
                } else {
                    $existingPlaceholders[] = &$placeholder;
                }

                # check edit permission
                if (!array_key_exists($tabID, $tabIdReadonly)) {
                    $tabIdReadonly[$tabID] = check_DataPermission($tabID, $datid, $gresult);
                }
                if ($tabIdReadonly[$tabID]) {
                    $placeholder->setReadOnly();
                }
            }

            // resolve ID-fetching placeholders (list mode)
            foreach ($tableRowPlaceholders as &$placeholder) {
                $placeholder->resolve($gresultKey);
            }

            # check if requested dataset exists in gresult
            if (!$datid || !in_array($datid, $gresult[$gtabid]['id'])) {
                $missingFieldIDs = $allFieldIDs;
            }

            # resolve with existing gresult (gtabid only used to get result key in gresult, which should be the same for
            #  1:1 related tables => any gtabid can be used)
            self::setDataPlaceholderValues($existingPlaceholders, $gresultKey, $gtabid, $datid);

            # fetch data and store in gresult
            foreach ($missingFieldIDs as $tabID => $fieldIDs) {
                if ($datid) {
                    TemplateConfig::$instance->gresults[] = get_gresult($tabID, 1, $filter, $gsr, null, array($tabID => $fieldIDs), $datid, $extension);
                    $subGresultKey = count(TemplateConfig::$instance->gresults) - 1;
                    $subGresult = &TemplateConfig::$instance->gresults[$subGresultKey];
                }

                # no dataset
                if ($subGresult[$tabID]['res_count'] == 0) {
                    # -> set fields for later lookup
                    foreach ($fieldIDs as $tmpFieldID) {
                        $subGresult[$tabID][$tmpFieldID][0] = null;
                    }

                    # -> set id 0 to create new dataset
                    $subGresult[$tabID]['id'][0] = 0;
                }

                # resolve remaining with new gresult
                self::setDataPlaceholderValues($missingPlaceholders[$tabID], $subGresultKey, $tabID, $datid);
            }
        }

        # recursively advance on relation tables
        foreach ($placeholdersByStructure as $relationFieldIdentifier => &$subStructure) {
            list($tabID, $relationFieldID) = explode(',', $relationFieldIdentifier);
            if (!is_numeric($tabID) || !is_numeric($relationFieldID)) {
                continue;
            }

            $verkn = set_verknpf($tabID, $relationFieldID, $datid, 0, 0, 1, 0);
            self::resolveDataPlaceholdersRec($subStructure, $verkn);
        }

        return true;
    }

    protected static function setDataPlaceholderValues(&$placeholders, $gresultKey, $gtabid, $datid) {
        $key = array_search($datid, TemplateConfig::$instance->gresults[$gresultKey][$gtabid]['id']);
        array_walk($placeholders, function($dataPlaceholder) use ($gresultKey, $key){
            $dataPlaceholder->resolve($gresultKey, $key);
        });
    }

    /**
     * Resolves the given relation data placeholders
     * @param $structure
     * @param $verkn
     */
    protected static function resolveDataPlaceholdersRec(&$structure, $verkn) {
        global $gfield;
        global $gtab;

        $targetTabid = $verkn['vtabid'];

        # check which fields to request
        $missingFieldIDs = array();
        $tableRowPlaceholders = array();
        $gsr = array();
        $filter = array();
        $extension = array();
        foreach ($structure['placeholders'] as $key => &$placeholder) {
            if ($placeholder->isModeFetchIDs()) {
                $tableRowPlaceholders[] = &$placeholder;
                $gsr = $placeholder->getGsr();
                $filter = $placeholder->getFilter();
                $extension = $placeholder->getExtension();
                unset($structure['placeholders'][$key]);
                continue;
            }
            list($tabID, $fieldID) = explode(';', $key);
            $missingFieldIDs[$tabID][] = intval($fieldID);
        }

        # call to get_gresult (only if parent-relation-datid is available)
        if ($verkn['id'] != 0) {
            # include at least id field
            if (!$missingFieldIDs) {
                $missingFieldIDs[$targetTabid][] = $gfield[$targetTabid]['argresult_name'][$gtab['keyfield'][$targetTabid]];
            }
            foreach ($missingFieldIDs as $tabID => $fieldIDs) {
                TemplateConfig::$instance->gresults[] = get_gresult($tabID, 1, $filter, $gsr, $verkn, array($tabID => $fieldIDs), null, $extension);
            }

        } else {
            TemplateConfig::$instance->gresults[] = array();
        }
        $gresultKey = count(TemplateConfig::$instance->gresults) - 1;
        $gresult = &TemplateConfig::$instance->gresults[$gresultKey];
        $gresult[$targetTabid]['parentage'] = $verkn['tabid'] . '_' . $verkn['fieldid'] . '_' . $verkn['id'];

        // resolve ID-fetching placeholders
        foreach ($tableRowPlaceholders as &$placeholder) {
            $placeholder->resolve($gresultKey);
        }

        # no datasets -> set to create new
        if ($verkn['id'] == 0 or $gresult[$targetTabid]['res_count'] == 0) {
            # -> set fields for later lookup
            foreach ($missingFieldIDs as $tmpFieldID) {
                $gresult[$targetTabid][$tmpFieldID][0] = null;
            }

            # -> set id 0 to create new dataset
            $gresult[$targetTabid]['id'][0] = 0;
            $datid = 0;
        } else {
            $datid = $gresult[$targetTabid]['id'][0]; # pick first dataset
        }

        # resolve with new gresult
        self::setDataPlaceholderValues($structure['placeholders'], $gresultKey, $targetTabid, $datid);

        # recursively advance on relation tables
        foreach ($structure as $relationFieldIdentifier => &$subStructure) {
            list($tabID, $relationFieldID) = explode(',', $relationFieldIdentifier);
            if (!is_numeric($relationFieldID)) {
                continue;
            }

            $verkn = set_verknpf($tabID, $relationFieldID, $datid, 0, 0, 1, 0);
            self::resolveDataPlaceholdersRec($subStructure, $verkn);
        }

    }

}
