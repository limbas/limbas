<?php

class ReportDataPlaceholder extends DataPlaceholder {

    public function __construct($fieldIdentifiers, $options, $altValue, $noResolve)
    {
        parent::__construct($fieldIdentifiers, $options, $altValue, $noResolve);
    }

    protected function resolve() {
        if ($this->noResolve) {
            return true;
        }
        
        $report = TemplateConfig::$instance->report;

        # not a valid field -> return true -> dont lookup in db
        if ($this->fieldlist === null) {
            return true;
        }

        # I fetch all IDs of base table -> no fieldid -> cannot be resolved
        if ($this->modeFetchBaseTable) {
            return false;
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

    public function getAsHtmlArr() {
        $htmlArr = &parent::getAsHtmlArr();

        // option: css class
        if (array_key_exists('class', $this->options)) {
            $el = 'span';
            if (array_key_exists('element', $this->options)) {
                $el = $this->options['element'];
            }
            array_unshift($htmlArr, "<{$el} class=\"{$this->options['class']}\">");
            array_push($htmlArr, "</{$el}>");
        }

        return $htmlArr;
    }


}
