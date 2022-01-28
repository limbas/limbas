<?php

/**
 * Class IfPlaceholder
 *
 * Syntax:
 *  ${if condition} htmlParts ${endif}
 *  ${if condition} htmlParts ${else} htmlParts ${endif}
 *  ${if condition} htmlParts ${elseif condition} htmlParts ${endif}
 *  ${if condition} htmlParts ${elseif condition} htmlParts ${else} htmlParts ${endif}
 * where condition is either
 *  DataPlaceholder or
 *  FunctionPlaceholder
 */
class IfPlaceholder extends AbstractHtmlPart {

    /**
     * @var DataPlaceholder|FunctionPlaceholder
     */
    protected $condition;

    /**
     * @var array of AbstractHtmlPart
     */
    protected $consequent;

    /**
     * @var array of AbstractHtmlPart
     */
    protected $alternative;

    public function __construct($condition, $consequent, $alternative) {
        $this->condition = $condition;
        $this->consequent = $consequent;
        $this->alternative = $alternative;
    }

    public function getAsHtmlArr() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        $partsHtmlArr = &array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAsHtmlArr();
        }, $parts));
        if ($partsHtmlArr === null) {
            return array('');
        }
        return $partsHtmlArr;
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedSubTemplateElementPlaceholders();
        }, $parts));
    }

    public function getUnresolvedDataPlaceholders() {
        # if-function couldn't be called?
        if ($this->condition instanceof FunctionPlaceholder) {
            if (!$this->condition->tryFunctionCall()) {
                return $this->condition->getUnresolvedDataPlaceholders();
            }
        } else if ($this->condition instanceof DataPlaceholder) {
            if (!$this->condition->isResolved()) {
                return array($this->condition);
            }
        }

        $parts = &$this->getParts();
        if (!$parts) {
            // else if
            if ($this->alternative instanceof IfPlaceholder) {
                return $this->alternative->getUnresolvedDataPlaceholders();
            }
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $parts));
    }


    public function getUnresolvedTemplateGroupPlaceholders() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedTemplateGroupPlaceholders();
        }, $parts));
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDynamicDataPlaceholders();
        }, $parts));
    }

    public function getAllDynamicDataPlaceholders() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getAllDynamicDataPlaceholders();
        }, $parts));
    }

    public function getTableRows() {
        $parts = &$this->getParts();
        if (!$parts) {
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getTableRows();
        }, $parts));
    }


    /**
     * Evaluates condition and decides whether to return consequent or alternative.
     * Returns false if condition cannot be evaluated.
     * @return false|array of AbstractHtmlPart
     */
    protected function getParts() {
        # if-function couldn't be called?
        if ($this->condition instanceof FunctionPlaceholder) {
            if (!$this->condition->tryFunctionCall()) {
                return false;
            }
            $truthy = $this->condition->resultTruthy();
        } else if ($this->condition instanceof DataPlaceholder) {
            if (!$this->condition->isResolved()) {
                return false;
            }
            $truthy = $this->condition->getValue() ? true : false;
        } else {
            throw new RuntimeException("Condition is neither FunctionPlaceholder not DataPlaceholder!");
        }

        if ($truthy) {
            return $this->consequent;
        } else {
            // elseif
            if ($this->alternative instanceof IfPlaceholder) {
                return $this->alternative->getParts();
            }
            return $this->alternative;
        }
    }
    
    
    public function getConsequent($templateElementGtabid) {
        if (empty($this->consequent)) {
            return null;
        }
        $html='';
        $templateElement = TemplateConfig::$instance->getTemplateElementInstance($templateElementGtabid, 'consequent', $html);
        $templateElement->setParts($this->consequent);
        return $templateElement;
    }


    public function getCondition() {
        return $this->condition;
    }

    public function getAlternative($templateElementGtabid) {
        if (empty($this->alternative)) {
            return null;
        } elseif ($this->alternative instanceof IfPlaceholder) {
            return $this->alternative;
        }
        $html='';
        $templateElement = TemplateConfig::$instance->getTemplateElementInstance($templateElementGtabid, 'alternative', $html);
        $templateElement->setParts($this->alternative);
        return $templateElement;
    }

}
