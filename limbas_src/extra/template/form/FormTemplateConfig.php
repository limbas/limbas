<?php

require_once('extra/template/base/TemplateConfig.php');
require_once(__DIR__ . '/FormTemplateElement.php');
require_once(__DIR__ . '/FormDataPlaceholder.php');

class FormTemplateConfig extends TemplateConfig {

    // stores all queried gresults globally, other objects only get key in array/reference to array
    public $gresults;
    protected $gtabid;

    // if true, uses cftyp functions instead of dftyp
    protected $listmode;

    public function __construct($gtabid, $listmode=false) {
        $this->gtabid = $gtabid;
        $this->listmode = $listmode;
    }

    public function getGtabid() {
        return $this->gtabid;
    }

    public function isListmode() {
        return $this->listmode;
    }

    public function getFunctionPrefix() {
        return 'form_';
    }

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html) {
        return new FormTemplateElement($templateElementGtabid, $name, $html);
    }

    public function getDataPlaceholderInstance($chain, $options, $altValue) {
        return new FormDataPlaceholder($chain, $options, $altValue);
    }

    public function getMedium() {
        return "form";
    }
}