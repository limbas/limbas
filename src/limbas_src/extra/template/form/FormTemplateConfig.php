<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\form;

use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateConfigInterface;

require_once __DIR__ . '/functions.php';

class FormTemplateConfig extends TemplateConfig implements TemplateConfigInterface
{

    // stores all queried gresults globally, other objects only get key in array/reference to array
    public $gresults;

    // if true, uses cftyp functions instead of dftyp
    protected $listmode;
    
    // force all input elements to be readonly
    protected bool $readOnly;

    public function __construct(int $gtabid, $listmode, $datID, bool $readOnly = false)
    {
        $this->dataTabId = $gtabid;
        $this->listmode = $listmode;
        $this->datIDs[] = $datID;
        $this->readOnly = $readOnly;
    }

    public function isListmode()
    {
        return $this->listmode;
    }

    public function getFunctionPrefix(): string
    {
        return 'form_';
    }

    public function getTemplateElementInstance($templateElementGtabid, $name, &$html, $id = 0, $gtabid = null, $datid = null, $recursion = 0): FormTemplateElement
    {
        return new FormTemplateElement($templateElementGtabid, $name, $html, $id, $gtabid, $datid, $recursion);
    }

    public function getDataPlaceholderInstance($chain, $options, $altValue): FormDataPlaceholder
    {
        $formDataPlaceholder = new FormDataPlaceholder($chain, $options, $altValue, !$this->resolveDataPlaceholders);
        if($this->readOnly) {
            $formDataPlaceholder->setReadOnly();
        }
        return $formDataPlaceholder;
    }

    public function getMedium(): string
    {
        return 'form';
    }

    public function forTemporaryBaseTable($gtabid, $func)
    {
        $oldTab = $this->dataTabId;
        $this->dataTabId = $gtabid;
        $result = $func();
        $this->dataTabId = $oldTab;
        return $result;
    }

    public function resolveDataPlaceholdersForTemporaryBaseTable($baseTableID, $datid, &$placeholders): void
    {
        $oldTab = $this->dataTabId;
        $this->dataTabId = $baseTableID;
        FormTemplateElement::resolveDataPlaceholdersForTable($datid, $placeholders);
        $this->dataTabId = $oldTab;
    }

}
