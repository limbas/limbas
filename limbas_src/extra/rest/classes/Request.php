<?php

namespace limbas\rest;

class Request {

    /**
     * Allowed request methods
     */
    const ALLOWED_METHODS = array('GET', 'POST', 'PATCH', 'DELETE');

    /**
     * parameters used by api functions
     */
    const API_PARAMETERS = array('page'=>'$page', 'sort'=>'$sort', 'fields'=>'$fields', 'archived'=>'$archived', 'validity'=>'$validity');

    /**
     * @var Router
     */
    public $router;

    /**
     * @var string the request method
     */
    private $method;

    /**
     * @var int limbas table id
     */
    public $table_id;

    /**
     * @var int|null limbas dataset id
     */
    public $id = null;

    /**
     * @var int|null limbas field id
     */
    public $field_id = null;

    /**
     * @var array query parameters
     */
    public $query_params = array();

    /**
     * @var array reserved parameters for api functions
     */
    public $api_params = array();

    /**
     * @var array request data ($_POST, etc.)
     */
    public $request_data = array();

    /**
     * Request constructor.
     * @param Router $router
     * @param string $method
     * @param string $table_identifier
     * @param int|null $id
     * @param string|null $field_identifier
     * @param array $query_params
     * @throws RestException
     */
    public function __construct($router, $method, $table_identifier, $id=null, $field_identifier=null, &$query_params=array()) {
        $this->router = $router;

        # check method
        if (!in_array($method, self::ALLOWED_METHODS)) {
            throw new RestException(null, 405);
        }
        $this->method = $method;

        # get table id from identifier
        $this->table_id = RequestHandler::getTableIDFromIdentifier($table_identifier);

        # parse id
        if ($id) {
            if (is_numeric($id)) {
                $this->id = intval($id);
            } else {
                throw new RestException('Invalid dataset id ' . $id, 400);
            }
        }

        # get field id from identifier
        if ($field_identifier) {
            if (!$this->id) {
                throw new RestException('Relation field without dataset id', 400);
            }
            $this->field_id = RequestHandler::resolveRelationField($this->table_id, $field_identifier);
        }

        # check query params
        if ($query_params) {
            if (is_array($query_params)) {
                foreach ($query_params as $filterFieldIdentifier => &$filterFieldData) {
                    if (in_array($filterFieldIdentifier, self::API_PARAMETERS)) {
                        $this->api_params[$filterFieldIdentifier] = $filterFieldData;
                    } else {
                        $this->query_params[$filterFieldIdentifier] = $filterFieldData;
                    }
                }
            } else {
                throw new RestException('Query parameters must be array', 400);
            }
        }

        # get request data
        switch ($this->method) {
            case 'GET':
                $this->request_data = array();
                break;

            case 'POST':
                if (!empty($_POST)) {
                    $this->request_data = &$_POST;
                    break;
                }
                $this->request_data = json_decode(file_get_contents("php://input"),true);
                if (!is_array($this->request_data)) {
                    throw new RestException('Could not parse request body', 400);
                }
                break;
            case 'PATCH':
            case 'DELETE':
                $input = file_get_contents("php://input");
                if ($_SERVER["CONTENT_TYPE"] === 'application/json') {
                    $this->request_data = json_decode($input,true);
                } else {
                    parse_str($input, $this->request_data);
                }
                if (!is_array($this->request_data) && $this->method != 'DELETE') {
                    throw new RestException('Could not parse request body', 400);
                }
                break;
        }
    }

    /**
     * @return RequestHandler
     * @throws RestException
     */
    public function getHandler() {
        switch ($this->method) {
            case 'GET':
                if ($this->id and $this->field_id) {
                    return new GetRelationRequestHandler($this);
                } else if ($this->id) {
                    return new GetDatasetRequestHandler($this);
                } else {
                    return new GetTableRequestHandler($this);
                }

            case 'POST':
                if ($this->id and $this->field_id) {
                    return new PostRelationRequestHandler($this);
                } else if ($this->id) {
                    throw new RestException('Post not allowed to single dataset!', 400);
                } else {
                    return new PostTableRequestHandler($this);
                }

            case 'PATCH':
                if ($this->id and $this->field_id) {
                    return new PatchRelationRequestHandler($this);
                } else if ($this->id) {
                    return new PatchDatasetRequestHandler($this);
                } else {
                    throw new RestException('Patch not allowed to whole table!', 400);
                }

            case 'DELETE':
                if ($this->id and $this->field_id) {
                    return new DeleteRelationRequestHandler($this);
                } else if ($this->id) {
                    return new DeleteDatasetRequestHandler($this);
                } else {
                    throw new RestException('Delete not allowed on whole table!', 400);
                }
        }

        throw new RestException(null, 405);
    }

    /**
     * @return string
     */
    public function buildGetUrl() {
        global $gtab;
        global $gfield;

        $parts = array();
        $parts[] = lmb_strtolower($gtab['table'][$this->table_id]);
        if ($this->id) {
            $parts[] = $this->id;
            if ($this->field_id) {
                $parts[] = lmb_strtolower($gfield[$this->table_id]['field_name'][$this->field_id]);
            }
        }

        $url = '/' . implode('/', $parts);
        $query_params = array_merge($this->api_params,$this->query_params);
        if (!empty($query_params)) {
            $url .= '?' . urldecode(http_build_query($query_params));
        }

        return $url;
    }



}