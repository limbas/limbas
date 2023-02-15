<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace limbas\rest;

/**
 * Class GetDatasetRequestHandler
 * Fetches data from the specified dataset (table_id, id)
 * @package limbas\rest
 */
class GetDatasetRequestHandler extends GetRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getData() {
        $onlyfield = array();
        $this->setIncludedFields($onlyfield);

        $filter['nolimit'][$this->request->table_id] = true;
        $filter['getlongval'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;

        list($resultIDs, $resultAttributes) = $this->getResult($filter, null, null, $onlyfield, $this->request->id);

        return array(
            'links' => array(
                'self' => $this->getSelfRequestLink($resultIDs[0])
            ),
            'id' => $resultIDs[0],
            'attributes' => &$resultAttributes[0]
        );
    }

}
