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

    protected $options = array();

    public function __construct($name, $options) {
        $this->name = $name;
        if ($options) {
            $this->options = $options;
        }
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

}
