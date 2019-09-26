<?php

namespace limbas\rest;

/**
 * Class PutRelationRequestHandler
 * Updates the specified relation (table_id, id, field_id)
 * @package limbas\rest
 */
class PatchRelationRequestHandler extends PatchRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getAnswer() {
        $data = &$this->request->request_data;

        $this->handleRelation($this->request->field_id,$data);


        # redirect to the resource
        $getRequest = new Request($this->request->router, 'GET', $this->request->table_id, $this->request->id, $this->request->field_id);
        return $getRequest->getHandler()->getAnswer();
    }

}