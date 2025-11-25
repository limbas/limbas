<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base\HtmlParts;

use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;
use Limbas\lib\general\Log\Log;

/**
 * Class SubTemplateElementPlaceholder
 * Placeholder for another template element
 */
class SubTemplateElementPlaceholder extends AbstractHtmlPart {

    /**
     * @var string the placeholder name (e.g. Test)
     */
    protected $name;

    /**
     * @var TemplateElement|null the template element if it could be resolved
     */
    protected $templateElement = null;

    protected $options = array();


    /**
     * @var int the id of the sub element source table if not the original table
     */
    protected $tabId;

    public function __construct($name, $options) {
        $this->name = $name;
        if ($options) {
            $this->options = $options;
        }

        if (isset($options['tabid'])) {
            $this->tabId = !empty($options['tabid']) ? intval($options['tabid']): null;
        }
    }

    public function getAsHtmlArr(): array
    {
        if (!$this->templateElement) {
            Log::limbasError("TemplateElement placeholder {$this->name} could not be resolved!", 'Not all placeholders could be resolved!');
            return array('${' . $this->name . '}');
        }
        return $this->templateElement->getAsHtmlArr();
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        if ($this->templateElement) {
            return $this->templateElement->getUnresolvedSubTemplateElementPlaceholders();
        }
        return array($this);
    }

    public function getUnresolvedDataPlaceholders() {
        if (!$this->templateElement) {
            return array();
        }
        return $this->templateElement->getUnresolvedDataPlaceholders();
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        if (!$this->templateElement) {
            return array();
        }
        return $this->templateElement->getUnresolvedTemplateGroupPlaceholders();
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        if (!$this->templateElement) {
            return array();
        }
        return $this->templateElement->getUnresolvedDynamicDataPlaceholders();
    }

    public function getAllDynamicDataPlaceholders() {
        if (!$this->templateElement) {
            return array();
        }
        return $this->templateElement->getAllDynamicDataPlaceholders();
    }

    public function getTableRows() {
        if (!$this->templateElement) {
            return array();
        }
        return $this->templateElement->getTableRows();
    }


    public function getName() {
        return $this->name;
    }

    public function getTemplateElement() {
        return $this->templateElement;
    }

    public function createTemplateElement($templateElementGtabid, $name, &$html, $id, $recursion = 1) {
        $this->templateElement = TemplateConfig::$instance->getTemplateElementInstance($templateElementGtabid, $name, $html, $id, $this->options['gtabid'], $this->options['datid'], $recursion);
        return $this->templateElement;
    }

    public function getTabId($originalTabId) {
        return $this->tabId ?: $originalTabId;
    }
}
