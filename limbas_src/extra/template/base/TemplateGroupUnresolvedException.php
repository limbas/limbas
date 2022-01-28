<?php

class TemplateGroupUnresolvedException extends \RuntimeException {

    public $unresolvedTemplateGroups;

    public function __construct(&$unresolvedTemplateGroups, $message = "", $code = 0, $previous = null) {
        $this->unresolvedTemplateGroups = &$unresolvedTemplateGroups;
        parent::__construct($message, $code, $previous);
    }

}