<?php

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

}