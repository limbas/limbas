<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class TemplateGroupPlaceholder
 * Placeholder for one of many template elements in a specific group
 * Syntax:
 *   ${{GroupName}}
 *   ${{GroupName("This is a description")}}
 *   ${{GroupName#identifier_for_this_tag}}
 *   ${{GroupName("This is a description")#identifier_for_this_tag}}
 */
class TemplateGroupPlaceholder extends AbstractHtmlPart {

    /**
     * @var string the group name (e.g. Test)
     */
    protected $groupName;

    /**
     * @var string a description to show when selecting the correct element
     */
    protected $description;

    /**
     * @var string an identifier to uniquely identify this group placeholder
     */
    protected $identifier;

    /**
     * @var int the id of the sub element source table if not the original table
     */
    protected $tabId;

    /**
     * @var SubTemplateElementPlaceholder|null the template element placeholder if it could be resolved
     */
    protected $subTemplateElementPlaceholder = null;

    public function __construct($groupName, $options) {
        $this->groupName = $groupName;
        $this->description = $options ? $options['desc'] : null;
        if (!$options || !$options['id']) {
            $this->identifier = $groupName;
        } else {
            $this->identifier = $options['id'];
        }
        
        if (isset($options['data'])) {
            $this->tabId = isset($options['data']['tabid']) && !empty($options['data']['tabid']) ? intval($options['data']['tabid']): null;
        }
        

        // resolve template group
        if (array_key_exists($this->identifier, TemplateConfig::$instance->resolvedTemplateGroups)) {
            $name = TemplateConfig::$instance->resolvedTemplateGroups[$this->identifier];
            $this->subTemplateElementPlaceholder = TemplateConfig::$instance->getSubTemplateElementPlaceholderInstance($name, array('tabid'=>$this->tabId));
        }
    }

    public function getAsHtmlArr() {
        if (!$this->subTemplateElementPlaceholder) {
            throw new TemplateGroupUnresolvedException($this, "TemplateGroup placeholder {$this->identifier} could not be resolved!");
        }
        return $this->subTemplateElementPlaceholder->getAsHtmlArr();
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        if (!$this->subTemplateElementPlaceholder) {
            return array();
        }
        return $this->subTemplateElementPlaceholder->getUnresolvedSubTemplateElementPlaceholders();
    }

    public function getUnresolvedDataPlaceholders() {
        if (!$this->subTemplateElementPlaceholder) {
            return array();
        }
        return $this->subTemplateElementPlaceholder->getUnresolvedDataPlaceholders();
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        if ($this->subTemplateElementPlaceholder) {
            return $this->subTemplateElementPlaceholder->getUnresolvedTemplateGroupPlaceholders();
        }
        return array($this);
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        if ($this->subTemplateElementPlaceholder) {
            return $this->subTemplateElementPlaceholder->getUnresolvedDynamicDataPlaceholders();
        }
        return array();
    }

    public function getAllDynamicDataPlaceholders() {
        if ($this->subTemplateElementPlaceholder) {
            return $this->subTemplateElementPlaceholder->getAllDynamicDataPlaceholders();
        }
        return array();
    }

    public function getTableRows() {
        if ($this->subTemplateElementPlaceholder) {
            return $this->subTemplateElementPlaceholder->getTableRows();
        }
        return array();
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function getDescription() {
        return $this->description;
    }

    /**
     * Returns an identifier which can be used to tell different TemplateGroupPlaceholder of the same groupName apart.
     * To do that it uses the description. If the descriptions of two TemplateGroupPlaceholders of the same group are
     * equal, the behaviour is undefined.
     * @return string
     */
    public function getIdentifier() {
        return $this->identifier;
    }

    public function getTemplateElement() {
        if ($this->subTemplateElementPlaceholder) {
            return $this->subTemplateElementPlaceholder->getTemplateElement();
        }
        return null;
    }

    
    public function getTabId($originalTabId) {
        return $this->tabId ?: $originalTabId;
    }
    
}
