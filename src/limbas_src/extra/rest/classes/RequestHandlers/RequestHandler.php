<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes\RequestHandlers;

use Limbas\extra\rest\classes\Request;
use Limbas\extra\rest\classes\RestException;

abstract class RequestHandler {

    protected Request $request;

    protected int $status;
    protected array $headers;

    public static array $status_codes = array (
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    protected ?array $links = null;

    /**
     * Request constructor.
     * @param Request $request
     */
    public function __construct(Request $request) {
        $this->status = 200;
        $this->request = $request;
        $this->headers = array();
    }

    public abstract function getAnswer(): ?array;

    /**
     * @param $identifier
     * @return int
     * @throws RestException
     */
    public static function getTableIDFromIdentifier(&$identifier): int
    {
        global $gtab;

        if (is_numeric($identifier) and array_key_exists($identifier, $gtab['table'])) {
            return intval($identifier);
        } else {
            $gtabid = $gtab['argresult_id'][lmb_strtoupper($identifier)];
            if ($gtabid) {
                return intval($gtabid);
            }
        }
        throw new RestException('Unknown table ' . $identifier, 400);
    }
    
    /**
     * @param $gtabid
     * @param $identifier
     * @return int
     * @throws RestException
     */
    public static function getFieldIdFromIdentifier($gtabid, $identifier): int
    {
        global $gfield;

        if (is_numeric($identifier)) {
            $fieldID = intval($identifier);
        } else {
            $fieldID = intval($gfield[$gtabid]['argresult_name'][lmb_strtoupper($identifier)]);
        }

        if (!$fieldID) {
            throw new RestException('Unknown field ' . $identifier, 400);
        }

        # check privilege
        if (!array_key_exists($fieldID, $gfield[$gtabid]['sort'])) {
            throw new RestException('No privilege for field ' . $identifier, 403);
        }

        return $fieldID;
    }

    /**
     * @param $field_identifier
     * @return array
     * @throws RestException
     */
    public function getTableAndFieldIDFromDottedIdentifier(&$field_identifier): array
    {
        $sortFieldParts = explode('.', $field_identifier, 2);
        if (lmb_count($sortFieldParts) > 1) {
            $tableID = self::getTableIDFromIdentifier($sortFieldParts[0]);
            $fieldID = self::getFieldIdFromIdentifier($tableID, $sortFieldParts[1]);
        } else {
            $tableID = $this->request->table_id;
            $fieldID = self::getFieldIdFromIdentifier($tableID, $field_identifier);
        }
        return array($tableID, $fieldID);
    }

    /**
     * Get id for a specific field
     *
     * @param $table_id
     * @param $identifier
     * @return int
     * @throws RestException
     */
    public static function resolveRelationField($table_id, &$identifier): int
    {
        global $gfield;

        # check table id
        if (!array_key_exists($table_id, $gfield)) {
            throw new RestException('Invalid table id ' . $table_id, 400);
        }

        $fieldID = self::getFieldIdFromIdentifier($table_id, $identifier);

        # check if is relation field
        if (!$fieldID or !array_key_exists($fieldID, $gfield[$table_id]['verkntabid'])) {
            throw new RestException('Relation field ' . $identifier . ' not found!', 404);
        }

        return $fieldID;
    }


    protected function getFieldRelations($field_id): array
    {

        $onlyfield = array();
        $onlyfield[$this->request->table_id] = array($field_id);

        $filter = array();
        $filter['nolimit'][$this->request->table_id] = true;
        $filter['relationval'][$this->request->table_id] = true;
        $gresult = get_gresult($this->request->table_id, 1, $filter, array(), array(), $onlyfield, $this->request->id);

        $relids = array();

        $gresult = $gresult[$this->request->table_id];
        if (array_key_exists($field_id,$gresult) && array_key_exists(0, $gresult[$field_id])) {
            foreach ($gresult[$field_id][0] as $relid) {
                $relids[] = $relid;
            }
        }
        return $relids;
    }



    public function sendHeader(): void
    {
        if ($this->status !== 200) {
            header($_SERVER['SERVER_PROTOCOL'] . ' ' . $this->status . ' ' . self::$status_codes[$this->status], true, $this->status);
        }
        foreach ($this->headers as $header) {
            header($header);
        }
    }
}
