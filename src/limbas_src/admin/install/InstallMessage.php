<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\admin\install;

class InstallMessage
{
    public const OK = 1;
    public const OKWARN = 2;
    public const WARN = 3;
    public const ERROR = 4;
    
    public function __construct(
        public string $name,
        public int $type,
        public ?string $message = ''
    )
    {
        //
    }


    public function getMessage(): string
    {
        $colors = [
            1 => 'success',
            2 => 'warning',
            3 => 'warning',
            4 => 'danger'
        ];

        $text = [
            1 => lang('OK'),
            2 => lang('OK, but will be better to change'),
            3 => lang('Function or tool does not work or exist, you can install later'),
            4 => lang('Necessary. You can not continue until this function works!')
        ];


        return '<span class="text-' . $colors[$this->type] . '" >' . $text[$this->type] . '</span> ' . $this->message;
    }


    public function icon(): string
    {

        $colors = [
            1 => 'success',
            2 => 'warning',
            3 => 'warning',
            4 => 'danger'
        ];

        $icons = [
            1 => 'circle-check',
            2 => 'circle-exclamation',
            3 => 'circle-xmark',
            4 => 'circle-xmark'
        ];
        
        return '<i class="fas fa-' . $icons[$this->type] . ' text-' . $colors[$this->type] . '" ></i>';
    }
    
    
}
