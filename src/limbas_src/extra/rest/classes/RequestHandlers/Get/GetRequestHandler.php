<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace limbas\rest;

abstract class GetRequestHandler extends RequestHandler {

    protected $links = array();
    protected $gresult = null;

    protected abstract function getData();

    public function getAnswer() {
        $this->links['self'] = $this->getSelfLink();

        $data = $this->getData();
        return array(
            'links' => $this->links,
            'data' => $data
        );
    }

    /**
     * @param $filter
     * @param $gsr
     * @param $verkn
     * @param $onlyfield
     * @param $single
     * @return array
     * @throws RestException
     */
    protected function getResult($filter, $gsr, $verkn, $onlyfield, $single) {
        global $gfield;

        $gtabid = $this->request->table_id;

        if (lmb_count($onlyfield) === 0) {
            $onlyfield = null;
        }



        $gresult = get_gresult($gtabid, 1, $filter, $gsr, $verkn, $onlyfield, $single);
        $this->gresult = &$gresult;

        $resultData = array();
        if (!$gresult) {
            return array();
        }

        $numDatasets = $gresult[$gtabid]['res_viewcount'];
        foreach ($gresult as $gtabid => &$tableGresult) {
            # add requested fields
            foreach ($tableGresult as $fieldID => &$value) {
                if (!is_numeric($fieldID)) {
                    continue;
                }
                $fieldName = lmb_strtolower($gfield[$gtabid]['field_name'][$fieldID]);

                for ($bzm = 0; $bzm < $numDatasets; $bzm++) {

                    if ($gfield[$gtabid]['field_type'][$fieldID] == 11 /* relation */) {
                        if (!array_key_exists($bzm, $value)) {
                            $resultData[$bzm][$fieldName] = array();
                            continue;
                        }

                        if ($gfield[$gtabid]['data_type'][$fieldID] == 25 AND $gfield[$gtabid]['verkntabletype'][$fieldID] == 2) {
                            # direct backwards relation (contains only one result)
                            $data = array(array(
                                'id' => $value[$bzm]
                            ));
                        } else {
                            # other relations
                            $data = [];
                            if (is_array($value[$bzm])) {
                                $data = array_map(function($relationID) {
                                    return array(
                                        'id' => $relationID /* TODO attributes */
                                    );
                                }, $value[$bzm]);
                            }
                            
                        }

                        # params
                        $resultData[$bzm][$fieldName] = array(
                            'links' => array(
                                'self' => $this->getRelationTableLinkSelf($gresult[$gtabid]['id'][$bzm], $fieldID),
                                'related' => $this->getRelationTableLinkRelated($fieldID)
                            ),
                            'data' => $data
                        );
                    } else {
                        $fname = 'cftyp_' . $gfield[$gtabid]['funcid'][$fieldID];
                        $result = &$fname($bzm, $fieldID, $gtabid, 6, $gresult);

                        // encode allways to UTF-8
                        if($GLOBALS["umgvar"]["charset"] == "UTF-8"){
                             $resultData[$bzm][$fieldName] = $result;
                        }else {
                            if (is_array($result)) {
                                array_walk_recursive($result, array($this, 'decode_recursive'));
                                $resultData[$bzm][$fieldName] = $result;
                            } elseif (is_numeric($result)) {
                                $resultData[$bzm][$fieldName] = $result;
                            } else {
                                $resultData[$bzm][$fieldName] = lmb_utf8_encode($result);
                            }
                        }

                    }
                }
            }
        }
        $resultIds = [];
        if (!empty($gresult[$this->request->table_id]['id'])) {
            $resultIds = $gresult[$this->request->table_id]['id'];
        }
        return array($resultIds, $resultData);
    }

    /**
     * @param $item
     * @param $key
     */
    protected function decode_recursive(&$item, $key) {
        if(!is_numeric($item)){
            $item = lmb_utf8_encode($item);
        }
    }

    /**
     * @param $filter
     */
    protected function setPaginationFilter(&$filter) {
        # TODO permit query before first/last page
        $queryParams = &$this->request->api_params;

        $paramname = Request::API_PARAMETERS['page'];

        $filter['page'][$this->request->table_id] = 1;
        if ($queryParams[$paramname]['number']) {
            $filter['page'][$this->request->table_id] = parse_db_int($queryParams[$paramname]['number']);
        }
        $filter['anzahl'][$this->request->table_id] = 'all';
        if ($queryParams[$paramname]['count']) {
            $filter['anzahl'][$this->request->table_id] = parse_db_int($queryParams[$paramname]['count']);
        }
    }

