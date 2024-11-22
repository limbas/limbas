<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes;

use Limbas\extra\rest\classes\RequestHandlers\RequestHandler;
use lmb_log;

class ErrorHandler {

    /** @var RestException[]  */
    private static array $errors = [];

    private static bool $shutdownWithoutError = false;

    /**
     * Throw an error immediately
     * @param RestException $e
     */
    public static function t($e): void
    {
        self::addError($e->getCode(), $e->getMessage());
        self::printError();
        die();
    }

    /**
     * Add an error to the list
     * @param int $code
     * @param string $message
     */
    public static function addError(mixed $code, string $message): void
    {
        self::$errors[] = array('code' => $code, 'message' => $message);
    }

    public static function checkLmbLog(): void
    {
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
    public static function printError(): void
    {
        if (empty(self::$errors)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            die();
        }
        else if (lmb_count(self::$errors)==1) {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . self::$errors[0]['code'] . ' ' . RequestHandler::$status_codes[self::$errors[0]['code']], true, self::$errors[0]['code']);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        }

        echo json_encode(array('errors' => self::$errors));
    }

    /**
     * Registers shutdown function to catch limbas class to die()
     */
    public static function registerShutdownHandler(): void
    {
        ob_start();
        register_shutdown_function(function() {
            if (ErrorHandler::$shutdownWithoutError) {
                return;
            }
            ErrorHandler::addError(400, ob_get_clean() ?: '');
            self::printError();
            die();
        });
    }

    /**
     * Prevents catching of default shutdown
     */
    public static function unregisterShutdownHandler(): void
    {
        ErrorHandler::$shutdownWithoutError = true;
        # TODO remove
        $content = ob_get_clean();
        if ($content) {
            echo $content;
        }
    }
}
