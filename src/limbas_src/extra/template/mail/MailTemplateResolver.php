<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\mail;

use Limbas\admin\mailTemplates\MailTemplate;
use Limbas\extra\template\base\TemplateConfig;
use Limbas\extra\template\base\TemplateElement;
use Limbas\extra\template\base\TemplateResolver;

require_once(__DIR__ . '/../../report/report.dao');

class MailTemplateResolver extends TemplateResolver
{


    /**
     * Resolves a complete report and all its root template elements
     *
     * @param int $gtabid
     * @param $elementId
     * @param array $resolvedTemplateGroups
     * @param int $ID
     * @param bool $includeChildren
     * @param bool $resolveElements
     * @param array $resolvedDynamicData
     * @param int $depth
     * @param bool $resolveDataPlaceholders
     * @param bool $noFunctionExecute
     * @return array
     */
    public function resolve(int $gtabid, $elementId, $resolvedTemplateGroups = [], $ID = 0, $includeChildren = true, $resolveElements = true, $resolvedDynamicData = [], $depth = -1, $resolveDataPlaceholders = true, $noFunctionExecute = false): array
    {

        $output = [];

        $mailTemplate = MailTemplate::get($elementId);
        if (!$mailTemplate) {
            return [];
        }

        TemplateConfig::$instance = new MailTemplateConfig([], null, $gtabid, null);
        $baseElement = TemplateElement::fromID($mailTemplate->rootTemplateTabId, $mailTemplate->rootTemplateElementId);

        $output[] = $this->resolveBaseElement($gtabid, $mailTemplate->rootTemplateTabId, $baseElement->getHtml(), '', $resolvedTemplateGroups, $ID, $includeChildren, $resolveElements, $resolvedDynamicData, $depth, $resolveDataPlaceholders, $noFunctionExecute);

        return $output;
    }


}