    protected function setPaginationLinks(&$filter) {
        $pageCount = $filter['anzahl'][$this->request->table_id];
        if ($pageCount === 'all') {
            return;
        }
        $pageCount = intval($pageCount);
        $currentPageNumber = intval($filter['page'][$this->request->table_id]);

        $totalNumber = $this->gresult[$this->request->table_id]['max_count'];

        $lastPageNumber = ceil($totalNumber / $pageCount);

        $paramname = Request::API_PARAMETERS['page'];

        $request = $this->request;
        $request->api_params[$paramname]['count'] = $pageCount;

        # first
        $request->api_params[$paramname]['number'] = 1;
        $this->links['first'] = $request->buildGetUrl();

        # prev
        if ($currentPageNumber > 1) {
            $request->api_params[$paramname]['number'] = $currentPageNumber - 1;
            $this->links['prev'] = $request->buildGetUrl();
        }

        # next
        if ($currentPageNumber < $lastPageNumber) {
            $request->api_params[$paramname]['number'] = $currentPageNumber + 1;
            $this->links['next'] = $request->buildGetUrl();
        }

        # last
        $request->api_params[$paramname]['number'] = $lastPageNumber;
        $this->links['last'] = $request->buildGetUrl();

        # restore old request
        $request->api_params[$paramname]['count'] = $pageCount;
        $request->api_params[$paramname]['number'] = $currentPageNumber;
    }

    /**
     * @param $filter
     * @throws RestException
     */
    protected function setSortingFilter(&$filter) {
        $queryParams = &$this->request->api_params;

        $paramname = Request::API_PARAMETERS['sort'];

        if (!array_key_exists($paramname, $queryParams)) {
            return;
        }

        $filter['order'][$this->request->table_id] = array();
        foreach (explode(',', $queryParams[$paramname]) as &$sortFieldStr) {
            # get sort direction
            $sortDirection = 'asc';
            if (lmb_substr($sortFieldStr, 0, 1) === '-') {
                $sortFieldStr = lmb_substr($sortFieldStr, 1);
                $sortDirection = 'desc';
            }

            # resolve table and field name
            list($sortTableID, $sortFieldID) = $this->getTableAndFieldIDFromDottedIdentifier($sortFieldStr);

            $filter['order'][$this->request->table_id][] = array($sortTableID, $sortFieldID, $sortDirection);
        }
    }

    /**
     * @param $filter
     * @throws RestException
     */
    protected function setArchivedFilter(&$filter) {
        $queryParams = &$this->request->api_params;
        $paramname = Request::API_PARAMETERS['archived'];

        if (!array_key_exists($paramname, $queryParams)) {
            return;
        }

        $param = lmb_strtolower($queryParams[$paramname]);

        if ($param === 'all') {
            $filter['status'][$this->request->table_id] = -1;
        } else if ($param == 1 || $param === 'true') {
            $filter['status'][$this->request->table_id] = 1;
        } else if ($param == 2) {
            $filter['status'][$this->request->table_id] = 2;
        } else if ($param == 0 || $param === 'false') {
            $filter['status'][$this->request->table_id] = 0;
        } else {
            throw new RestException("Invalid param '$param' for option $paramname.", 400);
        }
        /*
        if ($param === 'all') {
            $filter['unhide'][$this->request->table_id] = -1;
        } else if ($param == 1 || $param === 'true') {
            $filter['unhide'][$this->request->table_id] = 1;
        } else if ($param == 0 || $param === 'false') {
            $filter['unhide'][$this->request->table_id] = 0;
        } else {
            throw new RestException("Invalid param '$param' for option $paramname.", 400);
        }*/
    }

    /**
     * @param $filter
     * @throws RestException
     */
    protected function setValidityFilter(&$filter) {
        $queryParams = &$this->request->api_params;
        $paramname = Request::API_PARAMETERS['validity'];

        if (!array_key_exists($paramname, $queryParams)) {
            return;
        }

        $param = lmb_strtolower($queryParams[$paramname]);
        if ($param == 'all' OR $param == 'allto' OR $param == 'allfrom' OR convert_date($param)) {
            $filter['validity'][$this->request->table_id] = $param;
        } else {
            throw new RestException("Invalid param '$param' for option $paramname.", 400);
        }
    }

