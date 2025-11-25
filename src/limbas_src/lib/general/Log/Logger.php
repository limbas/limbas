<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\general\Log;

use Exception;
use Stringable;
use Throwable;

class Logger
{

    private array $logMessages = [];


    private $handle;


    private static array $instances = [];


    /**
     * @throws Exception
     */
    public static function get(string $name, bool $useErrorLog = false, string $logFile = null)
    {

        if (empty($name)) {
            throw new Exception('Logger name cannot be empty.');
        }

        if (!array_key_exists($name, self::$instances)) {
            self::$instances[$name] = new self($name, $useErrorLog, $logFile);
        }

        return self::$instances[$name];
    }


    /**
     * @param string $name
     * @param bool $useErrorLog
     * @param string|null $logFile
     */
    private function __construct(
        private readonly string $name,
        private readonly bool   $useErrorLog = false,
        string                  $logFile = null)
    {

        if ($logFile !== null) {
            if ($logFile && false === $this->handle = @fopen($logFile, 'a')) {
                $this->warning("Unable to open '$logFile'.");
            }
        }
    }


    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public function error(string|Stringable|Throwable $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public function warning(string|Stringable|Throwable $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public function notice(string|Stringable|Throwable $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * @param string|Stringable $message
     * @param array $context
     * @return void
     */
    public function deprecated(string|Stringable $message, array $context = []): void
    {
        $this->log(LogLevel::DEPRECATED, $message, $context);
    }

    /**
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public function info(string|Stringable|Throwable $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }


    /**
     * @param LogLevel $level
     * @param string|Stringable|Throwable $message
     * @param array $context
     * @return void
     */
    public function log(LogLevel $level, string|Stringable|Throwable $message, array $context = []): void
    {

        $logMessage = new LogMessage($this->name, $level, $message, $context);

        $this->logMessages[] = $logMessage;


        if ($this->handle) {
            @fwrite($this->handle, $logMessage->format() . PHP_EOL);
        }

        if ($level === LogLevel::INFO) {
            return;
        }

        if ($level === LogLevel::DEPRECATED) {
            trigger_error($logMessage->format(false), E_USER_DEPRECATED);
            $this->info(print_r(debug_backtrace(), 1));
        } elseif ($this->useErrorLog) {
            error_log($logMessage->format(false));
        }
    }


    /**
     * Clear all log entries
     * @param LogLevel|null $logLevel
     * @return void
     */
    public function clear(LogLevel $logLevel = null): void
    {
        if ($logLevel !== null) {
            $this->logMessages = array_filter($this->logMessages, function (LogMessage $logMessage) use ($logLevel) {
                return $logMessage->level !== $logLevel;
            });
        } else {
            $this->logMessages = [];
        }
    }


    /**
     * Get all log entries
     * @param bool $clear
     * @return array
     */
    public function getLog(bool $clear = false): array
    {
        $logMessages = $this->logMessages;
        if ($clear) {
            $this->clear();
        }
        return $logMessages;
    }

    /**
     * Get log entries for specific level
     * @param LogLevel $logLevel
     * @param bool $clear
     * @return array
     */
    public function getLevelLog(LogLevel $logLevel, bool $clear = false): array
    {
        $logMessages = array_filter($this->logMessages, function (LogMessage $logMessage) use ($logLevel) {
            return $logMessage->level === $logLevel;
        });
        if ($clear) {
            $this->clear($logLevel);
        }
        return $logMessages;
    }


    /**
     * Append log entries
     * @param array $logMessages
     * @param string $prefix
     * @return void
     */
    public function appendLog(array $logMessages, string $prefix = ''): void
    {
        /** @var LogMessage $logMessage */
        foreach ($logMessages as $logMessage) {
            $this->log($logMessage->level, ($prefix ? $prefix . ' ' : '') . $logMessage->message, $logMessage->context);
        }

    }


    /**
     * Returns only the messages of all log entries
     * @param bool $clearLog
     * @param string $delimiter
     * @return string
     */
    public function getMessagesAsString(bool $clearLog = false, string $delimiter = PHP_EOL): string
    {
        $output = implode($delimiter, array_map(function (LogMessage $logMessage) {
            return $logMessage->message;
        }, $this->logMessages));

        if ($clearLog) {
            $this->clear();
        }
        return $output;
    }


}

