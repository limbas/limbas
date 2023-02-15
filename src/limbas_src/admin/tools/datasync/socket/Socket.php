<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */
namespace admin\tools\datasync\socket;
use Socket\Raw\Exception;
use Socket\Raw\Socket as RawSocket;

class Socket extends RawSocket
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
        parent::__construct($resource);
    }
    
    public function accept()
    {
        $resource = @socket_accept($this->resource);
        if ($resource === false) {
            throw Exception::createFromGlobalSocketOperation();
        }
        return new Socket($resource);
    }
    
    public function lmbWriteArr($messageArr) {
        $this->lmbWrite(serialize($messageArr));
    }

    public function lmbReadArr() {
        return unserialize($this->lmbRead());
    }
    public function lmbWrite($message) {
        $this->write(strlen($message) . "\n");
        $this->write($message . "\n");
    }

    public function lmbRead() {
        $len = str_replace("\n", "", $this->read(8092, PHP_NORMAL_READ));
        return $this->read($len + 20, PHP_NORMAL_READ);
    }

}
