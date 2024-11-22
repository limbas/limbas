<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes\RequestHandlers\Patch;

use Limbas\extra\rest\classes\RequestHandlers\RequestHandler;
use Limbas\extra\rest\classes\RestException;

abstract class PatchRequestHandler extends RequestHandler {

    /**
     * @throws RestException
     */
    protected function updateRecord(): int|bool|array
    {
        global $gfield;
        //TODO: Transaction?

        $data = &$this->request->request_data;

        if (!array_key_exists('data', $data)) {
            throw new RestException('Key "data" missing!', 400);
        }

        $update = array();
        foreach ($data['data'] as $fieldIdentifier => $fieldValue) {
            $fieldID = self::getFieldIdFromIdentifier($this->request->table_id, $fieldIdentifier);

            $this->checkFieldType($fieldID,$fieldIdentifier);

            if ($gfield[$this->request->table_id]['field_type'][$fieldID] == 11 /* relation */) {
                $this->handleRelation($fieldID,$fieldValue);
            } else {
                $update["{$this->request->table_id},{$fieldID},{$this->request->id}"] = $fieldValue;
            }
        }

        $result = update_data($update);

        return $result;
    }

    /**
     * checks if editing requested field is allowed
     *
     * @param $fieldID
     * @param $fieldIdentifier
     * @throws RestException
     */
    protected function checkFieldType(&$fieldID,&$fieldIdentifier): void
    {
        global $gfield;
        $fieldtype = &$gfield[$this->request->table_id]['field_type'][$fieldID];
        $datatype = &$gfield[$this->request->table_id]['data_type'][$fieldID];

        $restrictedftype = array(8,14,15,22,100,101);
        $restricteddtype = array(22);

        if (in_array($fieldtype,$restrictedftype) || in_array($datatype,$restricteddtype)) {
            throw new RestException('Editing attribute "'.$fieldIdentifier.'" is not possible.', 400);
        }
    }

    /**
     * determines which type of relation has to be used
     *
     * @param int $fieldID
     * @param mixed $relData
     * @param bool $replace
     * @throws RestException
     */
    protected function handleRelation(&$fieldID,&$relData,$replace=true): void
    {
        global $gfield;

        if (!array_key_exists('data', $relData)) {
            throw new RestException('Key "data" of relation missing!', 400);
        }


        if ($gfield[$this->request->table_id]['unique'][$fieldID]) {
            $this->updateUniqueRelation($fieldID,$relData['data']);
        } else {
            $this->updateManyRelation($fieldID,$relData['data'],$replace);
        }
    }


    /**
     * Updates an unique relation
     *
     * @param int $fieldID
     * @param mixed $data
     * @throws RestException
     */
    protected function updateUniqueRelation($fieldID,$data): void
    {

        //delete existing relation
        $rel_del_id = $this->getFieldRelations($fieldID);

        $rel_add_id = array();
        if ($data !== null) {
            if (!array_key_exists('id', $data)) {
                throw new RestException('Key "id" of relation data missing!', 400);
            }

            if (in_array($data['id'],$rel_del_id)) {
                //nothing to do, relation already exists
                return;
            }

            //add new relation
            $rel_add_id = $data['id'];
        }


        //TODO: backwards relation
        $relation = init_relation($this->request->table_id,$fieldID,$this->request->id,$rel_add_id,$rel_del_id);
        #set_relation($relation);
        $result = set_relation($relation);
        if ($result === false) {
            throw new RestException('Could not add relation!', 400);
        }
    }


    /**
     * Updates a many relation
     *
     * @param int $fieldID
     * @param mixed $data
     * @param bool $replace
     * @throws RestException
     */
    protected function updateManyRelation($fieldID,$data,$replace=true): void
    {
        if (!is_array($data)) {
            throw new RestException('Key "data" of relation must be an array!', 400);
        }


        //delete all existing relations
        $rel_del_id = array();
        if ($replace) {
            $rel_del_id = $this->getFieldRelations($fieldID);
        }


        //add relations
        $rel_add_id = array();
        foreach ($data as $rel) {
            if (!array_key_exists('id', $rel)) {
                throw new RestException('Key "id" of relation data missing!', 400);
            }
            $rel_add_id[] = $rel['id'];
        }

        $rel_add_id = array_unique($rel_add_id);

        //TODO: backwards relation
        $relation = init_relation($this->request->table_id,$fieldID,$this->request->id,$rel_add_id,$rel_del_id);
        $result = set_relation($relation);
        if ($result === false) {
            throw new RestException('Could not add relation!', 400);
        }

        # TODO: update attributes of parameterized relation
    }




}
