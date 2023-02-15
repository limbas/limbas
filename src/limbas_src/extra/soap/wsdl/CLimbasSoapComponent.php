<?php
/**
 * @copyright Limbas GmbH <https://limbas.com>
 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 */



abstract class CLimbasSoapComponent
{

	public function __get($name)
	{
		$getter = 'get' . $name;
		if(method_exists($this, $getter)) {
			return $this->$getter();
		}
		
		throw new Exception('Property "' . get_class($this) . '.' . $name . '" is not defined.');
	}
	
	public function __set($name, $value)
	{
		$setter = 'set' . $name;
		if(method_exists($this, $setter)) {
			return $this->$setter($value);
		}
		if(method_exists($this, 'get' . $name)) {
			throw new Exception('Property "' . get_class($this) . '.' . $name . '" is read only.');
		}
		else {
			throw new Exception('Property "' . get_class($this) . '.' . $name . '" is not defined.');
		}
	}
	
	public function __isset($name)
	{
		$getter = 'get' . $name;
		if(method_exists($this, $getter)) {
			return $this->$getter() !== null;
		}
		return false;
	}
	
	public function __unset($name)
	{
		$setter = 'set' . $name;
		if(method_exists($this, $setter)) {
			$this->$setter(null);
		}
		else if(method_exists($this, 'get' . $name)) {
			throw new Exception('Property "' . get_class($this) . '.' . $name . '" is read only.');
		}
	}
}
