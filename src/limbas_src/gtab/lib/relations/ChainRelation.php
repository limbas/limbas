<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\gtab\lib\relations;

class ChainRelation
{

    public function __construct(
        private readonly int $sourceTabId,
        private readonly int $targetTabId
    )
    {
        //
    }


    public function getPossibleRelations(): array
    {
        $result = [];
        $this->getRelationPaths($this->sourceTabId, $result);
        return $result;
    }


    private function getRelationPaths(int $tabId, array &$result, array $parentTables = [], array $parentFields = []): bool|array
    {
        global $gfield, $gtab;

        if ($tabId == $this->targetTabId) {
            return true;
        }


        $path = [];

        if (in_array($tabId, $parentTables)) {
            // prevent loop: table has already been checked
            return false;
        }

        $parentTables[] = $tabId;

        // loop through all relations of given table
        foreach ($gfield[$tabId]['verkntabid'] as $fieldId => $relTabId) {

            if ($relTabId === $this->sourceTabId) {
                // skip source table to prevent loops
                continue;
            }

            if ($tabId === $this->sourceTabId && $relTabId === $this->targetTabId) {
                // this is a direct relation between source and target relation and can be ignored
                continue;
            }

            $parentFields[$tabId] = $fieldId;
            $path[$relTabId] = $this->getRelationPaths($relTabId, $result, $parentTables, $parentFields);
            if ($path[$relTabId] === true) {
                // target table reached => build array of params of the found path
                $pathParams = (object)[
                    'id' => '',
                    'names' => [],
                    'tabIds' => $parentTables,
                    'fieldIds' => $parentFields,
                    'md5' => []
                ];
                foreach ($parentFields as $parentTableId => $parentFieldId) {
                    $pathParams->names[] = $gtab['desc'][$parentTableId] . ' (' . $gfield[$parentTableId]['spelling'][$parentFieldId] . ')';
                    $pathParams->md5[] = $gfield[$parentTableId]['md5tab'][$parentFieldId];
                }
                $pathParams->id = md5(implode(',', $pathParams->md5));

                $pathParams->tabIds[] = $this->targetTabId;
                $pathParams->names[] = $gtab['table'][$this->targetTabId];

                $result[$pathParams->id] = $pathParams;
            }
        }

        $path = array_filter($path);

        return empty($path) ? false : $path;
    }

}
