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
 * Class PutDatasetRequestHandler
 * Updates the specified dataset (table_id, id)
 * @package limbas\rest
 */
class PatchDatasetRequestHandler extends PatchRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getAnswer() {
        $this->updateRecord();

        # redirect to the resource
        $getRequest = new Request($this->request->router, 'GET', $this->request->table_id, $this->request->id, $this->request->field_id);
        return $getRequest->getHandler()->getAnswer();
    }

}
