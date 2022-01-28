<?php

namespace limbas\rest;

/**
 * Class GetTableRequestHandler
 * Fetches data from the specified table (table_id)
 * @package limbas\rest
 */
class GetTableRequestHandler extends GetRequestHandler {

    /**
     * @throws RestException
     */
    public function getData() {
        $filter = array();
        $this->setPaginationFilter($filter);
        $this->setSortingFilter($filter);
        $this->setArchivedFilter($filter);
        $this->setValidityFilter($filter);

        $onlyfield = array();
        $this->setIncludedFields($onlyfield);

        $gsr = array();
        $this->setFilterGsr($gsr);

        $filter['nolimit'][$this->request->table_id] = true; # TODO maybe use database limit
        $filter['getlongval'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;

        list($resultIDs, $resultAttributes) = $this->getResult($filter, $gsr, null, $onlyfield, null);

        $this->setPaginationLinks($filter);

        return array_map(function($resultID, &$resultAttributes) {
            return array(
                'links' => array(
                    'self' => $this->getSelfRequestLink($resultID)
                ),
                'id' => $resultID,
                'attributes' => &$resultAttributes
            );
        }, $resultIDs, $resultAttributes);
    }

}