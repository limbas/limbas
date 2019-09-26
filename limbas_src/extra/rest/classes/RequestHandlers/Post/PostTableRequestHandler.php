<?php

namespace limbas\rest;

/**
 * Class PostTableRequestHandler
 * Creates the specified dataset (table_id)
 * @package limbas\rest
 */
class PostTableRequestHandler extends PostRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getAnswer() {
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
    protected function checkRequirement() {
        global $gfield;

        foreach ($gfield[$this->request->table_id]['need'] as $fieldid => $value) {
            $fieldname = strtolower($gfield[$this->request->table_id]['field_name'][$fieldid]);

            if (!array_key_exists($fieldname,$this->request->request_data['data'])){
                throw new RestException('Required attribute "'.$fieldname.'" missing!', 400);
            }
        }
    }

}