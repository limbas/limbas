<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\extra\template\select;

use Limbas\lib\LimbasController;

class TemplateSelectController extends LimbasController
{

    /**
     * @param array $request
     * @return array|false[]
     */
    public function handleRequest(array $request): array
    {
        return match ($request['action']) {
            'loadList' => $this->loadList($request),
            'resolve' => $this->resolve($request),
            default => ['success' => false],
        };
    }

    private function loadList(array $request): array
    {
        $type = $request['type'] ?? 'report';

        $id = $request['id'] ?? 0;
        if(is_array($id) && !empty($id)) {
            $id = $id[0];
        }
        $id = intval($id);
        
        $templateSelector = TemplateSelector::getTemplateSelectorByType($type);
        return $templateSelector->getElementListRendered(intval($request['gtabid']), $request['search'] ?? '', intval($request['page']), intval($request['perPage']), $id);
    }

    private function resolve(array $request): array
    {
        $type = $request['type'] ?? 'report';
        $templateSelector = TemplateSelector::getTemplateSelectorByType($type);
        //$selector = $templateSelector->resolveSelect($request);

        return $templateSelector->resolveSelect($request);
    }

}
