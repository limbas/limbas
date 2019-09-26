<?php

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

    public function __construct($name) {
        $this->name = $name;
    }

    public function getAsHtmlArr() {
        if (!$this->templateElement) {
            lmb_log::error("TemplateElement placeholder {$this->name} could not be resolved!", 'Not all placeholders could be resolved!');
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

    public function getName() {
        return $this->name;
    }

    public function getTemplateElement() {
        return $this->templateElement;
    }

    public function setTemplateElement(TemplateElement $templateElement) {
        $this->templateElement = $templateElement;
    }

}