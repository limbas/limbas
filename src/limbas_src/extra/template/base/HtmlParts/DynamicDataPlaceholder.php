<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class DynamicDataPlaceholder
 * A placeholder for a value that is set by the user just before printing
 *
 * Syntax:
 *  ${"Description for the value"}
 */
class DynamicDataPlaceholder extends AbstractHtmlPart {

    /**
     * @var string Description of the placeholder, shown to user
     */
    protected $description;

    /**
     * @var string Description of data type (text, number, date, select, extension)
     */
    protected $options;

    public function __construct($description, $options) {
        $this->description = $description;
        $this->options = $options ? $options : array();
    }

    public function getAsHtmlArr() {
        $identifier = $this->getIdentifier();
        if (!array_key_exists($identifier, TemplateConfig::$instance->resolvedDynamicData)) {
            throw new DynamicDataUnresolvedException($this, "Dynamic data placeholder unresolved!");
        }
        return array(TemplateConfig::$instance->resolvedDynamicData[$identifier]);
    }

    public function getDescription() {
        return $this->description;
    }

    public function getIdentifier() {
        return html_entity_decode($this->description); // TODO maybe add useful identifier
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        $identifier = $this->getIdentifier();
        if (!array_key_exists($identifier, TemplateConfig::$instance->resolvedDynamicData)) {
            return array($this);
        }
        return array();
    }

    public function getAllDynamicDataPlaceholders() {
        return array($this);
    }

    public function getOptions() {
        return $this->options;
    }

}
