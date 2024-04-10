<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base\HtmlParts;

use Limbas\extra\template\base\HtmlParts\Traits\TraitImage;

/**
 * Class Image
 * Parses all Images and translate path to dms if necessary
 */
class Image extends AbstractHtmlPart {
    use TraitImage;
    
    protected array $attributes;
    
    public function __construct($attributes) {        
        $this->attributes = $attributes ?? [];
        
        $src = $this->attributes['src'];
        
        if(str_contains($src, 'main.php?action=download')) {
            $fileId = intval(str_replace('main.php?action=download&ID=','',trim($this->attributes['src'])));

            $src = '';
            if($fileId > 0) {
                $src = $this->getLmbFilePath($fileId);
            }
        }

        $this->attributes['src'] = $src;
    }

    public function getAsHtmlArr(): array
    {
        $attributes = implode(' ', array_map(function($key){
            if(is_bool($this->attributes[$key])){
                return $this->attributes[$key]?$key:'';
            }
            return $key.'="'.$this->attributes[$key].'"';
        }, array_keys($this->attributes)));
        
        return array('<img ' . $attributes . '>');
    }

}
