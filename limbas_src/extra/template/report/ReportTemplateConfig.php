<?php

require_once('extra/template/base/TemplateConfig.php');
require_once(__DIR__ . '/ReportTemplateElement.php');
require_once(__DIR__ . '/ReportDataPlaceholder.php');

class ReportTemplateConfig extends TemplateConfig {

    public $report;
    public $parameter;

    /**
     * ReportTemplateConfig constructor.
     * @param $report array limbas report array
     * @param $parameter string limbas report editor param
     */
    public function __construct(&$report, $parameter) {
        $this->report = $report;
        $this->parameter = $parameter;
    }

    public function getGtabid() {
        return $this->report['referenz_tab'];
    }

    public function getFunctionPrefix() {
        return 'report_';
    }

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html) {
        return new ReportTemplateElement($templateElementGtabid, $name, $html);
    }

    public function getDataPlaceholderInstance($chain, $options, $altValue) {
        return new ReportDataPlaceholder($chain, $options, $altValue);
    }

    public function getMedium() {
        return "report";
    }
}