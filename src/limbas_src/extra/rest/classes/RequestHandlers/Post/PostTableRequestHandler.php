<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes\RequestHandlers\Post;

use Limbas\extra\rest\classes\Request;
use Limbas\extra\rest\classes\RestException;

/**
 * Class PostTableRequestHandler
 * Creates the specified dataset (table_id)
 * @package limbas\rest
 */
class PostTableRequestHandler extends PostRequestHandler {

    /**
     * @return array|null
     * @throws RestException
     */
    public function getAnswer(): ?array
    {
        if (!array_key_exists('data', $this->request->request_data)) {
            throw new RestException('Key "data" missing!', 400);
        }
        //TODO: transaction?

        $this->checkRequirement();

        $this->createNewRecord();
        $this->updateRecord();

        # redirect to the resource
        $getRequest = new Request($this->request->router, 'GET', $this->request->table_id, $this->request->id);
        return $getRequest->getHandler()->getAnswer();
    }

    /**
     * check if all required fields are included in the data
     *
     * @throws RestException
     */
    protected function checkRequirement(): void
    {
        global $gfield;

        foreach ($gfield[$this->request->table_id]['need'] as $fieldid => $value) {
            $fieldname = strtolower($gfield[$this->request->table_id]['field_name'][$fieldid]);

            if (!array_key_exists($fieldname,$this->request->request_data['data'])){
                throw new RestException('Required attribute "'.$fieldname.'" missing!', 400);
            }
        }
    }

}
