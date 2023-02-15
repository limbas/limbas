<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

/**
 * Class HeaderFooter
 * Placeholder for header or footer (only supported by mpdf)
 */
class HeaderFooter extends AbstractHtmlPart {

    /**
     * @var boolean defines if the header/footer is used on page one
     */
    protected $firstPage;
    
    /**
     * @var string the name of the template that should be loaded as header/footer
     */
    protected $templateName;

    /**
     * @var string [header|footer] specifies if this element this element should be treated as header or footer
     */
    protected $type;

    /**
     * @var SubTemplateElementPlaceholder|null the template element placeholder if it could be resolved
     */
    protected $subTemplateElementPlaceholder = null;

    public function __construct($type, $attributes, $options) {

        $this->type = $type;
        $this->attributes = $attributes;
        $this->templateName = $this->attributes['name'];

        $this->firstPage = false;
        if (array_key_exists('first-page',$options) && filter_var($options['first-page'], FILTER_VALIDATE_BOOLEAN)) {
            $this->firstPage = true;
        }
        
        $this->subTemplateElementPlaceholder = TemplateConfig::$instance->getSubTemplateElementPlaceholderInstance($this->templateName, array());
    }


    public function getAsHtmlArr() {
        
        // get html of linked template element
        $htmlArr = $this->subTemplateElementPlaceholder->getAsHtmlArr();

        //add extra attribute for html preview
        $firstPage = '';
        if ($this->firstPage) {
            $firstPage = ' | only on first page';
        }
        
        // add opening <htmlpage>
        array_unshift($htmlArr, '<htmlpage' . $this->type . ' name="' . $this->templateName . '" data-is-first-page="' . $firstPage . '" data-type="' . $this->type . '">');

        // add closing </htmlpage>
        array_push($htmlArr, '</htmlpage' . $this->type . '>');

        // add set header or footer as css
        $first = '';
        if ($this->firstPage) {
            $first = ':first';
        }
        $css = '@page ' . $first . ' {' . $this->type . ': ' . $this->templateName . ';}';

        array_push($htmlArr,'<style>',$css,'</style>');
        
        return $htmlArr;
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

}
