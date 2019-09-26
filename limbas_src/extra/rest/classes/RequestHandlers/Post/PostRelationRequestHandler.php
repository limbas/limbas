<?php

namespace limbas\rest;

/**
 * Class PostRelationRequestHandler
 * Creates the specified relation (table_id, id, field_id)
 * @package limbas\rest
 */
class PostRelationRequestHandler extends PostRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getAnswer() {
        $data = &$this->request->request_data;

        $this->handleRelation($this->request->field_id,$data,false);

        # redirect to the resource
        $getRequest = new Request($this->request->router, 'GET', $this->request->table_id, $this->request->id, $this->request->field_id);
        return $getRequest->getHandler()->getAnswer();
    }

}