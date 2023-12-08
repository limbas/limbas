<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\report;

use Limbas\extra\template\base\HtmlParts\DataPlaceholder;
use Limbas\extra\template\base\TemplateConfig;

class ReportDataPlaceholder extends DataPlaceholder
{

    public function __construct($fieldIdentifiers, $options, $altValue, $noResolve)
    {
        parent::__construct($fieldIdentifiers, $options, $altValue, $noResolve);
    }

    protected function resolve()
    {
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
        $rdbfieldCount = 0;
        if (is_array($report['dbfield'])) {
            $rdbfieldCount = lmb_count($report['dbfield']);
        }
        for ($i = 0; $i < $rdbfieldCount; $i++) {
            # field found?
            if ($this->fieldlist[0] !== $report['dbfield'][$i]) {
                continue;
            }

            # not relation?
            if (!is_array($this->fieldlist) || lmb_count($this->fieldlist) === 1 && $report['dbfield'][$i] !== '') {
                continue;
            }

            # same relation tree?
            $lmbRelationParts = explode('|', $report['verkn_baum'][$i]);
            $dataRelationParts = explode('|', $this->fieldlist[1]);
            if (!is_array($lmbRelationParts) || is_array($dataRelationParts) || lmb_count($lmbRelationParts) !== lmb_count($dataRelationParts)) {
                continue;
            }
            $lmbRelationPartCount = lmb_count($lmbRelationParts);
            for ($u = 0; $u < $lmbRelationPartCount; $u++) {
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

    public function getAsHtmlArr(): array
    {
        $htmlArr = parent::getAsHtmlArr();

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
