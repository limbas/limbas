<?php
namespace limbas\rest;

use lmb_log;

class ErrorHandler {

    /** @var RestException[]  */
    private static $errors = [];

    private static $shutdownWithoutError = false;

    /**
     * Throw an error immediately
     * @param RestException $e
     */
    public static function t($e) {
        self::addError($e->getCode(), $e->getMessage());
        self::printError();
        die();
    }

    /**
     * Add an error to the list
     * @param $code
     * @param $message
     */
    public static function addError($code, $message) {
        self::$errors[] = array('code' => $code, 'message' => $message);
    }

    public static function checkLmbLog() {
        $log = lmb_log::getLog(true);
        if ($log) {
            foreach ($log as &$logEntry) {
                self::addError(400 /* TODO */, $logEntry['message']);
            }
            self::printError();
            die();
        }
    }

    /**
     * Print all errors and set http header
     */
    public static function printError() {
        if (empty(self::$errors)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            die();
        }
        else if (count(self::$errors)==1) {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . self::$errors[0]['code'] . ' ' . RequestHandler::$status_codes[self::$errors[0]['code']], true, self::$errors[0]['code']);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        }

        echo json_encode(array('errors' => self::$errors));
    }

    /**
     * Registers shutdown function to catch limbas class to die()
     */
    public static function registerShutdownHandler() {
        ob_start();
        register_shutdown_function(function() {
            if (ErrorHandler::$shutdownWithoutError) {
                return;
            }
            ErrorHandler::addError(400, ob_get_clean());
            self::printError();
            die();
        });
    }

    /**
     * Prevents catching of default shutdown
     */
    public static function unregisterShutdownHandler() {
        ErrorHandler::$shutdownWithoutError = true;
        # TODO remove
        $content = ob_get_clean();
        if ($content) {
            echo $content;
        }
    }
}