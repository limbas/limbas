<?php

/**
 * Class IfPlaceholder
 *
 * Syntax:
 *  ${if condition} htmlParts ${endif}
 *  ${if condition} htmlParts ${else} htmlParts ${endif}
 * where condition is either
 *  DataPlaceholder or
 *  FunctionPlaceholder
 */
class IfPlaceholder extends AbstractHtmlPart {

    protected $condition;

    protected $consequent;

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
            return array();
        }

        return array_merge(...array_map(function (AbstractHtmlPart $part) {
            return $part->getUnresolvedDataPlaceholders();
        }, $parts));
    }

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
        }

        if ($truthy) {
            return $this->consequent;
        } else {
            return $this->alternative;
        }
    }

}