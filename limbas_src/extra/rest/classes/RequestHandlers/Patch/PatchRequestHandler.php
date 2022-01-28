<?php

namespace limbas\rest;

abstract class PatchRequestHandler extends RequestHandler {

    /**
     * @throws RestException
     */
    protected function updateRecord() {
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
                $update["{$this->request->table_id},{$fieldID},{$this->request->id}"] = lmb_utf8_decode($fieldValue);
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
    protected function checkFieldType(&$fieldID,&$fieldIdentifier) {
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
    protected function handleRelation(&$fieldID,&$relData,$replace=true) {
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
    protected function updateUniqueRelation($fieldID,$data) {

        //delete existing relation
        $rel_del_id = &$this->getFieldRelations($fieldID);

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
        $relation = &init_relation($this->request->table_id,$fieldID,$this->request->id,$rel_add_id,$rel_del_id);
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
    protected function updateManyRelation($fieldID,$data,$replace=true) {
        if (!is_array($data)) {
            throw new RestException('Key "data" of relation must be an array!', 400);
        }


        //delete all existing relations
        $rel_del_id = array();
        if ($replace) {
            $rel_del_id = &$this->getFieldRelations($fieldID);
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
        $relation = &init_relation($this->request->table_id,$fieldID,$this->request->id,$rel_add_id,$rel_del_id);
        $result = set_relation($relation);
        if ($result === false) {
            throw new RestException('Could not add relation!', 400);
        }

        # TODO: update attributes of parameterized relation
    }




}