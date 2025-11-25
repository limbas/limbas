<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\admin\tools\datasync\Data;

class DatasyncPackage
{

    /** @var bool general request status */
    public bool $success;

    /** @var array log entries of current system */
    public array $log;

    /** @var array the template used for the current sync process */
    public array $template;

    /** @var ?DatasyncData main data package */
    public ?DatasyncData $data;


    public function __construct(array $template = [])
    {
        $this->success = true;
        $this->log = [];
        $this->template = $template;
    }


    public function setLog(array $log): DatasyncPackage
    {
        $this->log = $log;
        return $this;
    }

    public function setSuccess(bool $success): DatasyncPackage
    {
        $this->success = $success;
        return $this;
    }

    public function setData(DatasyncData $data): DatasyncPackage
    {
        $this->data = $data;
        return $this;
    }

}
