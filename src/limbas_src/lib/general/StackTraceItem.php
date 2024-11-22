<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\general;

class StackTraceItem
{
    public string $file;
    public int $line;
    public string $function;
    public string $class;
    protected array $args;

    public function __construct(
        array $trace
    )
    {

        $this->file = '';
        $this->line = 0;
        $this->function = '';
        $this->class = '';
        $this->args = array();

        if (array_key_exists('file', $trace)) {
            $this->file = $trace['file'];
        }

        if (array_key_exists('line', $trace)) {
            $this->line = $trace['line'];
        }

        if (array_key_exists('function', $trace)) {
            $this->function = $trace['function'];
        }

        if (array_key_exists('class', $trace)) {
            $this->class = $trace['class'];
        }

        if (array_key_exists('args', $trace)) {
            $this->args = $trace['args'];
        }

    }


    public function getContext(): string
    {
        return empty($this->class) ? $this->file : $this->class;
    }

    public function getFunctionArguments(): string
    {
        if (empty($this->args)) {
            return '';
        }
        return '(' . implode(', ', $this->args) . ')';
    }

}
