<?php

namespace limbas\rest;

use Throwable;

class RestException extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        if (!array_key_exists($code, RequestHandler::$status_codes)) {
            $code = 500;
        }
        parent::__construct($message, $code, $previous);
    }

}