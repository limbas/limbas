<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\base;

use Limbas\extra\template\report\ReportTemplateElement;

abstract class TemplateRender
{

    protected abstract function getTemplateConfig(mixed $element, int $gtabid, int $id): TemplateConfig;

    public function getHtml(mixed $element, int $templateTableId, int $templateId, int $gtabid, int $id, array $resolvedTemplateGroups = [], array $resolvedDynamicData = [], $content = null): string
    {

        TemplateConfig::$instance = $this->getTemplateConfig($element, $gtabid, $id);
        TemplateConfig::$instance->resolvedTemplateGroups = $resolvedTemplateGroups;
        TemplateConfig::$instance->resolvedDynamicData = $resolvedDynamicData;

        if ($content !== null) {
            $template = new ReportTemplateElement($templateTableId, 'lmbBaseElement', $content);
        } else {
            $template = TemplateElement::fromId($templateTableId, $templateId);
        }

        $template->resolve($id);
        $htmlArr = $template->getAsHtmlArr();

        return implode('', $htmlArr);
    }


}
