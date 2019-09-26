<?php

class ReportTemplateElement extends TemplateElement {

    /**
     * Resolve templates and data placeholders until everything is resolved
     * @param $datid
     */
    public function resolve($datid) {
        do {
            $successData = $this->resolveDataPlaceholders($datid);
            $successTpl = $this->resolveTemplates();
        } while ($successTpl || $successData);
    }


    /**
     * Resolves all DataPlaceholders to their corresponding data
     * @param $datid int current dataset id for which the template is being resolved
     * @return bool whether a placeholder was resolved
     */
    public function resolveDataPlaceholders($datid) {
        $report = &TemplateConfig::$instance->report;
        $parameter = &TemplateConfig::$instance->parameter;

        $allDataPlaceholders = $this->getUnresolvedDataPlaceholders();
        if (!$allDataPlaceholders) {
            return false;
        }

        # group by trace
        $placeholdersByTrace = array();
        foreach ($allDataPlaceholders as &$placeholder) {
            $placeholdersByTrace[$placeholder->getTrace()][] = $placeholder;
        }

        # resolve placeholders
        foreach ($placeholdersByTrace as $trace => &$placeholders) {
            # collect fieldlist by trace
            $fieldlist = array();
            $numPlaceholders = count($placeholders);
            for ($i = 0; $i < $numPlaceholders; $i++) {
                $fieldlist[$i] = $placeholders[$i]->getFieldlist();
                $fieldlist[$i][3] = $parameter;
            }

            # get data from fields
            $data = get_dataArray($datid, $report['report_id'], null, $report, null, null, null, $fieldlist);

            # set placeholder value
            for ($i = 0; $i < $numPlaceholders; $i++) {
                $placeholders[$i]->setValue($data[$i][0]);
            }
        }
        return true;
    }

    public function getExtension() {
        $extension = null;

        $parameter = &TemplateConfig::$instance->parameter;
        if ($parameter) {
            eval($parameter);
            if (!isset($extension) or !array_key_exists($this->templateElementGtabid, $extension)) {
                $extension = null;
            } else {
                $extension = $extension[$this->templateElementGtabid];
            }
        }

        return $extension;
    }

}