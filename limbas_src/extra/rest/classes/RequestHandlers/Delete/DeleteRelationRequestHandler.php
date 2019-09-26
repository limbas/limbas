<?php

namespace limbas\rest;

/**
 * Class DeleteRelationRequestHandler
 * Deletes the specified relation (table_id, id, field_id)
 * @package limbas\rest
 */
class DeleteRelationRequestHandler extends DeleteRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getAnswer() {
        global $gfield;



        $rel_del_id = array();

        //Unique
        if ($gfield[$this->request->table_id]['unique'][$this->request->field_id]) {
            $rel_del_id = &$this->getFieldRelations($this->request->field_id);
        }
        //to many
        else {
            if (!is_array($this->request->request_data)) {
                throw new RestException('Could not parse request body', 400);
            }
            $data = &$this->request->request_data;

            if (!array_key_exists('data', $data)) {
                throw new RestException('Key "data" is missing!', 400);
            }
            if (!is_array($data['data']) || empty($data['data'])) {
                throw new RestException('"data" must be an array of relations!', 400);
            }
            foreach ($data['data'] as $rel) {
                if (!array_key_exists('id', $rel)) {
                    throw new RestException('Key "id" of relation data missing!', 400);
                }
                $rel_del_id[] = $rel['id'];
            }
        }

        $rel_del_id = &array_unique($rel_del_id);




        // delete relation
        $relation = &init_relation($this->request->table_id,$this->request->field_id,$this->request->id,array(),$rel_del_id);
        $result = set_relation($relation);
        if ($result === false) {
            throw new RestException('Deletion not possible.', 400);
        } else if ($result <= 0) {
            throw new RestException('Relation does not exit.', 400);
        }

        $this->status = 204 /* no content */;
        return null;
    }

}