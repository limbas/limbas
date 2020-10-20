<?php

class FormTemplateElement extends TemplateElement {

    public function __construct($templateElementGtabid, $name, $html) {
        if (lmb_substr($html, 0, 3) === '<p>' and lmb_substr($html, -4) === '</p>') {
            $html = lmb_substr($html, 3, -4);
        }
        parent::__construct($templateElementGtabid, $name, $html);
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

            # check which fields to request
            $allFieldIDs = array();
            $missingFieldIDs = array();
            $existingPlaceholders = array();
            $missingPlaceholders = array();
            foreach ($placeholdersByStructure['placeholders'] as $key => &$placeholder) {
                // note that $tabID can differ from $gtabid in case a 1:1 relation table is requested
                list($tabID, $fieldID) = explode(';', $key);
                $allFieldIDs[intval($tabID)][] = intval($fieldID);
                if (!array_key_exists($fieldID, $gresult[$tabID])) {
                    $missingFieldIDs[intval($tabID)][] = intval($fieldID);
                    $missingPlaceholders[] = &$placeholder;
                } else {
                    $existingPlaceholders[] = &$placeholder;
                }
            }

            # check if requested dataset exists in gresult
            if (!in_array($datid, $gresult[$gtabid]['id'])) {
                $missingFieldIDs = $allFieldIDs;
            }

            # resolve with existing gresult
            $this->setDataPlaceholderValues($existingPlaceholders, $gresultKey, $gtabid, $datid);

            # fetch data and store in gresult
            foreach ($missingFieldIDs as $tabID => $fieldIDs) {
                if ($datid) {
                    TemplateConfig::$instance->gresults[] = get_gresult($tabID, 1, null, null, null, array($tabID => $fieldIDs), $datid);
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
                $this->setDataPlaceholderValues($missingPlaceholders, $subGresultKey, $tabID, $datid);
            }
        }

        # recursively advance on relation tables
        foreach ($placeholdersByStructure as $relationFieldIdentifier => &$subStructure) {
            list($tabID, $relationFieldID) = explode(',', $relationFieldIdentifier);
            if (!is_numeric($tabID) || !is_numeric($relationFieldID)) {
                continue;
            }

            $verkn = set_verknpf($tabID, $relationFieldID, $datid, 0, 0, 1, 0);
            $this->resolveDataPlaceholdersRec($subStructure, $verkn);
        }

        return true;
    }

    protected function setDataPlaceholderValues(&$placeholders, $gresultKey, $gtabid, $datid) {
        $key = array_search($datid, TemplateConfig::$instance->gresults[$gresultKey][$gtabid]['id']);
        array_walk($placeholders, function($dataPlaceholder) use ($gresultKey, $key){
            $dataPlaceholder->resolve($gresultKey, $key);
        });
    }

    protected function resolveDataPlaceholdersRec(&$structure, $verkn) {
        global $gfield;
        global $gtab;

        $targetTabid = $verkn['vtabid'];

        # check which fields to request
        $missingFieldIDs = array();
        foreach ($structure['placeholders'] as $key => $_placeholder) {
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
                TemplateConfig::$instance->gresults[] = get_gresult($tabID, 1, null, null, $verkn, array($tabID => $fieldIDs));
            }

        } else {
            TemplateConfig::$instance->gresults[] = array();
        }
        $gresultKey = count(TemplateConfig::$instance->gresults) - 1;
        $gresult = &TemplateConfig::$instance->gresults[$gresultKey];
        $gresult[$targetTabid]['parentage'] = $verkn['tabid'] . '_' . $verkn['fieldid'] . '_' . $verkn['id'];

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
        $this->setDataPlaceholderValues($structure['placeholders'], $gresultKey, $targetTabid, $datid);

        # recursively advance on relation tables
        foreach ($structure as $relationFieldIdentifier => &$subStructure) {
            list($tabID, $relationFieldID) = explode(',', $relationFieldIdentifier);
            if (!is_numeric($relationFieldID)) {
                continue;
            }

            $verkn = set_verknpf($tabID, $relationFieldID, $datid, 0, 0, 1, 0);
            $this->resolveDataPlaceholdersRec($subStructure, $verkn);
        }

    }

}
