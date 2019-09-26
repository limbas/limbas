<?php

class ReportDataPlaceholder extends DataPlaceholder {

    protected function resolve() {
        $report = TemplateConfig::$instance->report;

        # not a valid field -> return true -> dont lookup in db
        if ($this->fieldlist === null) {
            return true;
        }

        # resolve from existing report element
        for ($i = 0; $i < count($report['dbfield']); $i++) {
            # field found?
            if ($this->fieldlist[0] !== $report['dbfield'][$i]) {
                continue;
            }

            # not relation?
            if (count($this->fieldlist) === 1 && $report['dbfield'][$i] !== '') {
                continue;
            }

            # same relation tree?
            $lmbRelationParts = explode('|', $report['verkn_baum'][$i]);
            $dataRelationParts = explode('|', $this->fieldlist[1]);
            if (count($lmbRelationParts) !== count($dataRelationParts)) {
                continue;
            }
            for ($u = 0; $u < count($lmbRelationParts); $u++) {
                if (!$lmbRelationParts[$u] && !$dataRelationParts[$u]) {
                    continue;
                }
                if (lmb_strpos($lmbRelationParts[$u], $dataRelationParts[$u] . ';') === false) {
                    continue 2;
                }
            }

            $this->setValue($report['dbvalue'][$i][0]);
            return true;
        }
        return false;
    }
}