<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
class Layout
{
    
    private static $layoutCache;

    public static function getFilePath($file): string
    {
        global $session;

        $require = EXTENSIONSPATH . 'layout/' . $session['layout'] . '/' . $file;
        if (!file_exists($require)) {
            $require = COREPATH . 'layout/' . $file;
        }
        
        return $require;
    }
    
    
    public static function getAvailableLayouts() {
        
        if (!empty(self::$layoutCache)) {
            return self::$layoutCache;
        }
        $layouts = ['barracuda'];
        if(is_dir(EXTENSIONSPATH . 'layout' ) && $path = read_dir(EXTENSIONSPATH . 'layout')){
            foreach($path['name'] as $key => $value){
                if ($value === 'css') continue;
                if($path['typ'][$key] == 'dir'){
                    $layouts[] = $value;
                }
            }
        }
        self::$layoutCache = $layouts;
        return $layouts;
    }
    
}
