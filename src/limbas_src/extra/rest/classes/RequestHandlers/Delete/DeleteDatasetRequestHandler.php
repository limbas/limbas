<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes\RequestHandlers\Delete;

use Limbas\extra\rest\classes\RestException;

/**
 * Class DeleteDatasetRequestHandler
 * Deletes the specified dataset (table_id, id)
 * @package limbas\rest
 */
class DeleteDatasetRequestHandler extends DeleteRequestHandler {

    /**
     * @return array|null
     * @throws RestException
     */
    public function getAnswer(): ?array
    {
        $success = del_data($this->request->table_id, $this->request->id);
        if (!$success) {
            //TODO: more specific error message
            throw new RestException('Deletion not possible.', 400);
        }

        $this->status = 204 /* no content */;
        return null;
    }

}
