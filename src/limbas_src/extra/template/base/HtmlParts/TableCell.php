<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class TableCell
 * Corresponds to a <td>
 */
class TableCell extends AbstractHtmlPart {

    /**
     * @var AbstractHtmlPart[]
     */
    protected $parts;

    /**
     * @var array the attributes of this td (attrName -> attrVal).
     */
    protected $attributes;

    public function __construct($parts, $attributes) {
        $this->parts = $parts;
        $this->attributes = $attributes;
    }

    public function getAsHtmlArr() {
        $attributesStrArr = array();
        foreach ($this->attributes as $key => $val) {
            $attributesStrArr[] = "{$key}=\"{$val}\"";
        }

        // get html of parts
        $dataCellsHtmlArr = array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAsHtmlArr();
        }, $this->parts));

        // add opening <td>
        array_unshift($dataCellsHtmlArr, '<td ', join(' ', $attributesStrArr), '>');

        // add closing </td>
        array_push($dataCellsHtmlArr, '</td>');
        return $dataCellsHtmlArr;
    }

    public function getUnresolvedDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedTemplateGroupPlaceholders();
        }, $this->parts));
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDynamicDataPlaceholders();
        }, $this->parts));
    }

    public function getAllDynamicDataPlaceholders() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAllDynamicDataPlaceholders();
        }, $this->parts));
    }

    public function getTableRows() {
        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getTableRows();
        }, $this->parts));
    }

    public function getParts() {
        return $this->parts;
    }

}
