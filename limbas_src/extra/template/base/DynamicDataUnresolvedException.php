<?php

class DynamicDataUnresolvedException extends \RuntimeException {

    public $unresolvedDynamicData;

    public function __construct(&$unresolvedDynamicData, $message = "", $code = 0, $previous = null) {
        $this->unresolvedDynamicData = &$unresolvedDynamicData;
        parent::__construct($message, $code, $previous);
    }

}