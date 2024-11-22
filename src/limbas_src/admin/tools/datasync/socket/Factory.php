<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\socket;

use Socket\Raw\Exception;
use Socket\Raw\Factory as RawFactory;

class Factory extends RawFactory
{
    
    /**
     * create low level socket with given arguments
     *
     * @param int $domain
     * @param int $type
     * @param int $protocol
     * @return Socket
     * @throws Exception if creating socket fails
     * @throws \Error PHP 8 only: throws \Error when arguments are invalid
     * @uses socket_create()
     */
    public function create($domain, $type, $protocol)
    {
        $sock = @socket_create($domain, $type, $protocol);
        if ($sock === false) {
            throw Exception::createFromGlobalSocketOperation('Unable to create socket');
        }
        return new Socket($sock);
    }

}

