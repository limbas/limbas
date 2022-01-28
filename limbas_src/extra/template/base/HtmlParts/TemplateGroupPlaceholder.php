<?php

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

        // resolve template group
        if (array_key_exists($this->identifier, TemplateConfig::$instance->resolvedTemplateGroups)) {
            $name = TemplateConfig::$instance->resolvedTemplateGroups[$this->identifier];
            $this->subTemplateElementPlaceholder = TemplateConfig::$instance->getSubTemplateElementPlaceholderInstance($name, array());
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

}