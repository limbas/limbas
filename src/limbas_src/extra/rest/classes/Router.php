<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace Limbas\extra\rest\classes;

class Router
{
    /** @var string original path of the request */
    private string $path;

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
    public function getRequest(): Request
    {
        $url_parts = explode('/', $this->path);
        if (lmb_count($url_parts) < 1) {
            throw new RestException('Request table missing', 400);
        } else if (lmb_count($url_parts) > 3) {
            throw new RestException('Too many request parts', 400);
        }

        $table_identifier = $url_parts[0];
        $id = null;
        $field_identifier = null;
        if (lmb_count($url_parts) > 1) {
            $id = $url_parts[1];
            if (lmb_count($url_parts) > 2) {
                $field_identifier = $url_parts[2];
            }
        }
        return new Request($this, $_SERVER['REQUEST_METHOD'], $table_identifier, $id, $field_identifier, $_GET);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

}
