<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\general\Log;

use DateTimeInterface;
use Stringable;
use Throwable;

readonly class LogMessage
{

    public int $timestamp;
    public string $message;

    public function __construct(
        public string                       $logName,
        public LogLevel                     $level,
        string|Stringable|Throwable         $message,
        public array                        $context = []
    )
    {
        $this->timestamp = time();
        
        if($message instanceof Throwable) {
            $this->message = $this->throwableToString($message);
        }
        else {
            $this->message = $message;
        }        
    }


    public function format(bool $prefixDate = true): string
    {
        $context = [];

        foreach ($this->context as $key => $value) {

            if (!(is_scalar($value) || $value instanceof Stringable)) {

                if ($value === null) {
                    $value = 'null';
                } elseif ($value instanceof DateTimeInterface) {
                    $value = $value->format(DateTimeInterface::RFC3339);
                } elseif (is_object($value)) {
                    $value = '[object ' . $value::class . ']';
                } else {
                    $value = '[' . gettype($value) . ']';
                }
            }

            $context[] = $key . ': ' . $value;
        }

        $message = $this->message;
        if (!empty($context)) {
            $message .= '(' . implode(', ', $context) . ')';
        }


        $logFormat = '%s %s';
        $logOutput = [
            $this->logName . '.' . $this->level->name() . ':',
            $message
        ];
        if ($prefixDate) {
            $logFormat = '[%s] ' . $logFormat;
            array_unshift($logOutput, date(DateTimeInterface::RFC3339, $this->timestamp));
        }

        return vsprintf($logFormat, $logOutput);
    }


    private function throwableToString(Throwable $throwable): string
    {
        return  $throwable->getMessage() . PHP_EOL . $throwable->getTraceAsString();
    }

}
