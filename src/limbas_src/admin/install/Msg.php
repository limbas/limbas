<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class Msg
{
    private const OK = 1;
    private const OKWARN = 2;
    private const WARN = 3;
    private const ERROR = 4;
    
    
    private static function get($type, $textOnly = false) {

        $text = '';
        $class = '';

        switch ($type) {
            case self::OK:
                $text = lang('OK');
                $class= 'text-success';
                break;
            case self::OKWARN:
                $text = lang('OK, but will be better to change');
                $class= 'text-warning';
                break;
            case self::WARN:
                $text = lang('Function or tool does not work or exist, you can install later');
                $class= 'text-warning';
                break;
            case self::ERROR:
                $text = lang('Necessary. You can not continue until this function works!');
                $class= 'text-danger';
                break;
        }
        
        
        if ($textOnly) {
            return $text;
        }        
        return '<span class="' . $class . '">' . $text . '</span>';
    }
    
    
    public static function ok() {
        return self::get(self::OK);
    }

    public static function okwarn() {
        return self::get(self::OKWARN);
    }

    public static function warn() {
        return self::get(self::WARN);
    }

    public static function error() {
        return self::get(self::ERROR);
    }

    public static function icon($code=null) {
        
        $msgcolors = [
            1 => 'success',
            2 => 'warning',
            3 => 'warning',
            4 => 'danger'
        ];

        if(!empty($code)) {

            $code = (int) $code;
            
            $tooltip = self::get($code, true);

            return "<td title=\"$tooltip\" style=\"width: 20px;\"><i class=\"lmb-icon lmb-status-$code text-{$msgcolors[$code]}\" ></i></td>";
        } else {
            return '<td style="width: 20px;"></td>';
        }
    }
    
}
