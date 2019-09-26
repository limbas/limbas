<?php

namespace limbas\rest;

/**
 * Class DeleteDatasetRequestHandler
 * Deletes the specified dataset (table_id, id)
 * @package limbas\rest
 */
class DeleteDatasetRequestHandler extends DeleteRequestHandler {

    /**
     * @return null
     * @throws RestException
     */
    public function getAnswer() {
        $success = del_data($this->request->table_id, $this->request->id);
        if (!$success) {
            //TODO: more specific error message
            throw new RestException('Deletion not possible.', 400);
        }

        $this->status = 204 /* no content */;
        return null;
    }

}