    /**
     * @param $onlyfield
     * @throws RestException
     */
    protected function setIncludedFields(&$onlyfield) {
        global $gfield;

        $queryParams = &$this->request->api_params;

        $paramname = Request::API_PARAMETERS['fields'];

        if (!array_key_exists($paramname, $queryParams)) {
            $onlyfield[$this->request->table_id] = array_keys($gfield[$this->request->table_id]['sort']); # all fields
            return;
        }

        # default table is current table
        if (!is_array($queryParams[$paramname])) {
            $queryParams[$paramname] = array($this->request->table_id => $queryParams[$paramname]);
        }

        # for every table
        foreach ($queryParams[$paramname] as $tableIdentifier => &$showFieldListStr) {
            $showFieldTableID = $this->getTableIDFromIdentifier($tableIdentifier);

            # check if valid table id
            if (!$this->isValidTableID($showFieldTableID)) {
                throw new RestException('Invalid table ' . $tableIdentifier, 400);
            }

            # shortcut: * for every field
            if ($showFieldListStr === '*') {
                $onlyfield[$showFieldTableID] = array_keys($gfield[$showFieldTableID]['sort']); # all fields
                continue;
            }

            # for every field
            foreach (explode(',', $showFieldListStr) as &$showFieldIdentifier) {
                $showFieldID = $this->getFieldIdFromIdentifier($showFieldTableID, $showFieldIdentifier);
                $onlyfield[$showFieldTableID][] = $showFieldID;
            }
        }

        # set to include all fields
        if (!$onlyfield) {
            $onlyfield[$this->request->table_id] = array_keys($gfield[$this->request->table_id]['sort']); # all fields
        }
    }

    /**
     * @param $gsr
     * @throws RestException
     */
    protected function setFilterGsr(&$gsr) {
        global $gfield;

        # TODO improve
        $operators = array(
            'numeric' => array(
                'eq' => 1,
                'neq' => 6,
                'lt' => 3,
                'gt' => 2,
                'lte' => 4,
                'gte' => 5
            ),
            'text' => array(
                'eq' => 2,
                'contains' => 1,
                'startswith' => 3,
                'endswith' => 5
            )
        );

        $queryParams = &$this->request->query_params;

        foreach ($queryParams as $filterFieldIdentifier => &$filterFieldData) {
            # id = 1
            # id[eq] = 1
            # kontakte[id] = 1
            # kontakte[id][eq] = 1
            # TODO make filter work for 1:1 relation tables
            $filterTableID = $this->request->table_id;
            $filterFieldID = $this->getFieldIdFromIdentifier($filterTableID, $filterFieldIdentifier);
            $filterFieldParseType = $gfield[$filterTableID]['parse_type'][$filterFieldID];

            # shortcut for "equals" (e.g. &id=1)
            if (!is_array($filterFieldData)) {
                $filterFieldData = array('eq' => $filterFieldData);
            }

            $gsrKey = 0;
            foreach ($filterFieldData as $filterOperator => &$filterValue) {
                switch ($filterFieldParseType) {
                    case 1 /* int */:
                    case 6 /* float */:
                    case 4 /* date */:
                    case 5 /* time */:
                    case 3 /* boolean */:

                        $gsr[$filterTableID][$filterFieldID][$gsrKey] = $filterValue;
                        $gsr[$filterTableID][$filterFieldID]['num'][$gsrKey] = $operators['numeric'][$filterOperator];
                        $gsrKey++;
                        break;

                    case 2 /* text */:
                        # TODO negation, case sensitive etc.
                        $gsr[$filterTableID][$filterFieldID][$gsrKey] = $filterValue;
                        $gsr[$filterTableID][$filterFieldID]['txt'][$gsrKey] = $operators['text'][$filterOperator];
                        $gsrKey++;
                        break;
                }
            }
        }
    }

    /**
     * @param $id
     * @param $relationFieldID
     * @return string
     * @throws RestException
     */
    private function getRelationTableLinkSelf($id, $relationFieldID) {
        $request = new Request($this->request->router, 'GET', $this->request->table_id, $id, $relationFieldID);
        return $request->buildGetUrl();
    }

    /**
     * @param $relationFieldID
     * @return string
     * @throws RestException
     */
    private function getRelationTableLinkRelated($relationFieldID) {
        global $gfield;

        $relationTableID = $gfield[$this->request->table_id]['verkntabid'][$relationFieldID];
        $request = new Request($this->request->router, 'GET', $relationTableID);
        return $request->buildGetUrl();
    }

    protected function getSelfLink() {
        return $this->request->buildGetUrl();
    }

    /**
     * @param $id
     * @return string
     * @throws RestException
     */
    protected function getSelfRequestLink($id) {
        $request = new Request($this->request->router, 'GET', $this->request->table_id, $id);
        return $request->buildGetUrl();
    }

    protected function isValidTableID($showFieldTableID) {
        global $gtab;

        # the fetched table?
        if ($showFieldTableID === $this->request->table_id) {
            return true;
        }

        # 1:1 relation?
        if (array_key_exists($showFieldTableID, $gtab['rverkn'][$this->request->table_id])) {
            return true;
        }

        return false;
    }
}
