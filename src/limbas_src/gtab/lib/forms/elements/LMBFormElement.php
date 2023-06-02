<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib\forms\elements;


use Limbas\gtab\lib\fields\LMBField;
use Limbas\gtab\lib\fields\LMBFieldType;

abstract class LMBFormElement
{
    
    public function __construct($ID=null, $edit=true, $class=null, $style=null, $pos=null, $z_index=1, &$event=null, $options=null) {
        
    }
    
    public abstract function render(): string;

    public abstract function getId(): int;
}
