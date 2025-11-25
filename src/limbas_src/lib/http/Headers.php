<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\http;

use Limbas\lib\db\Database;
use Throwable;

class Headers
{
    private array $allowed = [];

    public function __construct()
    {
        $this->loadFromDatabase();
    }


    public function addOrigin(string $domain): void
    {
        if ( !in_array( $domain, $this->allowed ))
        {
            $this->allowed[] = 'http://' . $domain;
            $this->allowed[] = 'https://' . $domain;
        }
    }


    public function handle(): void
    {

        $httpOrigin = $this->getAccessControlOrigin();
        $allowedHeaders = 'Origin, X-Requested-With, Content-Type, Accept, Authorization';

        if(empty($httpOrigin)) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS')
        {
            header("Access-Control-Allow-Origin: $httpOrigin");
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header("Access-Control-Allow-Headers: $allowedHeaders");
            header('Content-Length: 0');
            header('Content-Type: text/plain');
            die();
        }

        header("Access-Control-Allow-Origin: $httpOrigin");
        header("Access-Control-Allow-Headers: $allowedHeaders");
    }
    
    public function getAccessControlHeaders(): array
    {
        $httpOrigin = $this->getAccessControlOrigin();
        $allowedHeaders = 'Origin, X-Requested-With, Content-Type, Accept, Authorization';
        
        return [
            'Access-Control-Allow-Origin' => $httpOrigin,
            'Access-Control-Allow-Methods' => $allowedHeaders,
        ];
    }

    private function loadFromDatabase(): void {
        try {
            $rs = Database::select('LMB_UMGVAR', ['NORM'], ['FORM_NAME'=>'http_cors_domains']);
            while (lmbdb_fetch_row($rs)) {
                $domainList = lmbdb_result($rs,'NORM');
                $domains = explode(',', $domainList);
                foreach($domains as $domain) {
                    $this->addOrigin($domain);
                }
            }
        } catch (Throwable){}
    }

    private function getAccessControlOrigin(): ?string
    {
        $httpOrigin = null;

        if(isset($_SERVER['HTTP_ORIGIN']))
        {
            $httpOriginHeader = trim($_SERVER['HTTP_ORIGIN'], '/');
            if(in_array($httpOriginHeader, $this->allowed)) {
                $httpOrigin = $httpOriginHeader;
            }
        }

        return $httpOrigin;
    }
    
}
