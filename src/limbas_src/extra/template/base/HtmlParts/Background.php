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
 * Class Background
 * Placeholder for background (only supported by mpdf)
 */
class Background extends AbstractHtmlPart {
    use TraitImage;
    
    /**
     * @var boolean defines if this background is used on page one
     */
    protected $firstPage;


    /**
     * @var int number of the background shrink type
     */
    protected $shrinkMode;

    /**
     * @var boolean whether the background should be reapeated or not
     */
    protected $repeat;


    /**
     * @var boolean whether the image should be search in Limbas DMS or not
     */
    protected $useDMS;

    /**
     * @var string the source path of the background image
     */
    protected $imgPath;

    public function __construct($attributes, $options) {

        $this->firstPage = false;        
        if (array_key_exists('first-page',$options) && filter_var($options['first-page'], FILTER_VALIDATE_BOOLEAN)) {
            $this->firstPage = true;
        }
        
        $this->shrinkMode = (int)$options['shrink-mode'];
        if (!in_array($this->shrinkMode,[0,1,2,3,4,5,6])) {
            $this->shrinkMode = 6;
        }

        $this->repeat = false;
        if (array_key_exists('repeat',$options) && filter_var($options['repeat'], FILTER_VALIDATE_BOOLEAN)) {
            $this->repeat = true;
        }

        $this->useDMS = false;
        if (array_key_exists('use-dms',$options) && filter_var($options['use-dms'], FILTER_VALIDATE_BOOLEAN)) {
            $this->useDMS = true;
        }

        $this->imgPath = $attributes['value'];
        
        if ($this->useDMS) {
            $this->imgPath = $this->getLmbFilePath($this->imgPath);
        }
        
    }

    public function getAsHtmlArr(): array
    {
        
        $first = '';
        if ($this->firstPage) {
            $first = ':first';
        }
        
        $noRepeat = 'no-';
        if ($this->repeat) {
            $noRepeat = '';
        }
        
        $css = '@page ' . $first . ' {
                background: url("' . $this->imgPath . '") 0 0 ' . $noRepeat . 'repeat;
                background-image-resize: ' . $this->shrinkMode . ';
            }';

        return array('<style>',$css,'</style>');
    }


}
