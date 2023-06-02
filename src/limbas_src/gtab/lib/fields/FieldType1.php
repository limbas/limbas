<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib\fields;


use Limbas\gtab\lib\fields\legacy\Func2;

/**
 * Text
 */
class FieldType1 extends Func2
{

    protected string $fieldTypeName = 'text';
    
    public function renderInput(mixed $value = null): string
    {        
        return '<input type="text" class="form-control" value="' . $this->encodeValue($value) . '" name="' . $this->field->formName . '" maxlength="' . $this->field->size . '">';
    }
    
    public function update($gtabid,$fieldid,$ID,&$change_value,$language) {
        global $gfield;

        $fieldname = $gfield[$gtabid]["field_name"][$fieldid];
        if($language){
            $fieldname = 'LANG'.$language.'_'.$fieldname;
        }

        $change_value = parse_db_string($change_value,$gfield[$gtabid]["size"][$fieldid]);
        $sqlquery = $fieldname." = '".$change_value."'";
        $sqlquery = str_replace(array("\n", "\r"),"",$sqlquery);

        return $sqlquery;
    }


    
}
