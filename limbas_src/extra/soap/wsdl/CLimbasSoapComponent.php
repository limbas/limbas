<?php
/*
 * Copyright notice
 * (c) 1998-2018 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 3.5
 */

/*
 * ID:
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