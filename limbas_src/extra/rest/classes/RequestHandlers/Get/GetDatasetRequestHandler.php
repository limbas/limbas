<?php

namespace limbas\rest;

/**
 * Class GetDatasetRequestHandler
 * Fetches data from the specified dataset (table_id, id)
 * @package limbas\rest
 */
class GetDatasetRequestHandler extends GetRequestHandler {

    /**
     * @return array
     * @throws RestException
     */
    public function getData() {
        $onlyfield = array();
        $this->setIncludedFields($onlyfield);

        $filter['nolimit'][$this->request->table_id] = true;
        $filter['getlongval'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;

        list($resultIDs, $resultAttributes) = $this->getResult($filter, null, null, $onlyfield, $this->request->id);

        return array(
            'links' => array(
                'self' => $this->getSelfRequestLink($resultIDs[0])
            ),
            'id' => $resultIDs[0],
            'attributes' => &$resultAttributes[0]
        );
    }

}