<?php

namespace limbas\rest;

/**
 * Class GetRelationRequestHandler
 * Fetches data of the specified relation (table_id, id, field_id)
 * @package limbas\rest
 */
class GetRelationRequestHandler extends GetRequestHandler {

    protected $relation;

    public function __construct(Request $request) {
        $this->relation = &set_verknpf($request->table_id, $request->field_id, $request->id, null, null, 1);
        $request->table_id = $this->relation['vtabid'];
        parent::__construct($request);
    }

    /**
     * @return array
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

        $filter['nolimit'][$this->request->table_id] = true;
        $filter['getlongval'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;

        list($resultIDs, $resultAttributes) = $this->getResult($filter, $gsr, $this->relation, $onlyfield, null);

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

    protected function getSelfLink() {
        $verknTableID = $this->request->table_id;
        $this->request->table_id = $this->getOriginalTableID();
        $link = &$this->request->buildGetUrl();
        $this->request->table_id = $verknTableID;
        return $link;
    }

    public function getOriginalTableID() {
        return $this->relation['tabid'];
    }

    protected function isValidTableID($showFieldTableID) {
        global $gfield;

        if (parent::isValidTableID($showFieldTableID)) {
            return true;
        }

        # relation param
        if (in_array($showFieldTableID, $gfield[$this->getOriginalTableID()]['verknparams'])) {
            $this->relation['verknparams'] = 1;
            return true;
        }
        return false;
    }

}