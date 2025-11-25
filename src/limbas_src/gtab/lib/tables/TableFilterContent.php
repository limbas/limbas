<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\gtab\lib\tables;

class TableFilterContent
{

    /**
     * @param mixed $order
     * @param mixed $popups
     * @param mixed $status
     * @param mixed $hideCols
     * @param mixed $form
     * @param mixed $gsr
     * @param mixed $sort
     * @param mixed $rowSize
     */
    public function __construct(

        public mixed $order = null,
        public mixed $popups = null,
        public mixed $status = null,
        public mixed $hideCols = null,
        public mixed $form = null,
        public mixed $gsr = null,
        public mixed $sort = null,
        public mixed $rowSize = null,
    )
    {
        //
    }


    public static function fromJson(string $json): ?TableFilterContent
    {
        $attributes = json_decode($json, true);
        
        if($attributes === null) {
            return null;
        }
        
        return new self(
            $attributes['order'] ?? null,
                $attributes['popups'] ?? null,
                $attributes['status'] ?? null,
                $attributes['hidecols'] ?? null,
                $attributes['form'] ?? null,
                $attributes['gsr'] ?? null,
                $attributes['sort'] ?? null,
                $attributes['rowsize'] ?? null,
        );
    }
    
    public function toJson(): string
    {
        $encoded = json_encode($this->toArray());
        return $encoded === false ? '[]' : $encoded;
    }

    public function toArray(): array
    {
        return array_change_key_case(get_object_vars($this));
    }
    
    
}
