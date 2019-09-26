<?php

/**
 * Class Html
 * Simple Html string
 */
class Html extends AbstractHtmlPart {
    /**
     * @var string html
     */
    protected $html;

    public function __construct(&$html) {
        $this->html = &$html;
    }

    public function getAsHtmlArr() {
        return array($this->html);
    }

}