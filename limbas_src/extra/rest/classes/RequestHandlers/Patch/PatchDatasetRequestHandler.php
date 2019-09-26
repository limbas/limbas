<?php

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