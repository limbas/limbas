<?php

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