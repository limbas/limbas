<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class AbstractHtmlPart
 * One TemplateElement's content consists of multiple of these parts
 */
abstract class AbstractHtmlPart {

    /**
     * To convert the html part into its html representation
     * @return array of html
     */
    public abstract function getAsHtmlArr();

    /**
     * Returns any placeholder for a TemplateElement that the html part might hold, which hasn't been resolved yet
     * @return array of SubTemplateElementPlaceholder
     */
    public function getUnresolvedSubTemplateElementPlaceholders() {
        return array();
    }

    /**
     * Returns data placeholders, where a value is not set yet
     * @return array of DataPlaceholder
     */
    public function getUnresolvedDataPlaceholders() {
        return array();
    }

    /**
     * Returns template group placeholders whose element has not been selected
     * @return array of TemplateGroupPlaceholder
     */
    public function getUnresolvedTemplateGroupPlaceholders() {
        return array();
    }

    /**
     * Returns dynamic data placeholders where no value is set
     * @return array of DynamicDataPlaceholder
     */
    public function getUnresolvedDynamicDataPlaceholders() {
        return array();
    }

    /**
     * Returns dynamic data placeholders
     * @return array of DynamicDataPlaceholder
     */
    public function getAllDynamicDataPlaceholders() {
        return array();
    }

    /**
     * Returns repeated TableRows
     * @return array of TableRow
     */
    public function getTableRows() {
        return array();
    }
}
