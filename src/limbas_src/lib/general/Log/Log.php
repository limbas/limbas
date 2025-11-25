<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\lib\general\Log;

use Stringable;
use Throwable;

abstract class Log implements LogInterface
{
    
    protected static Logger $logger;

    protected static string $logFile;
    
    protected static function init(): void
    {
        if(!isset(static::$logger)) {
            static::$logger = Logger::get('limbas', true, static::$logFile ?? null);
        }
    }

    public static function getLogger(): Logger
    {
        static::init();
        return static::$logger;
    }

    public static function setLogFile($logFile): void
    {
        static::$logFile = $logFile;
    }

    public static function error(string|Stringable|Throwable $message, array $context = []): void
    {
        static::init();
        static::$logger->error($message, $context);
    }

    public static function warning(string|Stringable|Throwable $message, array $context = []): void
    {
        static::init();
        static::$logger->warning($message, $context);
    }

    public static function notice(string|Stringable|Throwable $message, array $context = []): void
    {
        static::init();
        static::$logger->notice($message, $context);
    }

    public static function deprecated(string|Stringable $message, array $context = []): void
    {
        static::init();
        static::$logger->deprecated($message, $context);
    }

    public static function info(string|Stringable|Throwable $message, array $context = []): void
    {
        static::init();
        static::$logger->info($message, $context);
    }

    public static function limbasError(string|Stringable|Throwable $message, string $userMessage = null, string $tabId = null, string $fieldId = null, string $datId = null, LogLevel $level = null): void
    {
        global $session;
        global $lang;
        global $gtab;
        global $gfield;
        
        static::init();
        
        
        $context = [];
        if($tabId !== null) {
            $context['tabId'] = $tabId;
        }
        if($fieldId !== null) {
            $context['fieldId'] = $fieldId;
        }
        if($datId !== null) {
            $context['datId'] = $datId;
        }

        if ($session['debug']) {
            if($level !== null) {
                static::$logger->log($level,$message, $context);
            }
            else {
                static::$logger->error($message, $context); 
            }
        }


        // user alert
        if (!empty($userMessage) && !defined('IS_REST') && !defined('IS_SOAP') && !defined('IS_CRON') && !defined('LMB_SYNC_PROC')) {
            // add table name
            if ($tabId !== null) {
                if(is_numeric($tabId)) {
                    $tabName = $gtab['table'][$tabId];
                    $userMessage .= "\n - " . $lang[164] . ': ' . $tabName;
                }
                if(is_numeric($tabId) && is_numeric($fieldId)) {
                    $fieldName = $gfield[$tabId]['spelling'][$fieldId];
                    $userMessage .= "\n - " . $lang[168] . ": " . $fieldName;
                }
                // add datId
                if ($datId !== null) {
                    $userMessage .= "\n - " . $lang[722] . ": " . $datId;
                }
                $userMessage .= "\n";
            }

            $isError = true;
            if($level !== null && $level !== LogLevel::ERROR) {
                $isError = false;
            }

            lmb_alert($userMessage,$isError);
        }

        
    }

    public static function getMessagesAsString(bool $clearLog = false, string $delimiter = PHP_EOL): string
    {
        static::init();
        return static::$logger->getMessagesAsString($clearLog, $delimiter);
    }
    
}
