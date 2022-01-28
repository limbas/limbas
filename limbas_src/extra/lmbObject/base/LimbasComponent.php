<?php
/*
 * Copyright notice
 * (c) 1998-2021 Limbas GmbH(support@limbas.org)
 * All rights reserved
 * This script is part of the LIMBAS project. The LIMBAS project is free software; you can redistribute it and/or modify it on 2 Ways:
 * Under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * Or
 * In a Propritary Software Licence http://limbas.org
 * The GNU General Public License can be found at http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license from the author is found in LICENSE.txt distributed with these scripts.
 * This script is distributed WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * This copyright notice MUST APPEAR in all copies of the script!
 * Version 4.3.36.1319
 */

/*
 * ID:
 */
abstract class LimbasComponent {
	/** @var reference id of this object */
	protected $refId;
	
	public function __construct() {
		list($this->refId, $tmp) = self::refCount($this);
	}
	
	/**
	 * Log a string to the logger
	 *
	 * @param string $str the string to log
	 * @param int $level the level to use
	 */
	protected function log($str, $level=LimbasLogger::LL_INFO) {
		LimbasLogger::log($str, $level);
	}
	
	/**
	 * Trace a string to the logger
	 *
	 * @param string $str the string to log
	 */
	protected function trace($str) {
		LimbasLogger::log($str, LimbasLogger::LL_TRACE);
	}
	
	/**
	 * This magic method is called each time a variable is referenced from the object
	 *
	 * @param string $name the variable name
	 * @return mixed
	 * @throws LimbasException if no getter exists
	 */
	public function __get($name) {
		$getter = 'get' . $name;
		if(method_exists($this, $getter)) {
			return $this->$getter();
		}
		
		throw new LimbasException('Property "' . get_class($this) . '.' . $name . '" is not defined.');
	}
	
	/**
	 * Get the PHP reference id
	 *  
	 * @return int reference id of this object
	 */
	public function getRefId() {
		return $this->refId;
	}

	/**
	 * This magic method is called each time a variable is set in the object
	 *
	 * @param string $name variable name
	 * @param mixed $value variable content
	 * @return void
	 * @throws LimbasException if no setter exists 
	 */
	public function __set($name, $value) {
		$setter = 'set' . $name;
		if(method_exists($this, $setter)) {
			$this->$setter($value);
		}
		if(method_exists($this, 'get' . $name)) {
			throw new LimbasException('Property "' . get_class($this) . '.' . $name . '" is read only.');
		}
		else {
			throw new LimbasException('Property "' . get_class($this) . '.' . $name . '" is not defined.');
		}
	}
	
	/**
	 * This magic method is invoked each time isset() is called on the object variable
	 *
	 * @param string $name variable name
	 * @return boolean true if the object variable is set, otherwise false
	 */
	public function __isset($name) {
		$getter = 'get' . $name;
		if(method_exists($this, $getter)) {
			return $this->$getter() !== null;
		}
		return false;
	}
	
	/**
	 * This magic method is invoked each time unset() is called on the object variable
	 *
	 * @param string $name variable name
	 * @throws LimbasException if no setter exists
	 */
	public function __unset($name) {
		$setter = 'set' . $name;
		if(method_exists($this, $setter)) {
			$this->$setter(null);
		}
		elseif(method_exists($this, 'get' . $name)) {
			throw new LimbasException('Property "' . get_class($this) . '.' . $name . '" is read only.');
		}
		else {
			throw new LimbasException('Property "' . get_class($this) . '.' . $name . '" is not defined.');
		}
	}
	
	/**
	 * Get the reference id and the count from debug_zval_dump funtion
	 * 
	 * @param multi $var 
	 * @return array id and count
	 */
	protected static function refCount(&$var) {
		ob_start();
		debug_zval_dump(array(&$var));
		$ob = ob_get_clean();

		$data = preg_replace('/^.+#(\d+).+refcount\((\d+)\).*$/', '$1;$2',
			lmb_substr($ob, 31, lmb_strpos($ob, '{', 31) - 31) , 1) . "<br/>";
		$data = explode(';', $data);
		$data[1] -= 4;
		return $data;
	}
	
}