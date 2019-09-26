<?php
namespace limbas\rest;

class Router
{
    /** @var string original path of the request */
    private $path;

    /**
     * Router constructor.
     */
    public function __construct() {
        global $umgvar;

        if (!empty($umgvar['restpath']) && $umgvar['restpath'] != 'main_rest.php') {
            $baseUrl = trim($umgvar['restpath'],'/');
        } else {
            $baseUrl = parse_url(trim($umgvar['url'],'/') . '/main_rest.php');
            $baseUrl = $baseUrl['path'];
        }
        $url = parse_url($_SERVER['REQUEST_URI']);
        $this->path = trim(preg_replace('*' . preg_quote($baseUrl, '*') . '*', '', $url['path'], 1),'/');
    }

    /**
     * Parses URL to create Request
     * @return Request
     * @throws RestException
     */
    public function getRequest() {
        $url_parts = explode('/', $this->path);
        if (count($url_parts) < 1) {
            throw new RestException('Request table missing', 400);
        } else if (count($url_parts) > 3) {
            throw new RestException('Too many request parts', 400);
        }

        $table_identifier = $url_parts[0];
        $id = null;
        $field_identifier = null;
        if (count($url_parts) > 1) {
            $id = $url_parts[1];
            if (count($url_parts) > 2) {
                $field_identifier = $url_parts[2];
            }
        }
        return new Request($this, $_SERVER['REQUEST_METHOD'], $table_identifier, $id, $field_identifier, $_GET);
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

}