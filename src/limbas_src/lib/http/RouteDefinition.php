<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */

namespace Limbas\lib\http;

class RouteDefinition
{
    private array $requirements = [];

    public function __construct(
        public string $path,
        public array  $controller,
        public array  $methods,
        public string $name
    )
    {
        //
    }


    public function name(string $name): self
    {
        Route::renameRoute($this->name, $name);
        $this->name = $name;
        return $this;
    }

    // Attach route requirements (parameter constraints)
    public function where(array $constraints): self
    {
        if (empty($constraints) && empty($this->requirements)) {
            return $this;
        }
        Route::setRequirements($this->name, $constraints);
        $this->requirements = $constraints;
        return $this;
    }

    // Get route requirements (parameter constraints)
    public function getRequirements(): array
    {
        return $this->requirements;
    }

}
