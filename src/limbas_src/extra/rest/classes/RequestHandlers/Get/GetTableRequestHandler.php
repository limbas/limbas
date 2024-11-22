<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes\RequestHandlers\Get;

use Limbas\extra\rest\classes\RestException;

/**
 * Class GetTableRequestHandler
 * Fetches data from the specified table (table_id)
 * @package limbas\rest
 */
class GetTableRequestHandler extends GetRequestHandler {

    /**
     * @throws RestException
     */
    public function getData(): array
    {
        $filter = array();
        $this->setPaginationFilter($filter);
        $this->setSortingFilter($filter);
        $this->setArchivedFilter($filter);
        $this->setValidityFilter($filter);

        $onlyfield = array();
        $this->setIncludedFields($onlyfield);

        $gsr = array();
        $this->setFilterGsr($gsr);

        $filter['nolimit'][$this->request->table_id] = true; # TODO maybe use database limit
        $filter['getlongval'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;

        list($resultIDs, $resultAttributes) = $this->getResult($filter, $gsr, null, $onlyfield, null);

        $this->setPaginationLinks($filter);

        return array_map(function($resultID, &$resultAttributes) {
            return array(
                'links' => array(
                    'self' => $this->getSelfRequestLink($resultID)
                ),
                'id' => $resultID,
                'attributes' => &$resultAttributes
            );
        }, $resultIDs, $resultAttributes);
    }

}
