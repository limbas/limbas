<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace limbas\rest;

abstract class PostRequestHandler extends PatchRequestHandler {

    /**
     * @throws RestException
     */
    protected function createNewRecord() {
        $result = new_data($this->request->table_id);
        if ($result === false) {
            throw new RestException('Could not create dataset!', 400);
        }
        $this->request->id = $result;
        $this->status = 201;
        $this->headers[] = 'Location: ' . $this->getRecordLink($this->request->id);
    }

    /**
     * @param $id
     * @return string
     * @throws RestException
     */
    protected function getRecordLink($id) {
        $request = new Request($this->request->router, 'GET', $this->request->table_id, $id);
        return $request->buildGetUrl();
    }

}
